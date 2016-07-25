app

  .controller("blindsCtrl", function($scope, $http, DataLoader, $compile) {

    $scope.bd_get_price = function(width, drop, selectedSwatch) {

      DataLoader.getBlindsPrices(width, drop, selectedSwatch).then(function(response) {
        console.log(response.data);
      });

    }

  })  