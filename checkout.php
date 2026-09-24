<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - History Kedai</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .header a {
            text-decoration: none;
            color: var(--color-purple-mid);
            font-weight: 600;
        }

        .container {
            max-width: 600px;
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
            margin-bottom: 30px;
        }
        
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-group input[type="text"] { 
            width: 100%; padding: 12px; border: 1px solid #ddd; 
            border-radius: 8px; box-sizing: border-box; 
            font-family: var(--font-body); font-size: 1em;
        }
        
        .payment-method-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .payment-method-container label {
            position: relative;
            padding: 20px;
            border: 2px solid #eee;
            border-radius: var(--border-radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
        }
        .payment-method-container input[type="radio"] { display: none; }
        .payment-method-container i {
            font-size: 1.5em;
            color: var(--color-muted-text);
            transition: color 0.2s ease;
        }
        .payment-method-container span { font-weight: 500; }
        .payment-method-container input[type="radio"]:checked + label {
            border-color: var(--color-purple-mid);
            background-color: var(--color-light-bg);
        }
        .payment-method-container input[type="radio"]:checked + label i {
            color: var(--color-purple-mid);
        }

        /* --- Style Baru untuk Gambar QRIS --- */
        #qris-display {
            text-align: center;
            margin-top: 20px;
            /* Animasi fade-in */
            opacity: 0;
            transition: opacity 0.5s ease;
            max-height: 0;
            overflow: hidden;
        }
        #qris-display.show {
            opacity: 1;
            max-height: 500px; /* Cukup besar untuk menampung gambar */
        }
        #qris-display img {
            max-width: 250px;
            border-radius: var(--border-radius);
            border: 1px solid #eee;
        }
        #qris-display p {
            font-size: 0.9em;
            color: var(--color-muted-text);
        }
        
        .summary-box { 
            background-color: var(--color-light-bg); padding: 20px; 
            border-radius: var(--border-radius); margin-top: 30px; 
            border: 1px solid #eee;
        }
        .summary-box h3 { margin-top: 0; font-family: var(--font-heading); color: var(--color-purple-deep); }
        #summary-items div { display: flex; justify-content: space-between; margin-bottom: 10px; color: var(--color-muted-text); }
        #summary-total { font-size: 1.5em; font-weight: bold; text-align: right; margin-top: 20px; border-top: 2px solid var(--color-purple-light); padding-top: 15px; color: var(--color-purple-deep); }

        .cta-button {
            display: block; width: 100%; padding: 1rem;
            background: var(--color-purple-mid); color: var(--color-white);
            text-decoration: none; text-align: center; font-size: 1.2em;
            font-weight: 600; border-radius: 50px; margin-top: 30px;
            transition: all 0.3s ease; border: none; cursor: pointer;
        }
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(123, 104, 238, 0.4);
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="keranjang.php"><i class="fas fa-arrow-left"></i> Kembali ke Keranjang</a>
    </header>

    <div class="container">
        <h2>Konfirmasi Pesanan</h2>
        <form action="proses_pesanan_pelanggan.php" method="POST">
            <div class="form-group">
                <label for="nama_pelanggan">Nama Anda</label>
                <input type="text" id="nama_pelanggan" name="nama_pelanggan" placeholder="Tulis nama Anda di sini..." required>
            </div>
            
            <div class="form-group">
                <label>Metode Pembayaran</label>
                <div class="payment-method-container">
                    <div>
                        <input type="radio" id="tunai" name="metode_bayar" value="Tunai" checked>
                        <label for="tunai">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Tunai</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" id="qris" name="metode_bayar" value="QRIS">
                        <label for="qris">
                            <i class="fas fa-qrcode"></i>
                            <span>QRIS</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div id="qris-display">
                <p>Silakan scan kode QRIS di bawah ini</p>
                <img src="gambar/qris.jpg" alt="Kode QRIS Pembayaran">
            </div>
            
            <div class="summary-box">
                <h3>Ringkasan Pesanan</h3>
                <div id="summary-items"></div>
                <div id="summary-total">Total: Rp 0</div>
            </div>

            <input type="hidden" name="pesanan_json" id="pesanan_json_input">
            <input type="hidden" name="total_harga" id="total_harga_input">

            <button type="submit" class="cta-button">Buat Pesanan Sekarang</button>
        </form>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Bagian untuk menampilkan ringkasan pesanan ---
        const cart = JSON.parse(sessionStorage.getItem('cart')) || {};
        const summaryItemsContainer = document.getElementById('summary-items');
        const summaryTotalElement = document.getElementById('summary-total');
        const pesananJsonInput = document.getElementById('pesanan_json_input');
        const totalHargaInput = document.getElementById('total_harga_input');
        
        let totalPrice = 0;
        let itemsForJson = [];
        summaryItemsContainer.innerHTML = '';

        for (const id in cart) {
            const item = cart[id];
            const subtotal = item.price * item.qty;
            totalPrice += subtotal;

            const itemDiv = document.createElement('div');
            itemDiv.innerHTML = `<span>${item.qty}x ${item.name}</span> <span>Rp ${subtotal.toLocaleString('id-ID')}</span>`;
            summaryItemsContainer.appendChild(itemDiv);
            
            itemsForJson.push({
                id: id, name: item.name, price: item.price, qty: item.qty
            });
        }

        summaryTotalElement.innerText = `Total: Rp ${totalPrice.toLocaleString('id-ID')}`;
        pesananJsonInput.value = JSON.stringify(itemsForJson);
        totalHargaInput.value = totalPrice;

        // --- Logika BARU untuk menampilkan/menyembunyikan gambar QRIS ---
        const paymentRadios = document.querySelectorAll('input[name="metode_bayar"]');
        const qrisDisplay = document.getElementById('qris-display');

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'QRIS') {
                    qrisDisplay.classList.add('show');
                } else {
                    qrisDisplay.classList.remove('show');
                }
            });
        });
    });
</script>
</body>
</html>