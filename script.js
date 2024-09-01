document.addEventListener('DOMContentLoaded', () => {
    // Handle filter changes
    const filters = document.querySelectorAll('.form-check-input');
    filters.forEach(filter => {
        filter.addEventListener('change', () => {
            // Add your filtering logic here
            console.log('Filter changed');
        });
    });
});
