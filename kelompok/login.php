<?php
    session_start();
    if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
        header("Location: dashboard.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orange Labo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Lazydog';
            src: url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/webfonts/lazy-dog.woff2') format('woff2'); 
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #FFF1E6;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .title {
            font-size: 85px;
            font-family: 'Lazydog', cursive;
            margin-bottom: 5px;
            line-height: 1;
            color: #000;
        }

        .subtitle {
            font-size: 24px;
            font-family: 'Poppins', sans-serif;
            font-weight: bold;
        }

        .login-container {
            display: flex;
            border-radius: 15px;
            overflow: visible;
            width: 100%;
            max-width: 1000px;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .login-left {
            flex: 1;
            background-color: #FFF;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            border-radius: 15px 0 0 15px;
        }

        .login-right {
            flex: 1;
            background-color: #FF6B00;
            padding: 40px;
            position: relative;
            border-radius: 0 15px 15px 0;
        }

        .logo-circle {
            width: 100px;
            height: 100px;
            background-color: #FF6B00;
            border-radius: 50%;
            margin-bottom: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .star-icon {
            width: 50px;
            height: 50px;
        }

        .login-text {
            font-size: 24px;
            font-family: 'Poppins', sans-serif;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 25px;
            width: 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 20px;
            color: #FFF;
            font-family: 'Poppins', sans-serif;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            background-color: #FFF;
            font-family: 'Poppins', sans-serif;
        }

        .login-button-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .login-button {
            background-color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 16px;
            transition: 0.3s;
        }

        .login-button:hover {
            background-color: #f0f0f0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .arrow-icon {
            width: 20px;
            height: 14px;
            margin-left: 5px;
        }

        .error-message {
            color: #FFF;
            background-color: rgba(255, 0, 0, 0.7);
            padding: 8px 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .mascot {
            position: absolute;
            right: -90px;
            bottom: -90px;
            width: 170px;
            height: auto;
            z-index: 10;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 20px;
            color: #333;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
        }

        /* Notification styling */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
        }

        .notification.error {
            background-color: #F44336;
        }

        /* Loading indicator */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .login-left, .login-right {
                width: 100%;
            }
            .login-left {
                border-radius: 15px 15px 0 0;
            }
            .login-right {
                border-radius: 0 0 15px 15px;
            }
            .mascot {
                right: 20px;
                bottom: -60px;
                width: 120px;
            }
            .title {
                font-size: 60px;
            }
            .subtitle {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">ORANGE LABO</h1>
        <p class="subtitle">Co - Working Space</p>
    </div>

    <div class="login-container">
        <div class="login-left">
            <div class="logo-circle">
                <svg class="star-icon" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2">
                    <path d="M12 1.5l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.94l-6.18 3.08L7 14.14 2 9.27l6.91-1.01L12 1.5z"></path>
                </svg>
            </div>
            <p class="login-text">Silahkan masuk</p>
        </div>
        <div class="login-right">
            <div id="error-container" style="display: none;" class="error-message"></div>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="login-button-container">
                    <button type="submit" class="login-button" id="loginBtn">
                        Masuk
                        <svg class="arrow-icon" viewBox="0 0 24 12" fill="none" stroke="black" stroke-width="2">
                            <line x1="1" y1="6" x2="18" y2="6"></line>
                            <polyline points="13,1 18,6 13,11"></polyline>
                        </svg>
                        <span class="loading-spinner" style="display: none;"></span>
                    </button>
                </div>
                <input type="hidden" name="ajax" value="1">
            </form>
        </div>
        <img class="mascot" src="Mascot Pemweb.PNG" alt="Orange Labo mascot character">
    </div>

    <div class="footer">
        <p>"Do your task with us"</p>
    </div>

    <script src="ajax.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const loadingSpinner = loginBtn.querySelector('.loading-spinner');
            const errorContainer = document.getElementById('error-container');
            
            const urlParams = new URLSearchParams(window.location.search);
            const errorMsg = urlParams.get('error');
            if (errorMsg) {
                errorContainer.textContent = errorMsg;
                errorContainer.style.display = 'block';
            }
            
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                errorContainer.style.display = 'none';
                
                loginBtn.disabled = true;
                loadingSpinner.style.display = 'inline-block';
                
                const formData = new FormData(loginForm);
                
                ajaxFormSubmit('action_login.php', formData, 
                    function(response) {
                        if (response.success) {
                            window.location.href = 'dashboard.php';
                        } else {
                            errorContainer.textContent = response.message || 'Username atau password salah';
                            errorContainer.style.display = 'block';
                            
                            loginBtn.disabled = false;
                            loadingSpinner.style.display = 'none';
                        }
                    },
                    function(status, statusText) {
                        errorContainer.textContent = 'Terjadi kesalahan: ' + status + ' ' + statusText;
                        errorContainer.style.display = 'block';
                        
                        loginBtn.disabled = false;
                        loadingSpinner.style.display = 'none';
                    }
                );
            });
        });
    </script>
</body>
</html>