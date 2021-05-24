import formIntakeInit from '../lib/form-intake-init';


const $ = jQuery;

const intakeFormQueue = [
    { selector: '#intake-form' }
];

$(document).ready(function () {
    intakeFormQueue.forEach(el => {
        if ($(el.selector).length) {
            formIntakeInit(el.selector, el.redirectPath);
            console.log(`/* Form: ${el.selector} has been registered. */`)
        }
    })
});
