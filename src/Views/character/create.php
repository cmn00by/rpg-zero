<div class="retro-box" style="max-width: 650px; margin: 20px auto;">
    <div class="retro-box-header">
        <span>✨ Création de votre Héros</span>
    </div>
    <div class="retro-box-body">
        <form method="POST" action="/character/create">
            <div class="form-group">
                <label class="form-label" for="name">Nom du Personnage :</label>
                <input type="text" id="name" name="name" class="form-input" required minlength="3" maxlength="20" placeholder="Ex: Valerius, Elora, Thorne...">
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label class="form-label">Choisissez votre Vocation :</label>
                <div class="class-grid">
                    <?php foreach ($classes as $index => $cls): ?>
                        <label class="class-card <?= $index === 0 ? 'selected' : '' ?>">
                            <input type="radio" name="class_id" value="<?= $cls['id'] ?>" <?= $index === 0 ? 'checked' : '' ?> onchange="document.querySelectorAll('.class-card').forEach(c => c.classList.remove('selected')); this.closest('.class-card').classList.add('selected');">
                            <div style="font-size: 2.5rem;"><?= $cls['icon'] ?></div>
                            <strong style="display:block; margin: 8px 0; color: var(--accent-gold); font-size: 1.1rem;"><?= htmlspecialchars($cls['name']) ?></strong>
                            <p style="font-size: 0.85rem; color: var(--text-muted); min-height: 48px;"><?= htmlspecialchars($cls['description']) ?></p>
                            <div style="margin-top: 10px; font-size: 0.8rem; border-top: 1px solid #332942; padding-top: 8px; text-align: left;">
                                <div>❤️ PV Base : <strong><?= $cls['base_hp'] ?></strong></div>
                                <div>⚡ PA Base : <strong><?= $cls['base_ap'] ?></strong></div>
                                <div>💪 Force : <strong><?= $cls['base_str'] ?></strong></div>
                                <div>🏃 Agilité : <strong><?= $cls['base_agi'] ?></strong></div>
                                <div>🔮 Intelligence : <strong><?= $cls['base_int'] ?></strong></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: center;">
                <button type="submit" class="btn-retro btn-primary" style="font-size: 1.1rem; padding: 10px 30px;">
                    Incarner ce Héros 🗡️
                </button>
            </div>
        </form>
    </div>
</div>
