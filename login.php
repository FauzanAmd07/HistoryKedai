<?php
session_start();
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("location:dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Karyawan</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&family=Montserrat:wght@700&display=swap" rel="stylesheet">
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
            --font-heading: 'Montserrat', sans-serif; /* Font Judul */
            --font-body: 'Poppins', sans-serif;
            --shadow-strong: 0 10px 40px rgba(0, 0, 0, 0.1);
            --border-radius: 16px;
        }

        body {
            font-family: var(--font-body);
            background: radial-gradient(circle, var(--color-purple-light) 0%, var(--color-white) 70%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .back-link {
            position: absolute;
            top: 30px; left: 30px;
            text-decoration: none;
            color: var(--color-purple-deep);
            font-weight: 500;
            display: flex; align-items: center; gap: 8px;
            transition: color 0.3s ease;
        }
        .back-link:hover { color: var(--color-purple-mid); }

        .login-container {
            background: var(--color-white);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-strong);
            width: 350px;
            text-align: center;
        }
        .login-container h2 {
            font-family: var(--font-heading);
            font-size: 2.5em;
            color: var(--color-purple-deep);
            margin-top: 0;
            margin-bottom: 30px;
        }
        
        .message {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        .message.error { background-color: #FEE2E2; color: #991B1B; }
        .message.success { background-color: #D1FAE5; color: #065F46; }

        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-muted-text);
        }
        
        /* Style untuk ikon mata (toggle password) */
        .input-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-muted-text);
            cursor: pointer;
            z-index: 10; /* Pastikan di atas input */
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 40px 12px 40px; /* Padding kanan ditambah untuk ikon mata */
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: var(--font-body);
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        .input-group input:focus {
            outline: none;
            border-color: var(--color-purple-mid);
        }

        .login-btn {
            display: block; width: 100%; padding: 1rem;
            background: var(--color-purple-mid); color: var(--color-white);
            text-decoration: none; text-align: center; font-size: 1.1em;
            font-weight: 600; border-radius: 50px;
            margin-top: 30px; transition: all 0.3s ease;
            border: none; cursor: pointer;
        }
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(123, 104, 238, 0.4);
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
    <div class="login-container">
        <h2>Login Karyawan</h2>
        <?php
        if (isset($_GET['pesan'])) {
            if ($_GET['pesan'] == "gagal") {
                echo "<p class='message error'>Login gagal! Username atau password salah.</p>";
            } else if ($_GET['pesan'] == "logout") {
                echo "<p class='message success'>Anda telah berhasil logout.</p>";
            } else if ($_GET['pesan'] == "belum_login") {
                echo "<p class='message error'>Anda harus login untuk mengakses halaman.</p>";
            }
        }
        ?>
        <form action="proses_login.php" method="post">
            <div class="input-group">
                <i class="fas fa-user icon"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock icon"></i>
                <input type="password" name="password" id="passwordInput" placeholder="Password" required>
                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <button type="submit" class="login-btn">LOGIN</button>
        </form>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#passwordInput');

        togglePassword.addEventListener('click', function (e) {
            // Toggle tipe atribut input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle ikon mata (buka/tutup)
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>