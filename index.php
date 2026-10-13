<?php

require __DIR__ . '/vendor/autoload.php';

use Services\MemberService;
use Services\BookService;
use Services\BorrowingService;

$memberService = new MemberService();
$bookService = new BookService();
$borrowingService = new BorrowingService();

function printLine(): void
{
    echo str_repeat("-", 60) . PHP_EOL;
}

function printTable(array $rows): void
{
    if (empty($rows)) {
        echo "No records found." . PHP_EOL;
        return;
    }

    foreach ($rows as $row) {
        printLine();
        foreach ($row as $key => $value) {
            echo ucfirst(str_replace('_', ' ', $key)) . ": " . ($value ?? 'N/A') . PHP_EOL;
        }
    }

    printLine();
}

function readInput(string $prompt): string
{
    echo $prompt;
    return trim(fgets(STDIN));
}

while (true) {
    echo PHP_EOL;
    echo "===== LIBRARY MANAGEMENT SYSTEM =====" . PHP_EOL;
    echo "1. Register member" . PHP_EOL;
    echo "2. View members" . PHP_EOL;
    echo "3. Add book with copies" . PHP_EOL;
    echo "4. View all books and authors" . PHP_EOL;
    echo "5. View available books" . PHP_EOL;
    echo "6. Borrow book" . PHP_EOL;
    echo "7. Return book" . PHP_EOL;
    echo "8. View member borrowing history" . PHP_EOL;
    echo "9. View member's current borrowings" . PHP_EOL;
    echo "10. View overdue borrowings" . PHP_EOL;
    echo "11. Books per category" . PHP_EOL;
    echo "12. Five most borrowed books" . PHP_EOL;
    echo "13. Full borrowing history" . PHP_EOL;
    echo "0. Exit" . PHP_EOL;

    $choice = readInput("Choose an option: ");

    try {
        switch ($choice) {
            case '1':
                $name = readInput("Member name: ");
                $email = readInput("Email: ");
                $phone = readInput("Phone: ");

                $id = $memberService->registerMember($name, $email, $phone);
                echo "Member registered with ID $id." . PHP_EOL;
                break;

            case '2':
                printTable($memberService->getAllMembers());
                break;

            case '3':
                $title = readInput("Book title: ");
                $isbn = readInput("ISBN: ");
                $categoryId = (int) readInput("Category ID: ");
                $copies = (int) readInput("Number of copies: ");

                $bookId = $bookService->addBook($title, $isbn, $categoryId, $copies);
                echo "Book added with ID $bookId and $copies copy/copies." . PHP_EOL;
                break;

            case '4':
                printTable($bookService->getAllBooksWithAuthors());
                break;

            case '5':
                printTable($bookService->getAvailableBooks());
                break;

            case '6':
                $memberId = (int) readInput("Member ID: ");
                $bookId = (int) readInput("Book ID: ");

                $borrowingId = $borrowingService->borrowBook($memberId, $bookId);
                echo "Book borrowed successfully. Borrowing ID: $borrowingId" . PHP_EOL;
                break;

            case '7':
                $borrowingId = (int) readInput("Borrowing ID: ");

                $borrowingService->returnBook($borrowingId);
                echo "Book returned successfully." . PHP_EOL;
                break;

            case '8':
                $memberId = (int) readInput("Member ID: ");
                printTable($borrowingService->getMemberHistory($memberId));
                break;

            case '9':
                $memberId = (int) readInput("Member ID: ");
                printTable($borrowingService->getCurrentlyBorrowed($memberId));
                break;

            case '10':
                printTable($borrowingService->getOverdueBorrowings());
                break;

            case '11':
                printTable($bookService->getBooksPerCategory());
                break;

            case '12':
                printTable($bookService->getMostBorrowedBooks());
                break;

            case '13':
                printTable($borrowingService->getFullHistory());
                break;

            case '0':
                echo "Goodbye!" . PHP_EOL;
                exit;

            default:
                echo "Invalid option. Please try again." . PHP_EOL;
        }
    } catch (\InvalidArgumentException $e) {
        echo "Validation error: " . $e->getMessage() . PHP_EOL;
    } catch (\PDOException $e) {
        echo "Database error: " . $e->getMessage() . PHP_EOL;
    } catch (\Throwable $e) {
        echo "Unexpected error: " . $e->getMessage() . PHP_EOL;
    }
}