<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toolsme — Solusi Peminjaman Alat Terpercaya</title>
    <link rel="icon" type="image/png" href="{{ asset('toolsme.png') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

<!-- ============================================
NAVBAR
============================================ -->
<header class="navbar-landing">
    <div class="nav-container">
        <a href="{{ route('landing') }}" class="nav-brand">
            <h1>Toolsme</h1>
            <span>Management System</span>
        </a>

        <nav class="nav-menu" id="navMenu">
            <a href="#hero">Beranda</a>
            <a href="#produk">Produk</a>
            <a href="#fitur">Fitur</a>
            <a href="#tentang">Tentang</a>
            <a href="#kontak">Kontak</a>
        </nav>

        <div class="nav-actions">
            @auth
                <div class="nav-user">
                    <span class="material-symbols-outlined">account_circle</span>
                    <span class="nav-username">{{ auth()->user()->name }}</span>
                </div>
                <a href="
                    @if(auth()->user()->role === 'admin') {{ route('admin.dashboard') }}
                    @elseif(auth()->user()->role === 'petugas') {{ route('petugas.peminjaman.index') }}
                    @else {{ route('peminjam.katalog') }}
                    @endif
                " class="btn-nav-primary">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-nav-primary">
                    <span class="material-symbols-outlined">login</span>
                    Masuk
                </a>
            @endauth

            <button class="nav-toggle" id="navToggle">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
    </div>
</header>

<!-- ============================================
HERO
============================================ -->
<section class="hero" id="hero">
    <div class="hero-bg"></div>
    <div class="hero-container">
        <div class="hero-content">
            <span class="hero-tag">
                <span class="material-symbols-outlined">verified</span>
                Sistem Peminjaman Alat Terpadu
            </span>
            <h1 class="hero-title">
                Pinjam Alat<br>
                <span class="accent">Jadi Lebih Mudah</span>
            </h1>
            <p class="hero-desc">
                Toolsme adalah platform peminjaman alat laboratorium yang cepat, aman, dan transparan.
                Kelola stok, ajukan peminjaman, dan pantau pengembalian dalam satu sistem.
            </p>
            <div class="hero-actions">
                <a href="{{ route('login') }}" class="btn-hero-primary">
                    <span class="material-symbols-outlined">login</span>
                    Masuk Sekarang
                </a>
                <a href="#produk" class="btn-hero-secondary">
                    <span class="material-symbols-outlined">inventory_2</span>
                    Jelajahi Alat
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ $alats->count() }}+</span>
                    <span class="stat-label">Alat Tersedia</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number">3</span>
                    <span class="stat-label">Role Pengguna</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Akses Sistem</span>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="gear gear-1"><span class="material-symbols-outlined">settings</span></div>
            <div class="gear gear-2"><span class="material-symbols-outlined">settings</span></div>
            <div class="gear gear-3"><span class="material-symbols-outlined">settings</span></div>
            <div class="hero-card">
                <span class="material-symbols-outlined hero-card-icon">handyman</span>
                <p>Kelola Alat dengan Presisi</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
