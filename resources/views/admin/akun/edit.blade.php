@extends('layouts.app')

@section('title', 'Edit Akun - Sistem Wisuda')
@section('bodyClass', 'feature-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/feature-pages.css') }}">
<style>
    /* Styling tambahan khusus form edit */
    .edit-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563; }
    .form-group input, .form-group select {
        width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;
        font-size: 0.95rem;
    }
    .full-width { grid-column: span 2; }
    .btn-back {
        background: #f3f4f6; color: #374151; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-block; margin-right: 10px;
    }
    .header-actions { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
</style>
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
  </div>
</header>

<div class="page-container">
  @include('layouts.admin-sidebar')

  <main class="main-content">
    
    <div class="header-actions">
        <div>
            <h1>Edit Akun Pengguna</h1>
            <p style="color:#666;">Perbarui data dan hak akses pengguna</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="edit-container">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="{{ $user->name }}" required>
                </div>

                <div class="form-group">
                    <label>NIM / NIDN / Identitas</label>
                    <input type="text" name="nim" value="{{ $user->nim }}" required>
                </div>

                <div class="form-group">
                    <label>Alamat Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" required>
                </div>

                <div class="form-group">
                    <label>Role (Hak Akses)</label>
                    <select name="role" required>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="wisudawan" {{ $user->role == 'wisudawan' ? 'selected' : '' }}>Wisudawan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status Akun</label>
                    <select name="status" required>
                        <option value="aktif" {{ $user->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $user->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Password Baru <small style="color:red;">(Kosongkan jika tidak ingin mengubah)</small></label>
                    <input type="password" name="password" placeholder="Masukkan password baru...">
                </div>
            </div>

            <div style="margin-top: 2rem; text-align: right; border-top: 1px solid #eee; padding-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 25px;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

  </main>
</div>
@endsection