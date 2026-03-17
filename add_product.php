<?php include 'config.php'; 
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'distributor') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name_en = mysqli_real_escape_string($conn, $_POST['name_en']);
    $name_hi = mysqli_real_escape_string($conn, $_POST['name_hi']);
    $price   = $_POST['price'];
    $unit    = mysqli_real_escape_string($conn, $_POST['unit']); 
    $shop    = $_SESSION['shop_name'];

    $target_dir = "uploads/";
    $file_ext = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
    $image_name = time() . "_" . rand(100, 999) . "." . $file_ext;
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
        
        $sql = "INSERT INTO products (name_en, name_hi, price, unit, image, is_available, shop_name) 
                VALUES ('$name_en', '$name_hi', '$price', '$unit', '$image_name', 1, '$shop')";
        
        if($conn->query($sql)) {
            echo "<script>alert('Product listed successfully!'); window.location='dashboard.php';</script>";
        } else {
            echo "Database Error: " . $conn->error;
        }
    } else {
        echo "<script>alert('Failed to upload image. Check if uploads folder exists.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product | Seller Hub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .card {
            background: white; padding: 30px; border-radius: 20px;
            width: 100%; max-width: 450px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        h2 { color: #333; margin-bottom: 20px; text-align: center; }
        .input-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600; color: #555; }
        input {
            width: 100%; padding: 12px; border: 2px solid #eee;
            border-radius: 10px; outline: none; transition: 0.3s;
        }
        input:focus { border-color: #764ba2; background: #fcfdff; }
        .btn-submit {
            width: 100%; padding: 14px; background: #764ba2; color: white;
            border: none; border-radius: 10px; font-size: 16px; font-weight: 600;
            cursor: pointer; transition: 0.3s; margin-top: 10px;
        }
        .btn-submit:hover { background: #667eea; transform: translateY(-2px); }
        .footer-link { display: block; text-align: center; margin-top: 15px; color: #764ba2; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Listing New Item</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="input-group">
            <label>Product Name (English)</label>
            <input type="text" name="name_en" placeholder="e.g. Fresh Mango" required>
        </div>

        <div class="input-group">
            <label>Product Name (Hindi - Copy Paste)</label>
            <input type="text" name="name_hi" placeholder="जैसे: ताज़ा आम" required>
        </div>

        <div class="input-group">
            <label>Price (₹)</label>
            <input type="number" step="0.01" name="price" placeholder="Price in Rupees" required>
        </div>

        <div class="input-group">
            <label>Quantity / Unit (Weight or Vol.)</label>
            <input type="text" name="unit" placeholder="e.g. 1kg, 500ml, 1 Dozen" required>
        </div>

        <div class="input-group">
            <label>Product Image</label>
            <input type="file" name="product_image" accept="image/*" required>
        </div>

        <button type="submit" class="btn-submit">Add to My Shop</button>
        <a href="dashboard.php" class="footer-link">← Back to Dashboard</a>
    </form>
</div>

</body>
</html>