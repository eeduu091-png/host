# Quick Reference - Updated Features

## Admin Panel New Capabilities

### 1. Edit Employees
**Location:** Admin Dashboard → Employees Tab

**How to Use:**
1. Find employee in the table
2. Click "Edit" button
3. Enter new phone number (or other field to edit)
4. Click OK

**What it does:**
- Updates employee information in database
- Changes are immediate
- No page refresh needed

### 2. Generate Download Codes with Controls
**Location:** Admin Dashboard → Download Codes Tab

**How to Use:**
1. Click "+ Generate Code" button
2. Set **Maximum Uses**: How many times code can be used
   - Example: 1 = one-time use, 5 = can be used 5 times
3. Set **Expires At** (optional): When code expires
   - Leave empty for no expiration
   - Select date/time for temporary codes
4. Click "Generate"
5. **IMPORTANT:** Copy the code - it's only shown once!

**Example Use Cases:**
- One-time use: Set Max Uses = 1 (for individual employees)
- Team use: Set Max Uses = 10 (for small team)
- Event use: Set Max Uses = 50, Expires = event end date

### 3. Reactivate Download Codes
**Location:** Admin Dashboard → Download Codes Tab

**When to Use:**
- Code was used up and you want to reuse it
- Code was deactivated and you want to enable it again
- Code expired but you want to extend validity

**How to Use:**
1. Find the code (shows "Inactive" or "fully used")
2. Click "Reactivate" button
3. Enter new max uses (or keep current)
4. Click OK

**Result:**
- Used count resets to 0
- Code becomes active
- Can be used again immediately

## Employee Portal Payment Details

### What Users See

When users select M-Pesa payment, they now see:

```
Payment Details:
• Amount: KES 50
• Till Number: 6604923
• Till Name: BUY GOODS GREEN COLOR NETWORKS
```

### Payment Flow

1. User enters phone number (format: 07XXXXXXXX)
2. User clicks "Pay KES 50" button
3. STK Push sent to their phone
4. User enters M-Pesa PIN on their phone
5. System waits for confirmation (up to 30 seconds)
6. **✓ Payment Successful** → ID card generates automatically
7. **✗ Payment Failed** → User sees error, can retry

### Important Notes

- **Users CANNOT download ID card until payment is successful**
- System enforces this at the API level
- No workaround possible
- All downloads are logged

## Download Code Alternative

### User Flow with Code

1. User selects "Download Code" option
2. User enters 8-character code (format: AB12-CD34)
3. System validates:
   - ✓ Code exists in database
   - ✓ Code is active
   - ✓ Code not expired
   - ✓ Code has remaining uses
4. **✓ Code Valid** → ID card generates immediately
5. **✗ Code Invalid** → User sees error message

### Important Notes

- **Users CANNOT download ID card without valid code**
- Each use decrements the usage count
- When max uses reached, code becomes "fully used"
- Admin must reactivate to reuse code

## Importing Employees

### Quick Import

```bash
cd /home/z/my-project/setup
python3 import_employees.py
```

### What It Does

- Reads from: `upload/MERU TERRITORRYDECEMBER  BAS PAYMENT PAYROLL.xlsx`
- Imports 73 employees
- Maps columns automatically:
  - PHONE NUMBER → phone
  - ID NUMBER → employeeId
  - NAME → firstName + lastName
  - TEAM → role
  - TERRITORY → department
  - REGION → region
  - COMMENT → site
- Skips duplicates
- Shows detailed progress

### Expected Output

```
✓ Imported: 29843215 - Employee Name 1
✓ Imported: 34158624 - Employee Name 2
...
============================================================
IMPORT SUMMARY
============================================================
Total rows processed: 73
Successfully imported: 73
Skipped (duplicates): 0
Errors: 0
============================================================
```

## Common Scenarios

### Scenario 1: Employee Lost ID Card

**Solution:** Admin generates one-time use code
- Max Uses = 1
- No expiration
- Give code to employee
- Employee enters code and downloads new ID

### Scenario 2: Team of 10 Need IDs

**Solution:** Admin generates team code
- Max Uses = 10
- Set expiration (e.g., end of week)
- Share single code with team
- Each team member uses it once

### Scenario 3: Code Used Up Too Quickly

**Solution:** Admin reactivates with higher limit
- Find the code in Download Codes tab
- Click "Reactivate"
- Increase Max Uses (e.g., from 1 to 5)
- Code now active again

### Scenario 4: Employee Changed Phone Number

**Solution:** Admin updates employee record
- Find employee in Employees tab
- Click "Edit"
- Enter new phone number
- Save

### Scenario 5: Bulk Upload New Employees

**Solution:** Import from Excel
- Prepare Excel with required columns
- Place in upload folder
- Run import script
- All employees imported automatically

## Security Checklist

✅ Admin authentication required for all admin actions
✅ Payment verified before ID card generation
✅ Code validated before ID card generation
✅ All downloads logged in database
✅ Duplicate employee IDs prevented
✅ SQL injection protection (prepared statements)
✅ XSS protection (output escaping)

## Troubleshooting

### Payment Stuck on "Waiting for confirmation"

**Cause:** Payment not completed or timeout

**Solution:**
- User should complete payment on their phone
- Wait up to 30 seconds
- If timeout, try payment again
- Use download code as alternative

### Code Shows "Invalid download code"

**Causes:**
- Code doesn't exist
- Code was deactivated
- Code expired
- Code reached max uses

**Solutions:**
- Check code spelling (format: AB12-CD34)
- Contact admin to verify code status
- Admin can reactivate code if needed

### Import Script Fails

**Common Issues:**
1. Database not running
2. Wrong database credentials
3. Excel file not found
4. MySQL connector not installed

**Solutions:**
```bash
# Check MySQL is running
sudo systemctl status mysql

# Install Python MySQL connector
pip3 install mysql-connector-python pandas openpyxl

# Update database credentials in import script
# Edit: DB_CONFIG in import_employees.py
```

## Support

For issues or questions:
1. Check this reference guide
2. Review UPDATES_SUMMARY.md for detailed changes
3. Check README.md for full documentation
4. Contact system administrator

## Quick Commands

```bash
# Import employees
cd /home/z/my-project/setup && python3 import_employees.py

# Create database and tables
mysql -u root -p < database/schema.sql

# Seed admin accounts
php api/seed.php

# Start development server
cd public && php -S localhost:8000

# Check database
mysql -u root -p maayash_id_system
SELECT COUNT(*) FROM Employee;
SELECT * FROM DownloadCode;
```

---

**Last Updated:** 2025-01-18
**System Version:** 2.0
