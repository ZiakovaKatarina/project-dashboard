<?php

namespace App\Models;

use Framework\Core\Model;

class UserInProject extends Model
{
    protected ?int $user_in_project_id = null;
    protected ?int $user_id;
    protected ?int $project_id;
    protected ?string $rights;

    protected static function getTableName(): string
    {
        return 'users_in_projects';
    }

    protected static function getPkColumnName(): string
    {
        return 'user_in_project_id';
    }

    public function getUserInProjectId(): ?int
    {
        return $this->user_in_project_id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getUser(): ?User
    {
        return User::getOne($this->user_id);
    }

    public function setUserId(int $text): void
    {
        $this->user_id = $text;
    }

    public function getProjectId(): ?int
    {
        return $this->project_id;
    }

    public function getProject(): ?Project
    {
        return Project::getOne($this->project_id);
    }

    public function setProjectId(int $text): void
    {
        $this->project_id = $text;
    }

    public function getRights(): ?string
    {
        return $this->rights;
    }

    public function setRights(string $text): void
    {
        $this->rights = $text;
    }
}
