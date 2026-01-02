<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Sistem Wisuda UNSIQ')</title>
  <meta name="description" content="@yield('description', 'Sistem Informasi Wisuda UNSIQ')">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
  @stack('styles')
</head>
<body class="@yield('bodyClass')">
  @yield('content')

  <footer class="site-footer">
    <small>&copy; <span class="site-year">{{ date('Y') }}</span> Sistem Wisuda UNSIQ. All rights reserved.</small>
  </footer>

  <script src="{{ asset('js/main.js') }}"></script>
  @stack('scripts')
</body>
</html>
