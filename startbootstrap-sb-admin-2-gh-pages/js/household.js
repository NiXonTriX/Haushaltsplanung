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