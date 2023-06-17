<?php 
/*
Plugin Name: Pro Click & Collect for WooCommerce
Description: Collection time plugin for WooCommerce orders
Version: 1.0
Author: Darren Kandekore
Author URI: https://darrenk.uk
License: GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Update URI:        https://wordpresswizard.net/clickandcollect
*/

// Plugin Activation and Deactivation

register_activation_hook(__FILE__, 'collection_time_booking_activate');
register_deactivation_hook(__FILE__, 'collection_time_booking_deactivate');

function collection_time_booking_activate()
{
    // Check if WooCommerce is active
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        // WooCommerce is not active, display an error message and deactivate the plugin
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('Sorry, but this plugin requires WooCommerce to be installed and activated.');
    }

    // Add default opening hours
    $default_opening_hours = array(
        'monday' => array('start_time' => '10:00', 'end_time' => '18:00'),
        'tuesday' => array('start_time' => '10:00', 'end_time' => '18:00'),
        'wednesday' => array('start_time' => '10:00', 'end_time' => '18:00'),
        'thursday' => array('start_time' => '10:00', 'end_time' => '18:00'),
        'friday' => array('start_time' => '10:00', 'end_time' => '18:00'),
        'saturday' => array('start_time' => '10:00', 'end_time' => '18:00'),
        'sunday' => array('start_time' => '10:00', 'end_time' => '18:00')
    );
    update_option('collection_time_booking_opening_hours', $default_opening_hours);
}

// Add admin settings page
add_action('admin_menu', 'add_custom_admin_menu');

function add_custom_admin_menu() {
    add_menu_page(
        'Woo Click & Collect', 
        'Woo Click & Collect', 
        'manage_options', 
        'woo-click-collect', 
        'display_main_menu_content', 
        'dashicons-cart', 
        30
    );
    
    add_submenu_page(
        'woo-click-collect', 
        'Booking Window', 
        'Booking Window', 
        'manage_options', 
        'booking-window', 
        'display_booking_window'
    );

    add_submenu_page(
        'woo-click-collect', 
        'Collection Time', 
        'Collection Time', 
        'manage_options', 
        'collection-time-settings', 
        'display_collection_time_settings'
    );
     add_submenu_page(
        'woo-click-collect', 
        'Shipping Methods', 
        'Shipping Methods', 
        'manage_options', 
        'shipping-methods-settings', 
        'display_shipping_methods_settings'
    );
}

// Main menu page content
function display_main_menu_content() {
    // Display content for the main menu page here
    echo '<div class="wrap">';
    echo '<h1>Pro Click & Collect for WooCommerce</h1>';
    echo '</div>';
    
    echo '<h2>Booking Window Settings</h2>';
    echo '<ul>';
    echo '<li>The Booking Window setting allows you to configure the minimum number of hours required for advanced booking. This ensures that customers cannot select collection times that are too close to the current time.</li>';
    echo '<li>Follow these steps to adjust the value:</li>';
    echo '<ol>';
    echo '<li>On the main menu page, click on the "Booking Window" option.</li>';
    echo '<li>You will see a form with a single field labeled "Minimum Hours in Advance".</li>';
    echo '<li>Enter the desired minimum number of hours in the input field. This value represents the minimum time required for customers to book a collection in advance.</li>';
    echo '<li>Click the "Save Changes" button to save your settings.</li>';
    echo '</ol>';
    echo '</ul>';
    
    echo '<h2>Collection Time Settings</h2>';
    echo '<ul>';
    echo '<li>The Collection Time Settings allow you to define the opening and closing times for collection on each day of the week. This ensures accurate scheduling of collection times based on your business\'s availability.</li>';
    echo '<li>Follow these steps to set the opening and closing times:</li>';
    echo '<ol>';
    echo '<li>On the main menu page, click on the "Collection Time Settings" option.</li>';
    echo '<li>You will see a form with a table displaying the days of the week and corresponding input fields for start and end times.</li>';
    echo '<li>For each day of the week, enter the opening and closing times in the respective input fields. This defines the available collection times for each day.</li>';
    echo '<li>After entering the times for all the days, click the "Save Changes" button to save your settings.</li>';
    echo '</ol>';
    echo '</ul>';
    
    echo '<h2>Shipping Methods Settings</h2>';
    echo '<ul>';
    echo '<li>The Shipping Methods Settings allow you to select the shipping methods that support click and collect. This ensures that only relevant shipping methods are available for customers to choose from during the checkout process.</li>';
    echo '<li>Follow these steps to select the supported shipping methods:</li>';
    echo '<ol>';
    echo '<li>On the main menu page, click on the "Shipping Methods Settings" option.</li>';
    echo '<li>You will see a form with a list of available shipping methods.</li>';
    echo '<li>Review the shipping methods and select the ones that are relevant to your business and support click and collect. To select a shipping method, check the corresponding checkbox.</li>';
    echo '<li>Once you have selected the desired shipping methods, click the "Save Changes" button to save your settings.</li>';
    echo '</ol>';
    echo '</ul>';
    
    echo '<p>By following these instructions, you will be able to configure the plugin\'s settings for the Booking Window, Collection Time, and Shipping Methods. These settings will customize the functionality of the click and collect feature according to your business requirements.</p>';
    
}

