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
                        <?= form_open_multipart("locals/add", 'class="validation"');?>
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <?= lang('code', 'code'); ?>
                                    <?= form_input('code', set_value('code'), 'class="form-control tip" id="code"  required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('name', 'name'); ?>
                                    <?= form_input('name', set_value('name'), 'class="form-control tip" id="name"  required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('address_name', 'address_name'); ?>
                                    <?= form_input('address', set_value('address'), 'class="form-control tip" id="address"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('cod_sunat', 'cod_sunat'); ?>
                                    <?= form_input('cod_sunat', set_value('cod_sunat', '0001'), 'class="form-control tip" id="cod_sunat"  required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang("default_warehouse", 'default_warehouse'); ?>
                                    <?php
                                    $wh[""] = lang("none2");
                                    foreach ($warehouses as $warehouse) {
                                        $wh[$warehouse->id] = $warehouse->warehouse;
                                    }
                                    echo form_dropdown('default_warehouse', $wh, "", 'class="form-control select2" style="width:100%;" id="default_warehouse"'); ?>
                                </div>

                                <div class="form-group">
                                    <div class="form-group">
                                        <?= lang('invoice_format', 'invoice_format'). ' - Ejemplo: '. $settings->invoice_format;  ?>
                                        <?= form_input('invoice_format', set_value('invoice_format'), 'class="form-control tip" id="invoice_format"'); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-group">
                                        <?= lang('bill_format', 'bill_format'). ' - Ejemplo: '. $settings->bill_format; ?>
                                        <?= form_input('bill_format', set_value('bill_format'), 'class="form-control tip" id="bill_format"'); ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <br>

                        <div class="form-group">
                            <?= form_submit('add_local', lang('add_local'), 'class="btn btn-primary"'); ?>
                        </div>

                        <?= form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
