<?php

namespace App\Models;

use Framework\Core\Model;

class Project extends Model
{
    protected ?int $project_id = null;
    protected ?string $name;
    protected ?string $description;
    protected ?string $status;
    protected ?string $deadline;
    protected ?string $submission;

    protected static function getTableName(): string
    {
        return 'projects';
    }

    protected static function getPkColumnName(): string
    {
        return 'project_id';
    }

    public function getId(): ?int
    {
        return $this->project_id;
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

    public function setDeadline(string $text): void
    {
        $this->deadline = $text;
    }

    public function getSubmission(): ?string
    {
        return $this->submission;
    }

    public function setSubmission(string $text): void
    {
        $this->submission = $text;
    }
}
