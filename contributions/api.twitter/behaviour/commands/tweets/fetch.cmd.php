<?php
/**
 * Fetch content from twitter
 * 
 * Takes following parameters:
 * 
 * user => name of twitter user to fetch tweets from
 * html_policy => policy for converting to HTML
 * 
 * @ingroup Twitter
 * @author Gerd Riesselmann
 */
class FetchTweetsCommand extends CommandChain {
	protected function do_execute() {
		$ret =  new Status();
		$params = $this->get_params();
		$twitter_user = Arr::get_item($params, 'user', false);
		$policy = Arr::get_item($params, 'html_policy', 0);
		$data = $this->fetch_tweets($twitter_user, $ret);
		if ($ret->is_ok()) {
			$ret->merge($this->insert_tweets($data, $policy));
			if ($ret->is_error()) {
				$ret->append('Inserting tweets of "' , $twitter_user . '" failed');
			}
		}
		return $ret;
	}
	
	/**
	 * Fetch Tweets from twitter
	 */
	protected function fetch_tweets($twitter_user, Status $err) {
		$data = array();
		if (empty($twitter_user)) {
			$ret->merge('Empty twitter user when fetching tweets');
		}
		else {
			$url = 'http://twitter.com/statuses/user_timeline/' . String::plain_ascii($twitter_user) . '.json';
			
			Load::components('httprequest');
			$json = HttpRequest::get_content($url, $err);
			if ($err->is_ok()) {
				$data = ConverterFactory::decode($json, CONVERTER_JSON);
			}
		}
		return $data;
	}
	
	/**
	 * Insert Tweets into DB
	 */
	protected function insert_tweets($data, $policy) {
		$ret = new Status();
		if (is_array($data)) {
			Load::models('tweets');
			foreach ($data as $s) {
				$ret->merge($this->insert_tweet($s, $policy));
				if ($ret->is_error()) {
					break;
				}						
			}
		}
		else {
			$ret->merge('Twitter Fetch returned no valid data!');
		}
		return $ret;
	}
	
	/**
	 * Insert a single tweet
	 */
	protected function insert_tweet($s, $policy) {
		$ret = new Status();
		$tweet = new DAOTweets();
		$tweet->id_twitter = $s->id;
		
		$tweet->find(DataObjectBase::AUTOFETCH);
		
		$message = ConverterFactory::decode($s->text, CONVERTER_TWITTER);
		$tweet->message = $message;
		$tweet->message_html = ConverterFactory::encode($message, CONVERTER_TWITTER, $policy);
		$tweet->title = String::substr_word($message, 0, 120);
		
		$tweet->username = $s->user->screen_name; 
		$tweet->creationdate = GyroDate::datetime($s->created_at);
		
		$ret->merge($tweet->validate());
		if ($ret->is_ok()) {
			if ($tweet->id) {
				$ret->merge($tweet->update());
			}
			else {
				$ret->merge($tweet->insert());
			}
		}
		return $ret;
	}
}