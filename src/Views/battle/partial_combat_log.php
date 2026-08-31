<?php if (isset($_SERVER['HTTP_HX_REQUEST'])): ?>
    <?php \Core\View::partial('partials/hero_strip', ['character' => $character, 'oob' => true]); ?>
<?php endif; ?>

<?php
$isFin = (bool)$is_finished;
$charClass = $character['class_code'] ?? 'warrior';
$skillTitle = '💥 Frappe Dévastatrice';
if ($charClass === 'rogue') $skillTitle = '🗡️ Attaque Sournoise';
if ($charClass === 'mage') $skillTitle = '🔮 Éclair Arcanique';

$effMaxHp = (int)($character['effective_max_hp'] ?? $character['max_hp']);
$curHp = (int)$character['current_hp'];
$monMaxHp = (int)$battle['monster_max_hp'];
$monCurHp = (int)$battle['monster_current_hp'];

$sum = $summary ?? \Models\Battle::calculateDuelSummary((int)$battle['id'], $battle, $character, $logs, $winner, $rewards);
?>

<div id="battle-container" class="retro-box duel-arena-frame" style="max-width: 840px; margin: 0 auto 30px auto;">
    <!-- En-tête de l'Arène -->
    <div class="retro-box-header duel-header">
        <div style="display:flex; align-items:center; gap: 10px;">
            <span class="duel-swords-icon">⚔️</span>
            <span style="font-size:1.1rem; font-weight:bold; letter-spacing:1px; text-transform:uppercase;">Arène des Duels</span>
            <span style="font-size:0.85rem; color:var(--text-muted); font-family:sans-serif;">— Tour <?= (int)$battle['turn'] ?></span>
        </div>
        <div style="font-size:0.9rem; color:var(--accent-gold);">
            ⚡ PA : <strong><?= (int)$character['current_ap'] ?>/<?= (int)$character['effective_max_ap'] ?></strong>
        </div>
    </div>

    <div class="retro-box-body" style="padding: 24px;">
        
        <!-- ===================================================================
             1. CARTE DU DUEL (MATCHUP VS FACE-À-FACE)
             =================================================================== -->
        <div class="duel-matchup-container">
            
            <!-- COMBATTANT GAUCHE : HÉROS -->
            <div class="duel-fighter-card hero-side">
                <div class="fighter-tag-line">
                    <strong><?= htmlspecialchars($character['name']) ?></strong> (Niv. <?= $character['level'] ?>)
                </div>
                <div class="fighter-title-line"><?= htmlspecialchars($character['title'] ?? 'Novice') ?></div>

                <div class="fighter-avatar-box hero-avatar-glow">
                    <span class="fighter-large-icon"><?= $character['class_icon'] ?></span>
                    <span class="fighter-banner-name"><?= htmlspecialchars($character['name']) ?></span>
                </div>

                <!-- Sphères d'Équipement -->
                <div class="fighter-gear-orbs">
                    <div class="gear-orb" title="Arme équipée">⚔️</div>
                    <div class="gear-orb" title="Armure équipée">🥋</div>
                    <div class="gear-orb" title="Casque / Bouclier">🛡️</div>
                </div>
            </div>

            <!-- CENTRE : INSIGNE VS ÉPIQUE -->
            <div class="duel-vs-emblem-center">
                <div class="vs-crescent-bg">
                    <span class="vs-large-text">VS</span>
                </div>
            </div>

            <!-- COMBATTANT DROIT : MONSTRE -->
            <div class="duel-fighter-card monster-side">
                <div class="fighter-tag-line">
                    <strong style="color:#ff7675;"><?= htmlspecialchars($battle['monster_name']) ?></strong> (Niv. <?= $battle['monster_level'] ?>)
                </div>
                <div class="fighter-title-line">Créature de la Forêt</div>

                <div class="fighter-avatar-box monster-avatar-glow">
                    <span class="fighter-large-icon"><?= $battle['monster_icon'] ?></span>
                    <span class="fighter-banner-name" style="color:#ff7675;"><?= htmlspecialchars($battle['monster_name']) ?></span>
                </div>

                <!-- Sphères de Menace -->
                <div class="fighter-gear-orbs">
                    <div class="gear-orb monster" title="Attaque : <?= $battle['monster_attack'] ?>">⚔️ <?= $battle['monster_attack'] ?></div>
                    <div class="gear-orb monster" title="Défense : <?= $battle['monster_defense'] ?>">🛡️ <?= $battle['monster_defense'] ?></div>
                </div>
            </div>

        </div>


        <!-- ===================================================================
             2. JAUGE DE CHOC DU TOUR (DUEL IMPACT & HP BARS)
             =================================================================== -->
        <div class="duel-clash-gauge-section">
            <div class="clash-side-box">
                <div class="clash-hp-label">
                    <span><?= htmlspecialchars($character['name']) ?></span>
                    <span style="color:var(--accent-gold); font-weight:bold;"><?= $curHp ?> / <?= $effMaxHp ?></span>
                </div>
                <div class="progress-bar-container" style="height:14px;">
                    <div class="progress-bar-fill hp" style="width: <?= min(100, max(0, round(($curHp / max(1, $effMaxHp)) * 100))) ?>%;"></div>
                </div>
                
                <!-- Dégâts infligés au dernier coup -->
                <div class="clash-impact-badge hero-impact">
                    <?php if (!empty($sum['last_player_action'])): ?>
                        <span class="impact-action-title"><?= ($sum['last_player_action']['action'] === 'crit') ? '💥 COUP CRITIQUE' : '⚔️ Attaque' ?></span>
                        <span class="impact-damage-value">+<?= (int)$sum['last_player_action']['damage'] ?></span>
                    <?php else: ?>
                        <span class="impact-action-title">⚔️ Prêt au choc</span>
                        <span class="impact-damage-value">--</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pastille centrale du Tour -->
            <div class="clash-round-bubble">
                <span class="round-num-text"><?= (int)$battle['turn'] ?></span>
                <span class="round-arrow">&gt;</span>
            </div>

            <div class="clash-side-box">
                <div class="clash-hp-label" style="text-align:right;">
                    <span style="color:#ff7675;"><?= htmlspecialchars($battle['monster_name']) ?></span>
                    <span style="color:#ff7675; font-weight:bold;"><?= $monCurHp ?> / <?= $monMaxHp ?></span>
                </div>
                <div class="progress-bar-container" style="height:14px;">
                    <div class="progress-bar-fill hp" style="background:#e74c3c; width: <?= min(100, max(0, round(($monCurHp / max(1, $monMaxHp)) * 100))) ?>%;"></div>
                </div>

                <!-- Dégâts subis de la riposte -->
                <div class="clash-impact-badge monster-impact">
                    <?php if (!empty($sum['last_monster_action'])): ?>
                        <span class="impact-action-title">🩸 Riposte subie</span>
                        <span class="impact-damage-value" style="color:#e74c3c;">-<?= (int)$sum['last_monster_action']['damage'] ?></span>
                    <?php else: ?>
                        <span class="impact-action-title">🛡️ En garde</span>
                        <span class="impact-damage-value">--</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        <!-- ===================================================================
             3. CONTRÔLES DU COMBAT & COMPÉTENCES
             =================================================================== -->
        <?php if (!$isFin): ?>
            <div class="duel-action-dock">
                <!-- Attaque standard -->
                <form hx-post="/battle/action" hx-target="#battle-container" hx-swap="outerHTML" style="margin:0;">
                    <input type="hidden" name="action" value="attack">
                    <button type="submit" class="btn-retro btn-primary duel-dock-btn" title="Attaque de base avec votre arme">
                        ⚔️ Porter un Coup
                    </button>
                </form>

                <!-- Compétence Spéciale -->
                <form hx-post="/battle/action" hx-target="#battle-container" hx-swap="outerHTML" style="margin:0;">
                    <input type="hidden" name="action" value="special">
                    <button type="submit" class="btn-retro btn-stat-plus duel-dock-btn" title="Frappe puissante de votre classe">
                        <?= $skillTitle ?>
                    </button>
                </form>

                <!-- Posture Défensive -->
                <form hx-post="/battle/action" hx-target="#battle-container" hx-swap="outerHTML" style="margin:0;">
                    <input type="hidden" name="action" value="defend">
                    <button type="submit" class="btn-retro btn-blue duel-dock-btn" title="Divise les dégâts reçus par 2 ce tour">
                        🛡️ Garde Défensive
                    </button>
                </form>

                <!-- Potion de Soin -->
                <form hx-post="/battle/action" hx-target="#battle-container" hx-swap="outerHTML" style="margin:0;">
                    <input type="hidden" name="action" value="potion">
                    <button type="submit" class="btn-retro duel-dock-btn" <?= ($sum['potions_count'] <= 0) ? 'disabled' : '' ?> title="Boire une potion du sac">
                        🧪 Potion (<?= $sum['potions_count'] ?>)
                    </button>
                </form>

                <!-- Fuite -->
                <form hx-post="/battle/action" hx-target="#battle-container" hx-swap="outerHTML" style="margin:0;">
                    <input type="hidden" name="action" value="flee">
                    <button type="submit" class="btn-retro duel-dock-btn btn-flee" title="Tenter de fuir le duel">
                        💨 Fuir
                    </button>
                </form>
            </div>
        <?php endif; ?>


        <!-- ===================================================================
             4. COMPTE RENDU DU DUEL (TABLEAU DE RÉSULTAT POST-COMBAT)
             =================================================================== -->
        <?php if ($isFin): ?>
            <div class="duel-summary-report-card">
                <div class="report-header-banner <?= $winner === 'player' ? 'victory' : 'defeat' ?>">
                    <?php if ($winner === 'player'): ?>
                        <span>🏆 (<?= htmlspecialchars($character['name']) ?>) a triomphé au <?= (int)$battle['turn'] ?>ème tour du duel !</span>
                    <?php elseif ($winner === 'fled'): ?>
                        <span>💨 (<?= htmlspecialchars($character['name']) ?>) a fui le combat au <?= (int)$battle['turn'] ?>ème tour du duel.</span>
                    <?php else: ?>
                        <span>💀 (<?= htmlspecialchars($character['name']) ?>) a succombé au <?= (int)$battle['turn'] ?>ème tour du duel.</span>
                    <?php endif; ?>
                </div>

                <div class="report-table-title">
                    <span>⚔️ COMPTE RENDU DU DUEL</span>
                </div>

                <div class="duel-summary-grid">
                    <!-- COLONNE HÉROS -->
                    <div class="summary-combatant-column">
                        <div class="summary-mini-header">
                            <div class="summary-mini-avatar hero"><?= $character['class_icon'] ?></div>
                            <div>
                                <strong>(<?= htmlspecialchars($character['name']) ?>)</strong><br>
                                <small><?= htmlspecialchars($character['title'] ?? 'Novice') ?> - Niv. <?= $character['level'] ?></small>
                            </div>
                            <?php if ($winner === 'player'): ?>
                                <div class="trophy-badge">🏆 VAINQUEUR</div>
                            <?php else: ?>
                                <div class="defeat-badge">💀 VAINCU</div>
                            <?php endif; ?>
                        </div>

                        <!-- Lignes de Statistiques Héros -->
                        <div class="summary-stat-category">⚔️ DÉGÂTS</div>
                        <div class="summary-stat-row">
                            <span>Total des dégâts infligés</span>
                            <strong><?= $sum['player_damage_total'] ?></strong>
                        </div>
                        <div class="summary-stat-row">
                            <span>Total des dégâts parés / esquivés</span>
                            <strong><?= $sum['player_damage_parried'] ?></strong>
                        </div>
                        <div class="summary-stat-row score-row">
                            <span>Score du combat</span>
                            <strong><?= $sum['player_score'] ?></strong>
                        </div>

                        <div class="summary-stat-category">✨ EXPÉRIENCE</div>
                        <div class="summary-stat-row">
                            <span>Expérience du duel</span>
                            <strong><?= $rewards['xp'] ?? 0 ?> XP</strong>
                        </div>
                        <div class="summary-stat-row">
                            <span>Bonus de victoire</span>
                            <strong>+<?= ($rewards['xp'] ?? 0) > 0 ? (int)floor($rewards['xp'] * 0.5) : 0 ?> XP</strong>
                        </div>
                        <div class="summary-stat-row total-highlight">
                            <span>Total XP Gagné</span>
                            <strong style="color:var(--accent-gold);">+<?= $rewards['xp'] ?? 0 ?> XP</strong>
                        </div>

                        <div class="summary-stat-category">💰 BUTIN D'OR</div>
                        <div class="summary-stat-row">
                            <span>Pièces d'or récoltées</span>
                            <strong style="color:var(--accent-gold);">+<?= $rewards['gold'] ?? 0 ?> 💰</strong>
                        </div>

                        <div class="summary-stat-category">🎁 BUTIN D'ÉQUIPEMENT</div>
                        <div class="summary-stat-row">
                            <span>Adversaire pillé</span>
                            <?php if (!empty($rewards['loot_item'])): ?>
                                <strong style="color:#2ecc71;">🎁 <?= $rewards['loot_item']['icon'] ?> <?= htmlspecialchars($rewards['loot_item']['name']) ?></strong>
                            <?php else: ?>
                                <span style="color:var(--text-muted);">Aucun équipement</span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($rewards['leveled_up'])): ?>
                            <div class="summary-levelup-banner">
                                ✨ <strong>MONTÉE DE NIVEAU !</strong> Vous passez <strong>Niveau <?= $rewards['new_level'] ?></strong> !<br>
                                <small>+<?= $rewards['stat_points_gained'] ?> pts d'attributs, +<?= $rewards['gold_bonus_gained'] ?> 💰 et +<?= $rewards['slots_gained'] ?> slot de sac débloqués !</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- COLONNE MONSTRE -->
                    <div class="summary-combatant-column">
                        <div class="summary-mini-header">
                            <div class="summary-mini-avatar monster"><?= $battle['monster_icon'] ?></div>
                            <div>
                                <strong style="color:#ff7675;"><?= htmlspecialchars($battle['monster_name']) ?></strong><br>
                                <small>Créature de la Forêt - Niv. <?= $battle['monster_level'] ?></small>
                            </div>
                            <?php if ($winner === 'monster'): ?>
                                <div class="trophy-badge">🏆 VAINQUEUR</div>
                            <?php else: ?>
                                <div class="defeat-badge">💀 VAINCU</div>
                            <?php endif; ?>
                        </div>

                        <!-- Lignes de Statistiques Monstre -->
                        <div class="summary-stat-category">⚔️ DÉGÂTS</div>
                        <div class="summary-stat-row">
                            <span>Total des dégâts infligés</span>
                            <strong><?= $sum['monster_damage_total'] ?></strong>
                        </div>
                        <div class="summary-stat-row">
                            <span>Total des dégâts parés</span>
                            <strong><?= $sum['monster_damage_parried'] ?></strong>
                        </div>
                        <div class="summary-stat-row score-row">
                            <span>Score</span>
                            <strong><?= $sum['monster_score'] ?></strong>
                        </div>

                        <div class="summary-stat-category">✨ EXPÉRIENCE</div>
                        <div class="summary-stat-row">
                            <span>Expérience du duel</span>
                            <strong>0</strong>
                        </div>
                        <div class="summary-stat-row">
                            <span>Total</span>
                            <strong>0</strong>
                        </div>

                        <div class="summary-stat-category">💰 BUTIN D'OR</div>
                        <div class="summary-stat-row">
                            <span>Gain d'or</span>
                            <strong>0</strong>
                        </div>

                        <div class="summary-stat-category">🎁 BUTIN D'ÉQUIPEMENT</div>
                        <div class="summary-stat-row">
                            <span>Objet cédé</span>
                            <span><?= !empty($rewards['loot_item']) ? $rewards['loot_item']['icon'] : '—' ?></span>
                        </div>
                    </div>
                </div>

                <!-- Liens de Navigation Post-Combat -->
                <div class="summary-actions-nav">
                    <form method="POST" action="/battle/start" style="margin:0;">
                        <button type="submit" class="btn-retro btn-primary" style="font-size:1.05rem; padding:10px 22px;">
                            🌲 Combattre à nouveau (5 PA)
                        </button>
                    </form>
                    <a href="/game/map" class="btn-retro btn-stat-plus" style="font-size:1.05rem; padding:10px 22px;">
                        🗺️ Retourner à la Carte
                    </a>
                    <a href="/game/hub" class="btn-retro" style="font-size:1.05rem; padding:10px 22px;">
                        🏰 Revenir à la Ville
                    </a>
                </div>
            </div>
        <?php endif; ?>


        <!-- ===================================================================
             5. HISTORIQUE DÉROULANT DES ACTIONS DU DUEL
             =================================================================== -->
        <div class="duel-log-container">
            <div style="font-size:0.9rem; font-weight:bold; color:var(--accent-gold); margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
                <span>📜 Déroulement du Duel</span>
                <span style="font-size:0.75rem; color:var(--text-muted); font-family:sans-serif;"><?= count($logs) ?> actions enregistrées</span>
            </div>
            <div class="combat-log-box" id="combat-log-scroll">
                <?php foreach ($logs as $log): ?>
                    <div class="combat-log-entry <?= htmlspecialchars($log['action']) ?>">
                        <span style="color: var(--text-muted); font-size: 0.8rem;">[T<?= $log['turn'] ?>]</span>
                        <?= preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', htmlspecialchars($log['message'])) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
            var logBox = document.getElementById('combat-log-scroll');
            if (logBox) {
                logBox.scrollTop = logBox.scrollHeight;
            }
        </script>

    </div>
</div>
