document.addEventListener("DOMContentLoaded", function () {
  const chartEl = document.getElementById("sankeyChart");
  if (!chartEl) return;

  const chart = echarts.init(chartEl);



const option = {
    tooltip: { trigger: 'item' },
    series: [
      {
        type: 'sankey',
        layout: 'none',
        data: [
          { name: 'Startkapital' },
          { name: 'Fixkosten' },
          { name: 'Variable Kosten' },
          { name: 'Tierkosten' },
          { name: 'Spezielle Kosten' },
          { name: 'Sonstige Einnahme' },
          { name: 'Miete' },
          { name: 'Unfallversicherung' },
          { name: 'Lebensmittel' },
          { name: 'Drogerieartikel' },
          { name: 'Tierarzt' },
          { name: 'Futter Hund' },
          { name: 'Geburtstagsgeschenk' },
          { name: 'Spende' }
        ],
        links: [
          // Einnahmen
          { source: 'Startkapital', target: 'Fixkosten', value: 1500 },
          { source: 'Startkapital', target: 'Variable Kosten', value: 1200 },
          { source: 'Startkapital', target: 'Tierkosten', value: 300 },
          { source: 'Startkapital', target: 'Spezielle Kosten', value: 250 },
          { source: 'Startkapital', target: 'Sonstige Einnahme', value: 1000 },

          // Fixkosten
          { source: 'Fixkosten', target: 'Miete', value: 1100 },
          { source: 'Fixkosten', target: 'Unfallversicherung', value: 400 },
          // Variable Kosten
          { source: 'Variable Kosten', target: 'Lebensmittel', value: 900 },
          { source: 'Variable Kosten', target: 'Drogerieartikel', value: 300 },

          // Tierkosten
          { source: 'Tierkosten', target: 'Tierarzt', value: 180 },
          { source: 'Tierkosten', target: 'Futter Hund', value: 120 },

          // Spezielle Kosten
          { source: 'Spezielle Kosten', target: 'Geburtstagsgeschenk', value: 150 },
          { source: 'Spezielle Kosten', target: 'Spende', value: 100 }
        ],
        emphasis: {
          focus: 'adjacency'
        },
        label: {
          color: '#111',
          fontWeight: 'bold'
        },
        lineStyle: {
          color: 'source',
          curveness: 0.5
        }
      }
    ]
  };

  chart.setOption(option);
  window.addEventListener("resize", () => chart.resize());
});