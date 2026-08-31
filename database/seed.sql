SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
USE `rpg_zero`;

-- Classes
INSERT INTO `character_classes` (`code`, `name`, `description`, `icon`, `base_hp`, `base_ap`, `base_str`, `base_agi`, `base_int`) VALUES
('warrior', 'Guerrier', 'Robuste combattant au corps à corps doté d''une force colossale et d''une grande résistance.', '⚔️', 120, 15, 14, 8, 6),
('rogue', 'Voleur', 'Agile et furtif, il frappe avec précision et esquive les attaques ennemies.', '🗡️', 90, 25, 8, 15, 7),
('mage', 'Mage', 'Érudit des arts mystiques capable de canaliser de puissants sortilèges destructeurs.', '🔮', 80, 20, 6, 7, 16);

-- Table des Niveaux & Récompenses de progression
INSERT INTO `levels` (`level`, `xp_required`, `stat_points_reward`, `gold_reward`, `inventory_slots_reward`, `title`) VALUES
(1, 0, 0, 0, 0, 'Novice'),
(2, 80, 5, 30, 1, 'Aventurier débutant'),
(3, 200, 5, 50, 1, 'Chasseur de monstres'),
(4, 380, 5, 80, 1, 'Combattant aguerri'),
(5, 650, 6, 120, 2, 'Vétéran des terres sauvages'),
(6, 1000, 6, 160, 1, 'Chevalier errant'),
(7, 1450, 6, 210, 1, 'Champion des arènes'),
(8, 2000, 7, 270, 1, 'Héros du royaume'),
(9, 2700, 7, 340, 1, 'Gardien des cités'),
(10, 3600, 8, 450, 2, 'Seigneur de guerre'),
(11, 4700, 8, 560, 1, 'Fléau des ombres'),
(12, 6000, 8, 680, 1, 'Maître d''armes'),
(13, 7500, 9, 820, 1, 'Grand Conquérant'),
(14, 9300, 9, 980, 1, 'Terreur des donjons'),
(15, 11500, 10, 1200, 2, 'Archange de la victoire'),
(16, 14200, 10, 1450, 1, 'Demi-Dieu'),
(17, 17500, 10, 1750, 1, 'Titan éveillé'),
(18, 21500, 11, 2100, 1, 'Incarnation du Destin'),
(19, 26500, 11, 2500, 1, 'Maître de l''Infini'),
(20, 33000, 15, 3500, 3, 'Légende Immortelle');

-- Catalogue d'Objets
INSERT INTO `items` (`code`, `name`, `type`, `rarity`, `icon`, `description`, `bonus_attack`, `bonus_defense`, `bonus_str`, `bonus_agi`, `bonus_int`, `bonus_hp`, `bonus_ap`, `heal_hp`, `restore_ap`, `buy_price`, `sell_price`, `level_required`) VALUES
-- Armes
('rusty_sword', 'Épée rouillée', 'weapon', 'common', '🗡️', 'Une lame émoussée mais encore tranchante.', 3, 0, 1, 0, 0, 0, 0, 0, 0, 15, 6, 1),
('iron_sword', 'Épée longue en fer', 'weapon', 'common', '⚔️', 'Une bonne épée forgée par un artisan local.', 7, 0, 3, 0, 0, 0, 0, 0, 0, 45, 18, 1),
('steel_dagger', 'Dague en acier trempé', 'weapon', 'common', '🗡️', 'Lame courte idéale pour les frappes sournoises.', 5, 0, 0, 4, 0, 0, 0, 0, 0, 40, 16, 1),
('apprentice_staff', 'Bâton d''apprenti', 'weapon', 'common', '🪄', 'Un bâton de chêne canalisant les flux magiques.', 4, 0, 0, 0, 4, 0, 2, 0, 0, 42, 17, 1),
('war_hammer', 'Masse de guerre lourde', 'weapon', 'rare', '🔨', 'Une arme dévastatrice capable de briser les os.', 12, 0, 6, -1, 0, 10, 0, 0, 0, 110, 45, 2),
('shadow_blade', 'Lame des ombres', 'weapon', 'rare', '🗡️', 'Forgée dans l''acier noir, rapide comme l''éclair.', 10, 0, 1, 7, 0, 0, 0, 0, 0, 125, 50, 2),
('pyromancer_orb', 'Orbe de pyromancien', 'weapon', 'rare', '🔮', 'Une sphère mystique brûlant d''une flamme éternelle.', 11, 0, 0, 0, 8, 0, 4, 0, 0, 130, 52, 2),

