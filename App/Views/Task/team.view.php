<?php

/** @var int $project */
/** @var App\Models\UserInTask[] $membership */
/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\User $user */

?>

<a href="<?= $link->url('task.index', ['project' => $project]) ?>">Späť</a>
<h1>Zoznam členov úlohy</h1>
<?php if (count($membership) == 0): ?>
    <div>K tejto úlohe nie sú zatiaľ priradení žiadni členovia.</div>
<?php else: ?>
    <table>
        <tr>
            <th>Meno a priezvisko</th>
            <th>Email</th>
            <th>Stav</th>
            <th>Akcie</th>
        </tr>
        <?php for ($x = 0; $x < count($membership); $x++): ?>
            <?php $m = $membership[$x]; ?>
            <?php $mUser = $m->getUser(); ?>
            <tr>
                <td><?= htmlspecialchars($mUser->getName()) ?></td>
                <td><?= htmlspecialchars($mUser->getEmail()) ?></td>
                <td>
                    <span id="saved_state_<?= $mUser->getId() ?>"><?= $m->getState() ?></span>
                    <?php if ($user->getId() == $mUser->getId()): ?>
                        <form id="form_change_user_state_<?= $mUser->getId() ?>" style="display:none;" method="POST" action="<?= $link->url('task.edit_user', ['project' => $project, 'task' => $m->getTaskId(), 'userId' => $mUser->getId()]) ?>">
                            <input type="hidden" name="return_to" value="team">
                            <input type="number" min="0" max="100" step="0.01" name="state" required>
                            <button type="submit">Uložiť</button>
                            <button type="button" onclick="cancel_editing_user_in_task(<?= $mUser->getId() ?>)">Zrušiť</button>
                        </form>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($mUser->getId() == $user->getId()): ?>
                        <button onclick="edit_user_in_task(<?= $mUser->getId() ?>)">Upraviť</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endfor; ?>
    </table>
<?php endif; ?>

<script src="js/task_form.js"></script>