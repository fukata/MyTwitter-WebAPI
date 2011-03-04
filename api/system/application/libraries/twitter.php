<?php
class twitter {
	/**
	 * @var CodeIgniter
	 */
	private $CI;

	private $consumerKey;
	private $consumerSecret;
	private $oauthToken;
	private $oauthTokenSecret;
	/**
	 * @var OAuth
	 */
	private $oauth;

	private $apiUrl = 'https://api.twitter.com/1/';
	private $apiFormat = '.json';
	
	public function __construct() {
		$this->CI =& get_instance();
		$this->consumerKey = $this->CI->config->item('consumer_key');
		$this->consumerSecret = $this->CI->config->item('consumer_secret');
		$this->oauthToken = $this->CI->config->item('oauth_token');
		$this->oauthTokenSecret = $this->CI->config->item('oauth_token_secret');
		
		$this->oauth = new OAuth($this->consumerKey, $this->consumerSecret);
		if (!empty($this->oauthToken) && !empty($this->oauthTokenSecret)) {
			$this->oauth->setToken($this->oauthToken, $this->oauthTokenSecret);
		}
	}
	
	public function authorizeXAuth($username, $password) {
		$response = '';
		$parameters = array(
			'x_auth_mode'     => 'client_auth',
			'x_auth_username' => $username,
			'x_auth_password' => $password,
		);

		try {
			$oauth = new OAuth($this->socialConfig['consumer_key'], $this->socialConfig['consumer_secret'],
			OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
			$oauth->fetch(self::AUTHORIZE_XAUTH_API, $parameters, OAUTH_HTTP_METHOD_POST);
			$response = $oauth->getLastResponse();
		} catch (OAuthException $e) {
			return false;
		}

		// oauth_token=xxx&oauth_token_secret=xxx&
		// user_id=xxx&screen_name=xxx&x_auth_expires=0
		parse_str($response, $accessTokenInfo);

		// oauth_token, oauth_token_secret, user_id, screen_name,
		// x_auth_expires
		return $accessTokenInfo;
	}

	// ==========================================================================
	// statuses
	// ==========================================================================
	
	public function statuses_home_timeline($params=array()) {
		$url = $this->apiUrl.'statuses/home_timeline'.$this->apiFormat;
		return $this->get($url, $params);
	}
	
	public function statuses_mentions($params=array()) {
		$url = $this->apiUrl.'statuses/mentions'.$this->apiFormat;
		return $this->get($url, $params);
	}

	public function statuses_update($params=array()) {
		$url = $this->apiUrl.'statuses/update'.$this->apiFormat;
		return $this->post($url, $params);
	}
	
	public function statuses_retweet($id, $params=array()) {
		$url = $this->apiUrl.'statuses/retweet/'.$id.$this->apiFormat;
		return $this->post($url, $params);
	}
	
	public function statuses_destroy($id, $params=array()) {
		$url = $this->apiUrl.'statuses/destroy/'.$id.$this->apiFormat;
		return $this->post($url, $params);
	}

	// ==========================================================================
	// direct_messages
	// ==========================================================================
	
	public function direct_messages($params=array()) {
		$url = $this->apiUrl.'direct_messages'.$this->apiFormat;
		return $this->get($url, $params);
	}

	public function direct_messages_sent($params=array()) {
		$url = $this->apiUrl.'direct_messages/sent'.$this->apiFormat;
		return $this->get($url, $params);
	}

	public function direct_messages_new($params=array()) {
		$url = $this->apiUrl.'direct_messages/new'.$this->apiFormat;
		return $this->post($url, $params);
	}

	public function direct_messages_destroy($id,$params=array()) {
		$url = $this->apiUrl.'direct_messages/destroy/'.$id.$this->apiFormat;
		return $this->post($url, $params);
	}

	// ==========================================================================
	// favolites
	// ==========================================================================
	
	public function favorites_create($id, $params=array()) {
		$url = $this->apiUrl.'favorites/create/'.$id.$this->apiFormat;
		return $this->post($url, $params);
	}
	
	public function favorites_destroy($id, $params=array()) {
		$url = $this->apiUrl.'favorites/destroy/'.$id.$this->apiFormat;
		return $this->post($url, $params);
	}

	// ==========================================================================
	// base
	// ==========================================================================

	private function get($url, $params) {
		if (!empty($params) && is_array($params)) {
			$url .= '?'.http_build_query($params);
		}
		$this->oauth->fetch($url);
		return @json_decode($this->oauth->getLastResponse());
	}

	private function post($url, $params) {
		$this->oauth->fetch($url, $params, OAUTH_HTTP_METHOD_POST);
		return @json_decode($this->oauth->getLastResponse());
	}
}
?>
