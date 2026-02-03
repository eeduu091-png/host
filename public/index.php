<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maayash Communications - Employee ID Card Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <h1>MAAYASH COMMUNICATIONS</h1>
                <p class="subtitle">Employee ID Card Portal</p>
                <p class="contractor">Contractor for: <strong>SAFARICOM</strong></p>
            </div>
        </header>

        <!-- Progress Steps -->
        <div class="progress-container">
            <div class="progress-step active" id="step1-indicator">
                <div class="step-number">1</div>
                <div class="step-label">Search</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step" id="step2-indicator">
                <div class="step-number">2</div>
                <div class="step-label">Select</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step" id="step3-indicator">
                <div class="step-number">3</div>
                <div class="step-label">Upload</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step" id="step4-indicator">
                <div class="step-number">4</div>
                <div class="step-label">Pay</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step" id="step5-indicator">
                <div class="step-number">5</div>
                <div class="step-label">Download</div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Step 1: Search Employee -->
            <div class="step-content active" id="step1">
                <div class="card">
                    <div class="card-header">
                        <h2>🔍 Find Your Profile</h2>
                        <p>Enter the first 5 digits of your ID number to search</p>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="id-digits">First 5 Digits of ID Number</label>
                            <input type="text" id="id-digits" maxlength="5" placeholder="e.g., 12345" />
                            <small class="hint">We'll show you matching profiles to select from</small>
                        </div>
                        <button class="btn btn-primary" onclick="searchEmployee()">
                            <span class="btn-text">Search</span>
                            <span class="btn-loading" style="display: none;">Searching...</span>
                        </button>
                        <div id="search-results" class="search-results"></div>
                        <div id="search-error" class="error-message"></div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Verify Details -->
            <div class="step-content" id="step2">
                <div class="card">
                    <div class="card-header">
                        <h2>✓ Verify Your Details</h2>
                        <p>Please confirm your information is correct</p>
                    </div>
                    <div class="card-body">
                        <div class="employee-details" id="employee-details"></div>
                        <div class="button-group">
                            <button class="btn btn-secondary" onclick="goToStep(1)">Back</button>
                            <button class="btn btn-primary" onclick="goToStep(3)">Continue</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Upload Photo -->
            <div class="step-content" id="step3">
                <div class="card">
                    <div class="card-header">
                        <h2>📷 Upload Your Photo</h2>
                        <p>Upload a clear passport-size photo for your ID card</p>
                    </div>
                    <div class="card-body">
                        <div class="upload-area" id="upload-area">
                            <input type="file" id="photo-input" accept="image/jpeg,image/png,image/jpg" hidden />
                            <div class="upload-placeholder">
                                <div class="upload-icon">📷</div>
                                <p>Click or drag photo here</p>
                                <small>JPEG or PNG, max 5MB</small>
                            </div>
                            <div class="photo-preview" id="photo-preview" style="display: none;">
                                <img id="preview-image" src="" alt="Preview" />
                                <button class="btn-remove" onclick="removePhoto()">✕</button>
                            </div>
                        </div>
                        <div class="button-group">
                            <button class="btn btn-secondary" onclick="goToStep(2)">Back</button>
                            <button class="btn btn-primary" onclick="goToStep(4)" id="continue-to-payment" disabled>Continue</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Choose Download Method -->
            <div class="step-content" id="step4">
                <div class="card">
                    <div class="card-header">
                        <h2>💳 Choose Download Method</h2>
                        <p>Pay KES 50 via M-Pesa or use a download code</p>
                    </div>
                    <div class="card-body">
                        <div class="payment-methods">
                            <div class="payment-option" id="payment-option" onclick="selectPaymentMethod('mpesa')">
                                <div class="option-icon">💳</div>
                                <div class="option-content">
                                    <h3>M-Pesa Payment</h3>
                                    <p>Pay KES 50 via M-Pesa STK Push</p>
                                    <strong>KES 50.00</strong>
                                </div>
                            </div>

                            <div class="payment-option" id="code-option" onclick="selectPaymentMethod('code')">
                                <div class="option-icon">🎟️</div>
                                <div class="option-content">
                                    <h3>Download Code</h3>
                                    <p>Enter a valid admin-provided code</p>
                                    <strong>FREE</strong>
                                </div>
                            </div>
                        </div>

                        <!-- M-Pesa Form -->
                        <div id="mpesa-form" class="payment-form" style="display: none;">
                            <div class="form-group">
                                <label for="phone-number">M-Pesa Phone Number</label>
                                <input type="tel" id="phone-number" placeholder="0712345678" maxlength="10" />
                                <small class="hint">Enter the number registered with M-Pesa</small>
                            </div>
                            <div class="payment-info">
                                <p><strong>Payment Details:</strong></p>
                                <p>• Amount: <strong>KES 50</strong></p>
                                <p>• Till Number: <strong>6604923</strong></p>
                                <p>• Till Name: <strong>BUY GOODS GREEN COLOR NETWORKS</strong></p>
                            </div>
                            <button class="btn btn-primary" onclick="initiatePayment()" id="pay-button">
                                <span class="btn-text">Pay KES 50</span>
                                <span class="btn-loading" style="display: none;">Processing...</span>
                            </button>
                            <div id="payment-status" class="payment-status"></div>
                        </div>

                        <!-- Download Code Form -->
                        <div id="code-form" class="payment-form" style="display: none;">
                            <div class="form-group">
                                <label for="download-code">Download Code</label>
                                <input type="text" id="download-code" placeholder="AB12-CD34" maxlength="9" />
                                <small class="hint">Enter the 8-character code provided by admin</small>
                            </div>
                            <button class="btn btn-primary" onclick="validateCode()">
                                <span class="btn-text">Validate Code</span>
                                <span class="btn-loading" style="display: none;">Validating...</span>
                            </button>
                            <div id="code-status" class="payment-status"></div>
                        </div>

                        <div class="button-group">
                            <button class="btn btn-secondary" onclick="goToStep(3)">Back</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Download ID Card -->
            <div class="step-content" id="step5">
                <div class="card">
                    <div class="card-header">
                        <h2>🎉 Your ID Card is Ready!</h2>
                        <p>Download your work ID card</p>
                    </div>
                    <div class="card-body">
                        <div class="id-card-preview" id="id-card-preview">
                            <img id="generated-card" src="" alt="ID Card" />
                        </div>
                        <div class="button-group">
                            <button class="btn btn-success" onclick="downloadIDCard()">
                                <span>⬇️ Download ID Card</span>
                            </button>
                            <button class="btn btn-secondary" onclick="location.reload()">Start Over</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2025 Maayash Communications. Contractor for Safaricom.</p>
            <p>All rights reserved.</p>
        </footer>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay" style="display: none;">
        <div class="spinner"></div>
        <p>Generating your ID card...</p>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
