<?php

namespace Services;

use Entities\Book;
use entities\BookCopy;
use Repositories\bookRepository;
use Repositories\bookCopyRepository;

class bookService
{
    private bookRepository $bookrepository;
    private bookCopyRepository $copyrepository;

    public function __construct()
    {
        $this->bookrepository = new bookRepository();
        $this->copyrepository    = new bookCopyRepository();
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

        if ($this->bookrepository->isbnExists($isbn)) {
            throw new \InvalidArgumentException("A book with this ISBN already exists.");
        }

        $bookId = $this->bookrepository->create( new Book($title, $isbn, $categoryId));

        for ($i = 1; $i <= $copies; $i++) {
            $copyCode = 'BOOK-' . $bookId . '-COPY-' . $i;

            $this->copyrepository->create(
                new BookCopy($bookId, $copyCode)
            );
        }

        return $bookId;
    }

    public function getBook(int $id): array
    {
        $book = $this->bookrepository->findById($id);

        if (!$book) {
            throw new \InvalidArgumentException("Book not found.");
        }

        return $book;
    }

    public function getAvailableBooks(): array
    {
        return $this->bookrepository->getAvailableBooks();
    }

    public function getAllBooksWithAuthors(): array
    {
        return $this->bookrepository->getAllWithAuthors();
    }

    public function getBooksPerCategory(): array
    {
        return $this->bookrepository->getBooksPerCategory();
    }

    public function getMostBorrowedBooks(): array
    {
        return $this->bookrepository->getMostBorrowedBooks(5);
    }
}