<?php
/**
Plugin Name: WooCommerce Order Email Markup
Plugin URI: http://wordpress.org/plugins/wc-order-email-markup
Description: Automatcially add email markup <a href="https://schema.org" target="_blank">schema.org</a> to your order emails so it improves your relation with your client and build trust for your website. Self evident but WooCommerce is required.
Author: Juan Martínez Alonso
Version: 1.0.0
Author URI: https://internaftis.com/
*/

/*REQUIRES WOOCOMMERCE*/

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

/*END REQUIRES WOOCOMMERCE*/


/*HOOK ORDER EMAIL*/

add_action( 'woocommerce_email_before_order_table', 'WOEM_schema_generator', 20, 4 );

function WOEM_schema_generator($order, $sent_to_admin, $plain_text, $email ){
	if ( $email->id == 'customer_processing_order' || $email->id == 'customer_completed_order' || $email->id == 'customer_invoice' || $email->id == 'new_order' ){
		
		
		$address    = $order->get_formatted_billing_address();
		$shipping   = $order->get_formatted_shipping_address();
		?>
		<script type="application/ld+json">
		{
		  "@context": "http://schema.org",
		  "@type": "Order",
		  "merchant": {
			"@type": "Organization",
			"name": "<?php echo get_bloginfo( 'name', 'display' ); ?>"
		  },
		  "orderNumber": "<?php echo wp_kses_post($order->get_order_number()); ?>",
		  "orderStatus": "http://schema.org/OrderProcessing",
		  "priceCurrency": "<?php echo wp_kses_post($order->get_currency()); ?>",
		  "price": "<?php echo wp_kses_post($order->get_total()); ?>",
		  <?php 
		  WOEM_schema_order_items($order);
		  ?>
		  "orderStatus": "http://schema.org/OrderProcessing",
		  "orderDate": "<?php echo $order->get_date_created(); ?>",
		  "customer": {
			"@type": "Person",
			"name": "<?php echo wp_kses_post($order->get_billing_first_name());?>"
		  },
		  "billingAddress": {
			"@type": "PostalAddress",
			"name": "<?php echo wp_kses_post( $order->get_billing_first_name() );?>",
			"streetAddress": "<?php echo wp_kses_post( $order->get_billing_address_1() );?>",
			"addressLocality": "<?php echo wp_kses_post( $order->get_billing_city() );?>",
			"addressRegion": "<?php echo wp_kses_post( $order->get_billing_state() );?>",
			"addressCountry": "<?php echo wp_kses_post( $order->get_billing_country() );?>"
		  }
		}
		</script>
		<?php
	}
}

function WOEM_schema_order_items($order){
	?>
		"acceptedOffer": [
	<?php
	foreach ( $items as $item_id => $item ) :
		$product = $item->get_product();
		$product_name = $item->get_name();
		
		if ( is_object( $product ) ) {
			$sku           = $product->get_sku();
			$image         = $product->get_image( $image_size );
		}
		
		$qty = $item->get_quantity();
		
		?>
			{
			  "@type": "Offer",
			  "itemOffered": {
				"@type": "Product",
				"name": "<?php echo wp_kses_post( $product_name ); ?>",
				"sku": "<?php echo wp_kses_post( ' (#' . $sku . ')' ); ?>",
				"url": "http://www.amazon.com/Samsung-XE303C12-A01US-Chromebook-Wi-Fi-11-6-Inch/dp/B009LL9VDG/",
				"image": "<?php echo wp_get_attachment_url( $product->get_image_id() ); ?>"
			  },
			  "price": "<?php  echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>",
			  "priceCurrency": "<?php echo $order->get_currency(); ?>",
			  "eligibleQuantity": {
				"@type": "QuantitativeValue",
				"value": "<?php esc_html( $qty ); ?>"
			  },
			  "seller": {
				"@type": "Organization",
				"name": "<?php echo get_bloginfo( 'name', 'display' ); ?>"
			  }
			},
		<?php
	endforeach;
	?>
		],
	<?php 
}

/*END HOOK ORDER EMAIL*/

?>