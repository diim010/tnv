<?php
/**
 * Основной класс плагина
 */
class Tonna_Vinyla {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = TNV_VERSION;
        $this->plugin_name = TNV_PLUGIN_NAME;
        
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Загрузка зависимостей
     */
    private function load_dependencies() {
        require_once TNV_PLUGIN_DIR . 'includes/class-tnv-loader.php';
        require_once TNV_PLUGIN_DIR . 'includes/class-tnv-i18n.php';

        // Загрузка моделей
        require_once TNV_PLUGIN_DIR . 'models/class-tnv-discogs-api.php';
        require_once TNV_PLUGIN_DIR . 'models/class-tnv-music-preview.php';
        require_once TNV_PLUGIN_DIR . 'models/class-tnv-vinyl-product.php';

        // Загрузка контроллеров
        require_once TNV_PLUGIN_DIR . 'controllers/class-tnv-admin-controller.php';
        require_once TNV_PLUGIN_DIR . 'controllers/class-tnv-api-controller.php';
        require_once TNV_PLUGIN_DIR . 'controllers/class-tnv-product-controller.php';
        require_once TNV_PLUGIN_DIR . 'controllers/class-tnv-qr-controller.php';

        $this->loader = new TNV_Loader();
    }

    /**
     * Настройка локализации
     */
    private function set_locale() {
        $plugin_i18n = new TNV_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Регистрация административных хуков
     */
    private function define_admin_hooks() {
        // Инициализация контроллеров
        $admin_controller = new TNV_Admin_Controller();
        $product_controller = new TNV_Product_Controller();
        $qr_controller = new TNV_QR_Controller();
        
        // Регистрация таксономий при инициализации
        $this->loader->add_action('init', $product_controller, 'register_vinyl_taxonomies');

        // Подключение административных стилей и скриптов
        $this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_admin_assets');
    }

    /**
     * Регистрация публичных хуков
     */
    private function define_public_hooks() {
        // Инициализация API контроллера
        $api_controller = new TNV_API_Controller();

        // Подключение публичных стилей и скриптов
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_public_assets');
    }

    /**
     * Подключение административных стилей и скриптов
     */
    public function enqueue_admin_assets($hook) {
        if ('post.php' != $hook && 'post-new.php' != $hook) {
            return;
        }

        // Стили админки
        wp_enqueue_style(
            'tnv-admin',
            TNV_PLUGIN_URL . 'views/admin/css/admin.css',
            array(),
            $this->version
        );

        // Библиотека QR-сканера
        wp_enqueue_script(
            'qr-scanner-lib',
            TNV_PLUGIN_URL . 'assets/js/qr-scanner.min.js',
            array(),
            $this->version,
            true
        );

        // Основной скрипт QR-сканера
        wp_enqueue_script(
            'tnv-qr-scanner',
            TNV_PLUGIN_URL . 'views/admin/js/qr-scanner.js',
            array('jquery', 'qr-scanner-lib'),
            $this->version,
            true
        );

        // Локализация скрипта
        wp_localize_script('tnv-qr-scanner', 'tnvQR', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tnv_qr_nonce'),
            'start_scanning' => __('Start Scanning', 'tonna-vinyla'),
            'stop_scanning' => __('Stop Scanning', 'tonna-vinyla'),
            'camera_error' => __('Could not access camera', 'tonna-vinyla'),
            'request_error' => __('Error processing request', 'tonna-vinyla')
        ));
    }

    /**
     * Подключение публичных стилей и скриптов
     */
    public function enqueue_public_assets() {
        // Стили для публичной части
        wp_enqueue_style(
            'tnv-public',
            TNV_PLUGIN_URL . 'views/public/css/public.css',
            array(),
            $this->version
        );

        // Скрипт предпрослушивания музыки
        wp_enqueue_script(
            'tnv-music-preview',
            TNV_PLUGIN_URL . 'views/public/js/music-preview.js',
            array('jquery'),
            $this->version,
            true
        );

        // Локализация скрипта
        wp_localize_script('tnv-music-preview', 'tnvPreview', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tnv_preview_nonce')
        ));
    }

    /**
     * Запуск загрузчика для выполнения всех хуков
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Получение имени плагина
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Получение загрузчика
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Получение версии плагина
     */
    public function get_version() {
        return $this->version;
    }
}