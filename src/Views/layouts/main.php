<?php
use Core\Session;
use Models\Character;

$userId = Session::getUserId();
$charId = Session::getCharacterId();
$activeChar = $charId ? Character::getEffectiveStats($charId) : null;
$flashes = Session::getFlashes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'RPG-Zero - Le RPG Rétro Web' ?></title>
    <link rel="stylesheet" href="/css/retro.css">
    <script src="/js/htmx.min.js"></script>
</head>
<body>
    <header class="top-nav">
        <a href="<?= $charId ? '/game/hub' : '/' ?>" class="brand-title">🗡️ RPG-Zero</a>
        <nav class="nav-links">
            <?php if ($userId): ?>
                <?php if ($activeChar): ?>
                    <a href="/game/hub">🏰 Ville</a>
                    <a href="/game/inventory">🎒 Sac & Équipement</a>
                    <a href="/battle/explore">🌲 Forêt</a>
                    <a href="/game/stats">📜 Héros</a>
                <?php else: ?>
                    <a href="/character/create">➕ Créer Héros</a>
                <?php endif; ?>
                <a href="/logout">🚪 Déconnexion</a>
            <?php else: ?>
                <a href="/login">Connexion</a>
                <a href="/register">Inscription</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php if ($activeChar): ?>
        <?php \Core\View::partial('partials/hero_strip', ['character' => $activeChar, 'oob' => false]); ?>
    <?php endif; ?>

    <main class="main-wrapper">
        <?php if (!empty($flashes)): ?>
            <div class="flash-messages">
                <?php foreach ($flashes as $type => $messages): ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="flash-alert flash-<?= htmlspecialchars($type) ?>">
                            <?= $msg ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <footer style="text-align: center; padding: 20px; font-size: 0.85rem; color: var(--text-muted);">
        RPG-Zero &copy; <?= date('Y') ?> - Hommage aux légendes du Web RPG des années 2000.
    </footer>
</body>
</html>
