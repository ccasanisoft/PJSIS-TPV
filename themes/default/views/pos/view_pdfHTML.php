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
        <title><?= $page_title . " " . lang("no") . " " . $inv->id; ?></title>
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
<?php 
		$width = 334;
		$height = 102;

		if ($pos_logo == 2) {
			$width = 334;
			$height = 102;
		} else if ($pos_logo == 1) {
			$width = 84;
			$height = 102;
		}

if ($type_imagen_pdf == 1){	
		$cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen) . '" width="' . $width . '" height="' . $height . '">';
		$cabecera2 = '<div><br>' . $razon_social . '<br>' . $direccion . '<br>' . $region_pais . '<br>' . $_ruc.'</div>';
           } 
	else if ($type_imagen_pdf == 2) {
		$width2 = 370;
		$height2 = 150;
		$cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen2) . '" width="' . $width2 . '" height="' . $height2 . '">';
		$cabecera2 ="";
    }
	
	$ex1 = new QRCodeGenerator();
	$qr_img = '<img src="' . $ex1->generate($serieNumero[0].$serieNumero[1],$qr) . '" width="120" height="120">';
?>
<div id="wrapper">
    <div id="receiptData">
	
	<!--**************************************************************************************************-->
	<div id="pdfPrint">
	
	<table border="0" width="100%" cellspacing="0" cellpadding="2" >
                <tr>
                    <td width="65%" align="center" style="font-size:10px">
                        <?php echo $cabecera; ?>
						<?php echo $cabecera2; ?>
                    </td>
                    <td width="35%" valign="middle"><br><br><br><br>
                        <table border="1" width="80%" cellspacing="0" cellpadding="2" >
                            <tr>
                                <td align="center"  style="font-size:10px"><h2><?=$_ruc;?></h2></td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#E4E4E4" style="font-size:10px"><h2><?=$tipo_doc;?></h2></td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size:10px"><h2><?=$numComp;?></h2></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                <td width="65%" align="left">

                        <?=$impRuc;?>

                    </td>

                    <td width="35%">

                        <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                        <tr>
                                <td align="left" style="font-size:10px"> <u><?=$region;?></u> ,  <u>     <?=$dia;?>     </u>   de   <u>   <?=$mes;?>   </u>   del   <u>     <?=$ano;?>     </u> </td>
                        </tr>
                        <tr>
                                <td align="left"  style="font-size:10px"><?= $l_currency.": ". $currency;?></td>
                        </tr>

                        </table>

                    </td>
                </tr>
            </table>

                <br><br>

                <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                    <tr>
                        <td bgcolor="#E4E4E4" align="center" width="10%" style="font-size:10px"><strong><?=$l_qty;?></strong></td>
                        <td bgcolor="#E4E4E4" align="center" width="60%" style="font-size:10px"><strong><?=$l_description;?></strong></td>
                        <td bgcolor="#E4E4E4" align="center" width="15%" style="font-size:10px"><strong>Precio Unitario</strong></td>
                        <td bgcolor="#E4E4E4" align="center" width="15%" style="font-size:10px"><strong><?=$l_importe;?></strong></td>
                    </tr>
                    <?=$row_items_html;?>
                </table>
                <table border="0" width="100%" cellspacing="0" cellpadding="2" >
					<tr>
                        <td align="left" style="font-size:10px">SON: <?=$totLetras;?></td>
                    </tr>
                </table>
                <table border="0" width="100%" cellspacing="0" cellpadding="2" >
                    <tr>
                        <td  width="70%" align="center">
							<table border="0" cellspacing="0" cellpadding="2" >
								<tr>
									<td align="center"><?=$qr_img;?></td>
									<br>
								</tr>
                                <tr>
                                    <td align="center" style="font-size:10px"><i>CANCELADO</i></td>
                                </tr>
                                <BR>
                                <tr>
                                    <td align="center" style="font-size:10px"><u><?=$region;?></u> ,  __________<!--$dia--> de __________<!--$mes--> del __________<!--$ano--> </td>
                                </tr>
                            </table>
                        </td>

                        <td align="center" width="30%">

                        <br><br>

                            <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Sub Total</strong></td>
                                    <td align="right" width="40%" style="font-size:10px"><?=$sub;?></td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Descuento</strong></td>
                                    <td align="right" width="40%" style="font-size:10px"><?=$desc;?></td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Total</strong></td>
                                    <td align="right" width="40%" style="font-size:10px"><?=$tot1;?></td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Op. Gravadas</strong></td>
                                    <td align="right" width="40%" style="font-size:10px"><?=$afc;?></td>
                                </tr>
								<tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong><?=$op_exoReal;?></strong></td>
                                    <td align="right" width="40%" style="font-size:10px"><?=$exoReal;?></td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong><?=$op_exo;?></strong></td>
                                    <td align="right" width="40%" style="font-size:10px"><?=$exo;?></td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>IGV - <?=$ivPorc;?> %</strong></td>
                                    <td align="right" width="40%" style="font-size:10px"><?=$igv;?></td>
								</tr>
								<tr>
									<td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong><?=$op_ICBPER;?></strong></td>
									<td align="right" width="40%" style="font-size:10px"><?=$tax_ICBPER;?></td>
								</tr>
								<tr>
									<td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Redondeo</strong></td>
									<td align="right" width="40%" style="font-size:10px"><?=$round;?></td>
								</tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Total Venta</strong></td>
                                    <td align="right" width="40%" style="font-size:10px"><?=$tot2;?></td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
	</div>

                <br>
	
	
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
        <!--<span class="pull-right col-xs-12">
        <a href="javascript:window.print()" id="web_print" class="btn btn-block btn-primary"
           onClick="window.print();return false;"><?= lang("web_print"); ?></a>
    </span>-->
	<span class="pull-right col-xs-12">
        <a id="web_print" class="btn btn-block btn-primary"
           onClick="imprimirPDFhtml();"><?= lang("web_print"); ?></a>
    </span>
    <?php } ?>
    <span class="pull-left col-xs-12"><a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a></span>

    <span class="col-xs-12">
    <?php
        if($this->Caja){ 
		//***************TRJ062 - KENY PONTE - 20/09/2019**************
			if($vista==2){ ?>
				<a class="btn btn-block btn-warning" href="<?= site_url('pos'); ?>"><?= lang("back_to_pos"); ?></a>
		<?php }else {?>
				<a class="btn btn-block btn-warning" onclick="window.close();"><?= lang("back_to_sales"); ?></a>
        <?php }
		
		}else{ 
           if($vista==2){ ?>
				<a class="btn btn-block btn-warning" href="<?= site_url('pos'); ?>"><?= lang("back_to_pos"); ?></a>
		<?php }else {?>
				<a class="btn btn-block btn-warning" onclick="window.close()"><?= lang("back_to_sales"); ?></a>
        <?php }
        }
        //***************TRJ062 - KENY PONTE - 20/09/2019**************
         ?>

    </span>
    <?php if (!$Settings->java_applet) { ?>
        <div style="clear:both;"></div>
        <div class="col-xs-12" style="background:#F5F5F5; padding:10px;">
            <font size="-2">
            <p style="font-weight:bold;">Por favor cambia la configuración de impresión de tu navegador</p>
            <p style="text-transform: capitalize;"><strong>FireFox:</strong> Archivo &gt; Configurar impresora &gt; Margen &amp; encabezado/pie de página - Ninguno -</p>
            <p style="text-transform: capitalize;"><strong>Chrome:</strong> Menú &gt; Imprimir &gt; Márgenes: Ninguno</p></div>
            <font>
    <?php } ?>
    <div style="clear:both;"></div>

