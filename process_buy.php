<?php
include 'config.php';

if(isset($_GET['id']) && $_SESSION['role'] == 'customer') {
    $p_id = $_GET['id'];
    $u_id = $_SESSION['user_id'];

    // 1. Check karein ki product stock mein hai ya nahi
    $check_product = $conn->query("SELECT * FROM products WHERE id = $p_id");
    $product = $check_product->fetch_assoc();

    if($product['stock_units'] > 0) {
        $price = $product['price'];
        $shop = $product['shop_name'];

        // 2. Order insert karein
        $sql_order = "INSERT INTO orders (product_id, customer_id, shop_name, price, status) 
                      VALUES ('$p_id', '$u_id', '$shop', '$price', 'Paid')";

        if($conn->query($sql_order)) {
            // 3. IMPORTANT: Stock ko 1 unit minus karein
            $conn->query("UPDATE products SET stock_units = stock_units - 1 WHERE id = $p_id");
            
            // 4. Agar stock 0 ho jaye toh product ko invisible (unavailable) kar dein
            $conn->query("UPDATE products SET is_available = 0 WHERE id = $p_id AND stock_units <= 0");

            echo "<script>alert('Order Placed! Stock Updated.'); window.location='market.php';</script>";
        }
    } else {
        echo "<script>alert('Sorry, this item is Out of Stock!'); window.location='market.php';</script>";
    }
}
?>