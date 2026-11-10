CREATE TABLE IF NOT EXISTS moments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(8) UNIQUE NOT NULL,
    host_key VARCHAR(9) UNIQUE NOT NULL,
    title VARCHAR(120) NOT NULL,
    category ENUM('graduation', 'wedding', 'birthday', 'farewell', 'other'),
    description TEXT,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS memories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    moment_id INT NOT NULL,
    guest_name VARCHAR(80) NOT NULL,
    audio_filename VARCHAR(255) NOT NULL,
    audio_duration INT,
    cover_filename VARCHAR(255) NOT NULL,
    stickers JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (moment_id) REFERENCES moments(id)
);
