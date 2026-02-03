# COMPREHENSIVE SITE GENERATION PROMPT
# Employee ID Card Management System for Maayash Communications (Safaricom Contractor)

---

## PROJECT OVERVIEW

Create a professional employee ID card generation and management website for Maayash Communications, a Safaricom contractor. The system allows employees to generate and download their work ID cards by either paying KES 50 via M-Pesa STK push or using a valid admin-generated download code. Admins can manage employees, generate download codes, and track all activities.

**Company Name:** Maayash Communications  
**Contractor For:** Safaricom  
**Payment Amount:** KES 50 per ID card download  
**Target Users:** 73+ employees in Meru Territory  

---

## TECHNOLOGY STACK (STRICT REQUIREMENTS)

### Frontend
- **Framework:** Next.js 16.1.3+ with App Router (Mandatory - cannot be changed)
- **Language:** TypeScript 5+ (Mandatory - strict mode enabled)
- **Styling:** Tailwind CSS 4.0+ with shadcn/ui component library (New York style)
- **Icons:** Lucide React (for all icons)
- **Charts:** Recharts (for dashboard statistics)
- **State Management:** Zustand for client state, TanStack Query for server state
- **Forms:** React Hook Form with Zod validation
- **QR Codes:** qrcode.react or similar library for QR code generation

### Backend
- **API Route Handlers:** Next.js 16 App Router API routes (use 'use server' directive)
- **Database:** Prisma ORM with SQLite client (db folder)
- **Caching:** In-memory caching (no Redis/MySQL middleware)
- **File Upload:** Native Next.js handling with temporary storage
- **Session Management:** JWT or session-based authentication

### Development Environment
- **Package Manager:** bun (mandatory)
- **Port:** 3000 only (for Next.js dev server)
- **Linting:** ESLint with Next.js rules
- **Code Style:** TypeScript strict, ES6+ import/export

### External Services
- **M-Pesa:** Daraja API for STK Push payments (Safaricom)
- **Font:** Inter or similar professional font family
- **Image Processing:** HTML5 Canvas for ID card generation

---

## DEFAULT ADMIN CREDENTIALS (EXACT VALUES)

Create the following admin accounts in the database with these EXACT credentials:

### Admin Account 1
- **Email:** greencorairtime@gmail.com
- **Password:** Admin@123 (hash with bcrypt)
- **Name:** GreenCor Airtime
- **Role:** Super Admin
- **Status:** Active

### Admin Account 2
- **Email:** Gatutunewton1@gmail.com
- **Password:** Admin@123 (hash with bcrypt)
- **Name:** Gatutunewton
- **Role:** Admin
- **Status:** Active

**CRITICAL:** These default passwords must be hashed with bcrypt and NEVER displayed on the login page interface. They should only be stored in the database and referenced in internal documentation.

---

## DATABASE SCHEMA (PRISMA)

Create a complete Prisma schema with the following models:

```prisma
// prisma/schema.prisma

generator client {
  provider = "prisma-client-js"
}

datasource db {
  provider = "sqlite"
  url      = "file:../db/custom.db"
}

model Admin {
  id            String    @id @default(cuid())
  email         String    @unique
  password      String
  name          String
  role          String    @default("admin") // "admin" or "super_admin"
  isActive      Boolean   @default(true)
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
  lastLoginAt   DateTime?
  
  downloadCodes DownloadCode[]
  activityLogs  ActivityLog[]
}

model Employee {
  id            String    @id @default(cuid())
  employeeId    String    @unique // National ID number
  firstName     String
  lastName      String
  email         String?
  phone         String
  role          String
  region        String
  department    String?
  site          String?
  contractType  String?   @default("permanent")
  salary        Float?
  isActive      Boolean   @default(true)
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
  
  downloads     DownloadHistory[]
  payments      Payment[]
}

model DownloadCode {
  id              String    @id @default(cuid())
  code            String    @unique
  maxUses         Int       @default(1)
  usedCount       Int       @default(0)
  expiresAt       DateTime?
  isActive        Boolean   @default(true)
  createdBy       String
  createdAt       DateTime  @default(now())
  
  admin           Admin     @relation(fields: [createdBy], references: [id])
  downloads       DownloadHistory[]
}

model Payment {
  id              String    @id @default(cuid())
  employeeId      String
  mpesaReceipt    String?   @unique
  phoneNumber     String
  amount          Float     @default(50.0)
  status          String    @default("pending") // pending, completed, failed
  merchantRequest String?
  checkoutRequest String?
  callbackReceived Boolean @default(false)
  createdAt       DateTime  @default(now())
  completedAt     DateTime?
  
  employee        Employee  @relation(fields: [employeeId], references: [id])
  downloads       DownloadHistory[]
}

model DownloadHistory {
  id              String    @id @default(cuid())
  employeeId      String
  downloadType    String    // "payment" or "code"
  paymentId       String?
  downloadCodeId  String?
  ipAddress       String?
  userAgent       String?
  createdAt       DateTime  @default(now())
  
  employee        Employee  @relation(fields: [employeeId], references: [id])
  payment         Payment?  @relation(fields: [paymentId], references: [id])
  downloadCode    DownloadCode? @relation(fields: [downloadCodeId], references: [id])
}

model ActivityLog {
  id          String   @id @default(cuid())
  adminId     String
  action      String
  entity      String
  entityId    String?
  details     String?
  ipAddress   String?
  createdAt   DateTime @default(now())
  
  admin       Admin    @relation(fields: [adminId], references: [id])
}
```