// Booking Window admin settings page
function display_booking_window() {
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Update 'booking_window_hours' if form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        update_option('booking_window_hours', $_POST['booking_window_hours']);
    }

    $booking_window_hours = get_option('booking_window_hours', 2);  // set default value as 2
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('booking_window_settings');
            do_settings_sections('booking_window_settings');
            ?>
            <table class="form-table" role="presentation">
                <tbody>
                <tr>
                    <th scope="row"><label for="minimum_hours">Minimum Hours in Advance:</label></th>
                    <td><input name="booking_window_hours" type="number" id="minimum_hours" value="<?= $booking_window_hours ?>" class="regular-text"></td>
                </tr>
                </tbody>
            </table>
            <?php
            submit_button('Save Changes');
            ?>
        </form>
    </div>
    <?php
}


// Admin settings page
function display_collection_time_settings()
{
    // Save settings if form submitted
    if (isset($_POST['collection_time_booking_submit'])) {
        $opening_hours = array();
        
        // Loop through days of the week
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            $opening_hours[$day] = array(
                'start_time' => sanitize_text_field($_POST[$day . '_start_time']),
                'end_time'   => sanitize_text_field($_POST[$day . '_end_time'])
            );
        }

        // Save opening hours to database
        update_option('collection_time_booking_opening_hours', $opening_hours);
        
        echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
    }

    // Retrieve opening hours from database
    $opening_hours = get_option('collection_time_booking_opening_hours', array());

    ?>
    <div class="wrap">
        <h1>Collection Time Settings</h1>

        <form method="post" action="">
            <?php wp_nonce_field('collection_time_booking_settings', 'collection_time_booking_nonce'); ?>

            <table class="form-table">
                <?php
                // Loop through days of the week
                foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                    $start_time = isset($opening_hours[$day]['start_time']) ? esc_attr($opening_hours[$day]['start_time']) : '';
                    $end_time = isset($opening_hours[$day]['end_time']) ? esc_attr($opening_hours[$day]['end_time']) : '';
                    ?>
                    <tr>
                        <th scope="row"><?php echo ucfirst($day); ?></th>
                        <td>
                            <input type="text" name="<?php echo $day; ?>_start_time" value="<?php echo $start_time; ?>" placeholder="Opening Time">
                            <input type="text" name="<?php echo $day; ?>_end_time" value="<?php echo $end_time; ?>" placeholder="Closing Time">
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <p class="submit">
                <input type="submit" name="collection_time_booking_submit" class="button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}

//Add shipping method selection page on dashboard

