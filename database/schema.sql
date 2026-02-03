-- Database Schema for Maayash Communications ID Card System
-- Create tables: Admin, Employee, DownloadCode, Payment, DownloadHistory, ActivityLog

-- Create Admin table
CREATE TABLE IF NOT EXISTS Admin (
    id VARCHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    isActive TINYINT(1) DEFAULT 1,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    lastLoginAt DATETIME
);

-- Create Employee table
CREATE TABLE IF NOT EXISTS Employee (
    id VARCHAR(36) PRIMARY KEY,
    employeeId VARCHAR(50) UNIQUE NOT NULL,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20) NOT NULL,
    role VARCHAR(255) NOT NULL,
    region VARCHAR(255) NOT NULL,
    department VARCHAR(255),
    site VARCHAR(255),
    contractType VARCHAR(50) DEFAULT 'permanent',
    salary DECIMAL(10, 2),
    isActive TINYINT(1) DEFAULT 1,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create DownloadCode table
CREATE TABLE IF NOT EXISTS DownloadCode (
    id VARCHAR(36) PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    maxUses INT DEFAULT 1,
    usedCount INT DEFAULT 0,
    expiresAt DATETIME,
    isActive TINYINT(1) DEFAULT 1,
    createdBy VARCHAR(36) NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (createdBy) REFERENCES Admin(id) ON DELETE CASCADE
);

-- Create Payment table
CREATE TABLE IF NOT EXISTS Payment (
    id VARCHAR(36) PRIMARY KEY,
    employeeId VARCHAR(50) NOT NULL,
    mpesaReceipt VARCHAR(100) UNIQUE,
    phoneNumber VARCHAR(20) NOT NULL,
    amount DECIMAL(10, 2) DEFAULT 50.00,
    status VARCHAR(50) DEFAULT 'pending',
    merchantRequest VARCHAR(100),
    checkoutRequest VARCHAR(100),
    callbackReceived TINYINT(1) DEFAULT 0,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    completedAt DATETIME
);

-- Create DownloadHistory table
CREATE TABLE IF NOT EXISTS DownloadHistory (
    id VARCHAR(36) PRIMARY KEY,
    employeeId VARCHAR(50) NOT NULL,
    downloadType VARCHAR(50) NOT NULL,
    paymentId VARCHAR(36),
    downloadCodeId VARCHAR(36),
    ipAddress VARCHAR(45),
    userAgent TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create ActivityLog table
CREATE TABLE IF NOT EXISTS ActivityLog (
    id VARCHAR(36) PRIMARY KEY,
    adminId VARCHAR(36) NOT NULL,
    action VARCHAR(255) NOT NULL,
    entity VARCHAR(100),
    entityId VARCHAR(36),
    details TEXT,
    ipAddress VARCHAR(45),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (adminId) REFERENCES Admin(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_employee_employeeId ON Employee(employeeId);
CREATE INDEX IF NOT EXISTS idx_employee_phone ON Employee(phone);
CREATE INDEX IF NOT EXISTS idx_employee_isActive ON Employee(isActive);
CREATE INDEX IF NOT EXISTS idx_payment_employeeId ON Payment(employeeId);
CREATE INDEX IF NOT EXISTS idx_payment_status ON Payment(status);
CREATE INDEX IF NOT EXISTS idx_downloadCode_code ON DownloadCode(code);
CREATE INDEX IF NOT EXISTS idx_downloadCode_isActive ON DownloadCode(isActive);
CREATE INDEX IF NOT EXISTS idx_downloadHistory_employeeId ON DownloadHistory(employeeId);
CREATE INDEX IF NOT EXISTS idx_activityLog_adminId ON ActivityLog(adminId);
CREATE INDEX IF NOT EXISTS idx_activityLog_createdAt ON ActivityLog(createdAt);
