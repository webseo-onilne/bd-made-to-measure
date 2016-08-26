jQuery(document).ready(function ($) {

	toastr.options.closeButton = true;
	NProgress.start();
	NProgress.done();

  var app = angular.module('curtainManager', []);

  app

    .factory('DataLoader', function($http) {
      return {
        getPriceData: function(priceGroup) {
          return $http.get(blinds.ajax_url+'?action=get_price_book_ajax&group='+priceGroup);
        },
        getMarkupData: function(priceGroup) {
          return $http.get(blinds.ajax_url+'?action=get_markup_ajax&group='+priceGroup);
        },
        addMarkupData: function(markupData, priceGroup, markupRange) {
          return $http.get(blinds.ajax_url+'?action=bd_ajax_save_markup_data&group='+priceGroup+'&mdata='+markupData+'&range='+markupRange);
        }                 
      } 
    })

    .controller('curtainCtrl', function($scope, $http, DataLoader) {

      $scope.curtaingroup = 'undefined';
      $scope.filterby = '';
      $scope.liningFilter = '';
      $scope.pageLimit = '10';

      $scope.getCurtainPrices = function(curtainGroup) { 
        NProgress.start();

        DataLoader.getPriceData(curtainGroup).then(function(response) {

          if (response.data == '""' || !response.data || response.data === null) {
            toastr.error('No price sheet for the selected group', 'Error');
            NProgress.done();
            $scope.allPrices = '';
            return;
          }

          angular.forEach(response.data, function(v, i) {
            if (!v.markup_range_1) return;
            v.marked_up_price = addMarkup(v).toFixed(2);
          });

          $scope.selectedGroupActaual = curtainGroup;
          $scope.selectedGroup = curtainGroup;
          $scope.allPrices = response.data;
          $scope.showAll = response.data.length;
          NProgress.done();
        });

        DataLoader.getMarkupData(curtainGroup).then(function(response) {
          
          if (response.data == '""' || !response.data || response.data === null) {
            toastr.warning('No markup data for the selected group', 'Notice');
            NProgress.done();
            $scope.range1 = {};
            $scope.range2 = {};
            $scope.range3 = {};
            return;
          }

          var range1Data = JSON.parse(response.data[0].markup_range_1);
          var range2Data = JSON.parse(response.data[0].markup_range_2);
          var range3Data = JSON.parse(response.data[0].markup_range_3);

          $scope.range1 = {
            'to': range1Data.to,
            'from': range1Data.from,
            'markup_by': range1Data.markup_by
          };

          $scope.range2 = {
            'to': range2Data.to,
            'from': range2Data.from,
            'markup_by': range2Data.markup_by
          };

          if (!range3Data) return;
          $scope.range3 = {
            'to': range3Data.to ? range3Data.to : 0,
            'from': range3Data.from ? range3Data.from : 0,
            'markup_by': range3Data.markup_by ? range3Data.markup_by : 0
          };                    
        });
      }

      $scope.saveMarkup = function(markupData, priceGroup, markupRange) {
        NProgress.start();
        //console.log(JSON.stringify(markupData));
        DataLoader.addMarkupData(JSON.stringify(markupData), priceGroup, markupRange).then(function(response) {
          if (!response.data || response.data == '' || response.data == null) {
            toastr.error('An error occured, please try again', 'Error');
            NProgress.done();
          }
          $scope.getCurtainPrices(priceGroup);
          toastr.success('Markup Added', 'Success');
          NProgress.done();
        });
      };

      function addMarkup(data) {
        
        var range1 = JSON.parse(data.markup_range_1);
        var range2 = JSON.parse(data.markup_range_2);
        var range3 = JSON.parse(data.markup_range_3);
        var price = parseFloat(data.price);

        //console.log(range1);

        if (range1 == null) {
          var range1 = {'from': 0, 'to': 0, 'by': 0};
        };
        if (range2 == null) {
          var range2 = {'from': 0, 'to': 0, 'by': 0};
        };
        if (range3 == null) {
          var range3 = {'from': 0, 'to': 0, 'by': 0};
        }

        if (price >= parseInt(range1.from) && price <= parseInt(range1.to) ) {
          var price = (price * parseInt(range1.markup_by) / 100 + price); 

        } else if (price >= parseInt(range2.from) && price <= parseInt(range2.to) ) {
          var price = (price * parseInt(range2.markup_by) / 100 + price);

        } else if (price >= parseInt(range3.from) && price <= parseInt(range3.to) ) {
          var price = (price * parseInt(range3.markup_by) / 100 + price);

        }
         else {
          var price = price;
        };

        return price;
      }      

    });

});