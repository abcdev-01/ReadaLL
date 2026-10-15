<?php

namespace Repositories;

use Config\Database;
use Entities\Book;

class bookRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(Book $book): int {
        $stmt = $this->db->prepare("INSERT INTO books (title, isbn, category_id) VALUES (:title, :isbn, :category_id)");  
        $stmt->execute([
            ':title' => $book->getTitle(),
            ':isbnr' => $book->getIsbn(),
            ':category_id' => $book->getCategoryId()
        ]);
     return (int) $this->db->lastInsertId();
    }

 public function isbnExists(string $isbn): bool {
        $stmt = $this->db->prepare("SELECT id FROM books WHERE isbn = :isbn");
        $stmt->execute([':isbn' => $isbn]);
        return $stmt->fetch() !== false;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT b.*, c.name AS category_name FROM books b 
            JOIN categories c ON c.id = b.category_id 
            WHERE b.id = :id" 
        );
        $stmt->execute([':id' => $id]);
        $book = $stmt->fetch();

        return $book ?: null;
    }
     
     public function getAllWithAuthors(): array{
        
        $stmt = $this->db->query(
            "SELECT b.*, c.name AS category, GROUP_CONCAT(a.name ORDER BY a.name SEPARATOR ', ') AS authors
            FROM books b
            JOIN categories c ON c.id = b.category_id
            LEFT JOIN book_authors ba ON ba.book_id = b.id
            LEFT JOIN authors a ON a.id = ba.author_id
            GROUP BY b.id, b.title, b.isbn, c.name
            ORDER BY b.title"
        );
        return $stmt->fetchAll();
     }

     public function getAvailableBooks(): array
{
    $sql = "SELECT b.id,
                   b.title,
                   b.isbn,
                   c.name AS category,
                   COUNT(bc.id) AS available_copies
            FROM books b
            JOIN categories c ON c.id = b.category_id
            LEFT JOIN book_copies bc
                ON bc.book_id = b.id
               AND bc.status = 'available'
            GROUP BY b.id, b.title, b.isbn, c.name
            ORDER BY b.title";

    return $this->db->query($sql)->fetchAll();
}

public function getMostBorrowedBooks(int $limit = 5): array
{
    $sql = "SELECT b.title,
                   COUNT(br.id) AS borrow_count
            FROM books b
            JOIN book_copies bc ON bc.book_id = b.id
            JOIN borrowings br ON br.copy_id = bc.id
            GROUP BY b.id, b.title
            ORDER BY borrow_count DESC
            LIMIT :limit";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

public function getBooksPerCategory(): array
{
    $sql = "SELECT c.id,
                   c.name AS category,
                   COUNT(b.id) AS total_books
            FROM categories c
            LEFT JOIN books b
                ON b.category_id = c.id
            GROUP BY c.id, c.name
            ORDER BY total_books DESC, c.name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}
     public function getMostBorrowedBook(int $limit = 5): array 
     {
       $stmt = $this->db->prepare(
                "
                SELECT b.title, COUNT(br.id) AS borrow_count FROM books b
                JOIN book_copies bc on bc.book_id = b.id
                JOIN borrowings br ON br.book_copy_id = bc.id
                GROUP BY b.id, b.title
                ORDER BY borrow_count DESC
                LIMIT :limit
                        "
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);    
        return $stmt->fetchAll();
        }
}