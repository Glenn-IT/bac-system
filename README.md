# BAC Eligibilities Record Keeping System

## Bids and Awards Committee (BAC) System - Based on RA 9184

A comprehensive web-based system for tracking suppliers/bidders and their eligibility documents as per Philippine procurement rules (RA 9184).

---

## 🚀 INSTALLATION INSTRUCTIONS

### Prerequisites
- **XAMPP** installed on your computer
- Web browser (Chrome, Firefox, Edge, etc.)

### Step 1: Extract Files
1. Place the entire `bac-system` folder inside `C:\xampp\htdocs\`
2. Final path should be: `C:\xampp\htdocs\bac-system\`

### Step 2: Start XAMPP
1. Open **XAMPP Control Panel**
2. Start **Apache** module
3. Start **MySQL** module

### Step 3: Create Database
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on **"Import"** tab
3. Click **"Choose File"** and select `database.sql` from the `bac-system` folder
4. Click **"Go"** button to import
5. The database `bac_system` will be created with all tables and default data

### Step 4: Access the System
1. Open your browser
2. Go to: `http://localhost/bac-system/public/`
3. You will see the login page

---

## 👤 DEFAULT USER ACCOUNTS

| Role | Username | Password |
|------|----------|----------|
| **Admin** | admin | admin123 |
| **BAC Secretariat Staff** | secretariat | secret123 |
| **BAC Committee Member** | member | member123 |
| **Auditor/COA** | auditor | audit123 |

---

## 📂 SYSTEM MODULES

### 1. **Authentication**
- Secure login/logout
- Session-based authentication
- Password hashing (bcrypt)

### 2. **Dashboard**
- Statistics overview
- Documents requiring attention
- Recent activity logs
- Quick actions

### 3. **Supplier Management**
- Add/Edit/View/Delete suppliers
- Track company information
- TIN and PhilGEPS number
- Contact information

### 4. **Document Management**
- Upload eligibility documents
- Auto-status calculation (Valid, Expired, For Renewal, Missing)
- Support for PDF, JPG, PNG files
- Document expiration tracking

### 5. **Compliance Checklist**
- RA 9184 compliance overview
- Document status by supplier
- Compliance percentage calculation
- Printable reports

### 6. **User Management** (Admin only)
- Add/Edit/Delete users
- 4 user roles with different permissions
- Password management

### 7. **Activity Logs** (Admin & Auditor)
- Complete audit trail
- User actions tracking
- IP address logging
- Filter and search capabilities

---

## 📋 BAC DOCUMENT TYPES

The system tracks the following eligibility documents:

1. ✅ PhilGEPS Registration
2. ✅ Registration Certificate
3. ✅ Mayor's Permit
4. ✅ Tax Clearance
5. ✅ Bid Security
6. ✅ Omnibus Sworn Statement
7. ✅ Audited Financial Statement
8. ✅ Net Statement Contracting Capacity
9. ✅ Resolution
10. ✅ Notice of Award
11. ✅ Performance Bond
12. ✅ Purchase Order/Contract
13. ✅ Notice to Proceed

---

## 🔐 USER ROLES & PERMISSIONS

### Admin
- Full system access
- Manage users
- Manage suppliers
- Upload/edit documents
- View activity logs
- Delete records

### BAC Secretariat Staff
- Add/update suppliers
- Upload/edit documents
- View compliance reports
- Cannot delete or manage users

### BAC Committee Member
- View suppliers
- View documents
- View compliance reports
- Read-only access

### Auditor/COA
- View all data
- View activity logs
- Read-only access
- Cannot modify any data

---

## 📌 FEATURES

✅ **Auto Document Status**
- Automatically marks documents as Valid, Expired, For Renewal, or Missing
- Based on expiration date and file upload status

✅ **File Upload**
- Secure file upload for documents
- Supports PDF, JPG, PNG formats
- Files stored in `/uploads` folder

✅ **Activity Logging**
- All actions are logged
- User, timestamp, IP address tracking
- Audit trail for compliance

