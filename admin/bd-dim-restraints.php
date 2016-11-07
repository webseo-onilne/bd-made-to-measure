<?php 

/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No script kiddies please!' );
};
global $wpdb;

// Show success message when settings are saved
if( isset( $_GET['settings-updated'] ) ) { ?>
    <div id="message" class="updated">
        <p><strong><?php _e('Settings saved.') ?></strong></p>
    </div>
<?php }
?>

<h3 class="page-title">Edit max/min Dimensions</h3>

<hr />
<?php

$addons = BD_made_to_measure_admin::get_product_addons();

