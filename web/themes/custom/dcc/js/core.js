import { createForm } from '@morgan-morgan/core';

require("./components/form-intake-registration");

function currentYear() {
    const date = new Date();
    return date.getFullYear();
}

console.log(` * * ${window.MM.forms.length} forms * * `);
window.MM.forms.forEach((f) => {
    createForm(f.target, { formId: f.formId });
});
