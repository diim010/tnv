<?php
/**
 * Выполняется при активации плагина
 */
class TNV_Activator {

    public static function activate() {
        // Проверяем наличие WooCommerce
        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            wp_die(__('This plugin requires WooCommerce to be installed and active.', 'tonna-vinyla'));
        }

        // Создаем дефолтные настройки
        self::create_default_settings();

        // Устанавливаем права доступа
        self::setup_capabilities();

        // Создаем необходимые директории
        self::create_directories();

        // Очищаем кэш правил перезаписи
        flush_rewrite_rules();
    }

    /**
     * Создание настроек по умолчанию
     */
    private static function create_default_settings() {
        $default_settings = array(
            'tnv_discogs_api_token' => '',
            'tnv_spotify_client_id' => '',
            'tnv_spotify_client_secret' => '',
            'tnv_youtube_api_key' => '',
        );

        foreach ($default_settings as $option_name => $default_value) {
            if (get_option($option_name) === false) {
                add_option($option_name, $default_value);
            }
        }
    }

    /**
     * Настройка прав доступа
     */
    private static function setup_capabilities() {
        $admin_role = get_role('administrator');
        
        if ($admin_role) {
            $admin_role->add_cap('manage_vinyl_products');
            $admin_role->add_cap('edit_vinyl_products');
            $admin_role->add_cap('delete_vinyl_products');
        }

        $shop_manager_role = get_role('shop_manager');
        
        if ($shop_manager_role) {
            $shop_manager_role->add_cap('manage_vinyl_products');
            $shop_manager_role->add_cap('edit_vinyl_products');
            $shop_manager_role->add_cap('delete_vinyl_products');
        }
    }

    /**
     * Создание необходимых директорий
     */
    private static function create_directories() {
        // Создаем директорию для загруженных обложек
        $upload_dir = wp_upload_dir();
        $vinyl_covers_dir = $upload_dir['basedir'] . '/vinyl-covers';
        
        if (!file_exists($vinyl_covers_dir)) {
            wp_mkdir_p($vinyl_covers_dir);
        }

        // Создаем .htaccess для защиты директории
        $htaccess_file = $vinyl_covers_dir . '/.htaccess';
        if (!file_exists($htaccess_file)) {
            $htaccess_content = "Options -Indexes\n";
            $htaccess_content .= "<Files *.php>\n";
            $htaccess_content .= "deny from all\n";
            $htaccess_content .= "</Files>";
            
            file_put_contents($htaccess_file, $htaccess_content);
        }
    }
}