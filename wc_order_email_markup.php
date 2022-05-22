<?php
/**
Plugin Name: Order Email Markup for Woocommerce
Plugin URI: http://wordpress.org/plugins/Order-Email-Markup-for-Woocommerce
Description: Automatcially add email markup <a href="https://schema.org" target="_blank">schema.org</a> to your order emails so it improves your relation with your client and build trust for your website. Self evident but WooCommerce is required.
Author: Juan Martínez Alonso
Version: 1.0.0
Author URI: https://profiles.wordpress.org/juanmartinezalonso/
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

add_action( 'woocommerce_email_before_order_table', 'WOEM_schema_generator', 10, 4 );

function WOEM_schema_generator($order, $sent_to_admin, $plain_text, $email ){
	if ( $email->id == 'customer_processing_order' || $email->id == 'customer_completed_order' || $email->id == 'customer_invoice' || $email->id == 'new_order' ){
		
		
		$address    = $order->get_formatted_billing_address();
		$shipping   = $order->get_formatted_shipping_address();
		
		$order_status = $order->get_status();
		$schema_status = '';
		switch($order_status){
			case 'pending':
				$schema_status = 'http://schema.org/OrderPaymentDue';
				break;
			case 'on-hold':
				$schema_status = 'http://schema.org/OrderProcessing';
				break;
			case 'processing':
				$schema_status = 'http://schema.org/OrderProcessing';
				break;
			case 'completed':
				$schema_status = 'http://schema.org/OrderInTransit';
				break;
			case 'cancelled':
				$schema_status = 'http://schema.org/OrderCancelled';
				break;
			case 'refunded':
				$schema_status = 'http://schema.org/OrderReturned';
				break;
			case 'failed':
				$schema_status = 'http://schema.org/OrderProblem';
				break;
			default:
				$schema_status = 'http://schema.org/OrderProcessing';
				break;
		}
		?>
		<script type="application/ld+json">
		{
		  "@context": "http://schema.org",
		  "@type": "Order",
		  "broker": {
			"@type": "LocalBusiness",
			"name": "<?php echo get_bloginfo( 'name', 'display' ); ?>"
		  },
		  "orderNumber": "<?php echo wp_kses_post($order->get_order_number()); ?>",
		  "OrderStatus": "<?php echo wp_kses_post( $schema_status ); ?>",
		  "orderDate": "<?php echo $order->get_date_created(); ?>",
		  "paymentStatus": "https://schema.org/PaymentComplete",
		  <?php 
		  WOEM_schema_order_items($order);
		  ?>
		  "url": "<?php echo wc_get_endpoint_url( 'view-order', $order->get_id(), get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>",
		  "potentialAction": {
			"@type": "ViewAction",
			"url": "<?php echo wc_get_endpoint_url( 'view-order', $order->get_id(), get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>"
		  },
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
		"orderedItem": [
	<?php
	$order_items = $order->get_items();
	$numItems = count($order_items);
	$i = 0;
	
	foreach ( $order_items as $item_id => $item ) :
		$product = $item->get_product();
		$product_name = $item->get_name();
		
		if ( is_object( $product ) ) {
			$sku           = $product->get_sku();
			$url 		   = get_permalink( $product->get_id() );
			$image         = $product->get_image( $image_size );
			$price		   = $product->get_price();
		}
		
		$qty = $item->get_quantity();
		
		?>
			{
			  "@type": "OrderItem",
			  "orderQuantity": "<?php echo wp_kses_post( $qty ); ?>",
			  "orderedItem": {
				"@type": "Product",
				"name": "<?php echo wp_kses_post( $product_name ); ?>",
				<?php
				if ($sku!=null && $sku!=''):
				?>
				"sku": "<?php echo wp_kses_post( $sku ); ?>",
				<?php
				endif;
				?>
				"url": "<?php echo wp_kses_post( $url ); ?>",
				"image": "<?php echo wp_get_attachment_url( $product->get_image_id() ); ?>"
			  },
			  "price": "<?php  echo wp_kses_post( $price ); ?>",
			  "priceCurrency": "<?php echo $order->get_currency(); ?>",
			  "seller": {
				"@type": "LocalBusiness",
				"name": "<?php echo get_bloginfo( 'name', 'display' ); ?>"
			  }
		<?php
		  if(++$i === $numItems) {
			?>
			}
			<?php
		  }else{
			?>
			},
			<?php
		  }
	endforeach;
	?>
		],
	<?php 
}

/*END HOOK ORDER EMAIL*/

?>