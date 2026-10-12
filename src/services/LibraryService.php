<?php

namespace App\Services;

use App\Repositories\LibraryRepository;
use RuntimeException;

class LibraryService
{
    public function __construct(private LibraryRepository $repo) {}

    public function registerMember(string $name, string $email, string $phone): int
    {
        if (trim($name) === '') {
            throw new RuntimeException("Member name is required.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Invalid email address.");
        }

        if (trim($phone) === '') {
            throw new RuntimeException("Phone number is required.");
        }

        if ($this->repo->findMemberByEmail($email)) {
            throw new RuntimeException("Duplicate member email.");
        }

        return $this->repo->createMember($name, $email, $phone);
    }

    public function addAuthor(string $name, string $email, string $phone): int
    {
        if (trim($name) === '') {
            throw new RuntimeException("Author name is required.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Invalid author email.");
        }

        if (trim($phone) === '') {
            throw new RuntimeException("Author phone is required.");
        }

        if ($this->repo->authorEmailExists($email)) {
            throw new RuntimeException("Duplicate author email.");
        }

        return $this->repo->createAuthor($name, $email, $phone);
    }

    public function addCategory(string $name): int
    {
        if (trim($name) === '') {
            throw new RuntimeException("Category name is required.");
        }

        if ($this->repo->categoryNameExists($name)) {
            throw new RuntimeException("Duplicate category name.");
        }

        return $this->repo->createCategory($name);
    }

    public function addBook(
        string $name,
        string $isbn,
        int $categoryId,
        array $authorIds
    ): int {
        if (trim($name) === '' || trim($isbn) === '') {
            throw new RuntimeException("Book name and ISBN are required.");
        }

        if ($this->repo->isbnExists($isbn)) {
            throw new RuntimeException("Duplicate ISBN.");
        }

        if (!$this->repo->categoryExists($categoryId)) {
            throw new RuntimeException("Category not found.");
        }

        if (empty($authorIds)) {
            throw new RuntimeException("At least one author is required.");
        }

        foreach ($authorIds as $authorId) {
            if (!$this->repo->authorExists($authorId)) {
                throw new RuntimeException("Author ID $authorId not found.");
            }
        }

        return $this->repo->createBook($name, $isbn, $categoryId, $authorIds);
    }

    public function addBookCopy(int $bookId, string $copyCode): int
    {
        if (!$this->repo->bookExists($bookId)) {
            throw new RuntimeException("Book not found.");
        }

        if (trim($copyCode) === '') {
            throw new RuntimeException("Copy code is required.");
        }

        if ($this->repo->copyCodeExists($copyCode)) {
            throw new RuntimeException("Duplicate copy code.");
        }

        return $this->repo->createBookCopy($bookId, $copyCode);
    }

    public function borrowBook(int $memberId, string $copyCode): void
    {
        if (!$this->repo->memberExists($memberId)) {
            throw new RuntimeException("Member not found.");
        }

        $this->repo->borrowBook($memberId, $copyCode);
    }

    public function returnBook(string $copyCode): void
    {
        $this->repo->returnBook($copyCode);
    }

    public function getAvailableBooks(): array
    {
        return $this->repo->getAvailableBooks();
    }

    public function getMemberCurrentLoans(int $memberId): array
    {
        return $this->repo->getMemberCurrentLoans($memberId);
    }

    public function getOverdueLoans(): array
    {
        return $this->repo->getOverdueLoans();
    }

    public function getMostBorrowedBooks(int $limit = 5): array
    {
        return $this->repo->getMostBorrowedBooks($limit);
    }

    public function getBookCountByCategory(): array
    {
        return $this->repo->getBookCountByCategory();
    }

    public function getFullLoanHistory(): array
    {
        return $this->repo->getFullLoanHistory();
    }

    public function getAllMembers(): array
    {
        return $this->repo->getAllMembers();
    }
}