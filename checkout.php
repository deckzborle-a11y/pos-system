<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'] ?? 'Guest';
    $total_amount = $_POST['total_amount'];
    $discount = $_POST['discount'] ?? 0;
    $tax = $_POST['tax'] ?? 0;
    $payment_method = $_POST['payment_method'];
    $items = json_decode($_POST['items'], true);
    
    if (empty($items)) {
        echo "<script>alert('Cart is empty!'); window.location.href='index.php';</script>";
        exit;
    }
    
    // Insert order
    $sql = "INSERT INTO orders (customer_name, total_amount, discount, tax, payment_method) 
            VALUES ('$customer_name', $total_amount, $discount, $tax, '$payment_method')";
    
    if ($conn->query($sql)) {
        $order_id = $conn->insert_id;
        
        // Insert order items and update stock
        foreach ($items as $item) {
            $product_id = $item['id'];
            $product_name = $item['name'];
            $quantity = $item['quantity'];
            $price_at_sale = $item['price'];
            $subtotal = $item['price'] * $item['quantity'];
            
            $sql_item = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price_at_sale, subtotal)
                        VALUES ($order_id, $product_id, '$product_name', $quantity, $price_at_sale, $subtotal)";
            $conn->query($sql_item);
            
            // Update stock
            $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
        }
        
        // Show success with receipt
        session_start();
        $_SESSION['last_order'] = [
            'order_id' => $order_id,
            'customer_name' => $customer_name,
            'items' => $items,
            'total_amount' => $total_amount,
            'discount' => $discount,
            'tax' => $tax,
            'payment_method' => $payment_method,
            'subtotal' => $_POST['subtotal']
        ];
        
        echo "<script>window.location.href='receipt.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href='index.php';</script>";
    }
} else {
    header('location: index.php');
}
?>