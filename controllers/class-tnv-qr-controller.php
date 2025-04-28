<?php
/**
 * Контроллер для работы с QR-кодами
 */
class TNV_QR_Controller {

    private $discogs_api;

    public function __construct() {
        $this->discogs_api = new TNV_Discogs_API();
        
        // Добавляем AJAX обработчики
        add_action('wp_ajax_tnv_scan_qr', array($this, 'handle_qr_scan'));
        add_action('wp_ajax_nopriv_tnv_scan_qr', array($this, 'handle_qr_scan'));
        
        // Подключаем скрипты для QR
        add_action('admin_enqueue_scripts', array($this, 'enqueue_qr_scripts'));
    }

    /**
     * Подключение скриптов для QR сканера
     */
    public function enqueue_qr_scripts($hook) {
        if ('post.php' != $hook && 'post-new.php' != $hook) {
            return;
        }

        wp_enqueue_script(
            'tnv-qr-scanner',
            TNV_PLUGIN_URL . 'assets/js/qr-scanner.js',
            array('jquery'),
            TNV_VERSION,
            true
        );

        wp_localize_script('tnv-qr-scanner', 'tnv_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tnv_qr_nonce'),
            'start_scanning' => __('Start Scanning', 'tonna-vinyla'),
            'stop_scanning' => __('Stop Scanning', 'tonna-vinyla'),
            'camera_error' => __('Could not access camera', 'tonna-vinyla'),
            'request_error' => __('Error processing request', 'tonna-vinyla'),
            'position_placeholder' => __('Position', 'tonna-vinyla'),
            'title_placeholder' => __('Track Title', 'tonna-vinyla'),
            'duration_placeholder' => __('Duration', 'tonna-vinyla'),
            'remove_track' => __('Remove', 'tonna-vinyla')
        ));
    }

    /**
     * Обработка результатов сканирования QR
     */
    public function handle_qr_scan() {
        check_ajax_referer('tnv_qr_nonce', 'nonce');

        $barcode = sanitize_text_field($_POST['barcode']);
        if (empty($barcode)) {
            wp_send_json_error('Barcode is required');
        }

        // Поиск релиза на Discogs по штрих-коду
        $release = $this->discogs_api->search_by_barcode($barcode);
        if (!$release) {
            wp_send_json_error('Release not found on Discogs');
        }

        // Получаем детальную информацию о релизе
        $details = $this->discogs_api->get_release_details($release->id);
        if (!$details) {
            wp_send_json_error('Could not fetch release details');
        }

        // Конвертируем данные в формат WooCommerce
        $product_data = $this->discogs_api->convert_to_wc_product_data($details);
        
        wp_send_json_success($product_data);
    }
}