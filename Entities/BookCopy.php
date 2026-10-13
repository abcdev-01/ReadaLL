<?php
namespace Entities;

class BookCopy{
    public const AVAILABLE = 'AVAILABLE';
    public const BORROWED = 'BORROWED';

    private ?int $id;
    private int $bookId;
    private int $copyNumber;
    private string $status;
    public function __construct(int $bookId, int $copyNumber, string $status = self::AVAILABLE, ?int $id = null)
    {
        $this->id = $id;
        $this->bookId = $bookId;
        $this->copyNumber = $copyNumber;
        $this->status = $status;
    }
    public function getId(): ?int{ return $this->id; }
    public function getBookId(): int{ return $this->bookId; }
    public function getCopyNumber(): int{ return $this->copyNumber; }
    public function getStatus(): string{ return $this->status; }
}