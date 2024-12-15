(function ($) {
  $(document).ready(function () {

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

    let page = 2; // Page number for users load more
    let shopPage = 2; // Page number for shops load more
    const postsPerPage = load_more_params.posts_per_page;

    // Load more shops
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

    // Search Shops
    const searchInput = $('#search-shops-input'); // Input field for search
    const resultsContainer = $('#shop-results'); // Where results will be displayed

    $('#search-shops-button').on('click', function () {
      const query = searchInput.val(); // Get the search query
      shopPage = 1; // Reset page for fresh search results
      resultsContainer.html('<p>Loading...</p>'); // Show loading text

      $.ajax({
        url: load_more_params.ajax_url,
        type: 'POST',
        data: {
          action: 'search_shops',
          query: query,  // Use 'query' here instead of 'search_query'
          page: shopPage,
        },
        success: function (response) {
          const data = JSON.parse(response);

          if (data.html) {
            resultsContainer.html(data.html); // Display results
          } else {
            resultsContainer.html('<p>No results found.</p>'); // No results
          }
        },
        error: function () {
          resultsContainer.html('<p>Error loading results.</p>');
        },
      });
    });

    // live search for shops
    searchInput.on('keyup', function () {
      let query = searchInput.val();
      shopPage = 1;
      resultsContainer.html('<p class="loader-container"><span class="loader"></span></p>');
      setTimeout(function (){
        $.ajax({
          url: load_more_params.ajax_url,
          type: 'POST',
          data: {
            action: 'live_search_shops',
            query: query,  // Use 'query' here instead of 'search_query'
            page: shopPage,
          },
          success: function (response) {
            const data = JSON.parse(response);
  
            if (data.html) {
              resultsContainer.html(data.html); // Display results
            } else {
              resultsContainer.html('<p>No results found.</p>'); // No results
            }
          },
          error: function () {
            resultsContainer.html('<p>Error loading results.</p>');
          },
        });
      }, 300);
    });

  });
})(jQuery);
