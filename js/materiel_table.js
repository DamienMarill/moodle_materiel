/**
 * JavaScript for materiel table filtering and sorting
 *
 * @package    local_materiel
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        var table = document.getElementById('materiel-table');
        if (!table) {
            return; // Table not found, exit
        }

        var tbody = table.querySelector('tbody');
        var filterButtons = document.querySelectorAll('.filter-btn');
        var searchInput = document.getElementById('materiel-search');
        var sortableHeaders = table.querySelectorAll('.sortable');

        var currentFilter = 'all';
        var currentSort = {column: -1, ascending: true};

        // Filter functionality
        filterButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(function(b) {
                    b.classList.remove('active');
                });
                // Add active class to clicked button
                this.classList.add('active');

                currentFilter = this.getAttribute('data-filter');
                applyFilters();
            });
        });

        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                applyFilters();
            });
        }

        // Sorting functionality
        sortableHeaders.forEach(function(header) {
            header.style.cursor = 'pointer';
            header.style.userSelect = 'none';

            // Add sort indicator
            var indicator = document.createElement('span');
            indicator.className = 'sort-indicator';
            indicator.style.marginLeft = '5px';
            indicator.style.opacity = '0.3';
            indicator.textContent = '▼';
            header.appendChild(indicator);

            header.addEventListener('click', function() {
                var column = parseInt(this.getAttribute('data-column'));

                // Toggle sort direction if clicking same column
                if (currentSort.column === column) {
                    currentSort.ascending = !currentSort.ascending;
                } else {
                    currentSort.column = column;
                    currentSort.ascending = true;
                }

                sortTable(column, currentSort.ascending);
                updateSortIndicators();
            });
        });

        function applyFilters() {
            var searchText = searchInput ? searchInput.value.toLowerCase() : '';
            var rows = tbody.querySelectorAll('tr');

            rows.forEach(function(row) {
                var status = row.getAttribute('data-status');
                var identifier = row.getAttribute('data-identifier') || '';
                var name = row.getAttribute('data-name') || '';
                var type = row.getAttribute('data-type') || '';
                var user = row.getAttribute('data-user') || '';

                // Check status filter
                var statusMatch = (currentFilter === 'all' || status === currentFilter);

                // Check search text
                var searchMatch = (
                    searchText === '' ||
                    identifier.indexOf(searchText) !== -1 ||
                    name.indexOf(searchText) !== -1 ||
                    type.toLowerCase().indexOf(searchText) !== -1 ||
                    user.indexOf(searchText) !== -1
                );

                // Show or hide row
                if (statusMatch && searchMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function sortTable(columnIndex, ascending) {
            var rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort(function(a, b) {
                var aCell = a.cells[columnIndex];
                var bCell = b.cells[columnIndex];

                if (!aCell || !bCell) {
                    return 0;
                }

                var aText = aCell.textContent.trim();
                var bText = bCell.textContent.trim();

                // Try numeric comparison first
                var aNum = parseFloat(aText);
                var bNum = parseFloat(bText);

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return ascending ? (aNum - bNum) : (bNum - aNum);
                }

                // String comparison
                if (ascending) {
                    return aText.localeCompare(bText);
                } else {
                    return bText.localeCompare(aText);
                }
            });

            // Re-append rows in sorted order
            rows.forEach(function(row) {
                tbody.appendChild(row);
            });

            // Re-apply filters to maintain filter state
            applyFilters();
        }

        function updateSortIndicators() {
            sortableHeaders.forEach(function(header) {
                var indicator = header.querySelector('.sort-indicator');
                var column = parseInt(header.getAttribute('data-column'));

                if (column === currentSort.column) {
                    indicator.style.opacity = '1';
                    indicator.textContent = currentSort.ascending ? '▲' : '▼';
                } else {
                    indicator.style.opacity = '0.3';
                    indicator.textContent = '▼';
                }
            });
        }
    }
})();
