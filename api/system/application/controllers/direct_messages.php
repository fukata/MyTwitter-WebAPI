<?php
class Direct_messages extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('twitter');
	}
	
	public function index_get() {
		$params = array();
		if ($this->input->get('max_id')!==false) $params['max_id'] = $this->input->get('max_id');
		if ($this->input->get('since_id')!==false) $params['since_id'] = $this->input->get('since_id');
		$res = $this->twitter->direct_messages($params);
		
		$data = array();
		foreach ($res as $r) {
			$d = new stdClass();
			$d->id = $r->id_str;
			$d->text = $r->text;
			$d->recipient_id = $r->recipient_id;
			$d->recipient_screen_name = $r->recipient_screen_name;
			$d->sender_id = $r->sender_id;
			$d->sender_screen_name = $r->sender_screen_name;
			$data[] = $d;
		}
		$this->response($data);
	}
}
?>