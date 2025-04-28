<?php
/**
 * Класс для работы с Discogs API
 */
class TNV_Discogs_API {
    private $api_token;
    private $api_url = 'https://api.discogs.com';

    public function __construct() {
        $this->api_token = get_option('tnv_discogs_api_token');
    }

    /**
     * Поиск пластинки по штрих-коду
     */
    public function search_by_barcode($barcode) {
        $response = wp_remote_get($this->api_url . '/database/search?barcode=' . urlencode($barcode), array(
            'headers' => array(
                'Authorization' => 'Discogs token=' . $this->api_token,
                'User-Agent' => 'TonnaVinylaPlugin/1.0'
            )
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body);
    }

    /**
     * Получение детальной информации о релизе
     */
    public function get_release_details($release_id) {
        $response = wp_remote_get($this->api_url . '/releases/' . $release_id, array(
            'headers' => array(
                'Authorization' => 'Discogs token=' . $this->api_token,
                'User-Agent' => 'TonnaVinylaPlugin/1.0'
            )
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body);
    }

    /**
     * Конвертация данных Discogs в формат WooCommerce
     */
    public function convert_to_wc_product_data($release) {
        return array(
            'title' => $release->title,
            'artist' => isset($release->artists[0]->name) ? $release->artists[0]->name : '',
            'genre' => isset($release->genres) ? $release->genres : array(),
            'style' => isset($release->styles) ? $release->styles : array(),
            'label' => isset($release->labels[0]->name) ? $release->labels[0]->name : '',
            'year' => isset($release->year) ? $release->year : '',
            'tracklist' => isset($release->tracklist) ? $release->tracklist : array(),
            'images' => isset($release->images) ? $release->images : array(),
        );
    }
}