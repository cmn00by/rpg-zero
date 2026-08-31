<div id="battle-container" class="retro-box" style="max-width: 820px; margin: 0 auto 30px auto;">
    <?php
    \Core\View::partial('battle/partial_combat_log', [
        'battle' => $battle,
        'character' => $character,
        'logs' => $logs,
        'is_finished' => (bool)$battle['is_finished'],
        'winner' => $battle['winner'],
        'rewards' => null,
        'summary' => $summary ?? null
    ]);
    ?>
</div>
