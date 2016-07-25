var app = angular.module("bd_made_to_measure", []);

app

  .factory('DataLoader', function($http) {

    return {
      get: function(width, height) {
        // lookup
        return $http.get(blinds.ajax_url+'?action=test_action');
      },
    } 

  })

