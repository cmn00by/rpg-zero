SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
USE `rpg_zero`;

-- Classes
INSERT INTO `character_classes` (`code`, `name`, `description`, `icon`, `base_hp`, `base_ap`, `base_str`, `base_agi`, `base_int`) VALUES
('warrior', 'Guerrier', 'Robuste combattant au corps à corps doté d''une force colossale et d''une grande résistance.', '⚔️', 120, 15, 14, 8, 6),
('rogue', 'Voleur', 'Agile et furtif, il frappe avec précision et esquive les attaques ennemies.', '🗡️', 90, 25, 8, 15, 7),
('mage', 'Mage', 'Érudit des arts mystiques capable de canaliser de puissants sortilèges destructeurs.', '🔮', 80, 20, 6, 7, 16);

-- Table des Niveaux & Récompenses de progression
INSERT INTO `levels` (`level`, `xp_required`, `stat_points_reward`, `gold_reward`, `title`) VALUES
(1, 0, 0, 0, 'Novice'),
(2, 80, 5, 30, 'Aventurier débutant'),
(3, 200, 5, 50, 'Chasseur de monstres'),
(4, 380, 5, 80, 'Combattant aguerri'),
(5, 650, 6, 120, 'Vétéran des terres sauvages'),
(6, 1000, 6, 160, 'Chevalier errant'),
(7, 1450, 6, 210, 'Champion des arènes'),
(8, 2000, 7, 270, 'Héros du royaume'),
(9, 2700, 7, 340, 'Gardien des cités'),
(10, 3600, 8, 450, 'Seigneur de guerre'),
(11, 4700, 8, 560, 'Fléau des ombres'),
(12, 6000, 8, 680, 'Maître d''armes'),
(13, 7500, 9, 820, 'Grand Conquérant'),
(14, 9300, 9, 980, 'Terreur des donjons'),
(15, 11500, 10, 1200, 'Archange de la victoire'),
(16, 14200, 10, 1450, 'Demi-Dieu'),
(17, 17500, 10, 1750, 'Titan éveillé'),
(18, 21500, 11, 2100, 'Incarnation du Destin'),
(19, 26500, 11, 2500, 'Maître de l''Infini'),
(20, 33000, 15, 3500, 'Légende Immortelle');

-- Monstres (Niveau 1 à 4)
INSERT INTO `monsters` (`name`, `level`, `hp`, `attack`, `defense`, `agility`, `xp_reward`, `gold_reward_min`, `gold_reward_max`, `icon`, `zone`) VALUES
('Rat d''égout géant', 1, 35, 6, 1, 6, 15, 2, 6, '🐀', 'forest'),
('Gobelin pillard', 1, 45, 8, 2, 8, 22, 5, 12, '👺', 'forest'),
('Loup affamé', 2, 60, 11, 3, 10, 35, 8, 18, '🐺', 'forest'),
('Bandit de grand chemin', 2, 75, 13, 4, 9, 45, 12, 25, '🦹', 'forest'),
('Squelette antique', 3, 95, 16, 6, 7, 65, 18, 35, '💀', 'forest'),
('Troll des cavernes (Mini-Boss)', 4, 160, 22, 10, 5, 120, 40, 80, '👹', 'forest');
