(function () {
  const body = document.body;
  const activePage = body ? body.dataset.page : '';

  if (!activePage) {
    return;
  }

  const navItems = document.querySelectorAll('.sidebar .nav-item');
  navItems.forEach((item) => {
    const link = item.querySelector('a.nav-link');
    if (link && link.dataset.page === activePage) {
      item.classList.add('active');
    }
  });
  if (activePage === 'dashboard') {
    initCategoryGroupsChart();
  }

  function initCategoryGroupsChart() {
    const chartContainer = document.getElementById('category-groups-chart');

    if (!chartContainer || typeof echarts === 'undefined') {
      return;
    }

    const categoryGroupChart = echarts.init(chartContainer);

    const option = {
      title: {
        text: 'Wochenausgaben in €',
        left: 'center'
      },

      tooltip: {
        trigger: 'item',
        formatter: (params) => {
          return `${params.seriesName}<br>${params.name}: ${params.value} €`;
        }
      },

      angleAxis: {
        type: 'category',
        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        axisLabel: {
          color: '#555'
        }
      },

      radiusAxis: {
        axisLabel: {
          formatter: '{value} €',
          color: '#777'
        }
      },

      polar: {},
      series: [
        {
          type: 'bar',
          data: [12.50, 20.10, 8.99, 22.70, 15.30, 11.00, 30.90],
          coordinateSystem: 'polar',
          name: 'Manuel',
          stack: 'a',
          emphasis: { focus: 'series' }
        },
        {
          type: 'bar',
          data: [5.99, 12.00, 18.90, 4.25, 9.10, 6.75, 11.20],
          coordinateSystem: 'polar',
          name: 'Stefan',
          stack: 'a',
          emphasis: { focus: 'series' }
        },
        {
          type: 'bar',
          data: [10.20, 8.10, 5.70, 12.80, 14.90, 7.40, 16.00],
          coordinateSystem: 'polar',
          name: 'Berkant',
          stack: 'a',
          emphasis: { focus: 'series' }
        }
      ],

      legend: {
        show: true,
        data: ['Manuel', 'Stefan', 'Berkant'],
        bottom: 0
      }
    };


    categoryGroupChart.setOption(option);
    window.addEventListener('resize', () => categoryGroupChart.resize());
  }
})();



//------------------------------------------------------------------------------------------------------------


const values = {
  bookingDate: document.getElementById("bookingDate").value,
  bookingTitle: document.getElementById("bookingTitle").value,
  bookingAmount: document.getElementById("bookingAmount").value,
  bookingType: document.querySelector('input[name="bookingType"]:checked')?.value
};

// 1) Regeln definieren
const constraints = {
  bookingDate: {
    presence: { allowEmpty: false, message: "^Bitte Datum wählen." }
  },

  bookingTitle: {
    presence: { allowEmpty: false, message: "^Bitte Beschreibung eingeben." },
    length: { minimum: 3, message: "^Mindestens 3 Zeichen." }
  },

  bookingType: {
    presence: { allowEmpty: false, message: "^Bitte Einnahme oder Ausgabe wählen." }
  },

  bookingAmount: function (value, attributes) {
    const type = attributes.bookingType;

    // Basis: muss vorhanden + Zahl
    const rule = {
      presence: { allowEmpty: false, message: "^Bitte Betrag eingeben." },
      numericality: { message: "^Bitte eine gültige Zahl eingeben." }
    };

    // Zusatzregel: Vorzeichen abhängig von Type
    if (type === "income") {
      rule.numericality.greaterThanOrEqualTo = 0;
      rule.numericality.message = "^Einnahmen müssen ≥ 0 sein.";
    }

    if (type === "expense") {
      rule.numericality.lessThanOrEqualTo = 0;
      rule.numericality.message = "^Ausgaben müssen ≤ 0 sein.";
    }

    return rule;
  }
};


// 2) Helpers: Fehler in Bootstrap UI darstellen
function getOrCreateFeedbackEl(input) {
  // Feedback soll direkt nach dem Input im gleichen form-group hängen
  const group = input.closest(".form-group") || input.parentElement;
  let fb = group.querySelector(".invalid-feedback");
  if (!fb) {
    fb = document.createElement("div");
    fb.className = "invalid-feedback";
    group.appendChild(fb);
  }
  return fb;
}

