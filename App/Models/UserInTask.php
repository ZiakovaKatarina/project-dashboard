<?php

namespace App\Models;

use Framework\Core\Model;

class UserInTask extends Model
{
    protected ?int $user_in_task_id = null;
    protected ?int $user_id;
    protected ?int $task_id;
    protected ?float $state;

    protected static function getTableName(): string
    {
        return 'users_in_tasks';
    }

    protected static function getPkColumnName(): string
    {
        return 'user_in_task_id';
    }

    public function getUserInTaskId(): ?int
    {
        return $this->user_in_task_id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getUser(): ?User
    {
        return User::getOne($this->user_id);
    }

    public function getTaskId(): ?int
    {
        return $this->task_id;
    }

    public function getTask(): ?Task
    {
        return Task::getOne($this->task_id);
    }

    public function getState(): ?float
    {
        return $this->state;
    }

    public function setState(float $text): void
    {
        $this->state = $text;
    }
}
