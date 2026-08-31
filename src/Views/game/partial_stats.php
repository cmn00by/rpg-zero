<?php \Core\View::partial('partials/hero_strip', ['character' => $character, 'oob' => true]); ?>
<div id="stats-container" class="retro-box" style="max-width: 650px; margin: 0 auto;">
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
                <div style="display:flex; align-items:center; gap: 10px;">
                    <h2 style="color: var(--accent-gold); font-size: 1.4rem;"><?= htmlspecialchars($character['name']) ?></h2>
                    <span style="font-size: 0.8rem; background: #3d2f14; border: 1px solid var(--border-gold); padding: 2px 8px; border-radius: 4px; color: var(--accent-gold);">
                        <?= htmlspecialchars($character['title'] ?? 'Novice') ?>
                    </span>
                </div>
                <p style="color: var(--text-muted); margin-top: 4px;">Niveau <?= $character['level'] ?> &bull; <?= htmlspecialchars($character['class_name']) ?></p>
                
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

        <?php if ((int)$character['stat_points'] > 0): ?>
            <div class="flash-alert flash-warning" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 18px; border-width: 2px;">
                <span>✨ <strong>Points disponibles :</strong> Vous avez <strong><?= $character['stat_points'] ?></strong> point(s) à distribuer !</span>
                <span style="font-size: 0.85rem; color: var(--accent-gold);">Cliquez sur les boutons <strong>[+]</strong></span>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <!-- PV -->
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 5px;">
                    <span style="color: var(--accent-gold); font-size: 0.9rem;">❤️ Points de Vie</span>
                    <?php if ((int)$character['stat_points'] > 0): ?>
                        <form hx-post="/character/allocate-stat" hx-target="#stats-container" hx-swap="outerHTML" style="margin:0;">
                            <input type="hidden" name="stat" value="max_hp">
                            <button type="submit" class="btn-retro btn-stat-plus" title="Ajouter +10 PV Max">+10 PV</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['current_hp'] ?> / <?= $character['max_hp'] ?></div>
            </div>

            <!-- PA -->
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 5px;">
                    <span style="color: var(--accent-gold); font-size: 0.9rem;">⚡ Points d'Action</span>
                    <?php if ((int)$character['stat_points'] > 0): ?>
                        <form hx-post="/character/allocate-stat" hx-target="#stats-container" hx-swap="outerHTML" style="margin:0;">
                            <input type="hidden" name="stat" value="max_ap">
                            <button type="submit" class="btn-retro btn-stat-plus" title="Ajouter +1 PA Max">+1 PA</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['current_ap'] ?> / <?= $character['max_ap'] ?></div>
            </div>

            <!-- Force -->
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 5px;">
                    <span style="color: var(--accent-gold); font-size: 0.9rem;">💪 Force</span>
                    <?php if ((int)$character['stat_points'] > 0): ?>
                        <form hx-post="/character/allocate-stat" hx-target="#stats-container" hx-swap="outerHTML" style="margin:0;">
                            <input type="hidden" name="stat" value="strength">
                            <button type="submit" class="btn-retro btn-stat-plus" title="Ajouter +1 Force">+1 💪</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['strength'] ?></div>
            </div>

            <!-- Agilité -->
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 5px;">
                    <span style="color: var(--accent-gold); font-size: 0.9rem;">🏃 Agilité</span>
                    <?php if ((int)$character['stat_points'] > 0): ?>
                        <form hx-post="/character/allocate-stat" hx-target="#stats-container" hx-swap="outerHTML" style="margin:0;">
                            <input type="hidden" name="stat" value="agility">
                            <button type="submit" class="btn-retro btn-stat-plus" title="Ajouter +1 Agilité">+1 🏃</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['agility'] ?></div>
            </div>

            <!-- Intelligence -->
            <div style="background: #14101c; padding: 12px; border-radius: 4px; border: 1px solid #332942;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 5px;">
                    <span style="color: var(--accent-gold); font-size: 0.9rem;">🔮 Intelligence</span>
                    <?php if ((int)$character['stat_points'] > 0): ?>
                        <form hx-post="/character/allocate-stat" hx-target="#stats-container" hx-swap="outerHTML" style="margin:0;">
                            <input type="hidden" name="stat" value="intelligence">
                            <button type="submit" class="btn-retro btn-stat-plus" title="Ajouter +1 Intelligence">+1 🔮</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div style="font-size: 1.2rem; font-weight: bold;"><?= $character['intelligence'] ?></div>
            </div>

            <!-- Or -->
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
