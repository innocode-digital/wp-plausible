(function () {
  const period = document.getElementById('filter-by-period');
  const startDate = document.getElementById('filter-by-start_date');
  const endDate = document.getElementById('filter-by-end_date');

  if (period) {
    period.addEventListener('change', () => {
      const disabled = period.value !== 'custom';

      startDate.disabled = disabled;
      endDate.disabled = disabled;
    });
  }

  if (startDate) {
    startDate.addEventListener('change', () => {
      endDate.min = startDate.value;
    });
  }

  if (endDate) {
    endDate.addEventListener('change', () => {
      startDate.max = endDate.value
        ? endDate.value
        : new Date().toISOString().split('T')[0];
    });
  }
})();
