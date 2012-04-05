<?php echo $this->load->view('header'); //Begin HTML ?>
	   Only Authorized user can use this application.<br/><br/>
		<?php echo authorizeButton("Please Login/Authorize App",$redirectURL);?>	
<?php echo $this->load->view('footer'); //Begin HTML ?>