(function () {
  const page = document.body ? document.body.dataset.page : "";
  if (page !== "bookings") {
    return;
  }

  const filterButtons = document.querySelectorAll("[data-booking-filter]");
  const rows = Array.from(document.querySelectorAll("table tbody tr"));
  const sumElement = document.getElementById("bookingSum");
  const filtersContainer = document.getElementById("bookingFilters");
  const toggleFiltersButton = document.getElementById("toggleFilters");

  const currencyFormatter = new Intl.NumberFormat("de-DE", {
    style: "currency",
    currency: "EUR"
  });

  function parseEuro(text) {
    const cleaned = text
      .replace("€", "")
      .replace(/\./g, "")
      .replace(/\s/g, "")
      .replace(",", ".");

    const value = parseFloat(cleaned);
    return Number.isNaN(value) ? 0 : value;
  }

  function classifyRows() {
    rows.forEach((row) => {
      const amountCell = row.querySelector("td:nth-child(6)");
      const isExpense = amountCell && amountCell.classList.contains("text-danger");
      const isIncome = amountCell && amountCell.classList.contains("text-success");

      if (isIncome) {
        row.dataset.bookingType = "income";
      } else if (isExpense) {
        row.dataset.bookingType = "expense";
      } else {
        row.dataset.bookingType = "unknown";
      }
    });
  }

  function setActiveButton(selectedType) {
    filterButtons.forEach((button) => {
      const isActive = button.dataset.bookingFilter === selectedType;
      button.classList.toggle("btn-primary", isActive);
      button.classList.toggle("btn-outline-secondary", !isActive);
      button.disabled = isActive;
      button.setAttribute("aria-pressed", String(isActive));
    });
  }

  function applyFilter(selectedType) {
    rows.forEach((row) => {
      const type = row.dataset.bookingType;
      const matches = selectedType === "all" || type === selectedType;
      row.style.display = matches ? "" : "none";
    });

    updateVisibleSum();
  }

  function updateVisibleSum() {
    const total = rows.reduce((sum, row) => {
      if (row.style.display === "none") {
        return sum;
      }

      const amountCell = row.querySelector("td:nth-child(6)");
      const rawText = amountCell ? amountCell.textContent : "0";
      const value = parseEuro(rawText || "0");
      const isExpense = amountCell && amountCell.classList.contains("text-danger");

      return sum + (isExpense ? -value : value);
    }, 0);

    if (sumElement) {
      sumElement.textContent = currencyFormatter.format(total);
    }
  }

  function initToggleFilters() {
    if (!toggleFiltersButton || !filtersContainer) {
      return;
    }

    toggleFiltersButton.addEventListener("click", () => {
      const isHidden = filtersContainer.classList.toggle("d-none");
      toggleFiltersButton.setAttribute("aria-expanded", String(!isHidden));
      toggleFiltersButton.textContent = isHidden
        ? "Filterkriterien einblenden"
        : "Filterkriterien ausblenden";
    });
  }

  function initFilterButtons() {
    filterButtons.forEach((button) => {
      button.addEventListener("click", () => {
        const selectedType = button.dataset.bookingFilter || "all";
        setActiveButton(selectedType);
        applyFilter(selectedType);
      });
    });
  }

  classifyRows();
  initToggleFilters();
  initFilterButtons();
  setActiveButton("all");
  applyFilter("all");
})();