function setValid(input) {
  input.classList.remove("is-invalid");
  input.classList.add("is-valid");
  const fb = getOrCreateFeedbackEl(input);
  fb.textContent = "";
}

function setInvalid(input, message) {
  input.classList.remove("is-valid");
  input.classList.add("is-invalid");
  const fb = getOrCreateFeedbackEl(input);
  fb.textContent = message || "Ungültige Eingabe.";
}

function clearState(input) {
  input.classList.remove("is-valid", "is-invalid");
  const fb = getOrCreateFeedbackEl(input);
  fb.textContent = "";
}

// 3) Werte aus dem Form holen
function getFormValues(form) {
  const values = {};
  new FormData(form).forEach((value, key) => (values[key] = value));
  return values;
}

// 4) Form Validation Logik
const form = document.getElementById("bookingForm");

function validateForm(showValid = true) {
  const values = getFormValues(form);
  const errors = validate(values, constraints) || {};

  // Alle Felder durchgehen, States setzen
  Object.keys(constraints).forEach((name) => {
    const inputs = form.querySelectorAll(`[name="${name}"]`);
    if (!inputs.length) return;

    const msg = errors[name]?.[0];

    // Radio-Gruppe?
    if (inputs.length > 1 && inputs[0].type === "radio") {
      const group = inputs[0].closest(".form-group") || inputs[0].parentElement;
      // Feedback an die form-group hängen
      let fb = group.querySelector(".invalid-feedback");
      if (!fb) {
        fb = document.createElement("div");
        fb.className = "invalid-feedback d-block"; // d-block, damit sichtbar
        group.appendChild(fb);
      }

      if (msg) {
        fb.textContent = msg;
      } else {
        fb.textContent = "";
      }
      return;
    }

    // normale Felder:
    const input = inputs[0];
    if (msg) setInvalid(input, msg);
    else if (showValid) setValid(input);
    else clearState(input);
  });

  return Object.keys(errors).length === 0;
}

// 5) Beim Submit blocken, wenn Fehler
form.addEventListener("submit", (e) => {
  const ok = validateForm(true);
  if (!ok) {
    e.preventDefault();
    e.stopPropagation();
  }
});

// 6) Live-Validation: bei Eingabe sofort prüfen (nice UX)
form.querySelectorAll("input, select, textarea").forEach((el) => {
  el.addEventListener("input", () => validateForm(true));
  el.addEventListener("change", () => validateForm(true));
});






// --- Auto-Sign-Korrektur (Income/Expense) -------------------------

function getBookingType() {
  return document.querySelector('input[name="bookingType"]:checked')?.value || "income";
}

function parseAmount(raw) {
  // erlaubt auch "12,50" -> 12.50
  const normalized = String(raw ?? "").replace(",", ".").trim();
  if (!normalized) return null;
  const n = Number(normalized);
  return Number.isFinite(n) ? n : null;
}

function formatAmount(n) {
  // Optional: 2 Nachkommastellen
  return n.toFixed(2);
}

function normalizeAmountForType(triggerValidate = true) {
  const type = getBookingType();
  const amountEl = document.getElementById("bookingAmount");
  if (!amountEl) return;

  const n = parseAmount(amountEl.value);
  if (n === null) return; // leer/ungültig -> nicht anfassen

  // Nur korrigieren, wenn eindeutig falsch:
  let corrected = n;

  if (type === "expense" && n > 0) corrected = -n;
  if (type === "income" && n < 0) corrected = Math.abs(n);

  if (corrected !== n) {
    amountEl.value = formatAmount(corrected);
  }

  if (triggerValidate) validateForm(true);
}

// 1) Wenn Nutzer Income/Expense umstellt -> Betrag ggf. automatisch korrigieren
document.querySelectorAll('input[name="bookingType"]').forEach((radio) => {
  radio.addEventListener("change", () => normalizeAmountForType(true));
});

// 2) Beim Verlassen des Betrag-Feldes nochmal korrigieren (gute UX)
const bookingAmountEl = document.getElementById("bookingAmount");
if (bookingAmountEl) {
  bookingAmountEl.addEventListener("blur", () => normalizeAmountForType(true));
}
