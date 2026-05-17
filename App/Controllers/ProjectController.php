<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Project;
use App\Models\UserInProject;
use App\Models\User;
use Override;

class ProjectController extends BaseController
{
    #[Override]
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->app->getAppUser()->isLoggedIn()) {
            return false;
        }

        if ($action == 'index' || $action == 'add') {
            return true;
        }

        $projectId = $request->value('project');
        if ($projectId <= 0 && $action == 'save') {
            return true;
        }


        $userId = $this->app->getAppUser()->getId();
        $membership = UserInProject::getAll('`project_id` = ? and `user_id` = ?', [$projectId, $userId]);
        if (empty($membership)) {
            return false;
        }

        $membership = $membership[0];
        $role = $membership->getRights();
        if ($role == 'A') {
            return true;
        }

        return false;
    }

    public function index(Request $request): Response
    {
        $userId = $this->app->getAppUser()->getId();
        $userINprojects = UserInProject::getAll('`user_id` = ?', [$userId]);
        $projects = array();
        $roles = array();
        for ($x = 0; $x < count($userINprojects); $x++) {
            $projects[] = Project::getOne($userINprojects[$x]->getProjectId());
            $roles[] = $userINprojects[$x]->getRights();
        }
        return $this->html(['projects' => $projects, 'roles' => $roles]);
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
        $members = UserInProject::getAll('`project_id` = ?', [$projectId]);
        return $this->html(['projectInstance' => $projectInstance, 'members' => $members]);
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

        $errors = array();
        if (empty(trim($request->value('name')))) {
            $errors[] = "Názov projektu nesmie byť prázdny.";
        } elseif (mb_strlen(trim($request->value('name'))) < 3 || mb_strlen(trim($request->value('name'))) > 100) {
            $errors[] = "Dĺžka názvu projektu musí byť v rozmedzí od 3 do 100 znakov vrátane.";
        }
        $allowed_status = ['C', 'P', 'D', 'R'];
        if (!in_array($request->value('status'), $allowed_status)) {
            $errors[] = "Neplatná hodnota statusu";
        }
        $description = $request->value('description');
        if (mb_strlen(trim($description)) > 500) {
            $errors[] = "Popis projektu je príliš dlhý. Maximálny počet znakov je 500.";
        }
        $deadline = $request->value('deadline');
        if (!empty($deadline)) {
            if (strtotime($deadline) === false) {
                $errors[] = "Dátum dokončenia nie je v dobrom formáte.";
            }
            $projectInstance->setDeadline($deadline);
        } else {
            $projectInstance->setDeadline(NULL);
        }
        $submission = $request->value('submission');
        if (!empty($submission)) {
            if (strtotime($submission) === false) {
                $errors[] = "Dátum odovzdania nie je v dobrom formáte.";
            }
            $projectInstance->setSubmission($submission);
        } else {
            $projectInstance->setSubmission(NULL);
        }

        $projectInstance->setName(trim($request->value('name')));
        $projectInstance->setDescription(trim($description));
        $projectInstance->setStatus($request->value('status'));
        
        if (count($errors) > 0) {
            if ($projectId > 0) {
                $usersInProjectInstance = UserInProject::getAll('`project_id` = ?', [$projectId]);
                return $this->html(['errors' => $errors, 'projectInstance' => $projectInstance, 'members' => $usersInProjectInstance], 'edit');
            } else {
                return $this->html(['errors' => $errors, 'projectInstance' => $projectInstance], 'add');
            }
        }
        
        $projectInstance->save();

        if ($projectId > 0) {
            // pass
        } else {
            $userInProjectInstance = new UserInProject();
            $userInProjectInstance->setProjectId($projectInstance->getId());
            $userInProjectInstance->setUserId($this->user->getId());
            $userInProjectInstance->setRights('A');
            $userInProjectInstance->save();
        }

        return $this->redirect($this->url('project.index'));
    }

    public function add_member(Request $request): Response
    {
        $projectId = $request->value('project');
        if (!$projectId || $projectId <= 0) {
            return $this->redirect($this->url('project.index'));
        }
        $projectInstance = Project::getOne($projectId);
        if (!$projectInstance) {
            return $this->redirect($this->url('project.index'));
        }
        
        $errors = array();

        $email = $request->value('email');
        if (empty(trim($email))) {
            $errors[] = 'Pre vyhľadávanie používateľov zadajte email.';
        } else {
            $searched_users = User::getAll('`email` = ?', [$email]);
            if (empty($searched_users)) {
                $errors[] = 'Neexistuje žiadny používateľ so zadaným emailom.';
            } else {
                $searched_user = $searched_users[0];
                $already_in_database = UserInProject::getAll('`user_id` = ? and `project_id` = ?', [$searched_user->getId(), $projectId]);
                if (!empty($already_in_database)) {
                    $errors[] = 'Tento používateľ v tomto projekte už priradený je.';
                }    
            }

            $rights = $request->value('rights');
            $allowed_rights = ['R', 'W', 'A'];
            if (!in_array($rights, $allowed_rights)) {
                $errors[] = 'Neplatná hodnota práv.';
            }
            
            if (count($errors) > 0) {
                $members = UserInProject::getAll('`project_id` = ?', [$projectId]);
                return $this->html(['errors' => $errors, 'projectInstance' => $projectInstance, 'members' => $members], 'edit');
            }

            $new_user_in_project = new UserInProject();
            $new_user_in_project->setUserId($searched_users[0]->getId());
            $new_user_in_project->setProjectId($projectId);
            $new_user_in_project->setRights($rights);

            $new_user_in_project->save();
        }

        return $this->redirect($this->url('project.edit', ['project' => $projectId]));
    }

    public function edit_user(Request $request): Response
    {
        $projectId = $request->value('project');
        if (!$projectId || $projectId <= 0) {
            return $this->redirect($this->url('project.index'));
        }
        $projectInstance = Project::getOne($projectId);
        if (!$projectInstance) {
            return $this->redirect($this->url('project.index'));
        }
        $userId = $request->value('userId');
        if (!$userId || $userId <= 0) {
            return $this->redirect($this->url('project.edit', ['project' => $projectId]));
        }
        $userInstance = User::getOne($userId);
        if (!$userInstance) {
            return $this->redirect($this->url('project.edit', ['project' => $projectId]));
        }

        $errors = array();

        $users_in_project = UserInProject::getAll('`project_id` = ? and `user_id` = ?', [$projectId, $userId]);
        if (empty($users_in_project)) {
            return $this->redirect($this->url('project.edit', ['project' => $projectId]));
        }

        $user_in_project = $users_in_project[0];
        $rights = $request->value('rights');
        $allowed_rights = ['R', 'W', 'A'];
        if (!in_array($rights, $allowed_rights)) {
            $errors[] = 'Neplatná hodnota práv.';
        }
        
        if (count($errors) > 0) {
            $members = UserInProject::getAll('`project_id` = ?', [$projectId]);
            return $this->html(['errors' => $errors, 'projectInstance' => $projectInstance, 'members' => $members], 'edit');
        }

        $user_in_project->setRights($rights);
        $user_in_project->save();
        
        return $this->redirect($this->url('project.edit', ['project' => $projectId]));
    }

    public function remove_user(Request $request): Response
    {
        $projectId = $request->value('project');
        if (!$projectId || $projectId <= 0) {
            return $this->redirect($this->url('project.index'));
        }
        $projectInstance = Project::getOne($projectId);
        if (!$projectInstance) {
            return $this->redirect($this->url('project.index'));
        }

        $userId = $request->value('userId');
        if (!$userId || $userId <= 0) {
            return $this->redirect($this->url('project.edit', ['project' => $projectId]));
        }
        $userInstance = User::getOne($userId);
        if (!$userInstance) {
            return $this->redirect($this->url('project.edit', ['project' => $projectId]));
        }

        $searched_users_in_project = UserInProject::getAll('`project_id` = ? and `user_id` = ?', [$projectId, $userId]);
        if (empty($searched_users_in_project)) {
            return $this->redirect($this->url('project.edit', ['project' => $projectId]));
        }

        $searched_user_in_project = $searched_users_in_project[0];
        $searched_user_in_project->delete();

        return $this->redirect($this->url('project.edit', ['project' => $projectId]));
    }
}