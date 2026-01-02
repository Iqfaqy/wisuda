@extends('layouts.app')

@section('title', 'Dashboard Wisudawan - Sistem Wisuda')
@section('bodyClass', 'dashboard-wisudawan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/wisudawan/dashboard-wisudawan.css') }}">
@endpush

@section('content')
<header>
  <div class="logo">
    <span class="logo-icon">🎓</span>
    <div class="logo-text">
      <div class="logo-title">Dashboard Wisudawan</div>
      <small class="logo-sub">Selamat datang di sistem wisuda unsiq</small>
    </div>
  </div>

  <div class="user-info">
    <div class="user-avatar">{{ substr($user->name, 0, 1) }}</div>
    <div>
      <div class="user-name">{{ $user->name }}</div>
      <small>Wisudawan</small>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf
      <button type="submit" class="btn btn-sm btn-danger">Keluar</button>
    </form>
  </div>
</header>

<div class="dashboard-container">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <h3>MENU UTAMA</h3>
    <ul class="sidebar-menu">
      <li><a href="{{ route('wisudawan.dashboard') }}" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
      <li><a href="{{ route('wisudawan.profile') }}"><i class="fas fa-user"></i> Profil Saya</a></li>
      <li><a href="{{ route('wisudawan.qrcode') }}"><i class="fas fa-qrcode"></i> QR Code</a></li>
      <li><a href="{{ route('wisudawan.kursi') }}"><i class="fas fa-chair"></i> Info Kursi</a></li>
      <li><a href="{{ route('wisudawan.foto') }}"><i class="fas fa-camera"></i> Foto Wisuda</a></li>
    </ul>
    <div class="sidebar-logo-container">
      <div>
        <img src="{{ asset('images/bg.png') }}" alt="UNSIQ Logo">
        <p class="university-name">Universitas Sains Al-Qur'an</p>
      </div>
    </div>
  </aside>

  <main class="main-content">
    <section class="dashboard-section">
      <!-- Hero -->
      <div class="hero">
        <div class="hero-left">
          <div class="hero-icon"><i class="fas fa-trophy"></i></div>
          <div class="hero-text">
            <h2>Selamat Atas Pencapaian Anda!</h2>
            <p class="hero-sub">Terima kasih sudah menggunakan Sistem Wisuda UNSIQ.</p>
          </div>
        </div>
        <div class="hero-stats">
          <div class="mini-card">
            <div class="mini-label">IPK</div>
            <div class="mini-value">{{ $wisudawan->ipk ?? '-' }}</div>
          </div>
          <div class="mini-card">
            <div class="mini-label">Predikat</div>
            <div class="mini-value">{{ $wisudawan->predikat ?? '-' }}</div>
          </div>
          <div class="mini-card">
            <div class="mini-label">Nomor Kursi</div>
            <div class="mini-value">{{ $kursi->kode_kursi ?? '-' }}</div>
          </div>
        </div>
      </div>

      <!-- Status Cards -->
      <div class="dashboard-row">
        <div class="card attendance-card">
          <div class="card-left"><i class="fas fa-check-circle"></i></div>
          <div class="card-body">
            <div class="card-title">Status Kehadiran</div>
            <div class="card-text">{{ $presensi ? 'Sudah Presensi' : 'Belum Presensi' }}</div>
          </div>
        </div>

        <div class="card event-card">
          <div class="event-header"><i class="fas fa-calendar-alt"></i> Informasi Acara Wisuda</div>
          <div class="event-grid">
            <div class="event-box soft-blue">
              <div class="ev-icon"><i class="fas fa-calendar-day"></i></div>
              <div>
                <div class="ev-label">Tanggal</div>
                <div class="ev-value">
                  <div>Hari {{ $wisudawan->hari_wisuda ?? '1' }}</div>
                </div>
              </div>
            </div>
            <div class="event-box soft-green">
              <div class="ev-icon"><i class="fas fa-clock"></i></div>
              <div>
                <div class="ev-label">Waktu</div>
                <div class="ev-value">08.00 WIB</div>
              </div>
            </div>
            <div class="event-box soft-orange">
              <div class="ev-icon"><i class="fas fa-map-marker-alt"></i></div>
              <div>
                <div class="ev-label">Tempat</div>
                <div class="ev-value">Gedung Sasana</div>
              </div>
            </div>
            <div class="event-box soft-pink">
              <div class="ev-icon"><i class="fas fa-tshirt"></i></div>
              <div>
                <div class="ev-label">Dress Code</div>
                <div class="ev-value">Toga & Kebaya</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Features Grid -->
      <div class="features-grid">
        <a class="feature-card alt-blue" href="{{ route('wisudawan.profile') }}">
          <div class="menu-icon"><i class="fas fa-user"></i></div>
          <div class="menu-title">Profil Saya</div>
          <div class="menu-desc">Lihat dan periksa data pribadi Anda.</div>
          <div class="menu-link">Lihat Detail →</div>
        </a>
        <a class="feature-card alt-green" href="{{ route('wisudawan.qrcode') }}">
          <div class="menu-icon"><i class="fas fa-qrcode"></i></div>
          <div class="menu-title">QR Code Kehadiran</div>
          <div class="menu-desc">Gunakan QR untuk melakukan presensi.</div>
          <div class="menu-link">Lihat Detail →</div>
        </a>
        <a class="feature-card alt-purple" href="{{ route('wisudawan.kursi') }}">
          <div class="menu-icon"><i class="fas fa-chair"></i></div>
          <div class="menu-title">Info Kursi</div>
          <div class="menu-desc">Lihat lokasi dan nomor tempat duduk.</div>
          <div class="menu-link">Lihat Detail →</div>
        </a>
        <a class="feature-card alt-pink" href="{{ route('wisudawan.foto') }}">
          <div class="menu-icon"><i class="fas fa-camera"></i></div>
          <div class="menu-title">Foto Wisuda</div>
          <div class="menu-desc">Unduh foto wisuda Anda.</div>
          <div class="menu-link">Lihat Detail →</div>
        </a>
      </div>

      <!-- Data Pribadi -->
      <section class="profile-section">
        <h3>Data Pribadi</h3>
        <div class="pd-grid">
          <div class="pd-row">
            <div class="pd-item"><span class="pd-key">NIM</span><span class="pd-val">{{ $user->nim }}</span></div>
            <div class="pd-item"><span class="pd-key">Nama Lengkap</span><span class="pd-val">{{ $user->name }}</span></div>
            <div class="pd-item"><span class="pd-key">Email</span><span class="pd-val">{{ $user->email }}</span></div>
            <div class="pd-item"><span class="pd-key">No. Telepon</span><span class="pd-val">{{ $wisudawan->telepon ?? '-' }}</span></div>
          </div>
          <div class="pd-row">
            <div class="pd-item"><span class="pd-key">Program Studi</span><span class="pd-val">{{ $wisudawan->prodi ?? '-' }}</span></div>
            <div class="pd-item"><span class="pd-key">IPK & Predikat</span><span class="pd-val">{{ $wisudawan->ipk ?? '-' }} / {{ $wisudawan->predikat ?? '-' }}</span></div>
            <div class="pd-item"><span class="pd-key">Nama Orang Tua</span><span class="pd-val">{{ $wisudawan->nama_ortu ?? '-' }}</span></div>
            <div class="pd-item"><span class="pd-key">Jumlah Tamu</span><span class="pd-val">{{ $wisudawan->jumlah_tamu ?? 0 }}</span></div>
          </div>
        </div>
      </section>
    </section>
  </main>
</div>
@endsection
