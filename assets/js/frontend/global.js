'use strict';

var app = angular.module("bd_made_to_measure", []);

app

  .factory('DataLoader', function($http) {

    return {
      getBlindsPrices: function(width, drop, attr) {
        // lookup
        return $http.get(blinds.ajax_url+'?action=bd_do_price_calcuation_ajax&width='+width+'&drop='+drop+'&selected_attribute='+attr);
      },
      getCurtainPrices: function(width, height) {
        // lookup
        return $http.get(blinds.ajax_url+'?action=bd_do_price_calcuation_ajax');
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

  .directive('changeQuantity', function () {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        let $_ = jQuery; 
        let plus = element.next();
        let minus = $_(element).prev('input');

        plus.on('click', function() {
          if (parseInt(element.val()) < 1) return;
          scope.productQuantity = parseInt(element.val())+1;
          scope.bd_get_price(scope.input_width, scope.input_drop, scope.selected_attribute, parseInt(element.val())+1);
        });

        minus.on('click', function() {
          if (parseInt(element.val()) <= 1) return;
          scope.productQuantity = parseInt(element.val())-1;
          scope.bd_get_price(scope.input_width, scope.input_drop, scope.selected_attribute, parseInt(element.val())-1);
        });        
      }
    };
  })

  .directive('inputWidthRestraints', function ($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        element.on('change', function() {
          let userInput = element.val();
          let selectedAttr = scope.selected_attribute;
          let restraints = angular.fromJson(attrs.dims)[selectedAttr][0];          
          if (userInput < parseInt(restraints.min_width)) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            $timeout(function(){scope.finalPrice = undefined},700);
          } else if (userInput > parseInt(restraints.max_width)) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            $timeout(function(){scope.finalPrice = undefined},700);
          } else {
            element.css('border', '1px solid #cccccc').removeClass('invalid').addClass('valid');
          } 
          if (jQuery('.valid').length === 2) {
              jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
          } else {
            jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
          }                   
        });
      }
    };
  })

  .directive('inputDropRestraints', function ($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        element.on('change', function() {
          let userInput = element.val();
          let selectedAttr = scope.selected_attribute;
          let restraints = angular.fromJson(attrs.dims)[selectedAttr][0];          
          if (userInput < parseInt(restraints.min_drop)) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            $timeout(function(){scope.finalPrice = undefined},700);
          } else if (userInput > parseInt(restraints.max_drop)) {
            element.css('border', '1px solid red').addClass('invalid').removeClass('valid');
            $timeout(function(){scope.finalPrice = undefined},700);
          } else {
            element.css('border', '1px solid #cccccc').removeClass('invalid').addClass('valid');
          }
          if (jQuery('.valid').length === 2) {
              jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
          } else {
            jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
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
              jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
          } else {
            jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
          }
        });
      }
    };
  })