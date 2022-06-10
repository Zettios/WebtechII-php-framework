<?php

namespace App\Entity;

use Webtek\Core\Database\DatabaseEntity;

class Crypto extends DatabaseEntity
{
    private int $crypto_id;
    private string $name;

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
}