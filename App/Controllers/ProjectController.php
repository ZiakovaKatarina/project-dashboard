<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Project;
use Override;

class ProjectController extends BaseController
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
        $projects = Project::getAll();
        return $this->html(['projects' => $projects]);
    }

    public function add(Request $request): Response
    {
        return $this->html();
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
        return $this->html(compact('projectInstance'));
    }

    public function delete(Request $request): Response
    {
        $projectId = $request->value('project');
        if ($projectId <= 0 || !$projectId) {
            return $this->redirect($this->url('project.index'));
        }
        $projectInstance = Project::getOne($projectId);
        if ($projectInstance) {
            $projectInstance->delete();
        }
        return $this->redirect($this->url('project.index'));
    }

    public function save(Request $request): Response
    {
        $projectId = $request->value('project');
        if ($projectId > 0) {
            $projectInstance = Project::getOne($projectId);
            if (!$projectInstance) {
                return $this->redirect($this->url('project.index'));
            }
        } else {
            $projectInstance = new Project();
        }
        $projectInstance->setName($request->value('name'));
        $projectInstance->setDescription($request->value('description'));
        $projectInstance->setStatus($request->value('status'));
        $projectInstance->setDeadline($request->value('deadline'));
        $projectInstance->setSubmission($request->value('submission'));
        $projectInstance->save();
        return $this->redirect($this->url('project.index'));
    }
}