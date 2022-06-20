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
}