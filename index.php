<?php 
include 'config.php'; 

// Agar user pehle se logged in hai, toh use login page nahi dikhana
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'distributor') {
        header("Location: dashboard.php");
    } else {
        header("Location: market.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Database se user fetch karna
    $sql = "SELECT * FROM users WHERE mobile = '$mobile' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // --- SESSION DATA STORAGE ---
        // Yeh data market.php aur dashboard.php mein kaam aayega
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['mobile'] = $user['mobile'];
        $_SESSION['address'] = $user['address']; 
        $_SESSION['role'] = $user['role'];
        $_SESSION['shop_name'] = $user['shop_name'];

        // --- ROLE-BASED REDIRECT ---
        if ($user['role'] == 'distributor') {
            header("Location: dashboard.php");
        } else {
            header("Location: market.php");
        }
        exit();
    } else {
        $error = "Invalid Mobile Number or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Local Marketplace</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 { color: #333; margin-bottom: 10px; font-size: 28px; }
        p { color: #777; margin-bottom: 30px; }

        .input-group { margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px; }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #eee;
            border-radius: 12px;
            outline: none;
            transition: 0.3s;
            font-size: 16px;
        }

        input:focus { border-color: #764ba2; }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: #764ba2;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
        }

        .login-btn:hover { background: #667eea; transform: translateY(-2px); }

        .error-msg {
            background: #fee2e2;
            color: #dc2626;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .register-link { margin-top: 25px; font-size: 14px; color: #777; }
        .register-link a { color: #764ba2; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Welcome Back</h2>
    <p>Please enter your details to login</p>

    <?php if($error != ""): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Mobile Number</label>
            <input type="text" name="mobile" placeholder="Enter your registered mobile" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="login-btn">Login to Account</button>
    </form>

    <div class="register-link">
        Don't have an account? <a href="register.php">Create one now</a>
    </div>
</div>

</body>
</html>