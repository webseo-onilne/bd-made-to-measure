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

				// Enqueue script
				add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_admin_assets' ) );

				
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
			        'wcbp_custom-js',
			        plugin_dir_url( __FILE__ ) . 'assets/js/wcbp_custom.js',
			        array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs' )
			    );

			    // Create globals here for the custom.js file
				wp_localize_script( 'wcbp_custom-js', 'blinds', array(
					'ajax_url' => admin_url( 'admin-ajax.php' )
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

		}

		// finally instantiate our plugin class and add it to the set of globals
		$GLOBALS['bd_made_to_measure'] = new BD_made_to_measure();
	}
}