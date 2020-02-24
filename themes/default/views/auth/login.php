<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title.' | '.$Settings->site_name; ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/ccasanisofticon.png"/>
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
          <a href="<?=base_url();?>"><?= $Settings->site_name == 'PDV' ? '<b>PDV</b>' : '<img width="100%" src="'.base_url('uploads/ccasanisoft.png').'" alt="'.$Settings->site_name.'" />'; ?>
            <h3>SISTEMA DE GESTION<br></h3>
            <h4>(Facturación, Compras y Almacén)</h4>
          </a>
        </div>
        <div class="login-box-body">
            <?php if($error)  { ?>
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <?= $error; ?>
				<?php $this->session->sess_destroy();//**********ALEXANDER ROCA - 22/05/2019*********?>
            </div>
            <?php } if($message) { ?>
            <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <?= $message; ?>
            </div>
            <?php } ?>
            <p class="login-box-msg"><small>INGRESAR CREDENCIALES</small></p>
            <?= form_open("auth/login"); ?>

            <div class="form-group has-feedback">
                <?php
                    $count = 0;
                    $lc[''] = lang("select")." ".lang('local');
                    foreach($locals as $local) {
                        $lc[$local->id] = $local->name;
                        $count = $count + 1;
                    }
                    $local = "";
                    if($count == 1){
                        $local = 1;
                    }
                ?>
                <?= form_dropdown('local', $lc, set_value('local', $local), 'class="form-control tip select2" id="local"  required="required" style="width:100%;"'); ?>
            </div>

            <div class="form-group has-feedback">
                <input type="email" name="identity" class="form-control" placeholder="<?= lang('email'); ?>" />
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" id="passw" name="password" class="form-control" placeholder="password"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>

            <button type="submit" style="background: #1C3F93;color: #fff" class="btn btn-block btn-flat">INGRESAR</button>

            <?= form_close(); ?>

            <!--   <div class="">
                <p>&nbsp;</p>
                <p><span class="text-danger"><?= lang('forgot_your_password');  ?><a href="#" class="text-danger" data-toggle="modal" data-target="#myModal"><?= lang('click_here'); ?></a></span><br>
                   
                </div>  -->

            </div>
        </div>

        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal"
        class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <?php echo form_open("auth/forgot_password"); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</i></button>
                    <h4 class="modal-title"><?= lang('forgot_password'); ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= lang('forgot_password_heading'); ?></p>
                    <input type="email" name="forgot_email" placeholder="<?= lang('email'); ?>" autocomplete="off"
                    class="form-control placeholder-no-fix">
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default pull-left" type="button"><?= lang('close'); ?></button>
                    <button class="btn btn-primary" type="submit"><?= lang('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
    <script src="<?= $assets ?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?= $assets ?>plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <script>
        $(function () {
            if ($('#identity').val())
                $('#password').focus();
            else
                $('#identity').focus();
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
                increaseArea: '20%'
            });
        });
    </script>
</body>
</html>
