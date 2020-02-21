<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title.' | '.$Settings->site_name; ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.ico"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/iCheck/square/yellow.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/redactor/redactor.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/custom.css" rel="stylesheet" type="text/css" />
    <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
</head>
<input type="hidden" id="flag" name="flag" value="<?php echo $_SESSION["noencontrado"];?>">
<input type="hidden" id="flag2" name="flag2" value="<?php echo $_SESSION["file_name"];;?>">
<input type="hidden" id="vurl" name="vurl" value="<?php echo base_url('uploads/');?>">
<div class="wrapper">
<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

    <header class="main-header">
        <a href="<?= site_url(); ?>" class="logo">
            <span class="logo-mini">TPV</span>
            <span class="logo-lg"><?= $Settings->site_name == 'TPV' ? '<b>TPV</b>' : '<img src="'.base_url('uploads/'.$Settings->logo).'" alt="'.$Settings->site_name.'" width=200 height=45 />'; ?></span>
        </a>
    </header>
<script type="text/javascript">
    var dt = "";
    var _flag = 1;


    $(document).ready(function () {
        $("#refreshCaptcha").click(
            function () {
                        $.get('<?= site_url('search_invoice/refresh') ?>', function(data){
                            $('#captImg').html(data);
                            $("#pErrCapctha").html("");
                        });
            }
        );
        $("#btnSearch").click(
            function () {
                $("#divError").attr("style","display:none");
                $("#pErrCapctha").html("");
            });
        

        $("#document_type").change(
            function(){
                if( $(this).val()==1){
                    $("#rowRuc").attr("style","display:none");
                    $("#ruc").removeAttr("required");
                }else{
                    $("#rowRuc").attr("style","display:block");
                    $("#ruc").attr("required","required"); 
                }
            }
        ).trigger("change");

        $("#btnReset").click(
            function () {
                $("#document_type").val(0);
                $("#correlativo,#number, #date,#ruc, #total, #captcha").val("");
                $("#refreshCaptcha").trigger("click");
            }
        );

        if(parseInt($("#flag").val())==-1){
            $("#divError").attr("style","display:block")    
        }else{
            if( $("#flag2").val()!=""){ 
                //window.open($("#vurl").val() + "/pdf/" +  $("#flag2").val());
                //DESCARGA
                          /*  var link = document.createElement('a');
                            link.href = $("#vurl").val() + "/pdf/";
                            link.download =  $("#flag2").val();
                            link.dispatchEvent(new MouseEvent('click'));*/
                //FIN DESCARGA
                $("#divError").attr("style","display:none");
                $("#pErrCapctha").html("");

                //LIMPIA SESION
                $.ajax({
                    type: "get",
                    url: '<?= site_url('search_invoice/limpiarsesion') ?>',
                    data: $( this ).serialize(),
                    dataType: "json",
                    success: function(res) {
                      
                     
                    }
                });

            }
        }


    });



</script>
<style type="text/css">
    .table td:first-child { padding: 1px; }
    .table td:nth-child(6), .table td:nth-child(7), .table td:nth-child(8) { text-align: center; }
    .table td:nth-child(9)<?= $Admin ? ', .table td:nth-child(10)' : ''; ?> { text-align: right; }
</style>
<section class="content">
<input type="hidden" id="vurl" value="<?= site_url('products/get_products/') ?>" >
<input type="hidden" id="fflag" value="" >
<?php //$attrib = array('class' => 'validation', 'role' => 'form');
echo form_open("search_invoice/search"); ?>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('search_by'); ?></h3>
                </div>
                <div class="box-body">
            <div class="alert alert-danger alert-dismissable" id="divError" style="display: none;">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <span id="spanError"><?= lang("inv_not_found"); ?></span>
            </div>                    
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('document_type', 'document_type'); ?>
                                    <select id="document_type" name="document_type" class="form-control document_type select2" required style="width:100%;">
                                        <option value=""><?= lang("select"); ?></option>
                                        <option value="1" <?php if($_SESSION["v1"]==1){echo " selected ";} ?> ><?= lang("bill_type"); ?></option>
                                        <option value="2" <?php if($_SESSION["v1"]==2){echo " selected ";} ?>><?= lang("invoice"); ?></option>
                                    </select>                                
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <?= lang("document_number2", "document_number2"); ?>
                                    <?php echo form_input('correlativo', $_SESSION["v2"], 'class="form-control" placeholder="00000" id="correlativo" required="required"'); ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group"><label>&nbsp;</label>
                                    <?php echo form_input('number', $_SESSION["v3"], 'class="form-control " id="number" placeholder="00000" required="required"'); ?>   
                                </div>
                            </div>

                            <input type="hidden" value="76" name="sale_id">

                        </div>
<!--
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang("document_number2", "document_number2"); ?>
                                    <?php echo form_input('correlativo', $_SESSION["v2"], 'class="form-control" id="correlativo" required="required"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang("document_number", "document_number"); ?>
                                    <?php echo form_input('number', $_SESSION["v3"], 'class="form-control " id="number" required="required"'); ?>
                                </div>
                            </div>
                        </div>-->



                        <div class="row">                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("emission_date2", "emission_date2"); ?>
                                    <?= form_input('date', $_SESSION["v4"], 'class="form-control datetimepicker" id="date" required="required"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="rowRuc" style="visibility: none;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("ccf2", "ccf2"); ?>
                                    <?php echo form_input('ruc', $_SESSION["v5"], 'class="form-control" id="ruc"'); ?>
                                </div>

                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("total3", "total3"); ?>
                                    <?php echo form_input('total', $_SESSION["v6"], 'class="form-control" id="grand_total" required="required"'); ?>
                                </div>

                            </div>
                        </div> 
                    <?php if( isset($_SESSION["errCaptcha"]) && $_SESSION["errCaptcha"]!=""){?>
                        <div class="row">
                            <div class="col-md-4">
                                <p style="color: red" id="pErrCapctha" ><?php echo $_SESSION["errCaptcha"]; ?></p>
                                
                            </div>
                        </div>
                    <?php  }?>
                        <div class="row">
                            <div class="col-md-3">
                                <p id="captImg"><?php echo $captchaImg; ?></p>
                                <input type="text" name="captcha" id="captcha" value=""/>
                                    <img style="cursor: pointer;" width="30" id="refreshCaptcha" src="<?php echo base_url().'uploads/refresh.png'; ?>"/>                                    
                            </div>
                        </div>
 

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <br>
                                    <button type="button" id="btnReset" class="btn btn-default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= lang("reset")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>

                                    <button type="submit" id="btnSearch" class="btn btn-success">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= lang("search2")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php echo form_close(); ?>
    </div>
<!--
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('list_results'); ?></h3>
                </div>
                <div class="box-body">
                        <div class="table-responsive">
                        <table id="fileData" class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
                            <thead>
                            <tr class="active">
                                <th style="max-width:30px;">#</th>
                                <th class="col-xs-3"><?= lang("customer"); ?></th>
                                <th class="col-xs-1"><?= lang("ccf2"); ?></th>
                                <th><?= lang("document_type"); ?></th>
                                <th><?= lang("document_number"); ?></th>
                                <th><?= lang("total"); ?></th>
                                <th style="width:145px;"><?= lang("download"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="7" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>

                        

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>-->
</section>


    
</div>
<script src="<?= $assets ?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });
</script>

