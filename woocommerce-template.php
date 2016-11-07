<?php


//Override the WooCommerce woocommerce_quantity_input function. 
//To do this this file MUST be loaded before WooCommerce core. 
function woocommerce_quantity_input( $args = array() ) {
	woocommerce_quantity_input_test($args);
}

	/**
	 * Output the quantity input for add to cart forms.
	 *
	 * @param  array $args Args for the input
	 * @param  WC_Product|null $product
	 * @param  boolean $echo Whether to return or echo|string
	 */
	function woocommerce_quantity_input_test( $args = array(), $product = null, $echo = true ) {
		if ( is_null( $product ) ) {
			$product = $GLOBALS['product'];
		}

		$defaults = array(
			'input_name'  => 'quantity',
			'input_value' => '1',
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', '', $product ),
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', '', $product ),
			'step'        => apply_filters( 'woocommerce_quantity_input_step', '1', $product ),
			'pattern'     => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
			'inputmode'   => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
		);

		$args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

		// Set min and max value to empty string if not set.
		$args['min_value'] = isset( $args['min_value'] ) ? $args['min_value'] : '';
		$args['max_value'] = isset( $args['max_value'] ) ? $args['max_value'] : '';

		// Apply sanity to min/max args - min cannot be lower than 0
		if ( '' !== $args['min_value'] && is_numeric( $args['min_value'] ) && $args['min_value'] < 0 ) {
			$args['min_value'] = 0; // Cannot be lower than 0
		}

		// Max cannot be lower than 0 or min
		if ( '' !== $args['max_value'] && is_numeric( $args['max_value'] ) ) {
			$args['max_value'] = $args['max_value'] < 0 ? 0 : $args['max_value'];
			$args['max_value'] = $args['max_value'] < $args['min_value'] ? $args['min_value'] : $args['max_value'];
		}

		ob_start();

		extract($defaults);
		echo "<pre>";
		var_dump($product);
		echo "</pre>";
		include 'includes/quantity.php';
		//wc_get_template( 'global/quantity-input.php', $args );

		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}