function display_shipping_methods_settings() {
    // Save settings if form submitted
    if (isset($_POST['shipping_methods_settings_submit'])) {
        $selected_shipping_methods = isset($_POST['shipping_methods']) ? $_POST['shipping_methods'] : array();
        $selected_shipping_methods = array_map('sanitize_text_field', $selected_shipping_methods);

        // Save selected shipping methods to database
        update_option('click_collect_shipping_methods', $selected_shipping_methods);
        
        echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
    }

    // Retrieve selected shipping methods from database
    $selected_shipping_methods = get_option('click_collect_shipping_methods', array());

    // Retrieve all available shipping methods
    $shipping_methods = WC()->shipping()->get_shipping_methods();

    ?>
    <div class="wrap">
        <h1>Shipping Methods Settings</h1>

        <form method="post" action="">
            <?php wp_nonce_field('shipping_methods_settings', 'shipping_methods_settings_nonce'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">Select Shipping Methods:</th>
                    <td>
                        <?php foreach ($shipping_methods as $id => $shipping_method) : ?>
                            <label>
                                <input type="checkbox" name="shipping_methods[]" value="<?php echo esc_attr($id); ?>" <?php checked(in_array($id, $selected_shipping_methods)); ?>>
                                <?php echo esc_html($shipping_method->method_title); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="shipping_methods_settings_submit" class="button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}


// Add custom meta box to checkout page
add_action('woocommerce_before_order_notes', 'collection_time_booking_add_meta_box');

function collection_time_booking_add_meta_box($checkout)
{
    $opening_hours = get_option('collection_time_booking_opening_hours', array());
    
    // Get current time
    $current_time = strtotime('now');
    
    // Minimum time slot interval in seconds (1 hour)
    $minimum_interval = 1 * 60 * 60;

    // Get available time slots for today
    $today = strtolower(date('l'));
    $start_time = strtotime($opening_hours[$today]['start_time']);
    $end_time = strtotime($opening_hours[$today]['end_time']);
    $time_slots = array();
    $selected_date = '';
    $selected_time = '';

    $time_slots[''] = "Select Collection Time";
    
    // Generate time slots based on the opening hours

    echo '<div id="collection-time-box">';
    woocommerce_form_field(
        'collection_date',
        array(
            'type' => 'text',
            'class' => array('form-row-wide'),
            'label' => __('Collection Date'),
            'placeholder' => __('Select date'),
            'required' => true,
            'autocomplete' => 'off',
            'custom_attributes' => array(
                'autocomplete' => 'off',
                'readonly' => 'readonly'
            )
        ),
        $selected_date
    );

    woocommerce_form_field(
        'collection_time',
        array(
            'type' => 'select',
            'class' => array('form-row-wide'),            
            'label' => __('Collection Time'),
            'options' => $time_slots,
            'required' => true,
        ),
        $selected_time
    );
    echo '</div>';

    // Set the session variables
    WC()->session->set('selected_collection_date', $selected_date);
    WC()->session->set('selected_collection_time', $selected_time);
}
// Validate collection date and time before placing the order
add_action('woocommerce_checkout_process', 'collection_time_booking_validate_collection_datetime');

function collection_time_booking_validate_collection_datetime()
{

    $current_shipping_method = WC()->session->get('chosen_shipping_methods')[0];

    $current_shipping=array();
    if($current_shipping_method!=''){
        $current_shipping=explode(":",$current_shipping_method);
    }
    $selected_shipping_methods = get_option('click_collect_shipping_methods', array());

    if(in_array($current_shipping[0],$selected_shipping_methods)){

        if (isset($_POST['collection_date']) && empty($_POST['collection_date'])) {
            wc_add_notice(__('Please select a collection date.'), 'error');
        } elseif (isset($_POST['collection_time']) && empty($_POST['collection_time'])) {
            wc_add_notice(__('Please select a collection time.'), 'error');
        } else {
            $selected_date = sanitize_text_field($_POST['collection_date']);
            $selected_time = sanitize_text_field($_POST['collection_time']);
            $booking_window_hours = get_option('booking_window_hours', 2); // Get booking window hours from settings, default to 2 if not set
            $selected_datetime = strtotime($selected_date . ' ' . $selected_time);
            $minimum_interval = $booking_window_hours * 60 * 60;
            $current_datetime = strtotime('now');

            // Calculate the minimum allowed collection datetime
            $minimum_collection_datetime = $current_datetime + $minimum_interval;

        
        }

    }
}


// Save the selected collection date and time to the order
add_action('woocommerce_checkout_create_order', 'collection_time_booking_save_collection_datetime');

function collection_time_booking_save_collection_datetime($order)
{
    $collection_date = isset($_POST['collection_date']) ? sanitize_text_field($_POST['collection_date']) : '';
    $collection_time = isset($_POST['collection_time']) ? sanitize_text_field($_POST['collection_time']) : '';
    $collection_datetime = strtotime($collection_date . ' ' . $collection_time);

    if (!empty($collection_date)) {
        $order->update_meta_data('Collection Date', $collection_date);
    }

    if (!empty($collection_time)) {
        $order->update_meta_data('Collection Time', $collection_time);
    }

    if (!empty($collection_datetime)) {
        $order->set_date_created(date('Y-m-d H:i:s', $collection_datetime));
        $order->update_meta_data('_collection_datetime', $collection_datetime);
    }
}


/*** Anuj  */

function  collection_time_booking_order_email( $fields ) {
    $fields['Collection Date'] = __('Collection Date', 'your-domain');
    $fields['Collection Time'] = __('Collection Time', 'your-domain');
    return $fields;
}
add_filter( 'woocommerce_email_order_meta_fields', 'collection_time_booking_order_email' );

/*** Anuj  */



// Display the selected collection date and time in the admin order page
add_action('woocommerce_admin_order_data_after_billing_address', 'collection_time_booking_display_admin_order_meta', 10, 1);

function collection_time_booking_display_admin_order_meta($order)
{
    $collection_date = $order->get_meta('Collection Date');
    $collection_time = $order->get_meta('Collection Time');
    $collection_datetime = $order->get_meta('Collection DateTime');
    
    if (!empty($collection_date)) {
        echo '<p><strong>Collection Date:</strong> ' . esc_html($collection_date) . '</p>';
    }

    if (!empty($collection_time)) {
        echo '<p><strong>Collection Time:</strong> ' . esc_html($collection_time) . '</p>';
    }

    if (!empty($collection_datetime)) {
        echo '<p><strong>Collection DateTime:</strong> ' . esc_html(date('l jS F H:i', $collection_datetime)) . '</p>';
    }
}

// Attach collection date and time to the order confirmation email sent to the admin and customers
add_filter('woocommerce_email_order_meta_fields', 'collection_time_booking_add_collection_datetime_to_email', 10, 3);

function collection_time_booking_add_collection_datetime_to_email($fields, $sent_to_admin, $order)
{
       // Check if the order has the collection date and time meta
    $collection_date = $order->get_meta('Collection Date');
    $collection_time = $order->get_meta('Collection Time');
    $pickup_location = $order->get_meta('Pickup Location');
    $branch_address = $order->get_meta('Branch Address');

    if (!empty($collection_date) && !empty($collection_time)) {
        $fields['collection_date'] = array(
            'label' => __('Collection Date', 'collection-time-booking'),
            'value' => $collection_date,
        );
        $fields['collection_time'] = array(
            'label' => __('Collection Time', 'collection-time-booking'),
            'value' => $collection_time,
        );

        if (!empty($pickup_location) && !empty($branch_address)) {
            $fields['pickup_location'] = array(
                'label' => __('Pickup Location', 'collection-time-booking'),
                'value' => $pickup_location,
            );
            $fields['branch_address'] = array(
                'label' => __('Branch Address', 'collection-time-booking'),
                'value' => $branch_address,
            );
        }
    }
    return $fields;
}

// Display the selected collection date and time on the order-received page
add_action('woocommerce_thankyou', 'collection_time_booking_display_order_received_collection_datetime', 10, 1);

function collection_time_booking_display_order_received_collection_datetime($order_id)
{
    $order = wc_get_order($order_id);
    $collection_date = $order->get_meta('Collection Date');
    $collection_time = $order->get_meta('Collection Time');

    if (!empty($collection_date) && !empty($collection_time)) {
        echo '<div class="order-received-collection-datetime">';
        echo '<h2>' . __('Collection Date and Time', 'collection-time-booking') . '</h2>';
        echo '<p><strong>' . __('Collection Date', 'collection-time-booking') . ':</strong> ' . esc_html($collection_date) . '</p>';
        echo '<p><strong>' . __('Collection Time', 'collection-time-booking') . ':</strong> ' . esc_html($collection_time) . '</p>';
        echo '</div>';
    }
}

// Add custom dashboard widget
add_action('wp_dashboard_setup', 'collection_time_booking_add_dashboard_widget');

// Add admin dashboard widget
add_action( 'wp_dashboard_setup', 'collection_time_booking_dashboard_widget' );

function collection_time_booking_dashboard_widget() {
    wp_add_dashboard_widget(
        'future_collection_orders_widget',  // Widget slug
        'Future Collection Orders',         // Title
        'display_future_collection_orders'  // Display function
    );
}

function display_future_collection_orders() {
    // Get current date and time
    $current_datetime = current_time('mysql');
    
    // Get upcoming collections from orders
    $orders = wc_get_orders(array(
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'Collection Date',
                'value' => date("Y-m-d"),
                'compare' => '>=',
                'type' => 'DATE',
            ),
        ),
        'meta_key' => 'Collection Date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'status' => 'any', // changed from 'wc-completed'
        'limit' => -1,
    ));

    // Display upcoming collections
    if (!empty($orders)) {
        echo '<ul>';
        foreach ($orders as $order) {
            
            $collection_date = $order->get_meta('Collection Date');            

            if($collection_date!='' && date("Ymd",strtotime($collection_date)) >=date("Ymd")){    
            
                //anuj
                $collection_time = $order->get_meta('Collection Time');
                $formatted_date = date_i18n('l jS F ', strtotime($collection_date));
                //anuj

                $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                $order_link = admin_url('post.php?post=' . $order->get_id() . '&action=edit');
                echo '<li><strong>' . esc_html($customer_name) . '</strong> - ' . esc_html($formatted_date) . ' '.$collection_time.' - <a href="' . esc_url($order_link) . '">View Order</a></li>';
            }
        }
        echo '</ul>';
    } else {
        echo '<p>No upcoming collections found.</p>';
    }
}


