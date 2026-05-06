<?php 

/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\Project[] $projects */

?>

<h1>Zoznam projektov</h1>
<?php if (count($projects) == 0): ?>
    <div>Zatiaľ nie sú vytvorené žiadne projekty.</div>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Názov</th>
            <th>Popis</th>
            <th>Status</th>
            <th>Termín odovzdania</th>
            <th>Odovzdanie</th>
            <th>Akcie</th>
        </tr>
        <?php foreach ($projects as $project): ?>
            <tr>
                <td><?= $project->getId() ?></td>
                <td><?= $project->getName() ?></td>
                <td><?= $project->getDescription() ?></td>
                <td>
                    <?php if ($project->getStatus() === 'C'): ?>
                        vytvorený
                    <?php elseif ($project->getStatus() === 'P'): ?>
                        pracuje sa na ňom
                    <?php elseif ($project->getStatus() === 'D'): ?>
                        dokončený
                    <?php elseif ($project->getStatus() === 'R'): ?>
                        odstránený
                    <?php endif ?>
                </td>
                <td><?= $project->getDeadline() ?></td>
                <td><?= $project->getSubmission() ?></td>
                <td><a href="<?= $link->url('project.edit', ['project' => $project->getId()]) ?>">Upraviť</a></td>
                <td><a href="<?= $link->url('project.delete', ['project' => $project->getId()]) ?>">Zmazať</a></td>
                <td><a href="<?= $link->url('task.index', ['project' => $project->getId()]) ?>">Prehliadať úlohy</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<a href="<?= $link->url('project.add') ?>">Pridať nový projekt</a>