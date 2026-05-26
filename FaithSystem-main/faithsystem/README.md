# Club Membership Manager System

A comprehensive PHP-based web application for managing club membership registration, tracking member status, and generating reports.

## Features

✅ **Member Registration** - Register new members with detailed information
✅ **Member Management** - Add, edit, view, and delete members
✅ **Status Tracking** - Track member status (Active, Inactive, Suspended, Expired)
✅ **Member Files** - Store and manage member documents
✅ **Status History** - Complete audit trail of status changes
✅ **Member List** - Search, filter, and view all members
✅ **Reporting** - Generate comprehensive membership reports
✅ **Data Export** - Export reports to CSV and PDF
✅ **Dashboard** - Real-time statistics and insights
✅ **Responsive Design** - Works on desktop and mobile devices

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP (or any PHP/MySQL environment)
- Modern web browser

## Installation Instructions

### Step 1: Extract Files
Extract the project files to `C:\xampp\htdocs\faithsystem\`

### Step 2: Create Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click on "SQL" tab
3. Copy the content from `database.sql` file
4. Paste it into the SQL query box
5. Click "Go" to execute

Alternatively, import the database:
- Click "Import" tab
- Select `database.sql` file
- Click "Go"

### Step 3: Configure Database Connection
The system uses the following default configuration in `config/config.php`:
- **Host**: localhost
- **User**: root
- **Password**: (empty)
- **Database**: club_membership

If your MySQL setup is different, update the configuration in `config/config.php`

### Step 4: Access the Application
Open your web browser and navigate to:
```
http://localhost/faithsystem/
```

## File Structure

```
faithsystem/
├── config/
│   └── config.php              # Database configuration
├── includes/
│   ├── header.php             # Navigation header
│   └── footer.php             # Footer template
├── css/
│   └── style.css              # Main stylesheet
├── js/
│   └── script.js              # JavaScript functions
├── index.php                  # Dashboard
├── register_member.php        # Member registration
├── member_list.php            # View all members
├── view_member.php            # Member details
├── edit_member.php            # Edit member information
├── delete_member.php          # Delete member
├── member_reports.php         # Generate reports
├── settings.php               # System settings
├── database.sql               # Database schema
└── README.md                  # This file
```

## Usage Guide

### Dashboard
- View member statistics at a glance
- See recently added members
- Quick access to all features

### Register Member
1. Click "Register Member" in the navigation
2. Fill in member details
3. Select membership type
4. Click "Register Member"

### Member List
1. Click "Members" to view all members
2. Use search box to find specific members
3. Filter by status and membership type
4. Click action buttons to view, edit, or delete

### Edit Member
1. Click the edit button next to a member
2. Modify member information
3. Update member status if needed
4. Provide reason for status changes
5. Click "Update Member"

### Member Reports
1. Click "Reports" to view statistics
2. Choose between Summary or Detailed reports
3. Export data as CSV or PDF
4. Print reports directly

### Member Status Tracking
- View complete status history for each member
- See when and why status changed
- Track member lifecycle

## Database Tables

### members
- id (Primary Key)
- first_name, last_name
- email (Unique)
- phone
- date_of_birth
- join_date
- address, city, state, zipcode
- status (Active, Inactive, Suspended, Expired)
- membership_type (Regular, Premium, Student, Senior)
- membership_fee
- date_created, date_updated

### member_files
- id (Primary Key)
- member_id (Foreign Key)
- file_name, file_path
- file_type, file_size
- upload_date

### status_history
- id (Primary Key)
- member_id (Foreign Key)
- old_status, new_status
- change_reason
- changed_date

### membership_types
- id (Primary Key)
- type_name
- description
- annual_fee
- benefits

## Membership Types

1. **Regular** - Standard membership (peso50/year)
2. **Premium** - Premium benefits (peso100/year)
3. **Student** - Discounted for students (peso25/year)
4. **Senior** - Special senior rate (peso30/year)

## Reports Available

### Summary Report
- Total members count
- Active/Inactive/Suspended/Expired breakdown
- Revenue statistics
- Membership type distribution

### Detailed Report
- Complete member list
- All member information
- Export to CSV/PDF
- Print capability

## Tips & Best Practices

1. **Regular Backups** - Backup your database regularly
2. **Update Status** - Keep member status current
3. **Document Changes** - Always provide reasons for status changes
4. **Export Reports** - Generate and archive monthly reports
5. **Security** - Change default database credentials in production

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in config.php
- Ensure database exists and tables are created

### Page Not Found
- Verify files are in the correct directory
- Check file permissions
- Ensure PHP is properly installed

### Missing Styles/Images
- Clear browser cache (Ctrl+F5)
- Verify all CSS and JS files are in place
- Check file paths in headers

## Support & Maintenance

- Review logs regularly
- Update PHP and MySQL when updates available
- Monitor database size
- Test backups periodically

## License

This system is provided as-is for club management purposes.

## Version

Current Version: 1.0.0
Created: 2026

---

For additional features or customization, please consult the code comments in each PHP file.

