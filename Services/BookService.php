<?php

namespace Services;

use Entities\Book;
use Entities\BookCopy;
use Repositories\BookRepository;
use Repositories\BookCopyRepository;

class BookService
{
    private BookRepository $bookRepository;
    private BookCopyRepository $copyRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
        $this->copyRepository = new BookCopyRepository();
    }

    public function addBook(string $title, string $isbn, int $categoryId, int $copies): int
    {
        $title = trim($title);
        $isbn = trim($isbn);

        if ($title === '') {
            throw new \InvalidArgumentException("Book title is required.");
        }

        if ($isbn === '') {
            throw new \InvalidArgumentException("ISBN is required.");
        }

        if ($categoryId <= 0) {
            throw new \InvalidArgumentException("Invalid category.");
        }

        if ($copies < 1) {
            throw new \InvalidArgumentException("A book must have at least one copy.");
        }

        if ($this->bookRepository->isbnExists($isbn)) {
            throw new \InvalidArgumentException("A book with this ISBN already exists.");
        }

        $book = new Book($title, $isbn, $categoryId);
        $bookId = $this->bookRepository->create($book);

        for ($i = 1; $i <= $copies; $i++) {
            $this->copyRepository->create(new BookCopy($bookId, $i));
        }

        return $bookId;
    }

    public function getBook(int $id): array
    {
        $book = $this->bookRepository->findById($id);

        if (!$book) {
            throw new \InvalidArgumentException("Book not found.");
        }

        return $book;
    }

    public function getAvailableBooks(): array
    {
        return $this->bookRepository->getAvailableBooks();
    }

    public function getAllBooksWithAuthors(): array
    {
        return $this->bookRepository->getAllWithAuthors();
    }

    public function getBooksPerCategory(): array
    {
        return $this->bookRepository->getBooksPerCategory();
    }

    public function getMostBorrowedBooks(): array
    {
        return $this->bookRepository->getMostBorrowedBooks(5);
    }
}