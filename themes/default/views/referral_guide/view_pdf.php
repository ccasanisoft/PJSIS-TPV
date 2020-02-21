<?php
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
include "app/class/QRCodeGenerator.class.php";
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
function product_name($name)
{
    return character_limiter($name, (isset($Settings->char_per_line) ? ($Settings->char_per_line-8) : 35));
}

if ($modal) {
    echo '<div class="modal-dialog no-modal-header"><div class="modal-content"><div class="modal-body"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>';
} else { ?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?= $page_title . " " . lang("no") . " " . $id; ?></title>
        <base href="<?= base_url() ?>"/>
        <meta http-equiv="cache-control" content="max-age=0"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="expires" content="0"/>
        <meta http-equiv="pragma" content="no-cache"/>
        <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
        <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <style type="text/css" media="all">
            body { color: #000; }
            #wrapper { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            
        </style>
    </head>
    <body>
<?php } ?>
<div id="wrapper">
    <div id="receiptData">
	
	<!--**************************************************************************************************-->
	<!--<embed id="pdfPrintfile" src="<?php echo base_url();?>uploads/pdf/<?=$file_pdf;?>" type="application/pdf" width="100%" height="600px" />-->
	<iframe id="pdfPrintfile" name="pdfPrintfile" src="<?php echo base_url();?>uploads/pdf/guia_remision/<?=$file_pdf;?>" width="100%" height="780" style="border: none;"></iframe>
	<!--**************************************************************************************************-->

             <div style="clear:both;"></div>
            <br>

<?php if ($modal) {
    echo '</div></div></div></div>';
} else { ?>
<div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
    <!-- <hr> -->
    <?php if ($message) { ?>
    <div class="alert alert-success">
        <button data-dismiss="alert" class="close" type="button">×</button>
        <?= is_array($message) ? print_r($message, true) : $message; ?>
    </div>
<?php } ?>

    <?php if ($Settings->java_applet) { ?>
        <span class="col-xs-12"><a class="btn btn-block btn-primary" onClick="printReceipt()"><?= lang("print"); ?></a></span>
        <span class="col-xs-12"><a class="btn btn-block btn-info" type="button" onClick="openCashDrawer()"><?= lang('open_cash_drawer'); ?></a></span>
        <div style="clear:both;"></div>
    <?php } else { ?>

	<span class="pull-right col-xs-12">
        <a id="web_print" class="btn btn-block btn-primary"
          onClick="imprimirPDFhtml()" ><?= lang("web_print"); ?></a>
    </span>
    <?php } ?>
    <span class="pull-left col-xs-12"><a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a></span>

    <span class="col-xs-12">
    <?php if($vista==2){ ?>
				<a class="btn btn-block btn-warning" href="<?= site_url('Referral_guide'); ?>">Ir a Lista de Guías de Remisión</a>
		<?php }else {?>
				<a class="btn btn-block btn-warning" onclick="window.close()">Cerrar Ventana</a>
        <?php }?>

    </span>
        <div style="clear:both;"></div>
        <div class="col-xs-12" style="background:#F5F5F5; padding:10px;">
            <font size="-2">
            <p style="font-weight:bold;">Por favor cambia la configuración de impresión de tu navegador</p>
            <p style="text-transform: capitalize;"><strong>FireFox:</strong> Archivo &gt; Configurar impresora &gt; Margen &amp; encabezado/pie de página - Ninguno -</p>
            <p style="text-transform: capitalize;"><strong>Chrome:</strong> Menú &gt; Imprimir &gt; Márgenes: Ninguno</p></div>
            <font>
    <div style="clear:both;"></div>

</div>

</div>
<canvas id="hidden_screenshot" style="display:none;">

</canvas>
<div class="canvas_con" style="display:none;"></div>
<script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#email').click(function () {
                        var email = prompt("<?= lang("email_address"); ?>", "<?= $customer->email; ?>");
                        if (email != null) {
                            $.ajax({
                                type: "post",
                                url: "<?= site_url('Referral_guide/email_receipt') ?>",

                                data: {<?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: "<?= $id; ?>"},

                                dataType: "json",
                                success: function (data) {
                                    alert(data.msg);
                                },
                                error: function () {
                                    alert('<?= lang('ajax_request_failed'); ?>');
                                    return false;
                                }
                            });
                        }
                        return false;
                    });
                });
        <?php if ($noprint == 2) { ?>
        $(window).load(function () {
            window.frames["pdfPrintfile"].focus();
			window.frames["pdfPrintfile"].print();
        });
    <?php } ?>
            </script>
			<script>
			function imprimirPDFhtml(){
				window.frames["pdfPrintfile"].focus();
				window.frames["pdfPrintfile"].print();
			}
			</script>
</body>
</html>
<?php } ?>