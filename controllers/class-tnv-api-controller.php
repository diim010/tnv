<?php
/**
 * Контроллер для работы с внешними API
 */
class TNV_API_Controller {

    private $discogs_api;
    private $music_preview;

    public function __construct() {
        $this->discogs_api = new TNV_Discogs_API();
        $this->music_preview = new TNV_Music_Preview();

        // Регистрируем REST API эндпоинты
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // Подключаем скрипты для предпрослушивания
        add_action('wp_enqueue_scripts', array($this, 'enqueue_preview_scripts'));
    }

    /**
     * Регистрация REST API маршрутов
     */
    public function register_rest_routes() {
        register_rest_route('tnv/v1', '/search-release', array(
            'methods' => 'GET',
            'callback' => array($this, 'search_release'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => array(
                'barcode' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                )
            )
        ));

        register_rest_route('tnv/v1', '/preview', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_music_preview'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => array(
                'artist' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'track' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                )
            )
        ));
    }

    /**
     * Проверка прав доступа к API
     */
    public function check_permissions() {
        // Для публичного доступа
        return true;
    }

    /**
     * Поиск релиза по штрих-коду
     */
    public function search_release($request) {
        $barcode = $request->get_param('barcode');
        
        $release = $this->discogs_api->search_by_barcode($barcode);
        if (!$release) {
            return new WP_Error(
                'release_not_found',
                __('Release not found', 'tonna-vinyla'),
                array('status' => 404)
            );
        }

        $details = $this->discogs_api->get_release_details($release->id);
        if (!$details) {
            return new WP_Error(
                'release_details_not_found',
                __('Could not fetch release details', 'tonna-vinyla'),
                array('status' => 404)
            );
        }

        return rest_ensure_response($this->discogs_api->convert_to_wc_product_data($details));
    }

    /**
     * Получение предпрослушивания музыки
     */
    public function get_music_preview($request) {
        $artist = $request->get_param('artist');
        $track = $request->get_param('track');

        $preview_data = array(
            'spotify' => null,
            'youtube' => null
        );

        // Поиск на Spotify
        $spotify_track = $this->music_preview->search_spotify_track($artist, $track);
        if ($spotify_track) {
            $preview_data['spotify'] = $spotify_track;
        }

        // Поиск на YouTube
        $youtube_video = $this->music_preview->search_youtube_video($artist, $track);
        if ($youtube_video) {
            $preview_data['youtube'] = $youtube_video;
        }

        if (!$preview_data['spotify'] && !$preview_data['youtube']) {
            return new WP_Error(
                'preview_not_found',
                __('No preview available', 'tonna-vinyla'),
                array('status' => 404)
            );
        }

        return rest_ensure_response($preview_data);
    }

    /**
     * Подключение скриптов для предпрослушивания
     */
    public function enqueue_preview_scripts() {
        if (!is_product()) {
            return;
        }

        wp_enqueue_script(
            'tnv-music-preview',
            TNV_PLUGIN_URL . 'views/public/js/music-preview.js',
            array('jquery'),
            TNV_VERSION,
            true
        );

        wp_localize_script('tnv-music-preview', 'tnv_preview', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tnv_preview_nonce'),
            'loading_text' => __('Loading preview...', 'tonna-vinyla'),
            'error_text' => __('Preview not available', 'tonna-vinyla')
        ));
    }
}