<?php
/**
 * Flatsome functions and definitions
 *
 * @package flatsome
 */

//require get_template_directory() . '/inc/init.php';

add_filter( 'action_scheduler_retention_period', function() { return DAY_IN_SECONDS * 1; } );

add_filter('woocommerce_subcategory_count_html', '__return_false');

@ini_set('upload_max_size', '120M');
@ini_set('post_max_size', '120M');
@ini_set('max_execution_time', '300');

// Load Our files
include_once(get_stylesheet_directory().'/fa_includes/fa_debug_functions.php');
include_once(get_stylesheet_directory().'/fa_includes/fa_site_setup.php');
include_once(get_stylesheet_directory().'/fa_includes/fa_formatter.php');
include_once(get_stylesheet_directory().'/fa_includes/fa_wpallimport.php');
include_once(get_stylesheet_directory().'/fa_includes/fa_import_hacks.php');
include_once(get_stylesheet_directory().'/fa_includes/fa_businessbloomer.php');

// Add FingerprintJS to end of the checkout process
add_action('woocommerce_before_checkout_form', 'fingerprint_add_jscript_checkout_noimport');

function fingerprint_add_jscript_checkout()
{
    ?>

<script id="fingerprint">
  console.log("Initializing Fingerprint");
  // Initialize the agent at application startup.
  const fpPromise = import('https://fpcdn.io/v3/Oo4CqqyVw0pCzwTpD4Mx')
    .then(FingerprintJS => FingerprintJS.load({
	    apiKey: 'Oo4CqqyVw0pCzwTpD4Mx',
	    endpoint: 'https://metrics.featherarms.com'
    })
  );

  // When you need the visitor identifier:
  fpPromise
    .then(fp => fp.get({tag: {
        PHPSESSID: '<?php echo session_id() ?>',
        userID: '<?php echo get_current_user_id() ?>'
      }}))
    .then(result => console.log(result.));
</script>

<?php
}

function fingerprint_add_jscript_checkout_noimport()
{
    ?>
<script src="https://fpcdn.io/v3/Oo4CqqyVw0pCzwTpD4Mx/iife.min.js"></script>
<script id="FingerPrint">
  // Initialize the agent at application startup.
  console.log("Initializing Fingerprint");
  var fpPromise = FingerprintJS.load({
    apiKey: 'Oo4CqqyVw0pCzwTpD4Mx',
    endpoint: 'https://metrics.featherarms.com'
  });

  // Get the visitor identifier when you need it.
  fpPromise
    .then(function (fp) { return fp.get({tag: {
        PHPSESSID: '<?php echo session_id() ?>',
        userID: '<?php echo get_current_user_id() ?>'
      }}) })
    .then(function (result) { console.log("Result: " + result) })
</script>

<?php
}









//Used in WP ALL Import for simple remote urls
function custom_file_download($url, $type = 'xml')
{
    // Set our default cURL options.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    /* Optional: Set headers if needed.
    *    $headers = array();
    *    $headers[] = "Accept-Language: de";
    *    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    */

    // Retrieve file from $url.
    $result = curl_exec($ch);

    // Return error if cURL fails.
    if (curl_errno($ch)) {
        exit('Error:' . curl_error($ch));
    }
    curl_close($ch);

    // Identify the upload directory path.
    $uploads  = wp_upload_dir();

    // Generate full file path and set extension to $type.
    $filename = $uploads['basedir'] . '/' . strtok(basename($url), "?") . '.' . $type;

    // If the file exists locally, mark it for deletion.
    if (file_exists($filename)) {
        @unlink($filename);
    }

    // Save the new file retrieved from FTP.
    file_put_contents($filename, $result);

    // Return the URL to the newly created file.
    return str_replace($uploads['basedir'], $uploads['baseurl'], $filename);
}


// WP All Import Functions


//function davidsons_upc_clean($upccode) {
//	return str_replace("#", "", $upccode);
//}

