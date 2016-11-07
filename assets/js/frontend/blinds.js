'use strict';

app
  .controller("blindsCtrl", function($scope, $http, DataLoader) {

    $scope.selected_attribute = '';
    $scope.productQuantity = jQuery('.quantity').find('input').val();
    $scope.showLoader = false;

    $scope.bd_get_price = function(width, drop, selectedSwatch, qty, restraints) {

      //console.log(restraints[selectedSwatch][0]);
      var bd_dims = selectedSwatch ? restraints[selectedSwatch][0] : {};

      if (!selectedSwatch) {
        jQuery('.notice').html('Please Choose a finish').css('color', 'red');
        jQuery('.single_variation_wrap, .final-price').fadeOut();
        return;

      } 
      else if (!width || !drop) {
        jQuery('.notice').html('Please Enter Dimensions').css('color', 'red');
        jQuery('.single_variation_wrap, .final-price').fadeOut();
        return;

      } 
      else if (width > parseInt(bd_dims.max_width)) {
        jQuery('.notice').html(blinds.max_width_error + ': '+parseInt(bd_dims.max_width)+'. Please consider splitting the product into two. Or feel free to contact us for assistance.').css('color', 'red');
        jQuery('.single_variation_wrap, .final-price').fadeOut();
        return;

      }
      else if (width < parseInt(bd_dims.min_width)) {
        jQuery('.notice').html(blinds.min_width_error +': '+parseInt(bd_dims.min_width)).css('color', 'red');
        jQuery('.single_variation_wrap, .final-price').fadeOut();
        return;

      }
      else if (drop > parseInt(bd_dims.max_drop)) {
        jQuery('.notice').html(blinds.max_drop_error + ': '+parseInt(bd_dims.max_drop)).css('color', 'red');
        jQuery('.single_variation_wrap, .final-price').fadeOut();
        return;

      } 
      else if (drop < parseInt(bd_dims.min_drop)) {
        jQuery('.notice').html(blinds.min_drop_error + ': '+parseInt(bd_dims.min_drop)).css('color', 'red');
        jQuery('.single_variation_wrap, .final-price').fadeOut();
        return;

      }
      else {

        $scope.finalPrice = '';
        
        if (!width || !drop || !selectedSwatch || parseInt(drop) < 500) return;

        $scope.showLoader = true; 

        DataLoader.getBlindsPrices(width, drop, selectedSwatch).then(function(response) {
          console.log(response.data);
          if (!response.data.price) return;
          // Price without tax
          var priceNoTax = response.data.price;
          // Tax rate
          var tax = response.data.tax[1].rate;
          // Price with tax
          var priceNoQty = (priceNoTax * tax / 100) + priceNoTax;
          // Price x product quantity
          var price = priceNoQty * qty;
          // Add price to scope
          $scope.finalPrice = price.toFixed(2);
          // Add currency symbol to scope
          $scope.currencySymbol = response.data.currency;
          
          $scope.showLoader = false;

          jQuery('.notice').html('').css('color', 'black');
          jQuery('.single_variation_wrap, .final-price').fadeIn();
          jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed').attr({'disabled': false, 'title': ''});

        });

      }

    }


    $scope.increment = function(val) {
      $scope.productQuantity = val + 1;
    }
    $scope.decrement = function(val) {
      if (val <= 1) return;
      $scope.productQuantity = val - 1;
    }

    $scope.validate_inputs = function(userInput, restraints) {
      console.log(restraints);
    };

    // $scope.$watch('productQuantity', function(v) {
    //   var input_width, input_drop, selected_attribute;
    //   //if (!v || !input_width || !input_drop || !selected_attribute) return;
    //   $scope.bd_get_price(input_width, input_drop, selected_attribute, v, {});
    // });

  })
