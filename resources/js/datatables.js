import 'datatables.net-dt/css/dataTables.dataTables.min.css';
import 'datatables.net-dt';
import $ from 'jquery';
import DataTable from 'datatables.net';

window.DataTable = DataTable;
window.$ = $;

function initDataTables() {
    document.querySelectorAll('table[data-datatable]').forEach((table) => {
        if ($.fn.DataTable.isDataTable(table)) return;

        const opts = JSON.parse(table.dataset.datatableOptions || '{}');
        const defaults = {
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'No entries',
                infoFiltered: '(filtered from _MAX_)',
                paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' },
                zeroRecords: 'No matching records',
            },
            order: [[0, 'asc']],
        };
        new DataTable(table, { ...defaults, ...opts });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDataTables);
} else {
    initDataTables();
}
