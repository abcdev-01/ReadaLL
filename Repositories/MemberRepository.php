<?php

namespace Repositories;

use Config\Database;
use Entities\Member;

class memberRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(Member $member): int
    {
        $sql = "INSERT INTO members (name, email, phone) VALUES (:name, :email, :phone)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $member->getName(),
            ':email' => $member->getEmail(),
            ':phone' => $member->getPhone()
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM members WHERE email = :email");
        $stmt->execute([':email' => $email]);

        return $stmt->fetch() !== false;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM members WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $member = $stmt->fetch();

        return $member ?: null;
    }

    public function getAll(): array
    {
        return $this->db->query("SELECT * FROM members ORDER BY name")->fetchAll();
    }
}