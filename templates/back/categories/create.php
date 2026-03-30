<?php
/**
 * Vue Création de catégorie
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Créer une catégorie</h1>
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
                <form method="POST" action="/admin/categories">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nom de la catégorie *</label>
                        <input type="text" class="form-control form-control-lg <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" 
                               required autofocus>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= $errors['name'] ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Exemple: Politique, Économie, Culture...</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (URL)</label>
                        <div class="input-group">
                            <span class="input-group-text">/category/</span>
                            <input type="text" class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                                   id="slug" name="slug" value="<?= htmlspecialchars($old['slug'] ?? '') ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="generateSlug()">
                                <i class="fas fa-sync"></i> Générer
                            </button>
                        </div>
                        <small class="text-muted">Laissez vide pour génération automatique. Utilisez uniquement des lettres minuscules, chiffres et tirets.</small>
                        <?php if (isset($errors['slug'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['slug'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Aperçu :</strong>
                        <div class="mt-2">
                            <div>URL : <code id="url_preview"><?= $_SERVER['HTTP_HOST'] ?>/category/</code></div>
                            <div>Slug généré : <code id="slug_preview"><?= htmlspecialchars($old['slug'] ?? '...') ?></code></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer la catégorie
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
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Conseils SEO</h5>
            </div>
            <div class="card-body">
                <h6>Pourquoi bien nommer ses catégories ?</h6>
                <ul>
                    <li>Les catégories aident Google à comprendre la structure de votre site</li>
                    <li>Elles apparaissent dans le fil d'Ariane (breadcrumb)</li>
                    <li>Améliorent la navigation des utilisateurs</li>
                </ul>
                
                <h6>Bonnes pratiques :</h6>
                <ul>
                    <li>Utilisez des noms courts et descriptifs</li>
                    <li>Évitez les catégories trop spécifiques (regroupez si nécessaire)</li>
                    <li>Limitez-vous à 10-15 catégories maximum</li>
                    <li>Le slug doit être en minuscules sans accents</li>
                </ul>
                
                <div class="alert alert-warning small">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Attention :</strong> La suppression d'une catégorie n'est possible que si elle ne contient aucun article.
                </div>
            </div>
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
    const preview = document.getElementById('slug_preview');
    const urlPreview = document.getElementById('url_preview');
    if (preview && urlPreview) {
        preview.textContent = slug || '...';
        urlPreview.innerHTML = window.location.host + '/category/' + (slug || '');
    }
}

// Génération auto du slug depuis le nom
document.getElementById('name').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value.trim()) {
        generateSlug();
    }
});

// Mise à jour de l'aperçu
const slugInput = document.getElementById('slug');
if (slugInput) {
    slugInput.addEventListener('input', function() {
        updatePreview(this.value);
    });
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    const existingSlug = '<?= htmlspecialchars($old['slug'] ?? '') ?>';
    if (existingSlug) {
        updatePreview(existingSlug);
    }
});
</script>