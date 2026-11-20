/* VOLUNOVA - skrip bersama untuk halaman setelah login */
(function () {
  'use strict';

  // ---- Data kegiatan sementara (placeholder lorem ipsum) ----
  var L1 = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
  var L2 = 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.';
  var L3 = 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.';

  var ACTIVITIES = [{
    id: 1,
    judul: 'Nama Kegiatan Volunteering',
    ringkas: L1,
    tanggal: 'Lorem ipsum',
    waktu: 'Lorem ipsum',
    lokasi: 'Lorem ipsum',
    kuota: 'Lorem ipsum',
    penyelenggara: 'Lorem ipsum',
    deskripsi: [L1, L2, L3]
  }];

  // ---- Ikon ----
  var SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';
  var ICON = {
    profile: SVG + '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>',
    history: SVG + '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
    leaderboard: SVG + '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0z"/><path d="M17 5h3v2a3 3 0 0 1-3 3M7 5H4v2a3 3 0 0 0 3 3"/></svg>',
    logout: SVG + '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>'
  };

  function el(tag, cls, text) {
    var n = document.createElement(tag);
    if (cls) n.className = cls;
    if (text !== undefined) n.textContent = text;
    return n;
  }

  // ---- Toast ----
  var toastTimer;
  function toast(msg) {
    var t = document.getElementById('toast');
    if (!t) { t = el('div', 'toast'); t.id = 'toast'; t.setAttribute('role', 'status'); document.body.appendChild(t); }
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { t.classList.remove('show'); }, 2200);
  }

  // ---- Sidebar ----
  function renderSidebar(active) {
    var nav = el('nav', 'sidebar');
    nav.setAttribute('aria-label', 'Menu utama');
    var html =
      '<a class="logo-btn' + (active === 'home' ? ' active' : '') + '" href="volunova-home.html" aria-label="Home" title="Home">' +
      '<svg viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="18" r="18" fill="#fff"/>' +
      '<path d="M10 12l8 14 8-14" fill="none" stroke="#1f8a4c" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>';
    [['profile', 'Profile', 'volunova-profile.html'], ['history', 'History'], ['leaderboard', 'Leaderboard']].forEach(function (it) {
      var target = it[2] ? 'href="' + it[2] + '"' : 'href="#" data-soon="' + it[1] + '"';
      html += '<a class="nav-item' + (active === it[0] ? ' active' : '') + '" ' + target + '>' + ICON[it[0]] + '<span>' + it[1] + '</span></a>';
    });
    html += '<span class="spacer"></span>' +
      '<button type="button" class="nav-item" id="logoutBtn">' + ICON.logout + '<span>Keluar</span></button>';
    nav.innerHTML = html; // isi statis, tanpa data pengguna
    document.body.appendChild(nav);

    nav.addEventListener('click', function (e) {
      var soon = e.target.closest('[data-soon]');
      if (soon) { e.preventDefault(); toast('Halaman ' + soon.getAttribute('data-soon') + ' akan segera tersedia.'); }
    });
    document.getElementById('logoutBtn').addEventListener('click', function () {
      fetch('logout.php', { credentials: 'same-origin' })
        .catch(function () {})
        .then(function () { window.location.href = 'volunova-login.html'; });
    });
  }

  // ---- Penjaga akses + inisialisasi halaman ----
  function goLogin() { window.location.replace('volunova-login.html'); }

  function init(opts) {
    opts = opts || {};
    renderSidebar(opts.active);
    fetch('check_session.php', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d || !d.loggedIn) { goLogin(); return; }
        document.body.classList.remove('pending');
        if (opts.onReady) opts.onReady(d);
      }, goLogin);
  }

  window.Volunova = { init: init, el: el, activities: ACTIVITIES, toast: toast };
})();
