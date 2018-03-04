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