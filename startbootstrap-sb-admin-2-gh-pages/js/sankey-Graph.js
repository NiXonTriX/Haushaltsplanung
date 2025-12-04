// Chart-Instanz erstellen
const sankeyMainEl = document.getElementById('main');

// Beispiel: Sankey-Diagramm-Daten für Einnahmen und Ausgaben
if (sankeyMainEl) {
  const chart = echarts.init(sankeyMainEl);

  // Beispiel: Sankey-Diagramm-Daten für Einnahmen und Ausgaben
  const option = {
    title: {
      text: 'Einnahmen & Ausgaben Sankey-Diagramm',
      subtext: 'Beispieldaten',
      left: 'center'
    },
    tooltip: {
        trigger: 'item'
    },
    series: [{
        type: 'sankey',
        layout: 'none',
        data: [
        { name: 'Aktueller Kontostand', itemStyle: { color: '#28C76F' } },
        { name: 'Gehalt', itemStyle: { color: '#8bd3e6' } },
        { name: 'Nebenjob', itemStyle: { color: '#8bd3e6' } },
        { name: 'Fixkosten', itemStyle: { color: '#f34e1e' } },
        { name: 'Variable Ausgaben', itemStyle: { color: '#71c285' } },
        { name: 'Sonderausgaben', itemStyle: { color: '#a27be0' } },
        { name: 'Tierkosten', itemStyle: { color: '#f6b26b' } },
        { name: 'Rücklagen', itemStyle: { color: '#a6a6a6' } }
    ],
    links: [
        // Flüsse von Einnahmen zum Kontostand
        { source: 'Gehalt', target: 'Aktueller Kontostand', value: 3000 },
        { source: 'Nebenjob', target: 'Aktueller Kontostand', value: 250 },
        // Flüsse vom Kontostand zu Ausgaben-Kategorien
        { source: 'Aktueller Kontostand', target: 'Fixkosten', value: 1200 }, 
        { source: 'Aktueller Kontostand', target: 'Variable Ausgaben', value: 1500 },
        { source: 'Aktueller Kontostand', target: 'Sonderausgaben', value: 200 },
        { source: 'Aktueller Kontostand', target: 'Tierkosten', value: 150 },
        // Rest (z.B. Rücklagen)
        { source: 'Aktueller Kontostand', target: 'Rücklagen', value: 200 }
    ],
    lineStyle: {
        color: 'source',
        curveness: 0.5
    },
    label: {
        color: '#000',
        fontSize: 12
    }
    }]
};

  // Option zuweisen und Diagramm rendern
  chart.setOption(option);
}