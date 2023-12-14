jQuery(document).ready(function ($) {
  function updateCollectionTimeBoxVisibility() {
    var selected_method =
      $(".shipping_method:checked").val() || $(".shipping_method").val();

    if (typeof selected_method !== "undefined") {
      var sel = selected_method.split(":");

      if (my_script_vars.selected_shipping_methods.includes(sel[0])) {
        // Show the date and time fields
        $("#collection-time-box").show();
      } else {
        // Hide the date and time fields
        $("#collection-time-box").hide();
      }
    } else {
      $("#collection-time-box").hide();
    }
  }

  // Check visibility on page load
  updateCollectionTimeBoxVisibility();

  $(document.body).on("change", ".shipping_method", function () {
    updateCollectionTimeBoxVisibility();
  });
});
