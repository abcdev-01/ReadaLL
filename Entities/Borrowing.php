<?php

namespace Entities;

class Borrowing{
    public const ACTIVE = 'ACTIVE';
    public const RETURNED = 'RETURNED';
    public const OVERDUE = 'OVERDUE';

    private ?int $id;
    private  int $memberId;
    private int $bookCopyId;
    private String $borrowDate;
    private string $dueDate;
    private ?string $returnDate;
    private string $status;

    public function __function(
        int $memberId,
        int $bookCopyId ,
        string $borrowDate,
        string $dueDate,
        ?string $returnDate = null,
        string $status = self::ACTIVE,
        ?int $id = null
    ){
$this->id = $id;
$this->memberId =  $memberId;
$this->bookCopyId = $bookCopyId;
$this->borrowDate = $borrowDate;
$this->dueDate = $dueDate;
$this->returnDate = $returnDate;
$this->status = $status;
    }

    public function getId(): ?int { return $this->id; }
    public function getMemberId(): int { return $this->memberId; }
    public function getBookCopyId(): int { return $this->bookCopyId;}
    public function getBorrowDate(): string { return $this->borrowDate; }
    public function getDueDate(): string { return $this->dueDate; }
    public function getReturnDate(): ?string { return $this->returnDate; }
    public function getStatus(): string {return $this->status;}

}