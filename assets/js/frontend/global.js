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
  });