 jQuery(document).ready(function($) {
    // Initialize datepicker for admin fields
    jQuery('#from_date').datepicker({
        dateFormat: 'yy-mm-dd'
        // Additional options if needed
    });

    jQuery('#to_date').datepicker({
        dateFormat: 'yy-mm-dd'
        // Additional options if needed
    });
});
