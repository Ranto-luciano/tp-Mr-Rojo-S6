<?php
/**
 * Vue Création d'utilisateur
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Créer un utilisateur</h1>
    <a href="/admin/users" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informations utilisateur</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/users">
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-bold">Nom complet *</label>
                        <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                               id="full_name" name="full_name" value="<?= htmlspecialchars($old['full_name'] ?? '') ?>" required>
                        <?php if (isset($errors['full_name'])): ?>
                            <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email *</label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Mot de passe *</label>
                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                               id="password" name="password" required>
                        <small class="text-muted">Minimum 8 caractères</small>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= $errors['password'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label fw-bold">Rôle</label>
                        <select class="form-select" id="role" name="role">
                            <option value="editor">Rédacteur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                        <small class="text-muted">Les administrateurs ont tous les droits</small>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer l'utilisateur
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informations sur les rôles</h5>
            </div>
            <div class="card-body">
                <h6>Rédacteur</h6>
                <ul>
                    <li>Peut créer et modifier ses propres articles</li>
                    <li>Peut publier ses articles</li>
                    <li>Peut modifier son profil</li>
                </ul>
                
                <h6>Administrateur</h6>
                <ul>
                    <li>Tous les droits des rédacteurs</li>
                    <li>Peut gérer tous les articles</li>
                    <li>Peut gérer les catégories</li>
                    <li>Peut gérer les utilisateurs</li>
                    <li>Accès aux logs et statistiques</li>
                </ul>
            </div>
        </div>
    </div>
</div>