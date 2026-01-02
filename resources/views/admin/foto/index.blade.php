@extends('layouts.app')

@section('title', 'Upload Foto - Sistem Wisuda')
@section('bodyClass', 'feature-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/feature-pages.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stats-unified.css') }}">
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
      <h1>Foto Wisuda</h1>
      <p>Upload link Google Drive untuk foto wisuda</p>
    </div>

    <div class="form-card" style="max-width: 600px; margin-bottom: 30px;">
      <h3>Tambah Link Foto</h3>
      <form method="POST" action="{{ route('admin.foto.store') }}" style="margin-top: 15px;">
        @csrf
        <div class="form-group">
          <label>Link Google Drive</label>
          <input type="url" name="drive_link" placeholder="https://drive.google.com/..." required>
        </div>
        <div class="form-group">
          <label>Hari Wisuda</label>
          <select name="hari" required class="form-control" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
            <option value="1">Hari 1</option>
            <option value="2">Hari 2</option>
          </select>
        </div>
        <div class="form-group">
          <label>Deskripsi (Opsional)</label>
          <textarea name="deskripsi" rows="3" placeholder="Keterangan foto..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Simpan
        </button>
      </form>
    </div>

    <div class="form-card">
      <h3>Daftar Link Foto</h3>
      <div style="margin-top: 15px;">
        @forelse($fotos ?? [] as $foto)
        <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <a href="{{ $foto->drive_link }}" target="_blank">{{ Str::limit($foto->drive_link, 50) }}</a>
            <span class="badge" style="background:#eef2ff; color:#4f46e5; padding:2px 8px; border-radius:4px; font-size:0.8em; margin-left:8px;">Hari {{ $foto->hari ?? 1 }}</span>
            @if($foto->deskripsi)
            <p style="color: #666; font-size: 0.9em;">{{ $foto->deskripsi }}</p>
            @endif
          </div>
          <form action="{{ route('admin.foto.destroy', $foto->id) }}" method="POST" onsubmit="return confirm('Hapus link ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
          </form>
        </div>
        @empty
        <p style="color: #666;">Belum ada link foto.</p>
        @endforelse
      </div>
    </div>
  </main>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Cek apakah ada session success
  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: "{{ session('success') }}",
      showConfirmButton: false,
      timer: 2000
    });
  @endif

  // Cek apakah ada session error
  @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Gagal!',
      text: "{{ session('error') }}",
    });
  @endif

  // Konfirmasi Hapus dengan SweetAlert (Opsional, menggantikan native confirm)
  // Kita bisa hapus onsubmit="return confirm..." di form dan ganti dengan event listener jika mau lebih modern
  // Tapi requirement utama adalah notifikasi SETELAH simpan.
</script>
@endpush
