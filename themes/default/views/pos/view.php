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
            .table { border-radius: 3px; }
            .table th { background: #f5f5f5; }
            .table th, .table td { vertical-align: middle !important; }
            h3 { margin: 5px 0; font-weight: bold; font-size: 14px;}
            hr { border: 1px dashed #000000 }
            .line{ border: 1px dashed  #000000 }
            @media print {
                .no-print { display: none; }
                #wrapper { max-width: 480px; width: 100%; min-width: 250px; margin: 0 auto; font-size: 12px; }
            }
        </style>
    </head>
    <body>
<?php } ?>
<div id="wrapper">
    <div id="receiptData">
    <div class="no-print">
        <?php if ($message) { ?>
            <div class="alert alert-success">
                <button data-dismiss="alert" class="close" type="button">×</button>
                <?= is_array($message) ? print_r($message, true) : $message; ?>
            </div>
        <?php } ?>
    </div>
    <div id="receipt-data">
        <div class="text-center">
                <?= $Settings->header; ?>
                <!-- <hr> -->
                <br><br>
                <h3 style="border-top: 1px dashed  #000000; "></h3>
                <h3>
                    <?php
                        if($inv->document_type==1){
                            echo strtoupper(lang("bill_title")." ".$inv->invoice_id);
                        }else if($inv->document_type==2){
                            echo strtoupper(lang("invoice_title")." ".$inv->invoice_id);
                        }else if($inv->document_type==3){
                            echo strtoupper(lang("nventa_sale")." ".$inv->invoice_id);//*****Renato TRJ023 25/04/2019   ********
                        }
                    ?>
                </h3>
                <h3 style="border-top: 1px dashed  #000000;"></h3>
                <br>
                <!-- <hr> -->
                <table>
                    <tr>
                        <td align="left" width="45%"><?= strtoupper(  lang("emission_date"));?><td>
                        <td width="10%">:</td>
                        <td align="left"><?= $this->tec->hrld($inv->date); ?><td>
                    </tr>
                    <tr>
                        <td align="left"><?= lang("correlative");?><td>
                        <td>:</td>
                        <td align="left"><?= $inv->id;?><td>
                    </tr>

                    <?php if($inv->document_type==1){ ?>
                            <tr>
                            <td align="left"><?= strtoupper(lang("customer"));?><td>
                            <td>:</td>
                            <td align="left"><?= $inv->customer_name; ?> <td>
                            <tr>
                    <?php   } ?>

                    </tr>
                </table>

            <div style="clear:both;"></div>
            <br>
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <!-- <th class="text-center col-xs-6"><?=lang('code') . " " . lang('description');?></th> -->
                        <th class="text-left col-xs-2"><?=lang('code');?></th>
                        <th class="text-left col-xs-8"><?=lang('description');?></th>
                        <th class="text-center col-xs-1"><?=lang('quantity');?></th>
                        <th class="text-center col-xs-1"><?=lang('price');?></th>
                        <th class="text-center col-xs-3"><?=lang('subtotal');?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $tax_summary = array();
                $subtotal_products = 0;
                foreach ($rows as $row) {
					//***********TRJ0004 - ALEXADER ROCA - 15/03/2019************
					$codigoProducto = "";
					
					if($row->product_code==''){
						$codigoProducto = " - ";
					}else{
						$codigoProducto = $row->product_code;
					}
					//***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************
					$nombreProduct=str_replace("<","&lt;",product_name($row->name));
					$nombreProduct=str_replace(">","&gt;",$nombreProduct);
					

                    echo '<tr><td class="text-left">' . $codigoProducto . '</td>';
					//***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************
                    echo '<td class="text-left">' . $nombreProduct . '</td>';
					//***********TRJ0004 - ALEXADER ROCA - 15/03/2019************
                    echo '<td class="text-center">' . $this->tec->formatNumber($row->quantity) . '</td>';
                    echo '<td class="text-center">'. $this->tec->formatMoney($row->unit_price);//($row->affect_price + ($row->item_tax / $row->quantity));

                    // if ($row->item_discount != 0) {
                    // echo '<td nowrap class="text-center"> &nbsp;&nbsp;' . '<del>' . $this->tec->formatMoney($row->real_unit_price) . '</del>' . ' ' . $this->tec->formatMoney($row->unit_price). '</td>';
                    // }
                    // else{
                    //     echo '<td class="text-center">'. $this->tec->formatMoney($row->unit_price). '</td>';
                    // }

                    echo '<td class="text-center">' . $this->tec->formatMoney($row->subtotal) . '</td></tr>';
                    $subtotal_products = $subtotal_products + $row->subtotal;
                }
                ?>
                </tbody>

                <?php
                // $gsub_total = $inv->subtotal - $inv->order_discount;
                // if ($Settings->rounding) {
                //     $round_total = $this->tec->roundNumber($gsub_total, $Settings->rounding); //grand_total
                //     // $rounding = $this->tec->formatMoney($inv->rounding);
                //     $rounding = $this->tec->formatMoney($round_total - $gsub_total); //grand_total
                // }
                // else {
                //     $round_total = $gsub_total; //grand_total
                // }

                $rounding = $this->tec->formatMoney($inv->rounding);

                $symbol = "";
                $currency = "";
                if($inv->currency == 'PEN'){
                    $symbol = "S/ ";
                    $currency = lang("currency_pen");
                }else if($inv->currency == 'USD'){
                    $symbol = "$ ";
                    $currency = lang("currency_USD");
                }
                ?>

            </table>

            <!-- <hr class="line"> -->
            <div class="text-right">
                <table class="table table-condensed">
                    <tbody>
                        <tr style="border-top: 1px dashed  #000000">
                            <td style="border: none; padding: 0px; padding-top: 5px;"><strong><?= strtoupper( lang("importe")).': '.$symbol; ?></strong></td>
                            <td style="border: none; padding: 0px; padding-top: 5px; padding-right: 5px;"><?= $this->tec->formatMoney($subtotal_products);//$inv->grand_total + $inv->total_discount) ;?></td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 0px; padding-bottom: 5px;"><?= strtoupper( lang("order_discount")).': '.$symbol; ?></td>
                            <td style="border: none; padding: 0px; padding-bottom: 5px; padding-right: 5px;"><?= $this->tec->formatMoney($inv->order_discount) ;?></td>
                        </tr>

                        <?php if($inv->document_type!=3){ ?>

                        <tr style="border-bottom: 1px dashed  #000000; border-top: 1px dashed  #000000">
                            <td style="border: none; padding: 0px;"><strong><?= strtoupper(lang("subtotal")).': '.$symbol; ?></strong></td>
                            <td style="border: none; padding: 0px; padding-right: 5px;"><?= $this->tec->formatMoney($inv->subtotal - $inv->order_discount) ?></td>
                        </tr>

                        <tr>
                            <td style="border: none; padding: 0px; padding-top: 5px;"><?= strtoupper(lang("opgrav")).': '.$symbol; ?></td>
                            <td style="border: none; padding: 0px; padding-top: 5px; padding-right: 5px;"><?= $this->tec->formatMoney($inv->affected); ?></td><!-- $inv->total -->
                        </tr>
						<tr><!--*****************TRJ008 - ALEXANDER ROCA - 11/04/2019***********************-->
                            <td style="border: none; padding: 0px;"><?= strtoupper(lang("opexonReal")).': '.$symbol; ?></td>
                            <td style="border: none; padding: 0px; padding-right: 5px;"><?= $this->tec->formatMoney($inv->exonerated); ?></td>
                        </tr><!--*****************TRJ008 - ALEXANDER ROCA - 11/04/2019***********************-->
                        <tr>
                            <td style="border: none; padding: 0px;"><?= strtoupper(lang("opexon")).': '.$symbol; ?></td>
                            <td style="border: none; padding: 0px; padding-right: 5px;"><?= $this->tec->formatMoney($inv->non_affected); ?></td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 0px;"><?= strtoupper(lang("product_tax")).': '.$symbol; ?></td> <!-- ' - '.lang("tax_igv") -->
                            <td style="border: none; padding: 0px; padding-right: 5px;"><?= $this->tec->formatMoney($inv->product_tax) ?></td>
                        </tr>
						<?php if($inv->tax_ICBPER > 0){?>
						<tr>
                            <td style="border: none; padding: 0px;"><?= strtoupper(lang('product_tax_ICBPER').' ('.lang('total_bags').')').': '.$symbol; ?></td> <!-- ' - '.lang("tax_igv") -->
                            <td style="border: none; padding: 0px; padding-right: 5px;"><?= $this->tec->formatMoney($inv->tax_ICBPER) ?></td>
                        </tr>
						<?php }?>

                        <?php } ?>

                        <tr style="border-bottom: 1px dashed  #000000" >
                            <td style="border: none; padding: 0px; padding-bottom: 5px;"><?= strtoupper(lang("rounding")).': '.$symbol; ?></td>
                            <td style="border: none; padding: 0px; padding-bottom: 5px; padding-right: 5px;"><?= $rounding; ?></td>
                        </tr>

                        <tr style="border-bottom: 1px dashed  #000000" >
                            <td style="border: none; padding: 0px;"><strong><?= strtoupper(lang("total_payable")).': '.$symbol; ?></strong></td>
                            <td style="border: none; padding: 0px; padding-right: 5px;"><?= $this->tec->formatMoney($inv->grand_total); // + $rounding); ?></td>
                        </tr>

                        <!-- <tr style="border-bottom: 1px dashed  #000000">
                            <td style="border: none; padding: 0px; padding-bottom: 5px;"><?= strtoupper(lang("importe")); ?> <?= strtoupper(lang("total")).': '.$symbol; ?></td>
                            <td style="border: none; padding: 0px; padding-bottom: 5px;"><?= $this->tec->formatMoney($inv->grand_total) ?></td>
                        </tr> -->
                <?php
                    if ($payments) {

                        foreach ($payments as $payment) {
                            if ($payment->paid_by == 'cash' && $payment->pos_paid) {
                                echo '<tr><td style="border: none; padding: 0px; padding-top: 5px;" width="75%">' . strtoupper(lang($payment->paid_by) . ' ' . $currency)  .'</td>
                                        <td style="border: none; padding: 0px; padding-top: 5px; padding-right: 5px;">' .  $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid)  .'</td><tr>';
                                echo '<tr><td style="border: none; padding: 0px;" width="75%">' . strtoupper(lang('change2')) .': '.$symbol .'</td>
                                        <td style="border: none; padding: 0px; padding-right: 5px;">' .  ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0)   .'</td><tr>';
                            }
                            if (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                                // echo '<tr><td style="border: none" width="75%">' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                                echo '<tr><td style="border: none" width="75%">'  . strtoupper(lang($payment->paid_by) . ' ' . $currency)  . '</td>
                                    <td style="border: none">' . $this->tec->formatMoney($payment->pos_paid) . '</td></tr>';
                                echo '<tr><td style="border: none" width="75%">' . lang("name") . ': '. '</td>
                                    <td style="border: none">' . $payment->cc_holder . '</td></tr>';
                                echo '<tr><td style="border: none" width="75%">' . lang("no") . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            }
                            if ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                                // echo '<tr><td style="border: none" width="75%">' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                                echo '<tr><td style="border: none" width="75%">' . strtoupper(lang($payment->paid_by) . ' ' . $currency) . '</td>
                                    <td style="border: none">' . $this->tec->formatMoney($payment->pos_paid) . '</td></tr>';
                                echo '<tr><td style="border: none" width="75%" >' . lang("cheque_no") . ': ' . '</td>
                                    <td style="border: none">' . $payment->cheque_no . '</td></tr>';
                            }
                            if ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                                // echo '<tr><td style="border: none" width="75%">' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                                echo '<tr><td style="border: none" width="75%">' . strtoupper(lang($payment->paid_by) . ' ' . $currency)  .'</td>
                                <td style="border: none">' .  $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid)  .'</td><tr>';
                                echo '<td style="border: none"">' . lang("no") . ': ' . $payment->gc_no . '</td>';
                                // echo '<tr><td style="border: none" width="75%">' . lang("amount") . ': ' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                                // echo '<td style="border: none">' . lang("no") . ': ' . $payment->gc_no . '</td></tr>';
                                // echo '<td style="border: none">' . lang("balance") . ': ' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</td></tr>';
                            }
                            if ($payment->paid_by == 'other' && $payment->amount) {
                                echo '<tr><td style="border: none" width="75%">' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                                echo '<td style="border: none">' . lang("amount") . ': ' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td></tr>';
                               // echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                                echo '<tr><td colspan=2>'.lang("payment_note") . ': ' . $payment->note .'</td></tr>';
                            }
                        }

                    }
                ?>

                    </tbody>
                </table>

            </div>

            <div class="text-left">
            <table class="table table-condensed">
                    <tbody>
                        <tr>
                            <td colspan="3" style="border: none; padding-bottom: 8px;">
                                <?php
                                    echo lang("tot_son") . ": ";
                                    echo numtoletras($inv->grand_total) . " " . $currency ;
                                ?>
                            </td>
                        </tr>
                        <?php if($inv->document_type==1 || $inv->document_type==3){?>
                        <tr style="border-top: 1px dashed #000000">
                            <td style="border: none; padding: 0px; padding-top: 5px;" width="25%"><?=strtoupper(lang("customer"))?></td>
                            <td style="border: none; padding: 0px; padding-top: 5px;" width="5%">:</td>
                            <td style="border: none; padding: 0px; padding-top: 5px;"><?= $customer->name?></td>
                        </tr>
                        <tr>
                            <?php

                                $document = strtoupper(lang("ccf1"));
                                $documentID = $customer->cf1;

                                // Si el cliente es una empresa
                                if($customer->cf1 == null && $customer->cf2 != null){
                                    $document = strtoupper(lang("ccf2"));
                                    $documentID = $customer->cf2;
                                }

                            ?>
                            <td style="border: none; padding: 0px;"><?= $document //strtoupper(lang("ccf1"))?></td>
                            <td style="border: none; padding: 0px;">:</td>
                            <td style="border: none; padding: 0px;"><?= $documentID ?></td>
                        </tr>

                        <?php } else if($inv->document_type==2){?>

                        <tr style="border-top: 1px dashed #000000">
                            <td style="border: none; padding: 0px; padding-top: 5px;" width="25%"><?=strtoupper(lang("enterprise"))?></td>
                            <td style="border: none; padding: 0px; padding-top: 5px;" width="5%">:</td>
                            <td style="border: none; padding: 0px; padding-top: 5px;"><?= $customer->name?></td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 0px;"><?=strtoupper(lang("ccf2"))?></td>
                            <td style="border: none; padding: 0px;">:</td>
                            <td style="border: none; padding: 0px;"><?= $customer->cf2?></td>
                        </tr>
                        <tr style="border-bottom: 1px dashed  #000000">
                            <td style="border: none; padding: 0px; padding-bottom: 5px;"><?=lang("customer_address")?></td>
                            <td style="border: none; padding: 0px; padding-bottom: 5px;">:</td>
                            <td style="border: none; padding: 0px; padding-bottom: 5px;"><?= $customer->direccion?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>

            <?php
            // if ($payments) {
               /* echo '<table class="table table-striped table-condensed"><tbody>';
                foreach ($payments as $payment) {
                    echo '<tr>';
                    if ($payment->paid_by == 'cash' && $payment->pos_paid) {
                        echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                        echo '<td>' . lang("amount") . ': A' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td>';
                        echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</td>';
                    }
                    if (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                        echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                        echo '<td>' . lang("amount") . ': B' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                        echo '<td>' . lang("no") . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                        echo '<td>' . lang("name") . ': ' . $payment->cc_holder . '</td>';
                    }
                    if ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                        echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                        echo '<td>' . lang("amount") . ': C' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                        echo '<td>' . lang("cheque_no") . ': ' . $payment->cheque_no . '</td>';
                    }
                    if ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                        echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                        echo '<td>' . lang("no") . ': ' . $payment->gc_no . '</td>';
                        echo '<td>' . lang("amount") . ': D' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                        echo '<td>' . lang("balance") . ': ' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</td>';
                    }
                    if ($payment->paid_by == 'other' && $payment->amount) {
                        echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                        echo '<td>' . lang("amount") . ': E' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td>';
                        echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                    }
                    echo '</tr>';
                }

                echo '</tbody></table>';*/

                if($inv->document_type != 3){

                    $qr = "http://mederma.sistematpv.com/Search_invoice";

                    // $ruc = "20603248661";
                    $ruc = $this->session->userdata('ruc');
                    $serieNumero = explode('-',$inv->invoice_id);
                    $serie = $serieNumero[0];
                    $numero = $serieNumero[1];
                    $igv = $inv->total_tax;
                    $total = $inv->amount;
                    $dateTime = explode(' ',$inv->date);
                    $date = $dateTime[0];
                    $hash = $inv->digest_value;

                    if($inv->document_type == 2){
                        $cusDoc = "06";
                        $docType = "01";
                        $numDoc = $customer->cf2;
                        $qr = $ruc."|".$docType."|".$serie."|".$numero."|".$igv."|".$total."|".$date."|".$cusDoc."|".$numDoc."|".$hash;
                    }else{
                        $cusDoc = "01";
                        $docType = "03";
                        $numDoc = $customer->cf1;
                        $qr = $ruc."|".$docType."|".$serie."|".$numero."|".$igv."|".$total."|".$date."|".$cusDoc."|".$numDoc."|".$hash;
                    }

                    //$ex1 = new QRCodeGenerator('http://www.nfe.fazenda.gov.br/portal/consulta.aspx?tipoConsulta=completa&tipoConteudo=XbSeqxE8pl8=MobLanche_PDVPARATODOS.COM.BR');
                    //****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
					$ex1 = new QRCodeGenerator();
                    echo "<img src=".$ex1->generate($serieNumero[0].$serieNumero[1],$qr)." width="."150". " height="."150"." >";
					//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************

                }
            // }

            ?>

            <?= $inv->note ? '<p class="text-center">' . $this->tec->decode_html($inv->note) . '</p>' : ''; ?>

            <!-- <div class="well well-sm"> -->
            <?php
                        if($inv->document_type==1){
                            echo "<br>";
                            echo strtoupper("REPRESENTACIÓN IMPRESA DE LA ".lang("bill_title"));
                        }else if($inv->document_type==2){
                            echo "<br>";
                            echo strtoupper("REPRESENTACIÓN IMPRESA DE LA ".lang("invoice_title"));
                        }
            ?>

            <?php if($inv->document_type!=3){ ?>
                <br>
                <?= $Settings->footer; ?>
            <?php } ?>

            <div style="border-top: 1px dashed  #000000; margin-bottom: 5px; margin-top: 8px;"></div>
            <div class="text-center"><?= lang("cashier").': '. $user->first_name. ' '. $user->last_name ?></div>
            <!-- </div> -->
            <br><br>



            </div>
        <div style="clear:both;"></div>
    </div>
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
        <a href="javascript:window.print()" id="web_print" class="btn btn-block btn-primary"
           onClick="window.print();return false;"><?= lang("web_print"); ?></a>
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
        <?php if ($noprint== 2) { ?>
        $(window).load(function () {
            window.print();
        });
    <?php } ?>
            </script>
</body>
</html>
<?php } ?>
