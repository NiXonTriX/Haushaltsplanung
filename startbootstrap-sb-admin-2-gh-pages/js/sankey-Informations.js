document.addEventListener("DOMContentLoaded", function () {
const chart = echarts.init(document.getElementById("sankeyChart"));


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
{ name: 'Miete' },
{ name: 'Lebensmittel' },
{ name: 'Tierarzt' },
{ name: 'Geschenk' },
],
links: [
{ source: 'Startkapital', target: 'Fixkosten', value: 1500 },
{ source: 'Startkapital', target: 'Variable Kosten', value: 1200 },
{ source: 'Startkapital', target: 'Tierkosten', value: 300 },
{ source: 'Startkapital', target: 'Spezielle Kosten', value: 250 },
{ source: 'Fixkosten', target: 'Miete', value: 1100 },
{ source: 'Variable Kosten', target: 'Lebensmittel', value: 900 },
{ source: 'Tierkosten', target: 'Tierarzt', value: 300 },
{ source: 'Spezielle Kosten', target: 'Geschenk', value: 250 },
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