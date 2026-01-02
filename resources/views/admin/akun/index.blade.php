@extends('layouts.app')

@section('title', 'Manajemen Akun - Sistem Wisuda')
@section('description', 'Manajemen Akun Administrator')
@section('bodyClass', 'feature-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/feature-pages.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stats-unified.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/manajemen-akun.css') }}">
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

<div class="page-container">
  @include('layouts.admin-sidebar')

  <main class="main-content">
    <div class="page-header">
      <h1>Manajemen Akun</h1>
      <p>Kelola akun administrator dan reset password</p>
    </div>

    <!-- STATISTICS -->
    <div class="stats-grid">
      <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-users-cog"></i></div>
        <div class="stat-content">
          <div class="stat-value" id="stat-total-akun">{{ $users->total() }}</div>
          <div class="stat-label">Total Akun</div>
        </div>
      </div>
      <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-content">
          <div class="stat-value" id="stat-aktif">{{ $users->where('status', 'aktif')->count() }}</div>
          <div class="stat-label">Akun Aktif</div>
        </div>
      </div>
      <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-user-times"></i></div>
        <div class="stat-content">
          <div class="stat-value" id="stat-nonaktif">{{ $users->where('status', 'nonaktif')->count() }}</div>
          <div class="stat-label">Akun Nonaktif</div>
        </div>
      </div>
      <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
        <div class="stat-content">
          <div class="stat-value">Admin</div>
          <div class="stat-label">Role Anda</div>
        </div>
      </div>
    </div>

    <!-- TABS -->
    <div class="tabs-container">
      <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab('manage')">
          <i class="fas fa-edit"></i> Kelola Akun
        </button>
        <button class="tab-btn" onclick="switchTab('add')">
          <i class="fas fa-user-plus"></i> Tambah Akun
        </button>
      </div>

      <!-- TAB: MANAGE -->
      <div class="tab-content active" id="tab-manage">
        <div class="accounts-list" id="accountsList">
          @forelse($users as $user)
          <div class="account-card">
            <div class="account-header">
              <div class="account-info">
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->email }}</p>
              </div>
              <span class="role-badge {{ strtolower($user->role) }}">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="account-details">
              <div class="detail-item">
                <span class="detail-label">NIM/ID:</span>
                <span class="detail-value">{{ $user->nim }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span class="status-badge {{ strtolower($user->status) }}">{{ ucfirst($user->status) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Terakhir Login:</span>
                <span class="detail-value">{{ $user->last_login ? $user->last_login->format('Y-m-d H:i') : '-' }}</span>
              </div>
            </div>
            <div class="account-actions">
              <button class="btn btn-sm btn-primary" onclick="editAccount({{ $user->id }})">
                <i class="fas fa-edit"></i> Edit
              </button>
              <button class="btn btn-sm btn-warning" onclick="resetPassword({{ $user->id }})">
                <i class="fas fa-key"></i> Reset Pass
              </button>
              <button class="btn btn-sm btn-danger" onclick="deleteAccount({{ $user->id }})">
                <i class="fas fa-trash"></i> Hapus
              </button>
            </div>
          </div>
          @empty
          <p>Tidak ada data akun.</p>
          @endforelse
        </div>
        {{ $users->links() }}
      </div>

      <!-- TAB: ADD ACCOUNT -->
      <div class="tab-content" id="tab-add">
        <form class="form-card" method="POST" action="{{ route('admin.users.store') }}">
          @csrf
          <div class="form-group">
            <label>Nama Admin</label>
            <input type="text" name="name" placeholder="Contoh: Admin Wisuda" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="admin@unsiq.ac.id" required>
          </div>
          <div class="form-group">
            <label>NIM/ID</label>
            <input type="text" name="nim" placeholder="Nomor identitas admin" required>
          </div>
          <div class="form-group">
            <label>Password Awal</label>
            <input type="password" name="password" placeholder="Minimal 8 karakter" required>
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="role" required>
              <option value="">Pilih Role</option>
              <option value="admin">Administrator</option>
              <option value="wisudawan">Wisudawan</option>
            </select>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Tambah Akun
            </button>
            <button type="reset" class="btn btn-secondary">
              <i class="fas fa-redo"></i> Reset
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tabName) {
  document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
  document.getElementById('tab-' + tabName).classList.add('active');
  event.target.classList.add('active');
}

function editAccount(id) {
  window.location.href = '{{ route('admin.users.index') }}/' + id;
}

function resetPassword(id) {
  if (confirm('Reset password akun ini?')) {
    fetch('{{ url('/admin/users') }}/' + id + '/reset-password', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      }
    })
    .then(r => r.json())
    .then(data => alert(data.message))
    .catch(err => alert('Error: ' + err));
  }
}

function deleteAccount(id) {
  if (confirm('Yakin hapus akun ini?')) {
    fetch('{{ url('/admin/users') }}/' + id, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      }
    })
    .then(r => r.json())
    .then(data => {
      alert(data.message);
      location.reload();
    })
    .catch(err => alert('Error: ' + err));
  }
}
</script>
@endpush
