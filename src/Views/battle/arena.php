<div id="battle-container" class="retro-box">
    <?php
    \Core\View::partial('battle/partial_combat_log', [
        'battle' => $battle,
        'character' => $character,
        'logs' => $logs,
        'is_finished' => (bool)$battle['is_finished'],
        'winner' => $battle['winner'],
        'rewards' => null
    ]);
    ?>
</div>
