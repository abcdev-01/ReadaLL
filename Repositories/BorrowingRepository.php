<?php

namespace Repositories;

use Config\Database;
use Entities\Borrowing;

class borrowingRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(Borrowing $borrowing): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO borrowings
                (member_id, copy_id, borrowed_at, due_at, returned_at, status)
             VALUES
                (:member_id, :copy_id, :borrowed_at, :due_at, :returned_at, :status)"
        );

        $stmt->execute([
            ':member_id' => $borrowing->getMemberId(),
            ':copy_id' => $borrowing->getCopyId(),
            ':borrowed_at' => $borrowing->getBorrowedAt(),
            ':due_at' => $borrowing->getDueAt(),
            ':returned_at' => $borrowing->getReturnedAt(),
            ':status' => $borrowing->getStatus()
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM borrowings WHERE id = :id"
        );

        $stmt->execute([':id' => $id]);
        $borrowing = $stmt->fetch();

        return $borrowing ?: null;
    }

    public function getMemberHistory(int $memberId): array
    {
        $sql = "SELECT br.id,
                       b.title,
                       bc.copy_code,
                       br.borrowed_at,
                       br.due_at,
                       br.returned_at,
                       br.status
                FROM borrowings br
                JOIN book_copies bc ON bc.id = br.copy_id
                JOIN books b ON b.id = bc.book_id
                WHERE br.member_id = :member_id
                ORDER BY br.borrowed_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':member_id' => $memberId]);

        return $stmt->fetchAll();
    }

    public function getCurrentlyBorrowedByMember(int $memberId): array
    {
        $sql = "SELECT b.title,
                       bc.copy_code,
                       br.borrowed_at,
                       br.due_at
                FROM borrowings br
                JOIN book_copies bc ON bc.id = br.copy_id
                JOIN books b ON b.id = bc.book_id
                WHERE br.member_id = :member_id
                  AND br.status = 'borrowed'
                ORDER BY br.borrowed_at";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':member_id' => $memberId]);

        return $stmt->fetchAll();
    }

    public function getOverdueBorrowings(): array
    {
        $sql = "SELECT br.id,
                       m.name AS member,
                       b.title,
                       bc.copy_code,
                       br.borrowed_at,
                       br.due_at,
                       TIMESTAMPDIFF(DAY, br.due_at, NOW()) AS days_overdue
                FROM borrowings br
                JOIN members m ON m.id = br.member_id
                JOIN book_copies bc ON bc.id = br.copy_id
                JOIN books b ON b.id = bc.book_id
                WHERE br.status = 'borrowed'
                  AND br.due_at < NOW()
                ORDER BY br.due_at";

        return $this->db->query($sql)->fetchAll();
    }

    public function getFullHistory(): array
    {
        $sql = "SELECT br.id,
                       m.name AS member,
                       b.title,
                       bc.copy_code,
                       br.borrowed_at,
                       br.due_at,
                       br.returned_at,
                       br.status
                FROM borrowings br
                JOIN members m ON m.id = br.member_id
                JOIN book_copies bc ON bc.id = br.copy_id
                JOIN books b ON b.id = bc.book_id
                ORDER BY br.borrowed_at DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function markReturned(int $borrowingId, string $returnedAt): void
    {
        $stmt = $this->db->prepare(
            "UPDATE borrowings
             SET returned_at = :returned_at,
                 status = 'returned'
             WHERE id = :id"
        );

        $stmt->execute([
            ':returned_at' => $returnedAt,
            ':id' => $borrowingId
        ]);
    }
}