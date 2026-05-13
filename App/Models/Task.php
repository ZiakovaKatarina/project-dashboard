<?php

namespace App\Models;

use Framework\Core\Model;

class Task extends Model
{
    protected ?int $task_id = null;
    protected ?int $project_id;
    protected ?string $name;
    protected ?string $description;
    protected ?string $status;
    protected ?string $deadline;
    protected ?string $submission;
    protected ?int $priority;

    protected static function getTableName(): string
    {
        return 'tasks';
    }

    protected static function getPkColumnName(): string
    {
        return 'task_id';
    }

    public function getId(): ?int
    {
        return $this->task_id;
    }

    public function getProjectId(): ?int
    {
        return $this->project_id;
    }

    public function setProjectId(string $text): void
    {
        $this->project_id = $text;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $text): void
    {
        $this->name = $text;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $text): void
    {
        $this->description = $text;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $text): void
    {
        $this->status = $text;
    }

    public function getDeadline(): ?string
    {
        return $this->deadline;
    }

    public function setDeadline(?string $text): void
    {
        $this->deadline = $text;
    }

    public function getSubmission(): ?string
    {
        return $this->submission;
    }

    public function setSubmission(?string $text): void
    {
        $this->submission = $text;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $text): void
    {
        $this->priority = $text;
    }
}
