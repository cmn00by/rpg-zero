<div id="battle-container" class="retro-box">
    <div class="retro-box-header">
        <span>⚔️ Arène de Combat - Tour <?= $battle['turn'] ?></span>
        <span><?= htmlspecialchars($battle['monster_name']) ?> (Niv. <?= $battle['monster_level'] ?>)</span>
    </div>
    <div class="retro-box-body">
        <!-- Arène : Face à Face -->
        <div class="battle-arena-grid">
            <!-- Héros -->
            <div style="background: #14101c; border: 2px solid #332942; border-radius: 6px; padding: 15px; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 6px;"><?= $character['class_icon'] ?></div>
                <h3 style="color: var(--accent-gold);"><?= htmlspecialchars($character['name']) ?></h3>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px;">Niv. <?= $character['level'] ?> &bull; <?= htmlspecialchars($character['class_name']) ?></div>
                
                <div style="text-align: left; margin-bottom: 8px;">
                    <div style="font-size: 0.8rem; display:flex; justify-content:space-between;">
                        <span>Points de Vie :</span>
                        <span><?= $character['current_hp'] ?> / <?= $character['max_hp'] ?></span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill hp" style="width: <?= min(100, max(0, round(($character['current_hp'] / $character['max_hp']) * 100))) ?>%;"></div>
                    </div>
                </div>

                <div style="font-size: 0.85rem; color: var(--text-muted); display:flex; justify-content:space-around; border-top: 1px solid #251d2f; padding-top: 8px;">
                    <span>💪 <?= $character['strength'] ?></span>
                    <span>🏃 <?= $character['agility'] ?></span>
                    <span>🔮 <?= $character['intelligence'] ?></span>
                </div>
            </div>

            <!-- Monstre -->
            <div style="background: #14101c; border: 2px solid #332942; border-radius: 6px; padding: 15px; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 6px;"><?= $battle['monster_icon'] ?></div>
                <h3 style="color: #e74c3c;"><?= htmlspecialchars($battle['monster_name']) ?></h3>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px;">Niveau <?= $battle['monster_level'] ?></div>

                <div style="text-align: left; margin-bottom: 8px;">
                    <div style="font-size: 0.8rem; display:flex; justify-content:space-between;">
                        <span>Points de Vie :</span>
                        <span><?= $battle['monster_current_hp'] ?> / <?= $battle['monster_max_hp'] ?></span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill hp" style="width: <?= min(100, max(0, round(($battle['monster_current_hp'] / $battle['monster_max_hp']) * 100))) ?>%;"></div>
                    </div>
                </div>

                <div style="font-size: 0.85rem; color: var(--text-muted); display:flex; justify-content:space-around; border-top: 1px solid #251d2f; padding-top: 8px;">
                    <span>⚔️ Attaque : <?= $battle['monster_attack'] ?></span>
                    <span>🛡️ Défense : <?= $battle['monster_defense'] ?></span>
                </div>
            </div>
        </div>

        <!-- Journal de combat -->
        <h4 style="color: var(--accent-gold); margin-bottom: 8px;">📜 Journal des Actions</h4>
        <div class="combat-log-box" id="combat-log-scroll">
            <?php foreach ($logs as $log): ?>
                <div class="combat-log-entry <?= htmlspecialchars($log['action']) ?>">
                    <span style="color: var(--text-muted); font-size: 0.8rem;">[T<?= $log['turn'] ?>]</span>
                    <?= preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', htmlspecialchars($log['message'])) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Contrôles et Actions -->
        <div style="margin-top: 20px; text-align: center;">
            <?php if (!$is_finished): ?>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <form hx-post="/battle/action" hx-target="#battle-container" hx-swap="outerHTML">
                        <input type="hidden" name="action" value="attack">
                        <button type="submit" class="btn-retro btn-primary" style="font-size: 1.1rem; padding: 10px 24px;">
                            ⚔️ Porter un Coup
                        </button>
                    </form>

                    <form hx-post="/battle/action" hx-target="#battle-container" hx-swap="outerHTML">
                        <input type="hidden" name="action" value="flee">
                        <button type="submit" class="btn-retro" style="font-size: 1.1rem; padding: 10px 24px;">
                            💨 Fuir le Combat
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="/battle/explore" class="btn-retro btn-primary" style="font-size: 1.1rem; padding: 10px 24px;">
                        🌲 Continuer l'Exploration
                    </a>
                    <a href="/game/hub" class="btn-retro" style="font-size: 1.1rem; padding: 10px 24px;">
                        🏰 Retourner à la Ville
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <script>
            // Scroll automatique vers le bas du journal de combat
            var logBox = document.getElementById('combat-log-scroll');
            if (logBox) {
                logBox.scrollTop = logBox.scrollHeight;
            }
        </script>
    </div>
</div>
