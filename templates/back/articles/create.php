<?php
/**
 * Vue Création d'article
 * Formulaire complet avec éditeur WYSIWYG et gestion des images
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Créer un article</h1>
    <a href="/admin/articles" class="btn btn-secondary">
        Retour à la liste
    </a>
</div>

<form method="POST" action="/admin/articles" enctype="multipart/form-data" id="articleForm">
    <div class="row">
        <div class="col-md-8">
            <!-- Informations principales -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informations principales</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Titre *</label>
                        <input type="text" class="form-control form-control-lg <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                               id="title" name="title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?= $errors['title'] ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Le titre doit être accrocheur et contenir le mot-clé principal</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (URL personnalisée)</label>
                        <div class="input-group">
                            <span class="input-group-text">/article/</span>
                            <input type="text" class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                                   id="slug" name="slug" value="<?= htmlspecialchars($old['slug'] ?? '') ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="generateSlugFromTitle()">
                                Générer
                            </button>
                        </div>
                        <small class="text-muted">Laissez vide pour génération automatique. Utilisez uniquement des lettres minuscules, chiffres et tirets.</small>
                        <?php if (isset($errors['slug'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['slug'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">Catégorie *</label>
                        <select class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>" 
                                id="category_id" name="category_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                    <?= (isset($old['category_id']) && $old['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?> (<?= $category['articles_count'] ?? 0 ?> articles)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['category_id'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Extrait / Résumé</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                                  placeholder="Un court résumé de l'article (apparaîtra dans les listes)"><?= htmlspecialchars($old['excerpt'] ?? '') ?></textarea>
                        <small class="text-muted">150-160 caractères recommandés pour le SEO</small>
                        <div id="excerpt-counter" class="text-muted small"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label fw-bold">Contenu *</label>
                        <textarea class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>" 
                                  id="content" name="content" rows="20" required><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
                        <?php if (isset($errors['content'])): ?>
                            <div class="invalid-feedback"><?= $errors['content'] ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Utilisez les balises HTML pour formater votre texte : &lt;p&gt;, &lt;h2&gt;, &lt;h3&gt;, &lt;strong&gt;, &lt;em&gt;</small>
                    </div>
                </div>
            </div>
            
            <!-- Gestion des images supplémentaires -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Galerie d'images</h5>
                </div>
                <div class="card-body">
                    <div id="image-gallery" class="row g-3">
                        <!-- Les images seront ajoutées dynamiquement -->
                    </div>
                    <button type="button" class="btn btn-outline-primary" onclick="addImageField()">
                        Ajouter une image
                    </button>
                    <small class="text-muted d-block mt-2">Formats acceptés : JPG, PNG, WebP (max 5MB par image)</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Options de publication -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Publication</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1"
                               <?= isset($old['is_published']) && $old['is_published'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold" for="is_published">Publier immédiatement</label>
                        <div class="small text-muted">Si non coché, l'article sera sauvegardé comme brouillon</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        Enregistrer l'article
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100" onclick="saveAsDraft()">
                        Sauvegarder comme brouillon
                    </button>
                </div>
            </div>
            
            <!-- SEO -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Optimisation SEO</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                               value="<?= htmlspecialchars($old['meta_title'] ?? '') ?>" 
                               maxlength="70" onkeyup="updateMetaPreview()">
                        <small class="text-muted">50-60 caractères recommandés. Actuellement: <span id="meta_title_length">0</span></small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" 
                                  rows="3" maxlength="160" onkeyup="updateMetaPreview()"><?= htmlspecialchars($old['meta_description'] ?? '') ?></textarea>
                        <small class="text-muted">150-160 caractères recommandés. Actuellement: <span id="meta_description_length">0</span></small>
                    </div>
                    
                    <div class="alert alert-info small">
                        <strong>Aperçu dans les moteurs de recherche :</strong>
                        <div id="meta_preview" class="mt-2">
                            <div class="fw-bold text-primary"><?= htmlspecialchars($old['meta_title'] ?? '') ?: 'Titre de l\'article' ?></div>
                            <div class="text-success small"><?= $_SERVER['HTTP_HOST'] ?>/article/...</div>
                            <div class="small text-secondary"><?= htmlspecialchars($old['meta_description'] ?? '') ?: 'Description de l\'article...' ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Image à la une -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Image à la une</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Image principale</label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*" onchange="previewImage(this)">
                        <div id="image_preview" class="mt-2 text-center"></div>
                        <small class="text-muted">Cette image apparaîtra en tête d'article et dans les réseaux sociaux</small>
                    </div>
                    <div class="mb-3">
                        <label for="featured_image_alt" class="form-label">Texte alternatif (alt)</label>
                        <input type="text" class="form-control" id="featured_image_alt" name="featured_image_alt" 
                               value="<?= htmlspecialchars($old['featured_image_alt'] ?? '') ?>">
                        <small class="text-muted">Description de l'image pour l'accessibilité et le SEO</small>
                    </div>
                </div>
            </div>
            
            <!-- Configuration Open Graph -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Réseaux sociaux</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="og_title" class="form-label">Titre Open Graph</label>
                        <input type="text" class="form-control" id="og_title" name="og_title" 
                               value="<?= htmlspecialchars($old['og_title'] ?? '') ?>">
                        <small class="text-muted">Laissez vide pour utiliser le titre par défaut</small>
                    </div>
                    <div class="mb-3">
                        <label for="og_description" class="form-label">Description Open Graph</label>
                        <textarea class="form-control" id="og_description" name="og_description" rows="2"><?= htmlspecialchars($old['og_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Compteur pour l'extrait
document.getElementById('excerpt').addEventListener('input', function() {
    const length = this.value.length;
    const counter = document.getElementById('excerpt-counter');
    counter.innerHTML = `${length} caractères (150-160 recommandés)`;
    if (length > 160) {
        counter.style.color = 'red';
    } else if (length > 150) {
        counter.style.color = 'orange';
    } else {
        counter.style.color = '#6c757d';
    }
});

// Compteurs meta
function updateMetaPreview() {
    const title = document.getElementById('meta_title').value;
    const description = document.getElementById('meta_description').value;
    
    document.getElementById('meta_title_length').innerHTML = title.length;
    document.getElementById('meta_description_length').innerHTML = description.length;
    
    document.getElementById('meta_preview').innerHTML = `
        <div class="fw-bold text-primary">${title || 'Titre de l\'article'}</div>
        <div class="text-success small">${window.location.host}/article/...</div>
        <div class="small text-secondary">${description || 'Description de l\'article...'}</div>
    `;
}

// Génération du slug
function generateSlugFromTitle() {
    const title = document.getElementById('title').value;
    if (title) {
        const slug = title.toLowerCase()
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
    }
}

// Prévisualisation de l'image
function previewImage(input) {
    const preview = document.getElementById('image_preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Ajout dynamique de champs image
let imageCount = 0;
function addImageField() {
    const container = document.getElementById('image-gallery');
    const div = document.createElement('div');
    div.className = 'col-md-6 image-field';
    div.innerHTML = `
        <div class="card">
            <div class="card-body">
                <input type="file" class="form-control mb-2" name="images[]" accept="image/*" onchange="previewAdditionalImage(this, ${imageCount})">
                <input type="text" class="form-control mb-2" name="image_alts[]" placeholder="Texte alternatif (alt)">
                <div id="preview_${imageCount}" class="text-center mb-2"></div>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.image-field').remove()">
                    Supprimer
                </button>
            </div>
        </div>
    `;
    container.appendChild(div);
    imageCount++;
}

function previewAdditionalImage(input, id) {
    const preview = document.getElementById(`preview_${id}`);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 100px;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function saveAsDraft() {
    const draftCheckbox = document.getElementById('is_published');
    draftCheckbox.checked = false;
    document.getElementById('articleForm').submit();
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    updateMetaPreview();
    
    // Génération auto du slug depuis le titre
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    titleInput.addEventListener('blur', function() {
        if (!slugInput.value.trim()) {
            generateSlugFromTitle();
        }
    });
});
</script>