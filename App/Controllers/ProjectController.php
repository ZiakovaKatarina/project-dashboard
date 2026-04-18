<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Project;

class ProjectController extends BaseController
{
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
        $projectId = $request->get('id');
        $projectInstance = Project::getOne($projectId);
        return $this->html(compact('projectInstance'));
    }

    public function delete(Request $request): Response
    {
        $projectId = $request->value('id');
        $projectInstance = Project::getOne($projectId);
        $projectInstance->delete();
        return $this->redirect($this->url('project.index'));
    }

    public function save(Request $request): Response
    {
        $projectId = $request->value('id');
        if ($projectId > 0) {
            $projectInstance = Project::getOne($projectId);
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