jQuery(document).ready(function ($) {
  var checkAndToggleCollectionBox = function () {
    var selected_method = $(".shipping_method:checked").val();
    var isLocalPickup =
      selected_method && selected_method.startsWith("local_pickup");

    if (isLocalPickup) {
      $("#collection-time-box").show();
    } else {
      $("#collection-time-box").hide();
    }
  };

  // Check on page load
  checkAndToggleCollectionBox();

  // Check on shipping method change
  $(document.body).on("change", ".shipping_method", function () {
    checkAndToggleCollectionBox();
  });
});
