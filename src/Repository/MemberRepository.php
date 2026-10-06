<?php

declare(strict_types=1);

namespace ReadAll\Repository;

use ReadAll\Model\Member;

interface MemberRepository
{
    /** @return Member[] */
    public function all(): array;

    public function find(int $id): ?Member;

    public function emailExists(string $email): bool;

    public function save(Member $member): Member;
}