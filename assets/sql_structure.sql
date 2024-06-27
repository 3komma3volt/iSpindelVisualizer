CREATE TABLE spindles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    spindle_id INT NOT NULL UNIQUE,
    spindle_key VARCHAR(255) NOT NULL,
    alias VARCHAR(255) NOT NULL
);

CREATE TABLE spindle_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    spindle_id INT NOT NULL,
    angle FLOAT NOT NULL,
    temperature FLOAT NOT NULL,
    temp_units CHAR(1) NOT NULL,
    battery FLOAT NOT NULL,
    gravity FLOAT NOT NULL,
    update_interval INT NOT NULL,
    rssi INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);