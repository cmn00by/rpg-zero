<?php if (isset($_SERVER['HTTP_HX_REQUEST'])): ?>
    <?php \Core\View::partial('partials/hero_strip', ['character' => $character, 'oob' => true]); ?>
<?php endif; ?>

<div id="map-container" class="retro-box" style="margin-bottom: 30px;">
    <div class="retro-box-header">
        <div style="display:flex; align-items:center; gap: 12px;">
            <span>🗺️ <?= htmlspecialchars($zone['name'] ?? 'Carte du Monde') ?></span>
            <span style="font-size:0.9rem; color: var(--text-muted); font-family:sans-serif;">
                — Position : <strong>[ X: <?= (int)$character['pos_x'] ?> | Y: <?= (int)$character['pos_y'] ?> ]</strong>
            </span>
        </div>
        <div style="font-size:0.95rem; color: var(--accent-gold); display:flex; gap: 15px; align-items:center;">
            <span>⚡ PA : <strong><?= (int)$character['current_ap'] ?>/<?= (int)$character['effective_max_ap'] ?></strong></span>
            <span>💰 <strong><?= (int)$character['gold'] ?></strong> pièces</span>
        </div>
    </div>

    <div class="retro-box-body" style="padding: 25px;">
        <div class="tactical-map-layout">
            
            <!-- PANNEAU GAUCHE : LE PLATEAU DE JEU 2D (5x5) -->
            <div class="map-board-panel">
                <div class="panel-header-title" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>🧭 Plateau d'Exploration</span>
                    <span style="font-size:0.8rem; color:var(--text-muted); font-family:sans-serif;">1 PA / déplacement</span>
                </div>

                <!-- GRILLE TACTIQUE 5x5 -->
                <div class="rpg-tactical-grid">
                    <?php 
                    $curX = (int)$character['pos_x'];
                    $curY = (int)$character['pos_y'];
                    
                    $tileMap = [];
                    foreach ($tiles as $t) {
                        $tileMap[(int)$t['y']][(int)$t['x']] = $t;
                    }

                    for ($y = 0; $y < (int)$zone['height']; $y++):
                        for ($x = 0; $x < (int)$zone['width']; $x++):
                            $tile = $tileMap[$y][$x] ?? null;
                            if (!$tile) continue;

                            $isPlayerHere = ($x === $curX && $y === $curY);
                            $dist = abs($x - $curX) + abs($y - $curY);
                            $isAdjacent = ($dist === 1);
                            $isWalkable = (bool)$tile['is_walkable'];
                            $typeClass = 'tile-theme-' . htmlspecialchars($tile['tile_type']);
                    ?>
                        <div class="tactical-cell <?= $typeClass ?> <?= $isPlayerHere ? 'is-hero-position' : '' ?> <?= $isAdjacent && $isWalkable ? 'is-reachable-step' : '' ?> <?= !$isWalkable ? 'is-obstacle-wall' : '' ?>">
                            
                            <?php if ($isAdjacent && $isWalkable): ?>
                                <!-- Case Adjacente Cliquable -->
                                <form hx-post="/map/move-to" hx-target="#map-container" hx-swap="outerHTML" class="cell-form-link">
                                    <input type="hidden" name="x" value="<?= $x ?>">
                                    <input type="hidden" name="y" value="<?= $y ?>">
                                    <button type="submit" class="cell-action-btn" title="Marcher vers <?= htmlspecialchars($tile['name']) ?> (1 PA)">
                                        <span class="cell-terrain-tag"><?= strtoupper($tile['tile_type']) ?></span>
                                        <span class="cell-main-icon"><?= $tile['icon'] ?></span>
                                        <span class="cell-label-text"><?= htmlspecialchars(mb_substr($tile['name'], 0, 10)) ?></span>
                                        <span class="step-indicator-arrow">✦</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <!-- Case Non-Adjacente ou Actuelle -->
                                <div class="cell-static-content" title="<?= htmlspecialchars($tile['name']) ?> (<?= $tile['is_walkable'] ? 'Coût: ' . $tile['ap_cost'] . ' PA' : 'Infranchissable' ?>)">
                                    <span class="cell-terrain-tag"><?= strtoupper($tile['tile_type']) ?></span>
                                    
                                    <?php if ($isPlayerHere): ?>
                                        <div class="hero-token-disc">
                                            <span class="hero-token-avatar"><?= $character['class_icon'] ?></span>
                                            <span class="hero-token-pulse"></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="cell-main-icon"><?= $tile['icon'] ?></span>
                                    <?php endif; ?>

                                    <span class="cell-label-text <?= $isPlayerHere ? 'hero-here-label' : '' ?>">
                                        <?= $isPlayerHere ? '★ VOUS ★' : htmlspecialchars(mb_substr($tile['name'], 0, 10)) ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php 
                        endfor;
                    endfor; 
                    ?>
                </div>

                <!-- BOUSSOLE / D-PAD RÉTRO -->
                <div class="compass-navigator-box">
                    <div class="compass-grid-dpad">
                        <div></div>
                        <form hx-post="/map/move" hx-target="#map-container" hx-swap="outerHTML">
                            <input type="hidden" name="direction" value="north">
                            <button type="submit" class="btn-retro dpad-cardinal-btn" title="Marcher vers le Nord" <?= ($adjacent['north'] && $adjacent['north']['is_walkable']) ? '' : 'disabled' ?>>
                                ⬆️ Nord
                            </button>
                        </form>
                        <div></div>

                        <form hx-post="/map/move" hx-target="#map-container" hx-swap="outerHTML">
                            <input type="hidden" name="direction" value="west">
                            <button type="submit" class="btn-retro dpad-cardinal-btn" title="Marcher vers l'Ouest" <?= ($adjacent['west'] && $adjacent['west']['is_walkable']) ? '' : 'disabled' ?>>
                                ⬅️ Ouest
                            </button>
                        </form>
                        <div class="compass-core-pin"><?= $character['class_icon'] ?></div>
                        <form hx-post="/map/move" hx-target="#map-container" hx-swap="outerHTML">
                            <input type="hidden" name="direction" value="east">
                            <button type="submit" class="btn-retro dpad-cardinal-btn" title="Marcher vers l'Est" <?= ($adjacent['east'] && $adjacent['east']['is_walkable']) ? '' : 'disabled' ?>>
                                ➡️ Est
                            </button>
                        </form>

                        <div></div>
                        <form hx-post="/map/move" hx-target="#map-container" hx-swap="outerHTML">
                            <input type="hidden" name="direction" value="south">
                            <button type="submit" class="btn-retro dpad-cardinal-btn" title="Marcher vers le Sud" <?= ($adjacent['south'] && $adjacent['south']['is_walkable']) ? '' : 'disabled' ?>>
                                ⬇️ Sud
                            </button>
                        </form>
                        <div></div>
                    </div>
                </div>

                <!-- LÉGENDE DE LA CARTE -->
                <div class="map-legend-strip">
                    <span>🏰 Cité</span>
                    <span>🔨 Forge</span>
                    <span>🍺 Taverne</span>
                    <span>🌲 Forêt</span>
                    <span>🌾 Sentier</span>
                    <span>🌊 Eau</span>
                    <span>🏔️ Mont</span>
                    <span>👹 Boss</span>
                </div>
            </div>

            <!-- PANNEAU DROIT : FICHE DU LIEU ACTUEL & ACTIONS -->
            <div class="map-story-panel">
                <div class="panel-header-title">
                    <span>📜 Carnet de Voyage</span>
                </div>

                <div class="location-story-scroll">
                    <div class="location-header-row">
                        <div class="location-emblem-box"><?= $currentTile['icon'] ?></div>
                        <div style="flex:1;">
                            <span class="location-subtitle">Lieu Découvert &bull; [ <?= (int)$character['pos_x'] ?>, <?= (int)$character['pos_y'] ?> ]</span>
                            <h2 class="location-name-title"><?= htmlspecialchars($currentTile['name']) ?></h2>
                            <span class="location-type-pill"><?= strtoupper($currentTile['tile_type']) ?></span>
                        </div>
                    </div>

                    <div class="location-narrative-text">
                        <?= nl2br(htmlspecialchars($currentTile['description'])) ?>
                    </div>
                </div>

                <!-- ZONE D'ACTION CONTEXTUELLE -->
                <div class="location-action-card">
                    <div class="action-card-title">
                        <span>⚡ Interaction Spéciale :</span>
                    </div>

                    <?php if ($currentTile['action_type'] === 'shop'): ?>
                        <div class="action-prompt-block">
                            <p style="font-size:0.92rem; color:var(--text-muted); margin-bottom:12px;">
                                La forge est ouverte ! Vous pouvez y acheter des armes acérées, des armures solides et des potions revigorantes.
                            </p>
                            <a href="/game/shop" class="btn-retro btn-stat-plus btn-action-heroic">
                                🔨 <?= htmlspecialchars($currentTile['action_label'] ?? 'Entrer dans la Forge') ?>
                            </a>
                        </div>

                    <?php elseif ($currentTile['action_type'] === 'tavern'): ?>
                        <div class="action-prompt-block">
                            <p style="font-size:0.92rem; color:var(--text-muted); margin-bottom:12px;">
                                Installez-vous près du feu de cheminée. Une chope et un lit douillet restaureront 100% de vos PV et PA.
                            </p>
                            <form method="POST" action="/game/tavern/rest" style="margin:0;">
                                <button type="submit" class="btn-retro btn-primary btn-action-heroic">
                                    🍺 Se Reposer à la Taverne (10 💰)
                                </button>
                            </form>
                        </div>

                    <?php elseif ($currentTile['action_type'] === 'battle_zone'): ?>
                        <div class="action-prompt-block">
                            <p style="font-size:0.92rem; color:var(--text-muted); margin-bottom:12px;">
                                Des bruits suspects résonnent dans les taillis. Vous sentez la présence d'ennemis féroces.
                            </p>
                            <a href="/battle/explore" class="btn-retro btn-primary btn-action-heroic">
                                ⚔️ <?= htmlspecialchars($currentTile['action_label'] ?? 'Traquer les monstres') ?>
                            </a>
                        </div>

                    <?php else: ?>
                        <div class="action-prompt-empty">
                            🌾 Le passage est tranquille. Vous pouvez vous reposer un instant ou poursuivre votre voyage vers une autre case.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RACCOURCIS RAPIDES -->
                <div style="display:flex; gap:12px; margin-top:20px;">
                    <a href="/game/inventory" class="btn-retro" style="flex:1; font-size:0.9rem; padding:8px;">
                        🎒 Ouvrir le Sac
                    </a>
                    <a href="/game/stats" class="btn-retro btn-blue" style="flex:1; font-size:0.9rem; padding:8px;">
                        📜 Fiche Héros
                    </a>
                </div>
            </div>

        </div>

        <div style="text-align:center; margin-top:25px;">
            <a href="/game/hub" class="btn-retro">
                🏰 Revenir au Hub de la Ville
            </a>
        </div>
    </div>
</div>
