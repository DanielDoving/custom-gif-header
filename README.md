# custom-gif-header

## Setup
1. Add Giphy API Key to `config.inc.php`

```php
const GIPHY_API_KEY = '-- INSERT GIPHY KEY HERE --';

```

2. Edit wordlist using `add_to_wordlist.php` and `remove_from_wordlist.php`


### Usage add_to_wordlist.php
Arg1: Keyword/Query String

Arg2 (optional): Limit Query results (more specific Keywords should have a lower limit; Max is 50) 

```bash
php add_to_wordlist.php 'Example Keyword' 40
```

### Usage remove_from_wordlist.php

Arg1: Keyword/Query String
```bash
php remove_from_wordlist.php 'Example Keyword'
```


### custom.css

You can add custom css rules to: `css/custom.css`
