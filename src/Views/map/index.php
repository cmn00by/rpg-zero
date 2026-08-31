<div id="map-container" class="retro-box">
    <?php \Core\View::partial('map/partial_map', [
        'character' => $character,
        'zone' => $zone,
        'tiles' => $tiles,
        'currentTile' => $currentTile,
        'adjacent' => $adjacent
    ]); ?>
</div>
