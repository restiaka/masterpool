<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Vote_m extends CI_Model {

	public function insert_vote($contestant_id,$uid){
		date_default_timezone_set('Asia/Jakarta');
		$data = array(
			'uid' => $uid ,
			'contestant_id' => $contestant_id ,
			'date' => date('Y-m-d H:i:s')
		);
		$this->db->insert('votes', $data); 
	}
	
}