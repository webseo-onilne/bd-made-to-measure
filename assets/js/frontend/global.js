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
          var $_ = jQuery; 
          var plus = element.next();
          var minus = $_(element).prev('span');
          var obj = {"pa_alum-blinds-25mm-perfhambr":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_alum-blinds-25mm-premium":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_alum-blinds-50mm-perfhambru":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_alum-blinds-50mm-premium":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_aluwood-blinds-25mm":[{"max_width":"2700","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_aluwood-blinds-50mm":[{"max_width":"2700","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_bamboo-venetian-50mm":[{"max_width":"2400","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_basswood-blinds-50mm":[{"max_width":"2500","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_cool-test":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_curtains-g1":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_curtains-g2":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_curtains-g3":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_curtains-g4":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_curtains-g5":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_curtains-g6":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_curtains-g7":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_panel-blinds-center-g2":[{"max_width":"5000","min_width":"1600","max_drop":"3000","min_drop":"1000"}],"pa_panel-blinds-centre-g3":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_panel-blinds-centre-g4":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_plaswood-blinds-50mm":[{"max_width":"2700","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_roller-bamboo-classic-g2":[{"max_width":"2400","min_width":"800","max_drop":"2800","min_drop":"800"}],"pa_roller-bamboo-stylish-g4":[{"max_width":"2400","min_width":"800","max_drop":"2800","min_drop":"800"}],"pa_roller-blinds-classic":[{"max_width":"2000","min_width":"600","max_drop":"2800","min_drop":"600"}],"pa_roller-blinds-premium":[{"max_width":"2400","min_width":"600","max_drop":"2800","min_drop":"600"}],"pa_roller-blinds-ultimate":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_roller-blinds-weave-perf":[{"max_width":"3000","min_width":"600","max_drop":"2800","min_drop":"600"}],"pa_roller-designer-g4-bm":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_roller-designer-g5-bm":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_roller-designer-g6-bm":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_roller-designer-g7-bm":[{"max_width":"2400","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_roller-double-lightly-r1":[{"max_width":"2500","min_width":"600","max_drop":"2800","min_drop":"600"}],"pa_roller-double-perforated-r2":[{"max_width":"2500","min_width":"600","max_drop":"2800","min_drop":"600"}],"pa_roller-outdoor-sheerweave":[{"max_width":"5800","min_width":"1000","max_drop":"4000","min_drop":"1000"}],"pa_roller-printed":[{"max_width":"2000","min_width":"600","max_drop":"2800","min_drop":"600"}],"pa_roman-blinds-classic-r2":[{"max_width":"2000","min_width":"600","max_drop":"2800","min_drop":"600"}],"pa_roman-blinds-premium-r3":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_roman-blinds-ultimate":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_roman-blinds-weave-r4":[{"max_width":"2400","min_width":"600","max_drop":"2800","min_drop":"600"}],"pa_roman-designer-g5-bm":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_roman-designer-g6-bm":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_roman-g4-bm":[{"max_width":"3000","min_width":"600","max_drop":"3000","min_drop":"600"}],"pa_shutters":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_vert-blinds-127mm-blockout":[{"max_width":"5104","min_width":"697","max_drop":"3000","min_drop":"600"}],"pa_vert-blinds-127mm-classic":[{"max_width":"5104","min_width":"697","max_drop":"3000","min_drop":"600"}],"pa_vert-blinds-127mm-g3":[{"max_width":"5104","min_width":"697","max_drop":"3000","min_drop":"600"}],"pa_vert-blinds-127mm-g5":[{"max_width":null,"min_width":null,"max_drop":null,"min_drop":null}],"pa_vert-blinds-90mm-blockout":[{"max_width":"5104","min_width":"697","max_drop":"3000","min_drop":"600"}],"pa_vert-blinds-90mm-classic":[{"max_width":"5104","min_width":"697","max_drop":"3000","min_drop":"600"}],"pa_vert-blinds-90mm-g3":[{"max_width":"5104","min_width":"697","max_drop":"3000","min_drop":"600"}],"pa_vertical-printed":[{"max_width":"5104","min_width":"697","max_drop":"3000","min_drop":"600"}]};

          plus.on('click', function() {
            scope.bd_get_price(scope.input_width, scope.input_drop, scope.selected_attribute, scope.productQuantity, obj);
          });

          minus.on('click', function() {
            scope.bd_get_price(scope.input_width, scope.input_drop, scope.selected_attribute, scope.productQuantity, obj);
          }); 
        });       
      }
    };
  })

  .directive('inputWidthRestraints', function ($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        //if (!scope.selected_attribute) return;
        element.on('change input', function() {
          var userInput = element.val();
          var selectedAttr = scope.selected_attribute;
          console.log('Width selected attr', selectedAttr);
          var restraints = selectedAttr ? angular.fromJson(attrs.dims)[selectedAttr][0] : {};
          var inputHeight = jQuery('#wpti-product-y').val();
          var inputWidth = jQuery('#wpti-product-x').val();                  
          if ( (userInput < parseInt(restraints.min_width)) ) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            jQuery('.notice').html(blinds.min_width_error +': '+parseInt(restraints.min_width)).css('color', 'red');
          } else if ( (userInput > parseInt(restraints.max_width)) && (inputWidth.length >= 3 && inputHeight.length >= 3)) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            jQuery('.notice').html(blinds.max_width_error + ': '+parseInt(restraints.max_width)+'. Please consider splitting the product into two. Or feel free to contact us for assistance.').css('color', 'red');
          } else {
            element.css('border', '1px solid #cccccc').removeClass('invalid').addClass('valid');
            jQuery('.notice').html('').css('color', 'black');
          } 
          if (jQuery('.valid').length === 2) {
              jQuery('.single_variation_wrap, .final-price').fadeIn();
              jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed').attr('disabled', false);
          } else {
            //if ( (inputWidth && inputHeight) ) {
              jQuery('.single_variation_wrap, .final-price').fadeOut();
              jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed').attr('disabled', true);
            //}
          }                   
        });

        //$watch
        scope.$watch('selected_attribute', function(v) {
          var userInput = element.val();
          var selectedAttr = v;
          //console.log('Width selected attr', selectedAttr);
          var restraints = selectedAttr ? angular.fromJson(attrs.dims)[selectedAttr][0] : {};
          var inputHeight = jQuery('#wpti-product-y').val();
          var inputWidth = jQuery('#wpti-product-x').val();                  
          if ( (userInput < parseInt(restraints.min_width)) ) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            setTimeout(function() {
              jQuery('.notice').html(blinds.min_width_error +': '+parseInt(restraints.min_width)).css('color', 'red');
            }, 500); 
          } else if ( (userInput > parseInt(restraints.max_width)) && (inputWidth.length >= 3 && inputHeight.length >= 3)) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            setTimeout(function() {
              jQuery('.notice').html(blinds.max_width_error + ': '+parseInt(restraints.max_width)+'. Please consider splitting the product into two. Or feel free to contact us for assistance.').css('color', 'red');
            }, 500);
          } else {
            element.css('border', '1px solid #cccccc').removeClass('invalid').addClass('valid');
            jQuery('.notice').html('').css('color', 'black');
          } 
          if (jQuery('.valid').length === 2) {
              jQuery('.single_variation_wrap, .final-price').fadeIn();
              jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed').attr('disabled', false);
          } else {
            //if ( (inputWidth && inputHeight) ) {
              jQuery('.single_variation_wrap, .final-price').fadeOut();
              jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed').attr('disabled', true);
            //}
          }  
        });
      }
    };
  })

  .directive('inputDropRestraints', function ($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        //if (!scope.selected_attribute) return;
        element.on('change input', function() {
          var userInput = element.val();
          var selectedAttr = scope.selected_attribute;
          console.log('Drop selected attr', selectedAttr);
          var restraints = selectedAttr ? angular.fromJson(attrs.dims)[selectedAttr][0] : {};
          var inputHeight = jQuery('#wpti-product-y').val();
          var inputWidth = jQuery('#wpti-product-x').val();
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
            //if ( (inputWidth && inputHeight) ) {
              jQuery('.single_variation_wrap, .final-price').fadeOut();
              jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed').attr('disabled', true);
            //}
          }                   
        });

        //$watch
        scope.$watch('selected_attribute', function(v) {

          var userInput = element.val();
          var selectedAttr = v;
          //console.log('Drop selected attr', selectedAttr);
          var restraints = selectedAttr ? angular.fromJson(attrs.dims)[selectedAttr][0] : {};
          var inputHeight = jQuery('#wpti-product-y').val();
          var inputWidth = jQuery('#wpti-product-x').val();
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
            //if ( (inputWidth && inputHeight) ) {
              jQuery('.single_variation_wrap, .final-price').fadeOut();
              jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed').attr('disabled', true);
            //}
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
            jQuery('.single_variation_wrap, .final-price').fadeIn();
              jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed').attr({'disabled': false, 'title': ''});
          } else {
            jQuery('.single_variation_wrap, .final-price').fadeOut();
            jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed').attr('disabled', true);
          }
        });
      }
    };
  })