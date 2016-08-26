<?php


/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No script kiddies please!' );
};
?>

<div style="display:none;" class="input-wrap">

	<label style="width:60px;" for="wpti-product-x">Width <small>(mm)</small></label>

	<input type="number" name="wpti_x" placeholder="Please choose a finish" class="wpti-product-size" id="wpti-product-x">

	<label style="width:60px;" for="wpti-product-y">Drop <small>(mm)</small></label>

	<input type="number" name="wpti_y" placeholder="Please choose a finish" class="wpti-product-size" id="wpti-product-y">

	<div ng-disabled="!selected_attribute" title="Please choose a finish" class="button calculate-price">Calculate</div>
	
</div>'