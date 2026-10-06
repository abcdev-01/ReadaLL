<?php /** @var array<string,string> $errors @var array<string,string> $old */ ?>

<form method="post" action="/index.php?route=members/create">
    <label for="name">Name</label>
    <input id="name" name="name"
           value="<?= htmlspecialchars($old['name'], ENT_QUOTES) ?>">
    <?php if (isset($errors['name'])): ?>
        <div class="error"><?= htmlspecialchars($errors['name'], ENT_QUOTES) ?></div>
    <?php endif; ?>

    <label for="email">Email</label>
    <input id="email" name="email" type="email"
           value="<?= htmlspecialchars($old['email'], ENT_QUOTES) ?>">
    <?php if (isset($errors['email'])): ?>
        <div class="error"><?= htmlspecialchars($errors['email'], ENT_QUOTES) ?></div>
    <?php endif; ?>

    <label for="phone">Phone (optional)</label>
    <input id="phone" name="phone"
           value="<?= htmlspecialchars($old['phone'], ENT_QUOTES) ?>">
    <?php if (isset($errors['phone'])): ?>
        <div class="error"><?= htmlspecialchars($errors['phone'], ENT_QUOTES) ?></div>
    <?php endif; ?>

    <button type="submit">Save member</button>
</form>