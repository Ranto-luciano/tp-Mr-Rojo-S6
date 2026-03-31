<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tableau de bord</h1>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Articles</h5>
                <p class="card-text display-4"><?= $stats['total_articles'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Catégories</h5>
                <p class="card-text display-4"><?= $stats['total_categories'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Utilisateurs</h5>
                <p class="card-text display-4"><?= $stats['total_users'] ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Derniers articles</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Auteur</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['recent_articles'] as $article): ?>
                    <tr>
                        <td><?= htmlspecialchars($article['title']) ?></td>
                        <td><?= htmlspecialchars($article['category_name'] ?? 'Non catégorisé') ?></td>
                        <td><?= htmlspecialchars($article['author_name']) ?></td>
                        <td>
                            <?php if ($article['is_published']): ?>
                                <span class="badge bg-success">Publié</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Brouillon</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($article['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>