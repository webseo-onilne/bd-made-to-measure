
app

  .controller("securityShuttersCtrl", function($scope, $http, DataLoader) {
    console.log('Security Shutters', $scope);
  })


/**
* Core Funtionality
**/

jQuery( document ).ready(function() {

  var $ = jQuery;

  $.each($('input[type="radio"]'), function() {
    $(this).attr('checked', false);
  });


  $( document ).on('click', '#toStep3', function() {
    $('.stepTwo .step-inner').slideUp();
      $('html,body').animate({
      scrollTop: $(".stepTwo").offset().top },
      'slow');
  });


  $( document ).on('click', '.step-2', function() {
    $( this ).next('.step-inner').slideToggle();
    $('#toStep2').trigger('click');
  });



  // Add class to width inputs
  $('.product-addon-add-your-measurements input:lt(3)').addClass('shutter-width');
  // Add class to drop inputs
  $('.product-addon-add-your-measurements input').slice( -3 ).addClass('shutter-drop');
  // Hide the divider options
  $('.product-addon-divider-options').hide();
  // Add to cart trigger
  $('.purchase-summary').on('click', '.single_add_to_cart_button', function() {
    $('.variations_button .single_add_to_cart_button').trigger('click');
    return false;
  });

  $('.purchase-summary--total').append('<input class="hidden-price" type="hidden" />');

  $( document ).off('click', '#toStep2');
  // When proceeding to the 2nd step, calculate the price
  $( document ).on('click', '#toStep2', function() {
    // show the loader
    $('.purchase-summary--total .alignright').html('<img src="/custom/price/ajax-loader.gif" />');
    // clear values if they exists
    $.each( $('.hidden-panels'), function() {
        $( this ).remove();
    });

    // Get the lowest width from input
    var minWidth = minimum_width();

    // Get the lowest drop from array
    var minDrop  = minimum_height();

    // Add to TBP y input
    $('#wpti-product-y').val( minDrop );

    var maximumPanels  = Math.floor( minWidth / 250 );
    var actualMinWidth = Math.floor( minWidth / maximumPanels ); 

    // Add to TBP x input
    $('#wpti-product-x').val( actualMinWidth );

    // The minimum width a panel can be
    var minPanelWi  = 250;

    // Minimum amount of panels that SHOULD be used to show price
    var threshold   = 2;

    // Check how many dividers should be selected based on drop
    choose_divider();


    // Select the correct amount of panels based on width
    $.each( $('.product-addon-number-of-panels .addon-radio'), function() {
        var panelsqty = $( this ).val();
        var newWidth = Math.floor( minimum_width() / panelsqty );
        var data = {
          'action'    : 'bd_do_shutters_price_calcuation_ajax',
          'min_width' : newWidth,
          'min_drop'  : minimum_height(),
          'term_id'   : '1282'
        };

        $.get( '/wp-admin/admin-ajax.php', data, function( r ) {
            var p = r.price * panelsqty;
            if ( ( newWidth >= minPanelWi ) && ( newWidth < 600 ) ) {
                $('body').append('<input class="hidden-panels" type="hidden" value="'+ panelsqty +'">');
            };

        }, 'json');


      $( this ).on('click', function( e ) {
          e.stopImmediatePropagation(); 
          $('.purchase-summary--total .alignright').html('<img src="/custom/price/ajax-loader.gif" />');
          var panelsqty = $( this ).val();
          var newWidth = Math.floor( minimum_width() / panelsqty );
          var installation = panelsqty * 513;
          if ( ( newWidth >= minPanelWi ) && ( newWidth < 600 ) ) {
              get_shutter_price( newWidth, minimum_height(), panelsqty );
              $('#panel-img .show-panels').removeClass('dimError');
          } else {
              $('.purchase-summary--total .alignright').html('<p style="font-size:25px" class="errorDim">Dimensions not feasable</p>');
              $('#panel-img .show-panels').addClass('dimError');
          };
      });

    });

    d = [];
    setTimeout(function() {
        $.each( $('.hidden-panels'), function() {
            q = $( this ).val();
            d.push( q );
        });

        $.each($('.product-addon-number-of-panels .addon-radio'), function() {
            if ( $( this ).val() == Math.min.apply( Math, d ) ) {
                $( this ).closest('span').removeClass('checked').closest('.radio').removeClass('focus');
                $( this ).closest('label').trigger('click');
                $( this ).closest('span').addClass('checked').closest('.radio').addClass('focus');
                if ( $(this).val() != 2 ) {
                    $(this).closest('.product-addon-number-of-panels').find('.radio span:lt(1)')
                      .removeClass('checked');  
                };

                var panelsqty = $( this ).val();
                var newWidth = Math.floor( minimum_width() / panelsqty );
                if ( ( newWidth >= minPanelWi ) && ( newWidth < 600 ) ) {
                    get_shutter_price( newWidth, minimum_height(), panelsqty );
                } else {
                    $('.purchase-summary--total .alignright').html('<p style="font-size:25px">Dimensions not feasable</p>');
                };

            };

        });

    }, 2000 );

    // Open the price summary
    $('.purchase-summary--details').slideDown();
  });

    $.each($('.product-addon-do-you-want-professional-installation .addon-radio'), function() {
        $( this ).on('click', function() {
            if ( $( this ).val() == 'yes' ) {
                var cp = parseFloat( $('.hidden-price').val() );
                var fp = ( cp + 513 * parseInt($('.product-addon-number-of-panels').find('input:checked').val()) );
                $('.purchase-summary--total .alignright').empty().html("R " + fp);
            };

            if ( $( this ).val() == 'no' ) {
                $('.purchase-summary--total .alignright').empty().html("R " + $('.hidden-price').val());
            };
        });

    });



    // Calculates where the dividers should be
    function dividers( height ) {
        if ( ( height >= 900 ) && ( height < 2500 )  ) {
            return 1;
        } 
        else if ( height > 2500 ) {
            return 2;
        }
    }

    // Check how many dividers should be selected based on drop
    function choose_divider() {

        switch ( dividers( minimum_height() ) ) {
            case 1:
                $('.product-addon-divider-options').fadeIn( 300 );
                break;
            case 2:
                $('.product-addon-divider-options').fadeIn( 300 );
                $('.product-addon-divider-options .addon-radio').slice( -1 )
                    .addClass('checked').closest('.product-addon-divider-options')
                    .find('.addon-radio:lt(1)').removeClass('checked');
                break;
        } 
    }

    function get_shutter_price( width, drop, panelQty ) {

        var data = {
          'action'  : 'bd_do_shutters_price_calcuation_ajax',
          'min_width' : width,
          'min_drop'  : drop,
          'term_id'   : '1282'
        };

        $.get( '/wp-admin/admin-ajax.php', data, function( response ) {

          var p = shutter_tax( panelQty * response.price );
          var hp = shutter_tax( panelQty * response.price );

          if ( needs_installtion() ) {
            var installAmnt = panelQty * 513;
            var p = shutter_tax( panelQty * response.price ) + installAmnt;
          };

          $('#wpti-product-x').val( width );
          if (isNaN(p)) {
            $('.purchase-summary--total .alignright').html('<p style="font-size:25px">Dimensions not feasable</p>');
          } else {
            $('.purchase-summary--total .alignright').empty().html("R " + p.toFixed( 2 ) );
          }
          $('.hidden-price').val(hp.toFixed(2));

        }, 'json' );

    }


    function shutter_tax( price ) {
      return price * 1.14;
    }

    function minimum_width() {
        // Add all width values to an array
        var widthVal = $('.shutter-width').map(function() {
            return $( this ).val();
        }).get();
        // Return lowest width from array
        return Math.min.apply( Math, widthVal );
    }

    function minimum_height() {
        // Add all drop values to an array
        var dropVal = $('.shutter-drop').map(function() {
            return $( this ).val();
        }).get();
        // Get the lowest drop from array
        return Math.min.apply( Math, dropVal );
    }

    function panel_calculation() {
        d = [];
        $.each( $('.hidden-panels'), function() {
            q = $( this ).val();
            d.push( q );
        });
        return Math.min.apply( Math, d );
    }

    function needs_installtion() {
      return true;
    }

});


