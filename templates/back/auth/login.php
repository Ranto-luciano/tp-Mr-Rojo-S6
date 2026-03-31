<div class="admin-login-card">
    <div class="mb-3">
        <h1 class="h2">Connexion administration</h1>
        <p class="small text-muted">Accedez au backoffice pour gerer les contenus du site.</p>
    </div>

    <form method="POST" action="/admin/login">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="admin@example.com"
                value="admin@example.com"
                required
            >
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="change_me"
                value="change_me"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>

    <div class="credentials-box">
        <strong>Identifiants admin par defaut</strong>
        <div>Email: admin@example.com</div>
        <div>Mot de passe: change_me</div>
    </div>
</div>