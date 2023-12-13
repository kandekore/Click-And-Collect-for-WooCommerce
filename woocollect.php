<?php 
/*
Plugin Name: Click & Collect for WooCommerce
Description: Collection time plugin for WooCommerce orders
Version: 1.0.3
Author: D Kandekore
License: GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
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
	add_submenu_page(
    'woo-click-collect', 
    'Unavailable Dates', 
    'Unavailable Dates', 
    'manage_options', 
    'unavailable-dates-settings', 
    'display_unavailable_dates_settings'
);
}
function my_plugin_admin_init() {
    add_settings_section(
        'my_plugin_settings_section', // ID
        'General Settings', // Title
        'my_plugin_settings_section_callback', // Callback
        'woo-click-collect' // Page
    );

    add_settings_field(
        'my_plugin_delete_data_on_deactivation', // ID
        'Delete Data on Deactivation', // Title
        'my_plugin_delete_data_on_deactivation_callback', // Callback
        'woo-click-collect', // Page
        'my_plugin_settings_section' // Section
    );

    register_setting('woo-click-collect', 'my_plugin_delete_data_on_deactivation');
}

function my_plugin_settings_section_callback() {
    echo '<p>General settings for the plugin.</p>';
}

add_action('admin_init', 'my_plugin_admin_init');

// Main menu page content
function display_main_menu_content() {
	 echo '<form method="post" action="options.php">';
    settings_fields('woo-click-collect');
    do_settings_sections('woo-click-collect');
    submit_button();
    echo '</form>';


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

function my_plugin_delete_data_on_deactivation_callback() {
    $option = get_option('my_plugin_delete_data_on_deactivation');
    echo '<input type="checkbox" id="my_plugin_delete_data_on_deactivation" name="my_plugin_delete_data_on_deactivation" value="1" ' . checked(1, $option, false) . '/>';
    echo '<label for="my_plugin_delete_data_on_deactivation">Check this box if you want all plugin data to be deleted upon plugin deactivation.</label>';
}
function my_plugin_deactivate() {
    // Check if the user has opted to delete data on deactivation
    if (get_option('my_plugin_delete_data_on_deactivation') == '1') {
        // Delete options
        delete_option('booking_window_hours');
        delete_option('collection_time_booking_opening_hours');
        delete_option('click_collect_shipping_methods');
        
    }
}

register_deactivation_hook(__FILE__, 'my_plugin_deactivate');
// Booking Window admin settings page
function display_booking_window() {
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Update 'booking_window_hours' if form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Verify nonce
        check_admin_referer('booking_window_settings_action', 'booking_window_settings_nonce');

        update_option('booking_window_hours', $_POST['booking_window_hours']);
    }

    $booking_window_hours = get_option('booking_window_hours', 2);  // set default value as 2
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form action="" method="post">
            <?php
            wp_nonce_field('booking_window_settings_action', 'booking_window_settings_nonce');
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
         // Verify nonce
    check_admin_referer('collection_time_booking_settings', 'collection_time_booking_nonce');

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
                    <th scope="row"><?php echo esc_html(ucfirst($day)); ?></th>
                        <td>
                            <input type="text" name="<?php echo esc_attr($day); ?>_start_time" value="<?php echo esc_attr($start_time); ?>" placeholder="Opening Time">                            
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
        // Verify nonce
    check_admin_referer('shipping_methods_settings', 'shipping_methods_settings_nonce');

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
		
		<div class="woo-settings-instructions">
    <h2>Instructions for Adding a Shipping Zone and Method in WooCommerce</h2>
    <p>Follow these steps to add a new shipping zone and method:</p>
    <ol>
        <li>Navigate to your WordPress Dashboard.</li>
        <li>Go to <strong>WooCommerce</strong> and click on <strong>Settings</strong>.</li>
        <li>Open the <strong>Shipping</strong> tab.</li>
        <li>Click on the <strong>Add Shipping Zone</strong> button.</li>
        <li>Enter a <strong>Zone Name</strong> and select <strong>Zone Regions</strong> that apply.</li>
        <li>Click on <strong>Add Shipping Method</strong> button within the zone.</li>
        <li>Choose the desired shipping method (e.g., Flat Rate, Free Shipping, Local Pickup) from the dropdown and click <strong>Add Shipping Method</strong>.</li>
        <li>Once added, click on the shipping method to configure its settings (like cost, tax status, etc.).</li>
        <li>After configuring the settings, click <strong>Save Changes</strong>.</li>
    </ol>
    <p>Note: You can create multiple shipping zones and assign different shipping methods to each zone based on your requirements.</p>
</div>

    </div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var checkboxes = document.querySelectorAll('input[name="shipping_methods[]"]');
        checkboxes.forEach(function(checkbox) {
            if (checkbox.value === 'pickup_location') {
                checkbox.style.display = 'none'; // Hide the checkbox
                checkbox.parentNode.style.display = 'none'; // Optionally hide the parent element, if needed
            }
        });
    });
</script>
    <?php
}
function display_unavailable_dates_settings() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }


    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-datepicker-css', plugins_url('assets/jquery-ui.css', __FILE__));
    wp_enqueue_script('custom-admin-script', plugin_dir_url(__FILE__) . 'js/admindp.js', array('jquery-ui-datepicker'), '1.0.0', true);


    // Handle form submission logic for adding new dates
    if (isset($_POST['unavailable_dates_submit'])) {
        check_admin_referer('unavailable_dates_action', 'unavailable_dates_nonce');

        $new_unavailable_dates = array(
            'from' => sanitize_text_field($_POST['from_date']),
            'to' => sanitize_text_field($_POST['to_date'])
        );

        // Retrieve existing unavailable dates and add new ones
        $unavailable_dates = get_option('unavailable_dates', array());
        $unavailable_dates[] = $new_unavailable_dates;
        update_option('unavailable_dates', $unavailable_dates);

        echo '<div class="notice notice-success"><p>New unavailable date range added.</p></div>';
    }

    // Handle deletion of unavailable date range
    if (isset($_POST['delete_unavailable_date'])) {
        check_admin_referer('delete_unavailable_date_action', 'delete_unavailable_date_nonce');

        $index_to_delete = sanitize_text_field($_POST['delete_unavailable_date']);
        $unavailable_dates = get_option('unavailable_dates', array());

        if (isset($unavailable_dates[$index_to_delete])) {
            unset($unavailable_dates[$index_to_delete]);
            $unavailable_dates = array_values($unavailable_dates); // Reindex the array
            update_option('unavailable_dates', $unavailable_dates);
            echo '<div class="notice notice-success"><p>Unavailable date range deleted.</p></div>';
        }
    }

    // Retrieve existing unavailable dates from the database for displaying
    $unavailable_dates = get_option('unavailable_dates', array());

    // Render the form
    ?>
    <div class="wrap">
        <h1>Unavailable Dates Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('unavailable_dates_action', 'unavailable_dates_nonce'); ?>

            <p>
                <label for="from_date">From Date:</label>
                <input type="text" id="from_date" name="from_date" class="date-picker" required>
            </p>
            <p>
                <label for="to_date">To Date:</label>
                <input type="text" id="to_date" name="to_date" class="date-picker" required>
            </p>

            <p>
                <input type="submit" name="unavailable_dates_submit" class="button-primary" value="Add Unavailable Date Range">
            </p>
        </form>

        <?php
        if (!empty($unavailable_dates)) {
            echo '<h2>Existing Unavailable Dates</h2>';
            echo '<ul>';
            foreach ($unavailable_dates as $index => $range) {
                echo '<li>';
                echo 'From: ' . esc_html($range['from']) . ' To: ' . esc_html($range['to']);
                ?>
                <form method="post" action="">
                    <?php wp_nonce_field('delete_unavailable_date_action', 'delete_unavailable_date_nonce'); ?>
                    <input type="hidden" name="delete_unavailable_date" value="<?php echo esc_attr($index); ?>">
                    <input type="submit" value="Delete" class="button-link-delete">
                </form>
                <?php
                echo '</li>';
            }
            echo '</ul>';
        }
        ?>

    </div>


    <?php
}

// Add custom meta box to checkout page
add_action('woocommerce_before_order_notes', 'collection_time_booking_add_meta_box');

function collection_time_booking_add_meta_box($checkout) {
    $opening_hours = get_option('collection_time_booking_opening_hours', array());
    
    // Initialize variables for selected date and time if needed
    $selected_date = WC()->session->get('selected_collection_date', '');
    $selected_time = WC()->session->get('selected_collection_time', '');

    // Output the HTML for the date and time fields
    echo '<div id="collection-time-box">';
    
    // Date field
    woocommerce_form_field('collection_date', array(
        'type'          => 'text',
        'class'         => array('form-row-wide'),
        'label'         => __('Collection Date'),
        'placeholder'   => __('Select date'),
        'required'      => true,
        'custom_attributes' => array('autocomplete' => 'off', 'readonly' => 'readonly')
    ), $selected_date);

    // Time field - initially hidden, shown by JS when a date is selected
    woocommerce_form_field('collection_time', array(
        'type'          => 'select',
        'class'         => array('form-row-wide'),
        'label'         => __('Collection Time'),
        'options'       => array('' => __('Select time')), // Initially empty, populated by JS
        'required'      => true,
    ), $selected_time);

    echo '</div>';
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


// Add Meta to email

function  collection_time_booking_order_email( $fields ) {
    $fields['Collection Date'] = __('Collection Date', 'your-domain');
    $fields['Collection Time'] = __('Collection Time', 'your-domain');
    return $fields;
}
add_filter( 'woocommerce_email_order_meta_fields', 'collection_time_booking_order_email' );


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

// Attach collection date and time to the order confirmation email sent to the admin
add_action('woocommerce_email_order_details', 'collection_time_booking_add_collection_datetime_to_email', 10, 4);

function collection_time_booking_add_collection_datetime_to_email($order, $sent_to_admin, $plain_text, $email)
{
    if ($sent_to_admin && $order->get_meta('Collection Date') && $order->get_meta('Collection Time')) {
        $collection_date = $order->get_meta('Collection Date');
        $collection_time = $order->get_meta('Collection Time');
        $collection_datetime = date('Y-m-d H:i', $order->get_meta('Collection DateTime'));
        echo '<p><strong>Collection Date:</strong> ' . esc_html($collection_date) . '</p>';
        echo '<p><strong>Collection Time:</strong> ' . esc_html($collection_time) . '</p>';
        echo '<p><strong>Collection DateTime:</strong> ' . esc_html($collection_datetime) . '</p>';
    }
}

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


function enqueue_my_script() {
    $selected_shipping_methods = get_option('click_collect_shipping_methods', array());
    $booking_window_hours = get_option('booking_window_hours', 2); // Default to 2 if not set

    // Enqueue scripts and styles
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-datepicker-css', plugins_url('assets/jquery-ui.css', __FILE__));
    wp_enqueue_script('jquery-ui-timepicker-addon', plugins_url('assets/jquery-ui-timepicker-addon.min.js', __FILE__), array('jquery-ui-datepicker'), '1.6.3', true);
    wp_enqueue_style('jquery-ui-timepicker-css', plugins_url('assets/jquery-ui-timepicker-addon.min.css', __FILE__));
    wp_enqueue_script('collection-time-booking-script', plugin_dir_url(__FILE__) . 'js/collection-time-booking.js', array('jquery-ui-datepicker', 'jquery-ui-timepicker-addon'), '1.19.1', true);
    wp_enqueue_style('plugin-styles', plugin_dir_url(__FILE__) . 'plugin-styles.css');

    // Prepare and localize script
    wp_localize_script('collection-time-booking-script', 'my_script_vars', array(
        'selected_shipping_methods' => implode(" , ", $selected_shipping_methods),
    ));
	  wp_enqueue_script('my-shipping-methods-script', plugin_dir_url(__FILE__) . 'js/shipping-methods.js', array('jquery', 'collection-time-booking-script'), '1.1.1', true);
    wp_localize_script('collection-time-booking-script', 'my_script_vars', array(
        'selected_shipping_methods' => implode(" , ",$selected_shipping_methods),
    ));

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

    $collection_time_options['unavailableDates'] = get_option('unavailable_dates');

    wp_localize_script('collection-time-booking-script', 'collectionTimeOptions', $collection_time_options);
}
add_action('wp_enqueue_scripts', 'enqueue_my_script');
function enqueue_admin_scripts($hook) {
    if ('admin.php?page=unavailable-dates-settings' !== $hook) {
        return;
    }

    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker', '', array('jquery-ui-core'), '1.12.1', true);
    wp_enqueue_style('jquery-ui-datepicker-css', plugins_url('assets/jquery-ui.css', __FILE__));

    // Enqueue your custom admin script
    wp_enqueue_script('custom-admin-script', plugin_dir_url(__FILE__) . 'js/admindp.js', array('jquery-ui-datepicker'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
