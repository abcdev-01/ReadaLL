<?php

namespace Repositories;

use Config\Database;
use Entities\BookCopy;

class bookCopyRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(BookCopy $copy): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO book_copies (book_id, copy_code, status)
             VALUES (:book_id, :copy_code, :status)"
        );

        $stmt->execute([
            ':book_id' => $copy->getBookId(),
            ':copy_code' => $copy->getCopyCode(),
            ':status' => $copy->getStatus()
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findAvailableCopy(int $bookId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM book_copies
             WHERE book_id = :book_id
               AND status = 'available'
             ORDER BY id
             LIMIT 1"
        );

        $stmt->execute([':book_id' => $bookId]);
        $copy = $stmt->fetch();

        return $copy ?: null;
    }

    public function markBorrowed(int $copyId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE book_copies
             SET status = 'borrowed'
             WHERE id = :id"
        );

        $stmt->execute([':id' => $copyId]);
    }

    public function markAvailable(int $copyId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE book_copies
             SET status = 'available'
             WHERE id = :id"
        );

        $stmt->execute([':id' => $copyId]);
    }
}