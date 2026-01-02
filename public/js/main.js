// SCRIPT GLOBAL UNTUK SEMUA HALAMAN
console.log("Sistem Wisuda UNSIQ 2025 aktif");

// BUTTON INTERAKTIF
document.querySelectorAll('button').forEach(btn => {
  btn.addEventListener('mousedown', () => btn.style.transform = "scale(0.97)");
  btn.addEventListener('mouseup', () => btn.style.transform = "scale(1)");
});

// SMOOTH SCROLL
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// NOTIFICATION FUNCTION
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `alert alert-${type}`;
  notification.textContent = message;
  notification.style.position = 'fixed';
  notification.style.top = '20px';
  notification.style.right = '20px';
  notification.style.zIndex = '1000';
  notification.style.maxWidth = '400px';
  notification.style.animation = 'slideDown 0.3s ease';

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.opacity = '0';
    notification.style.transition = 'opacity 0.3s';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// LOGOUT FUNCTION
// LOGOUT FUNCTION
async function logout() {
  if (confirm('Apakah Anda yakin ingin keluar?')) {
    const userLoggedIn = localStorage.getItem('userLoggedIn');
    if (userLoggedIn) {
      const user = JSON.parse(userLoggedIn);
      if (user.token) {
        try {
          await fetch('http://127.0.0.1:8000/api/logout', {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${user.token}`,
              'Accept': 'application/json'
            }
          });
        } catch (e) {
          console.error("Logout failed on server", e);
        }
      }
    }

    localStorage.removeItem('userLoggedIn');
    localStorage.removeItem('rememberNim');
    showNotification('Anda telah keluar dari sistem', 'success');
    setTimeout(() => {
      // Determine path relative to current location
      // If we are in pages/wisudawan or pages/admin, go up one level
      // If we are in root, go to pages/login.html
      const path = window.location.pathname;
      if (path.includes('/pages/')) {
        window.location.href = '../login.html';
      } else {
        window.location.href = 'pages/login.html';
      }
    }, 1000);
  }
}

// TABLE SORTING (jika ada)
function sortTable(tableId, n) {
  const table = document.getElementById(tableId);
  let switching = true;
  let dir = "asc";
  let switchcount = 0;

  while (switching) {
    switching = false;
    const rows = table.rows;

    for (let i = 1; i < (rows.length - 1); i++) {
      let shouldSwitch = false;
      const x = rows[i].getElementsByTagName("TD")[n];
      const y = rows[i + 1].getElementsByTagName("TD")[n];

      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }

    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount++;
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}

// EXPORT DATA TO CSV
function exportToCSV(tableId, filename = 'export.csv') {
  const table = document.getElementById(tableId);
  let csv = [];

  for (let row of table.rows) {
    let rowData = [];
    for (let cell of row.cells) {
      rowData.push('"' + cell.textContent + '"');
    }
    csv.push(rowData.join(","));
  }

  downloadCSV(csv.join("\n"), filename);
}

function downloadCSV(csv, filename) {
  const csvFile = new Blob([csv], { type: "text/csv" });
  const downloadLink = document.createElement("a");
  downloadLink.href = URL.createObjectURL(csvFile);
  downloadLink.download = filename;
  document.body.appendChild(downloadLink);
  downloadLink.click();
  document.body.removeChild(downloadLink);
}

// FILTER FUNCTION
function filterTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);
  const filter = input.value.toUpperCase();
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName("td");
    let found = false;

    for (let j = 0; j < cells.length; j++) {
      if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
        found = true;
        break;
      }
    }

    rows[i].style.display = found ? "" : "none";
  }
}

// DATE FORMATTER
function formatDate(date) {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('id-ID', options);
}

// CHECK IF USER LOGGED IN
function checkLogin() {
  const userLoggedIn = localStorage.getItem('userLoggedIn');
  if (!userLoggedIn) {
    window.location.href = 'pages/login.html';
  }
}

// DYNAMIC MENU ACTIVE
function setActiveMenu(menuId) {
  const menuItems = document.querySelectorAll('.sidebar-menu a');
  menuItems.forEach(item => {
    item.classList.remove('active');
  });
  const activeItem = document.getElementById(menuId);
  if (activeItem) {
    activeItem.classList.add('active');
  }
}

// Set dynamic year in footer if present
document.addEventListener('DOMContentLoaded', function () {
  const yearEls = document.querySelectorAll('.site-year');
  if (yearEls.length) {
    const y = new Date().getFullYear();
    yearEls.forEach(el => el.textContent = y);
  }
});
