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
      angleAxis: {
        type: 'category',
        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
      },
      radiusAxis: {},
      polar: {},
      series: [
        {
          type: 'bar',
          data: [1, 2, 3, 4, 3, 5, 1],
          coordinateSystem: 'polar',
          name: 'Manuel',
          stack: 'a',
          emphasis: {
            focus: 'series'
          }
        },
        {
          type: 'bar',
          data: [2, 4, 6, 1, 3, 2, 1],
          coordinateSystem: 'polar',
          name: 'Stefan',
          stack: 'a',
          emphasis: {
            focus: 'series'
          }
        },
        {
          type: 'bar',
          data: [1, 2, 3, 4, 1, 2, 5],
          coordinateSystem: 'polar',
          name: 'Berkant',
          stack: 'a',
          emphasis: {
            focus: 'series'
          }
        }
      ],
      legend: {
        show: true,
        data: ['Manuel', 'Stefan', 'Berkant']
      }
    };

    categoryGroupChart.setOption(option);
    window.addEventListener('resize', () => categoryGroupChart.resize());
  }
})();