// Used in WP All Import for CSSI API
function fa_cssi_inventory_download()
{
    // We first use the API to get a json response with the inventory file url
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.chattanoogashooting.com/rest/v3/items/product-feed',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic CE7C567DFA90B36C89D6E3FC13CE1246:91c495b09ff578bcc3d45b139310122d',
            'Cookie: FLASH=%7B%7D'
        ),
    ));

    $response = curl_exec($curl);

    // Return error if cURL fails.
    if (curl_errno($curl)) {
        exit('Error curl:' . curl_error($curl));
    }
    curl_close($curl);

    // Now parse the API response to get the url for the inventory file

    $json_as_array = json_decode($response, true);
    $inventory_url = $json_as_array['product_feed']['url'];
    write_log("Inventory URL: $inventory_url");
    // Now we download the inventory file
    // Set our default cURL options.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $inventory_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    // Retrieve file from $url.
    $result = curl_exec($ch);

    // Return error if cURL fails.
    if (curl_errno($ch)) {
        exit('Error ch:' . curl_error($ch));
    }
    curl_close($ch);

    // Identify the upload directory path.
    $uploads  = wp_upload_dir();
    //$filename = $uploads['basedir'] . '/' . basename( $inventory_url) . '.csv');
    $filename = $uploads['basedir'] . '/' . pathinfo($inventory_url, PATHINFO_FILENAME)  . '.csv';
    write_log("Filename: $filename");
    // If the file exists locally, mark it for deletion.
    if (file_exists($filename)) {
        @unlink($filename);
    }

    // Save the new file retrieved from FTP.
    write_log("CSSI Data Saved to $filename");
    file_put_contents($filename, $result);

    // Return the URL to the newly created file.
    write_log("New Url: "+str_replace($uploads['basedir'], $uploads['baseurl'], $filename));
    //return str_replace( $uploads['basedir'], $uploads['baseurl'], $filename );
}






// Code to adjust shipping class based upon Product Category and flag FFL Items
add_action('woocommerce_process_product_meta', 'update_product_shipping_class', 100);
function update_product_shipping_class($post_id)
{
    $handgun_shipping_class = 3143;
    $longgun_shipping_class = 3142;
    $ammunition_shipping_class = 3141;
    $standard_shipping_class = 3738;

    $handgun_categories = ['handgubs', 'pistols', 'revolvers', 'handgunsca-compliant'];
    $longgun_categories = ['rifles', 'firearms-shotguns', 'firearms-combination'];
    $ammunition_categories = ['ammunition', 'rifle-ammunition', 'handgun-ammunition', 'shotgun-ammunition', 'rimfire-ammunition'];

    if (has_term($handgun_categories, 'product_cat', $post_id)) {
        $shipping_class_id = $handgun_shipping_class;
    } elseif (has_term($longgun_categories, 'product_cat', $post_id)) {
        $shipping_class_id = $longgun_shipping_class;
    } elseif (has_term($ammunition_categories, 'product_cat', $post_id)) {
        $shipping_class_id = $ammunition_shipping_class;
    } else {
        $shipping_class_id = $standard_shipping_class;
    }
    $product = wc_get_product($post_id);
    $product->set_shipping_class_id($shipping_class_id);
    $product->save();
}

