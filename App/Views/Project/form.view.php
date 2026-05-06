<?php

/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\Project $projectInstance */

?>

<form method="post" action="<?= $link->url('project.save') ?>">
    <input name="project" type="hidden" value="<?= @$projectInstance?->getId() ?>">
    <label>Názov projektu</label>
    <input name="name" type="text" value="<?= @$projectInstance?->getName() ?>">
    <label>Popis projektu</label>
    <textarea name="description"><?= @$projectInstance?->getDescription() ?></textarea>
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
    <button>Uložiť</button>
    <a href="?c=project&a=index">Späť</a>
</form>