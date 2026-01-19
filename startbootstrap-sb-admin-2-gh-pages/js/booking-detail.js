(function () {
  const statusSelect = document.getElementById('detailStatus');
  const statusBadge  = document.getElementById('statusBadge');

  const dirBadge = document.getElementById('directionBadge');
  const dirRadios = document.querySelectorAll('input[name="direction"]');

  function setBadge(el, text, cls) {
    if (!el) return;
    el.textContent = text;

    // alte badge-* Klassen entfernen
    [...el.classList].forEach(c => { if (c.startsWith('badge-')) el.classList.remove(c); });
    el.classList.add(cls);
  }

  // Status live
  if (statusSelect && statusBadge) {
    const map = {
      planned:   { text: 'geplant',   cls: 'badge-warning' },
      due:       { text: 'fällig',    cls: 'badge-info' },
      booked:    { text: 'gebucht',   cls: 'badge-success' },
      posted:    { text: 'verbucht',  cls: 'badge-primary' },
      cancelled: { text: 'storniert', cls: 'badge-secondary' },
    };

    const updateStatus = () => {
      const cfg = map[statusSelect.value] || { text: statusSelect.value, cls: 'badge-secondary' };
      setBadge(statusBadge, cfg.text, cfg.cls);
    };

    statusSelect.addEventListener('change', updateStatus);
    updateStatus(); // initial setzen
  }

  // Direction live
  if (dirBadge && dirRadios.length) {
    const dmap = {
      income:  { text: 'Einnahme', cls: 'badge-success' },
      expense: { text: 'Ausgabe',  cls: 'badge-danger' },
    };

    const updateDir = () => {
      const checked = [...dirRadios].find(r => r.checked);
      const cfg = dmap[checked?.value] || { text: '—', cls: 'badge-secondary' };
      setBadge(dirBadge, cfg.text, cfg.cls);
    };

    dirRadios.forEach(r => r.addEventListener('change', updateDir));
    updateDir(); // initial setzen
  }
})();
