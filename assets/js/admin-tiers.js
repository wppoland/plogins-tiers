/**
 * Tiers – Admin tier builder: add / remove tier rows dynamically.
 *
 * Vanilla JS, no dependencies.
 */
(function () {
    'use strict';

    const OPTION = 'tiers_settings';

    function getRowCount() {
        return document.querySelectorAll('#tiers-rows tr').length;
    }

    function createRow(index) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="number" name="${OPTION}[tiers][${index}][min_qty]"
                       value="" min="1" step="1" class="small-text" required />
            </td>
            <td>
                <input type="number" name="${OPTION}[tiers][${index}][discount_percent]"
                       value="" min="0.01" max="100" step="0.01" class="small-text" required />
            </td>
            <td>
                <input type="text" name="${OPTION}[tiers][${index}][label]"
                       value="" class="regular-text" />
            </td>
            <td>
                <button type="button" class="button tiers-remove-row">Remove</button>
            </td>
        `;
        return tr;
    }

    function reindexRows() {
        document.querySelectorAll('#tiers-rows tr').forEach((tr, idx) => {
            tr.querySelectorAll('input').forEach((input) => {
                input.name = input.name.replace(/\[tiers\]\[\d+\]/, `[tiers][${idx}]`);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const addBtn = document.getElementById('tiers-add-row');
        const tbody  = document.getElementById('tiers-rows');

        if (!addBtn || !tbody) {
            return;
        }

        addBtn.addEventListener('click', () => {
            const index = getRowCount();
            tbody.appendChild(createRow(index));
        });

        tbody.addEventListener('click', (e) => {
            if (!e.target || !e.target.classList.contains('tiers-remove-row')) {
                return;
            }
            const row = e.target.closest('tr');
            if (row) {
                row.remove();
                reindexRows();
            }
        });
    });
}());
