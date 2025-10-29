<?php
// --- START: Code from Block 2 ---
require __DIR__ . '/config_mysqli.php';
require __DIR__ . '/csrf.php';
// --- END: Code from Block 2 ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column; /* Changed to column to stack elements vertically */
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            
            background: linear-gradient(to bottom, #a1c4fd, #c2e9fb); 
            background-size: cover;
            background-position: center;
        }
        .login-container {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
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
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            justify-content: flex-start;
        }
        .remember-me input[type="checkbox"] {
            margin-right: 10px;
            width: auto;
            accent-color: #555;
        }
        .remember-me label {
            font-size: 14px;
            color: #555;
            margin-bottom: 0;
            text-transform: none;
            letter-spacing: normal;
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

        /* --- START: Added CSS for Demo Text --- */
        .demo-text {
            margin-top: 20px; /* Space between login box and demo text */
            font-size: 12px;
            color: #555; /* Darker color for readability */
            text-align: center;
        }
        /* --- END: Added CSS for Demo Text --- */
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Sign in</h2>

        <?php if (!empty($_SESSION['flash_success'])): ?>
          <div class="alert alert-success py-3">
            <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash'])): ?>
          <div class="alert alert-danger py-2">
            <?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
          </div>
        <?php endif; ?>
        
        <form method="post" action="login_process.php" novalidate>
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            
            <div class="form-group">
                <label for="email">Email</label> 
                <input type="email" id="email" name="email" placeholder="" required> 
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="" required> 
            </div>

            <button class="sign-in-arrow-btn" type="submit">
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
        <div class="links">
            <a href="#" onclick="alert('Please contact admin to reset your password');return false;">CAN'T SIGN IN?</a>
            <br>
            <a href="register.php">CREATE ACCOUNT</a>
        </div>
    </div>

    <div class="demo-text">
        👻 Web Demo for Database na kub.
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>