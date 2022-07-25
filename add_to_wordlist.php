<?php
require __DIR__ . '/config.inc.php';

if (!file_exists(WORDLIST_FILE)) {
    file_put_contents(WORDLIST_FILE, json_encode([], JSON_PRETTY_PRINT));
}

$wordlist = json_decode(file_get_contents(WORDLIST_FILE), true);

if (!isset($argv[1])) {
    echo "No word provided" . PHP_EOL;
    exit(1);
}
$default_limit = 30;
$word_to_add   = $argv[1];
$limit         = $argv[2] ?? $default_limit;

if ($limit > 50) {
    echo "Max limit is 50!";
    exit(1);
}

// Check if word already exists
$word_found = false;
$action     = "No action needed";

foreach ($wordlist as $k => $word) {
    if ($word === $word_to_add) {
        $word_found = true;
        if ($limit !== $default_limit) {
            $action       = "Updated limit '$word_to_add' in wordlist (new limit: '$limit')" . PHP_EOL;
            $wordlist[$k] = [
                'keyword' => $word_to_add,
                'limit'   => $limit
            ];
        }
    }
    else if (isset($word['keyword']) && $word['keyword'] === $word_to_add) {
        $word_found = true;
        if ($word['limit'] !== $limit) {
            $action       = "Updated limit '$word_to_add' in wordlist (new limit: '$limit')" . PHP_EOL;
            $wordlist[$k] = [
                'keyword' => $word_to_add,
                'limit'   => $limit
            ];
        }
    }
}
if (!$word_found) {
    $action     = "Added word '$word_to_add' to wordlist (new limit: '$limit')" . PHP_EOL;
    $wordlist[] = [
        'keyword' => $word_to_add,
        'limit'   => $limit
    ];
}
file_put_contents(WORDLIST_FILE, json_encode($wordlist, JSON_PRETTY_PRINT));
echo $action;
exit(0);