**Database Location:** `/home/z/my-project/db/custom.db`  
**Schema File:** `/home/z/my-project/prisma/schema.prisma`  
**DB Client Export:** `/home/z/my-project/src/lib/db.ts`

Run: `bun run db:push` to initialize the database.

---

## FILE STRUCTURE (EXACT ORGANIZATION)

```
/home/z/my-project/
├── prisma/
│   └── schema.prisma
├── db/
│   └── custom.db
├── src/
│   ├── app/
│   │   ├── page.tsx                    # Employee landing page (main portal)
│   │   ├── layout.tsx                  # Root layout with providers
│   │   ├── globals.css                 # Global styles
│   │   ├── api/
│   │   │   ├── seed/
│   │   │   │   └── route.ts            # Create admin accounts
│   │   │   ├── import-employees/
│   │   │   │   └── route.ts            # Import from Excel
│   │   │   ├── admin/
│   │   │   │   ├── login/
│   │   │   │   │   └── route.ts        # Admin authentication
│   │   │   │   ├── employees/
│   │   │   │   │   ├── route.ts        # GET all employees
│   │   │   │   │   ├── [id]/
│   │   │   │   │   │   └── route.ts    # GET/DELETE by ID
│   │   │   │   │   └── create/
│   │   │   │   │       └── route.ts    # POST create employee
│   │   │   │   ├── download-codes/
│   │   │   │   │   ├── route.ts        # GET all codes
│   │   │   │   │   ├── create/
│   │   │   │   │   │   └── route.ts    # Generate code
│   │   │   │   │   └── [id]/
│   │   │   │   │       ├── deactivate/
│   │   │   │   │       │   └── route.ts
│   │   │   │   │       └── route.ts    # DELETE code
│   │   │   │   └── stats/
│   │   │   │       └── route.ts        # Dashboard statistics
│   │   │   ├── employees/
│   │   │   │   └── search/
│   │   │   │       └── route.ts        # Search by ID digits
│   │   │   ├── payments/
│   │   │   │   ├── initiate/
│   │   │   │   │   └── route.ts        # M-Pesa STK push
│   │   │   │   └── status/
│   │   │   │       └── [id]/
│   │   │   │           └── route.ts    # Check payment status
│   │   │   ├── download-codes/
│   │   │   │   └── validate/
│   │   │   │       └── route.ts        # Validate download code
│   │   │   └── id-cards/
│   │   │       └── generate/
│   │   │           └── route.ts        # Generate ID card
│   │   ├── admin/
│   │   │   ├── page.tsx                # Redirect to login
│   │   │   ├── login/
│   │   │   │   └── page.tsx            # Admin login page
│   │   │   └── dashboard/
│   │   │       └── page.tsx            # Admin dashboard
│   ├── components/
│   │   ├── ui/                         # shadcn/ui components (already exist)
│   │   ├── IDCardCanvas.tsx            # Canvas-based ID card generator
│   │   ├── EmployeeSearch.tsx          # Employee search component
│   │   ├── PhotoUpload.tsx             # Photo upload component
│   │   ├── PaymentFlow.tsx             # M-Pesa payment flow
│   │   ├── CodeRedemption.tsx          # Download code redemption
│   │   └── IDCardModal.tsx             # Modal to display/download ID
│   ├── lib/
│   │   ├── db.ts                       # Prisma client export
│   │   ├── auth.ts                     # Authentication utilities
│   │   ├── mpesa.ts                    # M-Pesa API integration
│   │   └── utils.ts                    # Utility functions
│   └── hooks/
│       ├── use-auth.ts                 # Auth state management
│       └── use-toast.ts                # Toast notifications
├── public/
│   └── images/
│       └── safaricom-logo.png          # Optional Safaricom logo
└── worklog.md                          # Development work log
```

---

## MODULE 1: AUTHENTICATION SYSTEM

### Admin Login API
**File:** `src/app/api/admin/login/route.ts`

**Requirements:**
- Use 'use server' directive
- Accept POST request with `{ email, password }`
- Find admin by email (case-insensitive)
- Compare password using bcrypt.compare()
- Return admin data if authenticated
- Return 401 if invalid credentials
- Log all login attempts
- Update lastLoginAt on successful login

