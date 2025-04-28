<?php
/**
 * Контроллер для административной части плагина
 */
class TNV_Admin_Controller {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Добавление пункта меню в админ-панель
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Tonna Vinyla Settings', 'tonna-vinyla'),
            __('Tonna Vinyla', 'tonna-vinyla'),
            'manage_options',
            'tonna-vinyla',
            array($this, 'render_settings_page'),
            'dashicons-album',
            56
        );
    }

    /**
     * Регистрация настроек плагина
     */
    public function register_settings() {
        register_setting('tnv_settings', 'tnv_discogs_api_token');
        register_setting('tnv_settings', 'tnv_spotify_client_id');
        register_setting('tnv_settings', 'tnv_spotify_client_secret');
        register_setting('tnv_settings', 'tnv_youtube_api_key');

        add_settings_section(
            'tnv_api_settings',
            __('API Settings', 'tonna-vinyla'),
            array($this, 'render_api_settings_section'),
            'tonna-vinyla'
        );

        add_settings_field(
            'tnv_discogs_api_token',
            __('Discogs API Token', 'tonna-vinyla'),
            array($this, 'render_text_field'),
            'tonna-vinyla',
            'tnv_api_settings',
            array('label_for' => 'tnv_discogs_api_token')
        );

        add_settings_field(
            'tnv_spotify_client_id',
            __('Spotify Client ID', 'tonna-vinyla'),
            array($this, 'render_text_field'),
            'tonna-vinyla',
            'tnv_api_settings',
            array('label_for' => 'tnv_spotify_client_id')
        );

        add_settings_field(
            'tnv_spotify_client_secret',
            __('Spotify Client Secret', 'tonna-vinyla'),
            array($this, 'render_text_field'),
            'tonna-vinyla',
            'tnv_api_settings',
            array('label_for' => 'tnv_spotify_client_secret')
        );

        add_settings_field(
            'tnv_youtube_api_key',
            __('YouTube API Key', 'tonna-vinyla'),
            array($this, 'render_text_field'),
            'tonna-vinyla',
            'tnv_api_settings',
            array('label_for' => 'tnv_youtube_api_key')
        );
    }

    /**
     * Отрисовка страницы настроек
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('tnv_settings');
                do_settings_sections('tonna-vinyla');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Отрисовка секции API настроек
     */
    public function render_api_settings_section($args) {
        ?>
        <p><?php _e('Enter your API credentials for various services below:', 'tonna-vinyla'); ?></p>
        <?php
    }

    /**
     * Отрисовка текстового поля
     */
    public function render_text_field($args) {
        $option = str_replace('[]', '', $args['label_for']);
        $value = get_option($option);
        ?>
        <input type="text" 
               id="<?php echo esc_attr($args['label_for']); ?>"
               name="<?php echo esc_attr($args['label_for']); ?>"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text">
        <?php
    }
}
