<?php


/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No script kiddies please!' );
};


// Get the max/min dimensions
foreach ( $addons as $addon ) {
	if (strpos($addon['slug'], 'pa_curtains') === false) continue;
	$tax_term = 'attribute_'.$addon['slug'];
	$max_dimensions[$addon['slug']] = $wpdb->get_results( "SELECT MAX(width) as max_width, MIN(width) as min_width 
	FROM `wp_woocommerce_curtain_price_table` 
	WHERE `price_group` = '$tax_term' ", OBJECT );
}

?>

<div add-model class="input-wrap">

	<label style="width:60px;" for="wpti-product-x">Width <small>(mm)</small></label>

	<input data-dims="<?php echo htmlspecialchars(json_encode($max_dimensions)) ?>" 
			input-width-restraints 
			ng-model="input_width" 
			ng-change="bd_get_price(input_width, input_drop, selected_attribute, productQuantity)" 
			type="number" 
			name="wpti_x" 
			placeholder="Enter a width" 
			class="wpti-product-size" 
			id="wpti-product-x">

	<label style="width:60px;" for="wpti-product-y">Drop <small>(mm)</small></label>

	<input data-dims="<?php echo htmlspecialchars(json_encode($max_dimensions)) ?>" 
			input-drop-restraints 
			ng-model="input_drop" 
			ng-change="bd_get_price(input_width, input_drop, selected_attribute, productQuantity)" 
			type="number" 
			name="wpti_y" 
			placeholder="Enter a drop" 
			class="wpti-product-size" 
			id="wpti-product-y">

	<div class="button calculate-price" 
			custom-validation 
			ng-click="bd_get_price(input_width, input_drop, selected_attribute, productQuantity)">
				Calculate
	</div>

	<div class="notice">Please ensure measurements are supplied in millimetres. (1cm = 10mm).</div>

</div>