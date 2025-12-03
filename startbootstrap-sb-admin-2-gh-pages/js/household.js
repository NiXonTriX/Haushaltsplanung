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
})();