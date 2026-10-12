<?php

namespace ReaDaLL\Models;

class Member
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private string $phone,
        private ?string $joinedAt = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPhone(): string { return $this->phone; }
    public function getJoinedAt(): ?string { return $this->joinedAt; }
}