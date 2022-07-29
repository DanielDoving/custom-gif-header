<?php

namespace gif_header;

class wordlist_manager {

    const WORDLIST_ERROR_MSG = 'ERROR: Could not write to wordlist!';
    public array $wordlist;

    public function __construct() {
        if (!file_exists(WORDLIST_FILE)) {
            if (!file_put_contents(WORDLIST_FILE, json_encode([], JSON_PRETTY_PRINT))) {
                echo self::WORDLIST_ERROR_MSG;
                exit(1);
            }
        }
        $this->wordlist = json_decode(file_get_contents(WORDLIST_FILE), true);
    }

    public function sort_wordlist() {
        $temp_list = [];
        foreach ($this->wordlist as $k => $word) {
            $temp_list[$k] = $word['keyword'] ?? $word;
        }
        asort($temp_list);
        $new_list = [];
        foreach ($temp_list as $k => $w) {
            $word = $this->wordlist[$k];
            if (isset($word['limit'])) {
                if ($word['limit'] === DEFAULT_LIMIT) {
                    $word = $word['keyword'];
                }
                else {
                    $word['limit'] = intval($word['limit']);
                }
            }
            $new_list[] = $word;
        }
        $this->wordlist = $new_list;
    }


    /**
     * -------------------------------------------------------------------
     * Search for Word in wordlist and returns key if found
     *
     * @param string $needle Word to search for
     * @return int|string Key if found otherwise -1
     */
    public function find_word(string $needle) {
        foreach ($this->wordlist as $k => $word) {
            if (strtoupper($word['keyword'] ?? $word) === strtoupper($needle)) {
                return $k;
            }
        }
        return -1;
    }

    /**
     * -------------------------------------------------------------------
     * Builds entry for wordlist
     *
     * @param string $word
     * @param int $limit
     * @return array|string
     */
    private function build_wordlist_entry(string $word, int $limit = 0) {
        if ($limit === 0 || $limit === DEFAULT_LIMIT) {
            return $word;
        }
        return [
            'keyword' => $word,
            'limit'   => $limit
        ];
    }

    public function add_or_update(string $word, int $limit = 0): bool {
        if ($limit > 50) {
            echo "Max limit is 50!";
            return false;
        }
        $key = $this->find_word($word);
        if ($key === -1) {
            return $this->add($word, $limit);
        }
        return $this->update($word, $limit, $key);
    }

    public function remove(string $word): bool {
        $key = $this->find_word($word);
        if ($key === -1) {
            echo "Word '$word' not found in wordlist!" . PHP_EOL;
            return true;
        }
        unset($this->wordlist[$key]);
        if ($this->save_wordlist()) {
            echo "Word '$word' removed from wordlist" . PHP_EOL;
            return true;
        }
        echo self::WORDLIST_ERROR_MSG;
        return false;
    }

    private function update(string $word, int $limit, $key): bool {
        if (!$this->is_update_needed($key, $limit)) {
            echo "No action needed";
            return true;
        }
        $this->wordlist[$key] = $this->build_wordlist_entry($word, $limit);
        if ($this->save_wordlist()) {
            echo "Updated limit '$word' in wordlist (new limit: '$limit')" . PHP_EOL;
            return true;
        }
        echo self::WORDLIST_ERROR_MSG;
        return false;
    }

    private function add(string $word, int $limit): bool {
        $this->wordlist[] = $this->build_wordlist_entry($word, $limit);
        if ($this->save_wordlist()) {
            echo "Success: Added word '$word' to wordlist";
            return true;
        }
        echo self::WORDLIST_ERROR_MSG;
        return false;
    }

    private function is_update_needed($key, $limit): bool {
        if (isset($this->wordlist[$key]['limit'])) {
            return intval($this->wordlist[$key]['limit']) !== intval($limit);
        }
        return $limit !== DEFAULT_LIMIT;
    }

    private function save_wordlist(): bool {
        $this->sort_wordlist();
        return boolval(file_put_contents(
            WORDLIST_FILE,
            json_encode($this->wordlist, JSON_PRETTY_PRINT)
        ));
    }
}
