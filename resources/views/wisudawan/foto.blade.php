@extends('layouts.app')

@section('title', 'Foto Wisuda - Sistem Wisuda')
@section('bodyClass', 'dashboard-wisudawan')

@push('styles')
{{-- 1. PANGGIL CSS DASHBOARD AGAR SIDEBAR & LAYOUT SAMA PERSIS --}}
<link rel="stylesheet" href="{{ asset('css/wisudawan/dashboard-wisudawan.css') }}">

{{-- 2. CSS KHUSUS HALAMAN FOTO (SISA DARI FOTO.HTML) --}}
<style>
  /* Styling khusus konten foto */
  .info-banner {
      background: #E6F4EA; border-radius: 18px; padding: 18px 22px;
      display: flex; align-items: center; gap: 18px;
      box-shadow: 0 6px 18px rgba(22,24,26,0.03); margin-bottom: 20px;
  }
  .info-banner .icon {
      width: 64px; height: 64px; border-radius: 50%; background: #fff;
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; color: #1a9a55; box-shadow: 0 4px 12px rgba(24,30,30,0.06);
      flex-shrink: 0;
  }
  .info-banner .content h3 { margin: 0; font-size: 18px; color: #1f2937; }
  .info-banner .content p { margin: 2px 0 0; color: #2f4f3a; font-size: 13px; }

  .section-title { font-size: 22px; margin: 18px 0; color: #374151; font-weight: 600; }

  .drive-cards { display: flex; gap: 18px; flex-wrap: wrap; }
  .drive-card {
      flex: 1 1 300px; background: #fff; border-radius: 16px; padding: 18px;
      box-shadow: 0 6px 18px rgba(22,24,26,0.04); display: flex; flex-direction: column; gap: 12px;
      border: 1px solid #f3f4f6;
  }
  .drive-card .meta { display: flex; align-items: center; gap: 12px; }
  .drive-card .meta .icon-box {
      font-size: 22px; width: 48px; height: 48px; border-radius: 10px; color: #fff;
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .icon-box.blue { background: #1D64E3; }
  .icon-box.green { background: #2FA04C; }
  
  .drive-card input[type=text] {
      width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #e6e9ee; 
      background: #f7f9fc; color: #4b5563; font-size: 0.9rem; outline: none;
  }
  .input-group { display: flex; gap: 8px; }
  
  .btn-copy {
      background: #f1f5f9; border: 1px solid #e2e8f0; padding: 0 16px; border-radius: 8px; 
      cursor: pointer; font-weight: 500; color: #4b5563; transition: all 0.2s;
  }
  .btn-copy:hover { background: #e2e8f0; color: #111; }

  .btn-open {
      margin-top: 5px; padding: 12px; border-radius: 10px; color: #fff; border: none; cursor: pointer;
      text-align: center; text-decoration: none; font-weight: 600; display: block; width: 100%;
  }
  .btn-open.blue { background: #1D64E3; }
  .btn-open.green { background: #2FA04C; }
  .btn-open:hover { opacity: 0.9; }

  .important-card {
      background: #fff; border-radius: 16px; padding: 25px;
      box-shadow: 0 8px 24px rgba(22,24,26,0.04); margin-top: 25px;
      border: 1px solid #f3f4f6;
  }
  .important-card ul { padding-left: 0; list-style: none; margin: 15px 0 0 0; }
  .important-card li { margin: 10px 0; display: flex; align-items: flex-start; gap: 10px; color: #4b5563; }
  .important-card li i { color: #2FA04C; margin-top: 4px; }

  @media(max-width: 768px) { .drive-cards { flex-direction: column; } }

  /* Toast */
  .toast-msg {
      position: fixed; right: 20px; bottom: 20px; padding: 12px 24px;
      background: #1f2937; color: white; border-radius: 8px; font-weight: 500;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); z-index: 9999;
      opacity: 0; transform: translateY(20px); transition: all 0.3s ease; pointer-events: none;
  }
  .toast-msg.show { opacity: 1; transform: translateY(0); }
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
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf
      <button type="submit" class="btn btn-sm btn-danger">Keluar</button>
    </form>
  </div>
</header>

<div class="dashboard-container">
  <aside class="sidebar">
    <h3>MENU UTAMA</h3>
    <ul class="sidebar-menu">
      <li><a href="{{ route('wisudawan.dashboard') }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
      <li><a href="{{ route('wisudawan.profile') }}"><i class="fas fa-user"></i> Profil Saya</a></li>
      <li><a href="{{ route('wisudawan.qrcode') }}"><i class="fas fa-qrcode"></i> QR Code</a></li>
      <li><a href="{{ route('wisudawan.kursi') }}"><i class="fas fa-chair"></i> Info Kursi</a></li>
      <li><a href="{{ route('wisudawan.foto') }}" class="active"><i class="fas fa-camera"></i> Foto Wisuda</a></li>
    </ul>
    <div class="sidebar-logo-container">
      <div>
        <img src="{{ asset('images/bg.png') }}" alt="UNSIQ Logo">
        <p class="university-name">Universitas Sains Al-Qur'an</p>
      </div>
    </div>
  </aside>

  <main class="main-content">
    <section class="dashboard-section"> {{-- Gunakan class dashboard-section agar padding konsisten --}}
      
      <h2 style="margin-bottom: 20px;">Foto Wisuda</h2>

      <div class="info-banner">
        <div class="icon"><i class="fas fa-check"></i></div>
        <div class="content">
          <h3><strong>Foto Sudah Tersedia</strong></h3>
          <p>Diunggah pada: {{ date('d F Y') }} oleh: Tim Fotografer Wisuda</p>
        </div>
      </div>

      <div class="section-title">Dokumentasi Wisuda Hari 1</div>
      <div class="drive-cards">
        @forelse($fotos->where('hari', 1) as $foto)
            <div class="drive-card">
                <div class="meta">
                    <div class="icon-box blue">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div>
                        <div style="font-weight:600; font-size:1.05rem;">
                            {{ $foto->judul ?? 'Drive Hari 1' }}
                        </div>
                        <div class="subtitle" style="margin-top:2px; color:#6b7280;">{{ $foto->deskripsi ?? 'Dokumentasi Wisuda Hari 1' }}</div>
                    </div>
                </div>

                <div class="input-group">
                    <input type="text" id="url-{{ $foto->id }}" value="{{ $foto->drive_link }}" readonly>
                    <button class="btn-copy" onclick="copyUrl('url-{{ $foto->id }}')">Copy</button>
                </div>

                <a href="{{ $foto->drive_link }}" target="_blank" class="btn-open blue">
                    Buka Folder <i class="fas fa-external-link-alt" style="margin-left:5px;"></i>
                </a>
            </div>
        @empty
            <div class="drive-card" style="align-items:center; text-align:center; flex-basis:100%; padding: 20px;">
                <p style="color:#6b7280;">Belum ada foto untuk Hari 1.</p>
            </div>
        @endforelse
      </div>

      <div class="section-title" style="margin-top: 40px;">Dokumentasi Wisuda Hari 2</div>
      <div class="drive-cards">
        @forelse($fotos->where('hari', 2) as $foto)
            <div class="drive-card">
                <div class="meta">
                    <div class="icon-box green">
                        <i class="fas fa-images"></i>
                    </div>
                    <div>
                        <div style="font-weight:600; font-size:1.05rem;">
                            {{ $foto->judul ?? 'Drive Hari 2' }}
                        </div>
                        <div class="subtitle" style="margin-top:2px; color:#6b7280;">{{ $foto->deskripsi ?? 'Dokumentasi Wisuda Hari 2' }}</div>
                    </div>
                </div>

                <div class="input-group">
                    <input type="text" id="url-{{ $foto->id }}" value="{{ $foto->drive_link }}" readonly>
                    <button class="btn-copy" onclick="copyUrl('url-{{ $foto->id }}')">Copy</button>
                </div>

                <a href="{{ $foto->drive_link }}" target="_blank" class="btn-open green">
                    Buka Folder <i class="fas fa-external-link-alt" style="margin-left:5px;"></i>
                </a>
            </div>
        @empty
            <div class="drive-card" style="align-items:center; text-align:center; flex-basis:100%; padding: 20px;">
                <p style="color:#6b7280;">Belum ada foto untuk Hari 2.</p>
            </div>
        @endforelse
      </div>

      <div class="important-card">
        <h3 style="margin:0 0 15px 0; color:#1f2937;">Informasi Penting</h3>
        <ul>
          <li><i class="fas fa-check-circle"></i><span>Foto dapat diakses secara gratis tanpa batas waktu.</span></li>
          <li><i class="fas fa-check-circle"></i><span>Wisudawan bebas mendownload dan membagikan foto kepada keluarga.</span></li>
          <li><i class="fas fa-check-circle"></i><span>File tersedia dalam kualitas tinggi dan resolusi penuh.</span></li>
          <li><i class="fas fa-check-circle"></i><span>Link Google Drive akan tetap aktif minimal 1 tahun.</span></li>
        </ul>
      </div>

    </section>
  </main>
</div>

<div id="toast" class="toast-msg">URL berhasil disalin!</div>

@endsection

@push('scripts')
<script>
    function copyUrl(elementId) {
        var input = document.getElementById(elementId);
        if(!input) return;

        input.select();
        input.setSelectionRange(0, 99999); 

        try {
            if(navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(input.value).then(() => {
                    showToast('URL disalin ke clipboard');
                });
            } else {
                document.execCommand('copy');
                showToast('URL disalin ke clipboard');
            }
        } catch(err) {
            showToast('Gagal menyalin URL');
        }
    }

    function showToast(message) {
        var toast = document.getElementById('toast');
        toast.innerText = message;
        toast.classList.add('show');
        
        setTimeout(function() {
            toast.classList.remove('show');
        }, 3000);
    }
</script>
@endpush