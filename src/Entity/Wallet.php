<?php

namespace App\Entity;

use Webtek\Core\Database\DatabaseEntity;
use Webtek\Core\Database\DBConnection;

class Wallet extends DatabaseEntity
{
    private int $wallet_id;
    private int $user_id;
    private DBConnection $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db;
    }

    /**
     * @return mixed
     */
    public function getWalletId(): int
    {
        return $this->wallet_id;
    }

    /**
     * @return mixed
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }


    public function getSpecificWallet(int $id): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM wallet WHERE user_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}