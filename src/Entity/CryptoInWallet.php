<?php

namespace App\Entity;

use Webtek\Core\Database\DatabaseEntity;
use Webtek\Core\Database\DBConnection;

class CryptoInWallet extends DatabaseEntity
{
    private int $wallet_id;
    private int $crypto_id;
    private float $amount;
    private DBConnection $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db;
    }

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

    public function getUsersCrypto(int $wallet_id): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT c.amount, crypto.name AS crypto_name FROM crypto_in_wallet AS c, crypto WHERE wallet_id = ? AND c.crypto_id = crypto.crypto_id');
        $stmt->execute([$wallet_id]);
        return $stmt->fetchAll();
    }
}