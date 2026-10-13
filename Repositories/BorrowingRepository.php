<?php

namespace Repositories;

use Config\Database;
use Entities\Borrowing;

class BorrowingRepository
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
             (member_id, book_copy_id, borrow_date, due_date, return_date, status)
             VALUES
             (:member_id, :book_copy_id, :borrow_date, :due_date, :return_date, :status)"
        );

        $stmt->execute([
            ':member_id' => $borrowing->getMemberId(),
            ':book_copy_id' => $borrowing->getBookCopyId(),
            ':borrow_date' => $borrowing->getBorrowDate(),
            ':due_date' => $borrowing->getDueDate(),
            ':return_date' => $borrowing->getReturnDate(),
            ':status' => $borrowing->getStatus()
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findActiveByCopyId(int $copyId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM borrowings
             WHERE book_copy_id = :copy_id AND status = 'ACTIVE'
             LIMIT 1"
        );

        $stmt->execute([':copy_id' => $copyId]);
        $borrowing = $stmt->fetch();

        return $borrowing ?: null;
    }

    public function markReturned(int $borrowingId, string $returnDate): void
    {
        $stmt = $this->db->prepare(
            "UPDATE borrowings
             SET return_date = :return_date, status = 'RETURNED'
             WHERE id = :id"
        );

        $stmt->execute([
            ':return_date' => $returnDate,
            ':id' => $borrowingId
        ]);
    }

    public function getMemberHistory(int $memberId): array
    {
        $sql = "SELECT br.id, b.title, bc.copy_number,
                       br.borrow_date, br.due_date, br.return_date, br.status
                FROM borrowings br
                JOIN book_copies bc ON bc.id = br.book_copy_id
                JOIN books b ON b.id = bc.book_id
                WHERE br.member_id = :member_id
                ORDER BY br.borrow_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':member_id' => $memberId]);

        return $stmt->fetchAll();
    }

    public function getCurrentlyBorrowedByMember(int $memberId): array
    {
        $sql = "SELECT b.title, bc.copy_number, br.borrow_date, br.due_date
                FROM borrowings br
                JOIN book_copies bc ON bc.id = br.book_copy_id
                JOIN books b ON b.id = bc.book_id
                WHERE br.member_id = :member_id AND br.status = 'ACTIVE'
                ORDER BY br.borrow_date";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':member_id' => $memberId]);

        return $stmt->fetchAll();
    }

    public function getOverdueBorrowings(): array
    {
        $sql = "SELECT br.id, m.name AS member, b.title, bc.copy_number,
                       br.borrow_date, br.due_date,
                       DATEDIFF(CURRENT_DATE, br.due_date) AS days_overdue
                FROM borrowings br
                JOIN members m ON m.id = br.member_id
                JOIN book_copies bc ON bc.id = br.book_copy_id
                JOIN books b ON b.id = bc.book_id
                WHERE br.status = 'ACTIVE' AND br.due_date < CURRENT_DATE
                ORDER BY br.due_date";

        return $this->db->query($sql)->fetchAll();
    }

    public function getFullHistory(): array
    {
        $sql = "SELECT br.id, m.name AS member, b.title, bc.copy_number,
                       br.borrow_date, br.due_date, br.return_date, br.status
                FROM borrowings br
                JOIN members m ON m.id = br.member_id
                JOIN book_copies bc ON bc.id = br.book_copy_id
                JOIN books b ON b.id = bc.book_id
                ORDER BY br.borrow_date DESC";

        return $this->db->query($sql)->fetchAll();
    }
}
