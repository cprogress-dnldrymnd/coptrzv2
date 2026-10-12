/**
 * Search modal helpers: post-type select ↔ radio sync, and focus the search
 * input when Bootstrap finishes opening the modal. Open/close, Escape,
 * backdrop, focus trap, and body scroll are handled by Bootstrap 5.
 *
 * @package DigitallyDisruptive
 */
(function () {
    'use strict';

    const MODAL_ID = 'coptrz-search-modal';

    /**
     * Sync select value onto matching radio (and vice versa) within a form.
     * On submit, omit an empty post_type so WP searches all content types.
     *
     * @param {HTMLElement} form
     */
    function bindTypeFilters(form) {
        if (!form || form.dataset.coptrzFiltersBound === '1') {
            return;
        }
        form.dataset.coptrzFiltersBound = '1';

        const select = form.querySelector('[data-coptrz-search-type-select]');
        const radios = form.querySelectorAll('[data-coptrz-search-type-radio]');

        if (select) {
            select.addEventListener('change', function () {
                const value = select.value;
                radios.forEach(function (radio) {
                    radio.checked = radio.value === value;
                });
            });
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (!select || !radio.checked) {
                    return;
                }
                select.value = radio.value;
            });
        });

        form.addEventListener('submit', function () {
            const checked = form.querySelector('[data-coptrz-search-type-radio]:checked');
            if (checked && checked.value === '') {
                radios.forEach(function (radio) {
                    radio.disabled = true;
                });
            }
        });
    }

    /**
     * Focus the search input after Bootstrap's open animation completes.
     *
     * @param {Event} event
     */
    function onModalShown(event) {
        if (!event.target || event.target.id !== MODAL_ID) {
            return;
        }

        const form = event.target.querySelector('form');
        if (form) {
            bindTypeFilters(form);
        }

        const input = event.target.querySelector('#coptrz-search-modal-input, input[name="s"]');
        if (!input) {
            return;
        }

        input.focus();
        if (typeof input.select === 'function') {
            input.select();
        }
    }

    function initResultsFilters() {
        document.querySelectorAll('.coptrz-search-form').forEach(bindTypeFilters);
    }

    document.addEventListener('shown.bs.modal', onModalShown);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initResultsFilters);
    } else {
        initResultsFilters();
    }
})();
