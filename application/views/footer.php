</div><!-- container fluid -->


<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
    <script src="<?php echo base_url()?>assets/bootstrap/js/bootstrap.js"></script>
	<script src="<?php echo base_url()?>assets/bootstrap/js/bootstrap-datepicker.js"></script>
	<script>
		$("#button1").click(function() {
        var ids = $('[name=contestant1]').val();
		$.ajax({
                type: 'POST',
				data: {contestant1:ids},
                url: '<?php echo site_url('home') ?>',
                dataType: 'json',
				success: function(data) {
                        if(data != "false") {
                                alert("Data Loaded: " + data);      
                        } 
                }
          })    
		});
		$(function(){
			$("#button2").click(function(){
				var ids = $('[name=contestant2]').val();
				$.post("<?php echo site_url('home') ?>",  {contestant2: ids},
				function(data){
					alert(data.data);
				},'json');
				return false;
		})
		});

</script>
</body>
</html>