/**
 * EventHub - Main JavaScript
 * Handles client-side validation, live search, and UI interactions
 */

document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // 1. Live Search for Events on Homepage
    // -------------------------------------------------------------
    const eventSearchInput = document.getElementById('eventSearch');
    if (eventSearchInput) {
        eventSearchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const eventCards = document.querySelectorAll('.event-card');
            let visibleCount = 0;

            eventCards.forEach(function (card) {
                const title = card.getAttribute('data-title')?.toLowerCase() || '';
                const category = card.getAttribute('data-category')?.toLowerCase() || '';
                const location = card.getAttribute('data-location')?.toLowerCase() || '';
                const desc = card.querySelector('.event-desc')?.textContent.toLowerCase() || '';

                if (title.includes(query) || category.includes(query) || location.includes(query) || desc.includes(query)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Toggle empty search results message
            const emptyNotice = document.getElementById('searchEmptyNotice');
            if (emptyNotice) {
                emptyNotice.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        });
    }

    // -------------------------------------------------------------
    // 2. Client-Side Form Validation for Registration Form
    // -------------------------------------------------------------
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const eventSelect = document.getElementById('event_id');
        const submitBtn = registrationForm.querySelector('button[type="submit"]');

        // Regex for RFC-compliant email validation
        const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*\.[a-zA-Z]{2,}$/;

        function validateName() {
            if (!nameInput) return true;
            const val = nameInput.value.trim();
            const feedback = nameInput.parentElement.querySelector('.invalid-feedback');

            if (val === '') {
                nameInput.classList.add('is-invalid');
                nameInput.classList.remove('is-valid');
                if (feedback) feedback.textContent = 'Full name is required.';
                return false;
            } else if (val.length < 2) {
                nameInput.classList.add('is-invalid');
                nameInput.classList.remove('is-valid');
                if (feedback) feedback.textContent = 'Name must be at least 2 characters long.';
                return false;
            } else {
                nameInput.classList.remove('is-invalid');
                nameInput.classList.add('is-valid');
                return true;
            }
        }

        function validateEmail() {
            if (!emailInput) return true;
            const val = emailInput.value.trim();
            const feedback = emailInput.parentElement.querySelector('.invalid-feedback');

            if (val === '') {
                emailInput.classList.add('is-invalid');
                emailInput.classList.remove('is-valid');
                if (feedback) feedback.textContent = 'Email address is required.';
                return false;
            } else if (!emailRegex.test(val)) {
                emailInput.classList.add('is-invalid');
                emailInput.classList.remove('is-valid');
                if (feedback) feedback.textContent = 'Please enter a valid email address (e.g., name@example.com).';
                return false;
            } else {
                emailInput.classList.remove('is-invalid');
                emailInput.classList.add('is-valid');
                return true;
            }
        }

        function validateEvent() {
            if (!eventSelect) return true;
            const val = eventSelect.value.trim();
            const feedback = eventSelect.parentElement.querySelector('.invalid-feedback');

            if (val === '' || val === '0') {
                eventSelect.classList.add('is-invalid');
                eventSelect.classList.remove('is-valid');
                if (feedback) feedback.textContent = 'Please select an event to attend.';
                return false;
            } else {
                eventSelect.classList.remove('is-invalid');
                eventSelect.classList.add('is-valid');
                return true;
            }
        }

        // Live validation listeners
        if (nameInput) {
            nameInput.addEventListener('input', validateName);
            nameInput.addEventListener('blur', validateName);
        }

        if (emailInput) {
            emailInput.addEventListener('input', validateEmail);
            emailInput.addEventListener('blur', validateEmail);
        }

        if (eventSelect) {
            eventSelect.addEventListener('change', validateEvent);
        }

        // Form Submit Handler
        registrationForm.addEventListener('submit', function (e) {
            const isNameValid = validateName();
            const isEmailValid = validateEmail();
            const isEventValid = validateEvent();

            if (!isNameValid || !isEmailValid || !isEventValid) {
                e.preventDefault();
                // Focus first invalid element
                const firstInvalid = registrationForm.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.focus();
                return false;
            }

            // Show loading state on submit button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Submitting Registration...';
            }
        });
    }
});
