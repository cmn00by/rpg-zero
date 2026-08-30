SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
USE `rpg_zero`;

-- Classes
INSERT INTO `character_classes` (`code`, `name`, `description`, `icon`, `base_hp`, `base_ap`, `base_str`, `base_agi`, `base_int`) VALUES
('warrior', 'Guerrier', 'Robuste combattant au corps à corps doté d''une force colossale et d''une grande résistance.', '⚔️', 120, 15, 14, 8, 6),
('rogue', 'Voleur', 'Agile et furtif, il frappe avec précision et esquive les attaques ennemies.', '🗡️', 90, 25, 8, 15, 7),
('mage', 'Mage', 'Érudit des arts mystiques capable de canaliser de puissants sortilèges destructeurs.', '🔮', 80, 20, 6, 7, 16);

-- Monstres (Niveau 1 à 4)
INSERT INTO `monsters` (`name`, `level`, `hp`, `attack`, `defense`, `agility`, `xp_reward`, `gold_reward_min`, `gold_reward_max`, `icon`, `zone`) VALUES
('Rat d''égout géant', 1, 35, 6, 1, 6, 15, 2, 6, '🐀', 'forest'),
('Gobelin pillard', 1, 45, 8, 2, 8, 22, 5, 12, '👺', 'forest'),
('Loup affamé', 2, 60, 11, 3, 10, 35, 8, 18, '🐺', 'forest'),
('Bandit de grand chemin', 2, 75, 13, 4, 9, 45, 12, 25, '🦹', 'forest'),
('Squelette antique', 3, 95, 16, 6, 7, 65, 18, 35, '💀', 'forest'),
('Troll des cavernes (Mini-Boss)', 4, 160, 22, 10, 5, 120, 40, 80, '👹', 'forest');
