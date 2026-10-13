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

INSERT INTO
    members (name, email, phone)
VALUES (
        'Alice Johnson',
        'alice@example.com',
        '555-0101'
    ),
    (
        'Bob Smith',
        'bob@example.com',
        '555-0102'
    ),
    (
        'Carol White',
        'carol@example.com',
        '555-0103'
    );

INSERT INTO
    authors (name, email, phone)
VALUES (
        'J.K. Rowling',
        'rowling@example.com',
        '555-1001'
    ),
    (
        'George Orwell',
        'orwell@example.com',
        '555-1002'
    ),
    (
        'Jane Austen',
        'austen@example.com',
        '555-1003'
    ),
    (
        'Robert C. Martin',
        'martin@example.com',
        '555-1004'
    );

INSERT INTO
    categories (name)
VALUES ('Fantasy'),
    ('Dystopian'),
    ('Classic'),
    ('Software Engineering');

INSERT INTO
    books (name, isbn, category_id)
VALUES (
        'Harry Potter and the Philosopher''s Stone',
        '9780747532699',
        1
    ),
    ('1984', '9780451524935', 2),
    (
        'Pride and Prejudice',
        '9780141439518',
        3
    ),
    (
        'Clean Code',
        '9780132350884',
        4
    ),
    (
        'Animal Farm',
        '9780451526342',
        2
    );

INSERT INTO
    book_authors (book_id, author_id)
VALUES (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 2);

INSERT INTO
    book_copies (book_id, copy_code, status)
VALUES (1, 'HP-001', 'available'),
    (1, 'HP-002', 'available'),
    (2, '1984-001', 'borrowed'),
    (3, 'PP-001', 'available'),
    (4, 'CC-001', 'available'),
    (4, 'CC-002', 'borrowed'),
    (5, 'AF-001', 'available');

INSERT INTO
    loan_records (
        member_id,
        copy_id,
        borrowed_at,
        due_at,
        returned_at,
        status
    )
VALUES (
        1,
        3,
        NOW() - INTERVAL 20 DAY,
        NOW() - INTERVAL 6 DAY,
        NULL,
        'borrowed'
    ),
    (
        2,
        6,
        NOW() - INTERVAL 5 DAY,
        NOW() + INTERVAL 9 DAY,
        NULL,
        'borrowed'
    ),
    (
        3,
        1,
        NOW() - INTERVAL 30 DAY,
        NOW() - INTERVAL 16 DAY,
        NOW() - INTERVAL 18 DAY,
        'returned'
    );