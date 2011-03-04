<?php
class Favorites extends MY_Controller {
	/**
	 * @var twitter
	 */
	public $twitter;
	
	public function __construct() {
		parent::__construct();
		$this->load->library('twitter');
	}
	
	public function create_post() {
		$id = $this->input->post('id');
		if ($id==false && mb_strlen($id)==0) {
			$this->response(array());
			return;
		}
		$params = array();
		$res = $this->twitter->favorites_create($id, $params);
		$this->response($res);
	}
	
	public function destroy_post() {
		$id = $this->input->post('id');
		if ($id==false && mb_strlen($id)==0) {
			$this->response(array());
			return;
		}
		$params = array();
		$res = $this->twitter->favorites_create($id, $params);
		$this->response($res);
	}
}
?>
