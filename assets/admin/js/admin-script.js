(function ($) {
  $(document).ready(function () {
    $("#save-btn").on("click", function () {
      // Collect form data
      const apiBaseUrl = $("#api_base_url").val();
      const apiKey = $("#api_key").val();
      const postsToDisplay = $("#how_many_posts_to_display").val();

      // AJAX request
      $.ajax({
        url: haOptions.ajaxUrl,
        method: "POST",
        data: {
          action: "save_ha_options",
          api_base_url: apiBaseUrl,
          api_key: apiKey,
          how_many_posts_to_display: postsToDisplay,
        },
        success: function (response) {
          if (response.success) {
            alert(response.data.message); // Display success message
          } else {
            alert(response.data.message); // Display error message
          }
        },
        error: function () {
          alert("An error occurred while saving settings.");
        },
      });
    });
  });
})(jQuery);
