<?php

/** @var string[] $errors; */
/** @var Framework\Support\LinkGenerator $link */
/** @var App\Models\User $register_user */

?>

<?php if (count($errors) > 0): ?>
    <?php foreach ($errors as $error): ?>
        <p><?= $error ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<form name="register-form" method="POST" action="<?= $link->url('auth.register') ?>">
    <label>First name</label>
    <input type="text" name="first_name" maxlength="100" minlength="3" value="<?= @$register_user?->getFirstName() ?>" required>
    <label>Last name</label>
    <input type="text" name="last_name" maxlength="100" minlength="3" value="<?= @$register_user?->getLastName() ?>" required>
    <label>Email</label>
    <input type="email" name="email" maxlength="250" value="<?= @$register_user?->getEmail() ?>" required>
    <label>Password</label>
    <input type="password" name="password" maxlength="250" minlength="5" required>
    <label>Repeat password</label>
    <input type="password" name="repeated_password" maxlength="250" minlength="5" required>
    <button type="submit">Register</button>
</form>
