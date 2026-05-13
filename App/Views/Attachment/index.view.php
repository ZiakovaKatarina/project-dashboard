<?php

/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\Attachment[] $attachments */
/** @var int $taskId */
/** @var int $projectId */
/** @var string[] $errors */

?>

<?php if (count($errors) > 0): ?>
    <?php foreach ($errors as $error): ?>
        <p><?= $error ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<a href="<?= $link->url('task.index', ['project' => $projectId]) ?>">Späť</a>
<h1>Zoznam príloh</h1>
<?php if (count($attachments) == 0): ?>
    <div>K danej úlohe nie sú zatiaľ priradené žiadne prílohy.</div>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Filename</th>
            <th>Akcie</th>
        </tr>
        <?php foreach ($attachments as $attachment): ?>
            <tr>
                <td><?= $attachment->getId() ?></td>
                <td><?= $attachment->getFilename() ?></td>
                <td><a href="<?= $link->asset($attachment->getPath() . $attachment->getFilename()) ?>" download>Stiahnuť</a></td>
                <td><a href="<?= $link->url('attachment.delete', ['attachment' => $attachment->getId(), 'task' => $taskId, 'project' => $projectId]) ?>">Zmazať</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<button id="show_form_for_new_attachment">Pridať novú prílohu</button>
<form id="form_new_attachment" method="POST" action="<?= $link->url('attachment.save', ['task' => $taskId, 'project' => $projectId]) ?>" enctype="multipart/form-data" style="display:none;">
    <input type="file" name="input_new_attachment" id="input_new_attachment">
    <button type="button" onclick="cancel_adding_attachments()">Zrušiť</button>
    <button type="submit">Uložiť</button>
</form>

<script src="js/attachments.js"></script>