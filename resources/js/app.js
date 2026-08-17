import './bootstrap';

const skipTypes = new Set([
    'password',
    'hidden',
    'checkbox',
    'radio',
    'file',
    'date',
    'time',
    'datetime-local',
    'number',
    'range',
    'color',
    'email',
]);

const skipNames = new Set([
    'password',
    'access_password',
    'current_password',
    'password_confirmation',
    'login',
    'email',
    '_token',
    '_method',
]);

const toUpper = value => String(value).toLocaleUpperCase('fr-FR');

const shouldUppercaseField = element => {
    if (!(element instanceof HTMLInputElement) && !(element instanceof HTMLTextAreaElement)) {
        return false;
    }

    if (element.classList.contains('ep-keep-case') || element.hasAttribute('data-keep-case')) {
        return false;
    }

    const type = (element.type || '').toLowerCase();
    if (skipTypes.has(type)) {
        return false;
    }

    const name = (element.name || '').toLowerCase();
    if (skipNames.has(name) || name.endsWith('_id')) {
        return false;
    }

    return true;
};

const applyUppercase = element => {
    if (!shouldUppercaseField(element)) {
        return;
    }

    const next = toUpper(element.value);
    if (next === element.value) {
        return;
    }

    const start = element.selectionStart;
    const end = element.selectionEnd;
    element.value = next;

    if (document.activeElement === element && typeof start === 'number' && typeof end === 'number') {
        element.setSelectionRange(start, end);
    }
};

document.addEventListener('input', event => {
    applyUppercase(event.target);
}, true);

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input, textarea').forEach(applyUppercase);
});
