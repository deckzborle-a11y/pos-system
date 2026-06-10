<?php
include 'db.php';
$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $barcode = $_POST['barcode'] ?? '';
    
    if (empty($name) || empty($price) || empty($stock)) {
        $error = "Please fill in all required fields!";
    } else {
        $sql = "INSERT INTO products (name, category, price, stock, barcode) 
                VALUES ('$name', '$category', $price, $stock, '$barcode')";
        
        if ($conn->query($sql)) {
            $message = "✅ Product '$name' added successfully!";
        } else {
            $error = "❌ Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            padding: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 500px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 { 
            color: #2c3e50; 
            margin-bottom: 20px;
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: 600;
            color: #2c3e50;
        }
        input, select { 
            width: 100%; 
            padding: 12px; 
            border: 2px solid #ecf0f1; 
            border-radius: 8px; 
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus, select:focus { 
            outline: none; 
            border-color: #4a90e2; 
        }
        .btn-submit { 
            width: 100%; 
            padding: 15px; 
            background: #27ae60; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-submit:hover { 
            background: #219a52; 
        }
        .btn-back { 
            width: 100%; 
            padding: 15px; 
            background: #95a5a6; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .message { 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Add New Product</h1>
        
        <?php if ($message): ?>
        <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" placeholder="Enter product name" required>
            </div>
            
            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <option value="Food">🍔 Food</option>
                    <option value="Drinks">🥤 Drinks</option>
                    <option value="Snacks">🍿 Snacks</option>
                    <option value="Desserts">🍰 Desserts</option>
                    <option value="Electronics">📱 Electronics</option>
                    <option value="Other">📦 Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Price ($) *</label>
                <input type="number" name="price" placeholder="0.00" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label>Stock Quantity *</label>
                <input type="number" name="stock" placeholder="0" min="0" required>
            </div>
            
            <div class="form-group">
                <label>Barcode (Optional)</label>
                <input type="text" name="barcode" placeholder="Enter barcode">
            </div>
            
            <button type="submit" class="btn-submit">
                💾 Save Product
            </button>
            
            <button type="button" class="btn-back" onclick="window.location.href='index.php'">
                ← Back to POS
            </button>
        </form>
    </div>
</body>
</html>