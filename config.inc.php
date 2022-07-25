<?php
require_once __DIR__ . '/apikey.inc.php';
/**
 * Path to where the current background data is stored
 */
const DATA_FILE = __DIR__ . "/data/data.json";
/**
 * Path to wordlist
 */
const WORDLIST_FILE = __DIR__ . "/data/wordlist.json";
/**
 * Default result limit for Giphy requests
 */
const DEFAULT_LIMIT = 30;
/**
 * Interval between backgrounds in seconds
 */
const NEW_BACKGROUND_INTERVAL = 120;
/**
 * Use giphys /random endpoint
 */
const GIPHY_RANDOM_ENDPOINT = false;
/**
 * Minimum aspect ratio required for the background size to be set to cover
 */
const MIN_ASPECT_RATIO_COVER = 1.5;
/**
 * Minimum width required for the background size to be set to cover
 */
const MIN_WIDTH_COVER = 500;
/**
 * Giphy API Key
 */
const GIPHY_API_KEY = '-- INSERT GIPHY KEY HERE --';
