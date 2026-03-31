<?php
$isLoginPage = str_starts_with(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', '/admin/login');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';
$flashSuccess = $_SESSION['success'] ?? null;
$flashError = $_SESSION['error'] ?? null;
$flashWarning = $_SESSION['warning'] ?? null;
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['warning']);

$isActive = static function (array $paths) use ($currentPath): string {
    foreach ($paths as $path) {
        if (str_starts_with($currentPath, $path)) {
            return 'active';
        }
    }

    return '';
};
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration | Iran News</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
    <?php if (!$isLoginPage): ?>
        <header class="admin-topbar">
            <div class="admin-topbar-inner">
                <a href="/admin/dashboard" class="brand">Iran News Admin</a>
                <div class="topbar-right">
                    <span class="user-pill"><?= htmlspecialchars((string)($_SESSION['user_name'] ?? 'Utilisateur'), ENT_QUOTES, 'UTF-8') ?></span>
                    <a class="btn btn-outline" href="/admin/logout">Deconnexion</a>
                </div>
            </div>
        </header>

        <div class="admin-shell">
            <aside class="admin-sidebar">
                <div class="sidebar-head">
                    <p class="sidebar-kicker">Backoffice</p>
                    <h2>Navigation</h2>
                </div>

                <nav class="menu">
                    <a href="/admin/dashboard" class="menu-link <?= $isActive(['/admin/dashboard']) ?>">Tableau de bord</a>
                    <a href="/admin/articles" class="menu-link <?= $isActive(['/admin/articles']) ?>">Articles</a>
                    <a href="/admin/categories" class="menu-link <?= $isActive(['/admin/categories']) ?>">Categories</a>
                    <a href="/admin/users" class="menu-link <?= $isActive(['/admin/users']) ?>">Utilisateurs</a>
                    <a href="/admin/users/profile" class="menu-link <?= $isActive(['/admin/users/profile']) ?>">Mon profil</a>
                </nav>

                <div class="sidebar-foot">
                    <a href="/" class="sidebar-site-link" target="_blank" rel="noopener noreferrer">Voir le site public</a>
                </div>
            </aside>

            <main class="admin-main">
                <?php if ($flashSuccess): ?>
                    <div class="alert alert-success"><?= htmlspecialchars((string)$flashSuccess, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if ($flashError): ?>
                    <div class="alert alert-error"><?= htmlspecialchars((string)$flashError, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if ($flashWarning): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars((string)$flashWarning, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </main>
        </div>
    <?php else: ?>
        <main class="login-main">
            <?php if ($flashSuccess): ?>
                <div class="alert alert-success"><?= htmlspecialchars((string)$flashSuccess, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($flashError): ?>
                <div class="alert alert-error"><?= htmlspecialchars((string)$flashError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($flashWarning): ?>
                <div class="alert alert-warning"><?= htmlspecialchars((string)$flashWarning, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?= $content ?? '' ?>
        </main>
    <?php endif; ?>
</body>
</html>
