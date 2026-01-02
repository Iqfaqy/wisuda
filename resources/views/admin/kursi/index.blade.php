@extends('layouts.app')

@section('title', 'Manajemen Kursi - Sistem Wisuda')
@section('bodyClass', 'feature-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/feature-pages.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stats-unified.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/kursi-management.css') }}">

<style>
  /* --- 1. CSS VISUAL KURSI --- */
  .section-card {
    background: #fff; border-radius: 12px; padding: 20px;
    margin-bottom: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;
  }
  .section-header-row {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f3f4f6;
    font-weight: 700; color: #374151;
  }
  .seat-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(70px, 1fr)); gap: 10px;
  }
  .seat-item {
    aspect-ratio: 1/1; display: flex; flex-direction: column; justify-content: center; align-items: center;
    border-radius: 8px; cursor: pointer; padding: 4px; text-align: center;
    transition: transform 0.2s, box-shadow 0.2s; background-color: white;
  }
  .seat-item:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 10; }
  .seat-code { font-size: 0.9rem; font-weight: 800; margin-bottom: 4px; line-height: 1; }
  .seat-name { font-size: 0.65rem; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; }

  /* Warna Kursi */
  .seat-item.female { background: #fce7f3; border: 1px solid #fbcfe8; color: #9d174d; }
  .seat-item.female .seat-name { color: #be185d; }
  .seat-item.male { background: #dbeafe; border: 1px solid #bfdbfe; color: #1e40af; }
  .seat-item.male .seat-name { color: #1d4ed8; }
  .seat-item.empty { background: #f3f4f6; border: 1px dashed #d1d5db; color: #9ca3af; }
  
  /* Legend & Tabs */
  .legend-box { display: flex; gap: 20px; padding: 15px; background: white; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
  
  /* Update Legend Style dengan Checkbox */
  .legend-item { display: flex; align-items: center; gap: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer; padding: 6px 12px; border-radius: 8px; transition: all 0.2s; user-select: none; border: 1px solid transparent; }
  .legend-item:hover { background-color: #f8fafc; }
  
  .legend-marker { width: 22px; height: 22px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
  .legend-marker.female { background: #fce7f3; border: 1px solid #fbcfe8; color: #9d174d; }
  .legend-marker.male { background: #dbeafe; border: 1px solid #bfdbfe; color: #1e40af; }
  .legend-marker.empty { background: #f3f4f6; border: 1px dashed #d1d5db; color: #6b7280; }

  /* Inactive State */
  .legend-item.inactive .legend-marker { background: #fff !important; border: 2px solid #cbd5e1 !important; color: transparent !important; box-shadow: none; }
  .legend-item.inactive span { color: #94a3b8; }

  .view-tabs { display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid #e5e7eb; }
  .tab-btn { padding: 1rem 1.5rem; border: none; background: none; font-weight: 600; color: #6b7280; cursor: pointer; border-bottom: 3px solid transparent; }
  .tab-btn.active { color: #16a34a; border-bottom-color: #16a34a; }
  .view-panel { display: none; }
  .view-panel.active { display: block; }

  /* Accordion */
  .accordion-header { background: #fff; padding: 15px 20px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; cursor: pointer; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; transition: all 0.2s; }
  .accordion-header:hover { background: #f8fafc; border-color: #cbd5e1; }
  .accordion-header h2 { margin: 0; font-size: 1.2rem; font-weight: 700; color: #334155; }
  .accordion-header.active { background: #eff6ff; border-color: #bfdbfe; }
  .accordion-content { display: none; padding: 10px 0; animation: fadeIn 0.3s ease; }
  .accordion-content.active { display: block; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

  /* --- PERBAIKAN WARNA TABEL (DATA LIST) --- */
  .table-responsive table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
  }
  .table-responsive th {
      background-color: #f3f4f6;
      color: #111827 !important; /* HITAM */
      font-weight: 700;
      padding: 15px;
      text-align: left;
      border-bottom: 2px solid #e5e7eb;
  }
  .table-responsive td {
      color: #374151 !important; /* ABU GELAP */
      padding: 15px;
      border-bottom: 1px solid #f3f4f6;
      vertical-align: middle;
  }
  .table-responsive tr:hover td {
      background-color: #f9fafb;
  }

  /* --- 2. MODAL MODERN STYLING --- */
  .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px); z-index: 1050; justify-content: center; align-items: center; opacity: 0; transition: opacity 0.3s ease; }
  .modal-overlay.show { opacity: 1; }
  .modal-container { background: white; width: 90%; max-width: 550px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); transform: translateY(20px) scale(0.95); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; }
  .modal-overlay.show .modal-container { transform: translateY(0) scale(1); }
  .modal-header { padding: 20px 25px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #fff; }
  .modal-title { font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0; }
  .modal-close { background: none; border: none; font-size: 1.5rem; color: #9ca3af; cursor: pointer; transition: color 0.2s; line-height: 1; }
  .modal-close:hover { color: #ef4444; }
  .modal-body { padding: 25px; overflow-y: auto; }
  .modal-tab-container { display: flex; gap: 8px; padding: 6px; background: #f1f5f9; border-radius: 10px; margin-bottom: 20px; }
  .modal-tab-btn { flex: 1; text-align: center; padding: 10px; cursor: pointer; border-radius: 8px; font-weight: 600; color: #64748b; transition: all 0.2s; font-size: 0.95rem; }
  .modal-tab-btn.active { background: white; color: #0d6efd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
  .modal-tab-btn:hover:not(.active) { background: #e2e8f0; }
  .form-group { margin-bottom: 1.25rem; }
  .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 0.9rem; }
  .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem; color: #1f2937; transition: all 0.2s; background: #f9fafb; }
  .form-control:focus { border-color: #0d6efd; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
  .modal-footer { padding: 20px 25px; border-top: 1px solid #f0f0f0; background: #f9fafb; text-align: right; display: flex; justify-content: flex-end; gap: 12px; }
  .btn-cancel { background: #fff; color: #4b5563; border: 1px solid #d1d5db; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
  .btn-cancel:hover { background: #f3f4f6; }
  .btn-save { background: #0d6efd; color: white; border: none; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3); transition: all 0.2s; }
  .btn-save:hover { background: #0b5ed7; transform: translateY(-1px); }
  .info-box-occupied { background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 15px; text-align: center; color: #065f46; }
  .info-box-empty { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 10px; padding: 15px; color: #92400e; display: flex; align-items: center; gap: 10px; }
</style>
@endpush

@section('content')
<header>
  <div class="logo"><span>🎓</span><span>Sistem Wisuda</span></div>
  <div class="user-info">
    <div class="user-avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
    <div>
      <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
      <small>Administrator</small>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf
      <button type="submit" class="btn btn-sm btn-danger" style="margin-left:10px;">Keluar</button>
    </form>
  </div>
</header>

<div class="page-container">
  @include('layouts.admin-sidebar')

  <main class="main-content">
    <div class="page-header">
      <h1>Manajemen Kursi</h1>
      <p>Atur penempatan tempat duduk wisudawan</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-chair"></i></div>
        <div class="stat-content">
          <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
          <div class="stat-label">Total Kursi Tersedia</div>
        </div>
      </div>
      <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-content">
          <div class="stat-number">{{ $stats['terisi'] ?? 0 }}</div>
          <div class="stat-label">Kursi Terisi</div>
        </div>
      </div>
      <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-chair"></i></div>
        <div class="stat-content">
          <div class="stat-number">{{ $stats['kosong'] ?? 0 }}</div>
          <div class="stat-label">Kursi Kosong</div>
        </div>
      </div>
    </div>

    <div class="view-tabs">
      <button class="tab-btn active" onclick="switchTab('visual')"><i class="fas fa-map"></i> Visual Layout</button>
      <button class="tab-btn" onclick="switchTab('list')"><i class="fas fa-list"></i> Data List</button>
    </div>

    <div id="visualView" class="view-panel active">
        <div class="legend-box">
            <div class="legend-item" id="filter-female" onclick="toggleFilter('female')">
                <div class="legend-marker female"><i class="fas fa-check"></i></div>
                <span>Perempuan</span>
            </div>
            <div class="legend-item" id="filter-male" onclick="toggleFilter('male')">
                <div class="legend-marker male"><i class="fas fa-check"></i></div>
                <span>Laki-laki</span>
            </div>
            <div class="legend-item" id="filter-empty" onclick="toggleFilter('empty')">
                <div class="legend-marker empty"><i class="fas fa-check"></i></div>
                <span>Kosong</span>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <input type="text" id="seatSearch" onkeyup="runFilter()" placeholder="Cari Kode Kursi, Nama, atau NIM..." 
                  class="form-control" style="width: 300px; background:white;">
        </div>

        <div class="accordion-item" style="margin-bottom: 20px;">
            <div class="accordion-header active" onclick="toggleAccordion('hari1')" id="header-hari1">
                <h2>Hari 1</h2>
                <i class="fas fa-chevron-down icon-toggle"></i>
            </div>
            <div id="seating-hari1" class="accordion-content active">Loading...</div>
        </div>

        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion('hari2')" id="header-hari2">
                <h2>Hari 2</h2>
                <i class="fas fa-chevron-down icon-toggle"></i>
            </div>
            <div id="seating-hari2" class="accordion-content">Loading...</div>
        </div>
    </div>

    <div id="listView" class="view-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin:0;">Daftar Kursi</h3>
            <div style="display:flex; gap:10px;">
                <input type="text" id="tableSearchInput" onkeyup="filterTable()" placeholder="Cari..." class="form-control" style="width: 250px;">
                <button class="btn-save" onclick="openModal('create')" style="padding: 10px 20px;">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>
        <div class="table-responsive" style="background:white; border-radius:12px; box-shadow:0 2px 5px rgba(0,0,0,0.05); overflow:hidden;">
            <table class="table" style="width:100%; margin-bottom:0;">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Section</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="kursiTableBody"></tbody>
            </table>
        </div>
    </div>
  </main>
</div>

<div id="kursiModal" class="modal-overlay">
  <div class="modal-container">
    
    <div class="modal-header">
        <h3 class="modal-title" id="modalTitle">Detail Kursi</h3>
        <button onclick="closeModal()" class="modal-close">&times;</button>
    </div>
    
    <div class="modal-body">
        <div class="modal-tab-container">
            <div id="tabDataBtn" class="modal-tab-btn active" onclick="toggleModalTab('data')">
                <i class="fas fa-couch"></i> Data Kursi
            </div>
            <div id="tabAssignBtn" class="modal-tab-btn" onclick="toggleModalTab('assign')">
                <i class="fas fa-user-graduate"></i> Wisudawan
            </div>
        </div>

        <form id="kursiForm" style="display:block;">
            @csrf
            <input type="hidden" id="kursiId">
            
            <div class="form-group">
                <label class="form-label">Kode Kursi</label>
                <input type="text" name="kode_kursi" id="kodeKursi" class="form-control" required placeholder="Contoh: A-01">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label class="form-label">Section / Area</label>
                    <select name="section" id="section" class="form-control">
                        <option value="A">Depan (Perempuan)</option>
                        <option value="B">Belakang (Laki-laki)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Urut</label>
                    <input type="number" name="nomor" id="nomor" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label class="form-label">Hari Wisuda</label>
                    <select name="hari" id="hari" class="form-control">
                        <option value="1">Hari 1</option>
                        <option value="2">Hari 2</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Gender Kursi</label>
                    <select name="jenis_kelamin" id="jenisKelamin" class="form-control">
                        <option value="P">Perempuan (P)</option>
                        <option value="L">Laki-laki (L)</option>
                    </select>
                </div>
            </div>
        </form>

        <div id="assignSection" style="display:none;">
            
            <div id="seatOccupiedInfo" class="info-box-occupied" style="display:none;">
                <div style="font-size:2rem; margin-bottom:5px;">👤</div>
                <h4 style="margin:5px 0;">Kursi Terisi</h4>
                <div style="font-size:1.1rem; font-weight:bold;" id="occupantName">-</div>
                <div style="font-size:0.9rem; opacity:0.8;" id="occupantNIM">-</div>
                <button onclick="unassignSeat()" class="btn-cancel" style="margin-top:15px; border-color:#ef4444; color:#ef4444;">
                    <i class="fas fa-times"></i> Kosongkan
                </button>
            </div>

            <div id="seatEmptyInfo" style="display:none;">
                <div class="info-box-empty">
                    <i class="fas fa-info-circle" style="font-size:1.5rem;"></i>
                    <div>Kursi ini saat ini <b>KOSONG</b>. Silakan pilih wisudawan untuk ditempatkan di sini.</div>
                </div>
                
                <div class="form-group" style="margin-top:20px;">
                    <label class="form-label">Pilih Wisudawan (<span id="genderLabel"></span>)</label>
                    
                    <input type="text" id="searchWisudawanInput" placeholder="Ketik Nama atau NIM untuk mencari..." 
                        class="form-control" style="margin-bottom: 10px; border-color: #10b981;" onkeyup="filterWisudawanList()">
                    
                    <select id="selectWisudawan" class="form-control" style="height:45px;">
                        <option value="">-- Memuat Data... --</option>
                    </select>
                    <small class="text-muted" style="color:#666; font-size: 0.85rem;">* Menampilkan hasil pencarian di atas.</small>
                </div>
                
                <button onclick="assignSeat()" class="btn-save" style="width:100%; margin-top:10px;">
                    <i class="fas fa-check"></i> Tetapkan Wisudawan
                </button>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Tutup</button>
        <button type="button" class="btn-save" id="btnSaveMain" onclick="submitMainForm()">Simpan</button>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// STATE & INIT
let allKursi = [];
let cachedWisudawanList = [];
document.addEventListener('DOMContentLoaded', () => { loadData(); });

// ACCORDION
function toggleAccordion(id) {
    const content = document.getElementById(`seating-${id}`);
    const header = document.getElementById(`header-${id}`);
    if (content.classList.contains('active')) {
        content.classList.remove('active'); header.classList.remove('active');
    } else {
        content.classList.add('active'); header.classList.add('active');
    }
}

// LOAD DATA
function loadData() {
    fetch("{{ route('admin.kursi.index') }}", { 
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } 
    })
    .then(r => r.json())
    .then(data => {
        const grouped = data.kursi;
        renderVisual(grouped);
        
        // --- MULAI PERUBAHAN DI SINI ---
        allKursi = [];
        Object.keys(grouped).forEach(hari => {
            Object.keys(grouped[hari]).forEach(sec => {
                // HANYA MASUKKAN DATA JIKA SECTION ADALAH 'A' ATAU 'B'
                if (sec === 'A' || sec === 'B') {
                    grouped[hari][sec].forEach(k => allKursi.push(k));
                }
            });
        });
        // --- SELESAI PERUBAHAN ---

        renderTable(allKursi);
    })
    .catch(console.error);
}

// RENDER VISUAL (FILTER & SEARCH FIXED)
function renderVisual(grouped) {
    const namaSection = { 'A': 'Depan (Perempuan)', 'B': 'Belakang (Laki-laki)' };

    ['1', '2'].forEach(hari => {
        const container = document.getElementById(`seating-hari${hari}`);
        container.innerHTML = ''; 
        if (!grouped[hari]) { container.innerHTML = '<p style="color:#888; padding:10px;">Belum ada data.</p>'; return; }
        
        ['A', 'B'].forEach(sec => {
            const seats = grouped[hari][sec] || [];
            const judulSection = namaSection[sec] || `Section ${sec}`;
            const filled = seats.filter(s => s.wisudawan_id).length;

            const card = document.createElement('div'); card.className = 'section-card';
            card.innerHTML = `
                <div class="section-header-row">
                    <span>${judulSection}</span>
                    <span style="font-size:0.8rem; background:#f1f5f9; padding:2px 10px; border-radius:12px;">${filled}/${seats.length}</span>
                </div>
                <div class="seat-grid"></div>
            `;
            
            const grid = card.querySelector('.seat-grid');
            if(seats.length === 0) grid.innerHTML = '<small style="color:#ccc;">Belum ada kursi.</small>';

            seats.sort((a,b) => parseInt(a.nomor) - parseInt(b.nomor)).forEach(seat => {
                const el = document.createElement('div');
                let css = 'empty'; let name = '-';
                let filterType = 'empty';
                let searchText = seat.kode_kursi.toLowerCase();
                
                if (seat.wisudawan_id) {
                    if (seat.jenis_kelamin === 'P') { css = 'female'; filterType = 'female'; }
                    else { css = 'male'; filterType = 'male'; }

                    if(seat.wisudawan && seat.wisudawan.user) {
                        const fname = seat.wisudawan.user.name;
                        name = fname.split(' ')[0];
                        searchText += ' ' + fname.toLowerCase() + ' ' + seat.wisudawan.user.nim.toLowerCase();
                    }
                }
                
                el.className = `seat-item ${css}`;
                el.onclick = () => editKursi(seat.id);
                el.setAttribute('data-filter', filterType);
                el.setAttribute('data-search', searchText);
                el.innerHTML = `<div class="seat-code">${seat.kode_kursi}</div><div class="seat-name">${name}</div>`;
                grid.appendChild(el);
            });
            container.appendChild(card);
        });
    });
}

// FILTER & SEARCH LOGIC
let activeFilters = { female: true, male: true, empty: true };

function toggleFilter(type) {
    activeFilters[type] = !activeFilters[type];
    const btn = document.getElementById(`filter-${type}`);
    if (activeFilters[type]) btn.classList.remove('inactive');
    else btn.classList.add('inactive');
    runFilter();
}

function runFilter() {
    const searchInput = document.getElementById('seatSearch').value.toLowerCase();
    const allSeats = document.querySelectorAll('.seat-item');

    allSeats.forEach(seat => {
        const type = seat.getAttribute('data-filter');
        const text = seat.getAttribute('data-search');
        
        const isTypeActive = activeFilters[type];
        const isSearchMatch = text && text.includes(searchInput);

        if (isTypeActive && isSearchMatch) {
            seat.style.display = 'flex';
        } else {
            seat.style.display = 'none';
        }
    });
}

// GANTI FUNCTION renderTable DENGAN KODE INI
function renderTable(data) {
    // 1. Ambil container
    const container = document.querySelector('#listView .table-responsive');
    
    // Hilangkan background wrapper agar accordion terlihat terpisah
    container.style.background = 'transparent'; 
    container.style.boxShadow = 'none';
    
    const namaSection = { 'A': 'Depan (Perempuan)', 'B': 'Belakang (Laki-laki)' };

    // 2. Filter HANYA Section A dan B
    let cleanData = data.filter(k => k.section === 'A' || k.section === 'B');

    // 3. Helper: Buat HTML Tabel
    const createTableHtml = (listData) => {
        if (listData.length === 0) return '<div style="padding:15px; text-align:center; color:#64748b;">Tidak ada data ditemukan.</div>';
        
        let rows = '';
        listData.forEach(k => {
            let statusHtml = k.wisudawan_id 
                ? `<span style="color:#059669; font-weight:bold; background:#d1fae5; padding:2px 8px; border-radius:4px;">${k.wisudawan.user.name}</span>` 
                : `<span style="color:#6b7280; background:#f3f4f6; padding:2px 8px; border-radius:4px;">Kosong</span>`;
            
            const displaySection = namaSection[k.section] || k.section;
            
            rows += `
                <tr>
                    <td style="padding:15px; font-weight:bold; color:#111;">${k.kode_kursi}</td>
                    <td style="padding:15px; color:#374151;">${displaySection}</td>
                    <td style="padding:15px; color:#374151;">${k.jenis_kelamin}</td>
                    <td style="padding:15px;">${statusHtml}</td>
                    <td style="padding:15px; text-align:right;">
                        <button class="btn-save" onclick="editKursi(${k.id})" style="padding:6px 12px; font-size:0.8rem;">Edit</button>
                    </td>
                </tr>
            `;
        });

        return `
            <table class="table" style="width:100%; margin-bottom:0;">
                <thead>
                    <tr>
                        <th style="width:15%;">Kode</th>
                        <th style="width:25%;">Section</th>
                        <th style="width:15%;">Gender</th>
                        <th style="width:30%;">Status</th>
                        <th style="text-align:right; width:15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        `;
    };

    // 4. Helper: Buat Accordion Item
    const createAccordionItem = (hari, label, isOpen = false) => {
        const listHari = cleanData
            .filter(k => k.hari == hari)
            .sort((a,b) => a.kode_kursi.localeCompare(b.kode_kursi, undefined, {numeric: true}));

        const count = listHari.length;
        
        // JIKA OPEN, tambahkan class 'active'. JANGAN pakai style="display:..."
        const activeClass = isOpen ? 'active' : '';
        
        // ID dibuat unik (list-hari1) agar tidak bentrok dengan Visual Layout
        return `
            <div class="accordion-item" style="margin-bottom: 20px;">
                <div class="accordion-header ${activeClass}" onclick="toggleAccordion('list-hari${hari}')" id="header-list-hari${hari}">
                    <h2 style="font-size:1.1rem;">
                        ${label} 
                        <span style="font-size:0.8rem; font-weight:normal; background:#e2e8f0; padding:2px 10px; border-radius:12px; margin-left:10px;">
                            ${count} Kursi
                        </span>
                    </h2>
                    <i class="fas fa-chevron-down icon-toggle"></i>
                </div>
                
                <div id="seating-list-hari${hari}" class="accordion-content ${activeClass}" 
                     style="background:white; border-radius:0 0 12px 12px; border:1px solid #e5e7eb; border-top:none;">
                    ${createTableHtml(listHari)}
                </div>
            </div>
        `;
    };

    // 5. Render: Hari 1 terbuka default, Hari 2 tertutup
    container.innerHTML = `
        ${createAccordionItem('1', 'Wisuda Hari 1', true)}
        ${createAccordionItem('2', 'Wisuda Hari 2', false)}
    `;
}

// FUNGSI SEARCH TABLE
function filterTable() {
    const searchTerm = document.getElementById('tableSearchInput').value.toLowerCase();
    const filteredData = allKursi.filter(item => {
        const kode = item.kode_kursi.toLowerCase();
        let namaMhs = '';
        if (item.wisudawan && item.wisudawan.user) namaMhs = item.wisudawan.user.name.toLowerCase();
        return kode.includes(searchTerm) || namaMhs.includes(searchTerm);
    });
    renderTable(filteredData);
}

// --- MODAL & LOGIC LAINNYA ---
function openModal(mode, data=null) {
    const modal = document.getElementById('kursiModal');
    const f = document.getElementById('kursiForm');
    toggleModalTab('data');

    if(mode=='create'){
        f.reset(); 
        document.getElementById('kursiId').value='';
        document.getElementById('modalTitle').innerText = 'Tambah Kursi Baru';
        document.getElementById('tabAssignBtn').style.display = 'none';
        document.getElementById('btnSaveMain').style.display = 'block'; 
    } else {
        document.getElementById('tabAssignBtn').style.display = 'block';
        document.getElementById('modalTitle').innerText = 'Edit Kursi ' + data.kode_kursi;
        document.getElementById('kursiId').value = data.id;
        document.getElementById('kodeKursi').value = data.kode_kursi;
        document.getElementById('section').value = data.section;
        document.getElementById('nomor').value = data.nomor;
        document.getElementById('hari').value = data.hari;
        document.getElementById('jenisKelamin').value = data.jenis_kelamin;

        const occupiedDiv = document.getElementById('seatOccupiedInfo');
        const emptyDiv = document.getElementById('seatEmptyInfo');
        
        if (data.wisudawan_id && data.wisudawan) {
            occupiedDiv.style.display = 'block'; emptyDiv.style.display = 'none';
            document.getElementById('occupantName').innerText = data.wisudawan.user ? data.wisudawan.user.name : '-';
            document.getElementById('occupantNIM').innerText = data.wisudawan.user ? data.wisudawan.user.nim : '-';
        } else {
            occupiedDiv.style.display = 'none'; emptyDiv.style.display = 'block';
            document.getElementById('genderLabel').innerText = (data.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
        }
    }
    modal.style.display = 'flex';
    setTimeout(() => { modal.classList.add('show'); }, 10);
}

function closeModal() {
    const modal = document.getElementById('kursiModal');
    modal.classList.remove('show');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}

function editKursi(id){ const k=allKursi.find(x=>x.id==id); if(k) openModal('edit', k); }

function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.view-panel').forEach(p => p.classList.remove('active'));
  if (tab === 'visual') {
    document.querySelector('.tab-btn:nth-child(1)').classList.add('active');
    document.getElementById('visualView').classList.add('active');
  } else {
    document.querySelector('.tab-btn:nth-child(2)').classList.add('active');
    document.getElementById('listView').classList.add('active');
  }
}

function toggleModalTab(tab) {
    const form = document.getElementById('kursiForm');
    const assign = document.getElementById('assignSection');
    const btnData = document.getElementById('tabDataBtn');
    const btnAssign = document.getElementById('tabAssignBtn');
    const btnSaveMain = document.getElementById('btnSaveMain');

    if (tab === 'data') {
        form.style.display = 'block'; assign.style.display = 'none';
        btnData.classList.add('active'); btnAssign.classList.remove('active');
        btnSaveMain.style.display = 'block'; 
    } else {
        form.style.display = 'none'; assign.style.display = 'block';
        btnAssign.classList.add('active'); btnData.classList.remove('active');
        btnSaveMain.style.display = 'none'; 
        loadAvailableWisudawan();
    }
}

function submitMainForm() {
    const form = document.getElementById('kursiForm');
    if(form.reportValidity()) {
        const id = document.getElementById('kursiId').value;
        const url = id ? `/admin/kursi/${id}` : "{{ route('admin.kursi.store') }}";
        const method = id ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' },
            body: JSON.stringify(Object.fromEntries(new FormData(form)))
        }).then(r=>r.json()).then(d=>{
            closeModal();
            Swal.fire('Sukses', d.message, 'success');
            loadData();
        }).catch(()=>Swal.fire('Error','Gagal menyimpan','error'));
    }
}

function loadAvailableWisudawan() {
    const gender = document.getElementById('jenisKelamin').value;
    const hari = document.getElementById('hari').value;
    const select = document.getElementById('selectWisudawan');
    select.innerHTML = '<option>Loading...</option>';
    document.getElementById('searchWisudawanInput').value = '';

    fetch(`{{ route('admin.wisudawan.index') }}?format=json&jenis_kelamin=${gender}&hari=${hari}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        let list = data.data || data; 
        cachedWisudawanList = list.filter(w => w.kursi === null);
        renderWisudawanOptions(cachedWisudawanList);
    })
    .catch(err => {
        console.error(err);
        select.innerHTML = '<option>Gagal memuat data</option>';
    });
}

function renderWisudawanOptions(dataList) {
    const select = document.getElementById('selectWisudawan');
    if(dataList.length === 0) { select.innerHTML = '<option value="">Tidak ada wisudawan yang cocok</option>'; return; }
    const limitList = dataList.slice(0, 100); 
    let html = '<option value="">-- Pilih Wisudawan --</option>';
    limitList.forEach(w => { html += `<option value="${w.id}">${w.user.nim} - ${w.user.name}</option>`; });
    if (dataList.length > 100) { html += `<option disabled>... dan ${dataList.length - 100} lainnya (Gunakan pencarian) ...</option>`; }
    select.innerHTML = html;
}

function filterWisudawanList() {
    const keyword = document.getElementById('searchWisudawanInput').value.toLowerCase();
    const filtered = cachedWisudawanList.filter(w => {
        const name = w.user ? w.user.name.toLowerCase() : '';
        const nim = w.user ? w.user.nim.toLowerCase() : '';
        return name.includes(keyword) || nim.includes(keyword);
    });
    renderWisudawanOptions(filtered);
}

function assignSeat() {
    const kursiId = document.getElementById('kursiId').value;
    const wisudawanId = document.getElementById('selectWisudawan').value;
    if (!wisudawanId) return Swal.fire('Peringatan', 'Pilih wisudawan dulu', 'warning');

    fetch("{{ route('admin.kursi.assign') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ kursi_id: kursiId, wisudawan_id: wisudawanId })
    }).then(r=>r.json()).then(d=>{
        if(d.message) { closeModal(); Swal.fire('Berhasil', 'Wisudawan masuk', 'success'); loadData(); }
    });
}

function unassignSeat() {
    const kursiId = document.getElementById('kursiId').value;
    Swal.fire({
        title: 'Kosongkan?', text: "Wisudawan akan dihapus dari kursi", icon: 'warning', showCancelButton: true
    }).then((res) => {
        if (res.isConfirmed) {
            fetch(`/admin/kursi/${kursiId}/unassign`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => { closeModal(); Swal.fire('Berhasil', 'Kursi kosong', 'success'); loadData(); });
        }
    });
}

window.onclick = function(e) {
    const m = document.getElementById('kursiModal');
    if (e.target == m) closeModal();
}
</script>
@endpush