</div>

</div>
<canvas id="hidden_screenshot" style="display:none;">

</canvas>
<div class="canvas_con" style="display:none;"></div>
<script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
<?php if ($Settings->java_applet) {

        function drawLine($Settings)
        {
            $size = $Settings->char_per_line;
            $new = '';
            for ($i = 1; $i < $size; $i++) {
                $new .= '-';
            }
            $new .= ' ';
            return $new;
        }

        function printLine($str, $Settings, $sep = ":", $space = NULL)
        {
            $size = $space ? $space : $Settings->char_per_line;
            $lenght = strlen($str);
            list($first, $second) = explode(":", $str, 2);
            $new = $first . ($sep == ":" ? $sep : '');
            for ($i = 1; $i < ($size - $lenght); $i++) {
                $new .= ' ';
            }
            $new .= ($sep != ":" ? $sep : '') . $second;
            return $new;
        }

        function printText($text, $Settings)
        {
            $size = $Settings->char_per_line;
            $new = wordwrap($text, $size, "\\n");
            return $new;
        }

        function taxLine($name, $code, $qty, $amt, $tax)
        {
            return printLine(printLine(printLine(printLine($name . ':' . $code, '', 18) . ':' . $qty, '', 25) . ':' . $amt, '', 35) . ':' . $tax, ' ');
        }

        ?>

        <script type="text/javascript" src="<?= $assets ?>plugins/qz/js/deployJava.js"></script>
        <script type="text/javascript" src="<?= $assets ?>plugins/qz/qz-functions.js"></script>
        <script type="text/javascript">
            deployQZ('themes/<?=$Settings->theme?>/assets/plugins/qz/qz-print.jar', '<?= $assets ?>plugins/qz/qz-print_jnlp.jnlp');
            usePrinter("<?= $Settings->receipt_printer; ?>");
            <?php /*$image = $this->tec->save_barcode($inv->reference_no);*/ ?>
            function printReceipt() {
                //var barcode = 'data:image/png;base64,<?php /*echo $image;*/ ?>';
                receipt = "";
                receipt += chr(27) + chr(69) + "\r" + chr(27) + "\x61" + "\x31\r";
                receipt += "<?= printText(strip_tags(preg_replace('/\s+/',' ', $Settings->header)), $Settings); ?>" + "\n";
                receipt += " \x1B\x45\x0A\r ";
                receipt += "<?=drawLine($Settings);?>\r\n";
                //receipt += "<?php // if($Settings->invoice_view == 1) { echo lang('tax_invoice'); } ?>\r\n";
                //receipt += "<?php // if($Settings->invoice_view == 1) { echo drawLine(); } ?>\r\n";
                receipt += "\x1B\x61\x30";
                receipt += "<?= printLine(lang("sale_no") . ": " . $inv->id, $Settings) ?>" + "\n";
                receipt += "<?= printLine(lang("sales_person") . ": " . $created_by->first_name." ".$created_by->last_name, $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("customer") . ": " . $inv->customer_name, $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("date") . ": " . $this->tec->hrld($inv->date), $Settings); ?>" + "\n\n";
                receipt += "<?php $r = 1;
            foreach ($rows as $row): ?>";
                receipt += "<?= "#" . $r ." "; ?>";
                receipt += "<?= product_name(addslashes($row->product_name)); ?>" + "\n";
                receipt += "<?= printLine($this->tec->formatNumber($row->quantity)."x".$this->tec->formatMoney($row->affect_price+($row->item_tax/$row->quantity)) . ":  ". $this->tec->formatMoney($row->subtotal), $Settings, ' ') . ""; ?>" + "\n";
                receipt += "<?php $r++;
            endforeach; ?>";
                receipt += "\x1B\x61\x31";
                receipt += "<?=drawLine($Settings);?>\r\n";
                receipt += "\x1B\x61\x30";
                receipt += "<?= printLine(lang("total") . ": " . $this->tec->formatMoney($inv->total+$inv->product_tax), $Settings); ?>" + "\n";
                <?php if ($inv->order_tax != 0) { ?>
                receipt += "<?= printLine(lang("tax") . ": " . $this->tec->formatMoney($inv->order_tax), $Settings); ?>" + "\n";
                <?php } ?>
                <?php if ($inv->total_discount != 0) { ?>
                receipt += "<?= printLine(lang("discount") . ": " . $this->tec->formatMoney($inv->total_discount), $Settings); ?>" + "\n";
                <?php } ?>
                <?php if($Settings->rounding) { ?>
                receipt += "<?= printLine(lang("rounding") . ": " . $rounding, $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("grand_total") . ": " . $this->tec->formatMoney($inv->grand_total + $rounding), $Settings); ?>" + "\n";
                <?php } else { ?>
                receipt += "<?= printLine(lang("grand_total") . ": " . $this->tec->formatMoney($inv->grand_total), $Settings); ?>" + "\n";
                <?php } ?>
                <?php if($inv->paid < $inv->grand_total) { ?>
                receipt += "<?= printLine(lang("paid_amount") . ": " . $this->tec->formatMoney($inv->paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("due_amount") . ": " . $this->tec->formatMoney($inv->grand_total-$inv->paid), $Settings); ?>" + "\n\n";
                <?php } ?>
                <?php
                if($payments) {
                    foreach($payments as $payment) {
                        if ($payment->paid_by == 'cash' && $payment->pos_paid) { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->pos_paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("change") . ": " . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0), $Settings); ?>" + "\n";
                <?php } if (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->pos_paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("card_no") . ": xxxx xxxx xxxx " . substr($payment->cc_no, -4), $Settings); ?>" + "\n";
                <?php  } if ($payment->paid_by == 'gift_card') { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->pos_paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("card_no") . ": " . $payment->gc_no, $Settings); ?>" + "\n";
                <?php } if ($payment->paid_by == 'Cheque' && $payment->cheque_no) { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->pos_paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("cheque_no") . ": " . $payment->cheque_no, $Settings); ?>" + "\n";
                <?php if ($payment->paid_by == 'other' && $payment->amount) { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->amount), $Settings); ?>" + "\n";
                receipt += "<?= printText(lang("payment_note") . ": " . $payment->note, $Settings); ?>" + "\n";
                <?php }
            }

        }
    }

    // /* if($Settings->invoice_view == 1) {
    //     if(!empty($tax_summary)) {
    // ?>
    //             receipt += "\n" + "<?= lang('tax_summary'); ?>" + "\n";
    //             receipt += "<?= taxLine(lang('name'),lang('code'),lang('qty'),lang('tax_excl'),lang('tax_amt')); ?>" + "\n";
    //             receipt += "<?php foreach ($tax_summary as $summary): ?>";
    //             receipt += "<?= taxLine($summary['name'],$summary['code'],$this->tec->formatNumber($summary['items']),$this->tec->formatMoney($summary['amt']),$this->tec->formatMoney($summary['tax'])); ?>" + "\n";
    //             receipt += "<?php endforeach; ?>";
    //             receipt += "<?= printLine(lang("total_tax_amount") . ":" . $this->tec->formatMoney($inv->product_tax)); ?>" + "\n";
    //             <?php
    //                 }
    //             } */
                ?>
                receipt += "\x1B\x61\x31";
                <?php if ($inv->note) { ?>
                receipt += "<?= printText(strip_tags(preg_replace('/\s+/',' ', $this->tec->decode_html($inv->note))), $Settings); ?>" + "\n";
                <?php } ?>
                receipt += "<?= printText(strip_tags(preg_replace('/\s+/',' ', $Settings->footer)), $Settings); ?>" + "\n";
                receipt += "\x1B\x61\x30";
                <?php if(isset($Settings->cash_drawer_cose)) { ?>
                print(receipt, '', '<?=$Settings->cash_drawer_cose;?>');
                <?php } else { ?>
                print(receipt, '', '');
                <?php } ?>

            }

        </script>
    <?php } ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#email').click(function () {
                        var email = prompt("<?= lang("email_address"); ?>", "<?= $customer->email; ?>");
                        if (email != null) {
                            $.ajax({
                                type: "post",
                                url: "<?= site_url('pos/email_receipt') ?>",
                                data: {<?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: <?= $inv->id; ?>},
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
        <?php if (!$Settings->java_applet && !$noprint) { ?>
        $(window).load(function () {
            window.print();
        });
    <?php } ?>
            </script>
			<!--************TRJ098 Alexander Roca 17-10-2019 *********************-->
			<script>
			function imprimirPDFhtml(){
				//#wrapper { max-width: 480px; margin: 0 auto; padding-top: 20px; }
				document.getElementById("wrapper").style.maxWidth ="none";
				document.getElementById("wrapper").style.paddingTop ="0px";
				document.getElementById("wrapper").style.margin ="none";
				document.getElementById("buttons").style.display ="none";
				window.print();
				document.getElementById("wrapper").style.maxWidth ="480px";
				document.getElementById("wrapper").style.paddingTop ="20px";
				/*var newWindow = window.open();
				newWindow.document.write(document.getElementById("pdfPrint").innerHTML);
				newWindow.print();*/
			}
			</script>
			<!--************TRJ098 Alexander Roca 17-10-2019 ************************-->
</body>
</html>
<?php } ?>
