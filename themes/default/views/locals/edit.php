<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <!-- <h3 class="box-title"><?= lang('enter_info'); ?></h3> -->
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <?= form_open_multipart("locals/edit/".$local->id, 'class="validation"');?>
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <?= lang('code', 'code'); ?>
                                    <?= form_input('code', set_value('code', $local->code), 'class="form-control tip" id="code"  required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('name', 'name'); ?>
                                    <?= form_input('name', set_value('name', $local->name), 'class="form-control tip" id="name"  required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('address_name', 'address_name'); ?>
                                    <?= form_input('address', set_value('address', $local->address), 'class="form-control tip" id="address"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('cod_sunat', 'cod_sunat'); ?>
                                    <?= form_input('cod_sunat', set_value('cod_sunat', $local->cod_sunat), 'class="form-control tip" id="cod_sunat"  required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang("default_warehouse", 'default_warehouse'); ?>
                                    <?php
                                    $wh[""] = lang("none2");
                                    foreach ($warehouses as $warehouse) {
                                        $wh[$warehouse->id] = $warehouse->warehouse;
                                    }
                                    echo form_dropdown('default_warehouse', $wh, $local->default_warehouse, 'class="form-control select2" style="width:100%;" id="default_warehouse"'); ?>
                                </div>

                                <div class="form-group">
                                    <div class="form-group">
                                        <?php

                                            if($local->invoice_format){

                                                $new_number = (int)$local->invoice_number + 1;
                                                $invoice_format = $local->invoice_format;

                                            } else {

                                                $new_number = (int)$settings->invoice_number + 1;
                                                $invoice_format = $settings->invoice_format;

                                            }

                                            $parte1=explode('{',$invoice_format);
                                            $parte2=explode('}',$parte1[1]);

                                            $parentesis= $parte2[0];

                                            $buscar = strpos($parentesis,'+');
                                            if($buscar){
                                                $numero = explode('+',$parentesis);
                                                $new_format = str_pad((string)$new_number,strlen($numero[0]), "0", STR_PAD_LEFT);
                                            }else{
                                                $new_format = str_pad((string)$new_number,strlen($parentesis), "0", STR_PAD_LEFT);
                                            }

                                            $next_invoice = $parte1[0] . $new_format;

                                        ?>
                                        <?= lang('invoice_format', 'invoice_format'). ' - Siguiente N° de Factura: '. $next_invoice; ?>
                                        <?= form_input('invoice_format', $local->invoice_format, 'class="form-control tip" id="invoice_format"'); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-group">
                                        <?php

                                            if($local->bill_format){

                                                $new_number = (int)$local->bill_number + 1;
                                                $bill_format = $local->bill_format;

                                            } else {

                                                $new_number = (int)$settings->bill_number + 1;
                                                $bill_format = $settings->bill_format;

                                            }

                                            $parte1=explode('{',$bill_format);
                                            $parte2=explode('}',$parte1[1]);

                                            $parentesis= $parte2[0];

                                            $buscar = strpos($parentesis,'+');
                                            if($buscar){
                                                $numero = explode('+',$parentesis);
                                                $new_format = str_pad((string)$new_number,strlen($numero[0]), "0", STR_PAD_LEFT);
                                            }else{
                                                $new_format = str_pad((string)$new_number,strlen($parentesis), "0", STR_PAD_LEFT);
                                            }

                                            $next_bill = $parte1[0] . $new_format;
                                        ?>
                                        <?= lang('bill_format', 'bill_format'). ' - Siguiente N° de Boleta: '. $next_bill; ?>
                                        <?= form_input('bill_format', $local->bill_format, 'class="form-control tip" id="bill_format"'); ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <br>

                        <div class="form-group">
                            <?= form_submit('edit_local', lang('edit_local'), 'class="btn btn-primary"'); ?>
                        </div>

                        <?= form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