PRODUK (SLIDE CAROUSEL)
============================================ -->
<section class="produk" id="produk">
    <div class="section-container">
        <div class="section-header">
            <span class="section-tag">Katalog Alat</span>
            <h2 class="section-title">Produk <span class="accent">Tersedia</span></h2>
            <p class="section-desc">Alat-alat yang siap dipinjam saat ini</p>
        </div>

        @if($alats->isEmpty())
            <div class="empty-produk">
                <span class="material-symbols-outlined">inventory_2</span>
                <p>Belum ada alat tersedia saat ini.</p>
            </div>
        @else
            <div class="carousel-wrapper">
                <button class="carousel-btn carousel-prev" id="prevBtn">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>

                <div class="carousel-track" id="carouselTrack">
                    @foreach($alats as $alat)
                        <div class="produk-card">
                            <div class="produk-image">
                                @if($alat->gambar)
                                    <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}">
                                @else
                                    <div class="no-image">
                                        <span class="material-symbols-outlined">handyman</span>
                                    </div>
                                @endif
                                <span class="produk-badge">{{ $alat->kategori->nama_kategori ?? 'Umum' }}</span>
                            </div>
                            <div class="produk-body">
                                <h3>{{ $alat->nama_alat }}</h3>
                                <p class="produk-desc">{{ Str::limit($alat->deskripsi ?? 'Alat siap dipinjam untuk keperluan praktikum.', 70) }}</p>
                                <div class="produk-footer">
                                    <span class="produk-stok">
                                        <span class="material-symbols-outlined">inventory</span>
                                        Stok: {{ $alat->stok }}
                                    </span>
                                    <span class="produk-kondisi">
                                        <span class="material-symbols-outlined">check_circle</span>
                                        {{ $alat->status_kondisi }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="carousel-btn carousel-next" id="nextBtn">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>

            <div class="carousel-dots" id="carouselDots"></div>
        @endif

        <div class="produk-cta">
            <a href="{{ route('login') }}" class="btn-section">
                Lihat Semua Alat
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

<!-- ============================================
FITUR
============================================ -->
<section class="fitur" id="fitur">
    <div class="section-container">
        <div class="section-header">
            <span class="section-tag">Keunggulan</span>
            <h2 class="section-title">Fitur <span class="accent">Unggulan</span></h2>
            <p class="section-desc">Dirancang untuk mempermudah pengelolaan alat</p>
        </div>

        <div class="fitur-grid">
            <div class="fitur-card">
                <div class="fitur-icon">
                    <span class="material-symbols-outlined">bolt</span>
                </div>
                <h3>Peminjaman Cepat</h3>
                <p>Ajukan peminjaman alat hanya dalam hitungan detik. Proses persetujuan real-time oleh petugas.</p>
            </div>
            <div class="fitur-card">
                <div class="fitur-icon">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <h3>Stok Real-time</h3>
                <p>Stok alat otomatis berkurang saat disetujui dan kembali saat dikembalikan. Tidak ada lagi stok minus.</p>
            </div>
            <div class="fitur-card">
                <div class="fitur-icon">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
                <h3>Denda Otomatis</h3>
                <p>Keterlambatan pengembalian dihitung otomatis. Transparan dan adil untuk semua pengguna.</p>
            </div>
            <div class="fitur-card">
                <div class="fitur-icon">
                    <span class="material-symbols-outlined">history</span>
                </div>
                <h3>Riwayat Lengkap</h3>
                <p>Semua aktivitas tercatat. Lihat riwayat peminjaman, pengembalian, dan log aktivitas dengan jelas.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
CARA KERJA
============================================ -->
<section class="cara-kerja">
    <div class="section-container">
        <div class="section-header">
            <span class="section-tag">Alur Sistem</span>
            <h2 class="section-title">Cara <span class="accent">Kerja</span></h2>
            <p class="section-desc">Tiga langkah mudah untuk meminjam alat</p>
        </div>

        <div class="steps">
            <div class="step">
                <div class="step-number">01</div>
                <div class="step-icon">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <h3>Pilih Alat</h3>
                <p>Jelajahi katalog alat yang tersedia dan pilih yang kamu butuhkan.</p>
            </div>
            <div class="step-arrow">
                <span class="material-symbols-outlined">arrow_forward</span>
            </div>
            <div class="step">
                <div class="step-number">02</div>
                <div class="step-icon">
                    <span class="material-symbols-outlined">assignment</span>
                </div>
                <h3>Ajukan Pinjam</h3>
                <p>Isi form pengajuan dan tunggu persetujuan dari petugas.</p>
            </div>
            <div class="step-arrow">
                <span class="material-symbols-outlined">arrow_forward</span>
            </div>
            <div class="step">
                <div class="step-number">03</div>
                <div class="step-icon">
                    <span class="material-symbols-outlined">undo</span>
                </div>
                <h3>Kembalikan</h3>
                <p>Kembalikan alat sesuai tanggal. Denda otomatis kalau telat.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
TENTANG
============================================ -->
<section class="tentang" id="tentang">
    <div class="section-container">
        <div class="tentang-grid">
            <div class="tentang-visual">
                <div class="tentang-card">
                    <span class="material-symbols-outlined">precision_manufacturing</span>
                </div>
                <div class="tentang-card-2">
                    <span class="material-symbols-outlined">engineering</span>
                </div>
            </div>
            <div class="tentang-content">
                <span class="section-tag">Tentang Kami</span>
                <h2 class="section-title">Toolsme <span class="accent">Management System</span></h2>
                <p>
                    Toolsme adalah sistem manajemen peminjaman alat yang dikembangkan oleh
                    <strong>ryoCompany</strong> untuk memenuhi kebutuhan pengelolaan alat laboratorium
                    di lingkungan pendidikan.
                </p>
                <p>
                    Kami percaya bahwa pengelolaan alat haruslah <em>transparan, efisien, dan mudah diakses</em>.
                    Dengan Toolsme, proses peminjaman yang dulu rumit kini jadi sederhana.
                </p>

                <div class="tentang-info">
                    <div class="info-item">
                        <span class="material-symbols-outlined">verified</span>
                        <div>
                            <strong>Transparan</strong>
                            <p>Semua transaksi tercatat dan bisa dipantau</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-symbols-outlined">bolt</span>
                        <div>
                            <strong>Efisien</strong>
                            <p>Proses cepat, tanpa antrian manual</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-symbols-outlined">security</span>
                        <div>
                            <strong>Aman</strong>
                            <p>Data terproteksi dengan autentikasi berlapis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
KONTAK
============================================ -->
<section class="kontak" id="kontak">
    <div class="section-container">
        <div class="section-header">
            <span class="section-tag">Hubungi Kami</span>
            <h2 class="section-title">Kontak <span class="accent">& Lokasi</span></h2>
            <p class="section-desc">Kami siap membantu kebutuhan Anda</p>
        </div>

        <div class="kontak-grid">
            <div class="kontak-card">
                <span class="material-symbols-outlined">location_on</span>
                <h3>Alamat</h3>
                <p>Jl. Pendidikan No. 1<br>Indonesia</p>
            </div>
            <div class="kontak-card">
                <span class="material-symbols-outlined">mail</span>
                <h3>Email</h3>
                <p>info@toolsme.com<br>support@toolsme.com</p>
            </div>
            <div class="kontak-card">
                <span class="material-symbols-outlined">call</span>
                <h3>Telepon</h3>
                <p>+62 812-3456-7890<br>Senin-Jumat, 08.00-17.00</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
FOOTER
============================================ -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <h3>Toolsme</h3>
            <p>Solusi peminjaman alat terpercaya untuk pendidikan.</p>
        </div>
        <div class="footer-links">
            <h4>Navigasi</h4>
            <a href="#hero">Beranda</a>
            <a href="#produk">Produk</a>
            <a href="#fitur">Fitur</a>
            <a href="#tentang">Tentang</a>
        </div>
        <div class="footer-links">
            <h4>Akses</h4>
            <a href="{{ route('login') }}">Masuk</a>
            <a href="#kontak">Kontak</a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} ryoCompany — Toolsme Management System. All rights reserved.</p>
    </div>
