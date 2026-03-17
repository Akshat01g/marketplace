<?php include 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];
    $addr = $_POST['addr'];
    $shop = $_POST['shop'];

    $sql = "INSERT INTO users (name, mobile, password, address, shop_name, role) 
            VALUES ('$name', '$mobile', '$pass', '$addr', '$shop', '$role')";
    
    if($conn->query($sql)) {
        header("Location: login.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Marketplace</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reg-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        /* Top Accent Bar */
        .reg-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 5px;
            background: #764ba2;
        }

        .lang-toggle {
            text-align: right;
            margin-bottom: 10px;
        }

        .lang-toggle a {
            text-decoration: none;
            font-size: 13px;
            color: #764ba2;
            font-weight: bold;
            border: 1px solid #764ba2;
            padding: 4px 10px;
            border-radius: 15px;
            transition: 0.3s;
        }

        .lang-toggle a:hover { background: #764ba2; color: white; }

        h2 { color: #333; margin-bottom: 10px; font-size: 28px; }
        p { color: #777; margin-bottom: 25px; font-size: 14px; }

        .form-group { margin-bottom: 18px; }

        label { display: block; margin-bottom: 5px; color: #555; font-size: 13px; font-weight: 600; }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #eee;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
            transition: 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #667eea;
            background-color: #fcfdff;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #764ba2;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #667eea;
            transform: translateY(-2px);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }

        .login-link a { color: #764ba2; text-decoration: none; font-weight: bold; }

        /* Smooth reveal for shop name */
        #shop_input_container {
            display: none;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="reg-container">
    <div class="lang-toggle">
        <a href="?lang=<?php echo $_SESSION['lang']=='en'?'hi':'en'; ?>">
            <?php echo $_SESSION['lang']=='en'?'हिन्दी में बदलें':'Switch to English'; ?>
        </a>
    </div>

    <h2><?php echo $_SESSION['lang']=='en'?'Create Account':'खाता बनाएं'; ?></h2>
    <p><?php echo $_SESSION['lang']=='en'?'Join our local marketplace today':'आज ही हमारे स्थानीय बाज़ार से जुड़ें'; ?></p>

    <form method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="John Doe" required>
        </div>

        <div class="form-group">
            <label>Mobile Number</label>
            <input type="text" name="mobile" placeholder="9876543210" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="pass" placeholder="••••••••" required>
        </div>

        <div class="form-group">
            <label>Register As</label>
            <select name="role" id="role" onchange="checkRole()">
                <option value="customer">Customer (Buyer)</option>
                <option value="distributor">Shopowner (Seller)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="addr" placeholder="Enter your full address" rows="2"></textarea>
        </div>

        <div class="form-group" id="shop_input_container">
            <label>Shop Name</label>
            <input type="text" name="shop" placeholder="e.g. Sharma Kirana Store">
        </div>

        <button type="submit" class="btn-submit">Register Now</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

<script>
    function checkRole() {
        var role = document.getElementById('role').value;
        var shopContainer = document.getElementById('shop_input_container');
        if (role === 'distributor') {
            shopContainer.style.display = 'block';
        } else {
            shopContainer.style.display = 'none';
        }
    }
</script>

</body>
</html>