/**
* End Core Functionality
**/


/**
* OTHER
**/

jQuery( document ).ready(function() {
  jQuery('#footer-wrap').insertAfter('#container #container');

  // Order Product Fields into Required Steps
  var stepOne = '<div class="stepOne step-container"><h3 class="step-1 step"><i class="fa fa-caret-right"></i><span>Step <strong>1</strong> </span>Choose your Dimensions <abbr class="required" data-tooltip-right="Required Step" oldtitle="required" title="">*</abbr></h3><div class="step-inner"></div></div>',
    stepTwo = '<div class="stepTwo step-container"><h3 class="step-2 step"><i class="fa fa-caret-right"></i><span>Step <strong>2</strong> </span>Choose your Panels <abbr class="required" data-tooltip-right="Required Step" data-hasqtip="3465" oldtitle="required" title="">*</abbr></h3><div class="step-inner"></div></div>',
    stepThree = '<div class="stepThree step-container"><h3 class="step-3 step"><i class="fa fa-caret-right"></i><span>Step <strong>3</strong> </span>Choose your Colours <abbr class="required" data-tooltip-right="Required Step" data-hasqtip="3465" oldtitle="required" title="">*</abbr></h3><div class="step-inner"></div></div>';

  jQuery('.cart').prepend(stepOne, stepTwo, stepThree);
  jQuery('.cart .product-addon-choose-your-fitting-type, .cart .product-addon-add-your-measurements, .cart .product-addon-is-there-floor-skirting-side-spacers-will-be-provided').appendTo( jQuery('.stepOne .step-inner') );
  jQuery('.product-addon-add-your-measurements .form-row:nth-of-type(3)').after('<div class="measurement-box"><img src="/wp-content/uploads/2015/09/Width-x-Drop-calculator-01.jpg"></div>'); // Append Measurement Box
  jQuery('.product-addon-add-your-measurements').append('<div class="error-box"><span></span></div>'); // Append Error Box

  jQuery(window).bind("load", function() {
    jQuery('.stepOne .step-inner').slideDown(500); // Display the first step
    jQuery('#hero').slideDown(500);
    jQuery('#hero .title').delay(200).css('opacity', 0).slideDown(500)
      .animate(
        { opacity: 1 }
      );

    buttonState(); // If dimensions are valid, enable next step

  });

  jQuery('.product-addon-number-of-panels, .product-addon-divider-options, .product-addon-controller').appendTo( jQuery('.stepTwo .step-inner') );
  jQuery('.product-addon-divider-options').append('<em>Only required if panel is larger than 900mm</em>');
  jQuery('.variations, .product-addon-select-raised-bottom-track-colour, .product-addon-top, .product-addon-bottom, .product-addon-do-you-want-professional-installation, .product-addon-location').appendTo( jQuery('.stepThree .step-inner') );
  jQuery('.product_title').prependTo( jQuery('#hero .title') );

  var twoPanels = '<div class="panels-2"><img src="/wp-content/uploads/2015/09/SHUTTER-PANELS-2-4-6-01.png"></div>',
      fourPanels = '<div class="panels-4 show-panels"><img src="/wp-content/uploads/2015/09/SHUTTER-PANELS-2-4-6-02.png"></div>',
      sixPanels = '<div class="panels-6"><img src="/wp-content/uploads/2015/09/SHUTTER-PANELS-2-4-6-03.png"></div>',
      eightPanels = '<div class="panels-8"><img src="/wp-content/uploads/2015/09/SHUTTER-PANELS-8-10-12-04.png"></div>',
      tenPanels = '<div class="panels-10"><img src="/wp-content/uploads/2015/09/SHUTTER-PANELS-8-10-12-03.png"></div>',
      twelvePanels = '<div class="panels-12"><img src="/wp-content/uploads/2015/09/SHUTTER-PANELS-8-10-12-05.png"></div>',
      allPanels = twoPanels+fourPanels+sixPanels+eightPanels+tenPanels+twelvePanels;

  jQuery('.product-addon-number-of-panels .addon-name').after('<div id="panel-img">'+allPanels+'</div>');
  jQuery('.product-addon-controller .addon-wrap-3783-controller-0').prepend('<div class="with-controller"><img src="/wp-content/uploads/2015/09/with-controller.png"></div>');
  jQuery('.product-addon-controller .addon-wrap-3783-controller-1').prepend('<div class="without-controller"><img src="/wp-content/uploads/2015/09/without-controller.png"></div>');
  jQuery('.product-addon-top').before('<h3 class="addon-name">What Is The Top & Bottom Mounting Surface?</h3>');
  jQuery('.variations').before('<h3 class="addon-name">Select Shutter Colour <abbr class="required" title="required">*</abbr></h3>');
  jQuery('.product-addon-select-raised-bottom-track-colour').before('<br><center><img width="50%" src="/wp-content/uploads/2015/09/track-demo.jpg"></center>');

  var profInst = '<div class="red-alert"><p><i class="fa fa-info"></i> Only available in CPT, JHB and Pretoria.</p><p>Your shipping address should be the same asthe installation address so we can confirm service. <a href="#">See list of areas covered.</a></p></div>';

  jQuery('.product-addon-do-you-want-professional-installation').append(profInst);

  // Next Step Buttons
  var proceedStep2 = '<div class="button alignright next-step disabled" id="toStep2">Next Step</div>',
      proceedStep3 = '<div class="button alignright next-step" id="toStep3">Next Step</div>';

  jQuery('.stepOne .step-inner').append(proceedStep2);
  jQuery('.stepTwo .step-inner').append(proceedStep3);

  jQuery('#toStep2').on('click', function() {
    if ( jQuery(this).hasClass('enabled') ) {
      jQuery('.stepTwo .step-inner').slideDown(500)
    }
  });


  jQuery('#toStep3').on('click', function() {
    jQuery('.stepThree .step-inner').slideDown(500)
  });


  // Add lightbox triggers to swatches
  jQuery( '.swatch-anchor' ).append('<a class="swatch-lightbox" rel="lightbox"><i class="fa fa-search-plus" data-tooltip-right="Get a closer look."></i></a>');
  jQuery('.swatch-anchor').each(function(){
    var src = jQuery(this).children('img').attr('src');
    var title = jQuery(this).attr('title');
    jQuery(this).children('.swatch-lightbox').attr({'href':src, 'title':title});
    jQuery(this).attr('data-tooltip-top', title);
  });

  // Placeholder label for swatches
  jQuery(".swatch-label:contains()").html("Select a colour/swatch");

  // Order Detail Toggle Functions
  jQuery('#toggle-summary').on('click', function() {
    jQuery('.purchase-summary--details').slideToggle();
    jQuery(this).find('.fa').toggleClass('fa-flip-vertical');
    jQuery(this).find('span').toggle();
  });

  // Tips
  jQuery( ".product-addon-choose-your-fitting-type" ).prepend("<div class='tips'></div>");
  jQuery( ".tips" ).append("<a class='button more-info' id='types-of-blinds' data-tooltip-right='Pop-up box with information on Recess(Inside) and Facefix (Outside) fittings'><span class='info icon'><i class='fa fa-question-circle'></i> Different Blind Fitting Types</span></a>");
  jQuery( ".tips" ).append("<a href='/how-to-videos' target='_blank' class='button more-info' data-tooltip-right='Watch our How To Measure Videos. Opens in new window'><span class='info icon'><i class='fa fa-play-circle'></i> how to measure videos</span></a>");

  // Append info icons
  jQuery( ".product-addon-choose-your-fitting-type .form-row label" ).each(function() {
    jQuery(this).append("<span class='info icon'><i class='fa fa-info' data-tooltip-top='More information on this fitting type'></i></span>");
  });

  // Validate dimension fields

  var buttonState = function() {
    var skirting = jQuery('.product-addon-is-there-floor-skirting-side-spacers-will-be-provided .radio span input');
    jQuery.each(skirting, function() {
      jQuery(this).on('click', function() {
        hasSkirting = true;
        buttonState();
      });
    });

    if( jQuery('.product-addon-add-your-measurements .addon.valid').length === 6 ) {
      jQuery('.error-box span').slideUp();
      jQuery('#toStep2').removeClass('disabled');
      jQuery('#toStep2').addClass('enabled');
    } 
    else if ( jQuery('.product-addon-add-your-measurements .addon.error') ) {
      jQuery('#toStep2').addClass('disabled');
      jQuery('#toStep2').removeClass('enabled');
    };
  };
  buttonState();

  var entryVal = jQuery(this).val(),
    dropMin = 700,
    dropMax = 2900,
    widthMin = 500,
    widthMax = 5001;

  jQuery('.addon.shutter-drop').each(function() {
    jQuery(this).on('input', function() {
      if ( jQuery(this).val().length >= 3 ) {
        var shutterDrop = jQuery(this),
            entryLength = jQuery(this).val().length;
        if ( (shutterDrop.val() >= dropMin) && (shutterDrop.val() <= dropMax) ) {
          jQuery(this).addClass('valid').removeClass('empty');
          jQuery('.error-box span').slideUp();
          if ( jQuery(this).hasClass('error') ) {
            jQuery(this).removeClass('error');
          };
        } 
        else if (shutterDrop.val() < dropMin && shutterDrop.val()) {
          jQuery(this).addClass('error').removeClass('empty');
          jQuery('.error-box span').html('Invalid entry, value must be greater than '+dropMin+'<i class="fa fa-exclamation-circle alignright"></i>').slideDown();
          if ( jQuery(this).hasClass('valid') ) {
            jQuery(this).removeClass('valid');
          };
        } 
        else if (shutterDrop.val() > dropMax) {
          jQuery(this).addClass('error').removeClass('empty');
          jQuery('.error-box span').html('Invalid entry, value must be less than '+dropMax+'<i class="fa fa-exclamation-circle alignright"></i>').slideDown();
          if ( jQuery(this).hasClass('valid') ) {
            jQuery(this).removeClass('valid');
          };
        } 
        else if( (typeof shutterDrop.val() != 'number') && (entryLength > 0) ) {
          jQuery(this).addClass('error');
          jQuery('.error-box span').html('Invalid entry, value must be a number between '+dropMin+' and '+dropMax+'<i class="fa fa-exclamation-circle alignright"></i>').slideDown();
          if ( jQuery(this).hasClass('valid') ) {
            jQuery(this).removeClass('valid');
          };
        };
        buttonState();
      }
    });
  });

  jQuery('.addon.shutter-width').each(function() {
    jQuery(this).on('input', function() {
      if ( jQuery(this).val().length >= 3 ) {
        var shutterWidth = jQuery(this),
            entryLength = jQuery(this).val().length;
        if ( (shutterWidth.val() >= widthMin) && (shutterWidth.val() <= widthMax) ) {
          jQuery(this).addClass('valid').removeClass('empty');
          jQuery('.error-box span').slideUp();
          if ( jQuery(this).hasClass('error') ) {
            jQuery(this).removeClass('error');
          };
        } 
        else if (shutterWidth.val() < widthMin && shutterWidth.val()) {
          jQuery(this).addClass('error');
          jQuery(this).removeClass('empty');
          jQuery('.error-box span').html('Invalid entry, value must be greater than '+widthMin+'<i class="fa fa-exclamation-circle alignright"></i>').slideDown();
          if ( jQuery(this).hasClass('valid') ) {
            jQuery(this).removeClass('valid');
          };
        } 
        else if (shutterWidth.val() > widthMax) {
          jQuery(this).addClass('error');
          jQuery('.error-box span').html('Invalid entry, value must be less than '+widthMax+'<i class="fa fa-exclamation-circle alignright"></i>').slideDown();
          if ( jQuery(this).hasClass('valid') ) {
            jQuery(this).removeClass('valid');
          };
        } 
        else if( (typeof shutterWidth.val() != 'number') && (entryLength > 0) ) {
          jQuery(this).addClass('error');
          jQuery('.error-box span').html('Invalid entry, value must be a number between '+widthMin+' and '+widthMax+'<i class="fa fa-exclamation-circle alignright"></i>').slideDown();
          if ( jQuery(this).hasClass('valid') ) {
            jQuery(this).removeClass('valid');
          };
        };
        buttonState();
      }    
    });
  });



  jQuery('.product-addon').each(function(){
    var addonTitle = jQuery(this).find('.addon-name').text().replace('*', ''),
        addonVal = jQuery(this).find('.addon[checked="checked"]').val(),
        addonClass = jQuery(this).attr('class').split(' ')[2];
    jQuery('.purchase-summary--results').append('<div class="'+addonClass+' addon-result"><div class="title">'+addonTitle+'</div><div class="selected">'+addonVal+'</div></div>');
    jQuery('.purchase-summary--results .product-addon-add-your-measurements').prependTo('.purchase-summary--results');
    jQuery('.purchase-summary--results .product-addon-add-your-measurements').find('.selected').remove();
    if ( jQuery(this).hasClass('product-addon-location') ) {
      var addonVal = jQuery(this).find('.addon').val();
      jQuery('.purchase-summary--results').find('.product-addon-location .selected').text(addonVal);
    }
    if ( jQuery(this).hasClass('product-addon-add-your-measurements') ) {
      jQuery( jQuery(this).find('.form-row') ).each(function() {
        var addonLabel = jQuery(this).find('label').text(),
            addonVal = jQuery(this).find('.addon').val(),
            addonClass = jQuery(this).find('label').text().replace(/\(|\)/g, "").replace("mm", "").replace(" ", "-").toLowerCase();
            jQuery('<div class="selected-measurements"><div class="'+addonClass+' addon-result"><div class="label">'+addonLabel+'</div><div class="selected-value">'+addonVal+'</div></div></div>').insertAfter('.purchase-summary--results .product-addon-add-your-measurements .title');
      });
    }
      jQuery(this).find('.checked').removeClass('checked');
  });

  var shutterColour = jQuery('<div class="shutter-colour addon-result"><div class="title">Shutter Colour</div><div class="selected"></div></div>');
  jQuery('.purchase-summary--results').append(shutterColour);

  jQuery('.addon').on('change', function() {
    if (jQuery(this).attr('checked')) {
      var val = jQuery(this).val(),
          addonClass = jQuery(this).parents('.product-addon').attr('class').split(' ')[2];
      jQuery('.purchase-summary--results').find('.'+addonClass+' .selected').html(val);
    } 
    else if (jQuery(this).hasClass('valid')) {
      jQuery( jQuery(this) ).each(function() {
        var val = jQuery(this).val(),
            addonLabel = jQuery(this).parents('.form-row').find('label').text(),
            addonClass = jQuery(this).parents('.form-row').find('label').text().replace(/\(|\)/g, "").replace("mm", "").replace(" ", "-").toLowerCase();
        jQuery('.purchase-summary--results .selected-measurements').find('.'+addonClass+' .selected-value').html(val);
      });
    } 
    else if (jQuery(this).hasClass('input-text')) {
      var val = jQuery(this).val(),
          addonClass = jQuery(this).parents('.product-addon').attr('class').split(' ')[2];
      jQuery('.purchase-summary--results').find('.'+addonClass+' .selected').html(val);
    }

  });



  jQuery('#pa_security-shutters .swatch-wrapper').on('click', function () {
    var swatch = jQuery(this).attr('data-name');
    jQuery('.purchase-summary--details .shutter-colour .selected').html(swatch);
  });


  var stickySummary = jQuery('.purchase-summary').offset().top;

  jQuery(window).scroll(function() {  
    if (jQuery(window).scrollTop() > (stickySummary+500)) {
      jQuery('.purchase-summary').addClass('affix');
    }
    else {
      jQuery('.purchase-summary').removeClass('affix');
    }
  });

  jQuery('.product-addon-number-of-panels input').on('click', function(){
    var val = jQuery(this).attr('value');
    jQuery('#panel-img div').removeClass('show-panels');
    jQuery('#panel-img').find('.panels-'+val).delay(200).addClass('show-panels');
  });

});  