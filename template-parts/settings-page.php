<h2 class="mt-3 mb-3 text-center text-wp-primary">Hello Again API Settings Page</h2>

<div class="api-credentials container shadow-sm p-4 pb-5">
    <h4 class="mt-3 text-center">API Credentials</h4>
    <div class="row mt-4">
        <div class="col-sm-4">
            <label for="api_base_url">API Base Url</label>
        </div>
        <div class="col-sm-8">
            <input type="text" name="api_base_url" id="api_base_url"
                value="<?php echo esc_attr( get_option( 'api_base_url' ) ); ?>" class="form-control" required>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label for="api_key">API Key</label>
        </div>
        <div class="col-sm-8">
            <input type="text" name="api_key" id="api_key" value="<?php echo esc_attr( get_option( 'api_key' ) ); ?>"
                class="form-control" required>
        </div>
    </div>
</div>

<div class="ha-options container shadow-sm mt-3 px-4 pt-0 pb-5">
    <h4 class="mt-3 text-center">Options</h4>

    <div class="row mt-4">
        <div class="col-sm-4">
            <label for="how_many_posts_to_display">Posts to display</label>
        </div>
        <div class="col-sm-8">
            <input type="number" name="how_many_posts_to_display" id="how_many_posts_to_display"
                value="<?php echo esc_attr( get_option( 'how_many_posts_to_display' ) ); ?>" class="form-control">
        </div>
    </div>
    <div class="save-btn-wrapper">
        <button type="button" class="btn btn-primary mt-4" id="save-btn">Save</button>
    </div>
</div>