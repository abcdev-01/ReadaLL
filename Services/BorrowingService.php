<?php

namespace Services;

use Entities\Borrowing;
use Repositories\BorrowingRepository;
use Repositories\BookCopyRepository;
use Repositories\MemberRepository;

class BorrowingService
{
    private BorrowingRepository $borrowingRepository;
    private BookCopyRepository $copyRepository;
    private MemberRepository $memberRepository;

    public function __construct()
    {
        $this->borrowingRepository = new BorrowingRepository();
        $this->copyRepository = new BookCopyRepository();
        $this->memberRepository = new MemberRepository();
    }

    public function borrowBook(int $memberId, int $bookId): int
    {
        $member = $this->memberRepository->findById($memberId);

        if (!$member) {
            throw new \InvalidArgumentException("Member not found.");
        }

        $copy = $this->copyRepository->findAvailableCopy($bookId);

        if (!$copy) {
            throw new \InvalidArgumentException("No available copy for this book.");
        }

        $borrowDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+14 days'));

        $borrowing = new Borrowing(
            $memberId,
            (int) $copy['id'],
            $borrowDate,
            $dueDate
        );

        $borrowingId = $this->borrowingRepository->create($borrowing);

        $this->copyRepository->markBorrowed((int) $copy['id']);

        return $borrowingId;
    }

    public function returnBook(int $borrowingId): void
    {
        $stmt = \Config\Database::getConnection()->prepare(
            "SELECT * FROM borrowings WHERE id = :id"
        );

        $stmt->execute([':id' => $borrowingId]);
        $borrowing = $stmt->fetch();

        if (!$borrowing) {
            throw new \InvalidArgumentException("Borrowing record not found.");
        }

        if ($borrowing['status'] !== Borrowing::ACTIVE) {
            throw new \InvalidArgumentException("This book has already been returned.");
        }

        $returnDate = date('Y-m-d');

        $this->borrowingRepository->markReturned($borrowingId, $returnDate);
        $this->copyRepository->markAvailable((int) $borrowing['book_copy_id']);
    }

    public function getMemberHistory(int $memberId): array
    {
        return $this->borrowingRepository->getMemberHistory($memberId);
    }

    public function getCurrentlyBorrowed(int $memberId): array
    {
        return $this->borrowingRepository->getCurrentlyBorrowedByMember($memberId);
    }

    public function getOverdueBorrowings(): array
    {
        return $this->borrowingRepository->getOverdueBorrowings();
    }

    public function getFullHistory(): array
    {
        return $this->borrowingRepository->getFullHistory();
    }
}