<?php

namespace App\Models;

use Framework\Core\Model;

class User extends Model
{
    protected ?int $user_id = null;
    protected ?string $first_name;
    protected ?string $last_name;
    protected ?string $email;
    protected ?string $password;
    protected ?bool $admin;

    protected static function getTableName(): string
    {
        return 'users';
    }

    protected static function getPkColumnName(): string
    {
        return 'user_id';
    }

    public function getId(): ?int
    {
        return $this->user_id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $text): void
    {
        $this->first_name = $text;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $text): void
    {
        $this->last_name = $text;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $text): void
    {
        $this->email = $text;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $text): void
    {
        $this->password = $text;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $text): void
    {
        $this->admin = $text;
    }
}
