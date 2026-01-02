@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Wisuda')
@section('description', 'Dashboard Admin Sistem Wisuda UNSIQ')
@section('bodyClass', 'dashboard-admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/stats-unified.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/dashboard-admin.css') }}">
@endpush

@section('content')
<header>
  <div class="logo">
    <span>🎓</span>
    <span>Sistem Wisuda</span>
  </div>

  <div class="user-info">
    <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
    <div>
      <div class="user-name">{{ auth()->user()->name }}</div>
      <small>Administrator</small>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
      @csrf
      <button type="submit" class="btn btn-sm btn-danger">Keluar</button>
    </form>
  </div>
</header>

<!-- MAIN CONTAINER -->
<div class="dashboard-container">
  <!-- SIDEBAR -->
  @include('layouts.admin-sidebar')

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <!-- PAGE HEADER -->
    <div class="page-header">
      <h1>Dashboard Admin Wisuda</h1>
      <p>Selamat datang di Sistem Informasi Wisuda UNSIQ</p>
      <div class="page-actions">
        <button class="btn btn-primary" onclick="location.reload()">
          <i class="fas fa-sync"></i> Refresh
        </button>
      </div>
    </div>

    <!-- STATISTICS -->
    <div class="stats-grid">
      <div class="stat-card primary" style="border-left-width:6px">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-content">
          <div class="stat-value" id="stat-total-wisudawan">{{ $stats['total_wisudawan'] ?? 0 }}</div>
          <div class="stat-label" style="color: black;">Total Wisudawan</div>
        </div>
      </div>
      <div class="stat-card success" style="border-left-width:6px">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-content">
          <div class="stat-value" id="stat-filled-seats">{{ $stats['total_hadir'] ?? 0 }}</div>
          <div class="stat-label" style="color: black;">Sudah Presensi</div>
        </div>
      </div>
      <div class="stat-card warning" style="border-left-width:6px">
        <div class="stat-icon"><i class="fas fa-user-clock"></i></div>
        <div class="stat-content">
          <div class="stat-value" id="stat-empty-seats">{{ $stats['belum_hadir'] ?? 0 }}</div>
          <div class="stat-label" style="color: black;">Belum Presensi</div>
        </div>
      </div>
      <div class="stat-card danger" style="border-left-width:6px">
        <div class="stat-icon"><i class="fas fa-chair"></i></div>
        <div class="stat-content">
          <div class="stat-value" id="stat-total-seats">{{ $stats['kursi_terisi'] ?? 0 }}</div>
          <div class="stat-label" style="color: black;">Kursi Terisi</div>
        </div>
      </div>
    </div>

    <!-- MENU GRID -->
    <h2 style="margin: 30px 0 20px; color: #1b5e20;">Fitur</h2>
    <div class="menu-grid">
      <div class="menu-card alt1" onclick="window.location.href='{{ route('admin.foto.index') }}'">
        <div class="menu-icon"><i class="fas fa-cloud-upload-alt"></i></div>
        <div class="menu-title">Upload Foto</div>
        <div class="menu-desc" style="color: black;">Upload link Google Drive</div>
      </div>
      <div class="menu-card alt2" onclick="window.location.href='{{ route('admin.qrcode.index') }}'">
        <div class="menu-icon"><i class="fas fa-qrcode"></i></div>
        <div class="menu-title">QR Code</div>
        <div class="menu-desc" style="color: black;">Kelola QR wisudawan</div>
      </div>
      <div class="menu-card alt3" onclick="window.location.href='{{ route('admin.kursi.index') }}'">
        <div class="menu-icon"><i class="fas fa-chair"></i></div>
        <div class="menu-title">Kursi</div>
        <div class="menu-desc" style="color: black;">Atur penempatan kursi</div>
      </div>
      <div class="menu-card alt4" onclick="window.location.href='{{ route('admin.presensi.index') }}'">
        <div class="menu-icon"><i class="fas fa-clipboard-check"></i></div>
        <div class="menu-title">Presensi</div>
        <div class="menu-desc" style="color: black;">Catat kehadiran</div>
      </div>
      <div class="menu-card alt5" onclick="window.location.href='{{ route('admin.laporan.index') }}'">
        <div class="menu-icon"><i class="fas fa-file-alt"></i></div>
        <div class="menu-title">Laporan</div>
        <div class="menu-desc" style="color: black;">Buat laporan</div>
      </div>
      <div class="menu-card alt6" onclick="window.location.href='{{ route('admin.users.index') }}'">
        <div class="menu-icon"><i class="fas fa-users-cog"></i></div>
        <div class="menu-title">Akun</div>
        <div class="menu-desc" style="color: black;">Kelola pengguna</div>
      </div>
    </div>
  </main>
</div>
@endsection
