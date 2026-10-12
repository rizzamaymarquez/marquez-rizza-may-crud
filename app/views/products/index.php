<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f6f9; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .btn-logout { background-color: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 14px; }
        .btn-logout:hover { background-color: #bd2130; }
        .container { display: flex; gap: 20px; flex-wrap: wrap; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-container { flex: 1; min-width: 300px; }
        .table-container { flex: 2; min-width: 450px; }
        .form-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; margin-top: 5px; }
        .btn-submit { background-color: #28a745; width: 100%; font-size: 16px; }
        .btn-cancel { background-color: #6c757d; width: 100%; display: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #343a40; color: white; }
        .btn-action { padding: 5px 10px; border: none; border-radius: 3px; font-size: 13px; color: white; cursor: pointer; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        .toast { position: fixed; right: 24px; top: 24px; z-index: 10; min-width: 260px; max-width: 360px; padding: 14px 18px; border-radius: 6px; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.2); opacity: 0; transform: translateY(-12px); pointer-events: none; transition: opacity .2s, transform .2s; }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background: #198754; }
        .toast.error { background: #dc3545; }
        .toast.info { background: #0d6efd; }
    </style>
</head>
<body>

    <?php $role = $_SESSION['role'] ?? 'user'; ?>

    <!-- HEADER WITH USERNAME AND LOGOUT BUTTON -->
    <div class="header">
        <h2>Product Management System</h2>
        <div class="user-info">
            <span>Welcome, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>!</span>
            <span style="font-size: 12px; padding: 4px 8px; background: #e9ecef; border-radius: 999px; text-transform: capitalize;">
                <?= htmlspecialchars($role); ?>
            </span>
            <a href="<?= site_url('logout'); ?>" class="btn-logout">Logout</a>
        </div>
    </div>
    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    <div class="container">
        <?php if ($role === 'admin'): ?>
        <!-- SINGLE FORM FOR ADD AND EDIT -->
        <div class="card form-container">
            <h3 id="form-title">Add New Product</h3>
            <form id="productForm">
                <input type="hidden" id="product_id" name="id">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Price (₱)</label>
                    <input type="number" step="0.01" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" id="quantity" name="quantity" required>
                </div>

                <button type="submit" class="btn-submit" id="btn-save">Save Product</button>
                <button type="button" class="btn-cancel" id="btn-cancel" onclick="resetForm()">Cancel Edit</button>
            </form>
        </div>
        <?php else: ?>
        <div class="card form-container">
            <h3>Read Only Access</h3>
            <p>You can view products, but you cannot add, edit, or delete them.</p>
        </div>
        <?php endif; ?>

        <!-- READ TABLE -->
        <div class="card table-container">
            <h3>Product List</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <!-- Populated via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const baseUrl = '<?= rtrim(site_url(), '/'); ?>';
        const userRole = '<?= htmlspecialchars($role); ?>';

        document.addEventListener('DOMContentLoaded', loadProducts);

        // Fetch & Display Products
        function loadProducts() {
            fetch(`${baseUrl}/products/get_all`)
                .then(res => {
                    if (!res.ok) throw new Error('Unable to load products.');
                    return res.json();
                })
                .then(data => {
                    let rows = '';
                    if (data.length > 0) {
                        data.forEach(p => {
                            let actionCell = '<td>-</td>';
                            if (userRole === 'admin') {
                                actionCell = `
                                    <td>
                                        <button class="btn-action btn-edit" onclick="editProduct(${p.id}, '${escapeQuotes(p.product_name)}', '${escapeQuotes(p.description || '')}', ${p.price}, ${p.quantity})">Edit</button>
                                        <button class="btn-action btn-delete" onclick="deleteProduct(${p.id})">Delete</button>
                                    </td>
                                `;
                            } else {
                                actionCell = '<td>View only</td>';
                            }

                            rows += `
                                <tr>
                                    <td>${p.id}</td>
                                    <td>${escapeHtml(p.product_name)}</td>
                                    <td>${escapeHtml(p.description || '')}</td>
                                    <td>₱${parseFloat(p.price).toFixed(2)}</td>
                                    <td>${p.quantity}</td>
                                    ${actionCell}
                                </tr>
                            `;
                        });
                    } else {
                        rows = `<tr><td colspan="6" style="text-align:center;">No products found.</td></tr>`;
                    }
                    document.getElementById('productTableBody').innerHTML = rows;
                })
                .catch(error => showNotification(error.message, 'error'));
        }

        // Handle Add / Edit Form Submit
        const productForm = document.getElementById('productForm');
        if (productForm) productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('product_id').value;
            const url = id ? `${baseUrl}/products/edit/${id}` : `${baseUrl}/products/create`;

            const formData = new FormData(this);

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error('Request failed.');
                return res.json();
            })
            .then(res => {
                if(res.status === 'success') {
                    showNotification(res.message, 'success');
                    resetForm();
                    loadProducts();
                } else {
                    showNotification(res.message || 'Product could not be saved.', 'error');
                }
            })
            .catch(error => showNotification(error.message, 'error'));
        });

        // Edit button click handler
        function editProduct(id, name, desc, price, qty) {
            document.getElementById('form-title').innerText = 'Edit Product';
            document.getElementById('product_id').value = id;
            document.getElementById('product_name').value = name;
            document.getElementById('description').value = desc;
            document.getElementById('price').value = price;
            document.getElementById('quantity').value = qty;
            document.getElementById('btn-save').innerText = 'Update Product';
            document.getElementById('btn-cancel').style.display = 'block';
            showNotification('Edit mode enabled.', 'info');
        }

        // Reset form to Add state
        function resetForm() {
            document.getElementById('form-title').innerText = 'Add New Product';
            document.getElementById('product_id').value = '';
            document.getElementById('productForm').reset();
            document.getElementById('btn-save').innerText = 'Save Product';
            document.getElementById('btn-cancel').style.display = 'none';
        }

        // Delete Handler
        function deleteProduct(id) {
            if (confirm('Sigurado ka bang buburahin mo ito?')) {
                fetch(`${baseUrl}/products/delete/${id}`, { method: 'POST' })
                    .then(res => {
                        if (!res.ok) throw new Error('Delete request failed.');
                        return res.json();
                    })
                    .then(res => {
                        if(res.status === 'success') {
                            showNotification(res.message, 'success');
                            loadProducts();
                        } else {
                            showNotification(res.message || 'Product could not be deleted.', 'error');
                        }
                    })
                    .catch(error => showNotification(error.message, 'error'));
            } else {
                showNotification('Delete cancelled.', 'info');
            }
        }

        function showNotification(message, type) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            clearTimeout(showNotification.timeout);
            showNotification.timeout = setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function escapeHtml(str) {
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function escapeQuotes(str) {
            return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
        }
    </script>

</body>
</html>