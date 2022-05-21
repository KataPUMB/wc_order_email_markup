<?php
/**
Plugin Name: WooCommerce Order Email Markup
Plugin URI: http://wordpress.org/plugins/wc-order-email-markup
Description: Automatcially add email markup schema to your order emails so it improves your relation with your client and build trust for your website. Self evident but WooCommerce is required.
Author: Juan Martínez Alonso
Version: 1.0.0
Author URI: https://internaftis.com/
*/

add_action('admin_init', 'WOEM_activate');
function WOEM_activate(){
	//requires WooCommerce
	if( !is_plugin_active('woocommerce/woocommerce.php') && current_user_can( 'activate_plugins')){
		add_action('admin_notices', 'WOEM_woocommerce_unactive_error');
        deactivate_plugins( plugin_basename( __FILE__ ) );
		
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
	}
}
function WOEM_woocommerce_unactive_error(){
    ?>
	<div class="error" style="padding-top: 1rem;padding-bottom: 1.5rem;">
		<h1>WooCommerce Order Email Markup can't be activated</h1>
		<p style="margin-top: 1em;margin-bottom: 2em;">Sorry but WOEM requires WooCommerce to be installed and active.</p>
		<a class="button button-primary" href="<?php echo get_site_url(); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=woocommerce">Download WooCommerce</a>
	</div>
	<?php
}
?>