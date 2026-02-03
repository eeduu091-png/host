#!/usr/bin/env python3
"""
Import Employees from Excel to Database
Reads employee data from Excel file and imports to MySQL database
"""

import pandas as pd
import mysql.connector
from mysql.connector import Error
import uuid
import sys
from datetime import datetime

# Database configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'maayash_id_system'
}

EXCEL_FILE = '../upload/MERU TERRITORRYDECEMBER  BAS PAYMENT PAYROLL.xlsx'

def generate_uuid():
    """Generate a UUID string"""
    return str(uuid.uuid4())

def get_db_connection():
    """Create database connection"""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        return conn
    except Error as e:
        print(f"Database connection error: {e}", file=sys.stderr)
        sys.exit(1)

def employee_exists(cursor, employee_id):
    """Check if employee ID already exists"""
    query = "SELECT COUNT(*) FROM Employee WHERE employeeId = %s"
    cursor.execute(query, (employee_id,))
    result = cursor.fetchone()
    return result[0] > 0

def split_name(full_name):
    """Split full name into first and last name"""
    parts = full_name.strip().split()
    if len(parts) == 1:
        return parts[0], ''
    elif len(parts) == 2:
        return parts[0], parts[1]
    else:
        return parts[0], ' '.join(parts[1:])

def import_employees():
    """Import employees from Excel file"""
    try:
        # Read Excel file
        print(f"Reading Excel file: {EXCEL_FILE}")
        df = pd.read_excel(EXCEL_FILE)

        print(f"Found {len(df)} rows in Excel file")
        print(f"Columns: {list(df.columns)}")
        print()

        # Connect to database
        conn = get_db_connection()
        cursor = conn.cursor()

        # Statistics
        imported = 0
        skipped = 0
        errors = []

        # Import each employee
        for index, row in df.iterrows():
            try:
                # Map Excel columns to database fields
                phone = str(row['PHONE NUMBER']).strip()
                employee_id = str(row['ID NUMBER']).strip()
                full_name = str(row['NAME']).strip()
                team = str(row['TEAM']).strip() if pd.notna(row['TEAM']) else ''
                territory = str(row['TERRITORY']).strip() if pd.notna(row['TERRITORY']) else ''
                region = str(row['REGION']).strip() if pd.notna(row['REGION']) else ''
                comment = str(row['COMMENT']).strip() if pd.notna(row['COMMENT']) else ''

                # Skip if required fields are missing
                if not phone or not employee_id or not full_name:
                    errors.append(f"Row {index + 2}: Missing required fields")
                    continue

                # Split name into first and last
                first_name, last_name = split_name(full_name)

                # Check if employee already exists
                if employee_exists(cursor, employee_id):
                    skipped += 1
                    print(f"⊘ Skipped (duplicate ID): {employee_id} - {full_name}")
                    continue

                # Insert employee
                query = """
                    INSERT INTO Employee (id, employeeId, firstName, lastName, phone, role, region, department, site, isActive, createdAt)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, 1, NOW())
                """
                values = (
                    generate_uuid(),
                    employee_id,
                    first_name,
                    last_name,
                    phone,
                    team,  # Use TEAM as role
                    region,
                    territory,  # Use TERRITORY as department
                    comment  # Use COMMENT as site
                )

                cursor.execute(query, values)
                imported += 1
                print(f"✓ Imported: {employee_id} - {full_name}")

            except Exception as e:
                errors.append(f"Row {index + 2}: {str(e)}")
                print(f"✗ Error at row {index + 2}: {e}", file=sys.stderr)

        # Commit changes
        conn.commit()

        # Print summary
        print()
        print("=" * 60)
        print("IMPORT SUMMARY")
        print("=" * 60)
        print(f"Total rows processed: {len(df)}")
        print(f"Successfully imported: {imported}")
        print(f"Skipped (duplicates): {skipped}")
        print(f"Errors: {len(errors)}")
        print("=" * 60)

        if errors:
            print("\nErrors encountered:")
            for error in errors[:10]:  # Show first 10 errors
                print(f"  • {error}")
            if len(errors) > 10:
                print(f"  ... and {len(errors) - 10} more errors")

        # Close connection
        cursor.close()
        conn.close()

        print("\nImport completed successfully!")

    except FileNotFoundError:
        print(f"Error: Excel file not found: {EXCEL_FILE}", file=sys.stderr)
        sys.exit(1)
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == '__main__':
    import_employees()
