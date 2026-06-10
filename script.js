// ==============================
// POS System JavaScript
// ==============================

// Global Variables
let cart = [];
let cartTotal = {
    subtotal: 0,
    tax: 0,
    discount: 0,
    total: 0
};
let selectedPayment = 'cash';
let darkMode = false;

// ==============================
// Cart Functions
// ==============================

function addToCart(id, name, price) {
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: id,
            name: name,
            price: price,
            quantity: 1
        });
    }
    
    updateCartDisplay();
    showNotification('Added: ' + name);
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCartDisplay();
}

function updateQuantity(id, change) {
    const item = cart.find(item => item.id === id);
    
    if (item) {
        item.quantity += change;
        
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            updateCartDisplay();
        }
    }
}

function clearCart() {
    if (cart.length === 0) {
        showNotification('Cart is already empty');
        return;
    }
    
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        updateCartDisplay();
        showNotification('Cart cleared');
    }
}

function updateCartDisplay() {
    const cartItemsContainer = document.getElementById('cartItems');
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="empty-cart">Cart is empty</p>';
        resetTotals();
        return;
    }
    
    let html = '';
    let subtotal = 0;
    
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        html += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">$${item.price.toFixed(2)} x ${item.quantity}</div>
                </div>
                <div class="cart-item-controls">
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                    <span>${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                    <button class="remove-btn" onclick="removeFromCart(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    cartItemsContainer.innerHTML = html;
    calculateTotals(subtotal);
}

function calculateTotals(subtotal) {
    const discountPercent = parseFloat(document.getElementById('discountInput').value) || 0;
    const taxRate = 0.10;
    
    const discountAmount = subtotal * (discountPercent / 100);
    const taxAmount = (subtotal - discountAmount) * taxRate;
    const total = subtotal - discountAmount + taxAmount;
    
    cartTotal = {
        subtotal: subtotal,
        tax: taxAmount,
        discount: discountAmount,
        total: total
    };
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('discount').textContent = '-$' + discountAmount.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

function resetTotals() {
    cartTotal = { subtotal: 0, tax: 0, discount: 0, total: 0 };
    document.getElementById('subtotal').textContent = '$0.00';
    document.getElementById('tax').textContent = '$0.00';
    document.getElementById('discount').textContent = '-$0.00';
    document.getElementById('total').textContent = '$0.00';
    document.getElementById('discountInput').value = '';
}

// ==============================
// Search & Filter Functions
// ==============================

function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const name = product.querySelector('h3').textContent.toLowerCase();
        const category = product.querySelector('.category').textContent.toLowerCase();
        
        if (name.includes(searchTerm) || category.includes(searchTerm) || searchTerm === '') {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

function filterCategory(category) {
    const products = document.querySelectorAll('.product-card');
    const buttons = document.querySelectorAll('.cat-btn');
    
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase() === category.toLowerCase() || category === 'all') {
            if (btn.textContent.toLowerCase() === category.toLowerCase()) {
                btn.classList.add('active');
            }
            if (category === 'all') {
                btn.classList.add('active');
            }
        }
    });
    
    // Fix active button
    document.querySelectorAll('.cat-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    event.target.classList.add('active');
    
    products.forEach(product => {
        const productCategory = product.querySelector('.category').textContent;
        
        if (category === 'all' || productCategory === category) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// ==============================
// Payment Functions
// ==============================

function selectPayment(method) {
    selectedPayment = method;
    
    document.querySelectorAll('.payment-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    event.target.classList.add('active');
}

function applyDiscount() {
    const subtotal = cartTotal.subtotal;
    calculateTotals(subtotal);
}

function checkout() {
    if (cart.length === 0) {
        showNotification('Cart is empty! Add some products first.');
        return;
    }
    
    const customerName = document.getElementById('customerName').value || 'Guest';
    const discountPercent = parseFloat(document.getElementById('discountInput').value) || 0;
    
    // Create hidden form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'checkout.php';
    
    // Add all data as hidden inputs
    const fields = {
        customer_name: customerName,
        total_amount: cartTotal.total,
        discount: cartTotal.discount,
        tax: cartTotal.tax,
        payment_method: selectedPayment,
        subtotal: cartTotal.subtotal,
        items: JSON.stringify(cart)
    };
    
    for (const [key, value] of Object.entries(fields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
}

// ==============================
// Admin Functions
// ==============================

function openAdminPanel() {
    document.getElementById('adminModal').classList.add('show');
    loadSalesData();
}

function closeAdminPanel() {
    document.getElementById('adminModal').classList.remove('show');
}

function showAdminTab(tabName) {
    const tabs = document.querySelectorAll('.admin-tab-content');
    tabs.forEach(tab => tab.style.display = 'none');
    
    const tabLinks = document.querySelectorAll('.admin-tabs li');
    tabLinks.forEach(link => link.classList.remove('active'));
    
    document.getElementById('admin' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).style.display = 'block';
    event.target.classList.add('active');
}

function loadSalesData() {
    fetch('api/get_sales.php')
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        document.getElementById('todaySales').textContent = '$' + parseFloat(data.today_sales).toFixed(2);
        document.getElementById('totalOrders').textContent = data.total_orders;
        
        var rows = '';
        if (data.orders && data.orders.length > 0) {
            data.orders.forEach(function(order) {
                var date = new Date(order.created_at).toLocaleString();
                rows += '<tr>';
                rows += '<td>#' + order.id + '</td>';
                rows += '<td>' + (order.customer_name || 'Guest') + '</td>';
                rows += '<td>$' + parseFloat(order.total_amount).toFixed(2) + '</td>';
                rows += '<td>' + date + '</td>';
                rows += '</tr>';
            });
        } else {
            rows = '<tr><td colspan="4">No orders yet</td></tr>';
        }
        document.getElementById('salesTable').innerHTML = rows;
    })
    .catch(function(error) {
        console.log('Error loading sales:', error);
        document.getElementById('salesTable').innerHTML = '<tr><td colspan="4">Error loading data</td></tr>';
    });
}

function addProduct(event) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append('name', document.getElementById('newProductName').value);
    formData.append('category', document.getElementById('newProductCategory').value);
    formData.append('price', document.getElementById('newProductPrice').value);
    formData.append('stock', document.getElementById('newProductStock').value);
    formData.append('barcode', document.getElementById('newProductBarcode').value);
    
    fetch('api/add_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added successfully!');
            location.reload();
        } else {
            showNotification('Error: ' + data.message);
        }
    });
}

