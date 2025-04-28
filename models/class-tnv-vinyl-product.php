<?php
/**
 * Класс для работы с виниловыми продуктами
 */
class TNV_Vinyl_Product {
    
    /**
     * Регистрация дополнительных полей для винилового продукта
     */
    public function register_product_meta() {
        register_post_meta('product', '_tnv_release_id', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ));
        register_post_meta('product', '_tnv_artist', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ));
        register_post_meta('product', '_tnv_label', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ));
        register_post_meta('product', '_tnv_year', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ));
        register_post_meta('product', '_tnv_tracklist', array(
            'type' => 'array',
            'single' => true,
            'show_in_rest' => array(
                'schema' => array(
                    'items' => array(
                        'type' => 'object',
                        'properties' => array(
                            'position' => array('type' => 'string'),
                            'title' => array('type' => 'string'),
                            'duration' => array('type' => 'string'),
                        ),
                    ),
                ),
            ),
        ));
    }

    /**
     * Создание нового продукта из данных Discogs
     */
    public function create_from_discogs_data($data) {
        $product = new WC_Product_Simple();
        
        $product->set_name($data['title']);
        $product->set_status('draft');
        
        // Сохраняем метаданные
        $product->update_meta_data('_tnv_release_id', $data['release_id']);
        $product->update_meta_data('_tnv_artist', $data['artist']);
        $product->update_meta_data('_tnv_label', $data['label']);
        $product->update_meta_data('_tnv_year', $data['year']);
        $product->update_meta_data('_tnv_tracklist', $data['tracklist']);
        
        // Устанавливаем таксономии
        if (!empty($data['genre'])) {
            wp_set_object_terms($product->get_id(), $data['genre'], 'genre');
        }
        if (!empty($data['style'])) {
            wp_set_object_terms($product->get_id(), $data['style'], 'style');
        }
        if (!empty($data['artist'])) {
            wp_set_object_terms($product->get_id(), $data['artist'], 'artist');
        }
        if (!empty($data['label'])) {
            wp_set_object_terms($product->get_id(), $data['label'], 'label');
        }
        
        // Загружаем изображения
        if (!empty($data['images'])) {
            foreach ($data['images'] as $image_url) {
                $attachment_id = $this->upload_image_from_url($image_url);
                if ($attachment_id) {
                    $product->set_image_id($attachment_id);
                    break;
                }
            }
        }
        
        $product->save();
        return $product;
    }
    
    /**
     * Загрузка изображения из URL
     */
    private function upload_image_from_url($url) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $tmp = download_url($url);
        if (is_wp_error($tmp)) {
            return false;
        }
        
        $file_array = array(
            'name' => basename($url),
            'tmp_name' => $tmp
        );
        
        $id = media_handle_sideload($file_array, 0);
        if (is_wp_error($id)) {
            @unlink($tmp);
            return false;
        }
        
        return $id;
    }
    
    /**
     * Получение данных предпрослушивания для продукта
     */
    public function get_preview_data($product_id) {
        $product = wc_get_product($product_id);
        if (!$product) {
            return false;
        }
        
        $artist = $product->get_meta('_tnv_artist');
        $tracklist = $product->get_meta('_tnv_tracklist');
        
        if (empty($artist) || empty($tracklist)) {
            return false;
        }
        
        return array(
            'artist' => $artist,
            'tracks' => $tracklist
        );
    }
}