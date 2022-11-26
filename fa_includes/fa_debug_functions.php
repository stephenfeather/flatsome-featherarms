<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

function wpdb() {
    global $wpdb;
    return $wpdb;
}

function var_dump_database() {
    var_dump(wpdb()->num_queries , wpdb()->queries);
}

function add_custom_tracer($tracer_name) {
    if (extension_loaded('newrelic')) { // Ensure PHP agent is available
        newrelic_add_custom_tracer($tracer_name);
        return true;
    }
    return false;
}

add_action('shutdown', function() {
   if(WP_DEBUG && current_user_can('administrator')) {
       var_dump_database();
   }
});


