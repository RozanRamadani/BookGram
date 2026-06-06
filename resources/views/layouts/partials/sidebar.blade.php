<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Guest' }}</span>
                    <span class="text-secondary text-small">Administrator</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('home') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kategori.index') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('buku.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('buku.index') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-variant menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('barang.index') }}">
                <span class="menu-title">Barang</span>
                <i class="mdi mdi-tag-multiple menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('scanner.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('scanner.index') }}">
                <span class="menu-title">Scanner</span>
                <i class="mdi mdi-barcode-scan menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('mahasiswa.index') }}">
                <span class="menu-title">Data Mahasiswa</span>
                <i class="mdi mdi-account-group menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('absensi.index') }}">
                <span class="menu-title">Absensi NFC</span>
                <i class="mdi mdi-nfc-variant menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('kunjungan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kunjungan.index') }}">
                <span class="menu-title">Kunjungan Toko</span>
                <i class="mdi mdi-map-marker-radius menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('toko.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('toko.index') }}">
                <span class="menu-title">Data Toko</span>
                <i class="mdi mdi-store menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('kunjungan.riwayat*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kunjungan.riwayat') }}">
                <span class="menu-title">Riwayat Kunjungan</span>
                <i class="mdi mdi-clipboard-check menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('studi-kasus.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('studi-kasus.table-html') }}">
                <span class="menu-title">Studi Kasus JS</span>
                <i class="mdi mdi-code-tags menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('studi-kasus.wilayah-*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('studi-kasus.wilayah-jquery') }}">
                <span class="menu-title">AJAX Wilayah</span>
                <i class="mdi mdi-map menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('studi-kasus.pos-*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('studi-kasus.pos-jquery') }}">
                <span class="menu-title">POS Ajax/Axios</span>
                <i class="mdi mdi-cart menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>