-- Boucliers & Main gauche
('wooden_buckler', 'Rondache en bois', 'shield', 'common', '🛡️', 'Un petit bouclier léger pour dévier les coups.', 0, 3, 0, 1, 0, 5, 0, 0, 0, 25, 10, 1),
('iron_shield', 'Écu de chevalier en fer', 'shield', 'rare', '🛡️', 'Un lourd bouclier blindé offrant une défense solide.', 0, 7, 2, -1, 0, 20, 0, 0, 0, 85, 35, 2),

-- Armures (Torse)
('tattered_robe', 'Tunique rapiécée', 'chest', 'common', '🥋', 'De simples vêtements de toile.', 0, 1, 0, 0, 0, 0, 0, 0, 0, 10, 4, 1),
('leather_armor', 'Armure de cuir bouilli', 'chest', 'common', '🥋', 'Offre une protection souple et équilibrée.', 0, 4, 1, 2, 0, 10, 0, 0, 0, 50, 20, 1),
('chainmail', 'Cotte de mailles renforcée', 'chest', 'rare', '🛡️', 'Des anneaux d''acier entrecroisés parfaits contre les tranchants.', 0, 9, 3, 0, 0, 25, 0, 0, 0, 120, 50, 2),

-- Casques (Tête)
('leather_cap', 'Capuche en cuir', 'head', 'common', '🪖', 'Protège le crâne des éclats et de la pluie.', 0, 2, 0, 1, 0, 5, 0, 0, 0, 30, 12, 1),
('iron_helm', 'Heaume de fer', 'head', 'rare', '🪖', 'Un solide casque protégeant intégralement le visage.', 0, 5, 2, 0, 0, 15, 0, 0, 0, 75, 30, 2),

-- Bottes (Pieds)
('worn_boots', 'Bottes usées', 'boots', 'common', '🥾', 'Des chaussures de marche convenables.', 0, 1, 0, 1, 0, 0, 0, 0, 0, 20, 8, 1),
('ranger_boots', 'Bottes de traqueur', 'boots', 'rare', '🥾', 'Semelles silencieuses pour se déplacer sans bruit.', 0, 3, 0, 4, 0, 10, 0, 0, 0, 70, 28, 2),

-- Anneaux / Bijoux
('copper_ring', 'Anneau de cuivre poli', 'ring', 'common', '💍', 'Un bijou simple sans propriétés magiques notables.', 0, 0, 1, 1, 1, 5, 0, 0, 0, 35, 14, 1),
('ruby_ring', 'Bague au rubis flamboyant', 'ring', 'rare', '💍', 'Le joyau pulse d''une chaleur bienfaisante.', 2, 2, 2, 2, 3, 15, 2, 0, 0, 140, 60, 2),

-- Consommables
('health_potion_minor', 'Potion de soin mineure', 'consumable', 'common', '🧪', 'Restaure 35 Points de Vie.', 0, 0, 0, 0, 0, 0, 0, 35, 0, 15, 6, 1),
('health_potion_major', 'Potion de grand soin', 'consumable', 'rare', '🧪', 'Restaure 80 Points de Vie.', 0, 0, 0, 0, 0, 0, 0, 80, 0, 40, 16, 2),
('energy_elixir', 'Élixir de vivacité', 'consumable', 'common', '⚡', 'Restaure 10 Points d''Action (PA).', 0, 0, 0, 0, 0, 0, 0, 0, 10, 20, 8, 1);

-- Monstres
INSERT INTO `monsters` (`name`, `level`, `hp`, `attack`, `defense`, `agility`, `xp_reward`, `gold_reward_min`, `gold_reward_max`, `icon`, `zone`) VALUES
('Rat d''égout géant', 1, 35, 6, 1, 6, 15, 2, 6, '🐀', 'forest'),
('Gobelin pillard', 1, 45, 8, 2, 8, 22, 5, 12, '👺', 'forest'),
('Loup affamé', 2, 60, 11, 3, 10, 35, 8, 18, '🐺', 'forest'),
('Bandit de grand chemin', 2, 75, 13, 4, 9, 45, 12, 25, '🦹', 'forest'),
('Squelette antique', 3, 95, 16, 6, 7, 65, 18, 35, '💀', 'forest'),
('Troll des cavernes (Mini-Boss)', 4, 160, 22, 10, 5, 120, 40, 80, '👹', 'forest');
