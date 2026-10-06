<?php

declare(strict_types=1);

namespace ReadAll\Repository;

use PDO;
use ReadAll\Model\Member;

final class MysqlMemberRepository implements MemberRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return Member[] */
    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, name, email, phone, joined_at
             FROM members
             ORDER BY id DESC'
        );

        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function find(int $id): ?Member
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, phone, joined_at
             FROM members
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM members WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    public function save(Member $member): Member
    {
        if ($member->id() === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO members (name, email, phone)
                 VALUES (:name, :email, :phone)'
            );
            $stmt->execute([
                'name'  => $member->name(),
                'email' => $member->email(),
                'phone' => $member->phone(),
            ]);

            return $this->find((int) $this->pdo->lastInsertId());
        }

        $stmt = $this->pdo->prepare(
            'UPDATE members
             SET name = :name, email = :email, phone = :phone
             WHERE id = :id'
        );
        $stmt->execute([
            'id'    => $member->id(),
            'name'  => $member->name(),
            'email' => $member->email(),
            'phone' => $member->phone(),
        ]);

        return $this->find($member->id());
    }

    private function hydrate(array $row): Member
    {
        return new Member(
            (int) $row['id'],
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['joined_at']
        );
    }
}