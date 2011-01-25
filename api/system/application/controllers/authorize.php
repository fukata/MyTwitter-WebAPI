<?php
class Authorize extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function xauth_get() {
		$username = $this->input->get('username');
		$password = $this->input->get('password');
		
		$user = new stdClass();
		$user->username = $username;
		$user->password = $password;
		
		$this->response($user);
	}
	
	
}
?>