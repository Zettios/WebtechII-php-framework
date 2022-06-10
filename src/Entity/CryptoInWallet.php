<?php

namespace App\Entity;

use Webtek\Core\Database\DatabaseEntity;

class CryptoInWallet extends DatabaseEntity
{
    private int $wallet_id;
    private int $crypto_id;
    private float $amount;

    /**
     * @return int
     */
    public function getWalletId(): int
    {
        return $this->wallet_id;
    }

    /**
     * @param int $wallet_id
     */
    public function setWalletId(int $wallet_id): void
    {
        $this->wallet_id = $wallet_id;
    }

    /**
     * @return int
     */
    public function getCryptoId(): int
    {
        return $this->crypto_id;
    }

    /**
     * @param int $crypto_id
     */
    public function setCryptoId(int $crypto_id): void
    {
        $this->crypto_id = $crypto_id;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
}