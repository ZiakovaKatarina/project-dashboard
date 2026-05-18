<?php

/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\Task $taskInstance */
/** @var int $projectId */
/** @var App\Models\UserInTask[] $members */
/** @var string[] $errors */
/** @var string $role */

?>

<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <p><?= $error ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<form method="post" action="<?= $link->url('task.save', ['project' => $projectId]) ?>">
    <input name="task" type="hidden" value="<?= @$taskInstance?->getId() ?>">
    <input name="project" type="hidden" value="<?= $projectId ?>">
    <label>Názov úlohy</label>
    <input name="name" type="text" required maxlength="100" minlength="3" value="<?= htmlspecialchars(@$taskInstance?->getName() ?? '') ?>">
    <label>Popis úlohy</label>
    <textarea name="description" required maxlength="500" type="text"><?= htmlspecialchars(@$taskInstance?->getDescription() ?? '') ?></textarea>
    <label>Status úlohy</label>
    <select name="status">
        <option value="C" <?= @$taskInstance?->getStatus() === 'C' ? 'selected' : '' ?>>vytvorený</option>
        <option value="P" <?= @$taskInstance?->getStatus() === 'P' ? 'selected' : '' ?>>pracuje sa na ňom</option>
        <option value="D" <?= @$taskInstance?->getStatus() === 'D' ? 'selected' : '' ?>>dokončený</option>
        <option value="R" <?= @$taskInstance?->getStatus() === 'R' ? 'selected' : '' ?>>odstránený</option>
    </select>
    <label>Priorita úlohy</label>
    <input name="priority" type="number" value="<?= @$taskInstance?->getPriority() ?>" min="1" max="10" required>
    <label>Termín odovzdania</label>
    <input name="deadline" type="date" value="<?= @$taskInstance?->getDeadline() ?>">
    <label>Odovzdanie úlohy</label>
    <input name="submission" type="date" value="<?= @$taskInstance?->getSubmission() ?>">
    <button>Uložiť</button>
</form>

<?php if (@$taskInstance?->getId() > 0): ?>
    <hr>
    <h2>Osoby pracujúce na tejto úlohe</h2>
    <?php if (count($members) === 0): ?>
        <p>Tejto úlohe ešte neboli priradení žiadni používatelia.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Meno</th>
                <th>Email</th>
                <th>Status</th>
                <?php if ($role === 'A'): ?>
                    <th>Akcie</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td><?= htmlspecialchars($member->getUser()->getName() ?? '') ?></td>
                    <td><?= htmlspecialchars($member->getUser()->getEmail() ?? '') ?></td>
                    <td>
                        <span id="saved_state_<?= $member->getUser()->getId() ?>"><?= $member->getState() ?></span>
                        <?php if ($role === 'A'): ?>
                            <form id="form_change_user_state_<?= $member->getUser()->getId() ?>" method="POST" style="display:none;"
                                action="<?= $link->url('task.edit_user', ['project' => $projectId, 'task' => $taskInstance->getId(), 'userId' => $member->getUser()->getId()]) ?>">
                                <input type="number" name="state" step="0.01" min="0" max="100" required>
                                <button type="button" onclick="cancel_editing_user_in_task(<?= $member->getUser()->getId() ?>">Zrušiť</button>
                                <button type="submit">Uložiť</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($role === 'A'): ?>
                            <button onclick="edit_user_in_task(<?= $member->getUser()->getId() ?>)">Upraviť</button>
                            <a href="<?= $link->url('task.remove_user', ['project' => $projectId, 'task' => $taskInstance->getId(), 'userId' => $member->getUser()->getId()]) ?>">Odstrániť</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if ($role === 'A'): ?>
        <p>Hľadať nového pracovníka na úlohe podľa emailu... Tento pracovník musí byť súčasťou projektu.</p>
        <form action="<?= $link->url('task.add_member', ['project' => $projectId, 'task' => $taskInstance->getId()]) ?>" method="POST">
            <label>Osoba v projekte:</label>
            <input type="email" name="email" required maxlength="250">
            <button type="submit">Pridať</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

<script src="js/task_form.js"></script>