<?php function enqueue_my_script() {
    $selected_shipping_methods = get_option('click_collect_shipping_methods', array());
    wp_enqueue_script('collection-time-booking-script', get_template_directory_uri() . '/js/collection-time-booking.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('my-shipping-methods-script', site_url() . '/wp-content/plugins/WooCollect/js/shipping-methods.js', array('jquery', 'collection-time-booking-script'), '1.1.1', true);
    wp_localize_script('collection-time-booking-script', 'my_script_vars', array(
        'selected_shipping_methods' => implode(" , ",$selected_shipping_methods),
    ));
}
add_action('admin_enqueue_scripts', 'enqueue_my_script');
add_action('wp_enqueue_scripts', 'enqueue_my_script');




// Enqueue jQuery UI
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-datepicker');

// Enqueue jQuery UI CSS
wp_enqueue_style('jquery-ui-datepicker-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

// Enqueue time picker JavaScript
wp_enqueue_script('jquery-ui-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', array('jquery-ui-datepicker'), '1.6.3', true);

// Enqueue time picker CSS
wp_enqueue_style('jquery-ui-timepicker-css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css');

// Enqueue custom JavaScript for initializing date and time pickers
wp_enqueue_script('collection-time-booking-script', plugin_dir_url(__FILE__) . 'js/collection-time-booking.js', array('jquery-ui-datepicker', 'jquery-ui-timepicker-addon'),'1.19', true);//

// Localize script with the collection time options
$booking_window_hours = get_option('booking_window_hours', 2); // Get booking window hours from settings, default to 2 if not set

$collection_time_options = array(
    'curdate' => date("Y-m-d"),
    'timeFormat' => get_option('time_format', 'g:i A'),
    'minDate' => 0, // Minimum date is today
    'minTime' => date('H:i', strtotime('+' . $booking_window_hours . ' hours')), // Minimum time is 'booking_window_hours' hours from now
    'maxTime' => '' // Placeholder for the maximum time based on opening hours
);
$opening_hours = get_option('collection_time_booking_opening_hours', array());
if (!empty($opening_hours)) {
    $collection_time_options['openingHours'] = $opening_hours;
}
wp_localize_script('collection-time-booking-script', 'collectionTimeOptions', $collection_time_options);

add_action('admin_init', 'register_booking_window_settings');

function register_booking_window_settings() {
    register_setting('booking_window_settings', 'booking_window_hours');
}

function register_collection_time_settings() {
    register_setting('collection_time_settings', 'booking_window_hours');
    register_setting('collection_time_booking_settings', 'collection_time_booking_opening_hours');
}
add_action('admin_init', 'register_collection_time_settings');
function enqueue_plugin_styles() {
    wp_enqueue_style( 'plugin-styles', plugin_dir_url( __FILE__ ) . 'plugin-styles.css' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_plugin_styles' );
