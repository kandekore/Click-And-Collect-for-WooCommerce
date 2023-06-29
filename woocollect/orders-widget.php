<?php  // Add custom dashboard widget
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