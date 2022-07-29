<?php

namespace gif_header\query;

class query_response {
    private string $url;
    private bool $cover;
    private bool $video;
    private string $special;
    private bool $success;

    public function __construct() {
        $this->url     = '';
        $this->cover   = true;
        $this->video   = false;
        $this->special = '';
        $this->success = false;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @return string
     */
    public function get_url(): string {
        return $this->url;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @param string $url
     * @return query_response
     */
    public function set_url(string $url): query_response {
        $this->url = $url;
        return $this;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @return bool
     */
    public function is_cover(): bool {
        return $this->cover;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @param bool $cover
     * @return query_response
     */
    public function set_cover(bool $cover): query_response {
        $this->cover = $cover;
        return $this;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @return bool
     */
    public function is_video(): bool {
        return $this->video;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @param bool $video
     * @return query_response
     */
    public function set_video(bool $video): query_response {
        $this->video = $video;
        return $this;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @return string
     */
    public function get_special(): string {
        return $this->special;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @param string $special
     * @return query_response
     */
    public function set_special(string $special): query_response {
        $this->special = $special;
        return $this;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @return bool
     */
    public function is_success(): bool {
        return $this->success;
    }

    /**
     * -------------------------------------------------------------------
     *
     * @param bool $success
     * @return query_response
     */
    public function set_success(bool $success): query_response {
        $this->success = $success;
        return $this;
    }
}
