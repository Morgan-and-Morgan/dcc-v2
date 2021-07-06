const $ = jQuery;

$(document).ready(function ($) {
  $('.we-mega-menu-li[data-level="0"] a').click(function (event) {
    event.preventDefault();
    window.location = "http://www.google.com/";
  });
});
