(function ($) {
  $(document).ready(function () {
    let page = 2;
    const postsPerPage = load_more_params.posts_per_page;

    $("#btn-load-more").on("click", function () {
      $.ajax({
        url: load_more_params.ajax_url,
        type: "POST",
        data: {
          action: "load_more_users",
          page: page,
          posts_per_page: postsPerPage,
        },
        success: function (response) {
          const data = JSON.parse(response);

          if (data.html) {
            $(".container .row").append(data.html);
            page++;
          } else {
            $("#btn-load-more").hide();
          }
        },
        error: function (xhr, status, error) {
          console.error(error);
        },
      });
    });
  });
})(jQuery);