function enqueue_my_script() {
    $selected_shipping_methods = get_option('click_collect_shipping_methods', array());
    wp_enqueue_script('collection-time-booking-script', get_template_directory_uri() . '/js/collection-time-booking.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('my-shipping-methods-script', site_url() . '/wp-content/plugins/WooCollect/js/shipping-methods.js', array('jquery', 'collection-time-booking-script'), '1.1.1', true);
    wp_localize_script('collection-time-booking-script', 'my_script_vars', array(
        'selected_shipping_methods' => implode(" , ",$selected_shipping_methods),
    ));
}
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


function remove_field_validation_on_shipping_change( $posted_data ) {

    $current_shipping_method = isset( $posted_data['shipping_method'][0] ) ? $posted_data['shipping_method'][0] : '';

    $current_shipping=array();
    if($current_shipping_method!=''){
        $current_shipping=explode(":",$current_shipping_method);
    }
    $selected_shipping_methods = get_option('click_collect_shipping_methods', array());

    if(!in_array($current_shipping[0],$selected_shipping_methods)){

        unset( $_POST['collection_date'] );
        unset( $_POST['collection_time'] );
    }
    
    return $posted_data;
}
add_filter( 'woocommerce_checkout_posted_data', 'remove_field_validation_on_shipping_change',10,2);






