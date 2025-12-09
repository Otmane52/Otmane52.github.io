-- Database schema for event redemption
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    total_seats INT NOT NULL,
    used_seats INT NOT NULL DEFAULT 0,
    status ENUM('active','full') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `keys` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    key_code VARCHAR(64) NOT NULL UNIQUE,
    is_used TINYINT(1) NOT NULL DEFAULT 0,
    used_by VARCHAR(150) DEFAULT NULL,
    used_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_keys_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed events
INSERT INTO events (name, description, image_url, total_seats, used_seats, status) VALUES
('Netflix', 'Premium Netflix seat redemption', 'https://via.placeholder.com/400x250?text=Netflix', 5, 1, 'active'),
('Spotify', 'Claim a Spotify Premium invite', 'https://via.placeholder.com/400x250?text=Spotify', 3, 0, 'active'),
('Disney+', 'Join the Disney+ watch party', 'https://via.placeholder.com/400x250?text=Disney%2B', 4, 2, 'active');

-- Seed keys (unused)
INSERT INTO `keys` (event_id, key_code, is_used) VALUES
(1, 'NETFLIX-001', 0),
(1, 'NETFLIX-002', 0),
(1, 'NETFLIX-003', 0),
(1, 'NETFLIX-004', 0),
(1, 'NETFLIX-005', 0),
(2, 'SPOTIFY-001', 0),
(2, 'SPOTIFY-002', 0),
(2, 'SPOTIFY-003', 0),
(3, 'DISNEY-001', 0),
(3, 'DISNEY-002', 0);
