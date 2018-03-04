<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Codeigniter-Twitter-Rest-API-Library
 *
 * Call Twitter 1.1 Rest API from your code igniter applications.
 *
 * This library require Codeigniter-TwitterOauth library integration ( https://github.com/MunGell/Codeigniter-TwitterOAuth )
 *
 * by Hatma Suryotrisongko - github.com/suryotrisongko  (forked from ElliottLandsborough/Codeigniter-Twitter-Search-Library)
 */
class TwitterRestApilib {

	public function __construct()
	{
		ini_set('precision', 20); // http://stackoverflow.com/a/8106127/908257
		$this->CI = & get_instance();
	}

	/**
	* Call Twitter 1.1 api.
	*/
	public function call($method=null, $url=null, $param=null, $content=false)
	{
		// do oauth
		$this->CI->load->library('twitteroauth');
		// get user/pass from config/twitter.php
		$this->CI->config->load('twitter');
		$consumer_token = $this->CI->config->item('twitter_consumer_token');
		$consumer_secret = $this->CI->config->item('twitter_consumer_secret');
		$access_token = $this->CI->config->item('twitter_access_token');
		$access_secret = $this->CI->config->item('twitter_access_secret');
		$connection = $this->CI->twitteroauth->create($consumer_token, $consumer_secret, $access_token, $access_secret);
		$content = $connection->get('account/verify_credentials');
		if(isset($content->errors))
		{
			foreach ($content->errors as $error)
			{
				echo $error->code.' '.$error->message.PHP_EOL;
			}
			die;
		}
		else
		{
			if ($method == 'post')
				$content=$connection->post($url,$param);
			else
				$content=$connection->get($url,$param);
			
			if(isset($content))
			{
				if(isset($content->errors))
				{
					foreach ($content->errors as $error)
					{
						echo $error->code.' '.$error->message.PHP_EOL;
					}
					die;
				} 
				else if(is_array($content))
				{
					foreach($content as $data)
						$this->process($data);
				}
				else if(is_object($content))
				{
					$this->update($content);
				}
				return $content;
			}
		}
		return $content;
	}

	/**
	* Process the tweet data and record it in mysql.
	* Input: $data (array - output from api)
	* (forked from ElliottLandsborough/Codeigniter-Twitter-Search-Library)
	*/
	public function process($data=null)
	{
		// if the tweet has an id, if the tweet does not already exist in db, if there is at least one hashtag
		if( ( is_object($data) && isset($data->id_str) )  && !$this->exists($data) )
		{
			if($this->save($data))
			{
				echo 'Tweet '.$data->id_str.' Saved!'.PHP_EOL;
			}
		}
	}

	/**
	* Find out if the tweet has already been inserted into the db
	* input: $data (array) - array from api - needs to contain $data['id_str']
	* output: true/false
	* (forked from ElliottLandsborough/Codeigniter-Twitter-Search-Library)
	*/
	function exists($data=null,$result=false)
	{
		if( is_object($data) && isset($data->id_str) ) 
		{
			$tweet_id=$data->id_str;			
			$this->CI->db->where('id',$tweet_id);
			$query=$this->CI->db->get('tweets',1,0);
			if($query->num_rows()>0)
			{
				$result=true;
			}
		}
		return $result;
	}

	/**
	* Save the tweet in MySQL.
	* input: $data - array of a tweet returned from the twitter api
	* input: $data['id_str'], $data['user']['id_str'] OR data['id_str'], $data['from_user_id_str']
	* output: true/false
	* (forked from ElliottLandsborough/Codeigniter-Twitter-Search-Library)
	*/
	function save($data=null,$result=false)
	{
		// if we have a tweet with an ID
		if (is_object($data) && isset($data->id_str) ) 
		{
			// set input
			$input=array( 'id' =>  $data->id_str, 'created_at' =>  $data->created_at, 'user_name' =>  $data->user->name, 'user_screen_name' =>  $data->user->screen_name, 'text' =>  $data->text );
			// save tweet in db
			$result=$this->CI->db->insert('tweets',$input);
		}
		return $result;
	}
	
	function update($data=null,$result=false)
	{
		if (is_object($data) && isset($data->in_reply_to_status_id) ) 
		{			
			$this->CI->db->set('reply', $data->text);
			$this->CI->db->where('id', $data->in_reply_to_status_id);
			$result=$this->CI->db->update('tweets');
		}
		return $result;
	}

}

/**
run this query to create a table in your mysql database !

CREATE TABLE IF NOT EXISTS `tweets` (
  `id` varchar(255) NOT NULL PRIMARY KEY,
  `created_at` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_screen_name` varchar(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `reply` varchar(255) NOT NULL
) ;
*/

/* End of file twitterrestapilib.php */