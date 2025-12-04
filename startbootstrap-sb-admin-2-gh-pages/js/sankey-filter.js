document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("sankeyChart");
  let chart;

const fullData = [
  // Einnahmen
  { from: 'Startkapital', to: 'Fixkosten', flow: 1500 },
  { from: 'Startkapital', to: 'Variable Kosten', flow: 1200 },
  { from: 'Startkapital', to: 'Tierkosten', flow: 300 },
  { from: 'Startkapital', to: 'Spezielle Kosten', flow: 250 },
  { from: 'Startkapital', to: 'Sonstige Einnahme', flow: 1000 },

  // Fixkosten
  { from: 'Fixkosten', to: 'Miete', flow: 1100 },
  { from: 'Fixkosten', to: 'Unfallversicherung', flow: 400 },

  // Variable Kosten
  { from: 'Variable Kosten', to: 'Lebensmittel', flow: 900 },
  { from: 'Variable Kosten', to: 'Drogerieartikel', flow: 300 },

  // Tierkosten
  { from: 'Tierkosten', to: 'Tierarzt', flow: 180 },
  { from: 'Tierkosten', to: 'Futter Hund', flow: 120 },

  // Spezielle Kosten
  { from: 'Spezielle Kosten', to: 'Geburtstagsgeschenk', flow: 150 },
  { from: 'Spezielle Kosten', to: 'Spende', flow: 100 }
];

    const uniqueNodes = Array.from(new Set(
  fullData.flatMap(link => [link.from, link.to])
)).map(name => {
  let color;

  if (name === 'Startkapital' || name === 'Sonstige Einnahme') {
    color = '#28a745'; // Grün
  } else if (['Fixkosten', 'Variable Kosten', 'Tierkosten', 'Spezielle Kosten'].includes(name)) {
    color = '#dc3545'; // Rot für Oberkategorien
  } else {
    color = '#f0ad4e'; // Orange für Unterpunkte
  }

  return { name, itemStyle: { color } };
});


  function createChart(filtered) {
    if (chart) chart.destroy();
    chart = new Chart(ctx, {
      type: 'sankey',
      data: {
        labels: [],
        datasets: [{
          label: 'Finanzfluss',
          data: filtered,
          colorFrom: c => ['Startkapital', 'Sonstige Einnahme'].includes(c.from) ? '#28a745' : '#f67280',
          colorTo: c => ['Startkapital', 'Sonstige Einnahme'].includes(c.to) ? '#28a745' : '#f67280',
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            callbacks: {
              label: context => `${context.raw.from} → ${context.raw.to}: € ${context.raw.flow}`
            }
          }
        }
      }
    });
  }

  function filterData(category) {
    if (category === "all") return fullData;

    const parents = ['Startkapital'];
    return fullData.filter(d =>
      d.to === category ||
      (d.from === category && !parents.includes(d.to)) ||
      (d.from === 'Startkapital' && d.to === category)
    );
  }

  document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
      this.classList.add("active");

      const filter = this.getAttribute("data-filter");
      const newData = filterData(filter);
      createChart(newData);
    });
  });

  createChart(fullData); // initial
});
