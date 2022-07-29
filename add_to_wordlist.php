<?php
require __DIR__ . '/config.inc.php';
require __DIR__ . '/classes/wordlist_manager.def.php';

if (!isset($argv[1])) {
    echo "No word provided" . PHP_EOL;
    exit(1);
}
$word_to_add      = $argv[1];
$limit            = $argv[2] ?? DEFAULT_LIMIT;
$wordlist_manager = new \wordlist_manager();
$wordlist_manager->add_or_update($word_to_add, $limit);
exit();



