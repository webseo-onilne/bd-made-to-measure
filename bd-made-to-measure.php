<?php
/*
Plugin Name: Blinds Direct - Made to Measure
Plugin URI: https://github.com/michaeldoye/bd-made-to-measure
Description: Tools for Blinds Direct
Author: Web SEO Online
Author URI: http://webseo.co.za
Version: 0.0.1

	Copyright: © 2016 Web SEO Online (email : michael@webseo.co.za)
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	if ( ! class_exists( 'BD_made_to_measure' ) ) {
		
		/**
		 * Localisation
		 **/
		load_plugin_textdomain( 'bd_made_to_measure', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

		class BD_made_to_measure {
			public function __construct() {

				require 'woocommerce-template.php';
				// called only after woocommerce has finished loading
				add_action( 'woocommerce_init', array( &$this, 'woocommerce_loaded' ) );
				// called after all plugins have loaded
				add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );
				// called just before the woocommerce template functions are included
				add_action( 'init', array( &$this, 'include_template_functions' ), 20 );
				// Enqueue admin script
				add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_assets' ) );
				// Enqueue frontend scripts
				add_action( 'wp_enqueue_scripts', array( &$this, 'frontend_product_scripts' ) );
				// Product type meta box
				add_action( 'add_meta_boxes', array( &$this, 'bd_product_type_meta_box' ) );
				// Save product type meta box
				add_action( 'save_post', array( &$this, 'save_bd_product_type' ) );
				// Add (ng) attributes to body tag - conditionally 
				add_filter( 'template_include', array( &$this, 'start_buffer_capture' ), 1 );
				// Ajax price calculation
				add_action( 'wp_ajax_bd_do_price_calcuation_ajax', array( &$this, 'bd_do_price_calcuation_ajax' ) );
				add_action( 'wp_ajax_nopriv_bd_do_price_calcuation_ajax', array( &$this, 'bd_do_price_calcuation_ajax' ) );
				// Add inputs inside woo product form
				add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'bd_product_size_inputs'), 21 );
				// Price Schema
				add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'bd_schema_price_wrapper'), 22 );
				// Add width/drop to cart item data
				add_filter( 'woocommerce_add_cart_item_data', array( &$this, 'bd_add_input_data_cart' ), 50, 3 );
				// Get the cart item from session
				add_filter( 'woocommerce_get_cart_item_from_session', array( &$this, 'bd_get_cart_item_from_session' ), 10, 2);
				// Get cart item data
				add_filter( 'woocommerce_get_item_data', array( &$this, 'bd_get_item_data' ), 10, 2 );
				// Add item data to cart
				add_filter( 'woocommerce_add_cart_item', array( &$this, 'bd_add_cart_item' ), 50, 2 );
				// Add order meta to cart
				add_action( 'woocommerce_add_order_item_meta', array( &$this, 'bd_add_order_item_meta' ), 10, 2 );
				// Save order meta
				add_action( 'woocommerce_order_item_meta', array( &$this, 'bd_order_item_meta' ), 10, 2 );
				// Remove price from prods without price table attached
				add_action('woocommerce_get_price_html', array( &$this, 'bd_get_price_html' ), 10, 2 );

				
				// indicates we are running the admin
				if ( is_admin() ) {
					// ...
				}
				
				// indicates we are being served over ssl
				if ( is_ssl() ) {
					// ...
				}
    
				// take care of anything else that needs to be done immediately upon plugin instantiation, here in the constructor
			}


			/**
			 * Add scripts used in the admin area
			 */	
			public function enqueue_admin_assets(){

			    // Enqueue custom js
			    wp_enqueue_script( 
			        'bd_admin_custom',
			        plugin_dir_url( __FILE__ ) . 'assets/js/admin/bd_admin_custom.js',
			        array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs' )
			    );

			    // Create globals here for the custom.js file
				wp_localize_script( 'bd_admin_custom', 'blinds', array(
					'ajax_url' => admin_url( 'admin-ajax.php' )
				));

			}


			/**
			 * Add scripts used on the front end
			 */	
			public function frontend_product_scripts () {
				global $post;

				// Angular and global scripts
				if ( is_product() ) {
					wp_enqueue_script( 'angular_js', plugin_dir_url( __FILE__ ) . 'assets/js/frontend/angular-1.4.6-min.js' );
					wp_enqueue_script( 'global_js', plugin_dir_url( __FILE__ ) . 'assets/js/frontend/global.js', array( 'angular_js', 'jquery', 'jquery-ui-core' ) );
					wp_enqueue_script( 'jquery_custom', plugin_dir_url( __FILE__ ) . 'assets/js/frontend/jquery_custom.js', array( 'jquery', 'jquery-ui-core' ) );
				}

				// Blinds 
				if ( is_product() && $this->check_bd_product_type() == 'Blinds') {
					wp_enqueue_script( 'blinds_js', plugin_dir_url( __FILE__ ) . 'assets/js/frontend/blinds.js', array( 'angular_js', 'global_js', 'jquery' ) );
				}

				// Curtains 
				if ( is_product() && $this->check_bd_product_type() == 'Curtains') {
					wp_enqueue_script( 'curtains_js', plugin_dir_url( __FILE__ ) . 'assets/js/frontend/curtains.js', array( 'angular_js', 'global_js', 'jquery' ) );
				}

				// Shutters 
				if ( is_product() && $this->check_bd_product_type() == 'Shutters') {
					wp_enqueue_script( 'shutters_js', plugin_dir_url( __FILE__ ) . 'assets/js/frontend/shutters.js', array( 'angular_js', 'global_js', 'jquery' ) );
				}

				// Create local variables here for the global.js file
				wp_localize_script( 'global_js', 'blinds', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'product_id' => $post->ID
				));
			}


			/**
			 * Check if a product has a price table attached to it
			 */	
			protected function product_has_price_table( $product_id ) {
				global $wpdb;
				$count = 0;
				$term_id = array();
				$category_terms = wp_get_post_terms( $product_id, 'product_cat' );
				foreach ( $category_terms as $term ) {
					$term_id[] = (int) $term->term_id;
				}
				if ( count( $term_id ) ) {
					$category_term_id = (string) implode( ', ', $term_id );
					$sql = "SELECT COUNT(term_id) count FROM `wp_woocommerce_cat_price_table` WHERE term_id IN ({$category_term_id})";
					$count = (int) $wpdb->get_var( $sql );
				}
				return ( $count > 0 );				
			}		


			/**
			 * Main Price calc function
			 */			
			public function bd_do_price_calcuation_ajax() {

				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

					$width = $_GET['width'];
					$drop = $_GET['drop'];
					$price_group = $_GET['selected_attribute'];

					$result = $this->calculate_blinds_price( $width, $drop, $price_group, false );
					$currency = html_entity_decode( get_woocommerce_currency_symbol() );
					$tax = new WC_Tax();
					$rates = $tax->find_rates( array( 'country' => 'ZA' ) );					

					echo json_encode( is_float( $result ) ? array( 'response' => 'OK', 'price' => $result, 'currency' => $currency, 'tax' => $rates ) : array( 'response' => 'ERROR', 'message' => $result ) );

				}

				wp_die();				
			}


			/**
			 * Insert the input fields on the product page
			 */	
			public function bd_product_size_inputs() {
				global $post, $wpdb;
				if ( $this->product_has_price_table( $post->ID ) ) {

					$addons = $this->get_product_addons();

					foreach ($addons as $addon) {
						$tax_term = $addon['slug'];
						$max_dimensions[$addon['slug']] = $wpdb->get_results( "SELECT MAX(width) as max_width, MIN(width) as min_width, MAX(height) as max_drop, MIN(height) as min_drop 
							FROM `wp_woocommerce_addon_price_table` 
							WHERE field_label = '$tax_term' ", OBJECT );
					}

					$output = '<div add-model class="input-wrap"><input data-dims="'.htmlspecialchars(json_encode($max_dimensions)).'" input-width-restraints ng-model="input_width" ng-change="bd_get_price(input_width, input_drop, selected_attribute, productQuantity)" type="number" name="wpti_x" placeholder="Enter Width" class="wpti-product-size" id="wpti-product-x">';
					$output .= '<input data-dims="'.htmlspecialchars(json_encode($max_dimensions)).'" input-drop-restraints ng-model="input_drop" ng-change="bd_get_price(input_width, input_drop, selected_attribute, productQuantity)" type="number" name="wpti_y" placeholder="Enter Drop" class="wpti-product-size" id="wpti-product-y">';
					$output .= '<div data-dims="'.htmlspecialchars(json_encode($max_dimensions)).'" class="button calculate-price" custom-validation ng-click="bd_get_price(input_width, input_drop, selected_attribute, productQuantity)">Calculate</div></div>';
					echo $output;
				}
			}


			/**
			 * Add html schema for product
			 */	
			public function bd_schema_price_wrapper() {
				global $woocommerce, $post;

				$product  = get_product( $post->ID );				
				$currency = get_woocommerce_currency();
				$stock    = ( $product->is_in_stock() ? 'InStock' : 'OutOfStock' );

				$output = '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<p itemprop="price" class="price">
						<span ng-cloak class="amount final-price" id="wpti-product-price">{{currencySymbol}} {{finalPrice}}</span>
					</p>
					<meta itemprop="priceCurrency" content="'.$currency.'" />
					<link itemprop="availability" href="http://schema.org/'.$stock.'" />
				</div>';

				echo $output;				
			}			

			/**
			 * Take care of anything that needs woocommerce to be loaded.  
			 * For instance, if you need access to the $woocommerce global
			 */
			public function woocommerce_loaded() {
				// ...
			}
			
			/**
			 * Take care of anything that needs all plugins to be loaded
			 */
			public function plugins_loaded() {
				// ...
			}
			
			/**
			 * Override any of the template functions from woocommerce/woocommerce-template.php 
			 * with our own template functions file
			 */
			public function include_template_functions() {
				//include( 'woocommerce-template.php' );
			}


			/**
			 * Display Metabox for different product types
			 **/
			public function bd_product_type_meta_box(){

			  add_meta_box(
			    'woocommerce-order-my-custom',
			    __( 'Product Type' ),
			    array($this, 'order_my_custom_prod' ),
			    'product',
			    'side',
			    'default'
			  );

			}


			/**
			 * Add fields to the metabox
			 **/
			public function order_my_custom_prod( $post ){
			  wp_nonce_field( 'save_bd_product_type', 'bd_product_type_nonce' );

			  woocommerce_wp_select(
			    array(
			      'id' => '_bd_product_type',
			      'label' => __('Type: ', 'woocommerce'),
			      'options' => array(
			      	'not_set' => __('Please Select', 'woocommerce'),
			        'Blinds' => __('Blinds', 'woocommerce'),
			        'Shutters' => __('Shutters', 'woocommerce'),
			        'Curtains' => __('Curtains', 'woocommerce')
			      )
			    )
			  );
			}


			/**
			 * Save Product Type
			 **/
			public function save_bd_product_type( $post_id ) {

			  // Check if nonce is set
			  if ( ! isset( $_POST['bd_product_type_nonce'] ) ) {
			    return $post_id;
			  }

			  if ( ! wp_verify_nonce( $_POST['bd_product_type_nonce'], 'save_bd_product_type' ) ) {
			    return $post_id;
			  }

			  // Check that the logged in user has permission to edit this post
			  if ( ! current_user_can( 'edit_post' ) ) {
			    return $post_id;
			  }

			  $bd_product_type = sanitize_text_field( $_POST['_bd_product_type'] );
			  update_post_meta( $post_id, '_bd_product_type', $bd_product_type );
			}


			/**
			 * Check Product Type
			 **/
			public function check_bd_product_type() {
				global $post;
				return get_post_meta( $post->ID,  '_bd_product_type', true );
			}

			
			/**
			 * Add angular attributes to body tag
			 * depending on product type
			 **/
			public function start_buffer_capture( $template ) {

				if ( is_product() && $this->check_bd_product_type() !== '' ) {
					// Start Page Buffer
					ob_start( array( &$this, 'end_buffer_capture' ) );
				}
			 	return $template;
			}


			/**
			 * Add angular attributes to body tag
			 * Depending on product type
			 **/
			public function end_buffer_capture( $buffer ) {

				if ( is_product() && $this->check_bd_product_type() == 'Blinds' ) {
			 		return str_replace( '<body', '<body ng-app="bd_made_to_measure" ng-controller="blindsCtrl"', $buffer );
			 	}

				elseif ( is_product() && $this->check_bd_product_type() == 'Shutters' ) {
			 		return str_replace( '<body', '<body ng-app="bd_made_to_measure" ng-controller="shuttersCtrl"', $buffer );
			 	}

				elseif ( is_product() && $this->check_bd_product_type() == 'Curtains' ) {
			 		return str_replace( '<body', '<body ng-app="bd_made_to_measure" ng-controller="curtainsCtrl"', $buffer );
			 	}

			 	else {
			 		return str_replace( '<body', '<body ng-app="bd_made_to_measure"', $buffer );
			 	}			 				 		
			}


			/**
			 * Add input data to the cart item
			 **/
			public function bd_add_input_data_cart( $cart_item = array(), $product_id = 0 ) {
				if ( $this->product_has_price_table( $product_id ) ) {
					$cart_item['wpti_options'] = array(
						'x' => array_key_exists( 'wpti_x', $_REQUEST ) ? $_REQUEST['wpti_x'] : 0.01,
						'y' => array_key_exists( 'wpti_y', $_REQUEST ) ? $_REQUEST['wpti_y'] : 0.01
					);
				}
				return $cart_item;
			}


			/**
			 * Get the cart item from the user session
			 **/
			public function bd_get_cart_item_from_session( $cart_item, $cart_item_data ) {
				if ( isset( $cart_item_data['wpti_options'] ) ) {
					$cart_item['wpti_options'] = $cart_item_data['wpti_options'];
					$this->set_price( $cart_item );
				}
				return $cart_item;
			}


			/**
			 * Get the item data (to be used in the cart)
			 **/
			public function bd_get_item_data( $item_data, $cart_item ) {
				if ( $this->product_has_price_table( $cart_item['product_id'] ) ) {
					$item_data[] = array(
						'name' => $this->get_size_label(),
						'value' =>  $this->get_size_string( $cart_item )
					);
				}
				return $item_data;
			}


			/**
			 * helper method to retrieve order size in string
			 **/
			protected function get_size_label() {
				$x_name = 'Width';
				$y_name = 'Drop';
				$label = '<br>' . $x_name . " x " . $y_name;
				return $label;
			}


			/**
			 * helper method to retrieve size information in string
			 **/
			protected function get_size_string(&$cart_item) {
				$x_value = number_format( $cart_item['wpti_options']['x'] );
				$y_value = number_format( $cart_item['wpti_options']['y'] );
				$x_metric = 'mm';
				$y_metric = 'mm';
				$string = $x_value ." ". $x_metric . " x " . $y_value . " " . $y_metric;
				return $string;
			}


			/**
			 * Add the item to the cart
			 **/
			public function bd_add_cart_item( $cart_item, $cart_item_key ) {
				$this->set_price( $cart_item );
				return $cart_item;
			}


			/**
			 * Add the meta data to the order
			 **/
			public function bd_add_order_item_meta( $item_id, $values ) {
				if ( $this->product_has_price_table( $values['product_id'] ) ) {
					woocommerce_add_order_item_meta( $item_id, $this->get_size_label(), $this->get_size_string( $values ) );
				}
			}


			/**
			 * Add input data to cart item (meta)
			 **/
			public function bd_order_item_meta( $item_meta, $cart_item ) {
				if ( $this->product_has_price_table( $cart_item['product_id'] ) ) {
					$item_meta->add( $this->get_size_label(), $this->get_size_string( $cart_item ) );
				}
			}																	


			/**
			 * Set price on the cart item
			 **/
			protected function set_price( &$cart_item ) {
				if ( $this->product_has_price_table( $cart_item['product_id'] ) ) {
					// Remove empty variation names from variation array
					if ( array_key_exists( 'variation', $cart_item ) && is_array( $cart_item['variation'] ) && count( $cart_item['variation'] ) ) {

						$tmp_cart = array();

						foreach ( $cart_item['variation'] as $key => $value ) {
							if ( !$value ) continue;

							if ( strpos( $key, 'attribute_' ) !== false ) {
								$key = substr( $key, strpos( $key, 'pa_' ) );
							}

							$tmp_cart[ $key ] = $value;
						}
						// Replace the variations array with the modified one
						$cart_item['variation'] = $tmp_cart;
						// Get the price group
						$group = array_keys( $cart_item['variation'] )[0];
					}				

					$wpti  = &$cart_item['wpti_options'];
					// Get the price
					$price = $this->calculate_blinds_price( $wpti['x'], $wpti['y'], $group, false );
					// Set the price on the cart item
					$cart_item['data']->set_price( $price );
				}	
			}


			/**
			 * Don't show defualt price if product uses price table
			 */	
			public function bd_get_price_html($price, $instance) {
				return $this->product_has_price_table($instance->id) ? '' : $price;
			}			


			/**
			 * Calculate price for blinds
			 **/
			public function calculate_blinds_price( $width, $drop, $price_group, $options ) {
				global $wpdb;
				if ( !$width || !$drop || !$price_group ) continue;

				$price = $wpdb->get_var( $wpdb->prepare(
					"SELECT price FROM `wp_woocommerce_addon_price_table`
					WHERE field_label = %s AND choice = %s
					ORDER BY ABS(width - %d) ASC, ABS(height - %d) ASC",
					$price_group, $price_group, $width, $drop )
				);

				$price = $this->add_price_markup( $price );

				return $price ? ( float )$price : $price = $wpdb->last_error;				
			}


			/**
			 * Add markup to product price
			 */	
			private function add_price_markup( $price ) {
				// Markup Logic Here
				return $price;
			}

			public function normalize_taxonomy_name($name, $prefix = 'pa_') {
				$prefix_length = strlen($prefix);

				if ($prefix_length > 0 && strpos($name, $prefix) == 0) {
					$name = substr($name, $prefix_length);
				}

				return ucwords(str_replace(array('-', '_'), ' ', $name));
			}

			public function fill_taxonomy_terms($terms = array(), &$target) {
				foreach ($terms as $term) {
					$target[$term->taxonomy]['terms'][] = (object) array(
						'term_id' => $term->term_id,
						'name' => $term->name,
						'slug' => $term->slug
					);
				}
			}			

			public function get_product_addons() {
				global $woocommerce;

				$addons = array();
				$version_comparison = version_compare($woocommerce->version, '2.1');
				$wc_get_attribute_taxonomy_names_exists = function_exists('wc_get_attribute_taxonomy_names');
				$attr_tax = ($version_comparison >= 0 && $wc_get_attribute_taxonomy_names_exists)
					? wc_get_attribute_taxonomy_names() // from 2.1
					: (method_exists($woocommerce, 'get_attribute_taxonomy_names')
						? $woocommerce->get_attribute_taxonomy_names() // pre 2.1
						: array());

				foreach ($attr_tax as $taxonomy) {
					$name = $this->normalize_taxonomy_name($taxonomy);
					$addons[$taxonomy] = array(
						'normalized_name' => $name,
						'slug' => $taxonomy,
						'terms' => array()
					);
				}

				$terms = get_terms($attr_tax);
				$this->fill_taxonomy_terms($terms, $addons);
				return $addons;
			}				 									

		}

		// finally instantiate our plugin class and add it to the set of globals
		$GLOBALS['bd_made_to_measure'] = new BD_made_to_measure();
	}
}