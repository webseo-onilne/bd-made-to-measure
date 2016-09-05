<?php 

/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No script kiddies please!' );
};


// Show success message when settings are saved
if( isset( $_GET['settings-updated'] ) ) { ?>
    <div id="message" class="updated">
        <p><strong><?php _e('Settings saved.') ?></strong></p>
    </div>
<?php }
?>

<h3 class="page-title">Settings</h3>

<hr />

<form method="post" action="options.php">

    <?php settings_fields( 'dimension-settings' ); ?>
    <?php do_settings_sections( 'dimension-settings' ); ?>

    <table class="form-table">

        <tr valign="top">
	        <th scope="row">Error Message for incorrect <strong>Maximum Width</strong></th>
	        <td><input type="text" name="max_width_error_message" value="<?php echo esc_attr( get_option('max_width_error_message') ); ?>" /></td>
        </tr>

        <tr valign="top">
            <th scope="row">Error Message for incorrect <strong>Minimum Width</strong></th>
            <td><input type="text" name="min_width_error_message" value="<?php echo esc_attr( get_option('min_width_error_message') ); ?>" /></td>
        </tr>

        <tr valign="top">
            <th scope="row">Error Message for incorrect <strong>Maximum Drop</strong></th>
            <td><input type="text" name="max_drop_error_message" value="<?php echo esc_attr( get_option('max_drop_error_message') ); ?>" /></td>
        </tr>

        <tr valign="top">
            <th scope="row">Error Message for incorrect <strong>Minimum Drop</strong></th>
            <td><input type="text" name="min_drop_error_message" value="<?php echo esc_attr( get_option('min_drop_error_message') ); ?>" /></td>
        </tr>        

    </table>

    <?php submit_button(); ?>

</form>