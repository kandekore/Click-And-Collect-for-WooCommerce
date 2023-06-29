<?php 
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

// Localize script with the collection time options


// Add admin scripts and styles
add_action('admin_enqueue_scripts', 'collection_time_booking_enqueue_admin_scripts');

function collection_time_booking_enqueue_admin_scripts()
{
    wp_enqueue_script('collection-time-booking-admin-script', plugin_dir_url(__FILE__) . 'js/admin-script.js', array('jquery'), '1.0.0', true);
    wp_enqueue_style('collection-time-booking-admin-style', plugin_dir_url(__FILE__) . 'css/admin-style.css');
}


function remove_field_validation_on_shipping_change($posted_data, $errors)
{
    $current_shipping_method = isset($posted_data['shipping_method'][0]) ? $posted_data['shipping_method'][0] : '';

    $current_shipping = array();
    if ($current_shipping_method != '') {
        $current_shipping = explode(":", $current_shipping_method);
    }
    $selected_shipping_methods = get_option('click_collect_shipping_methods', array());

    if (!in_array($current_shipping[0], $selected_shipping_methods)) {
        // Check if collection date and time fields are present in the posted data
        if (isset($posted_data['collection_date'])) {
            unset($errors->errors['collection_date']);
        }
        if (isset($posted_data['collection_time'])) {
            unset($errors->errors['collection_time']);
        }
    }

    return $posted_data;
}
add_filter('woocommerce_checkout_posted_data', 'remove_field_validation_on_shipping_change', 10, 2);
