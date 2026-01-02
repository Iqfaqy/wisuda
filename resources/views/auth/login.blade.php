<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistem Informasi Wisuda</title>
  <meta name="description" content="Login portal Sistem Wisuda UNSIQ">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login">
  <div class="login-wrapper">
    <div class="login-card">
      <!-- HEADER / BRAND -->
      <div class="login-header">
        <img src="{{ asset('images/bg.png') }}" alt="Sistem Wisuda - logo" class="brand-logo">
        <h1>Sistem Informasi Wisuda UNSIQ</h1>
        <p>Universitas Sains Al-Qur'an Jawa Tengah</p>
      </div>

      <!-- ERROR & SUCCESS MESSAGES -->
      @if ($errors->any())
      <div class="error-message" style="display: block;">
        {{ $errors->first() }}
      </div>
      @endif
      
      @if (session('success'))
      <div class="success-message" style="display: block;">
        {{ session('success') }}
      </div>
      @endif

      <!-- LOGIN FORM -->
      <form id="loginForm" method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="form-group icon-user">
          <label for="nim">NIM/ID</label>
          <input type="text" id="nim" name="nim" placeholder="Masukkan NIM/ID" autocomplete="username" value="{{ old('nim') }}" required>
        </div>

        <div class="form-group icon-pass">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Password" autocomplete="current-password" required>
        </div>

        <div class="form-footer">
          <div class="checkbox-wrapper">
            <input type="checkbox" id="rememberMe" name="remember">
            <label for="rememberMe">Ingat saya</label>
          </div>
          <a href="#">Perhatikan Format Penulisan</a>
        </div>

        <button type="submit" class="login-btn">LOGIN</button>
      </form>
    </div>
  </div>

  <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
