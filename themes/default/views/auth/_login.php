<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title.' | '.$Settings->site_name; ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.ico"/>
    <script type="text/javascript">if (parent.frames.length !== 0) { top.location = '<?=site_url('login')?>'; }</script>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/iCheck/square/green.css" rel="stylesheet" type="text/css" />
</head>

<style type="text/css" media="screen">

.login-box{
    /* border: 1px solid black; */
    overflow: hidden;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.2), 0 1px 5px 0 rgba(0,0,0,.12);
}
.login-logo
{position: relative;}
.login-logo > a > h2
{
    margin: 10px 10px 0px;
}

.login-box, .register-box {
    width: 360px;
    margin: 20px auto;
}

.login-logo a img {
  margin: 10px;
  margin-top: 50px;
  margin-bottom: 10px;
  width: 90%;
}

</style>
<body background="#5569A8">
    <div class="login-box">
        <div class="login-logo">
          <a href="<?=base_url();?>"><?= $Settings->site_name == 'PDV' ? '<b>PDV</b>' : '<img width="100%" src="'.base_url('uploads/ecomerce.png').'" alt="'.$Settings->site_name.'" />'; ?>
            <h3>Sistema de Gestión<br>para Pymes</h3>
            <h4>Ventas, Compras y Almacén</h4>
          </a>
        </div>
        <div class="login-box-body">
		<br/>
		<br/>
		<br/>
		
            <center><label style="color:red;">El servicio se encuentra suspendido por mantener una deuda pendiente.</label></center>
        </div>
        </div>
    <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
    <script src="<?= $assets ?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?= $assets ?>plugins/iCheck/icheck.min.js" type="text/javascript"></script>
</body>
</html>
