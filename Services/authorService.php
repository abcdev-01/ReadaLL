<?php

namespace Services;

use Entities\Author;
use Repositories\authorRepository;

class AuthorService
{
    private authorRepository $authorrepository;

    public function __construct()
    {
        $this->authorrepository = new authorRepository();
    }

    public function addAuthor(string $name, string $email, string $phone): int
    {
        $name = trim($name);
        $email = strtolower(trim($email));
        $phone = trim($phone);

        if ($name === '') {
            throw new \InvalidArgumentException("Author name is required.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email address.");
        }

        if ($phone === '') {
            throw new \InvalidArgumentException("Phone number is required.");
        }

        if ($this->authorrepository->emailExists($email)) {
            throw new \InvalidArgumentException("An author with this email already exists.");
        }

        $author = new Author($name, $email, $phone);

        return $this->authorrepository->create($author);
    }

    public function getAllAuthors(): array
    {
        return $this->authorrepository->getAll();
    }
}