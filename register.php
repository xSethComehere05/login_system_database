<?php
require __DIR__ . '/config_mysqli.php';
require __DIR__ . '/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Copied styles from login.php */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to bottom, #a1c4fd, #c2e9fb); 
            background-size: cover;
            background-position: center;
            padding: 20px 0; 
        }
        .login-container {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 450px; 
        }
        .login-container h2 {
            margin-bottom: 30px;
            font-size: 28px;
            color: #333;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: none;
            background-color: #f5f5f5;
            border-radius: 4px;
            font-size: 16px;
            color: #333;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sign-in-arrow-btn {
            background-color: #f5f5f5;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 30px auto;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border: none;
        }
        .sign-in-arrow-btn:hover { background-color: #e0e0e0; }
        .sign-in-arrow-btn i { color: #555; font-size: 24px; }
        .links { font-size: 13px; color: #777; margin-top: 15px; }
        .links a {
            color: #777;
            text-decoration: none;
            margin: 0 5px;
            transition: color 0.3s ease;
        }
        .links a:hover { color: #333; }
        .demo-text {
            margin-top: 20px;
            font-size: 12px;
            color: #555;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Create Account</h2> 

        <?php if (!empty($_SESSION['flash'])): ?>
          <div class="alert alert-danger py-2">
            <?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
          </div>
        <?php endif; ?>
        
        <form method="post" action="register_process.php" novalidate>
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            
            <div class="form-group">
                <label for="fullname">Full Name</label> 
                <input class="form-control" type="text" id="fullname" name="fullname" placeholder="" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label> 
                <input class="form-control" type="email" id="email" name="email" placeholder="" required> 
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input class="form-control" type="password" id="password" name="password" placeholder="" required> 
            </div>

            <div class="mb-2" style="margin-top: -10px;"> <div class="progress" style="height: 8px;">
                    <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small id="password-strength-text" class="form-text float-end mt-1"></small>
            </div>
            <div class="form-group" style="margin-top: 20px;"> <label for="confirm_password">Confirm Password</label>
                <input class="form-control" type="password" id="confirm_password" name="confirm_password" placeholder="" required> 
            </div>

            <button class="sign-in-arrow-btn" type="submit">
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="links">
            <a href="login.php">SIGN IN</a>
        </div>
    </div>

    <div class="demo-text">
        👻 Web Demo for Database na kub.
    </div>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('password').addEventListener('keyup', function() {
    var password = this.value;
    var strengthBar = document.getElementById('password-strength-bar');
    var strengthText = document.getElementById('password-strength-text');

    // 1. Reset bar and text
    strengthBar.style.width = '0%';
    strengthBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');
    strengthText.innerHTML = '';
    strengthText.className = 'form-text float-end mt-1'; // Reset class

    if (password.length === 0) {
        return; // Exit if empty
    }

    // 2. Calculate score
    var score = 0;
    if (password.length >= 8) score++;    // มีความยาว 8+
    if (password.match(/[a-z]/)) score++; // มีตัวพิมพ์เล็ก
    if (password.match(/[A-Z]/)) score++; // มีตัวพิมพ์ใหญ่
    if (password.match(/[0-9]/)) score++; // มีตัวเลข
    if (password.match(/[^a-zA-Z0-9]/)) score++; // มีอักขระพิเศษ

    // 3. Update UI based on score
    switch (score) {
        case 0:
        case 1:
        case 2:
            strengthBar.style.width = '33%';
            strengthBar.classList.add('bg-danger');
            strengthText.innerHTML = 'ง่าย WEAK';
            strengthText.classList.add('text-danger');
            break;
        case 3:
        case 4:
            strengthBar.style.width = '66%';
            strengthBar.classList.add('bg-warning');
            strengthText.innerHTML = 'ปานกลาง MEDIUM';
            strengthText.classList.add('text-warning');
            break;
        case 5:
            strengthBar.style.width = '100%';
            strengthBar.classList.add('bg-success');
            strengthText.innerHTML = 'ยาก STRONG';
            strengthText.classList.add('text-success');
            break;
    }
});
</script>
</body>
</html>