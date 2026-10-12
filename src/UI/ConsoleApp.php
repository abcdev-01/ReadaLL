<?php

namespace App\UI;

use App\Services\LibraryService;
use RuntimeException;

class ConsoleApp
{
    public function __construct(private LibraryService $service) {}

    public function run(): void
    {
        while (true) {
            echo "\n=== ReaDaLL Library Management ===\n";
            echo "1.  Register member\n";
            echo "2.  Add author\n";
            echo "3.  Add category\n";
            echo "4.  Add book\n";
            echo "5.  Add book copy\n";
            echo "6.  Borrow book\n";
            echo "7.  Return book\n";
            echo "8.  Report: available books\n";
            echo "9.  Report: member current loans\n";
            echo "10. Report: overdue loans\n";
            echo "11. Report: most borrowed books\n";
            echo "12. Report: books per category\n";
            echo "13. Report: full loan history\n";
            echo "0.  Exit\n";

            $choice = trim(readline("Choose: "));

            try {
                switch ($choice) {
                    case '1':  $this->registerMember(); break;
                    case '2':  $this->addAuthor(); break;
                    case '3':  $this->addCategory(); break;
                    case '4':  $this->addBook(); break;
                    case '5':  $this->addBookCopy(); break;
                    case '6':  $this->borrowBook(); break;
                    case '7':  $this->returnBook(); break;
                    case '8':  $this->printRows($this->service->getAvailableBooks()); break;
                    case '9':  $this->memberLoans(); break;
                    case '10': $this->printRows($this->service->getOverdueLoans()); break;
                    case '11': $this->printRows($this->service->getMostBorrowedBooks()); break;
                    case '12': $this->printRows($this->service->getBookCountByCategory()); break;
                    case '13': $this->printRows($this->service->getFullLoanHistory()); break;
                    case '0':
                        echo "Bye.\n";
                        return;
                    default:
                        echo "Invalid menu input.\n";
                }
            } catch (RuntimeException $e) {
                echo "Error: " . $e->getMessage() . "\n";
            } catch (\Throwable $e) {
                echo "Database/unexpected error: " . $e->getMessage() . "\n";
            }
        }
    }

    private function prompt(string $label): string
    {
        return trim(readline($label . ": "));
    }

    private function registerMember(): void
    {
        $name  = $this->prompt("Name");
        $email = $this->prompt("Email");
        $phone = $this->prompt("Phone");

        $id = $this->service->registerMember($name, $email, $phone);
        echo "Member registered with ID $id\n";
    }

    private function addAuthor(): void
    {
        $name  = $this->prompt("Author name");
        $email = $this->prompt("Author email");
        $phone = $this->prompt("Author phone");

        $id = $this->service->addAuthor($name, $email, $phone);
        echo "Author added with ID $id\n";
    }

    private function addCategory(): void
    {
        $name = $this->prompt("Category name");
        $id = $this->service->addCategory($name);
        echo "Category added with ID $id\n";
    }

    private function addBook(): void
    {
        $name       = $this->prompt("Book name");
        $isbn       = $this->prompt("ISBN");
        $categoryId = (int)$this->prompt("Category ID");

        $authorInput = $this->prompt("Author IDs (comma separated)");
        $authorIds = array_values(array_filter(
            array_map('intval', explode(',', $authorInput)),
            fn($v) => $v > 0
        ));

        $id = $this->service->addBook($name, $isbn, $categoryId, $authorIds);
        echo "Book added with ID $id\n";
    }

    private function addBookCopy(): void
    {
        $bookId   = (int)$this->prompt("Book ID");
        $copyCode = $this->prompt("Copy code");

        $id = $this->service->addBookCopy($bookId, $copyCode);
        echo "Book copy added with ID $id\n";
    }

    private function borrowBook(): void
    {
        $memberId = (int)$this->prompt("Member ID");
        $copyCode = $this->prompt("Copy code");

        $this->service->borrowBook($memberId, $copyCode);
        echo "Book borrowed. Due in 14 days.\n";
    }

    private function returnBook(): void
    {
        $copyCode = $this->prompt("Copy code");

        $this->service->returnBook($copyCode);
        echo "Book returned.\n";
    }

    private function memberLoans(): void
    {
        $members = $this->service->getAllMembers();

        if (!$members) {
            echo "No members found.\n";
            return;
        }

        foreach ($members as $member) {
            echo "{$member['id']}) {$member['name']} <{$member['email']}>\n";
        }

        $memberId = (int)$this->prompt("Member ID");
        $rows = $this->service->getMemberCurrentLoans($memberId);

        $this->printRows($rows);
    }

    private function printRows(array $rows): void
    {
        if (!$rows) {
            echo "No records found.\n";
            return;
        }

        foreach ($rows as $i => $row) {
            echo ($i + 1) . ") ";

            foreach ($row as $key => $value) {
                echo "$key=" . ($value ?? '-') . "  ";
            }

            echo "\n";
        }
    }
}