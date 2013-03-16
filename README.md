# Codeigniter-Twitter-Search-Library
## Search for tweets using Twitter search/streaming api
Search for certain tweets using keywords specified in a mysql database using the search api, streaming api or both at the same time.
### Requirements other than codeigniter
* curl
* memcache/memcached (optional for caching)
* twitter account (optional for streaming api)
* [Codeigniter-TwitterOAuth](https://github.com/MunGell/Codeigniter-TwitterOAuth) for searching v1.1 api

### How it works
1. Queries the db for specified keywords (can be 'string' or '#string' or '@string')
2. Two command line functions - search() and stream() let you choose whether to stream or search.

It is possible to run search() within the site but its a good idea to use its cache feature to avoid getting banned by twitter.

Search() is designed to be run with cron or directly on the site.
Stream() is best run with something like nohup as it is an endless loop.
### Example commands
search for tweets containing keywords set in mysql table search_terms:
```bash
php /path/to/codeigniter/index.php findtweets search
```
same as first example but cache results for 5 minutes before trying again:
```bash
php /path/to/index.php findtweets search 5
```
search for tweets in the same way as search() but using api v1.1 endpoints and oauth:
```bash
php /path/to/index.php findtweets searchone
```
stream tweets using keywords in mySQL
```bash
php index.php findtweets stream
```
#### Example controller (controllers/findtweets.php)
```php
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* A collection of functions to be run through command line only.
*/
class Findtweets extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // set maximum execution time to infinity
        set_time_limit(0);
        $this->load->library('twitterlib');
    }

    // stream tweets from twitter livestream
    public function stream()
    {
        $this->twitterlib->stream();
    }

    // search for tweets by hashtag
    public function search($cachetime=null)
    {
        $this->twitterlib->search($cachetime);
    }

    // search for tweets by hashtag using api v1.1
    public function searchone($cachetime=null)
    {
      $this->twitterlib->searchone($cachetime);
    }
}
?>
```
#### mySQL
```mysql
CREATE TABLE IF NOT EXISTS `search_terms` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `term` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `search_terms` (`id`, `term`) VALUES 
(1, 'yolo'),
(2, 'fact');

CREATE TABLE IF NOT EXISTS `tweets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tweet_id` text NOT NULL,
  `user_id` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
```
#### Twitter config (config/twitter.php) - only required for streaming api
```php
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| TWITTER CONFIG
| -------------------------------------------------------------------
*/

// required only for streaming api
$config['user'] = 'twitter-username';
$config['pass'] = 'twitter-password';

// required only for api v1.1 search
$config['consumer_token']  = 'abcdefghijklmnopqrstuvwxyz';
$config['consumer_secret'] = 'abcdefghijklmnopqrstuvwxyz';
$config['access_token']    = 'abcdefghijklmnopqrstuvwxyz';
$config['access_secret']   = 'abcdefghijklmnopqrstuvwxyz';
?>
```