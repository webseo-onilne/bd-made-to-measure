'use strict';

app

  .controller("curtainsCtrl", function($scope, $http, DataLoader) {

    $scope.selected_attribute = '';
    $scope.productQuantity = 1;
    $scope.showLoader = false;    
    
    $scope.bd_get_price = function(width, drop, selectedSwatch, qty) {

      if (!width || !drop || !selectedSwatch || parseInt(drop) < 500) return;

      $scope.showLoader = true;  

      let lining = jQuery('.addon-wrap-7400-lining').find('select').val();
      let style = jQuery('.product-addon-curtain-style').find('input:checked').val();

      DataLoader.getCurtainPrices(width, drop, selectedSwatch, lining, style).then(function(response) {
        console.log(response.data);
        if (!response.data.price) return;
        // Price without tax
        let priceNoTax = parseFloat(response.data.price);
        // Tax rate
        let tax = response.data.tax[1].rate;
        // Price with tax
        let priceNoQty = (priceNoTax * tax / 100) + priceNoTax;
        // Price x product quantity
        let price = priceNoQty * qty;
        // Add price to scope
        $scope.finalPrice = price.toFixed(2);
        // Add currency symbol to scope
        $scope.currencySymbol = response.data.currency;
        $scope.showLoader = false;  
      });
    
    }

    $scope.increment = function(val) {
      $scope.productQuantity = val + 1;
    }

    $scope.decrement = function(val) {
      if (val <= 1) return;
      $scope.productQuantity = val - 1;
    }    

  })