<h2 class="settings-title">Hello Again API Settings Page</h2>

<div class="api-credentials">
    <h4 class="section-title">API Credentials</h4>
    <div class="form-group">
        <label for="api_base_url">API Base URL</label>
        <input type="text" name="api_base_url" placeholder="Api base url" id="api_base_url"
            value="<?php echo esc_attr( get_option( 'api_base_url' ) ); ?>" required>
    </div>
    <div class="form-group">
        <label for="api_key">API Key</label>
        <input type="text" name="api_key" id="api_key" placeholder="Api key"
            value="<?php echo esc_attr( get_option( 'api_key' ) ); ?>" required>
    </div>
</div>

<div class="ha-options">
    <h4 class="section-title">Options</h4>
    <div class="form-group">
        <label for="how_many_posts_to_display">Posts to display</label>
        <input type="number" name="how_many_posts_to_display" placeholder="How many posts to display"
            id="how_many_posts_to_display" value="<?php echo esc_attr( get_option( 'how_many_posts_to_display' ) ); ?>">
    </div>
    <div class="save-btn-wrapper">
        <button type="button" id="save-btn">Save</button>
    </div>
</div>

<div class="ha-endpoints">
    <h4 class="section-title">Endpoints</h4>
    <?php
    $site_url = site_url();
    ?>

    <p>Insert Users to Database: <?= $site_url . '/wp-json/hello-again/v1/insert-users' ?></p>
    <p>Sync Users: <?= $site_url . '/wp-json/hello-again/v1/sync-users' ?></p>
    <p>Insert Shops to Database: <?= $site_url . '/wp-json/hello-again/v1/insert-shops' ?></p>
    <p>Sync Shops: <?= $site_url . '/wp-json/hello-again/v1/sync-shops' ?></p>

</div>