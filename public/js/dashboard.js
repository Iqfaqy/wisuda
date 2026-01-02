// SCRIPT DASHBOARD PAGE

const API_URL = 'http://127.0.0.1:8000/api';

document.addEventListener('DOMContentLoaded', function () {
  // Check if user is logged in
  const userLoggedIn = localStorage.getItem('userLoggedIn');
  if (!userLoggedIn) {
    window.location.href = '../login.html';
    return;
  }

  // Parse user data
  const user = JSON.parse(userLoggedIn);

  // Validate token with backend
  verifyToken(user.token).then(isValid => {
    if (!isValid) {
      alert('Sesi anda telah berakhir, silakan login kembali.');
      localStorage.removeItem('userLoggedIn');
      window.location.href = '../login.html';
      return;
    }

    // Proceed with dashboard initialization

    // Redirect wisudawan to their dashboard (if logic requires strict path checking)
    // Logic below is a bit loose, relying on file location.
    // Ideally we check if we are on the right page for the role.

    // Update user name in header
    const userNameElement = document.querySelector('.user-name');
    if (userNameElement) {
      userNameElement.textContent = user.name;
    }

    // Initialize animations
    animateStatistics();
    initMenuCards();
  });
});

async function verifyToken(token) {
  if (!token) return false;
  try {
    const response = await fetch(`${API_URL}/user`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    return response.ok;
  } catch (e) {
    return false;
  }
}

// ANIMATE STATISTICS
function animateStatistics() {
  const statValues = document.querySelectorAll('.stat-value');

  statValues.forEach(stat => {
    const finalValue = parseInt(stat.textContent);
    const duration = 2000;
    const startTime = Date.now();

    function updateValue() {
      const elapsed = Date.now() - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const currentValue = Math.floor(finalValue * progress);

      stat.textContent = currentValue;

      if (progress < 1) {
        requestAnimationFrame(updateValue);
      } else {
        stat.textContent = finalValue;
      }
    }

    updateValue();
  });
}

// MENU CARD INTERACTIONS
function initMenuCards() {
  const menuCards = document.querySelectorAll('.menu-card');

  menuCards.forEach((card, index) => {
    card.addEventListener('click', function () {
      handleMenuClick(index, this);
    });

    // Add hover animation
    card.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-10px)';
    });

    card.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0)';
    });
  });
}

// HANDLE MENU CLICK
function handleMenuClick(index, element) {
  const menus = [
    { name: 'QR Code', page: 'pages/admin/qrcode.html' },
    { name: 'Manajemen Kursi', page: 'pages/admin/kursi.html' },
    { name: 'Presensi', page: 'pages/admin/presensi.html' },
    { name: 'Foto Wisuda', page: 'pages/admin/foto.html' },
    { name: 'Laporan', page: 'pages/admin/laporan.html' },
    { name: 'Kelola Akun', page: 'pages/admin/akun.html' }
  ];

  const menu = menus[index];
  if (menu) {
    console.log('Menu clicked:', menu.name);
    // Uncomment untuk navigate ke halaman
    // window.location.href = menu.page;
  }
}

// REFRESH DATA
function refreshDashboard() {
  console.log('Refreshing dashboard...');
  showNotification('Data dashboard diperbarui', 'success');

  // Animate refresh button
  const refreshBtn = event.target;
  refreshBtn.style.animation = 'spin 1s linear';
  setTimeout(() => {
    refreshBtn.style.animation = '';
  }, 1000);
}

// EXPORT ACTIVITY LOG
function exportActivityLog() {
  const activityItems = document.querySelectorAll('.activity-item');
  let csvContent = 'Aktivitas\tWaktu\n';

  activityItems.forEach(item => {
    const text = item.querySelector('.activity-text').textContent;
    const time = item.querySelector('.activity-time').textContent;
    csvContent += text + '\t' + time + '\n';
  });

  downloadFile(csvContent, 'activity-log.csv', 'text/csv');
}

// DOWNLOAD FILE HELPER
function downloadFile(content, filename, type) {
  const element = document.createElement('a');
  element.setAttribute('href', 'data:' + type + ';charset=utf-8,' + encodeURIComponent(content));
  element.setAttribute('download', filename);
  element.style.display = 'none';
  document.body.appendChild(element);
  element.click();
  document.body.removeChild(element);
}

// ADD SPIN ANIMATION
const style = document.createElement('style');
style.textContent = `
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
`;
document.head.appendChild(style);
