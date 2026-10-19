<?php

spl_autoload_register(function ($class) {
    $folders = [
        'Config\\'       => 'config/',
        'Entities\\'     => 'entities/',
        'Repositories\\' => 'repositories/',
        'Services\\'     => 'services/'
    ];

    foreach ($folders as $namespace => $folder) {
        if (str_starts_with($class, $namespace)) {
            $className = substr($class, strlen($namespace));
            $file = __DIR__ . '/' . $folder . $className . '.php';

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

use Services\memberService;
use Services\bookService;
use Services\borrowingService;
use Services\authorService;

$memberservice = new memberService();
$bookservice = new bookService();
$borrowingservice = new borrowingService();
$authorservice = new authorService();

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
            $label = ucfirst(str_replace('_', ' ', $key));
            echo $label . ": " . ($value ?? 'N/A') . PHP_EOL;
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
    echo "3. Add author" . PHP_EOL;
    echo "4. View authors" . PHP_EOL;
    echo "5. Add book with copies" . PHP_EOL;
    echo "6. View all books and authors" . PHP_EOL;
    echo "7. View available books" . PHP_EOL;
    echo "8. Borrow book" . PHP_EOL;
    echo "9. Return book" . PHP_EOL;
    echo "10. View member borrowing history" . PHP_EOL;
    echo "11. View member's current borrowings" . PHP_EOL;
    echo "12. View overdue borrowings" . PHP_EOL;
    echo "13. Books per category" . PHP_EOL;
    echo "14. Five most borrowed books" . PHP_EOL;
    echo "15. Full borrowing history" . PHP_EOL;
    echo "0. Exit" . PHP_EOL;

    $choice = readInput("Choose an option: ");

    try {
        switch ($choice) {
            case '1':
                $name = readInput("Member name: ");
                $email = readInput("Email: ");
                $phone = readInput("Phone: ");

               echo "Member registered successfully with ID: " .  $memberservice->registerMember($name, $email, $phone) . PHP_EOL;
                break;

            case '2':
                printTable($memberservice->getAllMembers());
                break;

            case '3':
                $name = readInput("Author name: ");
                $email = readInput("Email: ");
                $phone = readInput("Phone: ");
                echo "Author added successfully with ID:" . $authorservice->addAuthor($name, $email, $phone) . PHP_EOL;
                break;
            case '4':
                printTable($authorservice->getAllAuthors());    
            case '5':
                $title = readInput("Book title: ");
                $isbn = readInput("ISBN: ");
                $categoryId = (int) readInput("Category ID: ");
                $copies = (int) readInput("Number of copies: ");
                echo "Book added successfully with ID: ".$bookservice->addBook($title, $isbn, $categoryId, $copies) . PHP_EOL;
                echo "Number of copies created: $copies" . PHP_EOL;
                break;

            case '6':
                printTable($bookservice->getAllBooksWithAuthors());
                break;

            case '7':
                printTable($bookservice->getAvailableBooks());
                break;

            case '8':
                echo "Borrowing ID:" . $borrowingservice->borrowBook((int) readInput("Member ID: "), (int) readInput("Book ID: ")) . PHP_EOL;
                echo "Book borrowed successfully." . PHP_EOL;
                break;

            case '9':
                $borrowingservice->returnBook( (int) readInput("Borrowing ID: "));
                echo "Book returned successfully." . PHP_EOL;
                break;

            case '10':

                printTable($borrowingservice->getMemberHistory((int) readInput("Member ID: ")));
                break;

            case '11':
                printTable($borrowingservice->getCurrentlyBorrowed((int) readInput("Member ID: ")));
                break;

            case '12':
                printTable($borrowingservice->getOverdueBorrowings());
                break;

            case '13':
                printTable($bookservice->getBooksPerCategory());
                break;

            case '14':
                printTable($bookservice->getMostBorrowedBooks());
                break;

            case '15':
                printTable($borrowingservice->getFullHistory());
                break;

            case '0':
                echo "Goodbye!" . PHP_EOL;
                exit;

            default:
                echo "Invalid option. Please choose a number from the menu." . PHP_EOL;
        }
    } catch (InvalidArgumentException $e) {
        echo "Validation error: " . $e->getMessage() . PHP_EOL;
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage() . PHP_EOL;
    } catch (Throwable $e) {
        echo "Unexpected error: " . $e->getMessage() . PHP_EOL;
    }
}