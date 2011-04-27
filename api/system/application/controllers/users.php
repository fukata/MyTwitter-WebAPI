<?php
class Users extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('twitter');
	}
	
	public function show_get() {
		// request
		$params = array();
		if ($this->input->get('user_id')!==false) $params['user_id'] = $this->input->get('user_id');
		if ($this->input->get('screen_name')!==false) $params['screen_name'] = $this->input->get('screen_name');
		if (empty($parmas)) $params['screen_name'] = $this->config->item('screen_name');
		$res = $this->twitter->users_show($params);
		
		// format data
		$data = new stdClass();
		$data->id = $res->id_str;
		$data->screen_name = $res->screen_name;
		$data->name = $res->name;

		// response
		$this->response($data);
	}
}