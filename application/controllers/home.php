<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->output->enable_profiler(true);
	}

	public function index()
	{
		$this->load->library('facebook');
		$user = getAuthorizedUser(true);
		
		$isAuthorized = $user ? true : false;
			 
	    $sr = $this->facebook->getSignedRequest();
		
		if($isAuthorized){
			$redirect_url = site_url('home');
		}else{
			$sr = $this->facebook->getSignedRequest();
			$redirect_url = isset($sr['page']) ? $this->config->item('APP_FANPAGE')."&app_data=redirect|".site_url('home') : "http://apps.facebook.com/".$this->config->item('APP_APPLICATION_ID')."/";
		}
		
		if(!$user = getAuthorizedUser(true)){
			redirect(site_url('home/authorize').'?ref='.$redirect_url);
		}

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('contestant1', 'Contestant', 'callback__ajax');
		$this->form_validation->set_message('_ajax', 'Please wait for a minute to vote again');
		if ($this->form_validation->run())
		{
			$id = $this->input->post('contestant1');
			$data = array(
				'uid' => $user['id'] ,
				'contestant_id' => $id,
				'date' => date('Y-m-d H:i:s')
				
			);
			$this->db->insert('votes', $data);
		}
		
		/* generate 10 id and value for hidden input field */
		for($i=1;$i<=10;$i++){
			$hidden[$i] = array('contestant'.$i => $i);
		}
		$this->load->view('vote',array('is_authorized' => $isAuthorized,
										'redirectURL' => $redirect_url,
										'hidden' => $hidden
										));
	}
	
	public function _ajax()
	{	
		$id = $this->input->post('contestant1');
		$this->load->library('form_validation');
		$contestants = array(1,2,3,4,5,6,7,8,9,10);
		if (in_array($id, $contestants))
		{
			return TRUE;
		}
		else
		{
			$this->form_validation->set_message('_ajax', 'Please wait for a minute to vote again');
			return FALSE;
		}
	}
	
	public function authorize()
	{				   
		$redirectURL = urldecode($this->input->get_post('ref'));
		$this->load->view('authorize',array('redirectURL' => $redirectURL));
	}
}