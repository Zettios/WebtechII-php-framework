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

    public function getAllUsers(): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM user WHERE name = ?');
        $stmt->execute(["Enrico"]);
        return $stmt->fetch();
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
            return ["status" => 201];
        }
    }
}
