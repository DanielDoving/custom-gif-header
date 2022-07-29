<?php

namespace gif_header\query;

interface query_interface {
    public static function get_response(string $keyword, int $limit): query_response;
}
