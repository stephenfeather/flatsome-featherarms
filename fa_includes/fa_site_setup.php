<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'woocommerce_subcategory_count_html', '__return_false' );
add_filter( 'woocommerce_background_image_regeneration', '__return_false' );

// Return 404 for bad queries
//add_action( 'template_redirect', 'my_lite_page_template_redirect' );

function my_lite_page_template_redirect() {
  global $wp_query;
  if ( ! (int) $wp_query->found_posts > 0 ) {
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
  }
}

function my_page_template_redirect() {
  global $wp_query, $wp;
  // this get an array of the query vars in the url
  parse_str( parse_url ( add_query_arg( array() ), PHP_URL_QUERY ), $qv);
  if ( ! empty( $qv ) ) { // if there are some query vars in the url
    $queried = array_keys( $qv ); // get the query vars name
    $valid = $wp->public_query_vars; // this are the valid query vars accepted by WP
    // intersect array to retrieve the queried vars tha are included in the valid vars
    $good = array_intersect($valid, $queried); 
    // if there are no good query vars or if there are at least one not valid var
//  error_log("Valid: ". $valid);
//  error_log("Queried: ". $queried);
  if ( empty($good) || count($good) !== count($queried)  ) {
     $wp_query->set_404();
     status_header(404);
     nocache_headers();
    }
  }
}

//add_action( 'template_redirect', 'my_page_template_redirect' );

// Generate IMG Urls through imagekit with params appended
function fa_appendImageSRC($image, $attachment_id, $size, $icon) {
        //write_log(get_page_template());
        if (! $image) {
                //write_log("Empty image: $image, $attachment_id, $size");
                return $image;
        }
        //write_log("image[0]: {$size}");
        if (empty($attachment_id)) return $image;
        //write_log("image[0]: {$image[0]}");
        if (is_string($size)) {
        //      write_log("size: {$size}");
        } else {
                //write_log("size(w): {$size[0]}");
                //write_log("size(h): {$size[1]}");
        }

        if (isset($size) && $size === 'full') {
                return $image;
        }

        //write_log("attachment_id: {$attachment_id}");

        if (isset($size) && $size === 'woocommerce_thumbnail') {
                $image[0] .= '?tr=n-woocommerce_thumbnail';
                $image[1] = 247;
                $image[2] = 247;
                //write_log("modified image: {$image[0]}");
        } else if (isset($size) && $size === 'woocommerce_single') {
                $image[0] .= '?tr=n-woocommerce_single';
                $image[1] = 510;
                $image[2] = 510;
                //write_log("modified image: {$image[0]}");
        } else if (isset($size) && $size === 'woocommerce_gallery_thumbnail') {
                $image[0] .= '?tr=n-woocommerce_gallery_thumbnail';
                $image[1] = 100;
                $image[2] = 100;
                //write_log("modified image: {$image[0]}");
        } else if (isset($size) && $size === 'sidebar_tower') {
                $image[0] .= '?tr=n-sidebar_tower';
                $image[1] = 330;
                $image[2] = 330;
        } else if (isset($size) && $size === 'large') {
                $image[0] .= '?tr=n-large';
                $image[1] = 1024;
                $image[2] = 1024;
                //write_log("modified image: {$image[0]}");
        } else if (isset($size) && is_array($size)) {
                $image[0] .= '?tr:w-' . $size[0] . ',h-' . $size[1] . ',q-80,cm-pad_resize,bg-FFFFFF,fo-auto';
                $image[1] = $size[0];
                $image[2] = $size[1];
                //write_log("size array: {$image[0]}");
        }

//      write_log("-- end wp_get_attachment_image_src filter --");
    return $image;
}

add_filter('wp_get_attachment_image_src', 'fa_appendImageSRC', 99, 4);

function fa_responsive_img($html, $post_id, $post_thumbnail_id, $size, $attr) {
        write_log("-- start post thumbnail html filter --");
        write_log("html: {$html}");
        write_log("post_id: {$post_id}");
        write_log("post_thumbnail_id: {$post_thumbnail_id}");
        write_log("size: {$size}");
        write_log("attr: {$attr}");
        write_log("-- end post thumbnail html filter --");
}
//add_filter( 'post_thumbnail_html', 'fa_responsive_img', 5, 5 );

