<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function fa_img_import( $post_id, $gallery_attachment_ids, $missing_images ) {
  add_custom_tracer("fa_img_import");
        //write_log($post_id);
        write_log($missing_images);
        if (empty($missing_images)) {
                return;
        }

        $uploads = wp_upload_dir();
        $date = date('Y-m-d');
        $log_file_name = $uploads['basedir'] . '/' . $date . '_import_missing_images.txt';
        write_log($log_file_name);
        $existing_data = $post_id . ',' . implode(', ', $missing_images);
        file_put_contents( $log_file_name, $existing_data.PHP_EOL, FILE_APPEND);
}

add_action( 'wpallimport_after_images_import', 'fa_img_import', 70, 3 );