✅ **Responsive Design**
- Bootstrap 5 UI
- Mobile-friendly interface
- Modern gradient design

✅ **Search & Filter**
- Search suppliers by name, TIN, PhilGEPS
- Filter documents by status, type, supplier
- Filter activity logs by user, module, date

---

## 🛠️ TECHNICAL STACK

- **Backend**: PHP 7.4+ (No framework)
- **Database**: MySQL (via phpMyAdmin)
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **Icons**: Bootstrap Icons
- **Server**: Apache (XAMPP)

---

## 📁 FOLDER STRUCTURE

```
bac-system/
├── config/
│   └── db.php              # Database configuration
├── public/
│   ├── index.php           # Login page
│   ├── dashboard.php       # Dashboard
│   └── logout.php          # Logout handler
├── suppliers/
│   ├── list.php            # List suppliers
│   ├── add.php             # Add supplier
│   ├── edit.php            # Edit supplier
│   ├── view.php            # View supplier details
│   └── delete.php          # Delete supplier
├── documents/
│   ├── list.php            # List documents
│   ├── add.php             # Upload document
│   ├── edit.php            # Edit document
│   ├── delete.php          # Delete document
│   └── compliance.php      # Compliance checklist
├── users/
│   ├── list.php            # List users
│   ├── add.php             # Add user
│   ├── edit.php            # Edit user
│   └── delete.php          # Delete user
├── logs/
│   └── activity.php        # Activity logs
├── includes/
│   ├── header.php          # HTML header
│   ├── navbar.php          # Navigation bar
│   └── footer.php          # HTML footer
├── uploads/                # Uploaded files
├── database.sql            # Database schema
└── README.md               # This file
```

---

## 🔧 CONFIGURATION

### Database Settings (config/db.php)
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bac_system');
```

### File Upload Settings
- **Allowed formats**: PDF, JPG, JPEG, PNG
- **Upload directory**: `/uploads/`
- **File naming**: `doc_{supplier_id}_{doc_type_id}_{timestamp}.{ext}`

---

## 📊 DATABASE TABLES

1. **users** - User accounts and roles
2. **suppliers** - Supplier/bidder information
3. **doc_types** - Document type definitions
4. **eligibility_docs** - Uploaded documents
5. **activity_logs** - System activity logs

---

## 🐛 TROUBLESHOOTING

### Cannot access the system?
- Make sure Apache and MySQL are running in XAMPP
- Check if you're using the correct URL: `http://localhost/bac-system/public/`

### Database connection error?
- Verify MySQL is running in XAMPP
- Check database credentials in `config/db.php`
- Import `database.sql` again if needed

### Cannot upload files?
- Check if `/uploads/` folder exists and has write permissions
- For Windows: Right-click folder → Properties → Security → Edit → Add write permission

### Session errors?
- Clear browser cache and cookies
- Restart Apache in XAMPP

---

## 📝 USAGE TIPS

1. **First Login**: Use `admin / admin123` to access the system
2. **Add Suppliers**: Go to Suppliers → Add New Supplier
3. **Upload Documents**: Go to Documents → Upload Document
4. **Check Compliance**: Go to Compliance Check → Select Supplier
5. **View Reports**: Dashboard shows overview of all documents
6. **Print Reports**: Use Print button on Compliance page

---

## 🔒 SECURITY FEATURES

- Password hashing using PHP `password_hash()`
- Prepared statements to prevent SQL injection
- Session-based authentication
- Role-based access control
- Input sanitization
- XSS protection

---

## 📞 SUPPORT

For issues or questions:
1. Check the troubleshooting section
2. Review the database.sql file for proper table structure
3. Ensure XAMPP is properly configured

---

## 📄 LICENSE

This system is developed for government procurement compliance tracking based on RA 9184 (Philippine Procurement Law).

---

## ✨ SYSTEM CREDITS

**BAC Eligibilities Record Keeping System**
Version: 1.0 MVP
Date: December 2025
Purpose: RA 9184 Compliance Tracking

---

**Developed for Philippine Government Procurement**
