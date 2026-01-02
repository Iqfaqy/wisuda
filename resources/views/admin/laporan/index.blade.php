@extends('layouts.app')

@section('title', 'Laporan - Sistem Wisuda')
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
      <h1>Laporan</h1>
      <p>Generate laporan wisuda</p>
    </div>

    <div class="form-card" style="max-width: 600px;">
      <h3>Export Laporan</h3>
      <div style="margin-top: 20px;">
        <a href="{{ route('admin.presensi.export') }}" class="btn btn-primary" style="margin-right: 10px;">
          <i class="fas fa-file-csv"></i> Export Presensi (CSV)
        </a>
      </div>
      <p style="margin-top: 20px; color: #666;">
        Fitur laporan PDF akan segera tersedia.
      </p>
    </div>
  </main>
</div>
@endsection
