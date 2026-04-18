<form method="post" action="<?= $link->url('task.save') ?>">
    <input name="id" type="hidden" value="<?= @$taskInstance?->getId() ?>">
    <label>Názov úlohy</label>
    <input name="name" type="text" value="<?= @$taskInstance?->getName() ?>">
    <label>Popis úlohy</label>
    <textarea name="description" type="text"><?= @$taskInstance?->getDescription() ?></textarea>
    <label>Status úlohy</label>
    <select name="status">
        <option value="C" <?= @$taskInstance?->getStatus() === 'C' ? 'selected' : '' ?>>vytvorený</option>
        <option value="P" <?= @$taskInstance?->getStatus() === 'P' ? 'selected' : '' ?>>pracuje sa na ňom</option>
        <option value="D" <?= @$taskInstance?->getStatus() === 'D' ? 'selected' : '' ?>>dokončený</option>
        <option value="R" <?= @$taskInstance?->getStatus() === 'R' ? 'selected' : '' ?>>odstránený</option>
    </select>
    <label>Priorita úlohy</label>
    <input name="priority" type="number" value="<?= @$taskInstance?->getPriority() ?>" min="0" max="5">
    <label>Termín odovzdania</label>
    <input name="deadline" type="date" value="<?= @$taskInstance?->getDeadline() ?>">
    <label>Odovzdanie úlohy</label>
    <input name="submission" type="date" value="<?= @$taskInstance?->getSubmission() ?>">
    <button>Uložiť</button>
    <a href="?c=task&a=index">Späť</a>
</form>