<div id="inventory-container" class="retro-box">
    <div class="retro-box-header">
        <span>🎒 Sac & Équipement de <?= htmlspecialchars($character['name']) ?></span>
        <span>Niveau <?= $character['level'] ?> &bull; 💰 <?= $character['gold'] ?> pièces</span>
    </div>
    <div class="retro-box-body">
        
        <div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 25px;">
            
            <!-- COLONNE GAUCHE : MANNEQUIN D'ÉQUIPEMENT (6 SLOTS) -->
            <div>
                <h3 style="color: var(--accent-gold); margin-bottom: 12px; font-size: 1.15rem; display:flex; align-items:center; gap: 8px;">
                    <span>👤 Équipement Actif</span>
                </h3>

                <div class="equipment-mannequin-grid">
                    
                    <!-- 1. TÊTE -->
                    <div class="equipment-slot-card <?= $equipped['head'] ? 'equipped' : 'empty' ?>">
                        <div class="slot-icon-badge">🪖</div>
                        <div class="slot-info">
                            <span class="slot-title">Casque / Tête</span>
                            <?php if ($equipped['head']): ?>
                                <strong class="item-name <?= $equipped['head']['rarity'] ?>"><?= htmlspecialchars($equipped['head']['name']) ?></strong>
                                <div class="item-bonus-tags">
                                    <?php if ($equipped['head']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['head']['bonus_defense'] ?></span><?php endif; ?>
                                    <?php if ($equipped['head']['bonus_hp'] > 0): ?><span>❤️ +<?= $equipped['head']['bonus_hp'] ?></span><?php endif; ?>
                                    <?php if ($equipped['head']['bonus_str'] > 0): ?><span>💪 +<?= $equipped['head']['bonus_str'] ?></span><?php endif; ?>
                                    <?php if ($equipped['head']['bonus_agi'] > 0): ?><span>🏃 +<?= $equipped['head']['bonus_agi'] ?></span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="slot-empty-label">Emplacement libre</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['head']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="head">
                                <button type="submit" class="btn-retro" style="font-size:0.75rem; padding: 3px 6px;" title="Ranger dans le sac">Déséquiper</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- 2. TORSE -->
                    <div class="equipment-slot-card <?= $equipped['chest'] ? 'equipped' : 'empty' ?>">
                        <div class="slot-icon-badge">🥋</div>
                        <div class="slot-info">
                            <span class="slot-title">Armure / Torse</span>
                            <?php if ($equipped['chest']): ?>
                                <strong class="item-name <?= $equipped['chest']['rarity'] ?>"><?= htmlspecialchars($equipped['chest']['name']) ?></strong>
                                <div class="item-bonus-tags">
                                    <?php if ($equipped['chest']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['chest']['bonus_defense'] ?></span><?php endif; ?>
                                    <?php if ($equipped['chest']['bonus_hp'] > 0): ?><span>❤️ +<?= $equipped['chest']['bonus_hp'] ?></span><?php endif; ?>
                                    <?php if ($equipped['chest']['bonus_str'] > 0): ?><span>💪 +<?= $equipped['chest']['bonus_str'] ?></span><?php endif; ?>
                                    <?php if ($equipped['chest']['bonus_agi'] > 0): ?><span>🏃 +<?= $equipped['chest']['bonus_agi'] ?></span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="slot-empty-label">Emplacement libre</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['chest']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="chest">
                                <button type="submit" class="btn-retro" style="font-size:0.75rem; padding: 3px 6px;" title="Ranger dans le sac">Déséquiper</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- 3. PIEDS -->
                    <div class="equipment-slot-card <?= $equipped['boots'] ? 'equipped' : 'empty' ?>">
                        <div class="slot-icon-badge">🥾</div>
                        <div class="slot-info">
                            <span class="slot-title">Bottes / Pieds</span>
                            <?php if ($equipped['boots']): ?>
                                <strong class="item-name <?= $equipped['boots']['rarity'] ?>"><?= htmlspecialchars($equipped['boots']['name']) ?></strong>
                                <div class="item-bonus-tags">
                                    <?php if ($equipped['boots']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['boots']['bonus_defense'] ?></span><?php endif; ?>
                                    <?php if ($equipped['boots']['bonus_agi'] > 0): ?><span>🏃 +<?= $equipped['boots']['bonus_agi'] ?></span><?php endif; ?>
                                    <?php if ($equipped['boots']['bonus_hp'] > 0): ?><span>❤️ +<?= $equipped['boots']['bonus_hp'] ?></span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="slot-empty-label">Emplacement libre</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['boots']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="boots">
                                <button type="submit" class="btn-retro" style="font-size:0.75rem; padding: 3px 6px;" title="Ranger dans le sac">Déséquiper</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- 4. ARME (MAIN DROITE) -->
                    <div class="equipment-slot-card <?= $equipped['weapon'] ? 'equipped' : 'empty' ?>">
                        <div class="slot-icon-badge">🗡️</div>
                        <div class="slot-info">
                            <span class="slot-title">Arme Principale</span>
                            <?php if ($equipped['weapon']): ?>
                                <strong class="item-name <?= $equipped['weapon']['rarity'] ?>"><?= htmlspecialchars($equipped['weapon']['name']) ?></strong>
                                <div class="item-bonus-tags">
                                    <?php if ($equipped['weapon']['bonus_attack'] > 0): ?><span>⚔️ +<?= $equipped['weapon']['bonus_attack'] ?> Atk</span><?php endif; ?>
                                    <?php if ($equipped['weapon']['bonus_str'] > 0): ?><span>💪 +<?= $equipped['weapon']['bonus_str'] ?></span><?php endif; ?>
                                    <?php if ($equipped['weapon']['bonus_agi'] > 0): ?><span>🏃 +<?= $equipped['weapon']['bonus_agi'] ?></span><?php endif; ?>
                                    <?php if ($equipped['weapon']['bonus_int'] > 0): ?><span>🔮 +<?= $equipped['weapon']['bonus_int'] ?></span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="slot-empty-label">Mains nues</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['weapon']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="weapon">
                                <button type="submit" class="btn-retro" style="font-size:0.75rem; padding: 3px 6px;" title="Ranger dans le sac">Déséquiper</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- 5. BOUCLIER / MAIN GAUCHE -->
                    <div class="equipment-slot-card <?= $equipped['shield'] ? 'equipped' : 'empty' ?>">
                        <div class="slot-icon-badge">🛡️</div>
                        <div class="slot-info">
                            <span class="slot-title">Bouclier / Main Gauche</span>
                            <?php if ($equipped['shield']): ?>
                                <strong class="item-name <?= $equipped['shield']['rarity'] ?>"><?= htmlspecialchars($equipped['shield']['name']) ?></strong>
                                <div class="item-bonus-tags">
                                    <?php if ($equipped['shield']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['shield']['bonus_defense'] ?></span><?php endif; ?>
                                    <?php if ($equipped['shield']['bonus_hp'] > 0): ?><span>❤️ +<?= $equipped['shield']['bonus_hp'] ?></span><?php endif; ?>
                                    <?php if ($equipped['shield']['bonus_str'] > 0): ?><span>💪 +<?= $equipped['shield']['bonus_str'] ?></span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="slot-empty-label">Emplacement libre</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['shield']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="shield">
                                <button type="submit" class="btn-retro" style="font-size:0.75rem; padding: 3px 6px;" title="Ranger dans le sac">Déséquiper</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- 6. BIJOU / ANNEAU -->
                    <div class="equipment-slot-card <?= $equipped['ring'] ? 'equipped' : 'empty' ?>">
                        <div class="slot-icon-badge">💍</div>
                        <div class="slot-info">
                            <span class="slot-title">Bijou / Anneau</span>
                            <?php if ($equipped['ring']): ?>
                                <strong class="item-name <?= $equipped['ring']['rarity'] ?>"><?= htmlspecialchars($equipped['ring']['name']) ?></strong>
                                <div class="item-bonus-tags">
                                    <?php if ($equipped['ring']['bonus_attack'] > 0): ?><span>⚔️ +<?= $equipped['ring']['bonus_attack'] ?></span><?php endif; ?>
                                    <?php if ($equipped['ring']['bonus_defense'] > 0): ?><span>🛡️ +<?= $equipped['ring']['bonus_defense'] ?></span><?php endif; ?>
                                    <?php if ($equipped['ring']['bonus_hp'] > 0): ?><span>❤️ +<?= $equipped['ring']['bonus_hp'] ?></span><?php endif; ?>
                                    <?php if ($equipped['ring']['bonus_int'] > 0): ?><span>🔮 +<?= $equipped['ring']['bonus_int'] ?></span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="slot-empty-label">Emplacement libre</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($equipped['ring']): ?>
                            <form hx-post="/inventory/unequip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                <input type="hidden" name="slot" value="ring">
                                <button type="submit" class="btn-retro" style="font-size:0.75rem; padding: 3px 6px;" title="Ranger dans le sac">Déséquiper</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Récapitulatif des bonus de panoplie -->
                <div style="background: #110e17; border: 1px solid #3d324f; border-radius: 6px; padding: 12px; margin-top: 15px; font-size: 0.88rem;">
                    <div style="color: var(--accent-gold); font-weight: bold; margin-bottom: 6px;">✨ Cumul des Bonus d'Équipement :</div>
                    <div style="display:flex; flex-wrap:wrap; gap: 12px; color: var(--text-primary);">
                        <span>⚔️ Attaque : <strong>+<?= $bonuses['bonus_attack'] ?></strong></span>
                        <span>🛡️ Défense : <strong>+<?= $bonuses['bonus_defense'] ?></strong></span>
                        <span>💪 Force : <strong>+<?= $bonuses['bonus_str'] ?></strong></span>
                        <span>🏃 Agi : <strong>+<?= $bonuses['bonus_agi'] ?></strong></span>
                        <span>🔮 Int : <strong>+<?= $bonuses['bonus_int'] ?></strong></span>
                        <span>❤️ PV : <strong>+<?= $bonuses['bonus_hp'] ?></strong></span>
                    </div>
                </div>
            </div>

            <!-- COLONNE DROITE : LE SAC À DOS (GRILLE D'ITEMS ÉVOLUTIVE) -->
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
                    <h3 style="color: var(--accent-gold); font-size: 1.15rem;">
                        🎒 Sac à Dos <span style="font-size:0.9rem; color: var(--text-muted);">(<?= count($bagItems) ?> / <?= $character['inventory_slots'] ?> emplacements)</span>
                    </h3>
                </div>

                <div class="bag-items-list">
                    <?php if (empty($bagItems)): ?>
                        <div style="text-align:center; padding: 30px 15px; color: var(--text-muted); background: #130f1c; border: 1px dashed #3d324f; border-radius: 6px;">
                            Votre sac est vide pour le moment.<br>
                            <small>Explorez la Forêt Sombre pour piller des trésors sur les monstres !</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($bagItems as $item): ?>
                            <div class="bag-item-card <?= $item['rarity'] ?>">
                                <div class="item-icon-box"><?= $item['icon'] ?></div>
                                <div style="flex:1;">
                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <strong class="item-name <?= $item['rarity'] ?>"><?= htmlspecialchars($item['name']) ?></strong>
                                        <?php if ($item['quantity'] > 1): ?>
                                            <span class="badge-qty">x<?= $item['quantity'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p style="font-size:0.82rem; color: var(--text-muted); margin: 3px 0;"><?= htmlspecialchars($item['description']) ?></p>
                                    
                                    <div class="item-bonus-tags">
                                        <?php if ($item['bonus_attack'] > 0): ?><span>⚔️ +<?= $item['bonus_attack'] ?> Atk</span><?php endif; ?>
                                        <?php if ($item['bonus_defense'] > 0): ?><span>🛡️ +<?= $item['bonus_defense'] ?> Def</span><?php endif; ?>
                                        <?php if ($item['bonus_str'] > 0): ?><span>💪 +<?= $item['bonus_str'] ?></span><?php endif; ?>
                                        <?php if ($item['bonus_agi'] > 0): ?><span>🏃 +<?= $item['bonus_agi'] ?></span><?php endif; ?>
                                        <?php if ($item['bonus_int'] > 0): ?><span>🔮 +<?= $item['bonus_int'] ?></span><?php endif; ?>
                                        <?php if ($item['heal_hp'] > 0): ?><span>❤️ Soin +<?= $item['heal_hp'] ?> PV</span><?php endif; ?>
                                        <?php if ($item['restore_ap'] > 0): ?><span>⚡ Recharge +<?= $item['restore_ap'] ?> PA</span><?php endif; ?>
                                    </div>
                                </div>

                                <div class="item-actions-stack">
                                    <?php if ($item['type'] === 'consumable'): ?>
                                        <form hx-post="/inventory/use" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                            <input type="hidden" name="character_item_id" value="<?= $item['character_item_id'] ?>">
                                            <button type="submit" class="btn-retro btn-primary" style="font-size:0.8rem; padding: 4px 10px; width: 100%;">
                                                Consommer 🧪
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form hx-post="/inventory/equip" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                            <input type="hidden" name="character_item_id" value="<?= $item['character_item_id'] ?>">
                                            <button type="submit" class="btn-retro btn-stat-plus" style="font-size:0.8rem; padding: 4px 10px; width: 100%;" <?= ($character['level'] < $item['level_required']) ? 'disabled title="Niveau ' . $item['level_required'] . ' requis"' : '' ?>>
                                                Équiper 🛡️
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form hx-post="/inventory/sell" hx-target="#inventory-container" hx-swap="outerHTML" style="margin:0;">
                                        <input type="hidden" name="character_item_id" value="<?= $item['character_item_id'] ?>">
                                        <button type="submit" class="btn-retro" style="font-size:0.75rem; padding: 3px 8px; width: 100%;" onclick="return confirm('Vendre pour <?= $item['sell_price'] * $item['quantity'] ?> or ?');">
                                            Vendre (<?= $item['sell_price'] * $item['quantity'] ?> 💰)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Slots déblocables visualisés -->
                <div style="margin-top: 15px; padding: 10px; background: #130f1c; border-radius: 4px; border: 1px solid #2d233c; font-size: 0.85rem; color: var(--text-muted); display:flex; justify-content:space-between; align-items:center;">
                    <span>🔒 <em>Prochain emplacement de sac débloqué au niveau <?= $character['level'] + 1 ?> !</em></span>
                    <a href="/battle/explore" class="btn-retro btn-primary" style="font-size:0.8rem; padding: 4px 10px;">Partir looter 🌲</a>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 25px;">
            <a href="/game/hub" class="btn-retro">🏰 Revenir au Hub de la Ville</a>
        </div>
    </div>
</div>
