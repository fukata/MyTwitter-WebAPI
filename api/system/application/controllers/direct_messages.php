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
			$d->created_at = $r->created_at;
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
	
	public function sent_get() {
		$params = array();
		if ($this->input->get('max_id')!==false) $params['max_id'] = $this->input->get('max_id');
		if ($this->input->get('since_id')!==false) $params['since_id'] = $this->input->get('since_id');
		$res = $this->twitter->direct_messages_sent($params);
		
		$data = array();
		foreach ($res as $r) {
			$d = new stdClass();
			$d->text = $r->text;
			$d->created_at = $r->created_at;
			$d->id = $r->id_str;
			$d->recipient_id = $r->recipient_id;
			$d->recipient_screen_name = $r->recipient_screen_name;
			$d->sender_id = $r->sender_id;
			$d->sender_screen_name = $r->sender_screen_name;
			$data[] = $d;
		}
		$this->response($data);
	}
	
	public function new_post() {
		$params = array(
			'text' => $this->input->post('text'),
		);
		if ($this->input->post('screen_name')!==false) $params['screen_name'] = $this->input->post('screen_name');
		if ($this->input->post('user_id')!==false) $params['user_id'] = $this->input->post('user_id');
		$res = $this->twitter->direct_messages_new($params);
		$this->response($res);
	}
	
	public function destroy_post() {
		$id = $this->input->post('id');
		if ($id==false && mb_strlen($id)==0) {
			$this->response(array());
			return;
		}
		$res = $this->twitter->direct_messages_destroy($id);
		$this->response($res);
	}
}
?>