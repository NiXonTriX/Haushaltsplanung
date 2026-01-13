document.addEventListener('DOMContentLoaded', () => {
  fetch('bookings.php')
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById('bookingsTableBody');
      tbody.innerHTML = '';

      data.forEach(b => {
        const tr = document.createElement('tr');

        const amount = (b.amount_cents / 100).toFixed(2);
        const isIncome = b.cost_type === 'income';

      const categoryText = b.category_parent
        ? `<span class="badge badge-light">${b.category_parent}</span> ${b.category}`
        : b.category ?? '-';

      tr.innerHTML = `
        <td>${b.booking_date}</td>
        <td>${b.title}</td>
        <td>${categoryText}</td>
        <td>${b.person_name ?? '-'} — ${b.account_name ?? '-'}</td>
        <td>${b.payee_name ?? '-'}</td>
        <td class="text-right font-weight-bold ${b.cost_type === 'income' ? 'text-success' : 'text-danger'}">
          € ${(b.amount_cents / 100).toFixed(2)}
        </td>
        <td><span class="badge badge-primary">verbucht</span></td>
      `;


        tbody.appendChild(tr);
      });
    })
    .catch(err => console.error('Fehler beim Laden:', err));
});
