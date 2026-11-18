-- ==========================================================
-- Event Management & Registration System Database Schema
-- Compatible with MySQL 5.7+ / MariaDB 10.2+ / XAMPP
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `event_management` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `event_management`;

-- --------------------------------------------------------
-- Table: events
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `event_date` DATETIME NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL DEFAULT 'General',
    `max_seats` INT NOT NULL DEFAULT 100,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: registrations
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `registrations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_registrations_event` FOREIGN KEY (`event_id`) 
        REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: admins
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL DEFAULT 'Administrator',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed Data: Simple, Easy-to-Understand Upcoming Events
-- --------------------------------------------------------
INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `location`, `category`, `max_seats`) VALUES
(1, 'Annual Sports Day 2026', 'Join us for a fun-filled day of athletic events, cricket matches, football tournaments, and indoor games. Medals and certificates for all participants!', '2026-12-10 09:00:00', 'College Sports Ground', 'Sports', 150),
(2, 'College Music & Cultural Night', 'An entertaining evening featuring live acoustic bands, classical and western dance performances, comedy skits, and delicious food stalls.', '2026-12-15 17:30:00', 'Main Campus Auditorium', 'Cultural', 300),
(3, 'Web Design & Coding Contest', 'A beginner-friendly 2-hour competition where students build a simple, creative web page using HTML, CSS, and basic JavaScript. Exciting prizes to win!', '2026-12-22 10:30:00', 'Computer Lab 2', 'Coding', 50),
(4, 'Smartphone Photography Workshop', 'Learn essential techniques for taking sharp, professional photos and editing short video reels directly from your mobile phone. No experience needed!', '2027-01-08 14:00:00', 'Seminar Hall B', 'Workshop', 60),
(5, 'Blood Donation & Free Health Camp', 'A community welfare drive offering free general health checkups, blood pressure screenings, and voluntary blood donation in association with the Red Cross.', '2027-01-15 09:30:00', 'Student Activity Center', 'Community', 200)
ON DUPLICATE KEY UPDATE 
    `title` = VALUES(`title`),
    `description` = VALUES(`description`),
    `event_date` = VALUES(`event_date`),
    `location` = VALUES(`location`),
    `category` = VALUES(`category`),
    `max_seats` = VALUES(`max_seats`);

-- --------------------------------------------------------
-- Seed Data: Default Admin User (admin / admin123)
-- --------------------------------------------------------
INSERT INTO `admins` (`id`, `username`, `password_hash`, `full_name`) VALUES
(1, 'admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'System Administrator')
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);
