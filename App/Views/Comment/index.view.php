<?php

/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\Comment[] $comments */
/** @var int $projectId */
/** @var int $taskId */
/** @var string $role */

?>

<a href="<?= $link->url('task.index', ['project' => $projectId]) ?>">Späť</a>
<h1>Komentáre</h1>
<?php if (count($comments) == 0): ?>
    <div>K danej úlohe nie sú zatiaľ vytvorené žiadne komentáre.</div>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>ID používateľa</th>
            <th>ID úlohy</th>
            <th>Obsah</th>
            <th>Dátum a čas vytvorenia</th>
            <?php if ($role): ?>
                <th>Akcie</th>
            <?php endif; ?>
        </tr>
        <tbody id='comments-list'>
            <?php foreach ($comments as $comment): ?>
                <tr id="comment-row-<?= $comment->getId() ?>">
                    <td><?= $comment->getId() ?></td>
                    <td><?= $comment->getUserId() ?></td>
                    <td><?= $comment->getTaskId() ?></td>
                    <td id="comment-content-<?= $comment->getId() ?>"><?= htmlspecialchars($comment->getContent() ?? '') ?></td>
                    <td><?= $comment->getCreation() ?></td>
                    <?php if ($role): ?>
                        <td id="edit_buttons-<?= $comment->getId() ?>"><button onclick="edit_comment(<?= $comment->getId() ?>)">Upraviť</button></td>
                        <td><button onclick="delete_comment(<?= $comment->getId() ?>)">Zmazať</button></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($role): ?>
    <button id="show_form_for_new_comment">Pridať nový komentár</button>
    <form id="form_new_comment" style="display:none;">
        <textarea id="new_comment_content" placeholder="Napíš komentár..." maxlength="500" minlength="1" required></textarea>
        <button type="button" onclick="cancel_adding_comment()">Zrušiť</button>
        <button type="button" id="add_new_comment" onclick="add_comment(<?= $taskId ?>)">Odoslať</button>
    </form>

    <script src="js/comments.js"></script>
<?php endif; ?>