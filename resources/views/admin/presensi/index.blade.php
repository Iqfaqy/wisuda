@extends('layouts.app')

@section('title', 'Presensi - Sistem Wisuda')
@section('bodyClass', 'feature-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/feature-pages.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stats-unified.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/presensi.css') }}">
<style>
    /* --- CSS SCANNER (TANPA GARIS PUTUS-PUTUS) --- */
    
    .scanner-preview {
        position: relative;
        width: 100%;
        min-height: 350px;
        background-color: #000;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.1);
    }

    #reader {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
        background: #000;
    }
    
    #reader video {
        object-fit: cover;
        width: 100% !important;
        height: 100% !important;
        border-radius: 12px;
    }

    #cameraPlaceholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        
        background-color: #f8fafc;
        color: #64748b;
        border: none; /* Garis putus-putus dihilangkan disini */
        transition: all 0.3s ease;
    }

    #cameraPlaceholder i {
        font-size: 3.5rem !important;
        margin-bottom: 15px !important;
        color: #94a3b8 !important;
    }
    
    #cameraPlaceholder p {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    .scan-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
    }
</style>
@endpush

@section('content')
<header>
  <div class="logo"><span>🎓</span><span>Sistem Wisuda</span></div>
  <div class="user-info">
    <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
    <div>
      <div class="user-name">{{ auth()->user()->name }}</div>
      <small>Administrator</small>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf
      <button type="submit" class="btn btn-sm btn-danger">Keluar</button>
    </form>
  </div>
</header>

