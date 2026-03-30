<?php
/**
 * Vue Édition de catégorie
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Modifier la catégorie</h1>
    <a href="/admin/categories" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informations de la catégorie</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/categories/<?= $category['id'] ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nom de la catégorie *</label>
                        <input type="text" class="form-control form-control-lg <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= $errors['name'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (URL)</label>
                        <div class="input-group">
                            <span class="input-group-text">/category/</span>
                            <input type="text" class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                                   id="slug" name="slug" value="<?= htmlspecialchars($category['slug']) ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="generateSlug()">
                                <i class="fas fa-sync"></i> Générer
                            </button>
                        </div>
                        <small class="text-muted">Modifiez le slug avec précaution - cela changera l'URL de toutes les catégories.</small>
                        <?php if (isset($errors['slug'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['slug'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Aperçu :</strong>
                        <div class="mt-2">
                            <div>URL : <code id="url_preview"><?= $_SERVER['HTTP_HOST'] ?>/category/<?= $category['slug'] ?></code></div>
                            <div>Nombre d'articles : <strong><?= $category['articles_count'] ?? 0 ?></strong></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                        <a href="/admin/categories" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Statistiques -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <div class="display-6"><?= $category['articles_count'] ?? 0 ?></div>
                            <div class="text-muted">Articles dans cette catégorie</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <div class="display-6"><?= date('d/m/Y', strtotime($category['created_at'])) ?></div>
                            <div class="text-muted">Date de création</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Derniers articles -->
        <?php if (isset($category['recent_articles']) && !empty($category['recent_articles'])): ?>
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Derniers articles dans cette catégorie</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($category['recent_articles'] as $article): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="/admin/articles/<?= $article['id'] ?>/edit">
                            <?= htmlspecialchars($article['title']) ?>
                        </a>
                        <span class="badge bg-<?= $article['is_published'] ? 'success' : 'warning' ?>">
                            <?= $article['is_published'] ? 'Publié' : 'Brouillon' ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Avertissement sur les slugs -->
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Attention :</strong> La modification du slug changera l'URL de cette catégorie. 
            Les anciens liens continueront de fonctionner si vous avez configuré des redirections.
        </div>
    </div>
</div>

<script>
function generateSlug() {
    const name = document.getElementById('name').value;
    if (name) {
        const slug = name.toLowerCase()
            .replace(/[àáâãä]/g, 'a')
            .replace(/[èéêë]/g, 'e')
            .replace(/[ìíîï]/g, 'i')
            .replace(/[òóôõö]/g, 'o')
            .replace(/[ùúûü]/g, 'u')
            .replace(/[ýÿ]/g, 'y')
            .replace(/[ç]/g, 'c')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
        updatePreview(slug);
    }
}

function updatePreview(slug) {
    const urlPreview = document.getElementById('url_preview');
    if (urlPreview) {
        urlPreview.innerHTML = window.location.host + '/category/' + (slug || '');
    }
}

// Mise à jour de l'aperçu
const slugInput = document.getElementById('slug');
if (slugInput) {
    slugInput.addEventListener('input', function() {
        updatePreview(this.value);
    });
}
</script>