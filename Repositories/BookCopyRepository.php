<?php
namespace Repositories; 

use Config\Database;
use Entities\Bookcopy;

class BookCopyRepository{
private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(BookCopy $Copy): int {
        $stmt = $this->db->prepare(
            "INSERT INTO book_copies (book_id, copy_number, status) 
            VALUES (:book_id, :copy_number, :status)");  
        $stmt->execute([
            ':book_id' => $Copy->getBookId(),
            ':copy_number' => $Copy->getCopyNumber(),
            ':status' => $Copy->getStatus()
        ]);
     return (int) $this->db->lastInsertId();
    }

    public function findAvailableCopy(int $bookId): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM book_copies
             WHERE book_id = :book_id AND status = 'AVAILABLE'
             ORDER BY copy_number
             LIMIT 1"
        );
        $stmt->execute([':book_id' => $bookId]);
        $copy = $stmt->fetch();

        return $copy ?: null;
    }
   public function markBorrowed(int $copyId): void
   {
    
        $stmt = $this->db->prepare(
            "UPDATE book_copies SET status = 'BORROWED' WHERE id = :id"
        );
        $stmt->execute([':id' => $copyId]);
    }

    public function markAvailable(int $copyId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE book_copies SET status = 'AVAILABLE' WHERE id = :id"
        );
        $stmt->execute([':id' => $copyId]);
    }
   }
