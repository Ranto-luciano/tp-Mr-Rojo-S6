<?php
/**
 * Vue de confirmation de déconnexion
 * (Redirection automatique vers login)
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="2;url=/admin/login">
    <title>Déconnexion en cours...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-sign-out-alt fa-3x text-primary mb-3"></i>
                        <h4>Déconnexion en cours...</h4>
                        <p>Vous allez être redirigé vers la page de connexion.</p>
                        <a href="/admin/login" class="btn btn-primary">Cliquez ici si la redirection ne fonctionne pas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>