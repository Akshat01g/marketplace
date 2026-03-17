<?php 
include 'config.php'; 

// Security: Only Admin allowed
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch Stats for the Top Boxes
$total_shops = $conn->query("SELECT COUNT(DISTINCT shop_name) as total FROM products")->fetch_assoc()['total'];
$total_customers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'")->fetch_assoc()['total'];
$total_sales = $conn->query("SELECT SUM(price) as total FROM orders")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Master Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f0f2f5; display: flex; }

        /* Admin Sidebar */
        .sidebar { width: 260px; background: #1e293b; color: white; min-height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { color: #38bdf8; margin-bottom: 30px; text-align: center; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 5px; }
        .sidebar a:hover { background: #334155; color: white; }

        .main { flex: 1; margin-left: 260px; padding: 40px; }

        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .card h3 { color: #64748b; font-size: 14px; text-transform: uppercase; }
        .card p { font-size: 28px; font-weight: bold; color: #1e293b; margin-top: 10px; }

        /* Table Styling */
        .table-box { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f8fafc; border-bottom: 2px solid #edf2f7; color: #475569; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-shop { background: #e0f2fe; color: #0369a1; }
        .badge-user { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="#">🏠 Dashboard Overview</a>
    <a href="#">🏪 Registered Shops</a>
    <a href="#">👥 All Customers</a>
    <a href="#">📦 All Orders</a>
    <a href="logout.php" style="margin-top: 50px; color: #fb7185;">🚪 Logout System</a>
</div>

<div class="main">
    <h1 style="margin-bottom: 25px;">Platform Overview</h1>

    <div class="stats-grid">
        <div class="card">
            <h3>Total Active Shops</h3>
            <p><?php echo $total_shops; ?></p>
        </div>
        <div class="card">
            <h3>Registered Customers</h3>
            <p><?php echo $total_customers; ?></p>
        </div>
        <div class="card">
            <h3>Total Platform Revenue</h3>
            <p>₹<?php echo number_format($total_sales, 2); ?></p>
        </div>
    </div>

    <div class="table-box">
        <h2 style="margin-bottom: 15px; font-size: 18px;">🏪 Shops & Inventory Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Shop Name</th>
                    <th>Total Items</th>
                    <th>Low Stock Items</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $shops_query = $conn->query("SELECT shop_name, COUNT(*) as items, 
                                            SUM(CASE WHEN stock_units < 5 THEN 1 ELSE 0 END) as low_stock 
                                            FROM products GROUP BY shop_name");
                while($s = $shops_query->fetch_assoc()) {
                    echo "<tr>
                            <td><strong>{$s['shop_name']}</strong></td>
                            <td>{$s['items']} Products</td>
                            <td style='color:red;'>{$s['low_stock']} Items</td>
                            <td><span class='badge badge-shop'>Verified</span></td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="table-box">
        <h2 style="margin-bottom: 15px; font-size: 18px;">📦 Global Order History</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Shop</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $all_orders = $conn->query("SELECT orders.*, users.name 
                                           FROM orders JOIN users ON orders.customer_id = users.id 
                                           ORDER BY orders.order_date DESC LIMIT 10");
                while($o = $all_orders->fetch_assoc()) {
                    echo "<tr>
                            <td>#ORD-{$o['id']}</td>
                            <td>{$o['name']}</td>
                            <td><span class='badge badge-user'>{$o['shop_name']}</span></td>
                            <td>₹{$o['price']}</td>
                            <td>" . date('d M, h:i A', strtotime($o['order_date'])) . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>