**API Request:**
```json
{
  "email": "greencorairtime@gmail.com",
  "password": "Admin@123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "admin": {
    "id": "clxxx",
    "email": "greencorairtime@gmail.com",
    "name": "GreenCor Airtime",
    "role": "super_admin"
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "error": "Invalid email or password"
}
```

**Implementation Notes:**
- Use `import { db } from '@/lib/db'` for database
- Use `import { compare } from 'bcryptjs'` for password verification
- Add extensive console logging for debugging
- Handle errors gracefully

### Admin Login Page
**File:** `src/app/admin/login/page.tsx`

**Requirements:**
- Clean, professional login form
- Email and password fields
- Loading state during submission
- Error message display
- Redirect to /admin/dashboard on success
- Save admin data to localStorage on success
- NO default credentials displayed on the page
- Responsive design (mobile-first)
- Safaricom branding (green header)

**Component Structure:**
```typescript
'use client'

import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Lock, Mail } from 'lucide-react'

export default function AdminLoginPage() {
  // State for email, password, loading, error
  // Handle form submission
  // Call /api/admin/login
  // Save to localStorage on success
  // Redirect to /admin/dashboard
  // Show error message on failure
  // Console logs for debugging
}
```

**UI Elements:**
- Card container with shadow
- "Admin Login" title
- "Maayash Communications" subtitle
- Email field with Mail icon
- Password field with Lock icon
- "Sign In" button with loading state
- Error message in red text
- Link to employee portal (optional)

### Seed Database API
**File:** `src/app/api/seed/route.ts`

**Requirements:**
- Create both admin accounts if they don't exist
- Hash passwords with bcrypt
- Return success status
- Log creation details

**Admin 1:**
- Email: greencorairtime@gmail.com
- Password: Admin@123 (hash it)
- Name: GreenCor Airtime
- Role: super_admin

**Admin 2:**
- Email: Gatutunewton1@gmail.com
- Password: Admin@123 (hash it)
- Name: Gatutunewton
- Role: admin

**Response:**
```json
{
  "success": true,
  "message": "Database seeded successfully",
  "admins": [
    { "email": "greencorairtime@gmail.com", "name": "GreenCor Airtime" },
    { "email": "Gatutunewton1@gmail.com", "name": "Gatutunewton" }
  ]
}
```

---

## MODULE 2: EMPLOYEE MANAGEMENT

### Import Employees API (Excel)
**File:** `src/app/api/import-employees/route.ts`

**Requirements:**
- Accept POST with FormData containing Excel file
- Parse Excel using xlsx library
- Map columns to database fields
- Skip duplicate employee IDs
- Validate required fields
- Return import statistics

**Excel Column Mapping:**
- `NO` → Skip (row number)
- `NAME` → Split into firstName and lastName (space separator)
- `ID NO.` → employeeId
- `PHONE NO.` → phone
- `ROLE` → role
- `REGION` → region
- `DEPARTMENT` → department
- `SITE` → site
- `BASIC PAY` → salary

**API Response:**
```json
{
  "success": true,
  "imported": 73,
  "skipped": 0,
  "errors": []
}
```

### Create Employee API
**File:** `src/app/api/admin/employees/create/route.ts`

**Requirements:**
- POST request with employee data
- Validate required fields
- Check for duplicate employeeId
- Create employee record
- Log activity
- Return created employee

### Get All Employees API
**File:** `src/app/api/admin/employees/route.ts`

**Requirements:**
- GET request
- Return paginated list of employees
- Support search/filter parameters
- Include download count
- Order by createdAt DESC

### Delete Employee API
**File:** `src/app/api/admin/employees/[id]/route.ts`

**Requirements:**
- DELETE request
- Soft delete (set isActive = false)
- Log deletion activity
- Return success status

### Search Employee API (Employee Portal)
**File:** `src/app/api/employees/search/route.ts`

**Requirements:**
- GET request with query param `digits` (first 5 digits of ID)
- Return matching employees (active only)
- Limit to 10 results
- Include all necessary fields

**API Request:**
```
GET /api/employees/search?digits=12345
```

**Response:**
```json
{
  "success": true,
  "employees": [
    {
      "id": "clxxx",
      "employeeId": "12345678",
      "firstName": "John",
      "lastName": "Doe",
      "phone": "0712345678",
      "role": "Sales Agent",
      "region": "Meru",
      "department": "Sales",
      "site": "Meru Town"
    }
  ]
}
```

---

## MODULE 3: DOWNLOAD CODE SYSTEM

### Create Download Code API
**File:** `src/app/api/admin/download-codes/create/route.ts`

**Requirements:**
- POST request with `{ maxUses, expiresAt }`
- Generate unique code (8-character alphanumeric)
- Get admin ID from session/request
- Create download code record
- Log creation
- Return code details

**Request:**
```json
{
  "maxUses": 1,
  "expiresAt": "2025-02-01T00:00:00Z"
}
```

