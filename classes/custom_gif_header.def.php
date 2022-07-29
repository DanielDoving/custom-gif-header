<?php

require_once __DIR__ . '/wordlist_manager.def.php';
require_once dirname(__DIR__) . '/config.inc.php';

class custom_gif_header {
    private const GIPHY_API_BASE_URL = 'https://api.giphy.com/v1/gifs';
    private const FALLBACK_HEADER_ATTR = 'style="background-color:#000000;color:white;background-position:center;">';

    private $giphy_api_key;
    private $wordlist;
    private $time_elapsed;

    public function __construct() {
        $this->giphy_api_key = GIPHY_API_KEY;
        $this->wordlist      = json_decode(file_get_contents(WORDLIST_FILE), true);
    }

    public function set_header($css = null, $js = null) {
        if (!mb_strpos($_SERVER['SCRIPT_FILENAME'], 'shop/Admin')) {
            return;
        }
        if (!file_exists(DATA_FILE)) {
            touch(DATA_FILE);
        }
        $content = file_get_contents(DATA_FILE);
        $content = json_decode($content, true);
        if (!isset($content['last-background-time'])) {
            $content['last-background-time'] = time();
        }
        $this->time_elapsed = time() - $content['last-background-time'];

        $query = $this->get_random_keyword();
        if (!isset($content['current-background']) || $this->is_new_bg_required()) {
            $content['current-background']       = $this->query_giphy($query['keyword'], $query['limit']);
            $content['last-background-time']     = time();
            $content['current-background-topic'] = $query['keyword'];
        }


        file_put_contents(DATA_FILE, json_encode($content, JSON_PRETTY_PRINT));
        $background      = $content['current-background'];
        $current_topic   = ucwords($content['current-background-topic'] ?? '');
        $time_until_next = '<span id="countdown">' . ((NEW_BACKGROUND_INTERVAL - $this->time_elapsed) >= 0 ? NEW_BACKGROUND_INTERVAL - $this->time_elapsed : 0) . '</span>s';

        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (mb_strpos($url, '?')) {
            $url .= '&force_refresh_background=true';
        }
        else {
            $url .= '?force_refresh_background=true';
        }
        // GET Param aus der url entfernen und redirecten
        if (isset($_GET['force_refresh_background']) || isset($_GET['bg-keyword'])) {
            $this->redirect_without_params($url);
        }

        $info = '';
        if ($background['video'] && $background['4k']) {
            $info = '<strong>4K</strong>&nbsp;';
        }
        else if ($background['video']) {
            $info = '<strong>HD</strong>&nbsp;';
        }
        $info_span = '<a id="bg-info-span">' . $info . $current_topic . ' (' . $time_until_next . ')<a>';
        if ($background) {
            if ($background['video']) {
                $background = '<video autoplay="" loop="" src="' . $background['url'] . '"></video>';
                define('DEVELOPMENT_CUSTOM_ADMIN_HEADER_ATTR', 'class="custom-gif-header">' . $background . $info_span . ($js ?? '') . ($css ?? ''));
            }
            else {
                $size = $background['cover'] ? 'background-size: cover;' : 'background-size: auto;';
                define('DEVELOPMENT_CUSTOM_ADMIN_HEADER_ATTR', 'class="custom-gif-header" style="background-image:url(' . $background['url'] . ');' . $size . '">' . $info_span . ($js ?? '') . ($css ?? ''));
            }
        }
        else {
            $alert = "<script>$(document).ready(function (){Swal.fire('Warning', 'Giphy returned no results for Query \'$current_topic\'', 'warning');});</script>";
            define('DEVELOPMENT_CUSTOM_ADMIN_HEADER_ATTR', self::FALLBACK_HEADER_ATTR . $info_span . $alert . ($js ?? '') . ($css ?? ''));
        }
    }

    private function is_new_bg_required() {
        $force_refresh = isset($_GET['force_refresh_background']) && $_GET['force_refresh_background'] == 'true';
        return $this->time_elapsed > NEW_BACKGROUND_INTERVAL || $force_refresh || !empty($_GET['bg-keyword']);
    }

    private function redirect_without_params($link) {
        $link = preg_replace('~(\?|&)force_refresh_background=[^&]*~', '$1', $link);
        $link = preg_replace('~(\?|&)bg-keyword=[^&]*~', '$1', $link);
        $link = rtrim($link, '?&');
        header('Location: ' . $link);
    }

    private function get_random_keyword() {
        if (!empty($_GET['bg-keyword'])) {
            $wl_manager = new \wordlist_manager();
            $key        = $wl_manager->find_word($_GET['bg-keyword']);
            if ($key !== -1) {
                if (isset($this->wordlist[$key]['limit'])) {
                    return $this->wordlist[$key];
                }
                else {
                    return [
                        'keyword' => $this->wordlist[$key],
                        'limit'   => DEFAULT_LIMIT
                    ];
                }
            }
            return [
                'keyword' => $_GET['bg-keyword'],
                'limit'   => 20
            ];
        }
        shuffle($this->wordlist);
        $result = $this->wordlist[array_rand($this->wordlist)];
        if (isset($result['limit'])) {
            return $result;
        }
        return [
            'keyword' => $result,
            'limit'   => DEFAULT_LIMIT
        ];
    }

    private function query_giphy($query, $limit) {
        if (!GIPHY_RANDOM_ENDPOINT) {
            $base_url     = self::GIPHY_API_BASE_URL . '/search?';
            $query_string = http_build_query([
                'q'       => $query,
                'api_key' => $this->giphy_api_key,
                'limit'   => $limit,
            ]);

        }
        else {
            $base_url     = self::GIPHY_API_BASE_URL . '/random?';
            $query_string = http_build_query([
                'tag'     => $query,
                'api_key' => $this->giphy_api_key,
            ]);
        }

        $response = file_get_contents($base_url . $query_string);
        $response = json_decode($response, true);

        if (!isset($response['data']) || !$response['data']) {
            return false;
        }
        $response = $response['data'];
        if (!GIPHY_RANDOM_ENDPOINT) {
            shuffle($response);
            $response = $response[array_rand($response)];
        }

        $video = false;
        $cover = true;
        $uhd   = false;
        if (isset($response['images']['4k'])) {
            $response = $response['images']['4k']['mp4'];
            $uhd      = true;
            $video    = true;
        }
        elseif (isset($response['images']['hd'])) {
            $response = $response['images']['hd']['mp4'];
            $video    = true;
        }
        else {
            $response = $response['images']['original'];
            $cover    = $response['width'] >= MIN_WIDTH_COVER && ($response['width'] / $response['height']) > MIN_ASPECT_RATIO_COVER;
            $response = $response['webp'];
        }

        return [
            'url'   => $response,
            'cover' => $cover,
            'video' => $video,
            '4k'    => $uhd,
        ];
    }
}
