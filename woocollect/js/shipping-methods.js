jQuery(document).ready(function($) {
    
    var selected_method = $(".shipping_method:checked").val();    
    
    if (typeof selected_method !== 'undefined') {
    
        var sel=selected_method.split(":");
        
        if (my_script_vars.selected_shipping_methods.includes(sel[0])) {
            // Show the date and time fields
            $("#collection-time-box").show();
        } else {
            // Hide the date and time fields
            $("#collection-time-box").hide();
        }
    }else{
        $("#collection-time-box").hide();
    }

    $(document.body).on('change', '.shipping_method', function() {  //input[name="shipping_method[0]"]
    
        var selected_method = $(this).val();
        var sel=selected_method.split(":");
        
        if (my_script_vars.selected_shipping_methods.includes(sel[0])) {
            // Show the date and time fields
            $("#collection-time-box").show();
        } else {
            // Hide the date and time fields
            $("#collection-time-box").hide();
        }
    });


});
