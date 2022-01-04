<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>

   <link rel="stylesheet" href="<?php echo base_url("assets/css/styles.css"); ?>" />
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

	<meta charset="utf-8">
	<title>Proceso de sincronización de Tiendas virtuales - eCommerce</title>
   <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>                       
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>


	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}



	</style>

             

</head>
<body>



	
<div class="well" style="width:50%; margin: 0 auto;">
<img src="<?php echo base_url("assets/img/".$company); ?>"  />
<div class="col-lg-12 alerts">
            <div id="custom-alerts" style="display:none;">
                <div class="alert alert-dismissable">
                    <div class="custom-msg"></div>
                </div>
            </div>
            <?php if ($error)  { ?>
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-ban"></i>Error</h4>
                <?= $error; ?>
            </div>
            <?php } if ($warning) { ?>
            <div class="alert alert-warning alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-warning"></i>Advertencia</h4>
                <?= $warning; ?>
            </div>
            <?php } if ($message) { ?>
            <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4>    <i class="icon fa fa-check"></i>Exito</h4>
                <?= $message; ?>
            </div>
            <?php } ?>
        </div>
        <div class="clearfix"></div>


<div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title"><i>Proceso de sincronización manual de Tiendas virtuales - eCommerce</i></h3>
        </div>
        <div class="box-body">

    <?php 
    echo form_open(); ?>

<div class="col-md-6">
			
<div class="col-md-12">
							  <div class="form-group has-feedback">
									  <label class="control-label" for="vstore">Identificador Tienda Virtual</label>
									  <input type="text" name="vstore"  class="form-control" placeholder="T001" />
                <span class="glyphicon glyphicon-shopping-cart form-control-feedback"></span>
							  </div> 
						 
				 </div>

<div style="clear:both">
				<div class="col-md-12">
							  <div class="form-group has-feedback">
									  <label class="control-label" for="username">Usuario</label>
									  <input type="text" name="identity"  class="form-control" placeholder="usuario@empresa" />
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
							  </div> 
						 
				 </div>

<div style="clear:both">
<div class="col-md-12">
							  <div class="form-group has-feedback">
									  <label class="control-label" for="passwd">Contraseña</label>
									 <input type="password" name="password"  class="form-control" placeholder="******" />
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
							  </div> 
						 


				 </div>



<div style="clear:both">

				<div class="col-md-6">
							 	<div class="form-group">
											  <?php echo form_submit('syncro', "Sincronizar", 'class="btn btn-primary"');?>
							 	</div>
				</div>

			
          <?php echo form_close();?>

      
        </div>
      </div>
    </div>
</div>

</body>
</html>
