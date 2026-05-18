<?php

/** @var int $project */
/** @var App\Models\UserInProject[] $membership */
/** @var Framework\Support\LinkGenerator $link */

?>

<a href="<?= $link->url('project.index') ?>">Späť</a>
<h1>Zoznam členov tímu</h1>
<?php if (count($membership) == 0): ?>
    <div>K tomuto projektu nie sú zatiaľ priradení žiadni členovia.</div>
<?php else: ?>
    <table>
        <tr>
            <th>Meno a priezvisko</th>
            <th>Email</th>
            <th>Rola</th>
        </tr>
        <?php for ($x = 0; $x < count($membership); $x++): ?>
            <?php $m = $membership[$x]; ?>
            <?php $user = $m->getUser(); ?>
            <tr>
                <td><?= htmlspecialchars($user->getName()) ?></td>
                <td><?= htmlspecialchars($user->getEmail()) ?></td>
                <td>
                    <?php switch (htmlspecialchars($m->getRights())): 
                        case 'A': ?>
                            Team Leader
                            <?php break;
                        case 'W': ?>
                            Writer
                            <?php break;
                        case 'R': ?>
                            Reader
                            <?php break;
                        default: ?>
                            Unknown
                            <?php break;
                    endswitch; ?>
                </td>
            </tr>
        <?php endfor; ?>
    </table>
<?php endif; ?>