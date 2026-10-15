<?php

namespace Services;

use Entities\Borrowing;
use Repositories\borrowingRepository;
use Repositories\bookCopyRepository;
use Repositories\memberRepository;

class borrowingService
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

        $borrowedAt = date('Y-m-d H:i:s');
        $dueAt = date('Y-m-d H:i:s', strtotime('+14 days'));

        $borrowing = new Borrowing(
            $memberId,
            (int) $copy['id'],
            $borrowedAt,
            $dueAt
        );

        $borrowingId = $this->borrowingRepository->create($borrowing);

        $this->copyRepository->markBorrowed((int) $copy['id']);

        return $borrowingId;
    }

    public function returnBook(int $borrowingId): void
    {
        $borrowing = $this->borrowingRepository->findById($borrowingId);

        if (!$borrowing) {
            throw new \InvalidArgumentException("Borrowing record not found.");
        }

        if ($borrowing['status'] !== Borrowing::BORROWED) {
            throw new \InvalidArgumentException("This book has already been returned.");
        }

        $returnedAt = date('Y-m-d H:i:s');

        $this->borrowingRepository->markReturned($borrowingId, $returnedAt);
        $this->copyRepository->markAvailable((int) $borrowing['copy_id']);
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