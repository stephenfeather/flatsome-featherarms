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

