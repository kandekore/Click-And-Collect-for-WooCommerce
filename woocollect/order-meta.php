<?php
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