/**
 * Admin Dashboard JavaScript
 */

// Check authentication
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    loadStats();
    loadEmployees();
    loadCodes();
});

function checkAuth() {
    const admin = localStorage.getItem('admin');
    if (!admin) {
        window.location.href = 'login.php';
        return;
    }

    const adminData = JSON.parse(admin);
    document.getElementById('admin-email').textContent = adminData.email;
}

function logout() {
    localStorage.removeItem('admin');
    window.location.href = 'login.php';
}

// Tab Navigation
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

// Load Statistics
async function loadStats() {
    try {
        // For demo, we'll use placeholder values
        // In production, you would call an API endpoint
        document.getElementById('stat-employees').textContent = '73';
        document.getElementById('stat-downloads').textContent = '12';
        document.getElementById('stat-codes').textContent = '5';
        document.getElementById('stat-revenue').textContent = 'KES 600';
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Load Employees
async function loadEmployees() {
    try {
        const response = await fetch('/api/get_employees.php');
        const data = await response.json();

        if (data.success) {
            displayEmployees(data.employees);
        }
    } catch (error) {
        console.error('Error loading employees:', error);
        document.getElementById('employees-table').innerHTML =
            '<tr><td colspan="7" style="text-align: center; color: #E60000;">Error loading employees</td></tr>';
    }
}

function displayEmployees(employees) {
    const tbody = document.getElementById('employees-table');

    if (employees.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No employees found</td></tr>';
        return;
    }

    let html = '';
    employees.forEach(emp => {
        html += `
            <tr>
                <td><strong>${emp.employeeId}</strong></td>
                <td>${emp.firstName} ${emp.lastName}</td>
                <td>${emp.phone}</td>
                <td>${emp.role}</td>
                <td>${emp.region}</td>
                <td>${emp.downloadCount || 0}</td>
                <td>
                    <button class="btn btn-secondary btn-small" onclick="editEmployee('${emp.id}')">Edit</button>
                    <button class="btn btn-danger btn-small" onclick="deleteEmployee('${emp.id}', '${emp.employeeId}')">Delete</button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

// Load Download Codes
async function loadCodes() {
    try {
        const response = await fetch('/api/get_codes.php');
        const data = await response.json();

        if (data.success) {
            displayCodes(data.codes);
        }
    } catch (error) {
        console.error('Error loading codes:', error);
        document.getElementById('codes-table').innerHTML =
            '<tr><td colspan="6" style="text-align: center; color: #E60000;">Error loading codes</td></tr>';
    }
}

function displayCodes(codes) {
    const tbody = document.getElementById('codes-table');

    if (codes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No codes found</td></tr>';
        return;
    }

    let html = '';
    codes.forEach(code => {
        const statusClass = code.isActive ? 'status-active' : 'status-inactive';
        const statusText = code.isActive ? 'Active' : 'Inactive';
        const expiresText = code.expiresAt ? new Date(code.expiresAt).toLocaleDateString() : 'Never';
        const isFullyUsed = code.usedCount >= code.maxUses;

        html += `
            <tr>
                <td><strong>${code.code}</strong></td>
                <td>${code.createdBy || 'Admin'}</td>
                <td>${code.usedCount}/${code.maxUses}</td>
                <td>${expiresText}</td>
                <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td>
                    ${code.isActive && !isFullyUsed ?
                        `<button class="btn btn-secondary btn-small" onclick="deactivateCode('${code.id}')">Deactivate</button>` :
                        ''
                    }
                    ${!code.isActive || isFullyUsed ?
                        `<button class="btn btn-primary btn-small" onclick="reactivateCode('${code.id}', ${code.maxUses})">Reactivate</button>` :
                        ''
                    }
                    <button class="btn btn-danger btn-small" onclick="deleteCode('${code.id}')">Delete</button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

// Modal Functions
function openAddEmployeeModal() {
    document.getElementById('add-employee-modal').classList.add('active');
}

function openGenerateCodeModal() {
    document.getElementById('generate-code-modal').classList.add('active');
    document.getElementById('generated-code-display').style.display = 'none';
    document.getElementById('generated-code').textContent = '';
    document.getElementById('generate-code-btn').style.display = 'inline-block';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Add Employee
async function addEmployee() {
    const employeeData = {
        employeeId: document.getElementById('emp-id').value.trim(),
        firstName: document.getElementById('emp-firstname').value.trim(),
        lastName: document.getElementById('emp-lastname').value.trim(),
        phone: document.getElementById('emp-phone').value.trim(),
        role: document.getElementById('emp-role').value.trim(),
        region: document.getElementById('emp-region').value.trim(),
        department: document.getElementById('emp-department').value.trim() || null,
        site: document.getElementById('emp-site').value.trim() || null
    };

    // Validation
    if (!employeeData.employeeId || !employeeData.firstName || !employeeData.lastName ||
        !employeeData.phone || !employeeData.role || !employeeData.region) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const response = await fetch('/api/create_employee.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(employeeData)
        });

        const data = await response.json();

        if (data.success) {
            alert('Employee added successfully!');
            closeModal('add-employee-modal');
            document.getElementById('add-employee-form').reset();
            loadEmployees();
            loadStats();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error adding employee: ' + error.message);
    }
}

// Delete Employee
async function deleteEmployee(id, employeeId) {
    if (!confirm(`Are you sure you want to delete employee with ID: ${employeeId}?`)) {
        return;
    }

    try {
        const response = await fetch(`/api/delete_employee.php?id=${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            alert('Employee deleted successfully!');
            loadEmployees();
            loadStats();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error deleting employee: ' + error.message);
    }
}

// Edit Employee
async function editEmployee(id) {
    // For now, we'll prompt for updates
    // In a full implementation, this would open a modal with pre-filled data
    const newPhone = prompt('Enter new phone number (or press Cancel to keep current):');
    if (newPhone !== null && newPhone.trim() !== '') {
        try {
            const response = await fetch('/api/update_employee.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id,
                    phone: newPhone.trim()
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Employee updated successfully!');
                loadEmployees();
            } else {
                alert('Error: ' + data.error);
            }
        } catch (error) {
            alert('Error updating employee: ' + error.message);
        }
    }
}

// Generate Download Code
async function generateCode() {
    const maxUses = parseInt(document.getElementById('code-max-uses').value) || 1;
    const expiresAt = document.getElementById('code-expires').value || null;

    try {
        const response = await fetch('/api/create_code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ maxUses, expiresAt })
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('generated-code').textContent = data.code.code;
            document.getElementById('generated-code-display').style.display = 'block';
            document.getElementById('generate-code-btn').style.display = 'none';
            loadCodes();
            loadStats();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error generating code: ' + error.message);
    }
}

// Deactivate Code
async function deactivateCode(id) {
    if (!confirm('Are you sure you want to deactivate this code?')) {
        return;
    }

    try {
        const response = await fetch('/api/deactivate_code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ codeId: id })
        });

        const data = await response.json();

        if (data.success) {
            alert('Code deactivated successfully!');
            loadCodes();
            loadStats();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error deactivating code: ' + error.message);
    }
}

// Reactivate Code
async function reactivateCode(id, currentMaxUses) {
    const newMaxUses = prompt(`Enter new max uses (current: ${currentMaxUses}):`, currentMaxUses);

    if (newMaxUses !== null) {
        const maxUses = parseInt(newMaxUses) || currentMaxUses;

        try {
            const response = await fetch('/api/reactivate_code.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    codeId: id,
                    newMaxUses: maxUses
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Code reactivated successfully!');
                loadCodes();
                loadStats();
            } else {
                alert('Error: ' + data.error);
            }
        } catch (error) {
            alert('Error reactivating code: ' + error.message);
        }
    }
}

// Delete Code
async function deleteCode(id) {
    if (!confirm('Are you sure you want to delete this code?')) {
        return;
    }

    try {
        const response = await fetch(`/api/delete_code.php?id=${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            alert('Code deleted successfully!');
            loadCodes();
            loadStats();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error deleting code: ' + error.message);
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('employee-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#employees-table tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
