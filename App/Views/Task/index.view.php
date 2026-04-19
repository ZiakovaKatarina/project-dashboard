<?php ?>

<a href="<?= $link->url('project.index') ?>">Späť</a>
<h1>Zoznam úloh</h1>
<?php if (count($tasks) == 0): ?>
    <div>Zatiaľ nie sú vytvorené žiadne úlohy.</div>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Názov</th>
            <th>Popis</th>
            <th>Status</th>
            <th>Termín odovzdania</th>
            <th>Odovzdanie</th>
            <th>Priorita</th>
            <th>Akcie</th>
        </tr>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= $task->getId() ?></td>
                <td><?= $task->getName() ?></td>
                <td><?= $task->getDescription() ?></td>
                <td>
                    <?php if ($task->getStatus() === 'C'): ?>
                        vytvorený
                    <?php elseif ($task->getStatus() === 'P'): ?>
                        pracuje sa na ňom
                    <?php elseif ($task->getStatus() === 'D'): ?>
                        dokončený
                    <?php elseif ($task->getStatus() === 'R'): ?>
                        odstránený
                    <?php endif ?>
                </td>
                <td><?= $task->getDeadline() ?></td>
                <td><?= $task->getSubmission() ?></td>
                <td><?= $task->getPriority() ?></td>
                <td><a href="<?= $link->url('task.edit', ['task' => $task->getId(), 'project' => $projectId]) ?>">Upraviť</a></td>
                <td><a href="<?= $link->url('task.delete', ['task' => $task->getId(), 'project' => $projectId]) ?>">Zmazať</a></td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif; ?>
<a href="<?= $link->url('task.add', ['project' => $projectId]) ?>">Pridať novú úlohu</a>