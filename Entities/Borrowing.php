<?php

namespace Entities;

class Borrowing
{
    public const BORROWED = 'borrowed';
    public const RETURNED = 'returned';
    public const OVERDUE = 'overdue';

    private ?int $id;
    private int $memberId;
    private int $copyId;
    private string $borrowedAt;
    private string $dueAt;
    private ?string $returnedAt;
    private string $status;

    public function __construct(
        int $memberId,
        int $copyId,
        string $borrowedAt,
        string $dueAt,
        ?string $returnedAt = null,
        string $status = self::BORROWED,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->memberId = $memberId;
        $this->copyId = $copyId;
        $this->borrowedAt = $borrowedAt;
        $this->dueAt = $dueAt;
        $this->returnedAt = $returnedAt;
        $this->status = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMemberId(): int
    {
        return $this->memberId;
    }

    public function getCopyId(): int
    {
        return $this->copyId;
    }

    public function getBorrowedAt(): string
    {
        return $this->borrowedAt;
    }

    public function getDueAt(): string
    {
        return $this->dueAt;
    }

    public function getReturnedAt(): ?string
    {
        return $this->returnedAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}