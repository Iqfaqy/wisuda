// SCRIPT LOGIN PAGE

document.addEventListener('DOMContentLoaded', function () {
  const loginForm = document.getElementById('loginForm');

  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      handleLogin();
    });
  }

  // Demo credentials info
  console.log('Demo Credentials:');
  console.log('NIM/Admin: admin | Password: admin123');
  console.log('NIM/User: user | Password: user123');
});

const API_URL = 'http://127.0.0.1:8000/api';

async function handleLogin() {
  const nim = document.getElementById('nim').value.trim();
  const password = document.getElementById('password').value.trim();
  const rememberMe = document.getElementById('rememberMe').checked;
  const errorMsg = document.getElementById('errorMessage');

  // Reset error message
  errorMsg.classList.remove('show');

  // Validation
  if (!nim || !password) {
    showError('NIM dan password harus diisi!');
    return;
  }

  try {
    const response = await fetch(`${API_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        username: nim,
        password: password
      })
    });

    const data = await response.json();

    if (response.ok) {
      // Login success
      localStorage.setItem('userLoggedIn', JSON.stringify({
        nim: data.user.username,
        name: data.user.name,
        role: data.user.role,
        token: data.token, // Store API token
        loginTime: new Date().toISOString()
      }));

      if (rememberMe) {
        localStorage.setItem('rememberNim', nim);
      }

      showSuccess('Login berhasil! Sekarang menuju dashboard...');

      setTimeout(() => {
        if (data.user.role === 'wisudawan') {
          window.location.href = './wisudawan/dashboard-wisudawan.html';
        } else {
          window.location.href = './admin/dashboard-admin.html';
        }
      }, 1500);
    } else {
      showError(data.message || 'NIM atau password salah!');
    }
  } catch (error) {
    console.error('Login error:', error);
    showError('Terjadi kesalahan koneksi ke server.');
  }
}

function showError(message) {
  const errorMsg = document.getElementById('errorMessage');
  errorMsg.textContent = message;
  errorMsg.classList.add('show');
}

function showSuccess(message) {
  const successMsg = document.getElementById('successMessage');
  if (successMsg) {
    successMsg.textContent = message;
    successMsg.classList.add('show');
  }
}

// Auto-fill username if "Remember Me" was checked
window.addEventListener('DOMContentLoaded', function () {
  const rememberedNim = localStorage.getItem('rememberNim');
  const nimInput = document.getElementById('nim');
  const rememberCheckbox = document.getElementById('rememberMe');

  if (rememberedNim && nimInput) {
    nimInput.value = rememberedNim;
    if (rememberCheckbox) {
      rememberCheckbox.checked = true;
    }
  }
});

// Show/Hide password
function togglePasswordVisibility() {
  const passwordInput = document.getElementById('password');
  const toggleBtn = document.querySelector('.toggle-password');

  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    if (toggleBtn) toggleBtn.textContent = '👁️‍🗨️';
  } else {
    passwordInput.type = 'password';
    if (toggleBtn) toggleBtn.textContent = '👁️';
  }
}
