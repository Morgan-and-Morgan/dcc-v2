const $ = jQuery;

$(document).ready(function ($) {
  $('.block-we-megamenu-blockheader-navigation .region-we-mega-menu nav .we-mega-menu-ul li[data-level="0"]>a.we-mega-menu-li').click(function (event) {
    $(this).once().trigger("click");
  });
});
