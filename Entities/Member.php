<?php

namespace Entities;

class Member
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $phone;  
    private ?string $joinedAt;

    public function __construct(
         ?int $id=null,
        string $name,
        string $email,
        string $phone,
        ?string $joinedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->joinedAt = $joinedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPhone(): string { return $this->phone; }
    public function getJoinedAt(): ?string { return $this->joinedAt; }
}