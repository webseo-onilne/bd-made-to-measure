jQuery(document).ready(function($) {


	$('#_wcbp_product_select').on('change', function() {

		var priceGroup = $(this).find('option:selected').val();

		var options = {
			action    : 'wcbp_ajax_build_addon_select',
			variation : priceGroup
		};

		$.get(blinds.ajax_url, options, {}, 'json')
			.then(function(response) {
				showFinishes(response);
			});
	});

	$('#_wcbp_finish_select').on('change', function() {

		var finish = $(this).find('option:selected').val();
		var group = $('#_wcbp_product_select').find('option:selected').val();

		var options = {
			action : 'wcbp_ajax_get_prices', 
			choice : finish,
			width  : $('#_wcbp_txt_field_x').val(),
			height : $('#_wcbp_txt_field_y').val(),
			group  : group
		};

		$.get(blinds.ajax_url, options, {}, 'json')
			.then(function(response) {
				var price = addMarkup(response);
				$('#meta-box-proposal').find('#prop-price').remove();
				$('#meta-box-proposal').append('<div data-price="'+response.price+'" id="prop-price" style="padding: 10px;text-align: center;background: orange;font-weight: bolder;color: white;font-size: 20px;">R '+price.toFixed(2)+'</div>');
				generateMeta();
			});
	});

	function showFinishes(response) {
		$('#_wcbp_finish_select').empty();
		$('#_wcbp_finish_select').append('<option value="TBA">TBA</option>');
		$.each(response.terms, function(i, v) {
			$('#_wcbp_finish_select').append('<option value="'+v.name+'">'+v.name+'</option>');
		});
	}


	function addMarkup(data) {

		var range1 = JSON.parse(data.markup_range_1);
		var range2 = JSON.parse(data.markup_range_2);
		var range3 = JSON.parse(data.markup_range_3);
		var price = parseInt(data.price);

		if (range1 == null) {
			var range1 = {'from': 0, 'to': 0, 'by': 0};
		};
		if (range2 == null) {
			var range2 = {'from': 0, 'to': 0, 'by': 0};
		};
		if (range3 == null) {
			var range3 = {'from': 0, 'to': 0, 'by': 0};
		}

		if (price >= range1.from && price <= range1.to ) {
			var price = (price * range1.by / 100 + price); 

		} else if (price >= range2.from && price <= range2.to) {
			var price = (price * range2.by / 100 + price);

		} else if (price >= range3.from && price <= range3.to) {
			var price = (price * range3.by / 100 + price);

		}
		else if (price >= range3.to && price <= 1000000) {
			var price = price;

		} else {
			var price = price;
		};

		return price;
	}


	var $_elements = '#_wcbp_fittingtype_select, #_wcbp_controller_select, #_wcbp_finish_select, #_wcbp_product_select';
	$($_elements).on('change', function() {
		generateMeta();
	});

	function generateMeta() {
		$('.prop-meta').remove();
		var meta = {
			choice      : $('#_wcbp_finish_select').find('option:selected').val(),
			width       : $('#_wcbp_txt_field_x').val(),
			height      : $('#_wcbp_txt_field_y').val(),
			group  	    : $('#_wcbp_product_select').find('option:selected').text(),
			controller  : $('#_wcbp_controller_select').find('option:selected').val(),
			fittingType : $('#_wcbp_fittingtype_select').find('option:selected').val(),
		};
		var metaDetails = '<div class="prop-meta"><hr/>Finish: '+meta.group+': '+meta.choice+', Dimensions: Width: '+meta.width+' x Height: '+meta.height+', Controller: '+meta.controller+', Fitting Type: '+meta.fittingType+'<hr/></div>';
		$('#_wcbp_fittingtype_select').after(metaDetails);
	}

});