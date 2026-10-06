<?php

declare(strict_types=1);

namespace ReaDaLL\Model;

final class Member
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private ?string $phone = null,
        private ?string $joinedAt = null
    ) {}

    public function id(): ?int          { return $this->id; }
    public function name(): string      { return $this->name; }
    public function email(): string     { return $this->email; }
    public function phone(): ?string    { return $this->phone; }
    public function joinedAt(): ?string { return $this->joinedAt; }

    public function rename(string $name): void
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }
        $this->name = $name;
    }

    public function changeContact(?string $phone): void
    {
        $this->phone = $phone;
    }
}