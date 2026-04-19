<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Task;
use App\Models\Project;

class TaskController extends BaseController
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
        } else {
            $tasks = Task::getAll('`project_id` = ?', [$projectId]);
            return $this->html(['tasks' => $tasks, 'projectId' => $projectId]);
        }
    }

    public function add(Request $request): Response
    {
        $projectId = $request->value('project');
        if ($projectId <= 0 || !$projectId) {
            return $this->redirect($this->url('project.index'));
        }
        $projectInstance = Project::getOne($projectId);
        if (!$projectInstance) {
            return $this->redirect($this->url('project.index'));
        }
        return $this->html(['projectId' => $projectId]);
    }

    public function edit(Request $request): Response
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
        if ($taskInstance->getProjectId() != $projectId) {
            return $this->redirect($this->url('project.index'));
        }
        return $this->html(['taskInstance' => $taskInstance, 'projectId' => $projectId]);
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
            return $this->redirect($this->url('task.index', ['project' => $projectId]));
        }
        $taskInstance = Task::getOne($taskId);
        if ($taskInstance->getProjectId() != $projectId) {
            return $this->redirect($this->url('project.index'));
        }
        if ($taskInstance) {
            $taskInstance->delete();
        }
        return $this->redirect($this->url('task.index', ['project' => $projectId]));
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
        if ($taskId > 0) {
            $taskInstance = Task::getOne($taskId);
            if (!$taskInstance) {
                return $this->redirect($this->url('task.index', ['project' => $projectId]));
            }
        } else {
            $taskInstance = new Task();
            $taskInstance->setProjectId($projectId);
        }
        $taskInstance->setName($request->value('name'));
        $taskInstance->setDescription($request->value('description'));
        $taskInstance->setStatus($request->value('status'));
        $taskInstance->setDeadline($request->value('deadline'));
        $taskInstance->setSubmission($request->value('submission'));
        $taskInstance->setPriority($request->value('priority'));
        $taskInstance->save();
        return $this->redirect($this->url('task.index', ['project' => $projectId]));
    }
}