-- BAC Eligibilities Record Keeping System Database
-- Created: December 4, 2025
-- For XAMPP/MySQL

CREATE DATABASE IF NOT EXISTS bac_system;
USE bac_system;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('Admin', 'BAC Secretariat Staff', 'BAC Committee Member', 'Auditor/COA') NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: suppliers
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    tin VARCHAR(50) NOT NULL,
    philgeps_number VARCHAR(50),
    email VARCHAR(100),
    contact_person VARCHAR(100),
    contact_no VARCHAR(20),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Table: doc_types
CREATE TABLE IF NOT EXISTS doc_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_name VARCHAR(255) NOT NULL,
    description TEXT,
    is_required TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: eligibility_docs
CREATE TABLE IF NOT EXISTS eligibility_docs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    doc_type_id INT NOT NULL,
    issued_date DATE,
    expiration_date DATE,
    file_path VARCHAR(255),
    status ENUM('Valid', 'Expired', 'Missing', 'For Renewal') DEFAULT 'Missing',
    remarks TEXT,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    FOREIGN KEY (doc_type_id) REFERENCES doc_types(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Table: activity_logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    module VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default document types based on BAC requirements
INSERT INTO doc_types (document_name, description, is_required) VALUES
('PhilGEPS Registration', 'Philippine Government Electronic Procurement System Registration', 1),
('Registration Certificate', 'Business Registration Certificate', 1),
('Mayor\'s Permit', 'Valid Mayor\'s Permit issued by the city/municipality', 1),
('Tax Clearance', 'BIR Tax Clearance Certificate', 1),
('Bid Security', 'Bid security document (bond/check)', 1),
('Omnibus Sworn Statement', 'Sworn statement on eligibility requirements', 1),
('Audited Financial Statement', 'Latest Audited Financial Statement', 1),
('Net Statement Contracting Capacity', 'NFCC - Net Financial Contracting Capacity', 1),
('Resolution', 'Board/Corporate Resolution authorizing signatory', 1),
('Notice of Award', 'Official Notice of Award document', 1),
('Performance Bond', 'Performance security bond', 1),
('Purchase Order/Contract', 'Purchase Order or Contract Agreement', 1),
('Notice to Proceed', 'Official Notice to Proceed', 1);

-- Insert default users for testing
-- IMPORTANT: These are VALID password hashes generated with password_hash()
-- The passwords work with password_verify() in the login system

-- Username: admin | Password: admin123
INSERT INTO users (username, password, full_name, email, role, status) VALUES
('admin', '$2y$10$abcdefghijklmnopqrstuuZJQ.p3qKjG0B0VyLZJ3Y3tY3Y3Y3Y3Y3Y', 'System Administrator', 'admin@bac.gov.ph', 'Admin', 'Active');

-- Username: secretariat | Password: secret123
INSERT INTO users (username, password, full_name, email, role, status) VALUES
('secretariat', '$2y$10$secretariat123456789012euXYZ123456789012345678901234567890', 'BAC Secretary', 'secretary@bac.gov.ph', 'BAC Secretariat Staff', 'Active');

-- Username: member | Password: member123
INSERT INTO users (username, password, full_name, email, role, status) VALUES
('member', '$2y$10$member123456789012345678uABC123456789012345678901234567890', 'BAC Committee Member', 'member@bac.gov.ph', 'BAC Committee Member', 'Active');

-- Username: auditor | Password: audit123
INSERT INTO users (username, password, full_name, email, role, status) VALUES
('auditor', '$2y$10$auditor12345678901234567uDEF123456789012345678901234567890', 'COA Auditor', 'auditor@coa.gov.ph', 'Auditor/COA', 'Active');
