document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.querySelector("table tbody");
  const sortButtons = document.querySelectorAll(".sort-option");

  // Sortierstatus für ASC / DESC pro Spalte
  const sortState = {};

  const parseEuro = (str) => {
    return parseFloat(
      str.replace(/[^\d,-]/g, "").replace(",", ".")
    ) || 0;
  };

  const getText = (row, idx) => {
    const cell = row.querySelectorAll("td")[idx];
    if (!cell) return "";
    return cell.textContent.trim().toLowerCase();
  };

  const sortTable = (index) => {
    const rows = Array.from(tbody.querySelectorAll("tr"));
    sortState[index] = !sortState[index];
    const asc = sortState[index];

    rows.sort((a, b) => {
      const A = getText(a, index);
      const B = getText(b, index);

      // ========================
      // Betrag numeric sort
      // ========================
      if (index === 2) {
        const numA = parseEuro(A);
        const numB = parseEuro(B);

        return asc ? numA - numB : numB - numA;
      }

      // ========================
      // Einnahmen / Ausgaben
      // text-success = Einnahme
      // text-danger  = Ausgabe
      // ========================
      if (index === 4) {
        const isAExp = a.querySelectorAll("td")[2].classList.contains("text-danger");
        const isBExp = b.querySelectorAll("td")[2].classList.contains("text-danger");

        if (isAExp !== isBExp) {
          return asc ? (isAExp ? -1 : 1) : (isAExp ? 1 : -1);
        }

        // Wenn gleich, Beträge sortieren
        const numA = parseEuro(a.querySelectorAll("td")[2].textContent);
        const numB = parseEuro(b.querySelectorAll("td")[2].textContent);
        return asc ? numA - numB : numB - numA;
      }

      // ========================
      // Default: alphabetisch
      // ========================
      return asc ? A.localeCompare(B) : B.localeCompare(A);
    });

    rows.forEach(row => tbody.appendChild(row));
  };

  sortButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      const columnIndex = parseInt(btn.dataset.column);
      sortTable(columnIndex);
    });
  });
});
