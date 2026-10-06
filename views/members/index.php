<?php /** @var \ReaDaLL\Model\Member[] $members */ ?>

<?php if (empty($members)): ?>
    <p>No members yet.
        <a href="/index.php?route=members/create">Add the first one</a>.
    </p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>S/N</th><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($members as $m): ?>
            <tr>
                <td><?= htmlspecialchars((string) $m->id(), ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($m->name(), ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($m->email(), ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars((string) $m->phone(), ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars((string) $m->joinedAt(), ENT_QUOTES) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>