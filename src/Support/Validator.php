<?php

declare(strict_types=1);

namespace ReaDaLL\Support;

final class Validator
{
    /** @return array<string,string> */
    public static function validateMember(array $data): array
    {
        $errors = [];

        if (trim((string) ($data['name'] ?? '')) === '') {
            $errors['name'] = 'Name is required.';
        }

        $email = trim((string) ($data['email'] ?? ''));
        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address.';
        }

        if (!empty($data['phone'])
            && !preg_match('/^[0-9+\-\s]{5,30}$/', (string) $data['phone'])) {
            $errors['phone'] = 'Invalid phone number.';
        }

        return $errors;
    }
}