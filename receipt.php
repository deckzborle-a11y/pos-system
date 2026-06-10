<?php
session_start();
if (!isset($_SESSION['last_order'])) {
    header('location: index.php');
    exit;
}
$order = $_SESSION['last_order'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - Order #<?php echo $order['order_id']; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .receipt-container {
            max-width: 400px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .receipt-header {
            border-bottom: 2px dashed #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
        }
        .receipt-total {
            font-size: 1.3rem;
            font-weight: bold;
            padding: 15px 0;
            border-top: 2px solid #333;
            margin-top: 10px;
        }
        .btn-print, .btn-back {
            width: 100%;
            padding: 15px;
            margin-top: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
        }
        .btn-print {
            background: #4a90e2;
            color: white;
        }
        .btn-back {
            background: #27ae60;
            color: white;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>🧾 POS System</h2>
            <p><strong>Order #<?php echo $order['order_id']; ?></strong></p>
            <p>Customer: <?php echo $order['customer_name']; ?></p>
            <p>Date: <?php echo date('Y-m-d H:i:s'); ?></p>
            <p>Payment: <?php echo ucfirst($order['payment_method']); ?></p>
        </div>
        
        <div class="receipt-items">
            <?php foreach ($order['items'] as $item): ?>
            <div class="receipt-item">
                <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="receipt-totals">
            <div class="receipt-item">
                <span>Subtotal:</span>
                <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
            </div>
            <div class="receipt-item">
                <span>Discount:</span>
                <span>-$<?php echo number_format($order['discount'], 2); ?></span>
            </div>
            <div class="receipt-item">
                <span>Tax:</span>
                <span>$<?php echo number_format($order['tax'], 2); ?></span>
            </div>
            <div class="receipt-total">
                <span>TOTAL:</span>
                <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <div class="receipt-footer">
            <p style="margin-top: 20px;"><strong>Thank you for your purchase!</strong></p>
            <p>Please come again</p>
        </div>
        
        <button class="btn-print" onclick="window.print()">🖨️ Print Receipt</button>
        <button class="btn-back" onclick="window.location.href='index.php'">🔄 New Order</button>
    </div>
</body>
</html>