<?php
function get_score($contestant_id){
	$CI = &get_instance();
	$total = $CI->db->query("SELECT count(*) as total FROM votes where contestant_id = '".$contestant_id."'")->row();
    $total = $total->total;
    return $total;
}