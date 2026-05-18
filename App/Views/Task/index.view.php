<?php 

/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\Task[] $tasks */
/** @var int $projectId */
/** @var string $role */
/** @var int[] $states */
/** @var int[] $myStates */
/** @var App\Models\User $user */

?>

<a href="<?= $link->url('project.index') ?>">Späť</a>
<h1>Zoznam úloh</h1>
<?php if (count($tasks) == 0): ?>
    <div>Zatiaľ nie sú vytvorené žiadne úlohy.</div>
<?php else: ?>
    <table>
        <tr>
            <th>Názov</th>
            <th>Popis</th>
            <th>Status</th>
            <th>Termín</th>
            <th>Odovzdanie</th>
            <th>Priorita</th>
            <th>Stav</th>
            <th>Akcie</th>
        </tr>
        <?php for ($x = 0; $x < count($tasks); $x++): ?>
            <?php $task = $tasks[$x]; ?>
            <?php $state = $states[$x]; ?>
            <tr>
                <td><?= htmlspecialchars($task->getName() ?? '') ?></td>
                <td><?= htmlspecialchars($task->getDescription() ?? '') ?></td>
                <td class="no_break">
                    <?php if ($task->getStatus() === 'C'): ?>
                        vytvorený
                    <?php elseif ($task->getStatus() === 'P'): ?>
                        rozpracovaný
                    <?php elseif ($task->getStatus() === 'D'): ?>
                        dokončený
                    <?php elseif ($task->getStatus() === 'R'): ?>
                        odstránený
                    <?php endif ?>
                </td>
                <td class="no_break"><?= $task->getDeadline() ?></td>
                <td class="no_break"><?= $task->getSubmission() ?></td>
                <td class="no_break"><?= $task->getPriority() ?></td>
                <td class="no_break"><?= $state ?> %</td>
                <td>
                    <?php if ($role === 'A' || $role === 'W'): ?>
                        <a href="<?= $link->url('task.edit', ['task' => $task->getId(), 'project' => $projectId]) ?>">Upraviť</a>
                    <?php endif; ?>
                    <?php if ($role === 'A'): ?>
                        <a href="<?= $link->url('task.delete', ['task' => $task->getId(), 'project' => $projectId]) ?>">Zmazať</a>
                    <?php endif; ?>
                    <a href="<?= $link->url('comment.index', ['task' => $task->getId(), 'project' => $projectId]) ?>">Komentáre</a>
                    <a href="<?= $link->url('attachment.index', ['task' => $task->getId(), 'project' => $projectId]) ?>">Prílohy</a>
                    <a href="<?= $link->url('task.team', ['project' => $projectId, 'task' => $task->getId()]) ?>">Členovia</a>
                </td>
            </tr>
        <?php endfor; ?>
    </table>
<?php endif; ?>
<?php if ($role === 'A' || $role === 'W'): ?>
    <a href="<?= $link->url('task.add', ['project' => $projectId]) ?>">Pridať novú úlohu</a>
<?php endif; ?>