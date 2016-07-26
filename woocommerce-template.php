<?php


//Override the WooCommerce wc_dropdown_variation_attribute_options function. 
//To do this this file MUST be loaded before WooCommerce core. 
function wc_dropdown_variation_attribute_options1( $args = array() ) {
	wc_swatches_variation_attribute_options1($args);
}

function wc_swatches_variation_attribute_options1( $args = array() ) {
	$args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
	    'options' => false,
	    'attribute' => false,
	    'product' => false,
	    'selected' => false,
	    'name' => '',
	    'id' => '',
	    'class' => '',
	    'show_option_none' => __( 'Choose an option', 'woocommerce' )
	) );


	$options = $args['options'];
	$product = $args['product'];
	$attribute = $args['attribute'];
	$name = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id = $args['id'] ? $args['id'] : sanitize_title( $attribute );

	$config = new WC_Swatches_Attribute_Configuration_Object($product, $attribute);
	if ( $config->get_type() != 'default' ) :
		echo '<div id="picker_' . esc_attr( $id ) . '" class="select swatch-control">';
		wc_core_dropdown_variation_attribute_options1( $args );

		if ( !empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms( $product->id, $attribute, array('fields' => 'all') );

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options ) ) {
						if ( $config->get_type() == 'term_options' ) {
							$swatch_term = new WC_Swatch_Term( $config, $term->term_id, $attribute, $args['selected'] == $term->slug, $config->get_size() );
						} elseif ( $config->get_type() == 'product_custom' ) {
							$swatch_term = new WC_Product_Swatch_Term( $config, $term->term_id, $attribute, $args['selected'] == $term->slug, $config->get_size() );
						}

						do_action( 'woocommerce_swatches_before_picker_item', $swatch_term );
						echo $swatch_term->get_output();
						do_action( 'woocommerce_swatches_after_picker_item', $swatch_term );
					}
				}
			} else {
				foreach ( $options as $option ) {
					// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
					$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
					$swatch_term = new WC_Product_Swatch_Term( $config, $option, $name, $selected, $config->get_size() );
					do_action( 'woocommerce_swatches_before_picker_item', $swatch_term );
					echo $swatch_term->get_output();
					do_action( 'woocommerce_swatches_after_picker_item', $swatch_term );
				}
			}
		}
		echo '</div>';
	else :
		wc_core_dropdown_variation_attribute_options1( $args );
	endif;
}

/**
 * Exact Duplicate of wc_dropdown_variation_attribute_options
 * 
 */
function wc_core_dropdown_variation_attribute_options1( $args = array() ) {
	$args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
	    'options' => false,
	    'attribute' => false,
	    'product' => false,
	    'selected' => false,
	    'name' => '',
	    'id' => '',
	    'class' => '',
	    'show_option_none' => __( 'Choose an option', 'woocommerce' )
		) );

	$options = $args['options'];
	$product = $args['product'];
	$attribute = $args['attribute'];
	$name = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class = $args['class'];

	if ( empty( $options ) && !empty( $product ) && !empty( $attribute ) ) {
		$attributes = $product->get_variation_attributes();
		$options = $attributes[$attribute];
	}

	echo '<select ng-model="selected_attribute" id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '">';

	if ( $args['show_option_none'] ) {
		echo '<option value="test">' . esc_html( $args['show_option_none'] ) . '</option>';
	}

	if ( !empty( $options ) ) {
		if ( $product && taxonomy_exists( $attribute ) ) {
			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms( $product->id, $attribute, array('fields' => 'all') );

			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options ) ) {
					echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
				}
			}
		} else {
			foreach ( $options as $option ) {
				// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
				$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
				echo '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
			}
		}
	}

	echo '</select>';
}

function woocommerce_swatches_get_template1( $template_name, $args = array() ) {
	global $woocommerce_swatches;
	return wc_get_template( $template_name, $args, 'woocommerce-swatches/', $woocommerce_swatches->plugin_dir() . '/templates/' );
}
