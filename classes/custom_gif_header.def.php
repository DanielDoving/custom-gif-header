<?php

namespace gif_header;

use gif_header\query\query_response;

class custom_gif_header {
    private const FALLBACK_HEADER_ATTR = 'style="background-color:#000000;color:white;background-position:center;">';
    private const PARAMETERS_TO_REDIRECT = [
        'force_refresh_background',
        'bg-keyword'
    ];

    private int $time_elapsed;
    private int $last_background_time;
    private query_response $current_background;
    private string $current_background_topic;

    private function read_data_file(): bool {
        if (!file_exists(DATA_FILE)) {
            touch(DATA_FILE);
        }
        $data_file_contents             = file_get_contents(DATA_FILE);
        $data_file_contents             = json_decode($data_file_contents, true);
        $this->last_background_time     = $data_file_contents['last-background-time'] ?? time();
        $this->time_elapsed             = time() - $this->last_background_time;
        $this->current_background_topic = $data_file_contents['current-background-topic'] ?? '';
        $unserialized_background        = unserialize($data_file_contents['current-background'] ?? null);
        if ($unserialized_background) {
            $this->current_background = $unserialized_background;
        }
        return true;
    }

    private function save_data_file(query_response $query_response): void {
        $content = [
            'current-background'       => serialize($query_response),
            'current-background-topic' => $this->current_background_topic,
            'last-background-time'     => $this->last_background_time
        ];
        file_put_contents(DATA_FILE, json_encode($content, JSON_PRETTY_PRINT));
    }

    private function set_fallback_header(): void {
        define('DEVELOPMENT_CUSTOM_ADMIN_HEADER_ATTR', self::FALLBACK_HEADER_ATTR);
    }

    private function get_special(): string {
        $info            = '<strong>' . $this->current_background->get_special() . '</strong>';
        $time_until_next = '<span id="countdown">' . ((NEW_BACKGROUND_INTERVAL - $this->time_elapsed) >= 0 ? NEW_BACKGROUND_INTERVAL - $this->time_elapsed : 0) . '</span>s';
        return '<a id="bg-info-span">' . $info . htmlspecialchars($this->current_background_topic) . ' (' . $time_until_next . ')<a>';
    }

    public function set_header($css = null, $js = null): void {
        if (!mb_strpos($_SERVER['SCRIPT_FILENAME'], 'shop/Admin')) {
            return;
        }
        if (!file_exists(DATA_FILE)) {
            touch(DATA_FILE);
        }
        if (!$this->read_data_file()) {
            return;
        }

        $special = '';
        if ($this->is_new_bg_required()) {
            $query          = $this->get_random_keyword();
            $query_response = \gif_header\query\giphy::get_response($query['keyword'], $query['limit']);
            if ($query_response->is_success()) {
                $this->current_background       = $query_response;
                $this->last_background_time     = time();
                $this->current_background_topic = $query['keyword'];
                $this->save_data_file($query_response);
            }
        }
        $special = $this->get_special();

        $special       .= $js ?? '';
        $special       .= $css ?? '';
        $current_topic = ucwords($this->current_background_topic ?? '');

        foreach (self::PARAMETERS_TO_REDIRECT as $param) {
            if (isset($_GET[$param])) {
                $this->redirect_without_params();
            }
        }

        if (isset($this->current_background) && $this->current_background->is_success()) {
            if ($this->current_background->is_video()) {
                $background = '<video autoplay="" loop="" src="' . $this->current_background->get_url() . '"></video>';
                define('DEVELOPMENT_CUSTOM_ADMIN_HEADER_ATTR', 'class="custom-gif-header">' . $background . $special);
            }
            else {
                $size = $this->current_background->is_cover() ? 'background-size: cover;' : 'background-size: auto;';
                define('DEVELOPMENT_CUSTOM_ADMIN_HEADER_ATTR', 'class="custom-gif-header" style="background-image:url(' . $this->current_background->get_url() . ');' . $size . '">' . $special);
            }
        }
        else {
            $alert = "<script>$(document).ready(function (){Swal.fire('Warning', 'Giphy returned no results for Query \'$current_topic\'', 'warning');});</script>";
            define('DEVELOPMENT_CUSTOM_ADMIN_HEADER_ATTR', self::FALLBACK_HEADER_ATTR . $alert . $special);
        }
    }

    private function is_new_bg_required(): bool {
        if (!isset($this->current_background)) {
            return true;
        }
        $force_refresh = isset($_GET['force_refresh_background']) && $_GET['force_refresh_background'] == 'true';
        return $this->time_elapsed > NEW_BACKGROUND_INTERVAL || $force_refresh || !empty($_GET['bg-keyword']);
    }

    private function redirect_without_params(): void {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        foreach (self::PARAMETERS_TO_REDIRECT as $param) {
            $url = preg_replace('~(\?|&)' . $param . '=[^&]*~', '$1', $url);
        }
        $url = rtrim($url, '?&');
        header('Location: ' . $url);
    }

    private function get_random_keyword() {
        $wl_manager = new wordlist_manager();
        if (!empty($_GET['bg-keyword'])) {
            $key = $wl_manager->find_word($_GET['bg-keyword']);
            if ($key !== -1) {
                if (isset($wl_manager->wordlist[$key]['limit'])) {
                    return $wl_manager->wordlist[$key];
                }
                else {
                    return [
                        'keyword' => $wl_manager->wordlist[$key],
                        'limit'   => DEFAULT_LIMIT
                    ];
                }
            }
            return [
                'keyword' => $_GET['bg-keyword'],
                'limit'   => 20
            ];
        }
        shuffle($wl_manager->wordlist);
        $result = $wl_manager->wordlist[array_rand($wl_manager->wordlist)];
        if (isset($result['limit'])) {
            return $result;
        }
        return [
            'keyword' => $result,
            'limit'   => DEFAULT_LIMIT
        ];
    }
}
