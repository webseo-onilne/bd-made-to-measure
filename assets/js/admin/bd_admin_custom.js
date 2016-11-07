jQuery(document).ready(function ($) {

	toastr.options.closeButton = true;
	NProgress.start();
	NProgress.done();

  var app = angular.module('curtainManager', ['selectize']);

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
        },
        addDimensionData: function(dimData) {
          return $http.get(blinds.ajax_url+'?action=bd_ajax_save_dimension_data&group='+dimData.priceSheet+'&max_w='+dimData.maxWidth+'&min_w='+dimData.minWidth+'&max_d='+dimData.maxDrop+'&min_d='+dimData.minDrop);
        },        
        getMetaData: function(priceGroup) {
          return $http.get(blinds.ajax_url+'?action=bd_ajax_get_meta_data&group='+priceGroup);
        },
        getVariationData: function(getSwatches) {
          if (getSwatches) {
            return $http.get(blinds.ajax_url+'?action=get_variations_ajax&get_swatches='+getSwatches);
          }
          else {
            return $http.get(blinds.ajax_url+'?action=get_variations_ajax');
          }
        }                   
      } 
    })

    .controller('curtainCtrl', function($scope, $http, DataLoader) {

      $scope.curtaingroup;
      $scope.filterby = '';
      $scope.liningFilter = '';
      $scope.pageLimit = '20';
      $scope.variations = [];

      $scope.myConfig = {
        onChange: function(value){
          if (!value) return;
          $scope.getCurtainPrices(value);
        },
        maxItems: 1,
        required: true,
        closeAfterSelect: true,
        selectOnTab: true,
        openOnFocus: false
      };

      DataLoader.getVariationData(false).then(function(response) {
        $scope.variations = response.data;
      });

      $scope.getCurtainPrices = function(curtainGroup) { 
        if (!curtainGroup) return;
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
          $scope.showAll = response.data.length+1;
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

        DataLoader.getMetaData(curtainGroup).then(function(response) {

          $scope.dimRestraints = {
            priceSheet: response.data.price_sheet,
            minWidth: response.data[0].min_width,
            maxWidth: response.data[0].max_width,
            minDrop: response.data[0].min_drop,
            maxDrop: response.data[0].max_drop
          };

        });

        DataLoader.getVariationData(true).then(function(response) {
          $scope.allVariations = response.data;
          
          var a = [];
          angular.forEach(response.data, function(v, i) {

            if (v.value == curtainGroup) {
              a.push(v);
            }
          });

          $scope.swatches = a[0].swatches;
          $scope.friendlyName = a[0].text;

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

      $scope.saveDims = function(obj) {
        NProgress.start();
        DataLoader.addDimensionData(obj).then(function(response) {
          if (!response.data || response.data == '' || response.data == null) {
            toastr.error('An error occured, please try again', 'Error');
            NProgress.done();
          }

          $scope.dimRestraints = {
            priceSheet: response.data[0].price_group,
            minWidth: response.data[0].min_width,
            maxWidth: response.data[0].max_width,
            minDrop: response.data[0].min_drop,
            maxDrop: response.data[0].max_drop
          };

          console.log(response);

          toastr.success('Dimensions Added', 'Success');
          NProgress.done();          
        });
      }

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

        return price *1.14;
      }      

    });

});