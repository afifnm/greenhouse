// Sales form — dynamic row add/remove and real-time subtotal/total calculation

function recalcRow(rowEl) {
    const w = parseFloat(rowEl.querySelector('[data-weight]')?.value) || 0;
    const p = parseFloat(rowEl.querySelector('[data-price]')?.value) || 0;
    const span = rowEl.querySelector('.subtotal-display');
    if (span) {
        span.textContent = 'Rp ' + (w * p).toLocaleString('id-ID');
    }
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.sale-row').forEach(row => {
        const w = parseFloat(row.querySelector('[data-weight]')?.value) || 0;
        const p = parseFloat(row.querySelector('[data-price]')?.value) || 0;
        total += w * p;
    });
    const el = document.getElementById('grand-total');
    if (el) {
        el.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
}

function reindexRows() {
    document.querySelectorAll('.sale-row').forEach((row, index) => {
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/sale_items\[\d+\]/, 'sale_items[' + index + ']');
        });
    });
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.sale-row');
    if (rows.length <= 1) {
        // Clear fields instead of removing
        rows[0]?.querySelectorAll('input, select').forEach(el => el.value = '');
        return;
    }
    btn.closest('tr').remove();
    reindexRows();
    recalcTotal();
}

function addRow() {
    const tbody = document.getElementById('sale-items-body');
    const template = document.getElementById('row-template');
    if (!tbody || !template) return;
    const clone = template.content.cloneNode(true);
    const idx = Date.now();
    clone.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace('[N]', '[' + idx + ']');
    });
    tbody.appendChild(clone);
    recalcTotal();
}

// Expose to onclick attributes in the template
window.removeRow = removeRow;
window.addRow = addRow;

document.addEventListener('DOMContentLoaded', () => {
    recalcTotal();

    // Event delegation — works for dynamically added rows automatically
    document.addEventListener('input', (e) => {
        if (e.target.closest('[data-weight]') || e.target.closest('[data-price]')) {
            const row = e.target.closest('.sale-row');
            if (row) { recalcRow(row); recalcTotal(); }
        }
    });

    document.getElementById('add-row-btn')?.addEventListener('click', addRow);

    // Toggle detail rows in index page
    document.querySelectorAll('.detail-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('tr').nextElementSibling;
            if (row?.classList.contains('detail-row')) {
                row.classList.toggle('hidden');
                // Rotate arrow
                const arrow = btn.querySelector('.detail-arrow');
                if (arrow) arrow.style.transform = row.classList.contains('hidden') ? '' : 'rotate(180deg)';
            }
        });
    });
});