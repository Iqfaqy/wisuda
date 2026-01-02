@extends('layouts.app')

@section('title', 'Manajemen Wisudawan - Sistem Wisuda')
@section('bodyClass', 'wisudawan-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard-admin.css') }}">
<style>
  /* Custom Styles for Wisudawan Page */
  .page-header {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .content-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    padding: 1.5rem;
  }

  /* Table Styling */
  .table-responsive {
    overflow-x: auto;
  }
  
  .table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    color: #256E29;
  }
  
  .table th {
    background: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
    padding: 1rem;
    text-align: left;
    border-bottom: 2px solid #e9ecef;
  }
  
  .table td {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
  }
  
  .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 500;
  }
  
  .status-badge.success { background: #e8f5e9; color: #2e7d32; }
  .status-badge.warning { background: #fff3e0; color: #ef6c00; }
  .status-badge.info { background: #e3f2fd; color: #1565c0; }

  /* Action Buttons */
  .action-btn {
    border: none;
    background: none;
    padding: 0.5rem;
    cursor: pointer;
    transition: transform 0.2s;
    border-radius: 4px;
  }
  
  .action-btn:hover { background: #f5f5f5; transform: scale(1.1); }
  .btn-edit { color: #f59e0b; }
  .btn-delete { color: #ef4444; }

  /* Modal Styling */
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
  }
  
  .modal.active { display: flex; }
  
  .modal-content {
    background: white;
    width: 90%;
    max-width: 600px;
    border-radius: 12px;
    padding: 2rem;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideUp 0.3s ease;
  }
  
  @keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .form-group { margin-bottom: 1.5rem; }
  .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151; }
  .form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    transition: border-color 0.2s;
  }
  .form-control:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
  
  .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
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

<div class="dashboard-container">
  @include('layouts.admin-sidebar')

  <main class="main-content">
    <div class="page-header">
      <div>
        <h1>Manajemen Wisudawan</h1>
        <p class="text-gray">Kelola data peserta wisuda</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('createModal')">
        <i class="fas fa-plus"></i> Tambah Wisudawan
      </button>
    </div>

    <!-- Filters -->
    <div class="content-card" style="margin-bottom: 1.5rem;">
      <form action="" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Cari Nama/NIM..." class="form-control" style="flex: 2;" value="{{ request('search') }}">
        <select name="prodi" class="form-control" style="flex: 1;">
          <option value="">Semua Prodi</option>
          <!-- Populate via JS or PHP -->
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
      </form>
    </div>

    <div class="content-card">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>NIM</th>
              <th>Nama</th>
              <th>Prodi</th>
              <th>IPK</th>
              <th>Kursi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($wisudawan as $w)
            <tr>
              <td>{{ $w->user->nim }}</td>
              <td>
                <div style="font-weight: 500;">{{ $w->user->name }}</div>
                <small class="text-gray">{{ $w->user->email }}</small>
              </td>
              <td>{{ $w->prodi ?? '-' }}</td>
              <td>{{ $w->ipk ?? '-' }}</td>
              <td>
                @if($w->kursi)
                  <span class="status-badge info">{{ $w->kursi->kode_kursi }}</span>
                @else
                  <span class="status-badge warning">Belum ada</span>
                @endif
              </td>
              <td>
                <span class="status-badge {{ $w->presensi ? 'success' : 'warning' }}">
                  {{ $w->presensi ? 'Hadir' : 'Belum Hadir' }}
                </span>
              </td>
              <td>
                <button class="action-btn btn-edit" onclick="editWisudawan({{ $w->id }})">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn btn-delete" onclick="deleteWisudawan({{ $w->id }})">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <div style="margin-top: 1.5rem;">
        {{ $wisudawan->withQueryString()->links() }}
      </div>
    </div>
  </main>
</div>

<!-- Modal Create/Edit -->
<div id="wisudawanModal" class="modal">
  <div class="modal-content">
    <h2 id="modalTitle" style="margin-bottom: 1.5rem;">Tambah Wisudawan</h2>
    <form id="wisudawanForm">
      @csrf
      <input type="hidden" id="wisudawanId">
      
      <div id="createOnlyFields">
          <div class="form-group">
            <label>NIM <span class="text-danger">*</span></label>
            <input type="text" name="nim" id="nim" class="form-control" placeholder="Nomor Induk Mahasiswa">
          </div>
          
          <div class="form-group">
            <label>Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Nama Mahasiswa">
          </div>

          <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control" placeholder="email@mahasiswa.com">
          </div>
          
          <div class="form-group">
            <label>Password Akun <span class="text-danger">*</span></label>
            <div style="position: relative;">
                <input type="password" name="password" id="password" class="form-control" placeholder="Buat password (min. 6 karakter)" required minlength="6">
                <span onclick="togglePasswordVisibility()" style="position: absolute; right: 15px; top: 10px; cursor: pointer; color: #6b7280;">
                    <i class="fas fa-eye" id="passwordIcon"></i>
                </span>
            </div>
            <small class="text-muted">Password ini digunakan wisudawan untuk login.</small>
          </div>

          <hr style="margin: 20px 0; border:0; border-top:1px solid #eee;">
      </div>
      
      <div class="form-group">
        <label>Program Studi</label>
        <input type="text" name="prodi" id="prodi" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Fakultas</label>
        <input type="text" name="fakultas" id="fakultas" class="form-control">
      </div>

      <div class="row" style="display: flex; gap: 1rem;">
        <div class="form-group" style="flex: 1;">
          <label>IPK</label>
          <input type="number" step="0.01" name="ipk" id="ipk" class="form-control" max="4.00">
        </div>
        <div class="form-group" style="flex: 1;">
          <label>Jenis Kelamin</label>
          <select name="jenis_kelamin" id="jenisKelamin" class="form-control">
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>
      </div>
      
      <div class="form-group">
        <label>Nomor Telepon</label>
        <input type="text" name="telepon" id="telepon" class="form-control">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openModal(type) {
  const modal = document.getElementById('wisudawanModal');
  const title = document.getElementById('modalTitle');
  const form = document.getElementById('wisudawanForm');
  
  if (type === 'createModal') {
    title.textContent = 'Tambah Wisudawan Baru';
    form.reset();
    document.getElementById('wisudawanId').value = '';
    
    // TAMPILKAN input akun saat create
    document.getElementById('createOnlyFields').style.display = 'block';
    
    // Set field required agar tidak error validation di frontend
    document.getElementById('nim').setAttribute('required', 'required');
    document.getElementById('name').setAttribute('required', 'required');
    document.getElementById('email').setAttribute('required', 'required');
    document.getElementById('password').setAttribute('required', 'required');
  }
  
  modal.classList.add('active');
}

function closeModal() {
  document.getElementById('wisudawanModal').classList.remove('active');
}

function editWisudawan(id) {
  fetch(`/admin/wisudawan/${id}`, {
    headers: { 'Accept': 'application/json' }
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById('wisudawanId').value = data.id;
    document.getElementById('prodi').value = data.prodi;
    document.getElementById('fakultas').value = data.fakultas;
    document.getElementById('ipk').value = data.ipk;
    document.getElementById('jenisKelamin').value = data.jenis_kelamin;
    document.getElementById('telepon').value = data.telepon;
    
    // SEMBUNYIKAN input akun saat edit (karena edit user beda logic)
    document.getElementById('createOnlyFields').style.display = 'none';
    
    // Hapus required saat edit
    document.getElementById('nim').removeAttribute('required');
    document.getElementById('name').removeAttribute('required');
    document.getElementById('email').removeAttribute('required');
    document.getElementById('password').removeAttribute('required');
    document.getElementById('modalTitle').textContent = 'Edit Data Wisudawan';
    document.getElementById('wisudawanModal').classList.add('active');
  });
}

document.getElementById('wisudawanForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const id = document.getElementById('wisudawanId').value;
  const isEdit = !!id;
  const url = isEdit ? `/admin/wisudawan/${id}` : '/admin/wisudawan';
  const method = isEdit ? 'PUT' : 'POST';
  
  const formData = new FormData(this);
  const data = Object.fromEntries(formData.entries());

  fetch(url, {
    method: method,
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(resp => {
    if (resp.message) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: resp.message,
        timer: 1500
      }).then(() => location.reload());
    } else {
      throw new Error('Gagal menyimpan');
    }
  })
  .catch(err => {
    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
  });
});

function deleteWisudawan(id) {
  Swal.fire({
    title: 'Hapus Data?',
    text: "Data wisudawan akan dihapus permanen",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Ya, Hapus!'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/admin/wisudawan/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
      .then(res => res.json())
      .then(resp => {
        Swal.fire('Terhapus!', resp.message, 'success')
        .then(() => location.reload());
      });
    }
  });
}

// Close modal when clicking outside
window.onclick = function(event) {
  const modal = document.getElementById('wisudawanModal');
  if (event.target == modal) {
    closeModal();
  }
}

// Tambahkan fungsi ini di dalam tag <script> Anda
function togglePasswordVisibility() {
    const input = document.getElementById('password');
    const icon = document.getElementById('passwordIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