**Response:**
```json
{
  "success": true,
  "code": "AB12CD34",
  "maxUses": 1,
  "expiresAt": "2025-02-01T00:00:00Z"
}
```

### Validate Download Code API
**File:** `src/app/api/download-codes/validate/route.ts`

**Requirements:**
- POST request with `{ code, employeeId }`
- Check if code exists and is active
- Check if not expired
- Check if uses remaining
- Increment usedCount
- Return validation result

**Request:**
```json
{
  "code": "AB12CD34",
  "employeeId": "12345678"
}
```

**Response:**
```json
{
  "valid": true,
  "codeId": "clxxx"
}
```

### Get All Download Codes API
**File:** `src/app/api/admin/download-codes/route.ts`

**Requirements:**
- GET request
- Return all codes with usage stats
- Show creator name
- Include active/inactive status

### Deactivate Download Code API
**File:** `src/app/api/admin/download-codes/[id]/deactivate/route.ts`

**Requirements:**
- POST request
- Set isActive = false
- Log activity
- Return success

### Delete Download Code API
**File:** `src/app/api/admin/download-codes/[id]/route.ts`

**Requirements:**
- DELETE request
- Remove code from database
- Log deletion
- Return success

---

## MODULE 4: PAYMENT SYSTEM (M-PESA)

### Initiate Payment API (STK Push)
**File:** `src/app/api/payments/initiate/route.ts`

**Requirements:**
- POST request with `{ phoneNumber, employeeId, amount }`
- Validate phone number (format: 07XXXXXXXX or 2547XXXXXXXX)
- Create payment record with status "pending"
- Call M-Pesa Daraja API for STK push
- Store MerchantRequestID and CheckoutRequestID
- Return payment initiation details

**M-Pesa Integration:**
- Use environment variables for credentials:
  - MPESA_CONSUMER_KEY
  - MPESA_CONSUMER_SECRET
  - MPESA_PASSKEY
  - MPESA_SHORTCODE
  - MPESA_CALLBACK_URL
- Implement OAuth token generation
- Call /mpesa/stkpush/v1/processrequest endpoint
- Handle API errors gracefully

**Request:**
```json
{
  "phoneNumber": "0712345678",
  "employeeId": "12345678",
  "amount": 50
}
```

**Response:**
```json
{
  "success": true,
  "paymentId": "clxxx",
  "merchantRequest": "ws-co-xxxx",
  "checkoutRequest": "ws-co-xxxx",
  "message": "STK push sent to your phone"
}
```

### Check Payment Status API
**File:** `src/app/api/payments/status/[id]/route.ts`

**Requirements:**
- GET request with payment ID
- Query M-Pesa API for transaction status
- Update payment record with result
- Return current status

### M-Pesa Callback API
**File:** `src/app/api/mpesa/callback/route.ts`

**Requirements:**
- POST request from M-Pesa
- Validate callback signature
- Find payment by MerchantRequestID
- Update payment status (completed/failed)
- Store M-Pesa receipt number
- Mark callbackReceived = true
- Set completedAt timestamp
- Return success response

**Callback Body:**
```json
{
  "Body": {
    "stkCallback": {
      "MerchantRequestID": "xxx",
      "CheckoutRequestID": "xxx",
      "ResultCode": 0,
      "ResultDesc": "Success",
      "CallbackMetadata": {
        "Item": [
          { "Name": "Amount", "Value": 50 },
          { "Name": "MpesaReceiptNumber", "Value": "xxx" },
          { "Name": "PhoneNumber", "Value": "254712345678" }
        ]
      }
    }
  }
}
```

---

## MODULE 5: ID CARD GENERATION

### Generate ID Card API
**File:** `src/app/api/id-cards/generate/route.ts`

**Requirements:**
- POST request with employee data and photo (base64)
- Generate ID card using HTML5 Canvas
- Embed QR code containing employee data
- Return base64 image data
- Log download in DownloadHistory

