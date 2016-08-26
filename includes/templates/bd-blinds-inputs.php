<?php


/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No script kiddies please!' );
};


// Get the max/min dimensions
foreach ( $addons as $addon ) {
	$tax_term = $addon['slug'];
	$max_dimensions[$addon['slug']] = $wpdb->get_results( "SELECT MAX(width) as max_width, MIN(width) as min_width, MAX(height) as max_drop, MIN(height) as min_drop 
		FROM `wp_woocommerce_addon_price_table`
		WHERE `field_label` = '$tax_term' ", OBJECT );
}
?>

<div add-model class="input-wrap">

	<label style="width:60px;" for="wpti-product-x" data-toggle="tooltip" data-placement="right" title="Please choose a finish">Width <small>(mm)</small></label>

	<input ng-disabled="!selected_attribute" 
			title="Please choose a finish" 
			data-dims="<?php echo htmlspecialchars(json_encode($max_dimensions)) ?>" 
			input-width-restraints 
			ng-model="input_width" 
			ng-change="bd_get_price(input_width, input_drop, selected_attribute, productQuantity)" 
			type="number" 
			name="wpti_x" 
			placeholder="Please choose a finish" 
			class="wpti-product-size" 
			id="wpti-product-x">

	<label style="width:60px;" for="wpti-product-y" data-toggle="tooltip" data-placement="right" title="Please choose a finish">Drop <small>(mm)</small></label>

	<input ng-disabled="!selected_attribute" 
			title="Please choose a finish" 
			data-dims="<?php echo htmlspecialchars(json_encode($max_dimensions)) ?>" 
			input-drop-restraints 
			ng-model="input_drop" 
			ng-change="bd_get_price(input_width, input_drop, selected_attribute, productQuantity)" 
			type="number" 
			name="wpti_y" 
			placeholder="Please choose a finish" 
			class="wpti-product-size" 
			id="wpti-product-y">

	<div ng-disabled="!selected_attribute" 
			title="Please choose a finish" 
			class="button calculate-price" 
			custom-validation 
			ng-click="bd_get_price(input_width, input_drop, selected_attribute, productQuantity)">
				Calculate
	</div>

	<div class="notice">Please ensure measurements are supplied in millimetres. (1cm = 10mm).</div>

</div>
