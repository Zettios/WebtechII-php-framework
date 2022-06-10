<?php

namespace App\Entity;

use Webtek\Core\Database\DatabaseEntity;

class CoursePrice extends DatabaseEntity
{
    private int $course_price_id;
    private int $value;
    private string $date;
    private int $crypto_id;

    /**
     * @return int
     */
    public function getCoursePriceId(): int
    {
        return $this->course_price_id;
    }

    /**
     * @param int $course_price_id
     */
    public function setCoursePriceId(int $course_price_id): void
    {
        $this->course_price_id = $course_price_id;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
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
}