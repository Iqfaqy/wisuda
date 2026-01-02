@extends('layouts.app')

@section('title', 'QR Code Management - Sistem Wisuda')
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
      <h1>QR Code Management</h1>
      <p>Kelola QR Code wisudawan</p>
    </div>

    <div class="form-card" style="max-width: 600px;">
      <h3>Generate QR Code</h3>
      <p style="margin-top: 10px; color: #666;">
        QR Code untuk setiap wisudawan dibuat secara otomatis berdasarkan NIM mereka.
        Wisudawan dapat mengakses QR Code melalui dashboard mereka.
      </p>
      <div style="margin-top: 20px;">
        <p><strong>Format QR:</strong> NIM Wisudawan</p>
        <p><strong>Contoh:</strong> 2020101</p>
      </div>
    </div>
  </main>
</div>
@endsection
