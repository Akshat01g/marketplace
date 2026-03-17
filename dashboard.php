<?php 
include 'config.php'; 

// Security: Only distributors allowed
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'distributor') {
    header("Location: index.php");
    exit();
}

$shop = $_SESSION['shop_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller Dashboard | <?php echo $shop; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f4f7fe; color: #333; display: flex; min-height: 100vh; }

        .sidebar {
            width: 260px; background: linear-gradient(180deg, #764ba2 0%, #667eea 100%);
            color: white; padding: 30px 20px; display: flex; flex-direction: column; position: fixed; height: 100vh;
        }
        .sidebar h2 { font-size: 20px; margin-bottom: 40px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 15px; }
        .sidebar a {
            color: white; text-decoration: none; padding: 12px 15px; margin: 5px 0;
            border-radius: 10px; transition: 0.3s; display: block; font-size: 14px;
        }
        .sidebar a:hover { background: rgba(255, 255, 255, 0.1); transform: translateX(5px); }
        .sidebar a.active { background: white; color: #764ba2; font-weight: bold; }
        .logout-link { margin-top: auto; color: #ffcbd1 !important; font-weight: bold; }

        .main-content { flex: 1; padding: 40px; margin-left: 260px; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .add-btn {
            background: #764ba2; color: white; padding: 12px 24px; border-radius: 12px;
            text-decoration: none; font-weight: 600; transition: 0.3s; box-shadow: 0 4px 15px rgba(118, 75, 162, 0.2);
        }
        
        .table-container {
            background: white; border-radius: 15px; padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 40px;
        }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #475569; }
        
        .status-badge { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .product-img { width: 45px; height: 45px; border-radius: 8px; object-fit: cover; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><?php echo $shop; ?></h2>
    <a href="dashboard.php" class="active">📊 Sales Overview</a>
    <a href="add_product.php">➕ Add New Product</a>
    <a href="market.php">🌐 View Market</a> 
    <a href="logout.php" class="logout-link">🚪 Logout</a>
</div>

<div class="main-content">
    
    <div class="header-flex">
        <div>
            <h1>Customer Orders</h1>
            <p style="color: #666; font-size: 14px;">Recent sales from your shop</p>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Mobile</th>
                    <th>Address</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query WITH Aliases to avoid confusion
                $orders = $conn->query("SELECT 
                    orders.price AS order_price, 
                    orders.order_date, 
                    users.name AS customer_name, 
                    users.mobile, 
                    users.address 
                    FROM orders 
                    JOIN users ON orders.customer_id = users.id 
                    WHERE orders.shop_name = '$shop'
                    ORDER BY orders.order_date DESC");

                if ($orders && $orders->num_rows > 0) {
                    while($o = $orders->fetch_assoc()) {
                        echo "<tr>
                                <td><strong>{$o['customer_name']}</strong></td>
                                <td>{$o['mobile']}</td>
                                <td>" . substr($o['address'], 0, 30) . "...</td>
                                <td>₹" . number_format($o['order_price'], 2) . "</td>
                                <td>" . date('d M, Y', strtotime($o['order_date'])) . "</td>
                                <td><span class='status-badge'>Paid</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding: 30px; color: #999;'>No orders yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <hr style="border: none; border-top: 2px dashed #ddd; margin: 40px 0;">

    <div class="header-flex">
        <div>
            <h1>My Listed Products</h1>
            <p style="color: #666; font-size: 14px;">Inventory management</p>
        </div>
        <a href="add_product.php" class="add-btn">+ Add New Item</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Pack Size</th>
                    <th>Stock Units</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $my_items = $conn->query("SELECT * FROM products WHERE shop_name = '$shop' ORDER BY id DESC");
                if ($my_items && $my_items->num_rows > 0) {
                    while($p = $my_items->fetch_assoc()) {
                        $img_src = !empty($p['image']) ? "uploads/".$p['image'] : "https://via.placeholder.com/50";
                        echo "<tr>
                                <td><img src='$img_src' class='product-img'></td>
                                <td><strong>{$p['name_en']}</strong><br><small>{$p['name_hi']}</small></td>
                                <td> {$p['unit']}</td>
                                <td><strong>{$p['stock_units']}</strong> units</td>
                                <td>₹" . number_format($p['price'], 2) . "</td>
                                <td>" . ($p['is_available'] ? "<span class='status-badge'>Active</span>" : "<span style='color:red;'>Hidden</span>") . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding: 30px; color: #999;'>No products listed.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>