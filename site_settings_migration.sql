-- Creare tabelă pentru setări site
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserare setări inițiale pentru site
INSERT INTO site_settings (setting_key, setting_value, description) VALUES 
('site_title', 'GETSPORTNEWS', 'Titlul principal al site-ului'),
('site_description', 'Watch live football streams in HD quality. Access to latest football matches, live scores, and streaming coverage of major leagues and tournaments.', 'Descrierea principală a site-ului'),
('meta_keywords', 'live football, football streams, soccer streams, live sports, football matches, live scores', 'Cuvinte cheie pentru SEO'),
('default_og_image', 'https://getsportnews.uk/images/og-image.jpg', 'Imagine Open Graph implicită')
ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);

-- Creare procedură pentru actualizare setări
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS update_site_setting(
    IN p_key VARCHAR(100), 
    IN p_value TEXT, 
    IN p_description VARCHAR(255)
)
BEGIN
    INSERT INTO site_settings (setting_key, setting_value, description)
    VALUES (p_key, p_value, p_description)
    ON DUPLICATE KEY UPDATE 
        setting_value = p_value,
        description = p_description;
END //
DELIMITER ;
