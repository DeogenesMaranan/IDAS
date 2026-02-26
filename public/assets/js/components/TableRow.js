// TableRow.js
// Reusable TableRow component for dynamic tables

/**
 * Creates a table row element with customizable columns and cell renderers.
 * @param {Object} options
 * @param {Object} options.data - The row data object
 * @param {Array} options.columns - Array of column definitions. Each column can have:
 *   - key: property name in data
 *   - className: (optional) string for cell classes
 *   - render: (optional) function(cellValue, rowData) => Node|string
 * @returns {HTMLTableRowElement}
 */
export function createTableRow({ data, columns }) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-slate-50 transition-colors';
    columns.forEach(col => {
        const td = document.createElement('td');
        if (col.className) td.className = col.className;
        let cellContent;
        if (typeof col.render === 'function') {
            cellContent = col.render(data[col.key], data);
        } else {
            cellContent = data[col.key] != null ? data[col.key] : '';
        }
        if (cellContent instanceof Node) {
            td.appendChild(cellContent);
        } else if (typeof cellContent === 'string' && /<.+>/.test(cellContent)) {
            td.innerHTML = cellContent;
        } else {
            td.textContent = cellContent;
        }
        row.appendChild(td);
    });
    return row;
}
