<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Maayash Communications</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #009933 0%, #006622 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .login-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #009933;
            box-shadow: 0 0 0 3px rgba(0, 153, 51, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #009933;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #006622;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 153, 51, 0.3);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .error-message {
            margin-top: 15px;
            padding: 12px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 8px;
            border: 1px solid #ef4444;
            display: none;
            text-align: center;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #009933;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Admin Login</h1>
                <p>Maayash Communications</p>
            </div>
            <div class="login-body">
                <form id="login-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email" />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password" />
                    </div>
                    <button type="submit" class="btn-login" id="login-btn">
                        <span class="btn-text">Sign In</span>
                        <span class="btn-loading" style="display: none;">Signing in...</span>
                    </button>
                </form>
                <div id="error-message" class="error-message"></div>
                <div class="back-link">
                    <a href="../index.php">← Back to Employee Portal</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const btn = document.getElementById('login-btn');
            const btnText = btn.querySelector('.btn-text');
            const btnLoading = btn.querySelector('.btn-loading');
            const errorDiv = document.getElementById('error-message');

            // Show loading
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            errorDiv.style.display = 'none';

            try {
                const response = await fetch('/api/admin_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (data.success) {
                    // Store admin data in localStorage
                    localStorage.setItem('admin', JSON.stringify(data.admin));

                    // Redirect to dashboard
                    window.location.href = 'dashboard.php';
                } else {
                    errorDiv.textContent = data.error || 'Login failed. Please try again.';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.style.display = 'block';
                console.error('Login error:', error);
            }

            // Hide loading
            btn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        });
    </script>
</body>
</html>
