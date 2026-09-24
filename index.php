<?php
include 'koneksi.php';

$kategori_query = mysqli_query($koneksi, "SELECT * FROM Kategori WHERE status_kategori = 'Tersedia'");
$menu_items = [];
$menu_query = mysqli_query($koneksi, "SELECT * FROM Menu ORDER BY nama_menu ASC");
while ($row = mysqli_fetch_assoc($menu_query)) {
    $menu_items[$row['id_kategori']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Kedai - Kopi & Cerita</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>

        :root {
            --color-primary: #6D28D9;
            --color-secondary: #8B5CF6;
            --color-light-bg: #F9FAFB;
            --color-surface: #FFFFFF;
            --color-dark-text: #111827;
            --color-muted-text: #6B7280;
            --font-heading: 'Syne', sans-serif;
            --font-body: 'Poppins', sans-serif;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            --border-radius: 12px;
        }

        /* --- Global & Preloader --- */
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
body {
    font-family: var(--font-body);
    margin: 0;
    background: linear-gradient(135deg, #ffffffff 0%, #c0aaffff 100%);
    color: var(--color-dark-text);
    overflow-x: hidden;
    min-height: 100vh;
}
        #preloader { position: fixed; inset: 0; background: var(--color-primary); z-index: 10000; display: flex; align-items: center; justify-content: center; }
        .spinner { width: 50px; height: 50px; border: 5px solid rgba(255,255,255,0.3); border-top: 5px solid var(--color-white); border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
        .section { padding: 120px 0; }
        .section-title {
            font-family: var(--font-heading);
            font-size: 3.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 60px;
            color: var(--color-dark-text);
        }
        .section-title span { color: var(--color-primary); }
        .cta-button {
            display: inline-block; padding: 1rem 2.5rem; background: var(--color-primary);
            color: var(--color-white); text-decoration: none; font-weight: 600;
            border-radius: 50px; transition: all 0.3s ease; border: none; cursor: pointer;
        }
        .cta-button:hover { transform: translateY(-3px); box-shadow: 0 5px 20px rgba(109, 40, 217, 0.3); }

        .header {
            position: fixed; width: 100%; top: 0; left: 0; z-index: 1000;
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.5rem 4rem;
            background-color: transparent;
            transition: background-color 0.4s ease, padding 0.4s ease, box-shadow 0.4s ease;
        }
        .header.scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 4rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
.logo {
    font-family: 'Poppins', sans-serif; 
    font-weight: 700; 
    font-size: 1.5rem; 
    text-decoration: none;
    color: var(--color-primary);
    display: flex;
    align-items: center;
    gap: 12px; 
}
        .logo img {height: 70px;}
        .header-nav { display: flex; align-items: center; gap: 2rem; }
        .header-nav .nav-links a {
            text-decoration: none; color: var(--color-dark-text);
            font-weight: 500; position: relative;
        }
        .header-nav .nav-links a::after {
            content: ''; position: absolute;
            width: 0; height: 2px;
            background: var(--color-primary);
            bottom: -5px; left: 50%;
            transform: translateX(-50%);
            transition: width 0.3s ease;
        }
        .header-nav .nav-links a:hover::after { width: 100%; }
        .nav-login a {
            text-decoration: none; color: var(--color-primary); font-weight: 500;
            border: 1px solid var(--color-primary); padding: 0.6rem 1.2rem;
            border-radius: 50px; transition: all 0.3s ease; white-space: nowrap;
        }
        .nav-login a:hover { background-color: var(--color-primary); color: var(--color-white); }
        
        .hero {
            padding-top: 150px;
            min-height: 90vh;
            display: flex;
            align-items: center;
        }
        .hero-content { display: grid; grid-template-columns: 1fr 1.2fr; align-items: center; gap: 4rem; }
        .hero-text h1 {
            font-family: var(--font-heading);
            font-size: clamp(3rem, 7vw, 5rem);
            line-height: 1.1;
            font-weight: 800;
            margin: 0 0 1.5rem 0;
        }
        .hero-text p { font-size: 1.1rem; margin-bottom: 2.5rem; color: var(--color-muted-text); line-height: 1.7; }
        .hero-image img { width: 100%; border-radius: var(--border-radius); box-shadow: var(--shadow); }
        
        #menu { background-color: var(--color-surface); }
        .menu-controls { display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 50px; flex-wrap: wrap; }
        .filter-btn, #search-menu {
            background: var(--color-surface); border: 1px solid #ffffffff;
            padding: 12px 25px; border-radius: 50px; cursor: pointer;
            font-weight: 500; transition: all 0.3s ease;
        }
        .filter-btn.active, .filter-btn:hover { background-color: var(--color-primary); color: var(--color-white); border-color: var(--color-primary); }
        #search-menu { font-family: var(--font-body); font-size: 1em; width: 250px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        .menu-card {
            background: var(--color-surface);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-subtle);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: none;
            border: 1px solid #E5E7EB;
        }
        .menu-card:hover { transform: translateY(-8px); box-shadow: var(--shadow); }
        .menu-card img { width: 100%; height: 200px; object-fit: cover; border-top-left-radius: var(--border-radius); border-top-right-radius: var(--border-radius); }
        .menu-card-content { padding: 20px; text-align: center; }
        .menu-card h3 { font-family: var(--font-heading); font-size: 1.4rem; margin: 0 0 10px 0; }
        .menu-card .harga { font-size: 1.3rem; color: var(--color-primary); font-weight: bold; }

        .parallax-section {
            padding: 150px 0;
            background-image: url('gambar/foodcourt.jpg');
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
            color: white;
            text-align: center;
        }
        .parallax-section::before { content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.6); }
        .parallax-content { position: relative; z-index: 2; }
        .parallax-content h2 { font-family: var(--font-heading); font-size: 3rem; margin-bottom: 2rem; text-shadow: 2px 2px 10px rgba(0,0,0,0.7); }
        
        #lokasi { background-color: var(--color-surface); }
        .lokasi-container { display: grid; grid-template-columns: 1fr 1.5fr; align-items: center; gap: 50px; }
        .lokasi-teks h3 { font-family: var(--font-heading); font-size: 2.5rem; color: var(--color-dark-text); }
        .lokasi-teks p { color: var(--color-muted-text); line-height: 1.8; }
        .lokasi-peta iframe { width: 100%; height: 450px; border: 0; border-radius: var(--border-radius); }

        .footer { background: var(--color-dark-text); color: var(--color-light-bg); padding: 80px 4rem 30px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 50px; margin-bottom: 50px; }
        .footer-about .logo, .footer-links h4 { font-family: var(--font-heading); color: var(--color-primary); }
        .footer-links ul { list-style: none; padding: 0; }
        .footer-links a { text-decoration: none; color: var(--color-muted-text); transition: color 0.3s ease; display: inline-block; margin-bottom: 10px;}
        .footer-links a:hover { color: var(--color-white); }
        .footer-socials a { color: var(--color-muted-text); margin-right: 15px; font-size: 1.5rem; transition: color 0.3s ease; }
        .footer-socials a:hover { color: var(--color-white); }
        .footer-bottom { text-align: center; padding-top: 30px; border-top: 1px solid #374151; color: var(--color-muted-text); }
        
        .modal { position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
        .modal.active { opacity: 1; visibility: visible; }
        .modal-content { background: var(--color-surface); color: var(--color-dark-text); padding: 40px; border-radius: var(--border-radius); width: 90%; max-width: 800px; display: flex; gap: 30px; position: relative; transform: scale(0.9); transition: transform 0.3s ease; }
        .modal.active .modal-content { transform: scale(1); }
        .modal-img { width: 50%; } .modal-img img { width: 100%; border-radius: var(--border-radius); }
        .modal-details { width: 50%; } .modal-details h2 { font-family: var(--font-heading); font-size: 2.5rem; margin-top: 0; color: var(--color-primary); }
        .modal-details .harga { font-size: 2rem; color: var(--color-primary); margin: 20px 0; }
        .close-modal { position: absolute; top: 15px; right: 20px; font-size: 2rem; color: #ccc; cursor: pointer; transition: color 0.3s ease; }
        .close-modal:hover { color: var(--color-dark-text); }

        #toast-container { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 3000; }
        .toast { background: var(--color-purple-deep); color: var(--color-white); font-weight: 500; padding: 15px 25px; border-radius: 50px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); opacity: 0; transform: translateY(20px); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); }
        .toast.show { opacity: 1; transform: translateY(0); }

        .cart-link { position: fixed; bottom: 30px; right: 30px; z-index: 1000; background: var(--color-primary); color: var(--color-white); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: transform 0.3s ease; }
        .cart-link:hover { transform: scale(1.1); }
        span#cart-count { position: absolute; top: -5px; right: -5px; background: var(--color-dark-purple); color: var(--color-white); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 500; }

/* --- CSS UNTUK BEST SELLER --- */
#bestseller { 
    background-color: var(--color-white); 
}
.bestseller-container { 
    display: flex; 
    gap: 30px; 
    padding: 20px 0; 
    overflow-x: auto; 
    scrollbar-width: none; /* Untuk Firefox */
}
.bestseller-container::-webkit-scrollbar { 
    display: none; /* Untuk Chrome, Safari, Opera */
}
.bestseller-card {
    flex: 0 0 300px; 
    background: var(--color-white);
    border-radius: var(--border-radius); 
    box-shadow: var(--shadow);
    text-align: center; 
    padding: 1.5rem;
    transition: transform 0.3s ease;
}
.bestseller-card:hover {
    transform: translateY(-5px);
}
.bestseller-card img { 
    width: 120px; 
    height: 120px; 
    border-radius: 50%; 
    object-fit: cover; 
    margin-bottom: 1rem; 
    border: 4px solid var(--color-light-bg); 
}
.bestseller-card h3 { 
    font-family: var(--font-heading); 
    font-size: 1.4rem; 
    margin: 0.5rem 0; 
    color: var(--color-dark-text); 
}
    </style>