</footer>

<!-- ============================================
SCRIPTS
============================================ -->
<script>
// ============================================
// NAV TOGGLE (MOBILE)
// ============================================
const navToggle = document.getElementById('navToggle');
const navMenu = document.getElementById('navMenu');

if (navToggle) {
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });

    // Tutup menu kalau klik link
    navMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => navMenu.classList.remove('active'));
    });
}

// ============================================
// CAROUSEL PRODUK
// ============================================
const track = document.getElementById('carouselTrack');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const dotsContainer = document.getElementById('carouselDots');

if (track) {
    const cards = track.querySelectorAll('.produk-card');
    let currentIndex = 0;
    let autoSlideInterval;

    // Tentukan berapa card per view berdasarkan lebar layar
    function getCardsPerView() {
        if (window.innerWidth < 640) return 1;
        if (window.innerWidth < 1024) return 2;
        return 3;
    }

    function getTotalPages() {
        return Math.ceil(cards.length / getCardsPerView());
    }

    // Bikin dots
    function buildDots() {
        dotsContainer.innerHTML = '';
        const totalPages = getTotalPages();
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('button');
            dot.classList.add('carousel-dot');
            if (i === currentIndex) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(i));
            dotsContainer.appendChild(dot);
        }
    }

    // Geser slide
    function updateSlide() {
        const cardWidth = cards[0].offsetWidth;
        const gap = 24;
        const offset = -(currentIndex * getCardsPerView() * (cardWidth + gap));
        track.style.transform = `translateX(${offset}px)`;

        // Update dots
        document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIndex);
        });

        // Disable tombol kalau di ujung
        const totalPages = getTotalPages();
        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex >= totalPages - 1;
    }

    function goToSlide(index) {
        const totalPages = getTotalPages();
        currentIndex = Math.max(0, Math.min(index, totalPages - 1));
        updateSlide();
        resetAutoSlide();
    }

    prevBtn.addEventListener('click', () => goToSlide(currentIndex - 1));
    nextBtn.addEventListener('click', () => goToSlide(currentIndex + 1));

    // Auto slide
    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            const totalPages = getTotalPages();
            currentIndex = currentIndex >= totalPages - 1 ? 0 : currentIndex + 1;
            updateSlide();
        }, 4000);
    }

    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }

    // Pause on hover
    track.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
    track.addEventListener('mouseleave', startAutoSlide);

    // Handle resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            currentIndex = 0;
            buildDots();
            updateSlide();
        }, 200);
    });

    // Init
    buildDots();
    updateSlide();
    startAutoSlide();
}

// ============================================
// SMOOTH SCROLL
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// ============================================
// NAVBAR SCROLL EFFECT
// ============================================
const navbar = document.querySelector('.navbar-landing');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});
</script>

</body>
</html>