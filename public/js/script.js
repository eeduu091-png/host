/**
 * Maayash Communications ID Card Portal
 * Main JavaScript
 */

// Global state
let currentStep = 1;
let selectedEmployee = null;
let uploadedPhoto = null;
let paymentMethod = null;
let currentPaymentId = null;
let currentCodeId = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
});

function setupEventListeners() {
    // ID digits input - allow only numbers
    const idDigitsInput = document.getElementById('id-digits');
    idDigitsInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Phone number input - allow only numbers
    const phoneInput = document.getElementById('phone-number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // Download code input - auto-format
    const codeInput = document.getElementById('download-code');
    if (codeInput) {
        codeInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^A-Z0-9]/g, '').toUpperCase();
            if (value.length > 8) value = value.substring(0, 8);
            // Add dashes at positions 2 and 5
            if (value.length > 5) {
                value = value.substring(0, 2) + '-' + value.substring(2, 5) + '-' + value.substring(5);
            } else if (value.length > 2) {
                value = value.substring(0, 2) + '-' + value.substring(2);
            }
            this.value = value;
        });
    }

    // Photo upload area
    const uploadArea = document.getElementById('upload-area');
    const photoInput = document.getElementById('photo-input');

    uploadArea.addEventListener('click', function() {
        if (!uploadedPhoto) {
            photoInput.click();
        }
    });

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#009933';
        this.style.background = 'rgba(0, 153, 51, 0.05)';
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = '#e5e7eb';
        this.style.background = 'transparent';
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#e5e7eb';
        this.style.background = 'transparent';

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handlePhotoUpload(files[0]);
        }
    });

    photoInput.addEventListener('change', function(e) {
        if (this.files.length > 0) {
            handlePhotoUpload(this.files[0]);
        }
    });

    // Enter key on ID digits
    idDigitsInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchEmployee();
        }
    });
}

// Step Navigation
function goToStep(step) {
    // Hide current step
    document.getElementById('step' + currentStep).classList.remove('active');
    document.getElementById('step' + currentStep + '-indicator').classList.remove('active');

    // Show new step
    document.getElementById('step' + step).classList.add('active');
    document.getElementById('step' + step + '-indicator').classList.add('active');

    // Mark previous steps as completed
    for (let i = 1; i < step; i++) {
        document.getElementById('step' + i + '-indicator').classList.add('completed');
    }

    currentStep = step;
}

// Search Employee
async function searchEmployee() {
    const digits = document.getElementById('id-digits').value.trim();
    const resultsDiv = document.getElementById('search-results');
    const errorDiv = document.getElementById('search-error');
    const btn = event.target;
    const btnText = btn.querySelector('.btn-text');
    const btnLoading = btn.querySelector('.btn-loading');

    if (digits.length < 1) {
        showError('Please enter at least 1 digit');
        return;
    }

    // Show loading
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    errorDiv.style.display = 'none';
    resultsDiv.innerHTML = '';

    try {
        const response = await fetch('/api/search_employee.php?digits=' + encodeURIComponent(digits));
        const data = await response.json();

        if (data.success && data.employees.length > 0) {
            displaySearchResults(data.employees);
        } else {
            showError('No employees found matching these digits. Please try again.');
        }
    } catch (error) {
        showError('An error occurred while searching. Please try again.');
        console.error('Search error:', error);
    }

    // Hide loading
    btnText.style.display = 'inline';
    btnLoading.style.display = 'none';
}

function displaySearchResults(employees) {
    const resultsDiv = document.getElementById('search-results');

    let html = '<p style="margin-bottom: 15px; color: #6b7280;">Found ' + employees.length + ' matching profile(s). Select yours:</p>';

    employees.forEach(emp => {
        html += `
            <div class="employee-item" onclick="selectEmployee('${emp.id}', '${emp.employeeId}', '${emp.firstName}', '${emp.lastName}', '${emp.phone}', '${emp.role}', '${emp.region}', '${emp.department || ''}', '${emp.site || ''}')">
                <div class="employee-name">${emp.firstName} ${emp.lastName}</div>
                <div class="employee-info">
                    ID: ${emp.employeeId} | Phone: ${emp.phone} | Role: ${emp.role} | Region: ${emp.region}
                </div>
            </div>
        `;
    });

    resultsDiv.innerHTML = html;
}

