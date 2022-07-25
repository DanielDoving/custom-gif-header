<?php
require __DIR__ . '/config.inc.php';

$wordlist = json_decode(file_get_contents(WORDLIST_FILE), true);

if (!isset($argv[1])) {
    echo "No word provided" . PHP_EOL;
    exit(1);
}

$word_to_remove = $argv[1];
$word_found     = false;

$action = "No action needed";
foreach ($wordlist as $k => $word) {
    if ($word === $word_to_remove) {
        $word_found = true;
        $action     = "Word '$word_to_remove' removed from wordlist" . PHP_EOL;
        unset($wordlist[$k]);
    }
    else if (isset($word['keyword']) && $word['keyword'] === $word_to_remove) {
        $word_found = true;
        $action     = "Word '$word_to_remove' removed from wordlist" . PHP_EOL;
        unset($wordlist[$k]);
    }
}

if (!$word_found) {
    $action = "Word '$word_to_remove' not found in wordlist!" . PHP_EOL;
}
else {
    file_put_contents(WORDLIST_FILE, json_encode($wordlist, JSON_PRETTY_PRINT));
}
echo $action;
exit(0);
