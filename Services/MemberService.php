<?php

namespace Services;

use Entities\Member;
use Repositories\MemberRepository;

class MemberService
{
    private MemberRepository $repository;

    public function __construct()
    {
        $this->repository = new MemberRepository();
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

        if ($this->repository->emailExists($email)) {
            throw new \InvalidArgumentException("A member with this email already exists.");
        }

        $member = new Member($name, $email, $phone);

        return $this->repository->create($member);
    }

    public function getMember(int $id): array
    {
        $member = $this->repository->findById($id);

        if (!$member) {
            throw new \InvalidArgumentException("Member not found.");
        }

        return $member;
    }

    public function getAllMembers(): array
    {
        return $this->repository->getAll();
    }
}