<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//function avoid_term_recounts( $data, $postarr ) {
//  wp_defer_term_counting( true );
//  wp_defer_comment_counting (true );
//
//  return $data;
//}

//function woo_avoid_term_recounts( $recountterms ) {
//  return false;
//}

add_filter('woocommerce_product_recount_terms', 'woo_avoid_term_recounts');
add_filter('wp_insert_post_data', 'avoid_term_recounts', 99, 2);

if (! wp_next_scheduled ('wpisp_performance_daily')) {
  wp_schedule_event(strtotime('01:00'), 'daily', 'wisp_performance_daily');
}

add_action('pmxi_before_xml_import', 'spro_avoidrecountsonimport');
add_action('pmxi_after_xml_import', 'spro_recount_items');

//function spro_avoidrecountsonimport() {
//  add_filter('woocommerce_product_recount_terms', '__return_false');
//}

//function spro_recount_items() {
//  wc_recount_all_terms();
//}

