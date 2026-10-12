<?php

namespace ReaDaLL\Repository;

use ReaDaLL\Config\Database;
use ReaDaLL\Models\Member;
use PDO;
use RuntimeException;

class LibraryRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->pdo();
    }

    public function findMemberByEmail(string $email): ?Member
    {
        $stmt = $this->pdo->prepare("SELECT * FROM members WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Member(
            (int)$row['id'],
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['joined_at']
        );
    }

    public function createMember(string $name, string $email, string $phone): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO members (name, email, phone) VALUES (?, ?, ?)"
        );
        $stmt->execute([$name, $email, $phone]);

        return (int)$this->pdo->lastInsertId();
    }

    public function memberExists(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM members WHERE id = ?");
        $stmt->execute([$id]);
        return (bool)$stmt->fetch();
    }

    public function createAuthor(string $name, string $email, string $phone): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO authors (name, email, phone) VALUES (?, ?, ?)"
        );
        $stmt->execute([$name, $email, $phone]);

        return (int)$this->pdo->lastInsertId();
    }

    public function authorEmailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM authors WHERE email = ?");
        $stmt->execute([$email]);
        return (bool)$stmt->fetch();
    }

    public function authorExists(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM authors WHERE id = ?");
        $stmt->execute([$id]);
        return (bool)$stmt->fetch();
    }

    public function createCategory(string $name): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);

        return (int)$this->pdo->lastInsertId();
    }

    public function categoryNameExists(string $name): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$name]);
        return (bool)$stmt->fetch();
    }

    public function categoryExists(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return (bool)$stmt->fetch();
    }

    public function isbnExists(string $isbn): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM books WHERE isbn = ?");
        $stmt->execute([$isbn]);
        return (bool)$stmt->fetch();
    }

    public function createBook(
        string $name,
        string $isbn,
        int $categoryId,
        array $authorIds
    ): int {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO books (name, isbn, category_id) VALUES (?, ?, ?)"
            );
            $stmt->execute([$name, $isbn, $categoryId]);

            $bookId = (int)$this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare(
                "INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)"
            );

            foreach ($authorIds as $authorId) {
                $stmt->execute([$bookId, $authorId]);
            }

            $this->pdo->commit();

            return $bookId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function bookExists(int $bookId): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM books WHERE id = ?");
        $stmt->execute([$bookId]);
        return (bool)$stmt->fetch();
    }

    public function copyCodeExists(string $copyCode): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM book_copies WHERE copy_code = ?");
        $stmt->execute([$copyCode]);
        return (bool)$stmt->fetch();
    }

    public function createBookCopy(int $bookId, string $copyCode): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO book_copies (book_id, copy_code, status)
             VALUES (?, ?, 'available')"
        );
        $stmt->execute([$bookId, $copyCode]);

        return (int)$this->pdo->lastInsertId();
    }

    public function borrowBook(int $memberId, string $copyCode): void
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "SELECT bc.id, bc.status
                 FROM book_copies bc
                 WHERE bc.copy_code = ?
                 FOR UPDATE"
            );
            $stmt->execute([$copyCode]);
            $copy = $stmt->fetch();

            if (!$copy) {
                throw new RuntimeException("Copy code not found.");
            }

            if ($copy['status'] !== 'available') {
                throw new RuntimeException("This copy is not available.");
            }

            $stmt = $this->pdo->prepare("SELECT id FROM members WHERE id = ?");
            $stmt->execute([$memberId]);

            if (!$stmt->fetch()) {
                throw new RuntimeException("Member not found.");
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO loan_records
                 (member_id, copy_id, borrowed_at, due_at, status)
                 VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'borrowed')"
            );
            $stmt->execute([$memberId, $copy['id']]);

            $stmt = $this->pdo->prepare(
                "UPDATE book_copies SET status = 'borrowed' WHERE id = ?"
            );
            $stmt->execute([$copy['id']]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function returnBook(string $copyCode): void
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "SELECT lr.id, lr.copy_id
                 FROM loan_records lr
                 JOIN book_copies bc ON bc.id = lr.copy_id
                 WHERE bc.copy_code = ?
                   AND lr.returned_at IS NULL
                 ORDER BY lr.id DESC
                 LIMIT 1
                 FOR UPDATE"
            );
            $stmt->execute([$copyCode]);
            $loan = $stmt->fetch();

            if (!$loan) {
                throw new RuntimeException("No active loan found for this copy.");
            }

            $stmt = $this->pdo->prepare(
                "UPDATE loan_records
                 SET returned_at = NOW(), status = 'returned'
                 WHERE id = ?"
            );
            $stmt->execute([$loan['id']]);

            $stmt = $this->pdo->prepare(
                "UPDATE book_copies SET status = 'available' WHERE id = ?"
            );
            $stmt->execute([$loan['copy_id']]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getAvailableBooks(): array
    {
        $sql = "SELECT b.id, b.name AS book, b.isbn, c.name AS category,
                       COUNT(bc.id) AS available_copies
                FROM books b
                JOIN categories c ON c.id = b.category_id
                LEFT JOIN book_copies bc
                    ON bc.book_id = b.id AND bc.status = 'available'
                GROUP BY b.id, b.name, b.isbn, c.name
                HAVING available_copies > 0
                ORDER BY b.name";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getMemberCurrentLoans(int $memberId): array
    {
        $sql = "SELECT b.name AS book, bc.copy_code,
                       lr.borrowed_at, lr.due_at, lr.status
                FROM loan_records lr
                JOIN book_copies bc ON bc.id = lr.copy_id
                JOIN books b ON b.id = bc.book_id
                WHERE lr.member_id = ?
                  AND lr.returned_at IS NULL
                ORDER BY lr.due_at";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$memberId]);

        return $stmt->fetchAll();
    }

    public function getOverdueLoans(): array
    {
        $sql = "SELECT m.name AS member, b.name AS book, bc.copy_code,
                       lr.borrowed_at, lr.due_at,
                       DATEDIFF(NOW(), lr.due_at) AS days_overdue
                FROM loan_records lr
                JOIN members m ON m.id = lr.member_id
                JOIN book_copies bc ON bc.id = lr.copy_id
                JOIN books b ON b.id = bc.book_id
                WHERE lr.returned_at IS NULL
                  AND lr.due_at < NOW()
                ORDER BY days_overdue DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getMostBorrowedBooks(int $limit = 5): array
    {
        $sql = "SELECT b.name AS book, COUNT(lr.id) AS borrow_count
                FROM loan_records lr
                JOIN book_copies bc ON bc.id = lr.copy_id
                JOIN books b ON b.id = bc.book_id
                GROUP BY b.id, b.name
                ORDER BY borrow_count DESC, b.name
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getBookCountByCategory(): array
    {
        $sql = "SELECT c.name AS category, COUNT(b.id) AS book_count
                FROM categories c
                LEFT JOIN books b ON b.category_id = c.id
                GROUP BY c.id, c.name
                ORDER BY book_count DESC, c.name";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getFullLoanHistory(): array
    {
        $sql = "SELECT m.name AS member, b.name AS book, bc.copy_code,
                       lr.borrowed_at, lr.due_at, lr.returned_at, lr.status
                FROM loan_records lr
                JOIN members m ON m.id = lr.member_id
                JOIN book_copies bc ON bc.id = lr.copy_id
                JOIN books b ON b.id = bc.book_id
                ORDER BY lr.borrowed_at DESC, lr.id DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getAllMembers(): array
    {
        return $this->pdo
            ->query("SELECT id, name, email FROM members ORDER BY name")
            ->fetchAll();
    }
}