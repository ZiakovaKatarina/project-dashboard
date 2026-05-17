<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Task;
use App\Models\Project;
use App\Models\UserInTask;
use App\Models\UserInProject;
use App\Models\User;
use Override;

class TaskController extends BaseController
{
    #[Override]
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->app->getAppUser()->isLoggedIn()) {
            return false;
        }

        $projectId = $request->value('project');
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

        if ($role == 'W') {
            if ($action == 'edit' || $action == 'save' || $action == 'add') {
                return true;
            }
        }
        
        return false;
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
        $tasks = Task::getAll('`project_id` = ?', [$projectId]);
        $userId = $this->app->getAppUser()->getId();
        $membership_in_project = UserInProject::getAll('`project_id` = ? and `user_id` = ?', [$projectId, $userId]);
        if (empty($membership_in_project)) {
            $role = null;
        } else {
            $role = ($membership_in_project[0])->getRights();
        }
        return $this->html(['tasks' => $tasks, 'projectId' => $projectId, 'role' => $role]);
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
        $members = UserInTask::getAll('`task_id` = ?', [$taskId]);
        $userId = $this->app->getAppUser()->getId();
        $membership_in_project = UserInProject::getAll('`project_id` = ? and `user_id` = ?', [$projectId, $userId]);
        if (empty($membership_in_project)) {
            $role = null;
        } else {
            $role = ($membership_in_project[0])->getRights();
        }
        return $this->html(['taskInstance' => $taskInstance, 'projectId' => $projectId, 'members' => $members, 'role' => $role]);
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
        $taskInstance->setPriority($request->value('priority'));

        $errors = array();
        if (empty(trim($request->value('name')))) {
            $errors[] = "Názov úlohy nesmie byť prázdny.";
        } elseif (mb_strlen(trim($request->value('name'))) < 3 || mb_strlen(trim($request->value('name'))) > 100) {
            $errors[] = "Dĺžka názvu úlohy musí byť v rozmedzí od 3 do 100 znakov vrátane.";
        }
        $allowed_status = ['C', 'P', 'D', 'R'];
        if (!in_array($request->value('status'), $allowed_status)) {
            $errors[] = "Neplatná hodnota statusu.";
        }
        $priority = $request->value('priority');
        if ($priority <= 0 || $priority > 10) {
            $errors[] = "Priorita musí byť z intervalu < 1 ; 10 >";
        }
        $description = $request->value('description');
        if (mb_strlen(trim($description)) > 500) {
            $errors[] = "Popis úlohy je príliš dlhý. Maximálny počet znakov je 500.";
        }
        
        $deadline = $request->value('deadline');
        if (!empty($deadline)) {
            if (strtotime($deadline) === false) {
                $errors[] = "Dátum dokončenia nie je v dobrom formáte.";
            }
            $taskInstance->setDeadline($request->value('deadline'));
        } else {
            $taskInstance->setDeadline(NULL);
        }
        $submission = $request->value('submission');
        if (!empty($submission)) {
            if (strtotime($submission) === false) {
                $errors[] = "Dátum odovzdania nie je v dobrom formáte.";
            }
            $taskInstance->setSubmission($request->value('submission'));
        } else {
            $taskInstance->setSubmission(NULL);
        }

        if (count($errors) > 0) {
            $userId = $this->app->getAppUser()->getId();
            $membership_in_project = UserInProject::getAll('`project_id` = ? and `user_id` = ?', [$projectId, $userId]);
            if (empty($membership_in_project)) {
                $role = null;
            } else {
                $role = ($membership_in_project[0])->getRights();
            }
            if ($taskId) {
                $usersInTaskInstance = UserInTask::getAll('`task_id` = ?', [$taskId]);
                return $this->html(['errors' => $errors, 'role' => $role, 'taskInstance' => $taskInstance, 'projectId' => $projectId, 'members' => $usersInTaskInstance], 'edit');
            } else {
                return $this->html(['errors' => $errors, 'role' => $role, 'taskInstance' => $taskInstance, 'projectId' => $projectId], 'add');
            }
        }

        $taskInstance->save();

        if ($taskId > 0) {
            // pass
        } else {
            $userInTaskInstance = new UserInTask();
            $userInTaskInstance->setTaskId($taskInstance->getId());
            $userInTaskInstance->setUserId($this->user->getId());
            $userInTaskInstance->setState(0);
            $userInTaskInstance->save();
        }

        return $this->redirect($this->url('task.index', ['project' => $projectId]));
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

        $taskId = $request->value('task');
        if (!$taskId || $taskId <= 0) {
            return $this->redirect($this->url('task.index'));
        }
        $taskInstance = Task::getOne($taskId);
        if (!$taskInstance) {
            return $this->redirect($this->url('task.index'));
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
                $is_user_in_project = UserInProject::getAll('`user_id` = ? and `project_id` = ?', [$searched_user->getId(), $projectId]);
                if (empty($is_user_in_project)) {
                    $errors[] = 'Tento používateľ nie je priradený do tohto projektu. Najskôr priraďte používateľa do projektu, a až potom ho pridajte k úlohe. ';
                } else {
                    $already_in_database = UserInTask::getAll('`user_id` = ? and `task_id` = ?', [$searched_user->getId(), $taskId]);
                    if (!empty($already_in_database)) {
                        $errors[] = 'Tento používateľ k tejto úlohe už priradený je.';
                    }
                }
            }

            $state = $request->value('state');
            if (!$state) {
                $state = 0;
            }
            if ($state < 0 || $state > 100) {
                $errors[] = 'Stav úlohy musí byť číslo od 0 do 100 vrátane.';
            }
            
            if (count($errors) > 0) {
                $members = UserInTask::getAll('`task_id` = ?', [$taskId]);
                $userId = $this->app->getAppUser()->getId();
                $membership_in_project = UserInProject::getAll('`project_id` = ? and `user_id` = ?', [$projectId, $userId]);
                if (empty($membership_in_project)) {
                    $role = null;
                } else {
                    $role = ($membership_in_project[0])->getRights();
                }
                return $this->html(['errors' => $errors, 'role' => $role, 'taskInstance' => $taskInstance, 'members' => $members, 'projectId' => $projectId], 'edit');
            }

            $new_user_in_task = new UserInTask();
            $new_user_in_task->setUserId($searched_users[0]->getId());
            $new_user_in_task->setTaskId($taskId);
            $new_user_in_task->setState($state);

            $new_user_in_task->save();
        }

        return $this->redirect($this->url('task.edit', ['project' => $projectId, 'task' => $taskId]));
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
        $taskId = $request->value('task');
        if (!$taskId || $taskId <= 0) {
            return $this->redirect($this->url('task.index'));
        }
        $taskInstance = Task::getOne($taskId);
        if (!$taskInstance) {
            return $this->redirect($this->url('task.index'));
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
            return $this->redirect($this->url('task.edit', ['project' => $projectId, 'task' => $taskId]));
        } else {
            $user_in_project = $users_in_project[0];
            $searched_users_in_task = UserInTask::getAll('`user_id` = ? and `task_id` = ?', [$userId, $taskId]);
            if (empty($searched_users_in_task)) {
                return $this->redirect($this->url('task.edit', ['project' => $projectId, 'task' => $taskId]));
            }
            $searched_user_in_task = $searched_users_in_task[0];

            $state = $request->value('state');
            if (!$state) {
                $state = 0;
            }
            if ($state < 0 || $state > 100) {
                $errors[] = 'Stav úlohy musí byť číslo od 0 do 100 vrátane.';
            }
            
            if (count($errors) > 0) {
                $members = UserInTask::getAll('`task_id` = ?', [$taskId]);
                $userId = $this->app->getAppUser()->getId();
                $membership_in_project = UserInProject::getAll('`project_id` = ? and `user_id` = ?', [$projectId, $userId]);
                if (empty($membership_in_project)) {
                    $role = null;
                } else {
                    $role = ($membership_in_project[0])->getRights();
                }
                return $this->html(['errors' => $errors, 'role' => $role, 'taskInstance' => $taskInstance, 'members' => $members, 'projectId' => $projectId], 'edit');
            }

            $searched_user_in_task->setState($state);
            $searched_user_in_task->save();
        }
        
        return $this->redirect($this->url('task.edit', ['project' => $projectId, 'task' => $taskId]));
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

        $taskId = $request->value('task');
        if (!$taskId || $taskId <= 0) {
            return $this->redirect($this->url('task.index'));
        }
        $taskInstance = Task::getOne($taskId);
        if (!$taskInstance) {
            return $this->redirect($this->url('task.index'));
        }

        $userId = $request->value('userId');
        if (!$userId || $userId <= 0) {
            return $this->redirect($this->url('task.edit', ['project' => $projectId, 'task' => $taskId]));
        }
        $userInstance = User::getOne($userId);
        if (!$userInstance) {
            return $this->redirect($this->url('task.edit', ['project' => $projectId, 'task' => $taskId]));
        }

        $is_user_in_project = UserInProject::getAll('`user_id` = ? and `project_id` = ?', [$userId, $projectId]);
        if (empty($is_user_in_project)) {
            return $this->redirect($this->url('task.edit', ['project' => $projectId, 'task' => $taskId]));
        } else {
            $searched_users_in_task = UserInTask::getAll('`user_id` = ? and `task_id` = ?', [$userId, $taskId]);
            if (empty($searched_users_in_task)) {
                return $this->redirect($this->url('task.edit', ['project' => $projectId, 'task' => $taskId]));
            }

            $searched_user_in_task = $searched_users_in_task[0];
            $searched_user_in_task->delete();
        }

        return $this->redirect($this->url('task.edit', ['project' => $projectId, 'task' => $taskId]));
    }
}