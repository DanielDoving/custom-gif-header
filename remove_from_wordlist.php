<?php
require __DIR__ . '/config.inc.php';
require __DIR__ . '/classes/wordlist_manager.def.php';

if (!isset($argv[1])) {
    echo "No word provided" . PHP_EOL;
    exit(1);
}

$word_to_remove = $argv[1];
$wordlist_manager = new \wordlist_manager();
$wordlist_manager->remove($word_to_remove);
exit();
