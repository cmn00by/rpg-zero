-- Schema initial RPG-Zero
CREATE DATABASE IF NOT EXISTS `rpg_zero` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rpg_zero`;

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

DROP TABLE IF EXISTS `battle_logs`;
DROP TABLE IF EXISTS `active_battles`;
DROP TABLE IF EXISTS `characters`;
DROP TABLE IF EXISTS `monsters`;
DROP TABLE IF EXISTS `character_classes`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `character_classes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(30) NOT NULL UNIQUE,
    `name` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `icon` VARCHAR(20) NOT NULL,
    `base_hp` INT NOT NULL DEFAULT 100,
    `base_ap` INT NOT NULL DEFAULT 20,
    `base_str` INT NOT NULL DEFAULT 10,
    `base_agi` INT NOT NULL DEFAULT 10,
    `base_int` INT NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `characters` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `class_id` INT NOT NULL,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `level` INT NOT NULL DEFAULT 1,
    `xp` INT NOT NULL DEFAULT 0,
    `xp_next` INT NOT NULL DEFAULT 100,
    `gold` INT NOT NULL DEFAULT 50,
    `current_hp` INT NOT NULL DEFAULT 100,
    `max_hp` INT NOT NULL DEFAULT 100,
    `current_ap` INT NOT NULL DEFAULT 20,
    `max_ap` INT NOT NULL DEFAULT 20,
    `strength` INT NOT NULL DEFAULT 10,
    `agility` INT NOT NULL DEFAULT 10,
    `intelligence` INT NOT NULL DEFAULT 10,
    `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`class_id`) REFERENCES `character_classes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `monsters` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `level` INT NOT NULL DEFAULT 1,
    `hp` INT NOT NULL DEFAULT 50,
    `attack` INT NOT NULL DEFAULT 8,
    `defense` INT NOT NULL DEFAULT 2,
    `agility` INT NOT NULL DEFAULT 5,
    `xp_reward` INT NOT NULL DEFAULT 25,
    `gold_reward_min` INT NOT NULL DEFAULT 5,
    `gold_reward_max` INT NOT NULL DEFAULT 15,
    `icon` VARCHAR(20) NOT NULL,
    `zone` VARCHAR(50) NOT NULL DEFAULT 'forest'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `active_battles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `character_id` INT NOT NULL,
    `monster_id` INT NOT NULL,
    `monster_current_hp` INT NOT NULL,
    `turn` INT NOT NULL DEFAULT 1,
    `is_finished` TINYINT(1) NOT NULL DEFAULT 0,
    `winner` VARCHAR(20) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`monster_id`) REFERENCES `monsters`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `battle_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `battle_id` INT NOT NULL,
    `turn` INT NOT NULL,
    `actor` VARCHAR(20) NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `damage` INT DEFAULT 0,
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`battle_id`) REFERENCES `active_battles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
