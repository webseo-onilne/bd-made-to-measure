'use strict';

var app = angular.module("bd_made_to_measure", []);

app

  .factory('DataLoader', function($http) {

    return {
      getBlindsPrices: function(width, drop, attr) {
        // lookup
        return $http.get(blinds.ajax_url+'?action=bd_do_price_calcuation_ajax&width='+width+'&drop='+drop+'&selected_attribute='+attr);
      },
      getCurtainPrices: function(width, drop, attr, lining, style) {
        // lookup
        return $http.get(blinds.ajax_url+'?action=bd_do_curtains_price_calcuation_ajax&width='+width+'&drop='+drop+'&selected_attribute='+attr+'&lining='+lining+'&style='+style);
      },
      getShutterPrices: function(width, height) {
        // lookup
        return $http.get(blinds.ajax_url+'?action=bd_do_price_calcuation_ajax');
      }         
    } 

  })

  .directive('addModel', function () {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        var $_ = jQuery, selectOption = $_.find('.select-option');

        $_(selectOption).on('click', function() {
          scope.selected_attribute = $_(this).closest('tr').find('select').attr('id');
        });
      }
    };
  })

  .directive('form', function($location) {
    return {
      restrict:'E',
      priority: 999,
      compile: function() {
        return {
          pre: function(scope, element, attrs){
            if (attrs.noaction === '') return;
            if (attrs.action === undefined || attrs.action === ''){
              attrs.action = $location.absUrl();
            }
          }
        }
      }
    }
  })

  .directive('changeQuantity', function ($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        $timeout(function() {
          let $_ = jQuery; 
          let plus = element.next();
          let minus = $_(element).prev('span');

          plus.on('click', function() {
            scope.bd_get_price(scope.input_width, scope.input_drop, scope.selected_attribute, scope.productQuantity);
          });

          minus.on('click', function() {
            scope.bd_get_price(scope.input_width, scope.input_drop, scope.selected_attribute, scope.productQuantity);
          }); 
        });       
      }
    };
  })

  .directive('inputWidthRestraints', function ($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        element.on('change input', function() {
          let userInput = element.val();
          let selectedAttr = scope.selected_attribute;
          let restraints = angular.fromJson(attrs.dims)[selectedAttr][0];
          let inputHeight = jQuery('#wpti-product-y').val();
          let inputWidth = jQuery('#wpti-product-x').val();                  
          if ( (userInput < parseInt(restraints.min_width)) && (inputWidth.length >= 3 && inputHeight.length >= 3)) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            jQuery('.notice').html(blinds.min_width_error +': '+parseInt(restraints.min_width)).css('color', 'red');
          } else if ( (userInput > parseInt(restraints.max_width)) && (inputWidth.length >= 3 && inputHeight.length >= 3)) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            jQuery('.notice').html(blinds.max_width_error + ': '+parseInt(restraints.max_width)).css('color', 'red');
          } else {
            element.css('border', '1px solid #cccccc').removeClass('invalid').addClass('valid');
            jQuery('.notice').html('').css('color', 'black');
          } 
          if (jQuery('.valid').length === 2) {
              jQuery('.single_variation_wrap, .final-price').fadeIn();
              jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed').attr('disabled', false);
          } else {
            if ( (inputWidth && inputHeight) && (inputWidth.length >= 3 && inputHeight.length >= 3) ) {
              jQuery('.single_variation_wrap, .final-price').fadeOut();
              jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed').attr('disabled', true);
            }
          }                   
        });
      }
    };
  })

  .directive('inputDropRestraints', function ($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        element.on('change input', function() {
          let userInput = element.val();
          let selectedAttr = scope.selected_attribute;
          let restraints = angular.fromJson(attrs.dims)[selectedAttr][0];
          let inputHeight = jQuery('#wpti-product-y').val();
          let inputWidth = jQuery('#wpti-product-x').val();
          if (userInput < parseInt(restraints.min_drop)) {
            if (inputWidth.length >= 3 && inputHeight.length >= 3) {
              element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
              jQuery('.notice').html(blinds.min_drop_error + ': '+parseInt(restraints.min_drop)).css('color', 'red');
            }
          } else if (userInput > parseInt(restraints.max_drop)) {
            if (inputWidth.length >= 3 && inputHeight.length >= 3) {
              element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
              jQuery('.notice').html(blinds.max_drop_error + ': '+parseInt(restraints.max_drop)).css('color', 'red'); 
            }
          } else {
            element.css('border', '1px solid #cccccc').removeClass('invalid').addClass('valid');
            jQuery('.notice').html('').css('color', 'black');
          }
          if (jQuery('.valid').length === 2) {
              jQuery('.single_variation_wrap, .final-price').fadeIn();
              jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed').attr({'disabled': false, 'title': ''});
          } else {
            if ( (inputWidth && inputHeight) && (inputWidth.length >= 3 && inputHeight.length >= 3) ) {
              jQuery('.single_variation_wrap, .final-price').fadeOut();
              jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed').attr('disabled', true);
            }
          }                   
        });
      }
    };
  })

  .directive('customValidation', function () {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        var $_ = jQuery;   
        element.on('click', function() {
          if (jQuery('.valid').length === 2) {
              jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed').attr({'disabled': false, 'title': ''});
          } else {
            jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed').attr('disabled', true);
          }
        });
      }
    };
  })