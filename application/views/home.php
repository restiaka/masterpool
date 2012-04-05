<?php echo $this->load->view('header');//Begin HTML ?>
<div class="row" style="min-height:500px;">
<?php
echo get_score(1);
echo form_open('home', '', $hidden[1]);
$data = array(
    'name' => 'button1',
    'id' => 'button1',
	'class' => 'btn primary',
    'content' => 'Votes'
);
echo form_button($data);
echo form_close();
?>

<?php
echo get_score(2);
echo form_open('home', '', $hidden[2]);
$data1 = array(
    'name' => 'button2',
    'id' => 'button2',
    'value' => 'Votes'
);
//echo form_button($data1);
echo form_submit($data1);
echo form_close();
?>

</div><!--row-->
<?php echo $this->load->view('footer'); //Begin HTML ?>