<?php \Core\View::partial('partials/hero_strip', ['character' => $character, 'oob' => true]); ?>
<div id="inventory-container" class="retro-box" style="margin-bottom: 30px;">
    <div class="retro-box-header">
        <div style="display:flex; align-items:center; gap: 12px;">
            <span>🎒 Inventaire & Équipements</span>
            <span style="font-size: 0.9rem; color: var(--text-muted); font-family: sans-serif;">
                — <?= htmlspecialchars($character['name']) ?> (Niveau <?= $character['level'] ?>)
            </span>
        </div>
        <div style="font-size: 0.95rem; display:flex; gap: 15px; align-items:center;">
            <span style="color: var(--accent-gold);">💰 <strong><?= $character['gold'] ?></strong> pièces</span>
            <span>⚡ PA: <strong><?= $character['current_ap'] ?>/<?= $character['effective_max_ap'] ?></strong></span>
        </div>
    </div>

    <div class="retro-box-body" style="padding: 25px;">
        
        <div class="inventory-main-layout">
            
            <!-- PANNEAU GAUCHE : MANNEQUIN D'ÉQUIPEMENT (PAPERDOLL) -->
            <div class="mannequin-panel">
                <div class="panel-header-title">
                    <span>👤 Mannequin d'Équipement</span>
                </div>

                <div class="paperdoll-grid">
                    
                    <!-- LIGNE 1 : CASQUE (CENTRÉ) -->
                    <div class="paperdoll-row" style="grid-column: 1 / span 2; justify-content: center;">
                        <div class="doll-slot-card <?= $equipped['head'] ? 'equipped ' . $equipped['head']['rarity'] : 'empty' ?>" style="width: 220px;">
                            <div class="doll-slot-icon">🪖</div>
                            <div class="doll-slot-content">
                                <span class="doll-slot-label">Casque / Tête</span>
                                <?php if ($equipped['head']): ?>
                                    <strong class="doll-item-name <?= $equipped['head']['rarity'] ?>"><?= htmlspecialchars($equipped['head']['name']) ?></strong>
                                    <div class="doll-item-stats">
                                        <?php if ($equipped['head']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['head']['bonus_defense'] ?> Def</span><?php endif; ?>
                                        <?php if ($equipped['head']['bonus_hp'] > 0): ?><span>❤️ +<?= $equipped['head']['bonus_hp'] ?> PV</span><?php endif; ?>
                                        <?php if ($equipped['head']['bonus_str'] > 0): ?><span>💪 +<?= $equipped['head']['bonus_str'] ?></span><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="doll-empty-text">Emplacement libre</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($equipped['head']): ?>
                                <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                    <input type="hidden" name="slot" value="head">
                                    <button type="submit" class="btn-retro btn-unequip" title="Déséquiper">✕</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- LIGNE 2 : ARME & BOUCLIER / ARMURE -->
                    <!-- ARME -->
                    <div class="doll-slot-card <?= $equipped['weapon'] ? 'equipped ' . $equipped['weapon']['rarity'] : 'empty' ?>">
                        <div class="doll-slot-icon">🗡️</div>
                        <div class="doll-slot-content">
                            <span class="doll-slot-label">Arme Principale</span>
                            <?php if ($equipped['weapon']): ?>
                                <strong class="doll-item-name <?= $equipped['weapon']['rarity'] ?>"><?= htmlspecialchars($equipped['weapon']['name']) ?></strong>
                                <div class="doll-item-stats">
                                    <?php if ($equipped['weapon']['bonus_attack'] > 0): ?><span>⚔️ +<?= $equipped['weapon']['bonus_attack'] ?> Atk</span><?php endif; ?>
                                    <?php if ($equipped['weapon']['bonus_str'] > 0): ?><span>💪 +<?= $equipped['weapon']['bonus_str'] ?></span><?php endif; ?>
                                    <?php if ($equipped['weapon']['bonus_agi'] > 0): ?><span>🏃 +<?= $equipped['weapon']['bonus_agi'] ?></span><?php endif; ?>
                                    <?php if ($equipped['weapon']['bonus_int'] > 0): ?><span>🔮 +<?= $equipped['weapon']['bonus_int'] ?></span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="doll-empty-text">Mains nues</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['weapon']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="weapon">
                                <button type="submit" class="btn-retro btn-unequip" title="Déséquiper">✕</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- BOUCLIER -->
                    <div class="doll-slot-card <?= $equipped['shield'] ? 'equipped' : 'empty' ?>">
                        <div class="doll-slot-icon">🛡️</div>
                        <div class="doll-slot-content">
                            <span class="doll-slot-label">Bouclier / Main Gauche</span>
                            <?php if ($equipped['shield']): ?>
                                <strong class="doll-item-name <?= $equipped['shield']['rarity'] ?>"><?= htmlspecialchars($equipped['shield']['name']) ?></strong>
                                <div class="doll-item-stats">
                                    <?php if ($equipped['shield']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['shield']['bonus_defense'] ?> Def</span><?php endif; ?>
                                    <?php if ($equipped['shield']['bonus_hp'] > 0): ?><span>❤️ +<?= $equipped['shield']['bonus_hp'] ?> PV</span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="doll-empty-text">Emplacement libre</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['shield']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="shield">
                                <button type="submit" class="btn-retro btn-unequip" title="Déséquiper">✕</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- LIGNE 3 : ARMURE & BIJOU -->
                    <!-- ARMURE -->
                    <div class="doll-slot-card <?= $equipped['chest'] ? 'equipped ' . $equipped['chest']['rarity'] : 'empty' ?>">
                        <div class="doll-slot-icon">🥋</div>
                        <div class="doll-slot-content">
                            <span class="doll-slot-label">Armure / Torse</span>
                            <?php if ($equipped['chest']): ?>
                                <strong class="doll-item-name <?= $equipped['chest']['rarity'] ?>"><?= htmlspecialchars($equipped['chest']['name']) ?></strong>
                                <div class="doll-item-stats">
                                    <?php if ($equipped['chest']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['chest']['bonus_defense'] ?> Def</span><?php endif; ?>
                                    <?php if ($equipped['chest']['bonus_hp'] > 0): ?><span>❤️ +<?= $equipped['chest']['bonus_hp'] ?> PV</span><?php endif; ?>
                                    <?php if ($equipped['chest']['bonus_str'] > 0): ?><span>💪 +<?= $equipped['chest']['bonus_str'] ?></span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="doll-empty-text">Emplacement libre</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['chest']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="chest">
                                <button type="submit" class="btn-retro btn-unequip" title="Déséquiper">✕</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- BIJOU -->
                    <div class="doll-slot-card <?= $equipped['ring'] ? 'equipped ' . $equipped['ring']['rarity'] : 'empty' ?>">
                        <div class="doll-slot-icon">💍</div>
                        <div class="doll-slot-content">
                            <span class="doll-slot-label">Bijou / Anneau</span>
                            <?php if ($equipped['ring']): ?>
                                <strong class="doll-item-name <?= $equipped['ring']['rarity'] ?>"><?= htmlspecialchars($equipped['ring']['name']) ?></strong>
                                <div class="doll-item-stats">
                                    <?php if ($equipped['ring']['bonus_attack'] > 0): ?><span>⚔️ +<?= $equipped['ring']['bonus_attack'] ?> Atk</span><?php endif; ?>
                                    <?php if ($equipped['ring']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['ring']['bonus_defense'] ?> Def</span><?php endif; ?>
                                    <?php if ($equipped['ring']['bonus_hp'] > 0): ?><span>❤️ +<?= $equipped['ring']['bonus_hp'] ?> PV</span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="doll-empty-text">Emplacement libre</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['ring']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="ring">
                                <button type="submit" class="btn-retro btn-unequip" title="Déséquiper">✕</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- LIGNE 4 : BOTTES (CENTRÉ) -->
                    <div class="paperdoll-row" style="grid-column: 1 / span 2; justify-content: center;">
                        <div class="doll-slot-card <?= $equipped['boots'] ? 'equipped ' . $equipped['boots']['rarity'] : 'empty' ?>" style="width: 220px;">
                            <div class="doll-slot-icon">🥾</div>
                            <div class="doll-slot-content">
                                <span class="doll-slot-label">Bottes / Pieds</span>
                                <?php if ($equipped['boots']): ?>
                                    <strong class="doll-item-name <?= $equipped['boots']['rarity'] ?>"><?= htmlspecialchars($equipped['boots']['name']) ?></strong>
                                    <div class="doll-item-stats">
                                        <?php if ($equipped['boots']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['boots']['bonus_defense'] ?> Def</span><?php endif; ?>
                                        <?php if ($equipped['boots']['bonus_agi'] > 0): ?><span>🏃 +<?= $equipped['boots']['bonus_agi'] ?></span><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="doll-empty-text">Emplacement libre</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($equipped['boots']): ?>
                                <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                    <input type="hidden" name="slot" value="boots">
                                    <button type="submit" class="btn-retro btn-unequip" title="Déséquiper">✕</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- CUMUL DES BONUS -->
                <div class="equipment-bonus-summary">
                    <div style="color: var(--accent-gold); font-weight: bold; margin-bottom: 6px; font-size: 0.9rem;">
                        ✨ Total des Bonus d'Équipement :
                    </div>
                    <div class="bonus-tags-list">
                        <span class="bonus-tag">⚔️ Attaque : <strong>+<?= $bonuses['bonus_attack'] ?></strong></span>
                        <span class="bonus-tag">🛡️ Défense : <strong>+<?= $bonuses['bonus_defense'] ?></strong></span>
                        <span class="bonus-tag">💪 Force : <strong>+<?= $bonuses['bonus_str'] ?></strong></span>
                        <span class="bonus-tag">🏃 Agilité : <strong>+<?= $bonuses['bonus_agi'] ?></strong></span>
                        <span class="bonus-tag">🔮 Intelligence : <strong>+<?= $bonuses['bonus_int'] ?></strong></span>
                        <span class="bonus-tag">❤️ PV Max : <strong>+<?= $bonuses['bonus_hp'] ?></strong></span>
                    </div>
                </div>
            </div>

            <!-- PANNEAU DROIT : GRILLE DU SAC À DOS ÉVOLUTIF -->
            <div class="bag-panel">
                <div class="panel-header-title" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>🎒 Sac à Dos</span>
                    <span class="bag-capacity-pill">
                        <strong><?= count($bagItems) ?></strong> / <?= $character['inventory_slots'] ?> emplacements
                    </span>
                </div>

                <!-- GRILLE DE SLOTS -->
                <div class="bag-grid-layout">
                    <!-- 1. OBJETS POSSÉDÉS -->
                    <?php foreach ($bagItems as $item): ?>
                        <?php $canEquip = ((int)$character['level'] >= (int)$item['level_required']); ?>
                        <div class="bag-slot-tile occupied <?= $item['rarity'] ?>">
                            
                            <!-- Header de l'objet (Icône + Rareté + Badge Qty) -->
                            <div class="tile-icon-wrapper">
                                <span class="tile-icon"><?= $item['icon'] ?></span>
                                <?php if ($item['quantity'] > 1): ?>
                                    <span class="tile-qty-badge">x<?= $item['quantity'] ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Nom & Type -->
                            <div class="tile-info">
                                <strong class="tile-item-name <?= $item['rarity'] ?>">
                                    <?= htmlspecialchars($item['name']) ?>
                                </strong>
                                
                                <!-- Niveau requis si > 1 -->
                                <?php if ($item['level_required'] > 1): ?>
                                    <?php if ($canEquip): ?>
                                        <div class="tile-level-badge met">Niv. <?= $item['level_required'] ?></div>
                                    <?php else: ?>
                                        <div class="tile-level-badge locked">🔒 Requis Niv. <?= $item['level_required'] ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Statistiques clés -->
                                <div class="tile-stats-box">
                                    <?php if ($item['bonus_attack'] > 0): ?><span>⚔️ +<?= $item['bonus_attack'] ?> Atk</span><?php endif; ?>
                                    <?php if ($item['bonus_defense'] > 0): ?><span>🛡️ +<?= $item['bonus_defense'] ?> Def</span><?php endif; ?>
                                    <?php if ($item['bonus_str'] > 0): ?><span>💪 +<?= $item['bonus_str'] ?></span><?php endif; ?>
                                    <?php if ($item['bonus_agi'] > 0): ?><span>🏃 +<?= $item['bonus_agi'] ?></span><?php endif; ?>
                                    <?php if ($item['bonus_int'] > 0): ?><span>🔮 +<?= $item['bonus_int'] ?></span><?php endif; ?>
                                    <?php if ($item['heal_hp'] > 0): ?><span>❤️ +<?= $item['heal_hp'] ?> PV</span><?php endif; ?>
                                    <?php if ($item['restore_ap'] > 0): ?><span>⚡ +<?= $item['restore_ap'] ?> PA</span><?php endif; ?>
                                </div>
                            </div>

                            <!-- Boutons d'Action -->
                            <div class="tile-actions-row">
                                <?php if ($item['type'] === 'consumable'): ?>
                                    <form hx-post="/inventory/use" hx-target="#inventory-container" hx-swap="outerHTML" style="flex:1; margin:0;">
                                        <input type="hidden" name="character_item_id" value="<?= $item['character_item_id'] ?>">
                                        <button type="submit" class="btn-retro btn-primary btn-tile-action">
                                            Utiliser 🧪
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form hx-post="/inventory/equip" hx-target="#inventory-container" hx-swap="outerHTML" style="flex:1; margin:0;">
                                        <input type="hidden" name="character_item_id" value="<?= $item['character_item_id'] ?>">
                                        <?php if ($canEquip): ?>
                                            <button type="submit" class="btn-retro btn-stat-plus btn-tile-action">
                                                Équiper 🛡️
                                            </button>
                                        <?php else: ?>
                                            <button type="button" disabled class="btn-retro btn-tile-action btn-disabled">
                                                🔒 Niv. <?= $item['level_required'] ?>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>

                                <form hx-post="/inventory/sell" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                    <input type="hidden" name="character_item_id" value="<?= $item['character_item_id'] ?>">
                                    <button type="submit" class="btn-retro btn-sell-action" title="Vendre cet objet pour <?= $item['sell_price'] * $item['quantity'] ?> pièces d'or" onclick="return confirm('Vendre <?= htmlspecialchars($item['name']) ?> pour <?= $item['sell_price'] * $item['quantity'] ?> or ?');">
                                        💰 <?= $item['sell_price'] * $item['quantity'] ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- 2. SLOTS LIBRES DÉBLOQUÉS -->
                    <?php 
                    $emptySlotsCount = max(0, (int)$character['inventory_slots'] - count($bagItems));
                    for ($i = 0; $i < $emptySlotsCount; $i++): 
                    ?>
                        <div class="bag-slot-tile empty-unlocked">
                            <span class="empty-slot-plus">+</span>
                            <span class="empty-slot-text">Libre</span>
                        </div>
                    <?php endfor; ?>

                    <!-- 3. SLOTS VERROUILLÉS (PROCHAINES MONTÉES DE NIVEAU) -->
                    <?php for ($lvl = (int)$character['level'] + 1; $lvl <= min(20, (int)$character['level'] + 3); $lvl++): ?>
                        <div class="bag-slot-tile slot-locked">
                            <span class="lock-icon">🔒</span>
                            <span class="lock-text">Niveau <?= $lvl ?></span>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- BANDEAU D'AIDE ET D'EXPLORATION -->
                <div class="bag-footer-banner">
                    <div style="font-size:0.85rem; color: var(--text-muted);">
                        💡 <em>Chaque passage de niveau débloque automatiquement de nouveaux emplacements de sac.</em>
                    </div>
                    <a href="/battle/explore" class="btn-retro btn-primary" style="font-size:0.85rem; padding: 6px 14px;">
                        Partir chasser du butin 🌲
                    </a>
                </div>
            </div>

        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="/game/hub" class="btn-retro" style="font-size: 1.05rem; padding: 8px 24px;">
                🏰 Retourner à la Ville
            </a>
        </div>
    </div>
</div>
