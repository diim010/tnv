<?php
/**
 * Класс для работы с предпрослушиванием музыки
 */
class TNV_Music_Preview {
    private $spotify_client_id;
    private $spotify_client_secret;
    private $youtube_api_key;

    public function __construct() {
        $this->spotify_client_id = get_option('tnv_spotify_client_id');
        $this->spotify_client_secret = get_option('tnv_spotify_client_secret');
        $this->youtube_api_key = get_option('tnv_youtube_api_key');
    }

    /**
     * Поиск трека на Spotify
     */
    public function search_spotify_track($artist, $track) {
        // Получаем токен доступа
        $token = $this->get_spotify_access_token();
        if (!$token) {
            return false;
        }

        $query = urlencode($artist . ' ' . $track);
        $response = wp_remote_get('https://api.spotify.com/v1/search?q=' . $query . '&type=track&limit=1', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token
            )
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response));
        if (isset($body->tracks->items[0])) {
            return $body->tracks->items[0];
        }

        return false;
    }

    /**
     * Поиск видео на YouTube
     */
    public function search_youtube_video($artist, $track) {
        $query = urlencode($artist . ' ' . $track);
        $response = wp_remote_get('https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $query . '&type=video&key=' . $this->youtube_api_key);

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response));
        if (isset($body->items[0])) {
            return $body->items[0];
        }

        return false;
    }

    /**
     * Получение токена доступа Spotify
     */
    private function get_spotify_access_token() {
        $token = get_transient('tnv_spotify_access_token');
        if ($token) {
            return $token;
        }

        $response = wp_remote_post('https://accounts.spotify.com/api/token', array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($this->spotify_client_id . ':' . $this->spotify_client_secret),
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
            'body' => array(
                'grant_type' => 'client_credentials'
            )
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response));
        if (isset($body->access_token)) {
            set_transient('tnv_spotify_access_token', $body->access_token, 3500); // Токен действителен 1 час
            return $body->access_token;
        }

        return false;
    }

    /**
     * Создание HTML для плеера предпрослушивания
     */
    public function get_preview_player_html($spotify_track = null, $youtube_video = null) {
        $html = '<div class="tnv-music-preview">';
        
        if ($spotify_track) {
            $html .= sprintf(
                '<iframe src="https://open.spotify.com/embed/track/%s" width="300" height="80" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>',
                esc_attr($spotify_track->id)
            );
        }
        
        if ($youtube_video) {
            $html .= sprintf(
                '<iframe width="560" height="315" src="https://www.youtube.com/embed/%s" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
                esc_attr($youtube_video->id->videoId)
            );
        }
        
        $html .= '</div>';
        return $html;
    }
}