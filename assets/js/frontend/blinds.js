'use strict';

app
  .controller("blindsCtrl", function($scope, $http, DataLoader) {

    $scope.selected_attribute = '';
    $scope.productQuantity = 1;

    $scope.bd_get_price = function(width, drop, selectedSwatch, qty) {

      if (!width || !drop || !selectedSwatch || parseInt(drop) < 500) return;

      DataLoader.getBlindsPrices(width, drop, selectedSwatch).then(function(response) {
        console.log(response.data);
        if (!response.data.price) return;
        // Price without tax
        let priceNoTax = response.data.price;
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
      });

    }

  });  