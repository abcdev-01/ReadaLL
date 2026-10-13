<?php 
namespace Entities;

class Book{
    private ?int $id;
    private string $title;
    private string $isbn;
    private int $categoryid;    
    public function __construct(string $title, String 
    $isbn, int $categoryid, ?int $id = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->isbn = $isbn;
        $this->categoryid = $categoryid;  
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getIsbn(): string { return $this->isbn; }
    public function getCategoryId(): int { return $this->categoryid; }
}