function selectEmployee(id, employeeId, firstName, lastName, phone, role, region, department, site) {
    selectedEmployee = {
        id,
        employeeId,
        firstName,
        lastName,
        phone,
        role,
        region,
        department,
        site
    };

    // Display employee details
    const detailsDiv = document.getElementById('employee-details');
    detailsDiv.innerHTML = `
        <div class="detail-row">
            <div class="detail-label">Full Name:</div>
            <div class="detail-value">${firstName} ${lastName}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">ID Number:</div>
            <div class="detail-value">${employeeId}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Phone:</div>
            <div class="detail-value">${phone}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Role:</div>
            <div class="detail-value">${role}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Region:</div>
            <div class="detail-value">${region}</div>
        </div>
        ${department ? `
        <div class="detail-row">
            <div class="detail-label">Department:</div>
            <div class="detail-value">${department}</div>
        </div>
        ` : ''}
        ${site ? `
        <div class="detail-row">
            <div class="detail-label">Site:</div>
            <div class="detail-value">${site}</div>
        </div>
        ` : ''}
    `;

    goToStep(2);
}

// Photo Upload
function handlePhotoUpload(file) {
    // Validate file size
    if (file.size > 5 * 1024 * 1024) {
        alert('File too large. Maximum size is 5MB.');
        return;
    }

    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!validTypes.includes(file.type)) {
        alert('Invalid file type. Only JPEG and PNG allowed.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        uploadedPhoto = e.target.result;

        // Show preview
        document.querySelector('.upload-placeholder').style.display = 'none';
        document.getElementById('photo-preview').style.display = 'inline-block';
        document.getElementById('preview-image').src = uploadedPhoto;

        // Enable continue button
        document.getElementById('continue-to-payment').disabled = false;
    };
    reader.readAsDataURL(file);
}

function removePhoto() {
    uploadedPhoto = null;
    document.querySelector('.upload-placeholder').style.display = 'block';
    document.getElementById('photo-preview').style.display = 'none';
    document.getElementById('preview-image').src = '';
    document.getElementById('photo-input').value = '';
    document.getElementById('continue-to-payment').disabled = true;
}

// Payment Method Selection
function selectPaymentMethod(method) {
    paymentMethod = method;

    // Update UI
    document.querySelectorAll('.payment-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    document.getElementById(method + '-option').classList.add('selected');

    // Show appropriate form
    document.getElementById('mpesa-form').style.display = method === 'mpesa' ? 'block' : 'none';
    document.getElementById('code-form').style.display = method === 'code' ? 'block' : 'none';
}

// Initiate Payment
async function initiatePayment() {
    const phoneNumber = document.getElementById('phone-number').value.trim();
    const btn = document.getElementById('pay-button');
    const btnText = btn.querySelector('.btn-text');
    const btnLoading = btn.querySelector('.btn-loading');
    const statusDiv = document.getElementById('payment-status');

    if (phoneNumber.length !== 10 || !phoneNumber.startsWith('07')) {
        statusDiv.className = 'payment-status error';
        statusDiv.textContent = 'Please enter a valid phone number (format: 07XXXXXXXX)';
        statusDiv.style.display = 'block';
        return;
    }

    // Show loading
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    statusDiv.style.display = 'none';

    try {
        const response = await fetch('/api/initiate_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                employeeId: selectedEmployee.employeeId,
                phoneNumber: phoneNumber,
                amount: 50
            })
        });

        const data = await response.json();

        if (data.success) {
            currentPaymentId = data.paymentId;
            statusDiv.className = 'payment-status info';
            statusDiv.innerHTML = `<strong>✓ STK Push Sent!</strong><br>
                Please check your phone and enter your M-Pesa PIN.<br>
                <br>
                <strong>Payment Details:</strong><br>
                • Amount: KES ${data.amount}<br>
                • Till Number: ${data.tillNumber}<br>
                • Till Name: ${data.tillName}<br>
                <br>
                Waiting for payment confirmation...`;
            statusDiv.style.display = 'block';

            // Poll for payment status
            pollPaymentStatus(data.paymentId);
        } else {
            throw new Error(data.error || 'Payment initiation failed');
        }
    } catch (error) {
        statusDiv.className = 'payment-status error';
        statusDiv.textContent = 'Error: ' + error.message;
        statusDiv.style.display = 'block';
    }

    btnText.style.display = 'inline';
    btnLoading.style.display = 'none';
}

