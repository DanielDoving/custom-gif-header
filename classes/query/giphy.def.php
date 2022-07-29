<?php

namespace gif_header\query;

class giphy implements query_interface {
    private const GIPHY_API_BASE_URL = 'https://api.giphy.com/v1/gifs';

    public static function get_response(string $keyword, int $limit): query_response {
        if (!GIPHY_RANDOM_ENDPOINT) {
            $base_url     = self::GIPHY_API_BASE_URL . '/search?';
            $query_string = http_build_query([
                'q'       => $keyword,
                'api_key' => GIPHY_API_KEY,
                'limit'   => $limit,
            ]);

        }
        else {
            $base_url     = self::GIPHY_API_BASE_URL . '/random?';
            $query_string = http_build_query([
                'tag'     => $keyword,
                'api_key' => GIPHY_API_KEY,
            ]);
        }

        $response = file_get_contents($base_url . $query_string);
        $response = json_decode($response, true);

        if (!isset($response['data']) || !$response['data']) {
            return (new query_response())->set_success(false);
        }
        $response = $response['data'];

        if (!GIPHY_RANDOM_ENDPOINT) {
            shuffle($response);
            $response = $response[array_rand($response)];
        }

        $query_response = new query_response();
        $query_response->set_success(true);

        if (isset($response['images']['4k'])) {
            $query_response
                ->set_url($response['images']['4k']['mp4'])
                ->set_special('4K')
                ->set_video(true);
        }
        elseif (isset($response['images']['hd'])) {
            $query_response
                ->set_url($response['images']['hd']['mp4'])
                ->set_video(true)
                ->set_special('HD');
        }
        else {
            $response = $response['images']['original'];
            $query_response
                ->set_video(false)
                ->set_cover($response['width'] >= MIN_WIDTH_COVER && ($response['width'] / $response['height']) > MIN_ASPECT_RATIO_COVER)
                ->set_url($response['webp']);
        }
        return $query_response;
    }
}
