<?php

namespace App\Entity;

use Webtek\Core\Database\DatabaseEntity;
use Webtek\Core\Database\DBConnection;

class Roles extends DatabaseEntity
{
    private int $role_id;
    private string $role_name;
    private DBConnection $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db;
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * @param int $role_id
     */
    public function setRoleId(int $role_id): void
    {
        $this->role_id = $role_id;
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->role_name;
    }

    /**
     * @param string $role_name
     */
    public function setRoleName(string $role_name): void
    {
        $this->role_name = $role_name;
    }

    public function getAllRoles()
    {
        $stmt = $this->db->getPdo()->prepare('SELECT role_id FROM roles');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}