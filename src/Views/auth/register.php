<div class="retro-box" style="max-width: 460px; margin: 40px auto;">
    <div class="retro-box-header">
        <span>📜 Registre des Héros</span>
    </div>
    <div class="retro-box-body">
        <form method="POST" action="/register">
            <div class="form-group">
                <label class="form-label" for="username">Choisissez un Nom d'Aventurier :</label>
                <input type="text" id="username" name="username" class="form-input" required minlength="3" maxlength="20" autofocus autocomplete="username">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" class="form-input" required minlength="6" autocomplete="new-password">
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirm">Confirmez le mot de passe :</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-input" required minlength="6" autocomplete="new-password">
            </div>

            <div style="margin-top: 25px; display: flex; justify-content: space-between; align-items: center;">
                <button type="submit" class="btn-retro btn-primary">Créer mon Compte ✨</button>
                <a href="/login" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: underline;">Déjà inscrit ?</a>
            </div>
        </form>
    </div>
</div>
