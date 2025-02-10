-- Add index for slug column in matches table
ALTER TABLE `matches` ADD INDEX `idx_slug` (`slug`);

-- Make sure slug column allows NULL for existing records
ALTER TABLE `matches` MODIFY `slug` varchar(255) NULL;

-- Create admin_users table if not exists
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `email` varchar(100) DEFAULT NULL,
    `last_login` datetime DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert admin user with password: Parola12!34
INSERT INTO `admin_users` (`username`, `password`, `email`) VALUES 
('admin', '$2y$10$2Dz2hHxJ0QJELHCsqKhDLOq7rHHGz7nGarP0k7z9RIeZGqaEhBz1K', 'admin@streamthunder.com');

-- Add login attempts table for security
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ip_address` varchar(45) NOT NULL,
    `attempt_time` datetime NOT NULL,
    `username` varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `ip_time_index` (`ip_address`, `attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;