</head>
<body>

    <div id="preloader"><div class="spinner"></div></div>

    <header class="header" id="header">
        <a href="#" class="logo">
    <img src="gambar/logoHK.png" alt="Logo History Kedai">
    HistoryKedai
        </a>
        <div class="header-nav">
            <nav class="nav-links">
                <a href="#">Beranda</a>
                <a href="#menu">Menu</a>
                <a href="#lokasi">Lokasi</a>
            </nav>
            <div class="nav-login"><a href="login.php">Login Karyawan</a></div>
        </div>
    </header>

    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Setiap Tegukan, Punya Cerita.</h1>
                <p>Kami percaya setiap minuman adalah sebuah pengalaman. Temukan pengalaman rasa favoritmu di sini, disajikan segar hanya untukmu.</p>
                <a href="#menu" class="cta-button">Jelajahi Menu</a>
            </div>
            <div class="hero-image">
                <img src="gambar/minuman.png">
            </div>
        </div>
    </section>

    <main>
<div class="container" style="text-align: center; margin-top: 80px; margin-bottom: -60px; position: relative; z-index: 10;">
    <a href="#menu" class="cta-button" style="box-shadow: 0 10px 25px rgba(255, 255, 255, 0.3);">Pesan Sekarang</a>
</div>
<section class="section" id="bestseller">
    <div class="container">
        <h2 class="section-title" style="margin-top: 30px;">Menu Andalan Kami</h2>
        <div class="bestseller-container">
            <?php 
            $bestseller_query = mysqli_query($koneksi, "SELECT * FROM Menu ORDER BY RAND() LIMIT 5");
            while($item = mysqli_fetch_assoc($bestseller_query)): 
            ?>
            <div class="bestseller-card">
                <img src="gambar/<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama_menu']); ?>">
                <h3><?php echo htmlspecialchars($item['nama_menu']); ?></h3>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="section" id="menu">
    <div class="container">
        <h2 class="section-title"><span>Daftar</span> Menu</h2>
        <div class="menu-controls">
            <button class="filter-btn active" data-filter="all">Semua Menu</button>
            <?php foreach ($kategori_query as $kategori) : ?>
                <button class="filter-btn" data-filter="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>">
                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                </button>
            <?php endforeach; ?>
            <input type="text" id="search-menu" placeholder="Cari nama menu...">
        </div>
        <div class="menu-grid">
            <?php if (!empty($menu_items)): foreach ($kategori_query as $kategori) : if (isset($menu_items[$kategori['id_kategori']])) : foreach ($menu_items[$kategori['id_kategori']] as $menu) : ?>
                <div class="menu-card" data-category="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>" data-name="<?php echo htmlspecialchars(strtolower($menu['nama_menu'])); ?>" onclick="openModal(this)"
                     data-id="<?php echo $menu['id_menu']; ?>" data-nama-lengkap="<?php echo htmlspecialchars(addslashes($menu['nama_menu'])); ?>" data-harga="<?php echo $menu['harga']; ?>"
                     data-deskripsi="<?php echo htmlspecialchars(addslashes($menu['deskripsi'])); ?>" data-gambar="gambar/<?php echo $menu['gambar']; ?>">
                    <img src="gambar/<?php echo $menu['gambar']; ?>" alt="<?php echo htmlspecialchars($menu['nama_menu']); ?>">
                    <div class="menu-card-content">
                        <h3><?php echo htmlspecialchars($menu['nama_menu']); ?></h3>
                        <div class="harga">Rp <?php echo number_format($menu['harga']); ?></div>
                    </div>
                </div>
            <?php endforeach; endif; endforeach; endif; ?>
        </div>
    </div>
