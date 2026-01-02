@extends('layouts.app')

@section('title', 'Info Kursi - Sistem Wisuda')
@section('bodyClass', 'dashboard-wisudawan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kursi-management.css') }}">
<link rel="stylesheet" href="{{ asset('css/wisudawan/kursi.css') }}">
<style>
    /* Mengadopsi Style dari Admin agar Seragam */
    .section-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }
    .section-header-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f3f4f6;
        font-weight: 700; color: #374151;
    }
    .seat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
        gap: 8px;
    }
    .seat-item {
        aspect-ratio: 1/1;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 8px; cursor: pointer; padding: 4px; text-align: center;
        transition: transform 0.2s, box-shadow 0.2s; background-color: white;
    }
    .seat-item:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 10; }
    .seat-code { font-size: 0.8rem; font-weight: 800; margin-bottom: 2px; line-height: 1; }
    .seat-name { font-size: 0.6rem; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; }

    /* Warna */
    .seat-item.female { background: #fce7f3; border: 1px solid #fbcfe8; color: #9d174d; }
    .seat-item.male { background: #dbeafe; border: 1px solid #bfdbfe; color: #1e40af; }
    .seat-item.empty { background: #f3f4f6; border: 1px dashed #d1d5db; color: #9ca3af; }
    
    /* Highlight Kursi Sendiri */
    .seat-item.my-seat {
        background: #f59e0b !important; /* Orange/Gold */
        color: #fff !important;
        border: 2px solid #d97706 !important;
        transform: scale(1.15);
        z-index: 20;
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.6);
    }
    .my-seat .seat-name { color: #fff; font-weight: bold; }

    /* Legend */
    .legend-box {
        display: flex; gap: 15px; padding: 15px; background: white; border-radius: 12px;
        margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-wrap: wrap;
    }
    .legend-item {
        display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 500;
        padding: 5px 10px; border-radius: 20px; cursor: pointer; user-select: none; transition: all 0.2s;
    }
    .legend-item:hover { background-color: #f9fafb; }
    .legend-item.inactive { opacity: 0.4; text-decoration: line-through; }
    
    /* Header Khusus User */
    .user-seat-info {
        /* Menggunakan gradasi hijau (Emerald Green) */
        background: linear-gradient(135deg, #047857 0%, #10b981 100%);
        color: white; 
        padding: 2rem; 
        border-radius: 16px; 
        margin-bottom: 2rem;
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        /* Shadow disesuaikan dengan warna hijau */
        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.25);
    }
    .seat-big-number { font-size: 3rem; font-weight: 800; line-height: 1; }
    .seat-big-label { font-size: 0.9rem; opacity: 0.9; margin-top: 5px; }

    /* Modal */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
    .modal.active { display: flex; }
    .modal-content { background: white; width: 400px; border-radius: 12px; padding: 2rem; text-align: center; }
    .modal-avatar { width: 80px; height: 80px; background: #eee; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #555; }
    
    @media (max-width: 768px) {
        .user-seat-info { flex-direction: column; text-align: center; gap: 1rem; }
        .seat-grid { grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)); }
    }
</style>
@endpush

@section('content')
<header>
  <div class="logo">
    <span class="logo-icon">🎓</span>
    <div class="logo-text">
      <div class="logo-title">Dashboard Wisudawan</div>
      <small class="logo-sub">Sistem Wisuda UNSIQ</small>
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
      <li><a href="{{ route('wisudawan.kursi') }}" class="active"><i class="fas fa-chair"></i> Info Kursi</a></li>
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
    <section class="page-empty kursi-page">
      
      <div class="user-seat-info">
          <div>
              <h2 style="margin:0; font-size:1.5rem; color:white;">Informasi Tempat Duduk</h2>
              <p style="margin:5px 0 0; opacity:0.8;">Hari Wisuda ke-{{ $wisudawan->hari_wisuda ?? 1 }}</p>
          </div>
          <div style="text-align: right;">
              @if($myKursi)
                <div class="seat-big-number">{{ $myKursi->kode_kursi }}</div>
                <div class="seat-big-label">Zona {{ $myKursi->section }} • No. {{ $myKursi->nomor }}</div>
              @else
                <div class="seat-big-number" style="font-size:1.5rem;">Belum Ada</div>
                <div class="seat-big-label">Hubungi Panitia</div>
              @endif
          </div>
      </div>

      <div class="legend-box">
          <div class="legend-item" id="filter-myseat" onclick="toggleFilter('myseat')">
             <div style="width:20px; height:20px; background:#f59e0b; border:2px solid #d97706; border-radius:4px;"></div>
             <span>Kursi Saya</span>
          </div>
          <div class="legend-item" id="filter-female" onclick="toggleFilter('female')">
              <div style="width:20px; height:20px; background:#fce7f3; border:1px solid #fbcfe8; border-radius:4px;"></div>
              <span>Perempuan</span>
          </div>
          <div class="legend-item" id="filter-male" onclick="toggleFilter('male')">
              <div style="width:20px; height:20px; background:#dbeafe; border:1px solid #bfdbfe; border-radius:4px;"></div>
              <span>Laki-laki</span>
          </div>
          <div class="legend-item" id="filter-empty" onclick="toggleFilter('empty')">
              <div style="width:20px; height:20px; background:#f3f4f6; border:1px dashed #d1d5db; border-radius:4px;"></div>
              <span>Kosong</span>
          </div>
          
          <div style="margin-left:auto;">
             <input type="text" id="seatSearch" onkeyup="runFilter()" placeholder="Cari Kursi..." 
             style="padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:0.9rem;">
          </div>
      </div>

      <div id="seating-layout">
          <div class="section-card" id="card-A">
              <div class="section-header-row">
                  <span>Depan (Perempuan)</span>
                  <span class="count-badge" id="count-A">0 Terisi</span>
              </div>
              <div class="seat-grid" id="grid-A"></div>
          </div>
          
          <div class="section-card" id="card-B">
              <div class="section-header-row">
                  <span>Belakang (Laki-laki)</span>
                  <span class="count-badge" id="count-B">0 Terisi</span>
              </div>
              <div class="seat-grid" id="grid-B"></div>
          </div>
      </div>
    </section>
  </main>
</div>

<div id="seatModal" class="modal">
    <div class="modal-content">
        <div style="display:flex; justify-content:flex-end;">
            <button onclick="closeModal()" style="border:none; background:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        <div class="modal-avatar" id="modal-avatar">?</div>
        <h3 id="modal-name" style="margin-bottom:5px;">Nama Wisudawan</h3>
        <p id="modal-nim" style="color:#666; margin-bottom:20px;">NIM: -</p>
        
        <div style="background:#f9fafb; padding:15px; border-radius:8px; text-align:left;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px; border-bottom:1px solid #eee; padding-bottom:5px;">
                <span style="color:#888;">Kode Kursi</span>
                <strong id="modal-code">-</strong>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:8px; border-bottom:1px solid #eee; padding-bottom:5px;">
                <span style="color:#888;">Status</span>
                <strong id="modal-status">-</strong>
            </div>
             <div style="display:flex; justify-content:space-between;">
                <span style="color:#888;">Area</span>
                <strong id="modal-section">-</strong>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Data dari Controller
    const dbSeats = @json($seatData); 
    const mySeatCode = "{{ $myKursi->kode_kursi ?? '' }}";
    const myNim = "{{ $user->nim }}";
    const myName = "{{ $user->name }}";

    // State Filter
    let activeFilters = {
        myseat: true,
        female: true,
        male: true,
        empty: true
    };

    document.addEventListener('DOMContentLoaded', function() {
        renderSeats();
    });

    function renderSeats() {
        // Konfigurasi Section
        const sections = ['A', 'B', 'C', 'D'];

        sections.forEach(sec => {
            const grid = document.getElementById(`grid-${sec}`);
            const card = document.getElementById(`card-${sec}`);
            const countEl = document.getElementById(`count-${sec}`);
            
            if(!grid) return;

            // Filter data sesuai section ini & urutkan
            const sectionSeats = dbSeats.filter(s => s.section === sec)
                                        .sort((a,b) => parseInt(a.nomor) - parseInt(b.nomor));
            
            // Hitung Terisi
            const total = sectionSeats.length;
            const filled = sectionSeats.filter(s => s.is_occupied).length;
            
            if (countEl) countEl.innerText = `${filled}/${total} Terisi`;

            // Sembunyikan section card jika kosong total (biar rapi)
            if (total === 0) {
                if (card) card.style.display = 'none';
                return;
            } else {
                if (card) card.style.display = 'block';
            }

            // Render Kotak-kotak
            grid.innerHTML = ''; // Reset
            
            sectionSeats.forEach(seat => {
                const el = document.createElement('div');
                let css = 'empty';
                let filterType = 'empty';
                let displayName = '';
                
                // Logic Penentuan Tampilan
                if (seat.kode === mySeatCode) {
                    css = 'my-seat';
                    filterType = 'myseat';
                    displayName = 'ANDA';
                } else if (seat.is_occupied) {
                    css = (seat.gender === 'P' ? 'female' : 'male');
                    filterType = (seat.gender === 'P' ? 'female' : 'male');
                    displayName = 'Terisi'; // Kita tidak menampilkan nama orang lain demi privasi sederhana, atau bisa diubah
                } else {
                    css = 'empty';
                    filterType = 'empty';
                    displayName = '';
                }

                el.className = `seat-item ${css}`;
                el.setAttribute('data-filter', filterType);
                el.setAttribute('data-code', seat.kode); // Untuk search
                
                // Data untuk Modal
                el.onclick = () => showDetail(seat, css);

                // Konten Dalam Kotak
                // Format Kode pendek: misal 1-A-01 jadi A-01
                const shortCode = seat.kode.split('-').slice(1).join('-') || seat.kode;
                
                el.innerHTML = `
                    <div class="seat-code">${shortCode}</div>
                    <div class="seat-name">${displayName}</div>
                `;
                
                grid.appendChild(el);
            });
        });
    }

    // --- FILTER FUNCTIONALITY ---
    function toggleFilter(type) {
        activeFilters[type] = !activeFilters[type];
        
        // Update UI Button
        const btn = document.getElementById(`filter-${type}`);
        if (activeFilters[type]) btn.classList.remove('inactive');
        else btn.classList.add('inactive');

        runFilter();
    }

    function runFilter() {
        const searchInput = document.getElementById('seatSearch').value.toLowerCase();
        const allItems = document.querySelectorAll('.seat-item');

        allItems.forEach(item => {
            const type = item.getAttribute('data-filter');
            const code = item.getAttribute('data-code').toLowerCase();
            
            // Logika: Tampilkan jika Filter Tipe Aktif AND Sesuai Search
            const isTypeActive = activeFilters[type];
            // Khusus myseat, dia selalu muncul jika filter myseat aktif (override female/male logic)
            
            const isSearchMatch = code.includes(searchInput);

            if (isTypeActive && isSearchMatch) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // --- MODAL DETAIL ---
    function showDetail(seat, cssClass) {
        const modal = document.getElementById('seatModal');
        const avatar = document.getElementById('modal-avatar');
        
        document.getElementById('modal-code').innerText = seat.kode;
        document.getElementById('modal-section').innerText = `Section ${seat.section}`;
        
        // Cek Status
        if (seat.kode === mySeatCode) {
            // Data Diri Sendiri
            document.getElementById('modal-name').innerText = myName + " (Anda)";
            document.getElementById('modal-nim').innerText = myNim;
            document.getElementById('modal-status').innerText = "Kursi Milik Anda";
            document.getElementById('modal-status').style.color = "#d97706";
            avatar.innerText = myName.charAt(0);
            avatar.style.background = "#fcd34d";
            avatar.style.color = "#78350f";
        } else if (seat.is_occupied) {
            // Orang Lain (Privasi: Hanya tampilkan status terisi)
            document.getElementById('modal-name').innerText = "Kursi Terisi";
            document.getElementById('modal-nim').innerText = "Wisudawan Lain";
            document.getElementById('modal-status').innerText = "Tidak Tersedia";
            document.getElementById('modal-status').style.color = "#dc2626";
            avatar.innerText = "👤";
            avatar.style.background = "#eee";
            avatar.style.color = "#555";
        } else {
            // Kosong
            document.getElementById('modal-name').innerText = "Kursi Kosong";
            document.getElementById('modal-nim').innerText = "-";
            document.getElementById('modal-status').innerText = "Tersedia";
            document.getElementById('modal-status').style.color = "#16a34a";
            avatar.innerText = "🪑";
            avatar.style.background = "#dcfce7";
            avatar.style.color = "#166534";
        }

        modal.classList.add('active');
    }

    function closeModal() {
        document.getElementById('seatModal').classList.remove('active');
    }

    // Close modal on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('seatModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endpush