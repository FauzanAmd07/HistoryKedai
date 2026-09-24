<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - History Kedai</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* --- TEMA UNGU & PUTIH (Sesuai index.php) --- */
        :root {
            --color-purple-deep: #41228e;
            --color-purple-mid: #7B68EE;
            --color-purple-light: #E6E0FF;
            --color-white: #FFFFFF;
            --color-light-bg: #F9F9FD;
            --color-dark-text: #1d1d1f;
            --color-muted-text: #6c757d;
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Poppins', sans-serif;
            --shadow-subtle: 0 4px 12px rgba(0, 0, 0, 0.05);
            --shadow-strong: 0 10px 40px rgba(0, 0, 0, 0.1);
            --border-radius: 16px;
        }

        body {
            font-family: var(--font-body);
            margin: 0;
            background-color: var(--color-light-bg);
            color: var(--color-dark-text);
        }

        .header {
            background-color: var(--color-white);
            padding: 1rem 4rem;
            box-shadow: var(--shadow-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header a {
            text-decoration: none;
            color: var(--color-purple-mid);
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .header a:hover { color: var(--color-purple-deep); }
        
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 40px;
            background: var(--color-white);
            box-shadow: var(--shadow-strong);
            border-radius: var(--border-radius);
        }
        h2 {
            font-family: var(--font-heading);
            text-align: center;
            font-size: 2.5em;
            color: var(--color-purple-deep);
            margin-top: 0;
            margin-bottom: 40px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        thead th {
            font-family: var(--font-body);
            font-weight: 500;
            color: var(--color-muted-text);
            text-transform: uppercase;
            font-size: 0.9em;
        }
        
        .item-info h3 {
            margin: 0 0 5px 0;
            font-size: 1.1em;
            font-weight: 500;
        }
        .item-info p {
            margin: 0;
            color: var(--color-muted-text);
        }
        
        .qty-input {
            width: 60px;
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        
        .btn-hapus {
            background: none;
            border: none;
            color: #ccc;
            font-size: 1.2em;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .btn-hapus:hover { color: #dc3545; }
        
        .summary {
            margin-top: 40px;
            text-align: right;
            border-top: 2px solid var(--color-purple-light);
            padding-top: 20px;
        }
        .total-price {
            font-family: var(--font-heading);
            font-size: 2em;
            font-weight: 700;
            color: var(--color-purple-deep);
        }
        
        .cta-button { /* Menggunakan style tombol yang sama dengan index.php */
            display: block;
            width: 100%;
            padding: 1rem;
            background: var(--color-purple-mid);
            color: var(--color-white);
            text-decoration: none;
            text-align: center;
            font-size: 1.2em;
            font-weight: 600;
            border-radius: 50px;
            margin-top: 30px;
            transition: all 0.3s ease;
            border: none;
        }
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(123, 104, 238, 0.4);
        }
        
        .cart-empty {
            text-align: center;
            padding: 50px;
        }
        .cart-empty h2 {
            font-size: 2em;
        }

        @media (max-width: 768px) {
            .container { padding: 20px; margin: 20px; }
            th, td { padding: 10px 5px; }
            .header { padding: 1rem; }
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Menu</a>
        <a href="login.php">Login Karyawan</a>
    </header>

    <main class="container">
        <h2>Keranjang Anda</h2>
        <div id="cart-items">
            <table>
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Harga</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
        <div class="summary">
            <div id="total-price" class="total-price">Rp 0</div>
        </div>
        <a href="checkout.php" id="checkout-btn" class="cta-button">Lanjutkan ke Pembayaran</a>
    </main>

<script>
    function renderCart() {
        const cart = JSON.parse(sessionStorage.getItem('cart')) || {};
        const cartTableBody = document.querySelector('#cart-items tbody');
        const mainContainer = document.querySelector('main.container');
        cartTableBody.innerHTML = '';
        let totalPrice = 0;

        if (Object.keys(cart).length === 0) {
            mainContainer.innerHTML = '<div class="cart-empty"><h2>Keranjang Anda Kosong</h2><p>Mari kembali dan pilih menu favorit Anda.</p><a href="index.php" class="cta-button" style="width: auto; padding: 15px 30px;">Kembali ke Menu</a></div>';
            return;
        }

        for (const id in cart) {
            const item = cart[id];
            const subtotal = item.price * item.qty;
            totalPrice += subtotal;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="item-info">
                        <h3>${item.name}</h3>
                        <p>@ Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                </td>
                <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                <td style="text-align: center;"><input type="number" class="qty-input" value="${item.qty}" min="1" onchange="updateQty(${id}, this.value)"></td>
                <td><strong>Rp ${subtotal.toLocaleString('id-ID')}</strong></td>
                <td><button class="btn-hapus" onclick="removeFromCart(${id})"><i class="fas fa-times"></i></button></td>
            `;
            cartTableBody.appendChild(row);
        }

        document.getElementById('total-price').innerText = `Total: Rp ${totalPrice.toLocaleString('id-ID')}`;
    }

    function updateQty(id, qty) {
        let cart = JSON.parse(sessionStorage.getItem('cart')) || {};
        if (cart[id]) {
            cart[id].qty = parseInt(qty);
            if (cart[id].qty <= 0) {
                delete cart[id];
            }
            sessionStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        }
    }

    function removeFromCart(id) {
        let cart = JSON.parse(sessionStorage.getItem('cart')) || {};
        if (cart[id]) {
            delete cart[id];
            sessionStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        }
    }

    document.addEventListener('DOMContentLoaded', renderCart);
</script>

</body>
</html>