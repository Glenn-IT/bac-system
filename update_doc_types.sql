-- UPDATE DOCUMENT TYPES MANUALLY (Alternative to Re-importing)
-- Run this in phpMyAdmin if you want to keep existing data
-- ============================================================

-- Step 1: Delete old document types
DELETE FROM doc_types;

-- Step 2: Reset auto-increment
ALTER TABLE doc_types AUTO_INCREMENT = 1;

-- Step 3: Insert new document types
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

-- Step 4: Verify
SELECT * FROM doc_types;
-- Should show 13 rows

-- ============================================================
-- NOTES:
-- ============================================================
-- 
-- WARNING: This will DELETE all old document type references!
-- 
-- If you have existing documents uploaded with old types,
-- they will become orphaned (doc_type_id won't match anymore).
-- 
-- SAFER OPTION: Drop and re-import entire database.sql
-- 
-- ============================================================
