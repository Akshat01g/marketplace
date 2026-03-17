<?php 
include 'config.php'; 

// 1. Security Check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace | Local Shop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f0f2f5; padding: 15px; color: #333; }

        /* Header & Profile */
        .profile-card {
            background: white; padding: 20px; border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px;
            display: flex; justify-content: space-between; align-items: center;
            border-left: 6px solid #764ba2;
        }
        .user-meta h4 { font-size: 18px; margin-bottom: 5px; }
        .user-meta p { font-size: 13px; color: #666; }

        /* Search Section */
        .search-box {
            display: flex; gap: 10px; margin-bottom: 30px; justify-content: center;
        }
        .search-box input {
            width: 100%; max-width: 500px; padding: 12px 20px; 
            border-radius: 30px; border: 2px solid #ddd; outline: none;
        }
        .search-box button {
            background: #764ba2; color: white; border: none; padding: 0 25px;
            border-radius: 30px; cursor: pointer; font-weight: bold;
        }

        /* Product Grid */
        .product-grid { 
            display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); 
            gap: 20px; margin-bottom: 40px;
        }
        .product-card { 
            background: white; border-radius: 15px; overflow: hidden; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s; position: relative;
        }
        .product-card:hover { transform: translateY(-5px); }
        
        .pack-badge {
            position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9);
            padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: bold; color: #764ba2;
        }

        .product-img { width: 100%; height: 180px; object-fit: cover; }
        .details { padding: 15px; }
        .price { font-size: 20px; font-weight: bold; color: #764ba2; margin: 8px 0; }
        .stock-text { font-size: 12px; color: #28a745; margin-bottom: 10px; }

        .buy-btn {
            display: block; background: #764ba2; color: white; text-align: center;
            text-decoration: none; padding: 10px; border-radius: 10px; font-weight: bold;
        }

        /* History Table */
        .history-card { background: white; padding: 20px; border-radius: 15px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #888; padding: 12px; border-bottom: 2px solid #eee; font-size: 13px; }
        td { padding: 12px; border-bottom: 1px solid #f9f9f9; font-size: 14px; }
    </style>
</head>
<body>

    <div class="profile-card">
        <div class="user-meta">
            <h4>👋 Welcome, <?php echo $_SESSION['name']; ?></h4>
            <p>📍 Delivery to: <b><?php echo $_SESSION['address']; ?></b></p>
        </div>
        <a href="logout.php" style="color: #dc3545; text-decoration: none; font-weight: bold;">Logout</a>
    </div>

    <form class="search-box" method="GET">
        <input type="text" name="search" placeholder="Search for milk, dahi, or shops..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>

    <h3 style="margin-bottom: 15px;">🛒 Nearby Products</h3>
    <div class="product-grid">
        <?php
        // Search logic: Filtering by name or shop
        $sql = "SELECT * FROM products WHERE is_available = 1";
        if($search_query != "") {
            $sql .= " AND (name_en LIKE '%$search_query%' OR name_hi LIKE '%$search_query%' OR shop_name LIKE '%$search_query%')";
        }
        $sql .= " ORDER BY id DESC";
        
        $res = $conn->query($sql);
        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                $name = ($_SESSION['lang'] == 'hi') ? $row['name_hi'] : $row['name_en'];
                $img = !empty($row['image']) ? "uploads/".$row['image'] : "https://via.placeholder.com/200";
                
                echo "
                <div class='product-card'>
                    <div class='pack-badge'> {$row['unit']}</div>
                    <img src='$img' class='product-img'>
                    <div class='details'>
                        <h4 style='font-size: 16px;'>$name</h4>
                        <p style='font-size: 12px; color:#777;'>🏪 {$row['shop_name']}</p>
                        <p class='price'>₹" . number_format($row['price'], 2) . "</p>
                        <p class='stock-text'>Only {$row['stock_units']} units left!</p>
                        <a href='process_buy.php?id={$row['id']}' class='buy-btn'>Buy Now</a>
                    </div>
                </div>";
            }
        } else {
            echo "<p style='padding:20px; color:#999;'>No products found.</p>";
        }
        ?>
    </div>

    <h3 style="margin-bottom: 15px;">📜 Your Recent Orders</h3>
    <div class="history-card">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Shop</th>
                    <th>Price</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $my_orders = $conn->query("SELECT orders.*, products.name_en, products.name_hi 
                                          FROM orders 
                                          JOIN products ON orders.product_id = products.id 
                                          WHERE orders.customer_id = $uid 
                                          ORDER BY orders.order_date DESC");

                if($my_orders->num_rows > 0) {
                    while($order = $my_orders->fetch_assoc()) {
                        $p_name = ($_SESSION['lang'] == 'hi') ? $order['name_hi'] : $order['name_en'];
                        echo "<tr>
                                <td><b>$p_name</b></td>
                                <td>{$order['shop_name']}</td>
                                <td>₹{$order['price']}</td>
                                <td>" . date('d M, Y', strtotime($order['order_date'])) . "</td>
                                <td style='color:green; font-weight:bold;'>✓ Paid</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:#999;'>No orders found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>