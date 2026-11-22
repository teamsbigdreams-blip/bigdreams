import 'bootstrap';
import axios from 'axios';
import Alpine from 'alpinejs';
import { Calendar } from 'vanilla-calendar-pro';
import 'vanilla-calendar-pro/styles/index.css';
import dataTable from './components/data-table';
import toastManager from './components/toast-manager';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

window.Alpine = Alpine;

Alpine.data('dataTable', dataTable);
Alpine.data('toastManager', toastManager);

function syncDateInput(input, value) {
    if (!input) return;
    input.value = value;
    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.dispatchEvent(new Event('change', { bubbles: true }));
}

document.addEventListener('DOMContentLoaded', () => {
    const calendarConfig = (input) => ({
        inputMode: true,
        dateFormat: 'YYYY-MM-DD',
        onClickDate(self) {
            const selectedDate = self.context.selectedDates[0] ?? '';
            syncDateInput(input, selectedDate);
        },
    });

    const startInput = document.getElementById('start_date');
    if (startInput) {
        new Calendar(startInput, calendarConfig(startInput)).init();
    }

    const endInput = document.getElementById('end_date');
    if (endInput) {
        new Calendar(endInput, calendarConfig(endInput)).init();
    }
});

Alpine.start();