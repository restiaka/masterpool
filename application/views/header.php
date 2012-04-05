<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="<?php echo base_url()?>/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="<?php echo base_url()?>/assets/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="<?php echo base_url()?>/assets/bootstrap/css/datepicker.css" rel="stylesheet">
<style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
	  .table {
		font-size: 12px;
	  }
	  input, textarea, select, .uneditable-input {
		height: 26px;
	  }
</style>
<?php $this->load->view('fbjs'); //Set Facebook JS SDK | REQUIRED!!! ?>
</head>
<body>
<?php $this->load->view('fbjs_async_load'); //Async Facebook js sdk Load (Always put after <body> tag!) | REQUIRED!!! ?>
<div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Guinness Master of Pool</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="<?php echo site_url('')?>">Home</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
</div>

<div class="container">