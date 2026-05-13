<?php

/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\Project $projectInstance */
/** @var App\Models\UserInProject[] $members */
/** @var string[] $errors */

?>

<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <p><?= $error ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<form method="POST" action="<?= $link->url('project.save') ?>">
    <input name="project" type="hidden" value="<?= @$projectInstance?->getId() ?>">
    <label>Názov projektu</label>
    <input name="name" type="text" value="<?= @$projectInstance?->getName() ?>" maxlength="100" minlength="3" required>
    <label>Popis projektu</label>
    <textarea name="description" maxlength="500"><?= @$projectInstance?->getDescription() ?></textarea>
    <label>Status projektu</label>
    <select name="status" value="<?= @$projectInstance?->getStatus() ?>">
        <option value="C" <?= @$projectInstance?->getStatus() === 'C' ? 'selected' : '' ?>>vytvorený</option>
        <option value="P" <?= @$projectInstance?->getStatus() === 'P' ? 'selected' : '' ?>>pracuje sa na ňom</option>
        <option value="D" <?= @$projectInstance?->getStatus() === 'D' ? 'selected' : '' ?>>dokončený</option>
        <option value="R" <?= @$projectInstance?->getStatus() === 'R' ? 'selected' : '' ?>>odstránený</option>
    </select>
    <label>Termín odovzdania</label>
    <input name="deadline" type="date" value="<?= @$projectInstance?->getDeadline() ?>">
    <label>Odovzdanie projektu</label>
    <input name="submission" type="date" value="<?= @$projectInstance?->getSubmission() ?>">
    <button type="submit">Uložiť</button>
    <a href="?c=project&a=index">Späť</a>
</form>

<?php if (@$projectInstance?->getId() > 0): ?>
    <h2>Členovia tímu</h2>
    <?php if (count($members) === 0): ?>
        <p>Tomuto projektu ešte neboli priradení žiadni používatelia.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Meno</th>
                <th>Email</th>
                <th>Rola</th>
                <th>Akcie</th>
            </tr>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td><?= $member->getUser()->getFirstName() . " " . $member->getUser()->getLastName() ?></td>
                    <td><?= $member->getUser()->getEmail() ?></td>
                    <td>
                        <span id="saved_right_<?= $member->getUser()->getId() ?>"><?= $member->getRights() ?></span>
                        <form id="form_change_user_rights_<?= $member->getUser()->getId() ?>" method="POST" style="display:none;"
                              action="<?= $link->url('project.edit_user', ['project' => $projectInstance->getId(), 'userId' => $member->getUser()->getId()]) ?>">
                            <select name="rights">
                                <option value="W">Writer</option>
                                <option value="R">Reader</option>
                                <option value="A">Admin</option>
                            </select>
                            <button type="button" onclick="cancel_editing_user_in_project(<?= $member->getUser()->getId() ?>)">Zrušiť</button>
                            <button type="submit">Uložiť</button>
                        </form>
                    </td>
                    <td><button onclick="edit_user_in_project(<?= $member->getUser()->getId() ?>)">Upraviť</button></td>
                    <td><a href="<?= $link->url('project.remove_user', ['project' => $projectInstance->getId(), 'userId' => $member->getUser()->getId()]) ?>">Odstrániť</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <label>Hľadať nového člena projektu podľa emailu</label>
    <form action="<?= $link->url('project.add_member', ['project' => $projectInstance->getId()]) ?>" method="POST">
        <input type="email" name="email" required maxlength="250">
        <select name="rights">
            <option value="W">Writer</option>
            <option value="R">Reader</option>
            <option value="A">Admin</option>
        </select>
        <button type="submit">Pridať</button>
    </form>
<?php endif; ?>

<script src="js/project_form.js"></script>