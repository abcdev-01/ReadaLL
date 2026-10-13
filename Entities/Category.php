<?php

namespace Entinties;

class Category{
    private ?int $id;
    private string $name;
    private ?string $joined_at;

    public function __construct(string $name, ?string $joined_at = null, ?int $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->joined_at = $joined_at;
    }   
     public function getId(): ?int { return $this->id; }    
     public function getName(): string { return $this->name; }
     public function getJoinedAt(): ?string { return $this->joined_at; }    
}