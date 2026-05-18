<?php 

/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\Project[] $projects */
/** @var string[] $roles */

?>

<h1>Zoznam projektov</h1>
<?php if (count($projects) == 0): ?>
    <div>Zatiaľ nie sú vytvorené žiadne projekty.</div>
<?php else: ?>
    <table>
        <tr>
            <th>Názov</th>
            <th>Popis</th>
            <th>Status</th>
            <th>Termín</th>
            <th>Odovzdanie</th>
            <th>Akcie</th>
        </tr>
        <?php for ($x = 0; $x < count($projects); $x++): ?>
            <?php $project = $projects[$x]; ?>
            <?php $role = $roles[$x]; ?>
            <tr>
                <td><?= htmlspecialchars($project->getName() ?? '') ?></td>
                <td><?= htmlspecialchars($project->getDescription() ?? '') ?></td>
                <td>
                    <?php if ($project->getStatus() === 'C'): ?>
                        vytvorený
                    <?php elseif ($project->getStatus() === 'P'): ?>
                        rozpracovaný
                    <?php elseif ($project->getStatus() === 'D'): ?>
                        dokončený
                    <?php elseif ($project->getStatus() === 'R'): ?>
                        odstránený
                    <?php endif ?>
                </td>
                <td><?= $project->getDeadline() ?></td>
                <td><?= $project->getSubmission() ?></td>
                <td>
                    <?php if ($role == 'A'): ?>
                        <a href="<?= $link->url('project.edit', ['project' => $project->getId()]) ?>">Upraviť</a>
                        <a href="<?= $link->url('project.delete', ['project' => $project->getId()]) ?>">Zmazať</a>
                    <?php endif; ?>
                    <a href="<?= $link->url('task.index', ['project' => $project->getId()]) ?>">Úlohy</a>
                </td>
            </tr>
        <?php endfor; ?>
    </table>
<?php endif; ?>
<a href="<?= $link->url('project.add') ?>">Pridať nový projekt</a>