</section>

<section class="parallax-section">
    <div class="parallax-content">
        <h2>Rasa yang Bercerita.</h2>
        </div>
</section>
        
<section class="section" id="lokasi">
            <div class="container">
                <h2 class="section-title"><span>Temukan</span> Kami</h2>
                <div class="lokasi-container">
                    
                    <div class="lokasi-teks">
                        <h3>Kunjungi Kedai Kami</h3>

                        <div class="kedai-photo-container" style="margin-bottom: 20px; margin-top: 10px;">
                            <img src="gambar/kedai.jpg" alt="Foto Kedai" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        </div>
                        <p>Kami menanti Anda untuk berbagi cerita sambil menikmati minuman segar. Temukan kami di Food Court Universitas Negeri Makassar dengan suasana yang nyaman.</p>
                        
                        <p>
                            <strong>Alamat:</strong><br> 
                            Jl. Raya Pendidikan No. 22, Tidung, Kec. Rappocini<br>
                            Makassar, Sulawesi Selatan, 90221
                        </p>
                        <p>
                            <strong>Jam Buka:</strong><br>
                            Senin - Sabtu: 07:00 - 17:00 WITA
                        </p>
                    </div>

                    <div class="lokasi-peta">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31788.75096329477!2d119.39795497431642!3d-5.168843000000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbee291f084750b%3A0xa4f4abfdff834d4b!2sUniversitas%20Negeri%20Makassar!5e0!3m2!1sid!2sid!4v1759409672141!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <h3 class="logo">History Kedai</h3>
                    <p>Menyajikan cerita terbaik dalam setiap tegukan.</p>
                </div>
                <div class="footer-links">
                    <h4>Tautan Cepat</h4>
                    <ul>
                        <li><a href="#">Beranda</a></li>
                        <li><a href="#menu">Menu</a></li>
                        <li><a href="#lokasi">Lokasi</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Kontak</h4>
                    <p>kontak@historykedai.com</p>
                </div>
                <div class="footer-links">
                    <h4>Sosial Media</h4>
                    <div class="footer-socials">
                        <a href="https://www.instagram.com/kedaihistory_?igsh=MWNkcTkybDR5dmhncg=="><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">&copy; <?php echo date("Y"); ?> History Kedai.</div>
        </div>
    </footer>
    
    <div class="modal" id="menu-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">×</span>
            <div class="modal-img"><img id="modal-img" src="" alt="Detail Menu"></div>
            <div class="modal-details">
                <h2 id="modal-name"></h2>
                <p id="modal-desc"></p>
                <div class="harga" id="modal-price"></div>
                <button id="modal-add-to-cart" class="cta-button" style="width:100%">Tambah ke Keranjang</button>
            </div>
        </div>
    </div>
    <div id="toast-container"></div>
    <a href="keranjang.php" class="cart-link">
        <i class="fas fa-shopping-cart"></i>
        <span id="cart-count">0</span>
    </a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

