<?php
/**
 * Контроллер для работы с виниловыми пластинками
 */
class TNV_Product_Controller {

    private $discogs_api;
    private $music_preview;
    private $vinyl_product;

    public function __construct() {
        $this->discogs_api = new TNV_Discogs_API();
        $this->music_preview = new TNV_Music_Preview();
        $this->vinyl_product = new TNV_Vinyl_Product();

        // Хуки для WooCommerce
        add_action('init', array($this, 'register_vinyl_taxonomies'));
        add_action('woocommerce_product_data_tabs', array($this, 'add_vinyl_product_data_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'add_vinyl_product_data_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_vinyl_product_data'));
        add_action('woocommerce_before_single_product', array($this, 'add_music_preview_player'));
        
        // Добавляем кнопку сканирования QR на странице редактирования товара
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_qr_scan_button'));
    }

    /**
     * Регистрация таксономий для виниловых пластинок
     */
    public function register_vinyl_taxonomies() {
        // Регистрация таксономии Artist (Исполнитель)
        register_taxonomy('vinyl_artist', 'product', array(
            'labels' => array(
                'name' => __('Artists', 'tonna-vinyla'),
                'singular_name' => __('Artist', 'tonna-vinyla'),
                'menu_name' => __('Artists', 'tonna-vinyla'),
                'all_items' => __('All Artists', 'tonna-vinyla'),
                'edit_item' => __('Edit Artist', 'tonna-vinyla'),
                'view_item' => __('View Artist', 'tonna-vinyla'),
                'update_item' => __('Update Artist', 'tonna-vinyla'),
                'add_new_item' => __('Add New Artist', 'tonna-vinyla'),
                'new_item_name' => __('New Artist Name', 'tonna-vinyla'),
                'search_items' => __('Search Artists', 'tonna-vinyla'),
            ),
            'public' => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'show_admin_column' => true,
        ));

        // Регистрация таксономии Genre (Жанр)
        register_taxonomy('vinyl_genre', 'product', array(
            'labels' => array(
                'name' => __('Genres', 'tonna-vinyla'),
                'singular_name' => __('Genre', 'tonna-vinyla'),
                'menu_name' => __('Genres', 'tonna-vinyla'),
                'all_items' => __('All Genres', 'tonna-vinyla'),
                'edit_item' => __('Edit Genre', 'tonna-vinyla'),
                'view_item' => __('View Genre', 'tonna-vinyla'),
                'update_item' => __('Update Genre', 'tonna-vinyla'),
                'add_new_item' => __('Add New Genre', 'tonna-vinyla'),
                'new_item_name' => __('New Genre Name', 'tonna-vinyla'),
                'search_items' => __('Search Genres', 'tonna-vinyla'),
            ),
            'public' => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
        ));

        // Регистрация таксономии Label (Лейбл)
        register_taxonomy('vinyl_label', 'product', array(
            'labels' => array(
                'name' => __('Labels', 'tonna-vinyla'),
                'singular_name' => __('Label', 'tonna-vinyla'),
                'menu_name' => __('Labels', 'tonna-vinyla'),
                'all_items' => __('All Labels', 'tonna-vinyla'),
                'edit_item' => __('Edit Label', 'tonna-vinyla'),
                'view_item' => __('View Label', 'tonna-vinyla'),
                'update_item' => __('Update Label', 'tonna-vinyla'),
                'add_new_item' => __('Add New Label', 'tonna-vinyla'),
                'new_item_name' => __('New Label Name', 'tonna-vinyla'),
                'search_items' => __('Search Labels', 'tonna-vinyla'),
            ),
            'public' => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'show_admin_column' => true,
        ));

        // Регистрация таксономии Style (Стиль)
        register_taxonomy('vinyl_style', 'product', array(
            'labels' => array(
                'name' => __('Styles', 'tonna-vinyla'),
                'singular_name' => __('Style', 'tonna-vinyla'),
                'menu_name' => __('Styles', 'tonna-vinyla'),
                'all_items' => __('All Styles', 'tonna-vinyla'),
                'edit_item' => __('Edit Style', 'tonna-vinyla'),
                'view_item' => __('View Style', 'tonna-vinyla'),
                'update_item' => __('Update Style', 'tonna-vinyla'),
                'add_new_item' => __('Add New Style', 'tonna-vinyla'),
                'new_item_name' => __('New Style Name', 'tonna-vinyla'),
                'search_items' => __('Search Styles', 'tonna-vinyla'),
            ),
            'public' => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
        ));
    }

    /**
     * Добавление вкладки с данными винила
     */
    public function add_vinyl_product_data_tab($tabs) {
        $tabs['vinyl'] = array(
            'label' => __('Vinyl Details', 'tonna-vinyla'),
            'target' => 'vinyl_product_data',
            'class' => array('show_if_simple', 'show_if_variable'),
            'priority' => 21
        );
        return $tabs;
    }

    /**
     * Добавление полей для данных винила
     */
    public function add_vinyl_product_data_fields() {
        global $post;
        
        // Загружаем шаблон с полями
        include TNV_PLUGIN_DIR . 'views/admin/partials/vinyl-product-data.php';
    }

    /**
     * Сохранение данных винила
     */
    public function save_vinyl_product_data($post_id) {
        // Сохраняем основные поля
        $fields = array('_tnv_artist', '_tnv_label', '_tnv_year');
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        // Сохраняем трек-лист
        if (isset($_POST['tnv_tracklist']) && is_array($_POST['tnv_tracklist'])) {
            $tracklist = array();
            foreach ($_POST['tnv_tracklist'] as $track) {
                if (!empty($track['title'])) {
                    $tracklist[] = array(
                        'position' => sanitize_text_field($track['position']),
                        'title' => sanitize_text_field($track['title']),
                        'duration' => sanitize_text_field($track['duration'])
                    );
                }
            }
            update_post_meta($post_id, '_tnv_tracklist', $tracklist);
        }
    }

    /**
     * Добавление плеера для предпрослушивания
     */
    public function add_music_preview_player() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        // Загружаем шаблон плеера предпрослушивания
        include TNV_PLUGIN_DIR . 'views/public/partials/music-preview-modal.php';
    }

    /**
     * Подключение скриптов для админки
     */
    public function enqueue_admin_scripts($hook) {
        if ('post.php' != $hook && 'post-new.php' != $hook) {
            return;
        }
        wp_enqueue_script('tnv-qr-scanner', TNV_PLUGIN_URL . 'assets/js/qr-scanner.js', array('jquery'), TNV_VERSION, true);
    }

    /**
     * Добавление кнопки сканирования QR
     */
    public function add_qr_scan_button() {
        echo '<div class="options_group">';
        echo '<button type="button" class="button" id="tnv-scan-qr">' . __('Scan QR Code', 'tonna-vinyla') . '</button>';
        echo '<div id="tnv-qr-scanner-container"></div>';
        echo '</div>';
    }
}