<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;
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
        } else {
            $comments = Comment::getAll('`task_id` = ?', [$taskId]);
            return $this->html(['comments' => $comments, 'taskId' => $taskId, 'projectId' => $projectId]);
        } 
    }

    public function add(Request $request): Response
    {
        if (!$request->isPost()) {
            throw new HttpException(405);
        }
        
        $json = $request->json();

        $comment = new Comment();
        $comment->setContent($json->comment_content);
        $comment->setTaskId($json->task_id);
        // $comment->setUserId();
        $comment->setCreation(date('Y-m-d H:i:s'));
        $comment->save();

        return $this->json([
            'comment_id' => $comment->getId(),
            // 'user_id' => $comment->getUserId(),
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

        $comment_content = $json->commentContent;
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

        $comment->delete();
        return $this->json(['success' => true]);
    }
}