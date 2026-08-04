// Bloom & Petal — main.js

document.addEventListener('DOMContentLoaded', function () {
    // Mobile nav toggle
    const navToggle = document.getElementById('navToggle');
    const mainNav = document.getElementById('mainNav');
    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function () {
            mainNav.classList.toggle('open');
        });
    }

    // Quantity stepper buttons (data-step="-1" or "1", data-target="inputId")
    document.querySelectorAll('[data-qty-step]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;
            const step = parseInt(btn.getAttribute('data-qty-step'), 10);
            const min = parseInt(input.getAttribute('min') || '1', 10);
            const max = parseInt(input.getAttribute('max') || '999', 10);
            let value = parseInt(input.value || '1', 10) + step;
            if (value < min) value = min;
            if (value > max) value = max;
            input.value = value;
        });
    });

    // Confirm before destructive admin actions
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = el.getAttribute('data-confirm') || 'Are you sure?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });
});
