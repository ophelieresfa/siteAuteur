
document.addEventListener('DOMContentLoaded', function () {

    const submenuToggles = document.querySelectorAll('.sa-account-submenu-toggle');

    submenuToggles.forEach(function (button) {
        button.addEventListener('click', function () {
            const parent = button.closest('.sa-account-menu-group');
            if (!parent) return;

            const isOpen = parent.classList.contains('is-open');

            parent.classList.toggle('is-open', !isOpen);
            button.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
        });
    });

    const toggles = document.querySelectorAll('.sa-password-toggle');

    toggles.forEach(function (button) {
        button.addEventListener('click', function () {
            const wrap = button.closest('.sa-password-input-wrap');
            if (!wrap) return;

            const input = wrap.querySelector('input');
            if (!input) return;

            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');

            button.classList.toggle('is-visible', isPassword);
        });
    });

    const passwordInput = document.getElementById('sa_new_password');
    const strengthText = document.getElementById('sa-password-strength-text');
    const strengthBars = document.querySelectorAll('.sa-strength-bars [data-bar]');
    const rulesBox = document.getElementById('sa-password-rules');

    if (!passwordInput || !strengthText || !strengthBars.length || !rulesBox) {
        return;
    }

    const ruleLength = rulesBox.querySelector('[data-rule="length"]');
    const ruleUppercase = rulesBox.querySelector('[data-rule="uppercase"]');
    const ruleDigit = rulesBox.querySelector('[data-rule="digit"]');
    const ruleSpecial = rulesBox.querySelector('[data-rule="special"]');

    function hasUppercase(value) {
        return /[A-Z]/.test(value);
    }

    function hasDigit(value) {
        return /\d/.test(value);
    }

    function hasSpecial(value) {
        return /[^A-Za-z0-9]/.test(value);
    }

    function setRuleState(element, valid) {
        if (!element) return;
        element.classList.toggle('is-valid', valid);
    }

    function updateStrength() {
        const value = passwordInput.value || '';

        const checks = {
            length: value.length >= 8,
            uppercase: hasUppercase(value),
            digit: hasDigit(value),
            special: hasSpecial(value)
        };

        setRuleState(ruleLength, checks.length);
        setRuleState(ruleUppercase, checks.uppercase);
        setRuleState(ruleDigit, checks.digit);
        setRuleState(ruleSpecial, checks.special);

        let score = 0;

        if (value.length > 0) score += 1;
        if (checks.length) score += 1;
        if (checks.uppercase) score += 1;
        if (checks.digit) score += 1;
        if (checks.special) score += 1;
        if (value.length >= 12) score += 1;

        let label = 'Très faible';
        let level = 0;

        if (value.length === 0) {
            label = 'Très faible';
            level = 0;
        } else if (score <= 2) {
            label = 'Faible';
            level = 1;
        } else if (score <= 4) {
            label = 'Moyenne';
            level = 2;
        } else if (score === 5) {
            label = 'Bonne';
            level = 3;
        } else {
            label = 'Forte';
            level = 4;
        }

        strengthText.textContent = label;
        strengthText.className = '';
        strengthText.classList.add('is-level-' + level);

        strengthBars.forEach(function (bar, index) {
            bar.classList.remove('is-active', 'is-level-1', 'is-level-2', 'is-level-3', 'is-level-4');

            if (index < level) {
                bar.classList.add('is-active', 'is-level-' + level);
            }
        });
    }

    passwordInput.addEventListener('input', updateStrength);
    updateStrength();
});