<?php
/**
 * Vue Édition d'utilisateur
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Modifier l'utilisateur</h1>
    <a href="/admin/users" class="btn btn-secondary">
        Retour
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informations utilisateur</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/users/<?= $user['id'] ?>">
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-bold">Nom complet</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label fw-bold">Rôle</label>
                        <select class="form-select" id="role" name="role">
                            <option value="editor" <?= $user['role'] == 'editor' ? 'selected' : '' ?>>Rédacteur</option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrateur</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="text-muted">Laissez vide pour conserver le mot de passe actuel</small>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        Mettre à jour
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Articles publiés :</strong>
                    <span class="float-end"><?= $user['articles_count'] ?? 0 ?></span>
                </div>
                <div class="mb-2">
                    <strong>Membre depuis :</strong>
                    <span class="float-end"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                </div>
                <div class="mb-2">
                    <strong>Dernière modification :</strong>
                    <span class="float-end"><?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?></span>
                </div>
            </div>
        </div>
        
        <?php if ($user['articles_count'] > 0): ?>
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Derniers articles de cet utilisateur</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($user['recent_articles'] ?? [] as $article): ?>
                    <li class="list-group-item">
                        <a href="/admin/articles/<?= $article['id'] ?>/edit">
                            <?= htmlspecialchars($article['title']) ?>
                        </a>
                        <span class="badge bg-<?= $article['is_published'] ? 'success' : 'warning' ?> float-end">
                            <?= $article['is_published'] ? 'Publié' : 'Brouillon' ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>