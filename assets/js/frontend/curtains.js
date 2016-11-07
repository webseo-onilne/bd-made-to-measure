'use strict';

app

  .controller("curtainsCtrl", function($scope, $http, DataLoader) {

    $scope.selected_attribute = '';
    $scope.productQuantity = 1;
    $scope.showLoader = false;    
    
    $scope.bd_get_price = function(width, drop, selectedSwatch, qty) {

      var lining = jQuery('.addon-wrap-7400-lining').find('select').val();
      var style = jQuery('.product-addon-curtain-style').find('input:checked').val();      

      if (!selectedSwatch) {
        $scope.finalPrice = 'Select a finish';
        return;

      } 
      else if (!style) {
        $scope.finalPrice = 'Select Curtain Style (Step 1)';
        return;

      } 
      else if (!lining) {
        $scope.finalPrice = 'Select Lining Type (Step 2)';
        return;

      }
      else if (!width || !drop) {
        $scope.finalPrice = 'Enter Dimensions (Step 3)';
        return;

      } 
      else {

        if (!width || !drop || !selectedSwatch || parseInt(drop) < 500) return;

        $scope.showLoader = true;  

        DataLoader.getCurtainPrices(width, drop, selectedSwatch, lining, style).then(function(response) {
          console.log(response.data);
          if (!response.data.price) return;
          // Price without tax
          var priceNoTax = parseFloat(response.data.price);
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

  })