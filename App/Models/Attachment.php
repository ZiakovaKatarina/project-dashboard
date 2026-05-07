<?php

namespace App\Models;

use Framework\Core\Model;

class Attachment extends Model
{
    protected ?int $attachment_id = null;
    protected ?int $task_id;
    protected ?string $path;
    protected ?string $filename;

    protected static function getTableName(): string
    {
        return 'attachments';
    }

    protected static function getPkColumnName(): string
    {
        return 'attachment_id';
    }

    public function getId(): ?int
    {
        return $this->attachment_id;
    }

    public function getTaskId(): ?string
    {
        return $this->task_id;
    }

    public function setTaskId(string $text): void
    {
        $this->task_id = $text;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $text): void
    {
        $this->path = $text;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $text): void
    {
        $this->filename = $text;
    }
}
