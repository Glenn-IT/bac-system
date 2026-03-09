<?php
require_once 'config/db.php';

// Step 1: Add category column to doc_types if it doesn't exist
$check = $conn->query("SHOW COLUMNS FROM doc_types LIKE 'category'");
if ($check->num_rows == 0) {
    $conn->query("ALTER TABLE doc_types ADD COLUMN `category` varchar(100) DEFAULT NULL AFTER `document_name`");
    echo "Added 'category' column to doc_types\n";
} else {
    echo "Column 'category' already exists\n";
}

// Step 2: Add sort_order column if not exists
$check2 = $conn->query("SHOW COLUMNS FROM doc_types LIKE 'sort_order'");
if ($check2->num_rows == 0) {
    $conn->query("ALTER TABLE doc_types ADD COLUMN `sort_order` int(11) DEFAULT 99 AFTER `category`");
    echo "Added 'sort_order' column to doc_types\n";
} else {
    echo "Column 'sort_order' already exists\n";
}

// Step 3: Insert the II. Eligibility and Technical Documents
$eligibility_docs = [
    ['Valid PHILGEPS Registration Certificate (Platinum Membership)', 'Philippine Government Electronic Procurement System Registration Certificate - Platinum Membership', 'II. Eligibility and Technical Documents', 1],
    ['DTI Registration Certification & Certificate of Accreditation', 'DTI Business Name Registration and Certificate of Accreditation', 'II. Eligibility and Technical Documents', 2],
    ["Mayor's Permit", "Valid Mayor's Permit issued by the city/municipality", 'II. Eligibility and Technical Documents', 3],
    ['Tax Clearance', 'BIR Tax Clearance Certificate', 'II. Eligibility and Technical Documents', 4],
    ['Statement of Ongoing Government and Private Contracts', 'Statement of the prospective bidder of all its ongoing government and private contracts, including contracts awarded but not yet started', 'II. Eligibility and Technical Documents', 5],
    ["Statement of Bidder's Single Largest Completed Contract (SLCC)", "Statement of the bidder's Single Largest Completed Contract (SLCC)", 'II. Eligibility and Technical Documents', 6],
    ['Bid Security', 'Bid security document (bond/check)', 'II. Eligibility and Technical Documents', 7],
    ['Conformity with the Technical Specification', 'Conformity with the Technical Specification (Production/Delivery Schedule / Manpower Requirement / Warranty Certificate and After Sales/Parts, if applicable)', 'II. Eligibility and Technical Documents', 8],
    ['Omnibus Sworn Statement (OSS)', 'Sworn statement on eligibility requirements', 'II. Eligibility and Technical Documents', 9],
];

$financial_docs = [
    ['Financial Bid Form', 'Original of duly signed and accomplished Financial Bid Form', 'III. Financial Documents', 1],
    ['Price Schedule(s)', 'Original of duly signed and accomplished Price Schedule(s)', 'III. Financial Documents', 2],
];

$stmt = $conn->prepare("INSERT INTO doc_types (document_name, category, sort_order, description, is_required) VALUES (?, ?, ?, ?, 1)");

$inserted = 0;
$skipped = 0;

foreach (array_merge($eligibility_docs, $financial_docs) as $doc) {
    // Check if already exists with same name and category
    $check = $conn->prepare("SELECT id FROM doc_types WHERE document_name = ? AND category = ?");
    $check->bind_param("ss", $doc[0], $doc[2]);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $stmt->bind_param("ssis", $doc[0], $doc[2], $doc[3], $doc[1]);
        if ($stmt->execute()) {
            echo "Inserted: [{$doc[2]}] {$doc[0]}\n";
            $inserted++;
        } else {
            echo "Error inserting {$doc[0]}: " . $conn->error . "\n";
        }
    } else {
        echo "Skipped (exists): {$doc[0]}\n";
        $skipped++;
    }
    $check->close();
}

$stmt->close();

echo "\nDone! Inserted: $inserted, Skipped: $skipped\n";

// Show final state
echo "\n--- Final doc_types ---\n";
$result = $conn->query("SELECT id, document_name, category, sort_order FROM doc_types ORDER BY COALESCE(category, 'ZZZ'), sort_order, document_name");
while ($row = $result->fetch_assoc()) {
    echo $row['id'] . ' | [' . ($row['category'] ?? 'No Category') . '] ' . $row['document_name'] . "\n";
}
