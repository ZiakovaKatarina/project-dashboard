<?php

/** @var string $error; */
/** @var Framework\Support\LinkGenerator $link */

?>

<?php if ($error != ""): ?>
    <div><?= $error ?></div>
<?php endif; ?>

<form name="register-form" method="POST" action="<?= $link->url('auth.register') ?>">
    <label>First name</label>
    <input type="text" name="first_name" required>
    <label>Last name</label>
    <input type="text" name="last_name" required>
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <label>Repeat password</label>
    <input type="password" name="repeated_password" required>
    <button type="submit">Register</button>
</form>
