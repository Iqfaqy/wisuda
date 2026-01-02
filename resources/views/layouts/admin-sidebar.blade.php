<!-- SIDEBAR -->
<aside class="sidebar">
  <h3>MENU UTAMA</h3>
  <ul class="sidebar-menu">
    <li><a href="{{ route('admin.dashboard') }}" class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i class="fas fa-chart-line"></i> Dashboard
    </a></li>
    <li><a href="{{ route('admin.qrcode.index') }}" class="menu-link {{ request()->routeIs('admin.qrcode.*') ? 'active' : '' }}">
      <i class="fas fa-qrcode"></i> QR Code
    </a></li>
    <li><a href="{{ route('admin.kursi.index') }}" class="menu-link {{ request()->routeIs('admin.kursi.*') ? 'active' : '' }}">
      <i class="fas fa-chair"></i> Manajemen Kursi
    </a></li>
    <li><a href="{{ route('admin.presensi.index') }}" class="menu-link {{ request()->routeIs('admin.presensi.*') ? 'active' : '' }}">
      <i class="fas fa-clipboard-check"></i> Presensi
    </a></li>
    <li><a href="{{ route('admin.foto.index') }}" class="menu-link {{ request()->routeIs('admin.foto.*') ? 'active' : '' }}">
      <i class="fas fa-camera"></i> Foto Wisuda
    </a></li>
    <li><a href="{{ route('admin.laporan.index') }}" class="menu-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
      <i class="fas fa-file-pdf"></i> Laporan
    </a></li>
    <li><a href="{{ route('admin.wisudawan.index') }}" class="menu-link {{ request()->routeIs('admin.wisudawan.*') ? 'active' : '' }}">
      <i class="fas fa-user-graduate"></i> Data Wisudawan
    </a></li>
    <li><a href="{{ route('admin.users.index') }}" class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <i class="fas fa-users-cog"></i> Kelola Akun
    </a></li>
  </ul>
  <div class="sidebar-logo-container">
    <div>
      <img src="{{ asset('images/bg.png') }}" alt="UNSIQ Logo">
      <p class="university-name">Universitas Sains Al-Qur'an</p>
    </div>
  </div>
</aside>
