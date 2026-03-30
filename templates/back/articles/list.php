<?php
/**
 * Vue Liste des articles
 * Affiche tous les articles avec pagination et filtres
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestion des articles</h1>
    <a href="/admin/articles/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvel article
    </a>
</div>

<!-- Filtres et recherche -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/admin/articles" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Rechercher un article..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="published" <?= ($_GET['status'] ?? '') == 'published' ? 'selected' : '' ?>>Publiés</option>
                    <option value="draft" <?= ($_GET['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Brouillons</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="category">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories ?? [] as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i> Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th width="50">ID</th>
                <th>Image</th>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Auteur</th>
                <th>Statut</th>
                <th>Vues</th>
                <th>Créé le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($articles)): ?>
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        Aucun article trouvé
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?= $article['id'] ?></td>
                    <td>
                        <?php 
                        $firstImage = $article['images'][0] ?? null;
                        if ($firstImage): 
                        ?>
                            <img src="/storage/uploads/<?= htmlspecialchars($firstImage['file_path']) ?>" 
                                 alt="<?= htmlspecialchars($firstImage['alt_text']) ?>"
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        <?php else: ?>
                            <div class="bg-secondary text-white text-center" 
                                 style="width: 50px; height: 50px; line-height: 50px; border-radius: 4px;">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($article['title']) ?></strong>
                        <br>
                        <small class="text-muted">Slug: <?= htmlspecialchars($article['slug']) ?></small>
                    </td>
                    <td>
                        <span class="badge bg-info">
                            <?= htmlspecialchars($article['category_name'] ?? 'Non catégorisé') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($article['author_name']) ?></td>
                    <td>
                        <?php if ($article['is_published']): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Publié
                            </span>
                            <br>
                            <small class="text-muted">
                                le <?= date('d/m/Y H:i', strtotime($article['published_at'])) ?>
                            </small>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-pencil-alt"></i> Brouillon
                            </span>
                        <?php endif; ?>
                    </td>
                    <td><?= $article['views_count'] ?? 0 ?></td>
                    <td>
                        <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                        <br>
                        <small class="text-muted">
                            modifié: <?= date('d/m/Y', strtotime($article['updated_at'])) ?>
                        </small>
                    </td>
                    <td class="text-nowrap">
                        <div class="btn-group" role="group">
                            <?php if ($article['is_published']): ?>
                                <a href="/article/<?= $article['slug'] ?>" target="_blank" 
                                   class="btn btn-sm btn-info" title="Voir sur le site">
                                    <i class="fas fa-eye"></i>
                                </a>
                            <?php endif; ?>
                            
                            <a href="/admin/articles/<?= $article['id'] ?>/edit" 
                               class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <button type="button" class="btn btn-sm btn-<?= $article['is_published'] ? 'secondary' : 'success' ?>" 
                                    onclick="togglePublish(<?= $article['id'] ?>, <?= $article['is_published'] ? 'false' : 'true' ?>)"
                                    title="<?= $article['is_published'] ? 'Dépublier' : 'Publier' ?>">
                                <i class="fas fa-<?= $article['is_published'] ? 'eye-slash' : 'check' ?>"></i>
                            </button>
                            
                            <button type="button" class="btn btn-sm btn-danger" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal<?= $article['id'] ?>"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        <!-- Modal de confirmation de suppression -->
                        <div class="modal fade" id="deleteModal<?= $article['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Confirmer la suppression</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Êtes-vous sûr de vouloir supprimer l'article :</p>
                                        <p class="fw-bold">"<?= htmlspecialchars($article['title']) ?>" ?</p>
                                        <p class="text-danger">Cette action est irréversible !</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <form method="POST" action="/admin/articles/<?= $article['id'] ?>/delete">
                                            <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&category=<?= urlencode($_GET['category'] ?? '') ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&category=<?= urlencode($_GET['category'] ?? '') ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
        
        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&category=<?= urlencode($_GET['category'] ?? '') ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<script>
function togglePublish(articleId, publish) {
    if (!confirm('Voulez-vous ' + (publish ? 'publier' : 'dépublier') + ' cet article ?')) {
        return;
    }
    
    fetch('/admin/articles/' + articleId + '/toggle-publish', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        alert('Erreur lors de la modification du statut');
    });
}
</script>