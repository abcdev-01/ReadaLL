<?php
namespace Repositories;

use Config\Database;
use Entities\Author;

class authorRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(Author $author): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO authors (name, email, phone)
             VALUES (:name, :email, :phone)"
        );

        $stmt->execute([
            ':name' => $author->getName(),
            ':email' => $author->getEmail(),
            ':phone' => $author->getPhone()
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM authors WHERE email = :email"
        );

        $stmt->execute([':email' => $email]);

        return $stmt->fetch() !== false;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM authors WHERE id = :id"
        );

        $stmt->execute([':id' => $id]);
        $author = $stmt->fetch();

        return $author ?: null;
    }

    public function getAll(): array
    {
        return $this->db
            ->query("SELECT * FROM authors ORDER BY name")
            ->fetchAll();
    }
}