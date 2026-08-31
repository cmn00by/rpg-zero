<?php \Core\View::partial('partials/hero_strip', ['character' => $character, 'oob' => true]); ?>
<div id="map-container" class="retro-box">
    <div class="retro-box-header">
        <div style="display:flex; align-items:center; gap: 12px;">
            <span>🗺️ <?= htmlspecialchars($zone['name'] ?? 'Carte du Monde') ?></span>
            <span style="font-size:0.9rem; color: var(--text-muted); font-family:sans-serif;">
                Coordonnées : <strong>[ X: <?= (int)$character['pos_x'] ?> | Y: <?= (int)$character['pos_y'] ?> ]</strong>
            </span>
        </div>
        <div style="font-size:0.95rem; color: var(--accent-gold);">
            ⚡ Points d'Action : <strong><?= (int)$character['current_ap'] ?>/<?= (int)$character['effective_max_ap'] ?></strong>
        </div>
    </div>

    <div class="retro-box-body" style="padding: 25px;">
        <div class="map-view-layout">
            
            <!-- COLONNE GAUCHE : LA GRILLE INTERACTIVE (5x5) -->
            <div class="map-grid-column">
                <div class="panel-header-title">
                    <span>🧭 Carte Régionale (5x5)</span>
                </div>

                <!-- Grille 5x5 -->
                <div class="world-map-grid-matrix">
                    <?php 
                    $curX = (int)$character['pos_x'];
                    $curY = (int)$character['pos_y'];
                    
                    // Organiser les tuiles par [y][x]
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
                    ?>
                        <div class="map-tile-cell <?= $isPlayerHere ? 'player-current' : '' ?> <?= $isAdjacent && $isWalkable ? 'adjacent-movable' : '' ?> <?= !$isWalkable ? 'obstacle-cell' : '' ?>"
                             title="<?= htmlspecialchars($tile['name']) ?> (<?= $tile['is_walkable'] ? 'Coût: ' . $tile['ap_cost'] . ' PA' : 'Infranchissable' ?>)">
                            
                            <?php if ($isAdjacent && $isWalkable): ?>
                                <!-- Clic direct pour se déplacer sur case adjacente -->
                                <form hx-post="/map/move-to" hx-target="#map-container" hx-swap="outerHTML" style="margin:0; width:100%; height:100%;">
                                    <input type="hidden" name="x" value="<?= $x ?>">
                                    <input type="hidden" name="y" value="<?= $y ?>">
                                    <button type="submit" class="btn-tile-click-move">
                                        <span class="cell-icon"><?= $tile['icon'] ?></span>
                                        <span class="cell-coord"><?= $x ?>,<?= $y ?></span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="cell-inner-static">
                                    <?php if ($isPlayerHere): ?>
                                        <div class="player-avatar-pin">
                                            <span class="hero-pin-icon"><?= $character['class_icon'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <span class="cell-icon"><?= $tile['icon'] ?></span>
                                    <span class="cell-coord"><?= $x ?>,<?= $y ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php 
                        endfor;
                    endfor; 
                    ?>
                </div>

                <!-- Boussole de navigation / D-Pad -->
                <div class="map-dpad-container">
                    <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:8px; text-align:center;">
                        🧭 Pavé directionnel (1 PA par pas) :
                    </div>
                    <div class="dpad-grid">
                        <div></div>
                        <form hx-post="/map/move" hx-target="#map-container" hx-swap="outerHTML">
                            <input type="hidden" name="direction" value="north">
                            <button type="submit" class="btn-retro dpad-btn" title="Nord" <?= ($adjacent['north'] && $adjacent['north']['is_walkable']) ? '' : 'disabled' ?>>⬆️ Nord</button>
                        </form>
                        <div></div>

                        <form hx-post="/map/move" hx-target="#map-container" hx-swap="outerHTML">
                            <input type="hidden" name="direction" value="west">
                            <button type="submit" class="btn-retro dpad-btn" title="Ouest" <?= ($adjacent['west'] && $adjacent['west']['is_walkable']) ? '' : 'disabled' ?>>⬅️ Ouest</button>
                        </form>
                        <div class="dpad-center-pin"><?= $character['class_icon'] ?></div>
                        <form hx-post="/map/move" hx-target="#map-container" hx-swap="outerHTML">
                            <input type="hidden" name="direction" value="east">
                            <button type="submit" class="btn-retro dpad-btn" title="Est" <?= ($adjacent['east'] && $adjacent['east']['is_walkable']) ? '' : 'disabled' ?>>➡️ Est</button>
                        </form>

                        <div></div>
                        <form hx-post="/map/move" hx-target="#map-container" hx-swap="outerHTML">
                            <input type="hidden" name="direction" value="south">
                            <button type="submit" class="btn-retro dpad-btn" title="Sud" <?= ($adjacent['south'] && $adjacent['south']['is_walkable']) ? '' : 'disabled' ?>>⬇️ Sud</button>
                        </form>
                        <div></div>
                    </div>
                </div>
            </div>

            <!-- COLONNE DROITE : DÉTAIL DE LA CASE ACTUELLE & ACTIONS -->
            <div class="tile-detail-column">
                <div class="panel-header-title">
                    <span>📍 Lieu Actuel</span>
                </div>

                <div class="tile-story-card">
                    <div class="tile-story-header">
                        <div class="story-icon-box"><?= $currentTile['icon'] ?></div>
                        <div>
                            <h2 style="color: var(--accent-gold); font-size: 1.35rem; margin-bottom: 3px;">
                                <?= htmlspecialchars($currentTile['name']) ?>
                            </h2>
                            <span style="font-size:0.8rem; color: var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                                Type de terrain : <?= htmlspecialchars($currentTile['tile_type']) ?>
                            </span>
                        </div>
                    </div>

                    <p class="tile-story-description">
                        <?= nl2br(htmlspecialchars($currentTile['description'])) ?>
                    </p>
                </div>

                <!-- ACTIONS CONTEXTUELLES EXCLUSIVES À LA CASE -->
                <div class="contextual-action-box">
                    <div style="font-size: 0.95rem; font-weight: bold; color: var(--accent-gold); margin-bottom: 12px;">
                        ⚡ Actions disponibles sur cette case :
                    </div>

                    <?php if ($currentTile['action_type'] === 'shop'): ?>
                        <div class="action-card-highlight">
                            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 10px;">
                                Les flammes de la forge crépitent. Le maître artisan propose des armes tranchantes et des armures blindées.
                            </p>
                            <a href="/game/shop" class="btn-retro btn-stat-plus" style="font-size: 1.05rem; padding: 10px 20px; width: 100%;">
                                🔨 <?= htmlspecialchars($currentTile['action_label'] ?? 'Ouvrir la Forge') ?>
                            </a>
                        </div>

                    <?php elseif ($currentTile['action_type'] === 'tavern'): ?>
                        <div class="action-card-highlight">
                            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 10px;">
                                Reposez-vous près du feu de cheminée pour restaurer l'intégralité de vos PV et PA.
                            </p>
                            <form method="POST" action="/game/tavern/rest" style="margin:0;">
                                <button type="submit" class="btn-retro btn-primary" style="font-size: 1.05rem; padding: 10px 20px; width: 100%;">
                                    🍺 Se Reposer à la Taverne (10 💰)
                                </button>
                            </form>
                        </div>

                    <?php elseif ($currentTile['action_type'] === 'battle_zone'): ?>
                        <div class="action-card-highlight">
                            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 10px;">
                                Des créatures sauvages et des prédateurs rôdent dans les parages. Êtes-vous prêt au combat ?
                            </p>
                            <a href="/battle/explore" class="btn-retro btn-primary" style="font-size: 1.05rem; padding: 10px 20px; width: 100%;">
                                ⚔️ <?= htmlspecialchars($currentTile['action_label'] ?? 'Traquer les monstres') ?>
                            </a>
                        </div>

                    <?php else: ?>
                        <div style="background: #110e17; border: 1px dashed #3d324f; border-radius: 6px; padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                            🌾 Vous êtes sur une voie de passage dégagée.<br>
                            <small>Déplacez-vous sur la carte pour explorer d'autres lieux, échoppes ou zones de chasse.</small>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Raccourcis utiles -->
                <div style="display:flex; gap:10px; margin-top: 20px;">
                    <a href="/game/inventory" class="btn-retro" style="flex:1; font-size:0.9rem;">🎒 Ouvrir le Sac</a>
                    <a href="/game/stats" class="btn-retro" style="flex:1; font-size:0.9rem;">📜 Fiche Héros</a>
                </div>
            </div>

        </div>
    </div>
</div>
