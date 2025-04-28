<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if (isset($message)): ?>
        <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="options.php">
        <?php settings_fields('tnv_settings'); ?>
        <?php do_settings_sections('tonna-vinyla'); ?>
        
        <h2><?php _e('API Settings', 'tonna-vinyla'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="tnv_discogs_api_token"><?php _e('Discogs API Token', 'tonna-vinyla'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           id="tnv_discogs_api_token" 
                           name="tnv_discogs_api_token" 
                           value="<?php echo esc_attr(get_option('tnv_discogs_api_token')); ?>" 
                           class="regular-text">
                    <p class="description">
                        <?php _e('Enter your Discogs API token. You can get it from your Discogs account settings.', 'tonna-vinyla'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="tnv_spotify_client_id"><?php _e('Spotify Client ID', 'tonna-vinyla'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           id="tnv_spotify_client_id" 
                           name="tnv_spotify_client_id" 
                           value="<?php echo esc_attr(get_option('tnv_spotify_client_id')); ?>" 
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="tnv_spotify_client_secret"><?php _e('Spotify Client Secret', 'tonna-vinyla'); ?></label>
                </th>
                <td>
                    <input type="password" 
                           id="tnv_spotify_client_secret" 
                           name="tnv_spotify_client_secret" 
                           value="<?php echo esc_attr(get_option('tnv_spotify_client_secret')); ?>" 
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="tnv_youtube_api_key"><?php _e('YouTube API Key', 'tonna-vinyla'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           id="tnv_youtube_api_key" 
                           name="tnv_youtube_api_key" 
                           value="<?php echo esc_attr(get_option('tnv_youtube_api_key')); ?>" 
                           class="regular-text">
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>