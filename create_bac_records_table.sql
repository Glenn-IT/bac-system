-- Create BAC Records table (separate from suppliers)
CREATE TABLE IF NOT EXISTS bac_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bac_cod VARCHAR(50) NOT NULL UNIQUE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create index for better performance
CREATE INDEX idx_bac_cod ON bac_records(bac_cod);
CREATE INDEX idx_created_at ON bac_records(created_at);
