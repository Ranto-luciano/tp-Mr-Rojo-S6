<?php
/**
 * Vue Liste des articles
 * Affiche tous les articles avec pagination et filtres
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestion des articles</h1>
    <a href="/admin/articles/create" class="btn btn-primary">
        Nouvel article
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
                    Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
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
                    <td colspan="8" class="text-center text-muted py-4">
                        Aucun article trouvé
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                <tr>
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
                                IMG
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
                                Publié
                            </span>
                            <br>
                            <small class="text-muted">
                                le <?= date('d/m/Y H:i', strtotime($article['published_at'])) ?>
                            </small>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">
                                Brouillon
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
                    <td class="text-nowrap actions-cell">
                        <div class="actions-group" role="group" aria-label="Actions article">
                            <?php if ($article['is_published']): ?>
                                <a href="/actualites/article-<?= rawurlencode((string)$article['slug']) ?>.html" target="_blank" 
                                   class="icon-action view" title="Voir sur le site" aria-label="Voir">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </a>
                            <?php endif; ?>
                            
                            <a href="/admin/articles/<?= $article['id'] ?>/edit" 
                               class="icon-action edit" title="Modifier" aria-label="Modifier">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20l4.5-1 9-9-3.5-3.5-9 9L4 20z"></path><path d="M13.5 6.5L17 10"></path></svg>
                            </a>
                            
                            <button type="button" class="icon-action <?= $article['is_published'] ? 'unpublish' : 'publish' ?>" 
                                    onclick="togglePublish(<?= $article['id'] ?>, <?= $article['is_published'] ? 'false' : 'true' ?>)"
                                    title="<?= $article['is_published'] ? 'Depublier' : 'Publier' ?>"
                                    aria-label="<?= $article['is_published'] ? 'Depublier' : 'Publier' ?>">
                                <?php if ($article['is_published']): ?>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18"></path><path d="M10.6 10.6A3 3 0 0 0 12 15a3 3 0 0 0 2.4-4.4"></path><path d="M6.7 6.7A18 18 0 0 0 2 12s3.5 6 10 6a10.8 10.8 0 0 0 5.3-1.3"></path><path d="M9.9 4.3A11.4 11.4 0 0 1 12 4c6.5 0 10 8 10 8a18 18 0 0 1-3 3.9"></path></svg>
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <?php endif; ?>
                            </button>
                            
                            <form method="POST" action="/admin/articles/<?= $article['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cet article ?');">
                                <button type="submit" class="icon-action delete" title="Supprimer" aria-label="Supprimer">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path></svg>
                                </button>
                            </form>
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
                Precedent
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
                Suivant
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