function fa_woocomerce_single_product_image($html, $post_id) {
        write_log("-- start woocomerce_single_product_image filter --");
        write_log("html: {$html}");
        write_log("post_id: {$post_id}");
        return $html;
}

//add_filter('woocommerce_single_product_image_thumbnail_html', 'fa_woocomerce_single_product_image', 10, 4);

function wc_add_custom_fields() {
     echo 'CUSTOMIZE CONTENT'.get_post_meta(get_the_ID(), "CUSTOMIZE CONTENT", true);
     echo 'CUSTOMIZEFIELD'.get_field(“CUSTOMIZEFIELD”);
}
//add_action( 'woocommerce_single_product_summary', 'wc_add_custom_fields', 21 );

function flatsome_wc_get_gallery_image_html( $attachment_id, $main_image = false, $size = 'woocommerce_single' ) {
        //write_log("Custom html. Main_image: {$main_image} Size: {$size}");
        $parent_title = "";
        $parent = get_post_ancestors( $attachment_id );

        if (($parent) && is_array($parent)) {
                //write_log(get_the_title($parent[0]));
                $parent_title = get_the_title($parent[0]);
        }


        //write_log("parent_title: {$parent_title}");

        $gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
    $thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array(            $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
    $image_size        = apply_filters( 'woocommerce_gallery_image_size', $size );
    $full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
    $thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
    $full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
    $image             = wp_get_attachment_image( $attachment_id, $image_size, false, array(
      'title'                   => $parent_title, //get_post_field( 'post_title', $attachment_id ),
      'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
      'data-src'                => $full_src[0],
      'data-large_image'        => $full_src[0],
      'data-large_image_width'  => $full_src[1],
      'data-large_image_height' => $full_src[2],
      'class'                   => $main_image ? 'wp-post-image skip-lazy' : 'skip-lazy', // skip-lazy, blacklist for Jetpack's lazy load.
    ) );
    $image_wrapper_class = $main_image ? 'slide first' : 'slide';
    $image_html = $main_image ? $image : '<a href="' . esc_url( $full_src[0] ) . '">' . $image . '</a>';
    return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="woocommerce-product-gallery__image '.$image_wrapper_class.'">' . $image_html . '</div>';
}

// Remove Unused Assets
function fa_remove_dashicons_junk() {
    if ( ! is_user_logged_in() ) {
          wp_dequeue_style('dashicons');
    }
}
add_action( 'wp_enqueue_scripts', 'fa_remove_dashicons_junk' );

add_action( 'wp_enqueue_scripts', 'crunchify_enqueue_scripts_styles' );

function crunchify_enqueue_scripts_styles() {

     wp_dequeue_script( 'devicepx' );

}

if (class_exists('acf') && class_exists('WooCommerce')) {
        add_filter('woocommerce_product_tabs', function($tabs) {
                global $post, $product;  // Access to the current product or post

                $custom_tab_title = "Specs";

                if (!empty($custom_tab_title)) {
                        $tabs['awp-' . sanitize_title($custom_tab_title)] = [
                                'title' => $custom_tab_title,
                                'callback' => 'awp_custom_woocommerce_tabs',
                                'priority' => 10
                        ];
                }
                return $tabs;
        });

        function awp_custom_woocommerce_tabs($key, $tab) {
                global $post;
                $post_id = $post->ID;
                $manufacturer = get_field('manufacturer', $post->ID);
                $model = get_field('model', $post->ID);
                $action = get_field('action', $post->ID);
                $capacity = get_field('capacity', $post->ID);
                $finish = get_field('finish', $post->ID);
                $sights = get_field('sights', $post->ID);
                $barrel_length = get_field('barrel_length', $post->ID);
?>
<!-- <?php echo $post_id; ?></h1> -->
<b>Manufacturer: </b><?php echo $manufacturer; ?><br/>
                <b>Model: </b><?php echo $model; ?><br/>
                <b>Action: </b><?php echo $action; ?><br/>
                <b>Capacity: </b><?php echo $capacity; ?><br/>
                <b>Finish: </b><?php echo $finish; ?><br/>
                <b>Sights: </b><?php echo $sights; ?><br/>
                <b>Barrel Length: </b><?php $barrel_length; ?><br/> <?php

        }
}

// Remove unneeded Image Sizes
function adjust_image_sizes() {
  remove_image_size('2048x2048');
  remove_image_size('1536x1536');
  add_image_size('thumbnail',0,0, false);
  add_image_size('medium',0,0, false);
  add_image_size('sidebar_tower',0,0, false);
  add_image_size('large',0,0, false);
  add_image_size('woocommerce_thumbnail',0,0, false);
  add_image_size('woocommerce_single',0,0, false);
  add_image_size('woocommerce_gallery_thumbnail',0,0, false);
  add_image_size('shop_catalog',0,0, false);
  add_image_size('shop_single',0,0, false);
  add_image_size('shop_thumbnail',0,0, false);
}
add_action('init', 'adjust_image_sizes');

add_filter('intermediate_image_sizes', function($sizes) {
  return array_diff($sizes, ['medium_large']);  // Medium Large (768 x 0)
});

add_filter( 'woocommerce_get_catalog_ordering_args', 'fa_first_sort_by_stock_amount', 9999 );

function fa_first_sort_by_stock_amount( $args ) {
   $args['orderby'] = 'meta_value';
   $args['order'] = 'ASC';
   $args['meta_key'] = '_stock_status';
   return $args;
}

function adjust_action_scheduler_log_retention() {
        return 3 * DAY_IN_SECONDS;
}

add_filter('action_scheduler_retention_period', 'adjust_action_scheduler_log_retention');


//add_filter( 'woocommerce_admin_disabled', '__return_true' );
add_filter( 'jetpack_just_in_time_msgs', '__return_false', 99 );
add_filter( 'jetpack_sharing_counts', '__return_false', 99 );
add_filter( 'jetpack_implode_frontend_css', '__return_false', 99 );
add_filter( 'jetpack_sync_incremental_sync_interval', function() { return 'hourly'; } );
add_filter( 'jetpack_sync_full_sync_interval', function() { return 'daily'; } );

function jetpackcom_custom_sync_schedule( $schedules ) {
    if ( ! isset( $schedules['10min'] ) ) {
        $schedules['10min'] = array(
            'interval' => 10 * MINUTE_IN_SECONDS,
            'display' => __( 'Every 10 minutes' ),
        );
    }
    return $schedules;
}
//add_filter( 'cron_schedules', 'jetpackcom_custom_sync_schedule' );

function jetpackcom_return_60_min() {
    return 'hourly';
}


add_filter( 'jetpack_sync_incremental_sync_interval', 'jetpackcom_return_10_min' );
add_filter( 'jetpack_sync_full_sync_interval', 'jetpackcom_return_60_min' );


//
add_filter('allowed_http_origins', function($origins) {
    $origins[] = home_url('');
    return $origins;
});

/* Modifies the list of US states available in checkout drop downs */

function fa_sell_only_states( $states ) {

        $states['US'] = array(
                'AL' => __( 'Alabama', 'woocommerce' ),
//              'AK' => __( 'Alaska', 'woocommerce' ),
                'AZ' => __( 'Arizona', 'woocommerce' ),
                'AR' => __( 'Arkansas', 'woocommerce' ),
//              'CA' => __( 'California', 'woocommerce' ),
                'CO' => __( 'Colorado', 'woocommerce' ),
//              'CT' => __( 'Connecticut', 'woocommerce' ),
//              'DE' => __( 'Delaware', 'woocommerce' ),
//              'DC' => __( 'District Of Columbia', 'woocommerce' ),
                'FL' => __( 'Florida', 'woocommerce' ),
                'GA' => __( 'Georgia', 'US state of Georgia', 'woocommerce' ),
//              'HI' => __( 'Hawaii', 'woocommerce' ),
                'ID' => __( 'Idaho', 'woocommerce' ),
//              'IL' => __( 'Illinois', 'woocommerce' ),
                'IN' => __( 'Indiana', 'woocommerce' ),
                'IA' => __( 'Iowa', 'woocommerce' ),
                'KS' => __( 'Kansas', 'woocommerce' ),
                'KY' => __( 'Kentucky', 'woocommerce' ),
                'LA' => __( 'Louisiana', 'woocommerce' ),
                'ME' => __( 'Maine', 'woocommerce' ),
//              'MD' => __( 'Maryland', 'woocommerce' ),
//              'MA' => __( 'Massachusetts', 'woocommerce' ),
                'MI' => __( 'Michigan', 'woocommerce' ),
                'MN' => __( 'Minnesota', 'woocommerce' ),
                'MS' => __( 'Mississippi', 'woocommerce' ),
                'MO' => __( 'Missouri', 'woocommerce' ),
                'MT' => __( 'Montana', 'woocommerce' ),
                'NE' => __( 'Nebraska', 'woocommerce' ),
                'NV' => __( 'Nevada', 'woocommerce' ),
                'NH' => __( 'New Hampshire', 'woocommerce' ),
//              'NJ' => __( 'New Jersey', 'woocommerce' ),
                'NM' => __( 'New Mexico', 'woocommerce' ),
//              'NY' => __( 'New York', 'woocommerce' ),
                'NC' => __( 'North Carolina', 'woocommerce' ),
                'ND' => __( 'North Dakota', 'woocommerce' ),
                'OH' => __( 'Ohio', 'woocommerce' ),
                'OK' => __( 'Oklahoma', 'woocommerce' ),
                'OR' => __( 'Oregon', 'woocommerce' ),
                'PA' => __( 'Pennsylvania', 'woocommerce' ),
                'RI' => __( 'Rhode Island', 'woocommerce' ),
                'SC' => __( 'South Carolina', 'woocommerce' ),
                'SD' => __( 'South Dakota', 'woocommerce' ),
                'TN' => __( 'Tennessee', 'woocommerce' ),
                'TX' => __( 'Texas', 'woocommerce' ),
                'UT' => __( 'Utah', 'woocommerce' ),
                'VT' => __( 'Vermont', 'woocommerce' ),
                'VA' => __( 'Virginia', 'woocommerce' ),
                'WA' => __( 'Washington', 'woocommerce' ),
                'WV' => __( 'West Virginia', 'woocommerce' ),
                'WI' => __( 'Wisconsin', 'woocommerce' ),
                'WY' => __( 'Wyoming', 'woocommerce' ),
//              'AA' => __( 'Armed Forces (AA)', 'woocommerce' ),
//              'AE' => __( 'Armed Forces (AE)', 'woocommerce' ),
//              'AP' => __( 'Armed Forces (AP)', 'woocommerce' ),
//              'AS' => __( 'American Samoa', 'woocommerce' ),
//              'GU' => __( 'Guam', 'woocommerce' ),
//              'MP' => __( 'Northern Mariana Islands', 'woocommerce' ),
//              'PR' => __( 'Puerto Rico', 'woocommerce' ),
//              'UM' => __( 'US Minor Outlying Islands', 'woocommerce' ),
//              'VI' => __( 'US Virgin Islands', 'woocommerce' ),
        );

        return $states;

}
add_filter( 'woocommerce_states', 'fa_sell_only_states' );

add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true');

add_filter( 'woocommerce_available_payment_gateways', 'fa_unset_gateway_by_category' );

function fa_unset_gateway_by_category( $available_gateways ) {

    if ( is_admin() ) return $available_gateways;

    if ( ! is_checkout() ) return $available_gateways;

    $unset = false;
        // 3156 is Gift Cards
    $category_ids = array( 3156 );

    foreach ( WC()->cart->get_cart_contents() as $key => $values ) {

        $terms = get_the_terms( $values['product_id'], 'product_cat' );

        foreach ( $terms as $term ) {

            if ( in_array( $term->term_id, $category_ids ) ) {

                $unset = true;

                break;

            }

        }

    }

    if ( $unset == true ) unset( $available_gateways['sezzlepay'] );

    return $available_gateways;

}

add_filter('wp_sitemaps_enabled', '__return_false');

// Add this to your theme's functions.php
// This filter will insure that the wp-image-{IMAGEID} class is added to your
// image tags, allowing media cloud to work more optimally
add_filter('wp_get_attachment_image_attributes', function($attrs, $attachment, $size) {
    if (!empty($attachment) && isset($attrs['class'])) {
        $attrs['class']  .= ' wp-image-'.$attachment->ID;
    }

    return $attrs;
}, PHP_INT_MAX, 3);