// Poll Payment Status
async function pollPaymentStatus(paymentId) {
    const maxAttempts = 30; // 30 seconds
    let attempts = 0;
    const statusDiv = document.getElementById('payment-status');

    const poll = setInterval(async () => {
        attempts++;

        try {
            const response = await fetch('/api/check_payment.php?payment_id=' + paymentId);
            const data = await response.json();

            if (data.success) {
                if (data.completed) {
                    clearInterval(poll);
                    statusDiv.className = 'payment-status success';
                    statusDiv.innerHTML = `<strong>✓ Payment Successful!</strong><br>Generating your ID card...`;
                    statusDiv.style.display = 'block';

                    // Generate ID card
                    setTimeout(() => generateIDCard('payment', paymentId, null), 1000);
                } else if (attempts >= maxAttempts) {
                    clearInterval(poll);
                    statusDiv.className = 'payment-status error';
                    statusDiv.textContent = 'Payment timeout. Please try again.';
                    statusDiv.style.display = 'block';
                }
            }
        } catch (error) {
            console.error('Poll error:', error);
        }
    }, 1000);
}

// Validate Download Code
async function validateCode() {
    const code = document.getElementById('download-code').value.trim();
    const btn = event.target;
    const btnText = btn.querySelector('.btn-text');
    const btnLoading = btn.querySelector('.btn-loading');
    const statusDiv = document.getElementById('code-status');

    if (code.length !== 9) {
        statusDiv.className = 'payment-status error';
        statusDiv.textContent = 'Please enter a valid 8-character code';
        statusDiv.style.display = 'block';
        return;
    }

    // Show loading
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    statusDiv.style.display = 'none';

    try {
        const response = await fetch('/api/validate_code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                code: code,
                employeeId: selectedEmployee.employeeId
            })
        });

        const data = await response.json();

        if (data.valid) {
            currentCodeId = data.codeId;
            statusDiv.className = 'payment-status success';
            statusDiv.innerHTML = `<strong>✓ Code Valid!</strong><br>Generating your ID card...`;
            statusDiv.style.display = 'block';

            // Generate ID card
            setTimeout(() => generateIDCard('code', null, data.codeId), 1000);
        } else {
            throw new Error(data.message || 'Invalid code');
        }
    } catch (error) {
        statusDiv.className = 'payment-status error';
        statusDiv.textContent = 'Error: ' + error.message;
        statusDiv.style.display = 'block';
    }

    btnText.style.display = 'inline';
    btnLoading.style.display = 'none';
}

// Generate ID Card
async function generateIDCard(downloadType, paymentId, codeId) {
    const overlay = document.getElementById('loading-overlay');
    overlay.style.display = 'flex';

    try {
        const response = await fetch('/api/generate_id.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                employeeId: selectedEmployee.id,
                photoData: uploadedPhoto,
                downloadType: downloadType,
                paymentId: paymentId,
                downloadCodeId: codeId
            })
        });

        const data = await response.json();

        if (data.success) {
            // Show generated card
            document.getElementById('generated-card').src = data.imageData;
            goToStep(5);
        } else {
            throw new Error(data.error || 'ID card generation failed');
        }
    } catch (error) {
        alert('Error generating ID card: ' + error.message);
        goToStep(4);
    }

    overlay.style.display = 'none';
}

// Download ID Card
function downloadIDCard() {
    const img = document.getElementById('generated-card');
    const link = document.createElement('a');
    link.download = `Maayash_ID_${selectedEmployee.employeeId}.png`;
    link.href = img.src;
    link.click();
}

// Show Error
function showError(message) {
    const errorDiv = document.getElementById('search-error');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}