<script>
    // SCRIPT LENGKAP DENGAN ANIMASI DAN FUNGSI STABIL
    document.addEventListener("DOMContentLoaded", function() {
        // PRELOADER
        const preloader = document.getElementById('preloader');
        gsap.to(preloader, { opacity: 0, duration: 1, delay: 0.5, onComplete: () => preloader.style.display = 'none' });

        // ANIMASI GSAP
        gsap.registerPlugin(ScrollTrigger);
        
        // Animasi Hero
        gsap.from('.hero-text > *', { opacity: 0, y: 30, stagger: 0.3, duration: 1, delay: 0.8 });
        gsap.from('.hero-image', { opacity: 0, scale: 0.9, duration: 1, delay: 0.5 });
        
        // Animasi Scroll-Trigger untuk semua section
        document.querySelectorAll('.section').forEach(section => {
            const elems = section.querySelectorAll('.section-title, .bestseller-card, .menu-controls, .menu-card, .about-content > div, .gallery-item, .lokasi-container > div, .footer-grid > div');
            if (elems.length > 0) {
                gsap.from(elems, {
                    opacity: 0,
                    y: 50,
                    duration: 0.8,
                    stagger: 0.1,
                    scrollTrigger: {
                        trigger: section,
                        start: 'top 80%',
                        toggleActions: 'play none none none'
                    }
                });
            }
        });
        
        // Efek Header saat scroll
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // LOGIKA FILTER STABIL (Anti Gagal)
        const filterBtns = document.querySelectorAll('.filter-btn');
        const searchInput = document.getElementById('search-menu');
        const menuCards = document.querySelectorAll('.menu-card');

        const filterAndSearch = () => {
            const activeFilter = document.querySelector('.filter-btn.active').dataset.filter.toLowerCase();
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : "";
            
            menuCards.forEach(card => {
                const cardCategory = card.dataset.category.toLowerCase();
                const cardName = card.dataset.name.toLowerCase();
                const isCategoryMatch = activeFilter === 'all' || cardCategory === activeFilter;
                const isSearchMatch = searchTerm === "" || cardName.includes(searchTerm);
                
                if (isCategoryMatch && isSearchMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        };

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelector('.filter-btn.active').classList.remove('active');
                btn.classList.add('active');
                filterAndSearch();
            });
        });
        if(searchInput) {
            searchInput.addEventListener('keyup', filterAndSearch);
        }

        // LOGIKA KERANJANG, MODAL, TOAST (LENGKAP)
        const cart = JSON.parse(sessionStorage.getItem('cart')) || {};

        const updateCartCount = () => {
            const count = Object.values(cart).reduce((sum, item) => sum + item.qty, 0);
            const cartCountEl = document.getElementById('cart-count');
            if (cartCountEl) cartCountEl.innerText = count;
        };

        const showToast = (message) => {
            const container = document.getElementById('toast-container');
            if(!container) return;
            const toast = document.createElement('div');
            toast.className = 'toast show';
            toast.innerText = message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => container.removeChild(toast), 500);
            }, 3000);
        };

        const addToCart = (id, name, price) => {
            id = parseInt(id); price = parseFloat(price);
            if (cart[id]) { cart[id].qty++; } else { cart[id] = { name: name, price: price, qty: 1 }; }
            sessionStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            showToast(`"${name}" ditambahkan!`);
        };
        
        const modal = document.getElementById('menu-modal');
        const modalImg = document.getElementById('modal-img');
        const modalName = document.getElementById('modal-name');
        const modalDesc = document.getElementById('modal-desc');
        const modalPrice = document.getElementById('modal-price');
        let modalAddToCartBtn = document.getElementById('modal-add-to-cart');

        window.openModal = (cardElement) => {
            if(!modal) return;
            const data = cardElement.dataset;
            modalImg.src = data.gambar;
            modalName.innerText = data.namaLengkap;
            modalDesc.innerText = data.deskripsi;
            modalPrice.innerText = `Rp ${parseInt(data.harga).toLocaleString('id-ID')}`;
            let newBtn = modalAddToCartBtn.cloneNode(true);
            modalAddToCartBtn.parentNode.replaceChild(newBtn, modalAddToCartBtn);
            modalAddToCartBtn = newBtn;
            modalAddToCartBtn.onclick = () => {
                addToCart(data.id, data.namaLengkap, data.harga);
                closeModal();
            };
            modal.classList.add('active');
        };

        window.closeModal = () => { if(modal) modal.classList.remove('active'); };
        updateCartCount();
    });
</script>
</body>
</html>
