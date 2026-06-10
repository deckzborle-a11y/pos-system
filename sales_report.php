<?php
include 'db.php';

$today = date('Y-m-d');
$result_today = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as today_sales FROM orders WHERE DATE(created_at) = '$today'");
$today_data = $result_today->fetch_assoc();

$result_count = $conn->query("SELECT COUNT(*) as total FROM orders");
$count_data = $result_count->fetch_assoc();

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            padding: 20px; 
            background: var(--light, #f5f5f5); 
        }
        .container { 
            max-width: 900px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 { 
            color: var(--dark, #2c3e50); 
            margin-bottom: 20px;
        }
        .stats { 
            display: flex; 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .stat { 
            flex: 1; 
            background: linear-gradient(135deg, #4a90e2, #357abd); 
            color: white; 
            padding: 25px; 
            border-radius: 10px; 
            text-align: center;
        }
        .stat h3 { 
            margin: 0; 
            font-size: 14px; 
            opacity: 0.9; 
        }
        .stat p { 
            margin: 10px 0 0; 
            font-size: 28px; 
            font-weight: bold; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th, td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid var(--light, #ecf0f1); 
        }
        th { 
            background: var(--light, #ecf0f1); 
            font-weight: 600;
            color: var(--dark, #2c3e50);
        }
        tr:hover {
            background: #f9f9f9;
        }
        .btn-back { 
            padding: 12px 25px; 
            background: #27ae60; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn-back:hover {
            background: #219a52;
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Sales Report</h1>
        
        <div class="stats">
            <div class="stat">
                <h3>Today's Sales</h3>
                <p>$<?php echo number_format($today_data['today_sales'], 2); ?></p>
            </div>
            <div class="stat">
                <h3>Total Orders</h3>
                <p><?php echo $count_data['total']; ?></p>
            </div>
        </div>
        
        <h2>Order History</h2>
        
        <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Payment</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                    <td><?php echo $row['customer_name'] ?: 'Guest'; ?></td>
                    <td><strong>$<?php echo number_format($row['total_amount'], 2); ?></strong></td>
                    <td>-$<?php echo number_format($row['discount'], 2); ?></td>
                    <td>$<?php echo number_format($row['tax'], 2); ?></td>
                    <td><?php echo ucfirst($row['payment_method']); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty">
            <p>No orders yet. Make your first sale!</p>
        </div>
        <?php endif; ?>
        
        <button class="btn-back" onclick="window.location.href='index.php'">
            ← Back to POS
        </button>
    </div>
</body>
</html>