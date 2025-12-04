document.addEventListener("DOMContentLoaded", () => {
  const chips = document.querySelectorAll(".filter-chip");
  const tbody = document.querySelector("table tbody");

  // Sortierstatus pro Spalte merken (ASC ⇄ DESC)
  const sortState = {};

  function getCell(row, index) {
    return row.querySelectorAll("td")[index] || null;
  }

  function getCellText(row, index) {
    const cell = getCell(row, index);
    return cell ? cell.textContent.trim() : "";
  }

  function parseEuro(text) {
    // "€ 1.100,00" -> 1100.00
    const cleaned = text
      .replace("€", "")
      .replace(/\./g, "")      // Tausenderpunkte entfernen
      .replace(/\s/g, "")      // Leerzeichen entfernen
      .replace(",", ".");      // Komma zu Punkt
    const num = parseFloat(cleaned);
    return isNaN(num) ? 0 : num;
  }

  function sortRows(columnIndex) {
    const rows = Array.from(tbody.querySelectorAll("tr"));

    // Toggle ASC <-> DESC
    sortState[columnIndex] = !sortState[columnIndex];
    const asc = sortState[columnIndex];

    rows.sort((a, b) => {
      // ===============================
      // 1) Betrag (numerisch) -> Spalte 5
      // ===============================
      if (columnIndex === 5) {
        const textA = getCellText(a, 5);
        const textB = getCellText(b, 5);

        const numA = parseEuro(textA);
        const numB = parseEuro(textB);

        return asc ? numA - numB : numB - numA;
      }

      // ===============================
      // 2) Ein- & Ausgaben (Button) -> gruppieren
      //    Wir schauen trotzdem auf Spalte 5 (Betrag)
      // ===============================
      if (columnIndex === 4) {
        const cellA = getCell(a, 5);
        const cellB = getCell(b, 5);

        const isAExpense = cellA && cellA.classList.contains("text-danger"); // rot
        const isBExpense = cellB && cellB.classList.contains("text-danger"); // rot

        // Ausgaben und Einnahmen gruppieren
        if (isAExpense !== isBExpense) {
          // asc = Ausgaben zuerst, desc = Einnahmen zuerst
          return asc
            ? (isAExpense ? -1 : 1)
            : (isAExpense ? 1 : -1);
        }

        // Wenn beide gleich (beides Einnahmen oder beides Ausgaben),
        // dann nach Betrag sortieren
        const numA = parseEuro(getCellText(a, 5));
        const numB = parseEuro(getCellText(b, 5));
        return asc ? numA - numB : numB - numA;
      }

      // ===============================
      // 3) Standard: alphabetisch sortieren
      // ===============================
      const textA = getCellText(a, columnIndex).toLowerCase();
      const textB = getCellText(b, columnIndex).toLowerCase();

      return asc
        ? textA.localeCompare(textB)
        : textB.localeCompare(textA);
    });

    // Neue Reihenfolge in DOM einfügen
    rows.forEach(row => tbody.appendChild(row));
  }

  // Klick-Handler für Chips
  chips.forEach(chip => {
    chip.addEventListener("click", () => {
      // Optional: aktive Optik
      chips.forEach(c => c.classList.remove("active"));
      chip.classList.add("active");

      const columnIndex = parseInt(chip.dataset.column, 10);
      sortRows(columnIndex);
    });
  });
});
