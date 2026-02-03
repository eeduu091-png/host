<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Maayash Communications</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #009933 0%, #006622 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .dashboard-header h1 {
            font-size: 1.8rem;
        }

        .dashboard-header .admin-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: white;
            color: #009933;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #009933;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .tab {
            padding: 12px 25px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.3s ease;
        }

        .tab:hover {
            border-color: #009933;
            color: #009933;
        }

        .tab.active {
            background: #009933;
            border-color: #009933;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .search-box input:focus {
            outline: none;
            border-color: #009933;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .data-table th,
        .data-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #333;
        }

        .data-table tr:hover {
            background: #f9fafb;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #009933;
            color: white;
        }

        .btn-primary:hover {
            background: #006622;
        }

        .btn-danger {
            background: #E60000;
            color: white;
        }

        .btn-danger:hover {
            background: #cc0000;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #009933 0%, #006622 100%);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .code-display {
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: 700;
            color: #009933;
            text-align: center;
            padding: 20px;
            background: #f0fdf4;
            border: 2px dashed #009933;
            border-radius: 8px;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .data-table {
                font-size: 0.85rem;
            }

            .data-table th,
            .data-table td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="dashboard-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Maayash Communications - ID Card Management</p>
            </div>
            <div class="admin-info">
                <span id="admin-email"></span>
                <button class="btn-logout" onclick="logout()">Logout</button>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="stat-employees">0</div>
                <div class="stat-label">Total Employees</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-downloads">0</div>
                <div class="stat-label">Downloads Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-codes">0</div>
                <div class="stat-label">Active Codes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-revenue">KES 0</div>
                <div class="stat-label">Revenue Today</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" onclick="showTab('employees')">Employees</div>
            <div class="tab" onclick="showTab('codes')">Download Codes</div>
        </div>

        <!-- Employees Tab -->
        <div class="tab-content active" id="employees-tab">
            <div class="action-bar">
                <div class="search-box">
                    <input type="text" id="employee-search" placeholder="Search employees..." />
                </div>
                <button class="btn btn-primary" onclick="openAddEmployeeModal()">+ Add Employee</button>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Region</th>
                        <th>Downloads</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="employees-table">
                    <tr><td colspan="7" style="text-align: center;">Loading...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Download Codes Tab -->
        <div class="tab-content" id="codes-tab">
            <div class="action-bar">
                <div></div>
                <button class="btn btn-primary" onclick="openGenerateCodeModal()">+ Generate Code</button>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Created By</th>
                        <th>Uses</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="codes-table">
                    <tr><td colspan="6" style="text-align: center;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal" id="add-employee-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Employee</h2>
            </div>
            <div class="modal-body">
                <form id="add-employee-form">
                    <div class="form-group">
                        <label>ID Number *</label>
                        <input type="text" id="emp-id" required />
                    </div>
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" id="emp-firstname" required />
                    </div>
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" id="emp-lastname" required />
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" id="emp-phone" required />
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        <input type="text" id="emp-role" required />
                    </div>
                    <div class="form-group">
                        <label>Region *</label>
                        <input type="text" id="emp-region" required />
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" id="emp-department" />
                    </div>
                    <div class="form-group">
                        <label>Site</label>
                        <input type="text" id="emp-site" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-small" onclick="closeModal('add-employee-modal')">Cancel</button>
                <button class="btn btn-primary btn-small" onclick="addEmployee()">Add Employee</button>
            </div>
        </div>
    </div>

    <!-- Generate Code Modal -->
    <div class="modal" id="generate-code-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Generate Download Code</h2>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Maximum Uses</label>
                    <input type="number" id="code-max-uses" value="1" min="1" />
                </div>
                <div class="form-group">
                    <label>Expires At (Optional)</label>
                    <input type="datetime-local" id="code-expires" />
                </div>
                <div id="generated-code-display" style="display: none;">
                    <label>Your Code:</label>
                    <div class="code-display" id="generated-code"></div>
                    <p style="text-align: center; color: #6b7280; font-size: 0.9rem;">Save this code - it won't be shown again!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-small" onclick="closeModal('generate-code-modal')">Close</button>
                <button class="btn btn-primary btn-small" id="generate-code-btn" onclick="generateCode()">Generate</button>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
</body>
</html>