<div class="page-container">
  @include('layouts.admin-sidebar')

  <main class="main-content">
    <div class="page-header">
      <h1>Presensi Wisudawan</h1>
      <p>Scan QR Code untuk mencatat kehadiran</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-content">
          <div class="stat-number" id="stat-total-wisudawan">{{ $stats['total_wisudawan'] ?? 0 }}</div>
          <div class="stat-label">Total Wisudawan</div>
        </div>
      </div>
      <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-content">
          <div class="stat-number" id="stat-hadir">{{ $stats['hadir'] ?? 0 }}</div>
          <div class="stat-label">Sudah Hadir</div>
        </div>
      </div>
      <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-content">
          <div class="stat-number" id="stat-belum-hadir">{{ $stats['belum_hadir'] ?? 0 }}</div>
          <div class="stat-label">Belum Hadir</div>
        </div>
      </div>
      <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-percent"></i></div>
        <div class="stat-content">
          <div class="stat-number" id="stat-persentase">{{ $stats['persentase'] ?? 0 }}%</div>
          <div class="stat-label">Persentase</div>
        </div>
      </div>
    </div>

    <div class="scanner-card">
      <div class="scanner-header">
        <h2>Scanner QR Code</h2>
        <p>Gunakan kamera atau input manual</p>
      </div>
      
      <div class="scanner-container">
        <div class="scanner-preview" id="scannerPreview">
            <div id="reader"></div>
            
            <div id="cameraPlaceholder" class="scanner-placeholder">
                <i class="fas fa-camera" style="font-size: 3rem; margin-bottom: 10px; color:#ccc;"></i>
                <p>Kamera Non-aktif</p>
            </div>
        </div>

        <div class="scan-buttons">
            <button class="btn btn-success" id="btnStartScan" onclick="startCamera()">
                <i class="fas fa-video"></i> Buka Kamera
            </button>
            <button class="btn btn-danger" id="btnStopScan" onclick="stopCamera()" style="display:none;">
                <i class="fas fa-video-slash"></i> Tutup Kamera
            </button>
        </div>

        <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
          <p style="margin-bottom:5px; font-size:0.9rem; color:#666;">Scan bermasalah? Input manual:</p>
          <div style="display:flex; gap:10px; justify-content:center;">
              <input type="text" id="manualScan" placeholder="Masukkan NIM..." class="form-control" style="width: 250px;">
              <button class="btn btn-primary" onclick="manualScanSubmit()">Cek</button>
          </div>
        </div>
      </div>

      <div class="scan-result" id="scanResult" style="display: none;">
        <div class="result-content">
          <div class="result-icon">✓</div>
          <h3 id="resultName">Nama Wisudawan</h3>
          <p id="resultNIM">NIM: -</p>
          <span class="result-status">Berhasil Terdaftar</span>
        </div>
      </div>
    </div>

    <div class="attendance-section">
      <div class="section-header">
        <h2>Daftar Kehadiran Terbaru</h2>
        <div style="display:flex; gap:8px; align-items:center;">
          <a href="{{ route('admin.presensi.export') }}" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export
          </a>
          <button class="btn btn-danger" onclick="clearAttendance()">
            <i class="fas fa-trash-alt"></i> Reset
          </button>
        </div>
      </div>
      <div class="attendance-list" id="attendanceList">
        @if(isset($presensi) && $presensi->count() > 0)
        <table class="attendance-table">
          <thead>
            <tr><th>No</th><th>Nama</th><th>NIM</th><th>Kursi</th><th>Waktu</th></tr>
          </thead>
          <tbody>
            @foreach($presensi as $index => $p)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $p->wisudawan->user->name ?? '-' }}</td>
              <td>{{ $p->wisudawan->user->nim ?? '-' }}</td>
              <td>{{ $p->wisudawan->kursi->kode_kursi ?? 'Belum ada' }}</td>
              <td>{{ $p->waktu_scan->format('H:i:s') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div class="empty-state">
          <i class="fas fa-inbox"></i>
          <p>Belum ada data kehadiran hari ini</p>
        </div>
        @endif
      </div>
    </div>
  </main>
</div>

<audio id="scanSound" src="https://assets.mixkit.co/active_storage/sfx/2578/2578-preview.mp3"></audio>

@endsection

@push('scripts')
{{-- Gunakan versi stabil dari cdnjs --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- VARIABEL SCANNER ---
    let html5QrcodeScanner = null;
    let isScanning = false;
    const readerId = "reader";

    // 1. FUNGSI MULAI KAMERA
    function startCamera() {
        // Cek apakah library sudah terload
        if (typeof Html5Qrcode === "undefined") {
            Swal.fire('Error', 'Library Scanner belum siap. Silakan refresh halaman.', 'error');
            return;
        }

        const placeholder = document.getElementById('cameraPlaceholder');
        const btnStart = document.getElementById('btnStartScan');
        const btnStop = document.getElementById('btnStopScan');

        // UI Update
        placeholder.style.display = 'none';
        btnStart.style.display = 'none';
        btnStop.style.display = 'inline-block';

        // Bersihkan instance lama jika ada (defensive)
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(err => console.warn("Clear error ignored:", err));
        }

        // Inisialisasi Scanner Baru
        html5QrcodeScanner = new Html5Qrcode(readerId);

        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0 
        };
        
        // Mulai scanning (menggunakan kamera belakang 'environment' jika ada)
        html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
        .then(() => {
            isScanning = true;
            console.log("Kamera aktif");
        })
        .catch(err => {
            console.error("Gagal membuka kamera:", err);
            let msg = 'Gagal membuka kamera.';
            if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                msg += ' Pastikan menggunakan HTTPS.';
            } else {
                msg += ' Pastikan izin kamera diberikan.';
            }
            Swal.fire('Error', msg, 'error');
            stopCamera();
        });
    }

    // 2. FUNGSI STOP KAMERA
    function stopCamera() {
        if (html5QrcodeScanner && isScanning) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner.clear();
                isScanning = false;
                resetUI();
            }).catch(err => {
                console.error("Gagal stop:", err);
                // Force reset UI even if stop fails
                isScanning = false;
                resetUI();
            });
        } else {
            resetUI();
        }
    }

    function resetUI() {
        document.getElementById('cameraPlaceholder').style.display = 'flex';
        document.getElementById('btnStartScan').style.display = 'inline-block';
        document.getElementById('btnStopScan').style.display = 'none';
        
        // Hapus elemen video sisa jika ada (clean up DOM)
        const reader = document.getElementById(readerId);
        if(reader) reader.innerHTML = '';
        
        html5QrcodeScanner = null;
    }

    // 3. CALLBACK SAAT QR TERBACA
    function onScanSuccess(decodedText, decodedResult) {
        if (!isScanning) return;

        // Mainkan suara beep
        const audio = document.getElementById('scanSound');
        if(audio) audio.play().catch(e => {});

        // Pause scanning agar tidak submit berkali-kali
        if (html5QrcodeScanner) {
            html5QrcodeScanner.pause(); 
        }

        // Proses data ke server
        processScanData(decodedText);
    }

    function onScanFailure(error) {
        // Biarkan kosong agar tidak spam console saat mencari QR
    }

    // 4. LOGIKA KIRIM KE SERVER (Digunakan oleh Kamera & Manual)
    function processScanData(code) {
        // Tampilkan loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Mencatat kehadiran NIM: ' + code,
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading() },
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        fetch('{{ route('admin.presensi.scan') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ qr_code: code })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Tampilkan Hasil Sukses di Halaman
                const rName = document.getElementById('resultName');
                const rNim = document.getElementById('resultNIM');
                const rRes = document.getElementById('scanResult');
                
                if(rName) rName.textContent = data.wisudawan.name || 'Wisudawan';
                if(rNim) rNim.textContent = 'NIM: ' + (data.wisudawan.nim || code);
                if(rRes) {
                    rRes.style.display = 'block';
                    rRes.className = 'scan-result success';
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Resume kamera setelah sukses
                    if (html5QrcodeScanner && isScanning) {
                        html5QrcodeScanner.resume();
                    }
                    // Reload untuk update tabel kehadiran (bisa di-optimize dengan append row JS)
                    location.reload(); 
                });

            } else {
                throw new Error(data.message || 'Gagal mencatat kehadiran');
            }
        })
        .catch(err => {
            let errMsg = err.message;
            if (err.message === 'Failed to fetch') errMsg = 'Koneksi terputus atau server error.';
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: errMsg
            }).then(() => {
                // Resume kamera jika gagal, agar bisa scan ulang
                if (html5QrcodeScanner && isScanning) {
                    html5QrcodeScanner.resume();
                }
            });
        });
    }

    // 5. SCAN MANUAL
    function manualScanSubmit() {
        const code = document.getElementById('manualScan').value.trim();
        if (!code) {
            Swal.fire('Peringatan', 'Masukkan NIM atau Kode terlebih dahulu', 'warning');
            return;
        }
        processScanData(code);
    }

    // 6. CLEAR DATA
    function clearAttendance() {
        Swal.fire({
            title: 'Yakin kosongkan data?',
            text: "Semua data presensi hari ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            confirmButtonColor: '#ef4444'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route('admin.presensi.clear') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(r => r.json())
                .then(data => {
                    Swal.fire('Dihapus!', data.message, 'success').then(() => location.reload());
                });
            }
        });
    }
</script>
@endpush