function editProduct(id) {
    showNotification('Edit function - Coming soon!');
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('api/delete_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Product deleted!');
                location.reload();
            }
        });
    }
}

// ==============================
// Order History Functions
// ==============================

function openOrderHistory() {
    document.getElementById('historyModal').classList.add('show');
    loadOrderHistory();
}

function closeOrderHistory() {
    document.getElementById('historyModal').classList.remove('show');
}

function loadOrderHistory() {
    fetch('api/get_orders.php')
    .then(response => response.json())
    .then(data => {
        let rows = '';
        data.orders.forEach(order => {
            rows += `
                <tr>
                    <td>#${order.id}</td>
                    <td>${order.customer_name}</td>
                    <td>${order.item_count} items</td>
                    <td>$${order.total_amount}</td>
                    <td>${order.created_at}</td>
                    <td>
                        <button onclick="viewOrderDetails(${order.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
            `;
        });
        document.getElementById('historyTable').innerHTML = rows;
    });
}

function viewOrderDetails(orderId) {
    showNotification('View Order #' + orderId + ' - Coming soon!');
}

// ==============================
// Receipt Functions
// ==============================

function showReceipt(orderId, customerName) {
    const receiptModal = document.getElementById('receiptModal');
    if (!receiptModal) {
        // Create modal if not exists
        createReceiptModal(orderId, customerName);
        return;
    }
    
    receiptModal.classList.add('show');
    document.getElementById('receiptOrderId').textContent = '#' + orderId;
    document.getElementById('receiptCustomer').textContent = customerName;
    document.getElementById('receiptDate').textContent = new Date().toLocaleString();
    document.getElementById('receiptPayment').textContent = selectedPayment;
    
    let itemsHtml = '';
    cart.forEach(item => {
        itemsHtml += `
            <div class="receipt-item">
                <span>${item.name} x ${item.quantity}</span>
                <span>$${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        `;
    });
    document.getElementById('receiptItems').innerHTML = itemsHtml;
    document.getElementById('receiptSubtotal').textContent = '$' + cartTotal.subtotal.toFixed(2);
    document.getElementById('receiptTax').textContent = '$' + cartTotal.tax.toFixed(2);
    document.getElementById('receiptDiscount').textContent = '-$' + cartTotal.discount.toFixed(2);
    document.getElementById('receiptTotal').textContent = '$' + cartTotal.total.toFixed(2);
}

function createReceiptModal(orderId, customerName) {
    const modalHtml = `
        <div class="modal" id="receiptModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Receipt</h2>
                    <button class="close-btn" onclick="closeReceipt()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="receipt">
                        <div class="receipt-header">
                            <h3>POS System</h3>
                            <p>Order #<span id="receiptOrderId">${orderId}</span></p>
                            <p>Customer: <span id="receiptCustomer">${customerName}</span></p>
                            <p>Date: <span id="receiptDate">${new Date().toLocaleString()}</span></p>
                            <p>Payment: <span id="receiptPayment">${selectedPayment}</span></p>
                        </div>
                        <div class="receipt-items" id="receiptItems">
                            <!-- Items will be here -->
                        </div>
                        <div class="receipt-totals">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="receiptSubtotal">$0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax:</span>
                                <span id="receiptTax">$0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Discount:</span>
                                <span id="receiptDiscount">-$0.00</span>
                            </div>
                            <div class="receipt-total">
                                <span>TOTAL:</span>
                                <span id="receiptTotal">$0.00</span>
                            </div>
                        </div>
                        <div class="receipt-footer">
                            <p>Thank you for your purchase!</p>
                            <p>Please come again</p>
                        </div>
                    </div>
                    <button class="btn-print" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    document.getElementById('receiptModal').classList.add('show');
    showReceipt(orderId, customerName);
}

function closeReceipt() {
    document.getElementById('receiptModal').classList.remove('show');
}

// ==============================
// Utility Functions
// ==============================

function toggleDarkMode() {
    darkMode = !darkMode;
    
    if (darkMode) {
        document.body.setAttribute('data-theme', 'dark');
    } else {
        document.body.removeAttribute('data-theme');
    }
}

function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Hide after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function showNotificationStyle() {
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--success);
            color: var(--white);
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            transform: translateX(400px);
            transition: transform 0.3s ease;
            z-index: 2000;
        }
        .notification.show {
            transform: translateX(0);
        }
    `;
    document.head.appendChild(style);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    showNotificationStyle();
});