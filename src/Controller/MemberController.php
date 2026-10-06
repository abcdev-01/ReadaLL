<?php

declare(strict_types=1);

namespace ReaDaLL\Controller;

use ReaDaLL\Model\Member;
use ReaDaLL\Repository\MemberRepository;
use ReaDaLL\Support\Validator;

final class MemberController
{
    public function __construct(
        private MemberRepository $members,
        private string $baseUrl
    ) {}

    public function index(): void
    {
        $members = $this->members->all();
        $this->render('members/index', ['members' => $members]);
    }

    public function create(): void
    {
        $errors = [];
        $old    = ['name' => '', 'email' => '', 'phone' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'name'  => trim((string) ($_POST['name']  ?? '')),
                'email' => trim((string) ($_POST['email'] ?? '')),
                'phone' => trim((string) ($_POST['phone'] ?? '')),
            ];

            $errors = Validator::validateMember($old);

            if (!$errors && $this->members->emailExists($old['email'])) {
                $errors['email'] = 'A member with this email already exists.';
            }

            if (!$errors) {
                $this->members->save(new Member(
                    null,
                    $old['name'],
                    $old['email'],
                    $old['phone'] ?: null
                ));

                header('Location: ' . $this->baseUrl . '/index.php?route=members');
                exit;
            }
        }

        $this->render('members/create', ['errors' => $errors, 'old' => $old]);
    }

    private function render(string $view, array $data = []): void
    {
        $baseUrl = $this->baseUrl;   // exposed to layout.php
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../views/layout.php';
    }
}