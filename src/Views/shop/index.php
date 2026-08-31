<div class="retro-box">
    <div class="retro-box-header">
        <div style="display:flex; align-items:center; gap: 12px;">
            <span>🔨 La Forge de Durin</span>
            <span style="font-size:0.9rem; color: var(--text-muted); font-family:sans-serif;">Armurerie & Marchand de la Vallée</span>
        </div>
        <div style="font-size:0.95rem; color: var(--accent-gold);">
            💰 Bourse : <strong><?= (int)$character['gold'] ?></strong> pièces
        </div>
    </div>

    <div class="retro-box-body" style="padding: 25px;">
        <p style="margin-bottom: 20px; font-size: 1.05rem; line-height: 1.6; color: var(--text-muted);">
            « Bienvenue dans ma forge, voyageur ! Tout mon acier est trempé dans les feux de la montagne. Choisissez ce qu'il vous faut pour affronter les créatures de la vallée. »
        </p>

        <div class="bag-grid-layout" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
            <?php foreach ($items as $item): ?>
                <?php 
                $canAfford = ((int)$character['gold'] >= (int)$item['buy_price']); 
                $canEquipLevel = ((int)$character['level'] >= (int)$item['level_required']);
                ?>
                <div class="bag-slot-tile occupied <?= $item['rarity'] ?>" style="min-height: 230px;">
                    <div class="tile-icon-wrapper">
                        <span class="tile-icon"><?= $item['icon'] ?></span>
                    </div>

                    <div class="tile-info">
                        <strong class="tile-item-name <?= $item['rarity'] ?>">
                            <?= htmlspecialchars($item['name']) ?>
                        </strong>

                        <?php if ($item['level_required'] > 1): ?>
                            <?php if ($canEquipLevel): ?>
                                <div class="tile-level-badge met">Niv. <?= $item['level_required'] ?></div>
                            <?php else: ?>
                                <div class="tile-level-badge locked">🔒 Requis Niv. <?= $item['level_required'] ?></div>
                            <?php endif; ?>
                        <?php endif; ?>

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

                    <div class="tile-actions-row">
                        <form method="POST" action="/shop/buy" style="width: 100%; margin:0;">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <?php if ($canAfford): ?>
                                <button type="submit" class="btn-retro btn-stat-plus" style="width: 100%; font-size: 0.85rem; padding: 6px;">
                                    Acheter (<?= $item['buy_price'] ?> 💰)
                                </button>
                            <?php else: ?>
                                <button type="button" disabled class="btn-retro btn-disabled" style="width: 100%; font-size: 0.8rem; padding: 6px;">
                                    Trop cher (<?= $item['buy_price'] ?> 💰)
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center; margin-top: 30px; display:flex; justify-content:center; gap: 15px;">
            <a href="/game/map" class="btn-retro" style="font-size: 1.05rem; padding: 8px 24px;">
                🗺️ Retourner à la Carte
            </a>
            <a href="/game/inventory" class="btn-retro btn-blue" style="font-size: 1.05rem; padding: 8px 24px;">
                🎒 Vérifier mon Sac
            </a>
        </div>
    </div>
</div>
