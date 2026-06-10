<?php
include 'db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="logo">
                <i class="fas fa-cash-register"></i>
                <span>POS System</span>
            </div>
            <div class="header-actions">
                <button class="btn-dark-mode" onclick="toggleDarkMode()">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="btn-admin" onclick="openAdminPanel()">
                    <i class="fas fa-cog"></i> Admin
                </button>
            </div>
        </header>

        <div class="main-content">
            <!-- Products Section -->
            <section class="products-section">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search products..." onkeyup="searchProducts()">
                    <i class="fas fa-search"></i>
                </div>

                <div class="category-filter">
                    <button class="cat-btn active" onclick="filterCategory('all')">All</button>
                    <button class="cat-btn" onclick="filterCategory('Food')">Food</button>
                    <button class="cat-btn" onclick="filterCategory('Drinks')">Drinks</button>
                </div>

                <div class="products-grid" id="productsGrid">
                    <?php
                    $result = $conn->query("SELECT * FROM products WHERE stock > 0");
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="product-card" onclick="addToCart('.$row['id'].', \''.$row['name'].'\', '.$row['price'].')">';
                        echo '<div class="product-image"><i class="fas fa-box"></i></div>';
                        echo '<h3>'.$row['name'].'</h3>';
                        echo '<p class="category">'.$row['category'].'</p>';
                        echo '<p class="price">$'.$row['price'].'</p>';
                        echo '<span class="stock">Stock: '.$row['stock'].'</span>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </section>

            <!-- Cart Section -->
            <section class="cart-section">
                <div class="cart-header">
                    <h2><i class="fas fa-shopping-cart"></i> Cart</h2>
                    <button class="btn-clear" onclick="clearCart()">Clear All</button>
                </div>

                <div class="cart-items" id="cartItems">
                    <p class="empty-cart">Cart is empty</p>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span id="tax">$0.00</span>
                    </div>
                    <div class="summary-row discount-row">
                        <input type="number" id="discountInput" placeholder="Discount %" min="0" max="100" onchange="applyDiscount()">
                        <span id="discount">-$0.00</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total:</span>
                        <span id="total">$0.00</span>
                    </div>
                </div>

                <div class="customer-info">
                    <input type="text" id="customerName" placeholder="Customer Name (Optional)">
                </div>

                <div class="payment-methods">
                    <label>Payment Method:</label>
                    <div class="payment-options">
                        <button class="payment-btn active" onclick="selectPayment('cash')">
                            <i class="fas fa-money-bill"></i> Cash
                        </button>
                        <button class="payment-btn" onclick="selectPayment('card')">
                            <i class="fas fa-credit-card"></i> Card
                        </button>
                        <button class="payment-btn" onclick="selectPayment('mobile')">
                            <i class="fas fa-mobile-alt"></i> Mobile
                        </button>
                    </div>
                </div>

                <button class="btn-checkout" onclick="checkout()">
                    <i class="fas fa-check"></i> Checkout
                </button>
            </section>
        </div>

        <!-- Order History Button -->
        <button class="btn-history" onclick="openOrderHistory()">
            <i class="fas fa-history"></i> Order History
        </button>
    </div>

    <!-- Admin Panel Modal -->
    <div class="modal" id="adminModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Admin Panel</h2>
                <button class="close-btn" onclick="closeAdminPanel()">&times;</button>
            </div>
            <div class="modal-body">
                <ul class="admin-tabs">
                    <li onclick="showAdminTab('products')">Products</li>
                    <li onclick="window.location.href='sales_report.php'">Sales Report</li>
                    <li onclick="window.location.href='add_product.php'">Add Product</li>
                </ul>
                
                <div id="adminProducts" class="admin-tab-content">
                    <h3>Manage Products</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM products");
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>'.$row['id'].'</td>';
                                echo '<td>'.$row['name'].'</td>';
                                echo '<td>'.$row['category'].'</td>';
                                echo '<td>$'.$row['price'].'</td>';
                                echo '<td>'.$row['stock'].'</td>';
                                echo '<td>';
                                echo '<button onclick="editProduct('.$row['id'].')"><i class="fas fa-edit"></i></button>';
                                echo '<button onclick="deleteProduct('.$row['id'].')"><i class="fas fa-trash"></i></button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div id="adminSales" class="admin-tab-content" style="display:none;">
                    <h3>Sales Report</h3>
                    <div class="sales-stats">
                        <div class="stat-card">
                            <h4>Today's Sales</h4>
                            <p id="todaySales">$0.00</p>
                        </div>
                        <div class="stat-card">
                            <h4>Total Orders</h4>
                            <p id="totalOrders">0</p>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="salesTable">
                            <!-- Filled by JS -->
                        </tbody>
                    </table>
                </div>

                <div id="adminAdd" class="admin-tab-content" style="display:none;">
                    <h3>Add New Product</h3>
                    <form onsubmit="addProduct(event)">
                        <input type="text" id="newProductName" placeholder="Product Name" required>
                        <select id="newProductCategory">
                            <option value="Food">Food</option>
                            <option value="Drinks">Drinks</option>
                        </select>
                        <input type="number" id="newProductPrice" placeholder="Price" step="0.01" required>
                        <input type="number" id="newProductStock" placeholder="Stock Quantity" required>
                        <input type="text" id="newProductBarcode" placeholder="Barcode">
                        <button type="submit" class="btn-save">Save Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Order History Modal -->
    <div class="modal" id="historyModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Order History</h2>
                <button class="close-btn" onclick="closeOrderHistory()">&times;</button>
            </div>
            <div class="modal-body">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="historyTable">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>