<?php
/**
 * Vue Profil utilisateur
 * Gestion du compte personnel
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mon profil</h1>
    <a href="/admin/dashboard" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 100px; height: 100px; font-size: 48px;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : 'info' ?>">
                    <?= $user['role'] == 'admin' ? 'Administrateur' : 'Rédacteur' ?>
                </span>
                <p class="mt-3 small text-muted">
                    Membre depuis le <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                </p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Articles publiés :</strong>
                    <span class="float-end"><?= $user['articles_count'] ?? 0 ?></span>
                </div>
                <div class="mb-2">
                    <strong>Dernière connexion :</strong>
                    <span class="float-end"><?= date('d/m/Y H:i', strtotime($user['last_login'] ?? $user['created_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Modification du profil -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">Modifier mes informations</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/users/profile/update">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Changement de mot de passe -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Changer mon mot de passe</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/users/profile/password">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="text-muted">Minimum 8 caractères</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key"></i> Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>