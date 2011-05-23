<?php
class Statuses extends MY_Controller {
	/**
	 * @var twitter
	 */
	public $twitter;
	
	public function __construct() {
		parent::__construct();
		$this->load->library('twitter');
	}
	
	public function home_timeline_get() {
		$params = array();
		if ($this->input->get('max_id')!==false) $params['max_id'] = $this->input->get('max_id');
		if ($this->input->get('since_id')!==false) $params['since_id'] = $this->input->get('since_id');
		if ($this->input->get('count')!==false) $params['count'] = $this->input->get('count');
		$res = $this->twitter->statuses_home_timeline($params);
		
		$data = array();
		foreach ($res as $r) {
			$d = new stdClass();
			$d->text = $r->text;
			$d->created_at = $r->created_at;
			$d->source = strip_tags($r->source);
			$d->id = $r->id_str;
			$d->user = new stdClass();
			$d->user->statuses_count = $r->user->statuses_count;
			$d->user->screen_name = $r->user->screen_name;
			$d->user->lang = $r->user->lang;
			$d->user->created_at = $r->user->created_at;
			$d->user->description = $r->user->description;
			$d->user->followers_count = $r->user->followers_count;
			$d->user->friends_count = $r->user->friends_count;
			$d->user->following = $r->user->following;
			$d->user->url = $r->user->url;
			$d->user->time_zone = $r->user->time_zone;
			$d->user->location = $r->user->location;
			$d->user->name = $r->user->name;
			$d->user->profile_image_url = $r->user->profile_image_url;
			$d->user->id = $r->user->id_str;
			$d->user->utc_offset = $r->user->utc_offset;
			$data[] = $d;
		}
		
		$this->response($data);
	}
	public function mentions_get() {
		$params = array();
		if ($this->input->get('max_id')!==false) $params['max_id'] = $this->input->get('max_id');
		if ($this->input->get('since_id')!==false) $params['since_id'] = $this->input->get('since_id');
		if ($this->input->get('count')!==false) $params['count'] = $this->input->get('count');
		$res = $this->twitter->statuses_mentions($params);
		
		$data = array();
		foreach ($res as $r) {
			$d = new stdClass();
			$d->text = $r->text;
			$d->created_at = $r->created_at;
			$d->source = strip_tags($r->source);
			$d->id = $r->id_str;
			$d->in_reply_to_status_id = $r->in_reply_to_status_id;
			$d->in_reply_to_user_id = $r->in_reply_to_user_id;
			$d->favorited = $r->favorited;
			$d->in_reply_to_screen_name = $r->in_reply_to_screen_name;
			$d->user = new stdClass();
			$d->user->id = $r->user->id_str;
			$d->user->screen_name = $r->user->screen_name;
			$data[] = $d;
		}
		
		$this->response($data);
	}
	public function update_post() {
		$status = $this->input->post('status');
		if ($status==false && mb_strlen($status)==0) {
			$this->response(array());
			return;
		}
		$params = array(
			'status' => $status,
		);
		$res = $this->twitter->statuses_update($params);
		$this->response($res);
	}
	public function retweet_post() {
		$id = $this->input->post('id');
		if ($id==false && mb_strlen($id)==0) {
			$this->response(array());
			return;
		}
		$params = array();
		$res = $this->twitter->statuses_retweet($id, $params);
		$this->response($res);
	}
	public function destroy_post() {
		$id = $this->input->post('id');
		if ($id==false && mb_strlen($id)==0) {
			$this->response(array());
			return;
		}
		$params = array();
		$res = $this->twitter->statuses_destroy($id, $params);
		$this->response($res);
	}
}
?>
