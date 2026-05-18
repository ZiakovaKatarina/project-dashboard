<?php

/** @var Framework\Support\LinkGenerator $link */
/** @var int $projectId */

?>

<a href="<?= $link->url('task.index', ['project' => $projectId]) ?>">Späť</a>
<h1>Úprava úlohy</h1>
<?php require 'form.view.php' ?>