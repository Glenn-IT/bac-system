-- Add BAC COD column to suppliers table
ALTER TABLE suppliers 
ADD COLUMN bac_cod VARCHAR(50) NULL AFTER id,
ADD UNIQUE KEY unique_bac_cod (bac_cod);

-- Create index for better search performance
CREATE INDEX idx_bac_cod ON suppliers(bac_cod);
