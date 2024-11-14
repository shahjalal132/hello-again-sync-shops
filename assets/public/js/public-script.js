(function ($) {
  $(document).ready(function () {
    let page = 2;
    let shopPage = 2;
    const postsPerPage = load_more_params.posts_per_page;

    // load more for user
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

    // load more for shop
    $("#btn-shop-load-more").on("click", function () {
      $.ajax({
        url: load_more_params.ajax_url,
        type: "POST",
        data: {
          action: "load_more_shops",
          page: shopPage,
          posts_per_page: postsPerPage,
        },
        success: function (response) {
          const data = JSON.parse(response);

          if (data.html) {
            $(".container .row").append(data.html);
            shopPage++;
          } else {
            $("#btn-shop-load-more").hide();
          }
        },
        error: function (xhr, status, error) {
          console.error(error);
        },
      });
    });
  });
})(jQuery);
