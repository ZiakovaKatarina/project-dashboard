<?php

/** @var string[] $errors; */
/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\User $register_user */

?>

<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <p><?= $error ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<form name="register-form" method="POST" action="<?= $link->url('auth.update') ?>">
    <label>First name</label>
    <input type="text" name="first_name" maxlength="100" minlength="3" value="<?= @$register_user?->getFirstName() ?>" required>
    <label>Last name</label>
    <input type="text" name="last_name" maxlength="100" minlength="3" value="<?= @$register_user?->getLastName() ?>" required>
    <label>Email</label>
    <input type="email" name="email" maxlength="250" value="<?= @$register_user?->getEmail() ?>" required>
    <label>Password</label>
    <input type="password" maxlength="250" minlength="5" name="password">
    <label>Repeat password</label>
    <input type="password" maxlength="250" minlength="5" name="repeated_password">
    <button type="submit">Aktualizovať profil</button>
</form>