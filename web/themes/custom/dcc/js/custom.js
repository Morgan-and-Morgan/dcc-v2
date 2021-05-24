const $ = jQuery;

$(document).ready(function ($) {
 //  //Livechat
 //  if (isWeekday() && isChatHours()) {
 //    /* show livechat */
 //    function livechat(){
 //      window.__lc = window.__lc || {};
 //      window.__lc.license = 9826220;
 //
 //      const tag = document.createElement('script');
 //      tag.type = 'text/javascript';
 //      tag.async = true;
 //      tag.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
 //
 //      const s = document.getElementsByTagName('script')[0];
 //      s.parentNode.insertBefore(tag, s);
 //    }
 //    livechat();
 //  }
 //
 //  $('.slider-for').slick({
 //    slidesToShow: 1,
 //    slidesToScroll: 1,
 //    arrows: true,
 //    prevArrow: '<i class="slick-prev fas fa-angle-left"></i>',
 //    nextArrow: '<i class="slick-next fas fa-angle-right"></i>',
 //    fade: false,
 //    asNavFor: '.slider-nav'
 //  });
 //  $('.slider-nav').slick({
 //    slidesToShow: 4,
 //    slidesToScroll: 1,
 //    vertical:true,
 //    asNavFor: '.slider-for',
 //    dots: false,
 //    arrows: false,
 //    focusOnSelect: true,
 //    verticalSwiping:true,
 //    responsive: [
 //      {
 //        breakpoint: 992,
 //        settings: {
 //          vertical: true,
 //        }
 //      },
 //      {
 //        breakpoint: 768,
 //        settings: {
 //          vertical: true,
 //        }
 //      },
 //      {
 //        breakpoint: 640,
 //        settings: {
 //          vertical: false,
 //          slidesToShow: 3,
 //        }
 //      }
 //    ]
 //  });
 //
 //  $('.navbar-toggle').on("click", function (e) {
 //    e.preventDefault();
 //    $('.side-menu').toggleClass('hidden open')
 //  });
 //  $('.close-slideout').on("click", function (e) {
 //    e.preventDefault();
 //    $('.side-menu').toggleClass('hidden open')
 //  });
 //
 //  $('.side-menu .ubermenu-item-level-0.ubermenu-item-has-children').on("click", function (e) {
 //    e.preventDefault();
 //    $(this).find('.ubermenu-submenu').toggle();
 //    // $('ul').not($(this).children(".ubermenu-submenu")).hide();
 //  });
 //
 //  let closeButton = $('.close-ubermenu-retractor');
 //  closeButton.on("click", function () {
 //    // $(this).parent('.ubermenu-submenu').fadeOut();
 //    $(this).parent("ul.ubermenu-submenu").toggleClass("hidden");
 //  });
 //
 //  //Homepage stats
 //  $('.stats-number > span').each(function () {
 //    $(this).prop('Counter',0).animate({
 //      Counter: $(this).text()
 //    }, {
 //      duration: 2000,
 //      easing: 'swing',
 //      step: function (now) {
 //        $(this).text(Math.ceil(now));
 //      }
 //    });
 //  });
 //
 //  // Product metadata show more start.
 //  ( function ( glob, $, undefined ) {
 //    $.excerpt = function ( $el, limit, ellipsis, more ) {
 //      if( $el.length == 0 ) { return;
 //      }
 //      ellipsis = ellipsis || '[...]';
 //      more = more || 'show more';
 //      limit = limit || null;
 //      $el.each( function () {
 //        var text = $( this ).text();
 //        if(( text.length ) > limit ) {
 //          var first = text.substring( 0, limit );
 //          var second = text.substring( limit ,text.length );
 //          $( this ).html( first + '<i class="ellipsis">' + ellipsis + '</i><i class="show-more">' + more + '</i><span class="hide">' + second + '</span>' );
 //        }
 //      });
 //    };
 //  })( window, jQuery );
 //
 //  $.excerpt( $( '#for_conditions span' ), 240, '', '' );
 //  $.excerpt( $( '#ingredients span' ), 240, '', '' );
 //  $.excerpt( $( '#side_effects span' ), 240, '', '' );
 //  $.excerpt( $( '#contraindications span' ), 240, '', '' );
 //  $.excerpt( $( '#drug_description span' ), 240, '', '' );
 //
 //  $( '.show-more' ).click( function () {
 //    $( this ).parent().find( '.hide' ).removeClass();
 //    $( this ).parent().find( '.ellipsis' ).remove();
 //    $( this ).parent().find( '.show-more' ).remove();
 //  });
 //  // Product metadata show more end.
 //
 //  equalheight = function ( container ) {
 //
 //    var currentTallest = 0,
 //      currentRowStart = 0,
 //      rowDivs = new Array(),
 //      $el,
 //      topPosition = 0;
 //
 //    $('body').on('click', '.card-link', function () {
 //      var url = $(this).data('url');
 //      if ( url ) {
 //        window.location.href = url;
 //      }
 //    });
 //
 //    $( container ).each( function () {
 //      $el = $( this );
 //      $( $el ).height( 'auto' )
 //      topPostion = $el.position().top;
 //
 //      if ( currentRowStart != topPostion ) {
 //        for ( currentDiv = 0; currentDiv < rowDivs.length; currentDiv++ ) {
 //          rowDivs[currentDiv].height( currentTallest );
 //        }
 //        rowDivs.length = 0; // empty the array
 //        currentRowStart = topPostion;
 //        currentTallest = $el.height();
 //        rowDivs.push( $el );
 //      } else {
 //        rowDivs.push( $el );
 //        currentTallest = ( currentTallest < $el.height() ) ? ( $el.height() ) : ( currentTallest );
 //      }
 //      for ( currentDiv = 0; currentDiv < rowDivs.length; currentDiv++ ) {
 //        rowDivs[currentDiv].height( currentTallest );
 //      }
 //    });
 //  }
 //
 // // Back to top start.
 //  var offset = 220;
 //  var duration = 500;
 //  $(window).scroll(function () {
 //    if ($(this).scrollTop() > offset) {
 //      $('.back-to-top').fadeIn(duration);
 //    } else {
 //      $('.back-to-top').fadeOut(duration);
 //    }
 //  });
 //
 //  $('.back-to-top').click(function (event) {
 //    event.preventDefault();
 //    $('html, body').animate({scrollTop: 0}, duration);
 //    return false;
 //  });
 //  // Back to top end.
 //
 //  // Side popup start.
 //  var height = $( document ).height();
 //  var one_third = parseFloat( height / 3 );
 //  var two_thirds = parseFloat( one_third * 2 );
 //  var half_page = parseFloat( height / 2 );
 //  var one_forth = parseFloat( height / 4 );
 //  var one_fifth = parseFloat( height / 5 );
 //  var one_tenth = parseFloat( height / 10 );
 //  $( '.hide-side-popup' ).click( function (e) {
 //    $( '#side_popup' ).removeClass( 'is-active' );
 //    $( '#side_popup' ).addClass( 'closed' );
 //    e.preventDefault();
 //  });
 //  $( '.hide-mobile-popup' ).click( function (e) {
 //    $( '#mobile_bottom_popup' ).removeClass( 'is-active' );
 //    $( '#mobile_bottom_popup' ).addClass( 'closed' );
 //    e.preventDefault();
 //  });
 //  $( window ).scroll( function () {
 //    if( $( window ).scrollTop() > one_tenth ) {
 //      if( !$( '#side_popup' ).hasClass( 'closed' ) ) {
 //        $( '#side_popup' ).addClass( 'is-active' );
 //      }
 //    }
 //    if( $( window ).scrollTop() < one_tenth ) {
 //      $( '#side_popup' ).removeClass( 'is-active' );
 //    }
 //    if( $( window ).scrollTop() > one_fifth ) {
 //      if( !$( '#mobile_bottom_popup' ).hasClass( 'closed' ) ) {
 //        $( '#mobile_bottom_popup' ).addClass( 'is-active' );
 //      }
 //    }
 //  });
 //  // Show/Hide mobile/desktop modal
 //  if ($(window).width() < 992) {
 //    $('#side_popup').hide();
 //  }
 //  else {
 //    $('#mobile_bottom_popup').hide();
 //  }
 //  // Side popup end.
});

// helpers
// function isWeekday(d) {
//   const date = d || new Date();
//   const dayOfWeek = date.getDay();
//
//   if (dayOfWeek >= 1 && dayOfWeek <= 5) {
//     return true;
//   } else {
//     return false;
//   }
// }
//
// function isChatHours(d) {
//   const date = d || new Date();
//   const hourOfDay = date.getHours();
//   const chatHours = {
//     start: 8,
//     end: 21
//   };
//
//   return hourOfDay >= chatHours.start && hourOfDay < chatHours.end;
// }
