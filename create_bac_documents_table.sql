-- Create BAC Documents table (separate from eligibility_docs)
CREATE TABLE IF NOT EXISTS bac_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bac_record_id INT NOT NULL,
    doc_type_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    issued_date DATE NULL,
    expiry_date DATE NULL,
    status ENUM('Valid', 'Expired', 'For Renewal', 'Missing') DEFAULT 'Missing',
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bac_record_id) REFERENCES bac_records(id) ON DELETE CASCADE,
    FOREIGN KEY (doc_type_id) REFERENCES doc_types(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    UNIQUE KEY unique_bac_doc (bac_record_id, doc_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create index for better performance
CREATE INDEX idx_bac_record ON bac_documents(bac_record_id);
CREATE INDEX idx_status_bac ON bac_documents(status);
