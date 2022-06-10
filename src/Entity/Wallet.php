<?php

namespace App\Entity;

use Webtek\Core\Database\DatabaseEntity;

class Wallet extends DatabaseEntity
{
    private int $wallet_id;
    private int $user_id;

    /**
     * @return mixed
     */
    public function getWalletId(): int
    {
        return $this->wallet_id;
    }

    /**
     * @param mixed $wallet_id
     */
    public function setWalletId($wallet_id): void
    {
        $this->wallet_id = $wallet_id;
    }

    /**
     * @return mixed
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }
}