add_action('woocommerce_after_cart_item_name', 'add_custom_text_after_cart_item_name_restricted', 10, 2);
function add_custom_text_after_cart_item_name_restricted($cart_item, $cart_item_key)
{
    $product = $cart_item['data'];
    $id = $product->get_id();
    $terms = get_the_terms($id, 'product_cat');
    $term_ids = wp_list_pluck($terms, 'term_id');
    $parents = array_filter(wp_list_pluck($terms, 'parent'));
    $term_ids_not_parents = array_diff($term_ids, $parents);
    $terms_not_parents = array_intersect_key($terms, $term_ids_not_parents);
    $object = array_values($terms_not_parents);

    $html = '';
    $is_restricted_category = get_field('is_restricted_category', $object[0]);
    if ($is_restricted_category) {
        $html = $html.'<div class="restricted"><strong>Restricted Item</strong><br/>';
        $is_ffl_required = get_field('is_ffl_required', $object[0]);
        if ($is_ffl_required) {
            $html = $html.'Must ship to FFL ';
            $is_handgun = get_field('is_handgun', $object[0]);
            if ($is_handgun) {
                $html = $html.'via 1 or 2 day express.<br/>';
                $html = $html.'Includes Federal Excise Tax of 10%';
            } else {
                $html = $html.'.<br /> Includes Federal Excise Tax of 11%';
            }
        }
        $is_ammunition = get_field('is_ammunition', $object[0]);
        $is_explosive = get_field('is_explosive', $object[0]);
        if ($is_ammunition || $is_explosive) {
            $html = $html.'Requires ground shipping only.<br />';
            $html = $html.'Includes Federal Excise Tax of 11%';
        }

        $html = $html.'</div>';
    }

    echo $html;
}

function fa_cart_item_class($class, $values, $values_key)
{
    if (isset($values[ 'product_id' ])) {
        $class .= ' custom-' . $values[ 'product_id' ];
    }

    return $class;
}
add_filter('woocommerce_cart_item_class', 'fa_cart_item_class', 10, 3);

add_action('woocommerce_before_checkout_shipping_form', 'fa_before_checkout_shipping_form', 10);

function fa_before_checkout_shipping_form()
{
    echo '<h2>Shipping Information</h2>';
    echo '<p>All firearms MUST be shipped to an FFL. Enter the company name and the address of the FFL you want your firearm shipped to.  Have them email a copy of their license to <a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#102;&#102;&#108;&#64;&#102;&#101;&#97;&#116;&#104;&#101;&#114;&#97;&#114;&#109;&#115;&#46;&#99;&#111;&#109;">&#102;&#102;&#108;&#64;&#102;&#101;&#97;&#116;&#104;&#101;&#114;&#97;&#114;&#109;&#115;&#46;&#99;&#111;&#109;</a>. Firearms will not be shipped until the receiving FFL has been verified! Failure to follow these steps will delay your order. If your order does not contain a firearm then enter your home address.</p>';
}


add_action('woocommerce_cart_totals_after_order_total', 'fa_woocommerce_cart_totals_after_order_total', 10);

function fa_woocommerce_cart_totals_after_order_total()
{
    echo '<p><i>*Route Shipping Protection is optional and can be removed during checkout. For more details on Route Shipping Protection click on the icon during checkout for more information.</i></p>';
}

//add_action( 'init', 'stop_heartbeat', 1 );
function stop_heartbeat()
{
    wp_deregister_script('heartbeat');
}

// test to see if removing dashboard widgets stops excessive queries
function remove_dashboard_widgets()
{
    remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal');
}
add_action('wp_user_dashboard_setup', 'remove_dashboard_widgets', 20);
add_action('wp_dashboard_setup', 'remove_dashboard_widgets', 20);

function my_wp_all_import_feed_url($url)
{
    return $url . '?something=else';
}

//add_filter( 'wp_all_import_feed_url', 'my_wp_all_import_feed_url', 10, 4 );

function fa_cssi_inventory_url()
{
    // We first use the API to get a json response with the inventory file url
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.chattanoogashooting.com/rest/v3/items/product-feed',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic CE7C567DFA90B36C89D6E3FC13CE1246:91c495b09ff578bcc3d45b139310122d',
            'Cookie: FLASH=%7B%7D'
        ),
    ));

    $response = curl_exec($curl);

    // Return error if cURL fails.
    if (curl_errno($curl)) {
        exit('Error curl:' . curl_error($curl));
    }
    curl_close($curl);

    // Now parse the API response to get the url for the inventory file

    $json_as_array = json_decode($response, true);
    $inventory_url = $json_as_array['product_feed']['url'];
    return $inventory_url;
}
