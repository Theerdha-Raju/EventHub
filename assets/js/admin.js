/**
 * EventHub - Admin Dashboard JavaScript
 * Handles registration search filter, detail modal, and delete confirmations
 */

document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // 1. Live Filter for Admin Registrations Table
    // -------------------------------------------------------------
    const adminSearchInput = document.getElementById('adminTableSearch');
    const tableBody = document.getElementById('registrationsTableBody');

    if (adminSearchInput && tableBody) {
        adminSearchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('tr.registration-row');
            let matchCount = 0;

            rows.forEach(function (row) {
                const text = row.innerText.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                    matchCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const noMatchRow = document.getElementById('noTableMatchesRow');
            if (noMatchRow) {
                noMatchRow.style.display = (matchCount === 0 && rows.length > 0) ? '' : 'none';
            }
        });
    }

    // -------------------------------------------------------------
    // 2. Registration Detail View Modal
    // -------------------------------------------------------------
    const modalOverlay = document.getElementById('viewModalOverlay');
    const modalCloseButtons = document.querySelectorAll('.modal-close-trigger');

    function openModal(data) {
        if (!modalOverlay) return;

        document.getElementById('modalRegId').textContent = '#' + String(data.id).padStart(5, '0');
        document.getElementById('modalName').textContent = data.name || '-';
        document.getElementById('modalEmail').textContent = data.email || '-';
        document.getElementById('modalEventTitle').textContent = data.event || '-';
        document.getElementById('modalDateRegistered').textContent = data.date || '-';
        document.getElementById('modalEventDate').textContent = data.eventDate || '-';
        document.getElementById('modalEventLocation').textContent = data.eventLocation || '-';

        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // prevent background scrolling
    }

    function closeModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Attach click listeners to "View" buttons
    document.querySelectorAll('.btn-view-reg').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const data = {
                id: this.getAttribute('data-id'),
                name: this.getAttribute('data-name'),
                email: this.getAttribute('data-email'),
                event: this.getAttribute('data-event'),
                date: this.getAttribute('data-date'),
                eventDate: this.getAttribute('data-event-date'),
                eventLocation: this.getAttribute('data-event-location')
            };
            openModal(data);
        });
    });

    // Close modal triggers
    modalCloseButtons.forEach(function (btn) {
        btn.addEventListener('click', closeModal);
    });

    // Close on overlay click outside dialog
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function (e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('active')) {
            closeModal();
        }
    });

    // -------------------------------------------------------------
    // 3. Delete Confirmation
    // -------------------------------------------------------------
    document.querySelectorAll('.form-delete-reg').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const name = this.getAttribute('data-name') || 'this registration';
            const id = this.getAttribute('data-id') || '';
            const confirmMsg = `Are you sure you want to delete registration #${id} for "${name}"?\n\nThis action cannot be undone.`;
            
            if (!confirm(confirmMsg)) {
                e.preventDefault();
                return false;
            }
        });
    });
});
