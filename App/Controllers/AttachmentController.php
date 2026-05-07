<?php

namespace App\Controllers;

use App\Configuration;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Project;
use App\Models\Task;
use App\Models\Attachment;

class AttachmentController extends BaseController
{
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
        } else {
            $attachments = Attachment::getAll('`task_id` = ?', [$taskId]);
            return $this->html(['attachments' => $attachments, 'taskId' => $taskId, 'projectId' => $projectId]);
        }
    }

    // public function add(Request $request): Response
    // {
    //     $projectId = $request->value('project');
    //     if ($projectId <= 0 || !$projectId) {
    //         return $this->redirect($this->url('project.index'));
    //     }
    //     $projectInstance = Project::getOne($projectId);
    //     if (!$projectInstance) {
    //         return $this->redirect($this->url('project.index'));
    //     }
    //     $taskId = $request->value('task');
    //     if ($taskId <= 0 || !$taskId) {
    //         return $this->redirect($this->url('task.index'));
    //     }
    //     $taskInstance = Task::getOne($taskId);
    //     if (!$taskInstance) {
    //         return $this->redirect($this->url('task.index'));
    //     }
    //     return $this->html(['taskId' => $taskId, 'projectId' => $projectId]);
    // }

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
        $attachmentInstance = new Attachment();
        $file = $request->file('input_new_attachment');
        if ($file && $file->isOk()) {
            $filename = time() . "_" . $file->getName();
            $path = Configuration::UPLOAD_DIR;
            
            $file->store($path . $filename);

            $attachmentInstance->setTaskId($taskId);
            $attachmentInstance->setFilename($filename);
            $attachmentInstance->setPath($path);
            $attachmentInstance->save();
        }
        
        return $this->redirect($this->url('attachment.index', ['task' => $taskId, 'project' => $projectId]));
    }
}