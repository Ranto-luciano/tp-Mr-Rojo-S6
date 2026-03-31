<?php
/**
 * Vue Édition d'article
 * Formulaire complet avec gestion des images existantes
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Modifier l'article</h1>
    <a href="/admin/articles" class="btn btn-secondary">
        Retour à la liste
    </a>
</div>

<form method="POST" action="/admin/articles/<?= $article['id'] ?>" enctype="multipart/form-data" id="articleForm">
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
                               id="title" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?= $errors['title'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (URL personnalisée)</label>
                        <div class="input-group">
                            <span class="input-group-text">/article/</span>
                            <input type="text" class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                                   id="slug" name="slug" value="<?= htmlspecialchars($article['slug']) ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="generateSlugFromTitle()">
                                Générer
                            </button>
                        </div>
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
                                    <?= $article['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Extrait / Résumé</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?= htmlspecialchars($article['excerpt'] ?? '') ?></textarea>
                        <div id="excerpt-counter" class="text-muted small"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label fw-bold">Contenu *</label>
                        <textarea class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>" 
                                  id="content" name="content" rows="20" required><?= htmlspecialchars($article['content']) ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Gestion des images existantes -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Images existantes</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($article['images'] as $index => $image): ?>
                        <div class="col-md-4">
                            <div class="card">
                                <img src="/storage/uploads/<?= htmlspecialchars($image['file_path']) ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($image['alt_text']) ?>" 
                                     style="height: 150px; object-fit: cover;">
                                <div class="card-body">
                                    <input type="text" class="form-control form-control-sm mb-2" 
                                           name="image_alts[<?= $image['id'] ?>]" 
                                           value="<?= htmlspecialchars($image['alt_text']) ?>"
                                           placeholder="Texte alternatif">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               name="delete_images[]" value="<?= $image['id'] ?>" 
                                               id="delete_image_<?= $image['id'] ?>">
                                        <label class="form-check-label text-danger" for="delete_image_<?= $image['id'] ?>">
                                            Supprimer cette image
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Ajout de nouvelles images -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Ajouter des images</h5>
                </div>
                <div class="card-body">
                    <div id="image-gallery" class="row g-3"></div>
                    <button type="button" class="btn btn-outline-primary" onclick="addImageField()">
                        Ajouter une image
                    </button>
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
                               <?= $article['is_published'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold" for="is_published">
                            <?= $article['is_published'] ? 'Publié' : 'Brouillon' ?>
                        </label>
                    </div>
                    
                    <?php if ($article['published_at']): ?>
                    <div class="alert alert-info small">
                        Publié le : <?= date('d/m/Y H:i', strtotime($article['published_at'])) ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-secondary small">
                        Créé par : <?= htmlspecialchars($article['author_name']) ?>
                        <br>
                        Dernière modification : <?= date('d/m/Y H:i', strtotime($article['updated_at'])) ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        Mettre à jour
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
                               value="<?= htmlspecialchars($article['meta_title'] ?? '') ?>" 
                               maxlength="70" onkeyup="updateMetaPreview()">
                        <small class="text-muted"><span id="meta_title_length">0</span>/70 caractères</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" 
                                  rows="3" maxlength="160" onkeyup="updateMetaPreview()"><?= htmlspecialchars($article['meta_description'] ?? '') ?></textarea>
                        <small class="text-muted"><span id="meta_description_length">0</span>/160 caractères</small>
                    </div>
                    
                    <div class="alert alert-info small">
                        <strong>Aperçu Google :</strong>
                        <div id="meta_preview" class="mt-2">
                            <div class="fw-bold text-primary"><?= htmlspecialchars($article['meta_title'] ?? $article['title']) ?></div>
                            <div class="text-success small"><?= $_SERVER['HTTP_HOST'] ?>/article/<?= $article['slug'] ?></div>
                            <div class="small text-secondary"><?= htmlspecialchars($article['meta_description'] ?? substr(strip_tags($article['content']), 0, 160)) ?></div>
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
                    <?php 
                    $featuredImage = $article['images'][0] ?? null;
                    if ($featuredImage): 
                    ?>
                    <div class="text-center mb-3">
                        <img src="/storage/uploads/<?= htmlspecialchars($featuredImage['file_path']) ?>" 
                             class="img-fluid rounded" alt="<?= htmlspecialchars($featuredImage['alt_text']) ?>"
                             style="max-height: 150px;">
                        <div class="mt-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="delete_featured" value="1" id="delete_featured">
                                <label class="form-check-label text-danger" for="delete_featured">Supprimer l'image à la une</label>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Remplacer l'image</label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*" onchange="previewImage(this)">
                        <div id="image_preview" class="mt-2 text-center"></div>
                    </div>
                    <div class="mb-3">
                        <label for="featured_image_alt" class="form-label">Texte alternatif (alt)</label>
                        <input type="text" class="form-control" id="featured_image_alt" name="featured_image_alt" 
                               value="<?= htmlspecialchars($featuredImage['alt_text'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Compteurs
function updateMetaPreview() {
    const title = document.getElementById('meta_title').value || document.getElementById('title').value;
    const description = document.getElementById('meta_description').value;
    
    document.getElementById('meta_title_length').innerHTML = title.length;
    document.getElementById('meta_description_length').innerHTML = description.length;
    
    document.getElementById('meta_preview').innerHTML = `
        <div class="fw-bold text-primary">${title.substring(0, 70)}</div>
        <div class="text-success small">${window.location.host}/article/${document.getElementById('slug').value}</div>
        <div class="small text-secondary">${description ? description.substring(0, 160) : '...'}</div>
    `;
}

// Compteur d'extrait
const excerptInput = document.getElementById('excerpt');
if (excerptInput) {
    excerptInput.addEventListener('input', function() {
        const length = this.value.length;
        const counter = document.getElementById('excerpt-counter');
        if (counter) {
            counter.innerHTML = `${length} caractères (150-160 recommandés)`;
            counter.style.color = length > 160 ? 'red' : (length > 150 ? 'orange' : '#6c757d');
        }
    });
    excerptInput.dispatchEvent(new Event('input'));
}

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

function previewImage(input) {
    const preview = document.getElementById('image_preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 150px;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

let imageCount = <?= count($article['images']) ?>;
function addImageField() {
    const container = document.getElementById('image-gallery');
    const div = document.createElement('div');
    div.className = 'col-md-6 image-field mb-3';
    div.innerHTML = `
        <div class="card">
            <div class="card-body">
                <input type="file" class="form-control mb-2" name="new_images[]" accept="image/*" onchange="previewNewImage(this, ${imageCount})">
                <input type="text" class="form-control mb-2" name="new_image_alts[]" placeholder="Texte alternatif (alt)">
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

function previewNewImage(input, id) {
    const preview = document.getElementById(`preview_${id}`);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 100px;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    updateMetaPreview();
    
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    if (titleInput && slugInput) {
        titleInput.addEventListener('blur', function() {
            if (!slugInput.value.trim() || slugInput.value === '<?= $article['slug'] ?>') {
                generateSlugFromTitle();
            }
        });
    }
});
</script>