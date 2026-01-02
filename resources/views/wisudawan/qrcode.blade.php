@extends('layouts.app')

@section('title', 'QR Code Saya - Sistem Wisuda')
@section('bodyClass', 'feature-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/wisudawan/dashboard-wisudawan.css') }}">
<style>
.qr-container { text-align: center; padding: 40px; background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 400px; margin: 30px auto; }
.qr-code { 
    width: 200px; 
    height: 200px; 
    margin: 20px auto; 
    /* Ubah background jadi putih agar QR terbaca scanner */
    background: #ffffff; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    /* Hapus font-size 80px karena kita pakai gambar/svg sekarang */
    border-radius: 10px; 
    border: 1px solid #ddd; /* Tambah border tipis */
}
.qr-info { margin-top: 20px; }
.qr-nim { font-size: 1.5rem; font-weight: 600; color: #1b5e20; }
.btn-download {
    margin-top: 20px;
    background-color: #1b5e20; /* Warna Hijau UNSIQ */
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}
.btn-download:hover {
    background-color: #144a18;
    color: white;
}
</style>
@endpush

@section('content')
<header>
  <div class="logo"><span class="logo-icon">🎓</span><div class="logo-text"><div class="logo-title">QR Code Saya</div></div></div>
  <div class="user-info">
    <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
    <div>
      <div class="user-name">{{ auth()->user()->name }}</div>
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
      <li><a href="{{ route('wisudawan.qrcode') }}" class="active"><i class="fas fa-qrcode"></i> QR Code</a></li>
      <li><a href="{{ route('wisudawan.kursi') }}"><i class="fas fa-chair"></i> Info Kursi</a></li>
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
    <div class="qr-container">
      <h2>QR Code Kehadiran</h2>
<div class="qr-code" id="qrTarget">
    @if(extension_loaded('gd'))
        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->generate(auth()->user()->nim) !!}
     @else
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ auth()->user()->nim }}" 
             alt="QR Code" style="width: 180px; height: 180px;">
     @endif
</div>

<button onclick="downloadQR()" class="btn-download">
    <i class="fas fa-download"></i> Download QR Code
</button>
      <div class="qr-info">
        <p class="qr-nim">{{ auth()->user()->nim }}</p>
        <p style="color: black">{{ auth()->user()->name }}</p>
        <p style="color: #666; margin-top: 10px;">Tunjukkan QR code ini saat presensi</p>
      </div>
    </div>
  </main>
</div>

<script>
function downloadQR() {
    // 1. Ambil elemen SVG
    const svg = document.querySelector('.qr-code svg');
    
    // 2. Serialisasi SVG menjadi XML
    const serializer = new XMLSerializer();
    let source = serializer.serializeToString(svg);
    
    // Tambahkan namespace jika belum ada
    if(!source.match(/^<svg[^>]+xmlns="http\:\/\/www\.w3\.org\/2000\/svg"/)){
        source = source.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
    }
    if(!source.match(/^<svg[^>]+"http\:\/\/www\.w3\.org\/1999\/xlink"/)){
        source = source.replace(/^<svg/, '<svg xmlns:xlink="http://www.w3.org/1999/xlink"');
    }

    // 3. Konversi ke Blob dan Download
    const preface = '<?xml version="1.0" standalone="no"?>\r\n';
    const url = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(preface + source);

    const downloadLink = document.createElement("a");
    downloadLink.href = url;
    downloadLink.download = "QR_Wisuda_{{ auth()->user()->nim }}.svg";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
@endsection
