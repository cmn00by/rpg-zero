<?php
$isOob = $oob ?? false;
$effHp = $character['effective_max_hp'] ?? $character['max_hp'];
$effAp = $character['effective_max_ap'] ?? $character['max_ap'];
$totAtk = $character['total_attack'] ?? $character['strength'];
$totDef = $character['total_defense'] ?? (int)floor(($character['strength'] ?? 10) * 0.4);
?>
<div id="hero-status-strip" <?= $isOob ? 'hx-swap-oob="true"' : '' ?> class="hero-status-strip">
    <div class="status-badge">
        <span><?= $character['class_icon'] ?> <strong><?= htmlspecialchars($character['name']) ?></strong> (Niv. <?= $character['level'] ?> - <em><?= htmlspecialchars($character['title'] ?? 'Novice') ?></em>)</span>
    </div>
    <div class="status-badge" style="min-width: 140px;">
        <span>❤️ PV:</span>
        <div class="progress-bar-container" style="display:inline-block; vertical-align:middle; width:90px;">
            <div class="progress-bar-fill hp" style="width: <?= min(100, round(($character['current_hp'] / max(1, $effHp)) * 100)) ?>%;"></div>
            <span class="progress-text"><?= $character['current_hp'] ?>/<?= $effHp ?></span>
        </div>
    </div>
    <div class="status-badge" style="min-width: 140px;">
        <span>⚡ PA:</span>
        <div class="progress-bar-container" style="display:inline-block; vertical-align:middle; width:90px;">
            <div class="progress-bar-fill ap" style="width: <?= min(100, round(($character['current_ap'] / max(1, $effAp)) * 100)) ?>%;"></div>
            <span class="progress-text"><?= $character['current_ap'] ?>/<?= $effAp ?></span>
        </div>
    </div>
    <div class="status-badge" style="min-width: 140px;">
        <span>✨ XP:</span>
        <div class="progress-bar-container" style="display:inline-block; vertical-align:middle; width:90px;">
            <div class="progress-bar-fill xp" style="width: <?= min(100, round(($character['xp'] / max(1, $character['xp_next'])) * 100)) ?>%;"></div>
            <span class="progress-text"><?= $character['xp'] ?>/<?= $character['xp_next'] ?></span>
        </div>
    </div>
    <div class="status-badge">
        <span>⚔️ Atk: <strong><?= $totAtk ?></strong></span>
    </div>
    <div class="status-badge">
        <span>🛡️ Def: <strong><?= $totDef ?></strong></span>
    </div>
    <div class="status-badge">
        <span style="color: var(--accent-gold);">💰 <strong><?= $character['gold'] ?></strong> pièces</span>
    </div>
</div>
