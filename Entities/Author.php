<?php 
namespace Entities;
class Author {
    private ?int $id;
    private string $name;
    private string $email;
    private string $phone;
    private ?string $joined_at;

    public function __construct(string $name,string $email,string $phone, ?string $joined_at = null, ?int $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->joined_at = $joined_at;
    }   
     public function getId(): ?int { return $this->id; }    
     public function getname(): string { return $this->name; }  
     public function getEmail(): string { return $this->email; }  
     public function getPhone(): string { return $this->phone; }  
     public function getJoinedAt(): ?string { return $this->joined_at; }  

}    