**ID Card Specifications:**
- **Dimensions:** 1200px width × 760px height
- **DPI:** 300 for print quality
- **Format:** PNG
- **Colors:** 
  - Background: White
  - Safaricom Green: #009933
  - Safaricom Red: #E60000
  - Text: Black (#000000) and Dark Gray (#333333)
- **Font:** Arial or similar sans-serif

**ID Card Layout (ASCII Diagram):**
```
+----------------------------------------+
|  [SAFARICOM LOGO]   MAAYASH COMM.     |  <- Header (Green: #009933)
|  Contractor for: SAFARICOM            |  <- Subtitle
+----------------------------------------+
|                                        |
|  +--------------------+  Name: John    |  <- Photo + Details
|  |                    |  DOE            |
|  |    EMPLOYEE        |  ID: 12345678  |
|  |    PHOTO           |  Phone: 0712.. |
|  |   (200x250px)      |  Role: Sales.. |
|  |                    |  Region: Meru  |
|  +--------------------+  Dept: Sales   |
|                        Site: Meru Town |
|                                        |
|  [QR CODE]        Valid Until: 2026    |  <- Footer
|  (150x150px)                           |
+----------------------------------------+
```

**QR Code Content (JSON string):**
```json
{
  "employeeId": "12345678",
  "name": "John Doe",
  "phone": "0712345678",
  "role": "Sales Agent",
  "region": "Meru",
  "company": "Maayash Communications",
  "validUntil": "2026-12-31"
}
```

**Canvas Drawing Steps:**
1. Fill white background (1200x760)
2. Draw green header bar (height: 100px, color: #009933)
3. Add "MAAYASH COMMUNICATIONS" text (white, bold, large)
4. Add "Contractor for: SAFARICOM" text (white, medium)
5. Draw employee photo (left side, 200x250px, rounded corners)
6. Add employee details (right side, vertical list):
   - Name (bold, large)
   - ID Number
   - Phone Number
   - Role
   - Region
   - Department
   - Site
7. Add section dividers (red lines, color: #E60000)
8. Generate and draw QR code (bottom right, 150x150px)
9. Add "Valid Until: 2026" text
10. Add footer with company details
11. Add decorative elements (Safaricom styling)
12. Convert to base64 PNG data URL

**Request:**
```json
{
  "employee": {
    "id": "clxxx",
    "employeeId": "12345678",
    "firstName": "John",
    "lastName": "Doe",
    "phone": "0712345678",
    "role": "Sales Agent",
    "region": "Meru",
    "department": "Sales",
    "site": "Meru Town"
  },
  "photo": "data:image/jpeg;base64,...",
  "downloadType": "payment",
  "paymentId": "clxxx"
}
```

**Response:**
```json
{
  "success": true,
  "imageData": "data:image/png;base64,iVBORw0KGgo...",
  "filename": "Maayash_ID_12345678.png"
}
```

---

## MODULE 6: EMPLOYEE PORTAL (MAIN PAGE)

**File:** `src/app/page.tsx`

### 6-Step Wizard Flow:

**Step 1: Search Employee**
- Input field for first 5 digits of ID
- "Search" button
- Loading state during search
- Display results in dropdown

**Step 2: Select Profile**
- Dropdown with matching employees
- Display: Name, ID, Role, Region
- Auto-fill form on selection

**Step 3: Verify Details**
- Display all employee details (read-only)
- Allow user to confirm information
- "Continue" button

**Step 4: Upload Photo**
- Photo upload component
- Preview uploaded image
- Validate file type (jpg, jpeg, png)
- Validate file size (max 5MB)
- Crop/resize to 200x250px

**Step 5: Choose Download Method**
- Two options:
  1. **Pay with M-Pesa** (KES 50)
     - Enter phone number
     - Click "Pay Now"
     - Show STK push sent message
     - Poll for payment status
     - Auto-proceed on success
  2. **Use Download Code**
     - Enter 8-character code
     - Click "Validate"
     - Show validation result
     - Proceed if valid

**Step 6: Download ID Card**
- Call /api/id-cards/generate
- Display generated ID card in modal
- Show "Download ID Card" button
- Download as PNG file
- Show success message

### Component Structure:
```typescript
'use client'

import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import IDCardModal from '@/components/IDCardModal'
import { Search, Upload, Download, CreditCard, Ticket } from 'lucide-react'

export default function HomePage() {
  const [step, setStep] = useState(1)
  const [searchDigits, setSearchDigits] = useState('')
  const [searchResults, setSearchResults] = useState([])
  const [selectedEmployee, setSelectedEmployee] = useState(null)
  const [photo, setPhoto] = useState(null)
  const [paymentMethod, setPaymentMethod] = useState('mpesa')
  const [phoneNumber, setPhoneNumber] = useState('')
  const [downloadCode, setDownloadCode] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [generatedCard, setGeneratedCard] = useState(null)
  
  // Implement all step handlers
  // Search, select, upload, pay, validate, generate
}
```

### UI Requirements:
- **Header:**
  - Maayash Communications logo/title
  - "Employee ID Card Portal"
  - Safaricom green accent
- **Progress Indicator:**
  - Show current step (1-6)
  - Completed steps in green
  - Current step highlighted
- **Cards:**
  - Each step in a card container
  - Clean shadows and borders
  - Proper spacing (gap-4, p-6)
- **Buttons:**
  - Primary buttons in Safaricom green (#009933)
  - Secondary buttons in gray
  - Loading states with spinners
  - Disabled states for invalid inputs
- **Responsive:**
  - Mobile-first design
  - Stack columns on mobile
  - Side-by-side on desktop
- **Colors:**
  - Primary: #009933 (Safaricom Green)
  - Secondary: #E60000 (Safaricom Red)
  - Background: White
  - Text: #000000, #333333

---

## MODULE 7: ADMIN DASHBOARD

**File:** `src/app/admin/dashboard/page.tsx`

### Dashboard Features:

**1. Statistics Cards (Top Row)**
- Total Employees
- Total Downloads Today
- Total Revenue (KES)
- Active Download Codes

**2. Charts Section**
- Downloads by Day (Bar chart - last 7 days)
- Downloads by Region (Pie chart)
- Revenue Trend (Line chart - last 30 days)

**3. Employee Management Section**
- "Add Employee" button (opens modal)
- Employee table with columns:
  - ID Number
  - Name
  - Phone
  - Role
  - Region
  - Downloads Count
  - Actions (View, Delete)
- Search/filter functionality
- Pagination (20 per page)

**4. Download Codes Section**
- "Generate Code" button (opens modal)
- Code table with columns:
  - Code
  - Max Uses
  - Used Count
  - Expires At
  - Status (Active/Inactive)
  - Actions (Deactivate, Delete)
- Copy to clipboard functionality

**5. Recent Activity**
- List of recent actions (last 20)
- Columns: Timestamp, Admin, Action, Details
- Auto-refresh every 30 seconds

**6. Header**
- "Maayash Communications Admin Dashboard"
- Logged-in admin email
- "Logout" button
- Responsive navigation

### Component Structure:
```typescript
'use client'

import { useState, useEffect } from 'react'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Input } from '@/components/ui/input'
import { Modal } from '@/components/ui/modal'
import { BarChart, PieChart, LineChart } from 'recharts'
import { Users, Download, DollarSign, Ticket, Plus, LogOut, Search } from 'lucide-react'

export default function AdminDashboard() {
  const [admin, setAdmin] = useState(null)
  const [stats, setStats] = useState({})
  const [employees, setEmployees] = useState([])
  const [codes, setCodes] = useState([])
  const [activities, setActivities] = useState([])
  
  // Fetch all data on mount
  // Implement CRUD operations
  // Handle modals for add employee/generate code
}
```

---

## MODULE 8: ID CARD CANVAS COMPONENT

**File:** `src/components/IDCardCanvas.tsx`

### Requirements:
- Client-side canvas rendering
- Accept employee data and photo as props
- Draw ID card exactly as specified in Module 5
- Return base64 image data
- Support download functionality
- Handle high-DPI displays

### Component Structure:
```typescript
'use client'

import { useEffect, useRef, useState } from 'react'
import { Download } from 'lucide-react'

interface IDCardCanvasProps {
  employee: any
  photo: string
  onGenerated?: (imageData: string) => void
}

export default function IDCardCanvas({ employee, photo, onGenerated }: IDCardCanvasProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null)
  const [imageData, setImageData] = useState<string>('')
  const [isGenerating, setIsGenerating] = useState(false)
  
  useEffect(() => {
    // Draw ID card on mount
    drawIDCard()
  }, [employee, photo])
  
  const drawIDCard = async () => {
    setIsGenerating(true)
    const canvas = canvasRef.current
    const ctx = canvas.getContext('2d')
    
    // Set canvas dimensions
    canvas.width = 1200
    canvas.height = 760
    
    // Drawing steps (as per Module 5)
    // 1. Background
    // 2. Header
    // 3. Photo
    // 4. Details
    // 5. QR Code
    // 6. Footer
    
    // Generate QR code (using qrcode library)
    // Convert to base64
    // Call onGenerated callback
  }
  
  const handleDownload = () => {
    // Download canvas as PNG
    const link = document.createElement('a')
    link.download = `Maayash_ID_${employee.employeeId}.png`
    link.href = imageData
    link.click()
  }
  
  return (
    <div>
      <canvas ref={canvasRef} className="hidden" />
      {imageData && (
        <div>
          <img src={imageData} alt="ID Card" />
          <Button onClick={handleDownload}>
            <Download className="mr-2 h-4 w-4" />
            Download ID Card
          </Button>
        </div>
      )}
    </div>
  )
}
```

---

## MODULE 9: RESPONSIVE DESIGN SYSTEM

### CSS Requirements:
**File:** `src/app/globals.css`

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

:root {
  /* Safaricom Colors */
  --safaricom-green: #009933;
  --safaricom-red: #E60000;
  --safaricom-dark: #006622;
  
  /* Neutral Colors */
  --background: #ffffff;
  --foreground: #000000;
  --muted: #f3f4f6;
  --muted-foreground: #6b7280;
}

body {
  font-family: 'Inter', Arial, sans-serif;
  background-color: var(--background);
  color: var(--foreground);
  min-height: 100vh;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: var(--safaricom-green);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--safaricom-dark);
}

/* Loading Spinner */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.spinner {
  animation: spin 1s linear infinite;
}

/* Print Styles */
@media print {
  .no-print {
    display: none !important;
  }
}
```

### Responsive Breakpoints:
- **Mobile:** < 640px (stack everything vertically)
- **Tablet:** 640px - 1024px (2-column layouts)
- **Desktop:** > 1024px (3+ column layouts)

### Component-Specific Styles:
```css
/* ID Card Preview */
.id-card-preview {
  width: 100%;
  max-width: 600px;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

/* Progress Steps */
.progress-step {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.progress-step.completed {
  color: var(--safaricom-green);
}

.progress-step.active {
  color: var(--safaricom-green);
  font-weight: 600;
}

/* Table Styles */
.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background-color: var(--muted);
  padding: 0.75rem;
  text-align: left;
  font-weight: 600;
}

.data-table td {
  padding: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
}

.data-table tr:hover {
  background-color: var(--muted);
}
```

---

## MODULE 10: FILE UPLOAD SYSTEM

### Photo Upload Component
**File:** `src/components/PhotoUpload.tsx`

### Requirements:
- Drag and drop support
- File type validation (jpg, jpeg, png)
- File size validation (max 5MB)
- Image preview
- Crop/resize to 200x250px
- Convert to base64
- Remove functionality
- Loading states

### Excel Import Component (Admin)
**File:** `src/components/ExcelImport.tsx`

### Requirements:
- Accept .xlsx files only
- Parse and preview data
- Show import statistics
- Confirm before import
- Error handling for invalid files
- Progress indicator during import

---

## ENVIRONMENT VARIABLES

**File:** `.env.local`

```env
# Database
DATABASE_URL="file:../db/custom.db"

# M-Pesa Daraja API
MPESA_CONSUMER_KEY="your_consumer_key"
MPESA_CONSUMER_SECRET="your_consumer_secret"
MPESA_PASSKEY="your_passkey"
MPESA_SHORTCODE="174379"
MPESA_CALLBACK_URL="https://your-domain.com/api/mpesa/callback"

# App
NEXT_PUBLIC_APP_NAME="Maayash Communications ID Portal"
NEXT_PUBLIC_COMPANY_NAME="Maayash Communications"
NEXT_PUBLIC_CONTRACTOR_FOR="Safaricom"
```

---

## API ENDPOINTS SUMMARY

### Public Endpoints (Employee Portal)
1. `POST /api/seed` - Initialize database with admin accounts
2. `POST /api/import-employees` - Import employees from Excel
3. `GET /api/employees/search?digits=XXXXX` - Search employees
4. `POST /api/payments/initiate` - Initiate M-Pesa payment
5. `GET /api/payments/status/[id]` - Check payment status
6. `POST /api/download-codes/validate` - Validate download code
7. `POST /api/id-cards/generate` - Generate ID card

### Admin Endpoints
8. `POST /api/admin/login` - Admin authentication
9. `GET /api/admin/employees` - Get all employees
10. `POST /api/admin/employees/create` - Create employee
11. `DELETE /api/admin/employees/[id]` - Delete employee
12. `GET /api/admin/download-codes` - Get all codes
13. `POST /api/admin/download-codes/create` - Generate code
14. `POST /api/admin/download-codes/[id]/deactivate` - Deactivate code
15. `DELETE /api/admin/download-codes/[id]` - Delete code
16. `GET /api/admin/stats` - Get dashboard statistics

### Webhook
17. `POST /api/mpesa/callback` - M-Pesa payment callback

---

## TESTING CHECKLIST

### Authentication
- [ ] Can seed database with admin accounts
- [ ] Can login with correct credentials
- [ ] Cannot login with wrong credentials
- [ ] Session persists on page refresh
- [ ] Logout works correctly
- [ ] Protected routes redirect to login

### Employee Portal
- [ ] Search by first 5 ID digits works
- [ ] Dropdown shows correct matches
- [ ] Auto-fill works after selection
- [ ] Photo upload accepts valid files
- [ ] Photo upload rejects invalid files
- [ ] M-Pesa payment initiates correctly
- [ ] Payment status updates
- [ ] Download code validation works
- [ ] ID card generates with correct data
- [ ] ID card includes QR code
- [ ] Download button saves PNG file
- [ ] Download logged in history

### Admin Dashboard
- [ ] Dashboard loads with correct stats
- [ ] Charts render correctly
- [ ] Can add new employee
- [ ] Can view all employees
- [ ] Can delete employee
- [ ] Can generate download code
- [ ] Can view all codes
- [ ] Can deactivate code
- [ ] Can delete code
- [ ] Activity log updates
- [ ] Recent activity shows

### ID Card Generation
- [ ] Canvas renders at 1200x760
- [ ] All employee details displayed
- [ ] Photo positioned correctly
- [ ] QR code contains correct data
- [ ] Safaricom colors applied
- [ ] Company name correct
- [ ] Download filename correct format
- [ ] Image quality high (300 DPI)

### Payment System
- [ ] STK push initiates
- [ ] Callback receives updates
- [ ] Payment status changes
- [ ] Receipt number stored
- [ ] Cannot download without payment
- [ ] Can download after payment

### Responsive Design
- [ ] Mobile layout works (< 640px)
- [ ] Tablet layout works (640-1024px)
- [ ] Desktop layout works (> 1024px)
- [ ] Touch targets minimum 44px
- [ ] Text readable on mobile
- [ ] Tables scroll on mobile

### Security
- [ ] Passwords hashed with bcrypt
- [ ] SQL injection prevented (Prisma)
- [ ] XSS protection (React)
- [ ] CSRF protection (Next.js)
- [ ] File upload validation
- [ ] Rate limiting on login
- [ ] Session expiration

---

## IMPLEMENTATION GUIDELINES

### Coding Standards
1. **TypeScript Strict Mode:** Enable all strict checks
2. **ESLint:** Follow Next.js recommended rules
3. **Naming Conventions:**
   - Components: PascalCase (e.g., `IDCardCanvas`)
   - Functions: camelCase (e.g., `handleLogin`)
   - Constants: UPPER_SNAKE_CASE (e.g., `MAX_FILE_SIZE`)
   - Files: kebab-case or PascalCase for components
4. **Code Organization:**
   - One component per file
   - Keep components under 300 lines
   - Extract reusable logic to custom hooks
   - Use proper TypeScript interfaces
5. **Error Handling:**
   - Always handle API errors
   - Show user-friendly error messages
   - Log errors for debugging
   - Never expose sensitive data in errors
6. **Performance:**
   - Use React.memo for expensive components
   - Implement loading states
   - Lazy load heavy components
   - Optimize images
7. **Accessibility:**
   - Use semantic HTML
   - Add ARIA labels
   - Support keyboard navigation
   - Proper color contrast
   - Alt text for images

### Best Practices
1. **Database:**
   - Use transactions for multi-step operations
   - Implement soft deletes where appropriate
   - Add indexes on frequently queried fields
   - Use Prisma's type safety
2. **API:**
   - Use appropriate HTTP methods
   - Return consistent response format
   - Implement request validation
   - Rate limit sensitive endpoints
3. **Frontend:**
   - Use controlled components
   - Implement form validation
   - Show loading states
   - Handle edge cases
4. **Security:**
   - Never store plain text passwords
   - Validate all inputs
   - Use HTTPS in production
   - Implement CORS correctly
   - Sanitize user content

---

## DEPLOYMENT CHECKLIST

### Pre-Deployment
- [ ] Change default admin passwords
- [ ] Update M-Pesa credentials to production
- [ ] Set strong environment variables
- [ ] Enable HTTPS
- [ ] Configure domain
- [ ] Set up database backups
- [ ] Configure error tracking (Sentry)
- [ ] Set up monitoring
- [ ] Test all flows end-to-end
- [ ] Performance testing

### Post-Deployment
- [ ] Verify all API endpoints
- [ ] Test payment integration (small amount)
- [ ] Monitor error logs
- [ ] Check database performance
- [ ] Verify email notifications (if any)
- [ ] Test mobile responsiveness
- [ ] Verify file uploads work
- [ ] Test download functionality

---

## SUCCESS CRITERIA

The system is considered complete when:

1. ✅ Both admin accounts can login with specified credentials
2. ✅ Employees can search by ID digits and find themselves
3. ✅ Employees can upload photos and see previews
4. ✅ M-Pesa STK push works (test environment)
5. ✅ Payment status updates correctly
6. ✅ Download codes can be generated and validated
7. ✅ ID cards generate with correct layout and data
8. ✅ QR codes contain employee information
9. ✅ Download button saves PNG files
10. ✅ Admin dashboard shows accurate statistics
11. ✅ Admins can add/delete employees
12. ✅ Admins can manage download codes
13. ✅ All activity is logged
14. ✅ Design is responsive on all devices
15. ✅ Security measures are in place
16. ✅ Excel import works with employee data
17. ✅ All 73 employees can be imported
18. ✅ No console errors in browser
19. ✅ TypeScript compiles without errors
20. ✅ ESLint passes with no warnings

---

## FINAL NOTES

This prompt provides a complete, detailed specification for building an exact replica of the Maayash Communications Employee ID Card Management System. Follow each module's specifications carefully, implement all API endpoints, create all components, and ensure the design matches the Safaricom contractor branding.

**Key Points to Remember:**
- Use Next.js 16 with App Router (mandatory)
- Use TypeScript with strict mode
- Use Prisma with SQLite
- Use Tailwind CSS with shadcn/ui
- Apply Safaricom colors (#009933, #E60000)
- Implement exact admin credentials
- Create 1200x760 ID cards with QR codes
- Support both M-Pesa and download code methods
- Make everything responsive
- Follow the SDLC lifecycle
- Test thoroughly before completion

The system should be production-ready, secure, and user-friendly when completed.

---

**END OF COMPREHENSIVE PROMPT**
