<?php 
/*
Plugin Name: Pro Click & Collect for WooCommerce
Description: Collection time plugin for WooCommerce orders
Version: 1.0.1
Author: Darren Kandekore
Author URI: https://darrenk.uk
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Update URI: https://wordpresswizard.net/clickandcollect
*/

// Plugin Activation and Deactivation
register_activation_hook(__FILE__, 'collection_time_booking_activate');
register_deactivation_hook(__FILE__, 'collection_time_booking_deactivate');

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'activation.php';
require_once plugin_dir_path(__FILE__) . 'admin-pages.php';
require_once plugin_dir_path(__FILE__) . 'checkout.php';
require_once plugin_dir_path(__FILE__) . 'enqueue.php';
require_once plugin_dir_path(__FILE__) . 'order-meta.php';
require_once plugin_dir_path(__FILE__) . 'orders-widget.php';
