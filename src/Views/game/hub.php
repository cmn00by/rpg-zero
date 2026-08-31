<div class="retro-box">
    <div class="retro-box-header">
        <span>🏰 La Cité d'Orépierre</span>
        <span style="font-size: 0.9rem; color: var(--text-muted);">Lieu de paix et de commerce</span>
    </div>
    <div class="retro-box-body">
        <p style="margin-bottom: 20px; font-size: 1.05rem; line-height: 1.6;">
            Bienvenue à Orépierre, voyageur. Les forgerons martèlent le métal, les bruits de chopes résonnent depuis la taverne voisine, et au loin, les arbres sombres de la forêt semblent dissimuler de sombres secrets...
        </p>

        <?php if ($activeBattle): ?>
            <div class="flash-alert flash-warning" style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
                <span>⚠️ Vous avez un combat en cours contre un <strong><?= htmlspecialchars($activeBattle['monster_name']) ?></strong> !</span>
                <a href="/battle/arena" class="btn-retro btn-primary">Reprendre le Combat ⚔️</a>
            </div>
        <?php endif; ?>

        <div class="hub-grid">
            <!-- Carte du Monde -->
            <div class="hub-card">
                <div class="hub-card-icon">🗺️</div>
                <h3 style="color: var(--accent-gold); margin-bottom: 8px;">Carte du Monde</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">
                    Explorez la vallée case par case, visitez la forge et découvrez les secrets ancestraux.
                </p>
                <a href="/game/map" class="btn-retro btn-primary" style="width: 100%;">
                    Explorer la Carte 🧭
                </a>
            </div>

            <!-- Taverne -->
            <div class="hub-card">
                <div class="hub-card-icon">🍺</div>
                <h3 style="color: var(--accent-gold); margin-bottom: 8px;">La Taverne du Sanglier</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">
                    Reposez-vous pour restaurer l'intégralité de vos points de vie et d'énergie.
                </p>
                <form method="POST" action="/game/tavern/rest">
                    <button type="submit" class="btn-retro" style="width: 100%;">
                        Se Reposer (10 💰)
                    </button>
                </form>
            </div>

            <!-- Sac & Équipement -->
            <div class="hub-card">
                <div class="hub-card-icon">🎒</div>
                <h3 style="color: var(--accent-gold); margin-bottom: 8px;">Sac & Équipement</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">
                    Équipez vos armes, armures et organisez votre sac à dos extensible.
                </p>
                <a href="/game/inventory" class="btn-retro btn-stat-plus" style="width: 100%;">
                    Ouvrir le Sac 🛡️
                </a>
            </div>

            <!-- Exploration / Forêt -->
            <div class="hub-card">
                <div class="hub-card-icon">🌲</div>
                <h3 style="color: var(--accent-gold); margin-bottom: 8px;">La Forêt Sombre</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">
                    Traquez des créatures sauvages, gagnez de l'expérience et pillez de l'équipement.
                </p>
                <a href="/battle/explore" class="btn-retro btn-primary" style="width: 100%;">
                    Partir en Expédition ⚔️
                </a>
            </div>

            <!-- Fiche de Personnage -->
            <div class="hub-card">
                <div class="hub-card-icon">📜</div>
                <h3 style="color: var(--accent-gold); margin-bottom: 8px;">Fiche de Personnage</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">
                    Consultez vos statistiques, vos bonus d'équipement et répartissez vos attributs.
                </p>
                <a href="/game/stats" class="btn-retro btn-blue" style="width: 100%;">
                    Consulter 📜
                </a>
            </div>
        </div>
    </div>
</div>
