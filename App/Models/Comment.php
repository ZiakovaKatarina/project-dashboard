<?php

namespace App\Models;

use Framework\Core\Model;

class Comment extends Model
{
    protected ?int $comment_id = null;
    protected ?int $user_id;
    protected ?int $task_id;
    protected ?string $content;
    protected ?string $creation;

    protected static function getTableName(): string
    {
        return 'comments';
    }

    protected static function getPkColumnName(): string
    {
        return 'comment_id';
    }

    public function getId(): ?int
    {
        return $this->comment_id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getTaskId(): ?int
    {
        return $this->task_id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $text): void
    {
        $this->content = $text;
    }

    public function getCreation(): ?string
    {
        return $this->creation;
    }

    public function setCreation(string $text): void
    {
        $this->creation = $text;
    }
}
