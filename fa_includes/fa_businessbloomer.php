<?php

/*
 * @snippet       WooCommerce: Show Product Published Date
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 5
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

add_action('woocommerce_single_product_summary', 'bloomer_echo_product_date', 25 );

function bloomer_echo_product_date() {
   if ( is_product() ) {
      echo the_date( '', '<span class="single_product_date_published">Updated: ', '</span>', false );
   }
}

// Change the date format: https://codex.wordpress.org/Function_Reference/the_date

/**
 * @snippet       Hide Price If Out of Stock @ WooCommerce Frontend
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 6
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

add_filter( 'woocommerce_get_price_html', 'bbloomer_hide_price_if_out_stock_frontend', 9999, 2 );

function bbloomer_hide_price_if_out_stock_frontend( $price, $product ) {
   if ( is_admin() ) return $price; // BAIL IF BACKEND
   if ( ! $product->is_in_stock() ) {
      $price = apply_filters( 'woocommerce_empty_price_html', '', $product );
   }
   return $price;
}

/**
 * @snippet       Save & Display Order Total Weight - WooCommerce Order
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.6.4
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

add_action( 'woocommerce_checkout_update_order_meta', 'bbloomer_save_weight_order' );

function bbloomer_save_weight_order( $order_id ) {
    $weight = WC()->cart->get_cart_contents_weight();
    update_post_meta( $order_id, '_cart_weight', $weight );
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'bbloomer_delivery_weight_display_admin_order_meta', 10, 1 );

function bbloomer_delivery_weight_display_admin_order_meta( $order ) {
    echo '<p><strong>Order Weight:</strong> ' . get_post_meta( $order->get_id(), '_cart_weight', true ) . get_option( 'woocommerce_weight_unit' ) . '</p>';
}
