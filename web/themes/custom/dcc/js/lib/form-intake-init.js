import stripURLEncodedHTML from './strip-html';
import { unixTimestamp } from './dates';


const $ = jQuery;

function formIntakeInit(formEl, redirectPath = "/thank-you/") {

    // set input value for tracked_landing_timestamp
    $(formEl).find('input[name="tracked_landing_timestamp"]').val(unixTimestamp());

    // set input value for tracked_landing_page
    const trackedLandingPage = sessionStorage.getItem('entrypoint')
      ? sessionStorage.getItem('entrypoint')
      : '';
    $(formEl).find('input[name="tracked_landing_page"]').val(trackedLandingPage);

    // set input value for conversion_page
    $(formEl).find('input[name="conversion_page"]').val(window.location.href);

    // set input value for ga_client_id
    const gaClientId = document.cookie.match(/_ga=(.+?);/)
        ? document.cookie.match(/_ga=(.+?);/)[1].split('.').slice(-2).join(".")
        : ''
    $(formEl).find('input[name="ga_client_id"]').val(gaClientId);

    // post email signup form to zapier without returning JSON in window
    $(formEl).submit(function (e) {
        e.preventDefault();

        const form = $(this);
        const data = stripURLEncodedHTML(form.serialize());
        const url = form.attr("action");

        console.log('form', form.serialize());
        console.log('serlializd form', data);

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            async: true,
            cache: false,
            success: function (data) {
                window.location.pathname = redirectPath;
            }
        });
    });
}

export default formIntakeInit;
