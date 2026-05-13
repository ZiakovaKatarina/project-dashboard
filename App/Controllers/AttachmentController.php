<?php

namespace App\Controllers;

use App\Configuration;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Project;
use App\Models\Task;
use App\Models\Attachment;
use Override;

class AttachmentController extends BaseController
{
    #[Override]
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->app->getAppUser()->isLoggedIn()) {
            return false;
        }

        return true;
    }

    public function index(Request $request): Response
    {
        $projectId = $request->value('project');
        if ($projectId <= 0 || !$projectId) {
            return $this->redirect($this->url('project.index'));
        }
        $projectInstance = Project::getOne($projectId);
        if (!$projectInstance) {
            return $this->redirect($this->url('project.index'));
        }
        $taskId = $request->value('task');
        if ($taskId <= 0 || !$taskId) {
            return $this->redirect($this->url('task.index'));
        }
        $taskInstance = Task::getOne($taskId);
        if (!$taskInstance) {
            return $this->redirect($this->url('task.index'));
        }
        if ($taskInstance->getProjectId() != $projectId) {
            return $this->redirect($this->url('project.index'));
        }
        
        $attachments = Attachment::getAll('`task_id` = ?', [$taskId]);
        return $this->html(['attachments' => $attachments, 'taskId' => $taskId, 'projectId' => $projectId]);
    }

    public function delete(Request $request): Response
    {
        $projectId = $request->value('project');
        if ($projectId <= 0 || !$projectId) {
            return $this->redirect($this->url('project.index'));
        }
        $projectInstance = Project::getOne($projectId);
        if (!$projectInstance) {
            return $this->redirect($this->url('project.index'));
        }
        $taskId = $request->value('task');
        if ($taskId <= 0 || !$taskId) {
            return $this->redirect($this->url('task.index'));
        }
        $taskInstance = Task::getOne($taskId);
        if (!$taskInstance) {
            return $this->redirect($this->url('task.index'));
        }
        $attachmentId = $request->value('attachment');
        if ($attachmentId <= 0 || !$attachmentId) {
            return $this->redirect($this->url('attachment.index'));
        }
        $attachmentInstance = Attachment::getOne($attachmentId);
        if ($attachmentInstance->getTaskId() != $taskId) {
            return $this->redirect($this->url('task.index'));
        }
        if ($attachmentInstance) {
            $attachmentInstance->delete();
        }
        return $this->redirect($this->url('attachment.index', ['task' => $taskId, 'project' => $projectId]));
    }

    public function save(Request $request): Response
    {
        $projectId = $request->value('project');
        if ($projectId <= 0 || !$projectId) {
            return $this->redirect($this->url('project.index'));
        }
        $projectInstance = Project::getOne($projectId);
        if (!$projectInstance) {
            return $this->redirect($this->url('project.index'));
        }
        $taskId = $request->value('task');
        if ($taskId <= 0 || !$taskId) {
            return $this->redirect($this->url('task.index', ['project' => $projectId]));
        }
        $taskInstance = Task::getOne($taskId);
        if (!$taskInstance) {
            return $this->redirect($this->url('task.index', ['project' => $projectId]));
        }

        $file = $request->file('input_new_attachment');
        $path = Configuration::UPLOAD_DIR;
        
        $errors = array();
        if (!$file) {
            $errors[] = "Nebol vybraný žiadny súbor.";
        } elseif (!$file->isOk()) {
            $errors[] = "Nastala chyba pri nahrávaní súboru.";
        } elseif ($file->getSize() > 2 * 1024 * 1024) {
            $errors[] = "Súbor je príliš veľký. Maximálna možná veľkosť súboru sú 2 MB.";
        }
        
        if ($file) {
            $filename = $file->getName();
            
            // lebo DB limit je 50 znakov a to minus time() _
            if (mb_strlen($filename) > 39) {
                $errors[] = "Názov súboru je príliš dlhý (max. 39 znakov).";
            }
        }

        if (mb_strlen($path) > 100) {
            $errors[] = "Cesta k súboru je príliš dlhá (max. 100 znakov).";
        }

        if (count($errors) > 0) {
            $attachments = Attachment::getAll('`task_id` = ?', [$taskId]);
            return $this->html(['errors' => $errors, 'projectId' => $projectId, 'taskId' => $taskId, 'attachments' => $attachments], 'index');
        }

        $filename = time() . "_" . $file->getName();

        $attachmentInstance = new Attachment();
        $attachmentInstance->setFilename($filename);
        $attachmentInstance->setPath($path);
        $attachmentInstance->setTaskId($taskId);
        $attachmentInstance->save();

        $file->store($path . $filename);
        
        return $this->redirect($this->url('attachment.index', ['task' => $taskId, 'project' => $projectId]));
    }
}