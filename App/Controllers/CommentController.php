<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;
use App\Models\UserInProject;
use App\Models\UserInTask;
use Framework\Http\HttpException;
use Override;

class CommentController extends BaseController
{
    #[Override]
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->app->getAppUser()->isLoggedIn()) {
            return false;
        }

        $projectId = $request->value('project');
        $taskId = $request->value('task');
        $userId = $this->app->getAppUser()->getId();
        $membership_in_project = UserInProject::getAll('`project_id` = ? and `user_id` = ?', [$projectId, $userId]);
        if (empty($membership_in_project)) {
            return false;
        }

        if ($action == 'index') {
            return true;
        }

        $membership_in_project = $membership_in_project[0];
        $role = $membership_in_project->getRights();

        if ($role == 'A') {
            return true;
        }

        if ($role == 'R') {
            return false;
        }

        $membership_in_task = UserInTask::getAll('`task_id` = ? and `user_id` = ?', [$taskId, $userId]);
        if (empty($membership_in_task)) {
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
        $comments = Comment::getAll('`task_id` = ?', [$taskId]);
        return $this->html(['comments' => $comments, 'taskId' => $taskId, 'projectId' => $projectId]);
    }

    public function add(Request $request): Response
    {
        if (!$request->isPost()) {
            throw new HttpException(405);
        }
        
        $json = $request->json();

        $taskId = $json->task_id;
        if (!$taskId || $taskId <= 0) {
            return $this->json(['error' => 'Úloha neexistuje.']);
        }
        $taskInstance = Task::getOne($taskId);
        if (!$taskInstance) {
            return $this->json(['error' => 'Úloha neexistuje.']);
        }

        $error_content = "";
        $comment_content = trim($json->comment_content);
        if (empty($comment_content)) {
            $error_content = $error_content . "Obsah komentáru nemôže byť prázdny. ";
        } elseif (mb_strlen($comment_content) > 500) {
            $error_content = $error_content . "Obsah komentáru nemôže byť dlhší ako 500 znakov. ";
        }

        if (!empty(trim($error_content))) {
            return $this->json(['error' => trim($error_content)]);
        }

        $comment = new Comment();
        $comment->setContent($comment_content);
        $comment->setTaskId($taskId);
        $comment->setUserId($this->user->getId());
        $comment->setCreation(date('Y-m-d H:i:s'));
        $comment->save();

        return $this->json([
            'comment_id' => $comment->getId(),
            'user_id' => $comment->getUserId(),
            'task_id' => $comment->getTaskId(),
            'content' => $comment->getContent(),
            'creation' => $comment->getCreation()
        ]);
    }

    public function edit(Request $request): Response
    {
        if (!$request->isPost()) {
            throw new HttpException(405);
        }

        $json = $request->json();

        $commentId = $json->commentId;
        if ($commentId <= 0 || !$commentId) {
            throw new HttpException(400);
        }

        $commentInstance = Comment::getOne($commentId);
        if (!$commentInstance) {
            throw new HttpException(404);
        }

        $error_content = "";
        $comment_content = trim($json->commentContent);
        if (empty($comment_content)) {
            $error_content = $error_content . "Obsah komentáru nemôže byť prázdny. ";
        } elseif (mb_strlen($comment_content) > 500) {
            $error_content = $error_content . "Obsah komentáru nemôže byť dlhší ako 500 znakov. ";
        }

        if ($this->user->getId() != $commentInstance->getUserId()) {
            $error_content = $error_content . "Nemôžete upravovať cudzie komentáre. ";
        }

        if (!empty($error_content)) {
            return $this->json(['error' => $error_content]);
        }

        $commentInstance->setContent($comment_content);
        $commentInstance->save();

        return $this->json($commentInstance);
    }

    public function delete(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->json(['success' => false]);
        }
        
        $commentId = ($request->json())->comment_id;
        $comment = Comment::getOne($commentId);
        if (!$comment) {
            return $this->json(['success' => false]);
        }

        if ($this->user->getId() != $comment->getUserId()) {
            return $this->json(['success' => false]);
        }

        $comment->delete();
        return $this->json(['success' => true]);
    }
}