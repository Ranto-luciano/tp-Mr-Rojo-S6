<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Catégories</h1>
    <a href="/admin/categories/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle catégorie
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
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
                <td><?= $category['id'] ?></td>
                <td><?= htmlspecialchars($category['name']) ?></td>
                <td><?= htmlspecialchars($category['slug']) ?></td>
                <td><?= $category['articles_count'] ?? 0 ?></td>
                <td><?= date('d/m/Y', strtotime($category['created_at'])) ?></td>
                <td>
                    <a href="/admin/categories/<?= $category['id'] ?>/edit" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <?php if (($category['articles_count'] ?? 0) == 0): ?>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $category['id'] ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                        
                        <div class="modal fade" id="deleteModal<?= $category['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirmer la suppression</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Êtes-vous sûr de vouloir supprimer la catégorie "<?= htmlspecialchars($category['name']) ?>" ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <form method="POST" action="/admin/categories/<?= $category['id'] ?>/delete">
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-sm btn-secondary" disabled title="Cette catégorie contient des articles">
                            <i class="fas fa-lock"></i>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>