<?php

/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\Comment[] $comments */
/** @var string[] $usernames */
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
            <th>Používateľ</th>
            <th>Obsah</th>
            <th>Dátum a čas vytvorenia</th>
            <?php if ($role): ?>
                <th>Akcie</th>
            <?php endif; ?>
        </tr>
        <tbody id='comments-list'>
            <?php for ($x = 0; $x < count($comments); $x++): ?>
                <?php $comment = $comments[$x]; ?>
                <?php $username = $usernames[$x]; ?>
                <tr id="comment-row-<?= $comment->getId() ?>">
                    <td><?= htmlspecialchars($username) ?></td>
                    <td id="comment-content-<?= $comment->getId() ?>"><?= htmlspecialchars($comment->getContent() ?? '') ?></td>
                    <td><?= $comment->getCreation() ?></td>
                    <td>
                        <?php if ($role): ?>
                            <span id="edit_buttons-<?= $comment->getId() ?>">
                                <button id="edit_button-<?= $comment->getId() ?>" onclick="edit_comment(<?= $comment->getId() ?>, <?= $taskId ?>, <?= $projectId ?>)">Upraviť</button>
                            </span>
                            <button onclick="delete_comment(<?= $comment->getId() ?>, <?= $taskId ?>, <?= $projectId ?>)">Zmazať</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($role): ?>
    <button id="show_form_for_new_comment">Pridať nový komentár</button>
    <form id="form_new_comment" class="one-row-comment" style="display:none;">
        <textarea id="new_comment_content" placeholder="Napíš komentár..." maxlength="500" minlength="1" required></textarea>
        <button type="button" onclick="cancel_adding_comment()">Zrušiť</button>
        <button type="button" id="add_new_comment" onclick="add_comment(<?= $taskId ?>, <?= $projectId ?>)">Odoslať</button>
    </form>
<?php endif; ?>
<script src="js/comments.js"></script>