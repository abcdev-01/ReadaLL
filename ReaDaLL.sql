CREATE DATABASE IF NOT EXISTS ReaDaLL CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ReaDaLL;

DROP TABLE IF EXISTS loan_records;

DROP TABLE IF EXISTS book_copies;

DROP TABLE IF EXISTS book_authors;

DROP TABLE IF EXISTS books;

DROP TABLE IF EXISTS categories;

DROP TABLE IF EXISTS authors;

DROP TABLE IF EXISTS members;

CREATE TABLE members (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE TABLE authors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE TABLE books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    category_id INT UNSIGNED NOT NULL,
    CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT
) ENGINE = InnoDB;

CREATE TABLE book_authors (
    book_id INT UNSIGNED NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (book_id, author_id),
    CONSTRAINT fk_book_authors_book FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE,
    CONSTRAINT fk_book_authors_author FOREIGN KEY (author_id) REFERENCES authors (id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE book_copies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id INT UNSIGNED NOT NULL,
    copy_code VARCHAR(50) NOT NULL UNIQUE,
    status ENUM(
        'available',
        'borrowed',
        'lost'
    ) NOT NULL DEFAULT 'available',
    CONSTRAINT fk_copies_book FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE loan_records (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    copy_id INT UNSIGNED NOT NULL,
    member_id INT UNSIGNED NOT NULL,
    borrowed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    due_at DATETIME NOT NULL,
    returned_at DATETIME NULL,
    status ENUM(
        'borrowed',
        'returned',
        'overdue'
    ) NOT NULL DEFAULT 'borrowed',
    CONSTRAINT fk_loan_copy FOREIGN KEY (copy_id) REFERENCES book_copies (id) ON DELETE RESTRICT,
    CONSTRAINT fk_loan_member FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE RESTRICT,
    INDEX idx_loan_member (member_id),
    INDEX idx_loan_copy (copy_id),
    INDEX idx_loan_status (status),
    INDEX idx_loan_due (due_at)
) ENGINE = InnoDB;

RENAME TABLE loan_records TO borrowings;

ALTER TABLE books RENAME COLUMN name TO title;