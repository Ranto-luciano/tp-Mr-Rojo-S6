<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Catégories</h1>
    <a href="/admin/categories/create" class="btn btn-primary">
        Nouvelle catégorie
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Slug</th>
                <th>Articles</th>
                <th>Créée le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= htmlspecialchars($category['name']) ?></td>
                <td><?= htmlspecialchars($category['slug']) ?></td>
                <td><?= $category['articles_count'] ?? 0 ?></td>
                <td><?= date('d/m/Y', strtotime($category['created_at'])) ?></td>
                <td class="actions-cell">
                    <div class="actions-group" role="group" aria-label="Actions categorie">
                        <a href="/admin/categories/<?= $category['id'] ?>/edit" class="icon-action edit" title="Modifier" aria-label="Modifier">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20l4.5-1 9-9-3.5-3.5-9 9L4 20z"></path><path d="M13.5 6.5L17 10"></path></svg>
                        </a>
                        <?php if (($category['articles_count'] ?? 0) == 0): ?>
                            <form method="POST" action="/admin/categories/<?= $category['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cette categorie ?');">
                                <button type="submit" class="icon-action delete" title="Supprimer" aria-label="Supprimer">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path></svg>
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="icon-action unpublish" disabled title="Categorie protegee" aria-label="Categorie protegee">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
                            </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>