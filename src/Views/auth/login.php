<div class="retro-box" style="max-width: 460px; margin: 40px auto;">
    <div class="retro-box-header">
        <span>⚔️ Entrer dans le Royaume</span>
    </div>
    <div class="retro-box-body">
        <form method="POST" action="/login">
            <div class="form-group">
                <label class="form-label" for="username">Nom d'Aventurier (Pseudo) :</label>
                <input type="text" id="username" name="username" class="form-input" required autofocus autocomplete="username">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" class="form-input" required autocomplete="current-password">
            </div>

            <div style="margin-top: 25px; display: flex; justify-content: space-between; align-items: center;">
                <button type="submit" class="btn-retro btn-primary">Se Connecter 🛡️</button>
                <a href="/register" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: underline;">Créer un compte</a>
            </div>
        </form>
    </div>
</div>
