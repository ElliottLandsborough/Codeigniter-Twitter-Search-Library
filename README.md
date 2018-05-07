# Codeigniter-Twitter-Rest-API-Library
## Call Twitter 1.1 Rest API from your code igniter applications.
### Requirements other than codeigniter
* [Codeigniter-TwitterOAuth](https://github.com/MunGell/Codeigniter-TwitterOAuth) for searching v1.1 api

### How it works
1. Call Twitter 1.1 API and save the result into mysql database
2. Example Controller shows how to get mentions and post/reply 

#### Example controller (controllers/Twitterapi.php)
```php
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Example controller for TwitterRestApilib
*/
class Twitterapi extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // set maximum execution time to infinity
        set_time_limit(0);
        $this->db = $this->load->database('default', true);
        $this->load->library('twitterrestapilib');
    }

    // search for tweets by mentions using api v1.1
    public function getmentions()
    {
		$method='get';
		$url='https://api.twitter.com/1.1/statuses/mentions_timeline.json'; 
		$param=null;
		//$param= array("since_id" => "970310438612189184");
		$content = $this->twitterrestapilib->call($method, $url, $param);
		print_r($content); exit;
    }

    // post tweets using api v1.1
    public function post()
    {
		$method='post';
		$url='https://api.twitter.com/1.1/statuses/update.json'; 
		$param= array("status" => "@hatma_s coba reply 1234567890123456x BARU");
		$content = $this->twitterrestapilib->call($method, $url, $param);
		print_r($content); exit;
    }

    // reply tweets using api v1.1
    public function reply()
    {
		$method='post';
		$url='https://api.twitter.com/1.1/statuses/update.json'; 
		$param= array("status" => "@hatma_s coba reply REPLYYYY YYYY", "in_reply_to_status_id" => "970310438612189184", "in_reply_to_status_id_str" => "970310438612189184");
		$content = $this->twitterrestapilib->call($method, $url, $param);
		print_r($content); exit;
    }
}
?>
```
#### mySQL
```mysql
CREATE TABLE IF NOT EXISTS `tweets` (
  `id` varchar(255) NOT NULL PRIMARY KEY,
  `created_at` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_screen_name` varchar(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `reply` varchar(255) NOT NULL
) ;
```
