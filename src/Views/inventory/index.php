<div id="inventory-container" class="retro-box">
    <?php \Core\View::partial('inventory/partial_inventory', [
        'character' => $character,
        'bagItems' => $bagItems,
        'equipped' => $equipped,
        'bonuses' => $bonuses
    ]); ?>
</div>
