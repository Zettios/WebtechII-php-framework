<?php

namespace App\Entity\Users;

use Webtek\Core\Database\DatabaseEntity;
use Webtek\Core\Database\DBConnection;

class User extends DatabaseEntity
{
    private int $user_id;
    private string $name;
    private string $email;
    private string $password;
    private int $role;
    private DBConnection $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    public function getSpecificUser(int $id): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM user WHERE user_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function checkUsername(int $id, string $username): bool
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM user WHERE name = ?');
        $stmt->execute([$username]);
        $result1 = $stmt->fetch();

        if (is_bool($result1) || $result1['user_id'] === $id) {
            return true;
        }

        return false;
    }

    public function updateUser(int $id, string $username, string $email, string $password): array|bool
    {
        if (empty($password)) {
            $stmt = $this->db->getPdo()->prepare('UPDATE user SET name = ?, email = ? WHERE user_id = ?');
            $stmt->execute([$username, $email, $id]);
        } else {
            $password = password_hash($password,PASSWORD_BCRYPT);
            $stmt = $this->db->getPdo()->prepare('UPDATE user SET name = ?, email = ?, password = ? WHERE user_id = ?');
            $stmt->execute([$username, $email, $password, $id]);
        }

        return $stmt->fetch();
    }

    public function updateUserAdmin(int $id, string $username, string $email, string $password, int $role): array|bool
    {
        if (empty($password)) {
            $stmt = $this->db->getPdo()->prepare('UPDATE user SET name = ?, email = ?, role = ? WHERE user_id = ?');
            $stmt->execute([$username, $email, $role, $id]);
        } else {
            $password = password_hash($password,PASSWORD_BCRYPT);
            $stmt = $this->db->getPdo()->prepare('UPDATE user SET name = ?, email = ?, password = ?, role = ? WHERE user_id = ?');
            $stmt->execute([$username, $email, $password, $role, $id]);
        }

        return $stmt->fetch();
    }

    public function deleteUserAdmin(int $id): int
    {
        $stmt = $this->db->getPdo()->prepare('SELECT user_id FROM user WHERE user_id = ?');
        $stmt->execute([$id]);
        $userResult = $stmt->fetch();

        $stmt = $this->db->getPdo()->prepare('SELECT wallet_id FROM wallet WHERE user_id = ?');
        $stmt->execute([$userResult['user_id']]);
        $walletResult = $stmt->fetch();

        $stmt = $this->db->getPdo()->prepare('DELETE FROM crypto_in_wallet WHERE wallet_id = ?');
        $stmt->execute([$walletResult['wallet_id']]);
        $stmt = $this->db->getPdo()->prepare('DELETE FROM wallet WHERE user_id = ?');
        $stmt->execute([$userResult['user_id']]);
        $stmt = $this->db->getPdo()->prepare('DELETE FROM user WHERE user_id = ?');
        $stmt->execute([$userResult['user_id']]);

        $stmt = $this->db->getPdo()->prepare('SELECT user_id FROM user WHERE user_id = ?');
        $stmt->execute([$id]);
        $userResult = $stmt->fetch();

        if (is_bool($userResult)) {
           return 200;
        }

        return 404;
    }

    public function getUserWallet(int $id): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT w.wallet_id FROM wallet AS w WHERE w.user_id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if (is_bool($result)) {
            $result = [];
        }

        return $result;
    }

    public function getWallet(int $id, int $crypto_id): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT w.wallet_id, c.crypto_id, c.amount FROM user AS u, wallet AS w, crypto_in_wallet AS c 
                                                WHERE u.user_id = ? AND c.crypto_id = ? AND u.user_id = w.user_id AND w.wallet_id = c.wallet_id');
        $stmt->execute([$id, $crypto_id]);

        $result = $stmt->fetch();

        if (is_bool($result)) {
            $result = [];
        }

        return $result;
    }

    public function addCryptoWallet(int $wallet_id, int $crypto_id): array|bool
    {
        $stmt = $this->db->getPdo()->prepare('INSERT INTO crypto_in_wallet (wallet_id, crypto_id, amount) VALUES (?,?,?)');
        $stmt->execute([$wallet_id, $crypto_id, 0.00000]);
        return $stmt->fetch();
    }

    public function getNoneCryptoWallets(int $id): array|bool
    {
        $stmt = $this->db->getPdo()->prepare('  SELECT c.name
                                                FROM crypto as c
                                                WHERE NOT EXISTS (	SELECT cu.name
					                            FROM db_bit_traders.user AS u, wallet AS w, crypto_in_wallet AS cw, crypto as cu
					                            WHERE u.user_id = ? 
					                              AND u.user_id = w.user_id AND w.wallet_id = cw.wallet_id 
					                              AND cw.crypto_id = cu.crypto_id AND c.crypto_id = cu.crypto_id);');
        $stmt->execute([$id]);

        return $stmt->fetchAll();
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM user');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function loginUser(string $username, string $password): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM user WHERE name = ?');
        $stmt->execute([$username]);
        $results = $stmt->fetch();

        if ($results) {
            if (password_verify($password, $results["password"])) {
                return ["status" => 200, "id" => $results["user_id"], "role" => $results["role"]];
            } else {
                return ["status" => 404];
            }
        }
        return ["status" => 404];
    }

    public function registerUser(string $username, string $email, string $password): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM user WHERE name = ?');
        $stmt->execute([$username]);
        $results = $stmt->fetch();

        if ($results) {
            return ["status" => 409];
        } else {
            $stmt = $this->db->getPdo()->prepare("INSERT INTO user (name, email, password, role) VALUES (?,?,?,?)");
            $stmt->execute([$username, $email, $password, 1]);

            $id = $this->db->getPdo()->lastInsertId();

            $stmt = $this->db->getPdo()->prepare("INSERT INTO wallet (user_id) VALUES (?)");
            $stmt->execute([$id]);

            $id = $this->db->getPdo()->lastInsertId();

            $stmt = $this->db->getPdo()->prepare("INSERT INTO crypto_in_wallet (wallet_id, crypto_id, amount) VALUES (?, ?, ?)");
            $stmt->execute([$id, 1, 0.0]);

            return ["status" => 201];
        }
    }
}
