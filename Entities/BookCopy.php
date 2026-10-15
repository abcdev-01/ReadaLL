<?php

namespace Entities;

class BookCopy
{
    public const AVAILABLE = 'available';
    public const BORROWED = 'borrowed';
    public const LOST = 'lost';

    private ?int $id;
    private int $bookId;
    private string $copyCode;
    private string $status;

    public function __construct(
        int $bookId,
        string $copyCode,
        string $status = self::AVAILABLE,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->bookId = $bookId;
        $this->copyCode = $copyCode;
        $this->status = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function getCopyCode(): string
    {
        return $this->copyCode;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isAvailable(): bool
    {
        return $this->status === self::AVAILABLE;
    }
}