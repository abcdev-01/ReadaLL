<?php

namespace Services;

use Entities\Member;
use Repositories\memberRepository;

class memberService
{
    private memberRepository $memberrepository;

    public function __construct()
    {
        $this->memberrepository = new memberRepository();
    }

    public function registerMember(string $name, string $email, string $phone): int
    {
        $name = trim($name);
        $email = strtolower(trim($email));
        $phone = trim($phone);

        if ($name === '') {
            throw new \InvalidArgumentException("Member name is required.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email address.");
        }

        if ($phone === '') {
            throw new \InvalidArgumentException("Phone number is required.");
        }

        if ($this->memberrepository->emailExists($email)) {
            throw new \InvalidArgumentException("A member with this email already exists.");
        }

        $member = new Member($name, $email, $phone);

        return $this->memberrepository->create($member);
    }

    public function getMember(int $id): array
    {
        $member = $this->memberrepository->findById($id);

        if (!$member) {
            throw new \InvalidArgumentException("Member not found.");
        }

        return $member;
    }

    public function getAllMembers(): array
    {
        return $this->memberrepository->getAll();
    }
}