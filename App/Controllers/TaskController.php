<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Task;

class TaskController extends BaseController
{
    public function index(Request $request): Response
    {
        $tasks = Task::getAll();
        return $this->html(['tasks' => $tasks]);
    }

    public function add(Request $request): Response
    {
        return $this->html();
    }

    public function edit(Request $request): Response
    {
        $taskId = $request->get('id');
        $taskInstance = Task::getOne($taskId);
        return $this->html(compact('taskInstance'));
    }

    public function delete(Request $request): Response
    {
        $taskId = $request->value('id');
        $taskInstance = Task::getOne($taskId);
        $taskInstance->delete();
        return $this->redirect($this->url('task.index'));
    }

    public function save(Request $request): Response
    {
        $taskId = $request->value('id');
        if ($taskId > 0) {
            $taskInstance = Task::getOne($taskId);
        } else {
            $taskInstance = new Task();
        }
        $taskInstance->setName($request->value('name'));
        $taskInstance->setDescription($request->value('description'));
        $taskInstance->setStatus($request->value('status'));
        $taskInstance->setDeadline($request->value('deadline'));
        $taskInstance->setSubmission($request->value('submission'));
        $taskInstance->setPriority($request->value('priority'));
        $taskInstance->save();
        return $this->redirect($this->url('task.index'));
    }
}