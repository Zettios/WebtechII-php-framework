<?php

namespace App\Entity;

use Webtek\Core\Database\DatabaseEntity;
use Webtek\Core\Database\DBConnection;

class Crypto extends DatabaseEntity
{
    private int $crypto_id;
    private string $name;
    private DBConnection $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAllCrypto(): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM crypto AS c, course_price AS cp
                                              WHERE cp.crypto_id = c.crypto_id');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSingleCrypto(int $id): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM crypto AS c, course_price AS cp
                                              WHERE cp.crypto_id = c.crypto_id AND c.crypto_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getCryptoByName(string $name): array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT crypto_id FROM crypto WHERE name = ?');
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    public function updateCryptoValue(int $crypto_id, float $newAmount): void
    {
        $stmt = $this->db->getPdo()->prepare('UPDATE course_price SET value = ? WHERE crypto_id = ?');
        $stmt->execute([$newAmount, $crypto_id]);
    }

    public function updateWallet(int $wallet_id, int $crypto_id, float $newAmount): void
    {
        $stmt = $this->db->getPdo()->prepare('UPDATE crypto_in_wallet SET amount = ? WHERE wallet_id = ? AND crypto_id = ?');
        $stmt->execute([$newAmount, $wallet_id, $crypto_id]);
    }
}