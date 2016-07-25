<?php
/*
Plugin Name: Blinds Direct - Made to Measure
Plugin URI: http://www.blindsdirect.co.za
Description: Tools for Blindsdirect.co.za
Author: Blinds Direct
Author URI: http://www.blindsdirect.co.za
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
				// called only after woocommerce has finished loading
				add_action( 'woocommerce_init', array( &$this, 'woocommerce_loaded' ) );
				
				// called after all plugins have loaded
				add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );
				
				// called just before the woocommerce template functions are included
				add_action( 'init', array( &$this, 'include_template_functions' ), 20 );

				// Enqueue admin script
				add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_assets' ) );

				// Enqueue angular on frontend
				add_action( 'wp_enqueue_scripts', array( &$this, 'frontend_product_scripts' ) );

				// Product type meta box
				add_action( 'add_meta_boxes', array( &$this, 'bd_product_type_meta_box' ) );

				// Save product type meta box
				add_action( 'save_post', array( &$this, 'save_bd_product_type' ) );

				add_filter( 'template_include', array( &$this, 'start_buffer_capture' ), 1 );

				
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

			public function frontend_product_scripts () {
				global $post;

				// Angular and global scripts
				if ( is_product() ) {
					wp_enqueue_script( 'angular_js', plugin_dir_url( __FILE__ ) . 'assets/js/frontend/angular-1.4.6-min.js' );
					wp_enqueue_script( 'global_js', plugin_dir_url( __FILE__ ) . 'assets/js/frontend/global.js', array( 'angular_js', 'jquery', 'jquery-ui-core' ) );
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
				include( 'woocommerce-template.php' );
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
			 	ob_start( array(&$this, 'end_buffer_capture' ) );  // Start Page Buffer
			 	return $template;
			}

			/**
			 * Add angular attributes to body tag
			 * Depending on product type
			 **/
			public function end_buffer_capture( $buffer ) {

				if ( is_product() && $this->check_bd_product_type() == 'Blinds' ) {
			 		return str_replace( '<body','<body ng-app="bd_made_to_measure" ng-controller="blindsCtrl"', $buffer );
			 	}

				if ( is_product() && $this->check_bd_product_type() == 'Shutters' ) {
			 		return str_replace( '<body','<body ng-app="bd_made_to_measure" ng-controller="shuttersCtrl"', $buffer );
			 	}

				if ( is_product() && $this->check_bd_product_type() == 'Curtains' ) {
			 		return str_replace( '<body','<body ng-app="bd_made_to_measure" ng-controller="curtainsCtrl"', $buffer );
			 	}			 				 		
			}						

		}

		// finally instantiate our plugin class and add it to the set of globals
		$GLOBALS['bd_made_to_measure'] = new BD_made_to_measure();
	}
}