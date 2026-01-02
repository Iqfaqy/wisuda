@extends('layouts.app')

@section('title', 'Profil Saya - Sistem Wisuda')
@section('bodyClass', 'dashboard-wisudawan')

@push('styles')
    {{-- Pastikan file CSS ini ada di public/css/wisudawan/profile.css --}}
    <link rel="stylesheet" href="{{ asset('css/wisudawan/profile.css') }}">
    <style>
        /* --- LAYOUT UTAMA --- */
        .profile-main-card { 
            padding: 3rem 2rem; 
            text-align: center; 
        }
        
        .profile-avatar {
            width: 100px; height: 100px; margin: 0 auto 1rem;
            background: #0d6efd; color: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; font-weight: bold;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        }

        .profile-name { font-size: 1.5rem; font-weight: 700; color: #333; margin-bottom: 5px; }
        .muted { color: #666; margin-bottom: 0.2rem; font-size: 0.95rem; }

        /* --- IPK BOX --- */
        .ipk-box {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 1.5rem 0;
            border: 1px solid #e9ecef;
        }
        .ipk-icon { font-size: 2rem; color: #ffc107; margin-bottom: 0.5rem; }
        .ipk-score { font-size: 2.5rem; font-weight: 800; color: #2c3e50; line-height: 1.2; }
        .ipk-honor { font-size: 1rem; color: #198754; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        .divider { height: 1px; background: #e9ecef; margin: 2rem 0; }

        /* --- HEADER DETAIL & TOMBOL EDIT --- */
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .detail-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.25rem;
            font-weight: 700;
            border-left: 5px solid #0d6efd;
            padding-left: 15px;
        }

        .btn-edit {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-edit:hover {
            background-color: #0b5ed7;
        }

        /* --- GRID DATA --- */
        .profile-section {
            text-align: left;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1rem;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pd-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .info-item {
            background: #fff;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            transition: all 0.2s;
        }
        
        .info-item:hover {
            border-color: #b0c4de;
            background: #fdfdfd;
        }

        .info-label {
            display: block;
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-value {
            font-size: 1.1rem;
            color: #111827; /* Hitam pekat agar jelas */
            font-weight: 600;
            line-height: 1.4;
        }

        /* --- MODAL STYLING --- */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;
        }
        .modal-content {
            background: white; width: 90%; max-width: 600px; padding: 2rem;
            border-radius: 12px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; }
        .form-control {
            width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;
            font-size: 1rem; color: #333;
        }
        .modal-actions { text-align: right; margin-top: 20px; }
        .btn-cancel { background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; margin-right: 10px; }
        .btn-save { background: #0d6efd; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }

    </style>
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
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger">Keluar</button>
    </form>
  </div>
</header>

<div class="dashboard-container">
  <aside class="sidebar">
    <h3>MENU UTAMA</h3>
    <ul class="sidebar-menu">
      <li><a href="{{ route('wisudawan.dashboard') }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
      <li><a href="{{ route('wisudawan.profile') }}" class="active"><i class="fas fa-user"></i> Profil Saya</a></li>
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
    <section class="page-empty profile-page">
      <h2>Profil Saya</h2>

      <div class="profile-center">
        <div class="card profile-main-card">
          <div class="profile-avatar">{{ substr($user->name, 0, 1) }}</div>
          <h3 class="profile-name">{{ $user->name }}</h3>
          <p class="muted">NIM: <strong>{{ $user->nim }}</strong></p>
          <p class="muted">{{ $wisudawan->prodi ?? 'Prodi Belum Diisi' }} • {{ $wisudawan->fakultas ?? 'Fakultas Belum Diisi' }}</p>

          <div class="ipk-box">
            <i class="fas fa-medal ipk-icon" aria-hidden="true"></i>
            <div class="ipk-label">Indeks Prestasi Komulatif</div>
            <div class="ipk-score">{{ $wisudawan->ipk ?? '0.00' }}</div>
            <div class="ipk-honor">{{ $wisudawan->predikat ?? 'Belum Ada Predikat' }}</div>
          </div>

          <div class="divider"></div>

          <div class="detail-header">
            <h3>Detail Informasi Wisudawan</h3>
          </div>

          <div class="profile-section">
            <button onclick="openEditModal()" class="btn-edit" style="margin-bottom: 1.5rem;">
                <i class="fas fa-edit"></i> Edit Data
            </button>
            <h4 class="section-title">Data Pribadi</h4>
            <div class="pd-grid">
              <div class="info-item" style="grid-column: 1 / -1;">
                  <span class="info-label">Judul Skripsi</span>
                  <div class="info-value">{{ $wisudawan->judul_skripsi ?? '-' }}</div>
              </div>
              <div class="info-item">
                  <span class="info-label">Nama Ayah (Wali)</span>
                  <div class="info-value">{{ $wisudawan->nama_ortu ?? '-' }}</div>
              </div>
              <div class="info-item">
                  <span class="info-label">Nama Ibu</span>
                  <div class="info-value">{{ $wisudawan->nama_ibu ?? '-' }}</div>
              </div>
              <div class="info-item">
                  <span class="info-label">Ukuran Toga</span>
                  <div class="info-value">{{ $wisudawan->ukuran_toga ?? '-' }}</div>
              </div>
            </div>
          </div>

          <div class="profile-section">
            <h4 class="section-title">Kontak</h4>
            <div class="pd-grid">
              <div class="info-item">
                  <span class="info-label">Alamat Email</span>
                  <div class="info-value">{{ $user->email }}</div>
              </div>
              <div class="info-item">
                  <span class="info-label">Nomor Telepon / WA</span>
                  <div class="info-value">{{ $wisudawan->telepon ?? '-' }}</div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
  </main>
</div>

<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 15px; color: #333;">Edit Data Wisudawan</h3>
        
        <form action="{{ route('wisudawan.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Nomor Telepon / WhatsApp</label>
                <input type="text" name="telepon" value="{{ $wisudawan->telepon }}" class="form-control" required placeholder="Contoh: 08123456789">
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Judul Skripsi</label>
                <textarea name="judul_skripsi" rows="3" class="form-control" required placeholder="Masukkan judul skripsi lengkap...">{{ $wisudawan->judul_skripsi }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label class="form-label">Nama Ayah</label>
                    <input type="text" name="nama_ortu" value="{{ $wisudawan->nama_ortu }}" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Nama Ibu</label>
                    <input type="text" name="nama_ibu" value="{{ $wisudawan->nama_ibu }}" class="form-control" required>
                </div>
            </div>

            <div style="margin-top: 1rem;">
                <label class="form-label">Ukuran Toga</label>
                <select name="ukuran_toga" class="form-control" required>
                    <option value="">-- Pilih Ukuran --</option>
                    <option value="S" {{ ($wisudawan->ukuran_toga == 'S') ? 'selected' : '' }}>S (Small)</option>
                    <option value="M" {{ ($wisudawan->ukuran_toga == 'M') ? 'selected' : '' }}>M (Medium)</option>
                    <option value="L" {{ ($wisudawan->ukuran_toga == 'L') ? 'selected' : '' }}>L (Large)</option>
                    <option value="XL" {{ ($wisudawan->ukuran_toga == 'XL') ? 'selected' : '' }}>XL (Extra Large)</option>
                    <option value="XXL" {{ ($wisudawan->ukuran_toga == 'XXL') ? 'selected' : '' }}>XXL (Double XL)</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeEditModal()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-save">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal() {
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        var modal = document.getElementById('editModal');
        if (event.target == modal) {
            closeEditModal();
        }
    }
</script>
@endsection