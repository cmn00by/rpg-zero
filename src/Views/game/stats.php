<div class="retro-box" style="max-width: 600px; margin: 0 auto;">
    <div class="retro-box-header">
        <span>📜 Fiche de <?= htmlspecialchars($character['name']) ?></span>
        <span><?= $character['class_icon'] ?> <?= htmlspecialchars($character['class_name']) ?></span>
    </div>
    <div class="retro-box-body">
        <div style="display: flex; gap: 20px; align-items: center; border-bottom: 1px solid #3d324f; padding-bottom: 15px; margin-bottom: 15px;">
            <div style="font-size: 3.5rem; background: #251c33; border: 2px solid var(--border-gold); border-radius: 8px; width: 80px; height: 80px; display:flex; align-items:center; justify-content:center;">
                <?= $character['class_icon'] ?>
            </div>
            <div style="flex: 1;">
                <h2 style="color: var(--accent-gold); font-size: 1.4rem;"><?= htmlspecialchars($character['name']) ?></h2>
                <p style="color: var(--text-muted);">Niveau <?= $character['level'] ?> &bull; <?= htmlspecialchars($character['class_name']) ?></p>
                
                <div style="margin-top: 8px;">
                    <div style="font-size: 0.8rem; display:flex; justify-content:space-between; margin-bottom: 2px;">
                        <span>Progression XP :</span>
                        <span><?= $character['xp'] ?> / <?= $character['xp_next'] ?></span>
                    </div>
                    <div class="progress-bar-container" style="height: 12px;">
                        <div class="progress-bar-fill xp" style="width: <?= min(100, round(($character['xp'] / max(1, $character['xp_next'])) * 100)) ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="color: var(--accent-gold); font-size: 0.9rem; margin-bottom: 5px;">❤️ Points de Vie (PV)</div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['current_hp'] ?> / <?= $character['max_hp'] ?></div>
            </div>
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="color: var(--accent-gold); font-size: 0.9rem; margin-bottom: 5px;">⚡ Points d'Action (PA)</div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['current_ap'] ?> / <?= $character['max_ap'] ?></div>
            </div>
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="color: var(--accent-gold); font-size: 0.9rem; margin-bottom: 5px;">💪 Force</div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['strength'] ?></div>
            </div>
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="color: var(--accent-gold); font-size: 0.9rem; margin-bottom: 5px;">🏃 Agilité</div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['agility'] ?></div>
            </div>
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="color: var(--accent-gold); font-size: 0.9rem; margin-bottom: 5px;">🔮 Intelligence</div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['intelligence'] ?></div>
            </div>
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="color: var(--accent-gold); font-size: 0.9rem; margin-bottom: 5px;">💰 Bourse d'Or</div>
                <div style="font-size: 1.2rem; font-weight: bold; color: var(--accent-gold);"><?= $character['gold'] ?> pièces</div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="/game/hub" class="btn-retro">🏰 Revenir au Hub</a>
        </div>
    </div>
</div>
