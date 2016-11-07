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
	$max_dimensions[ $addon['slug'] ] = $wpdb->get_results( "SELECT max_width, min_width, max_drop, min_drop 
		FROM `wp_woocommerce_dimension_restraints`
		WHERE `price_group` = '$tax_term' ", OBJECT );
}
?>

<div add-model class="input-wrap">

	<label style="width:60px;" for="wpti-product-x" data-toggle="tooltip" data-placement="right" title="Enter width in Millimeters (mm)">Width <small>(mm)</small></label>

	<input ng-model="input_width" 
			ng-change="bd_get_price(input_width, input_drop, selected_attribute, productQuantity, <?php echo htmlspecialchars(json_encode($max_dimensions)) ?>)" 
			type="number" 
			name="wpti_x" 
			placeholder="Enter a width" 
			class="wpti-product-size" 
			id="wpti-product-x">

	<label style="width:60px;" for="wpti-product-y" data-toggle="tooltip" data-placement="right" title="Enter drop in Millimeters (mm)">Drop <small>(mm)</small></label>

	<input ng-model="input_drop" 
			ng-change="bd_get_price(input_width, input_drop, selected_attribute, productQuantity, <?php echo htmlspecialchars(json_encode($max_dimensions)) ?>)" 
			type="number" 
			name="wpti_y" 
			placeholder="Enter a drop" 
			class="wpti-product-size" 
			id="wpti-product-y">

	<div ng-disabled="!selected_attribute" 
			title="Please choose a finish" 
			class="button calculate-price" 
			ng-click="bd_get_price(input_width, input_drop, selected_attribute, productQuantity, <?php echo htmlspecialchars(json_encode($max_dimensions)) ?>)">
				Calculate
	</div>

	<div id="notice1" class="notice">Please ensure measurements are supplied in millimetres. (1cm = 10mm).</div>

</div>
