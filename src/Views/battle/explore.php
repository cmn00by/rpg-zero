<div class="retro-box">
    <div class="retro-box-header">
        <span>🌲 La Forêt Sombre</span>
        <span>Coût : 5 ⚡ PA</span>
    </div>
    <div class="retro-box-body">
        <p style="margin-bottom: 20px; font-size: 1.05rem; line-height: 1.6;">
            La canopée étouffe les rayons du soleil. Des bruissements inquiétants se font entendre derrière les fougères. Des créatures sauvages et des brigands rôdent dans les parages.
        </p>

        <div style="background: #14101c; border: 1px solid #3d324f; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
            <h3 style="color: var(--accent-gold); margin-bottom: 10px;">📋 Faune répertoriée (Niveau 1 à 4)</h3>
            <ul style="list-style-position: inside; color: var(--text-muted); line-height: 1.8;">
                <li>🐀 Rat d'égout géant (Niveau 1)</li>
                <li>👺 Gobelin pillard (Niveau 1)</li>
                <li>🐺 Loup affamé (Niveau 2)</li>
                <li>🦹 Bandit de grand chemin (Niveau 2)</li>
                <li>💀 Squelette antique (Niveau 3)</li>
                <li>👹 Troll des cavernes - Mini-Boss (Niveau 4)</li>
            </ul>
        </div>

        <div style="display: flex; gap: 15px; justify-content: center;">
            <form method="POST" action="/battle/start">
                <button type="submit" class="btn-retro btn-primary" style="font-size: 1.1rem; padding: 12px 25px;" <?= ($character['current_ap'] < 5 || $character['current_hp'] <= 0) ? 'disabled' : '' ?>>
                    🌲 S'aventurer dans les bois (5 PA)
                </button>
            </form>
            <a href="/game/hub" class="btn-retro" style="font-size: 1.1rem; padding: 12px 25px;">
                🏰 Retourner en Ville
            </a>
        </div>

        <?php if ($character['current_ap'] < 5): ?>
            <p style="color: #e74c3c; text-align: center; margin-top: 15px; font-size: 0.9rem;">
                ⚡ Vos PA se rechargent passivement (1 PA / minute) ou immédiatement à la Taverne !
            </p>
        <?php endif; ?>
    </div>
</div>
