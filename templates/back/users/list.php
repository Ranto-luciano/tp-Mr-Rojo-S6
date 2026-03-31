<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Utilisateurs</h1>
    <a href="/admin/users/create" class="btn btn-primary">Nouvel utilisateur</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Articles</th>
                        <th>Cree le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aucun utilisateur trouve.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="badge"><?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td><?= (int)($user['articles_count'] ?? 0) ?></td>
                                <td><?= date('d/m/Y', strtotime((string)$user['created_at'])) ?></td>
                                <td class="text-nowrap actions-cell">
                                    <div class="actions-group" role="group" aria-label="Actions utilisateur">
                                        <a href="/admin/users/<?= (int)$user['id'] ?>/edit" class="icon-action edit" title="Modifier" aria-label="Modifier">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20l4.5-1 9-9-3.5-3.5-9 9L4 20z"></path><path d="M13.5 6.5L17 10"></path></svg>
                                        </a>
                                        <?php if ((int)$user['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                                            <form method="POST" action="/admin/users/<?= (int)$user['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                                <button type="submit" class="icon-action delete" title="Supprimer" aria-label="Supprimer">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path></svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
