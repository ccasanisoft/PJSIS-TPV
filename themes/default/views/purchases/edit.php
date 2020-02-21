<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<style>
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
        border: #eae9e9;
    }
    .table>tbody>tr.active>td, .table>tbody>tr.active>th, .table>tbody>tr>td.active, .table>tbody>tr>th.active, .table>tfoot>tr.active>td, .table>tfoot>tr.active>th, .table>tfoot>tr>td.active, .table>tfoot>tr>th.active, .table>thead>tr.active>td, .table>thead>tr.active>th, .table>thead>tr>td.active, .table>thead>tr>th.active {
        background-color: #eae9e9;
    }
    .table {
        margin-bottom: 10px;
    }
</style>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('update_info'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <?php echo form_open_multipart("purchases/edit/".$purchase->id, 'class="validation edit-po-form"'); ?>

                        <div class="alert alert-warning alert-dismissable" style="display: none" id="warning">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-warning"></i> <?= lang('warning'); ?></h4>
                            <p id="warning_text"></p>
                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('supplier', 'supplier'); ?>
                                    <?php
                                    $sp[''] = lang("select")." ".lang("supplier");
                                    foreach($suppliers as $supplier) {
                                        $sp[$supplier->id] = $supplier->name;
                                    }
                                    ?>
                                    <?= form_dropdown('supplier', $sp, set_value('supplier', $purchase->supplier_id ), 'class="form-control select2 tip" id="supplier"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('reference', 'reference'); ?>
                                    <?= form_input('reference', $purchase->reference, 'class="form-control tip" id="reference"'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('issue_date_2', 'issue_date_2'); ?>
                                    <?= form_input('date', set_value('date', $purchase->date), 'class="form-control datetimepicker tip" id="date"  required="required"'); ?>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('currency_name', 'currency_name'); ?>
                                    <?php
                                        $cy['PEN'] = lang('currency_pen');
                                        $cy['USD'] = lang('currency_usd');
                                    ?>
                                    <?= form_dropdown('currency_code', $cy, set_value('currency_code', $purchase->currency), 'class="form-control select2" id="currency_code"  required="required"'); ?>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('description', 'description'); ?>
                                    <?= form_input('description', set_value('description', $purchase->description), 'class="form-control tip" id="description"'); ?>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('expiration_date', 'expiration_date'); ?>
                                    <?= form_input('expiration_date', set_value('expiration_date', $purchase->expiration_date), 'class="form-control datepicker tip" id="expiration_date"'); ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                        <?= lang('warehouse_s', 'warehouse_s'); ?>
                                        <?php
                                        $sw[''] = lang("select")." ".lang("warehouse_s");
                                        foreach($warehouses as $ware) {
                                            $sw[$ware->id] = $ware->warehouse;
                                        }
                                        ?>
                                        <?= form_dropdown('warehouse', $sw, set_value('warehouse', $purchase->warehouse_id ), 'class="form-control select2 warehouse" id="warehouse"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>

                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('received', 'received'); ?>
                                    <?php $sts = array(1 => lang('received'), 0 => lang('not_received_yet')); ?>
                                    <?= form_dropdown('received', $sts, set_value('received'), 'class="form-control select2 tip" id="received"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div> -->
                        </div>

                        <!-- <hr> -->
                        &nbsp;

                        <div style="border-top: 1px solid #337ab7; border-bottom: 1px solid #337ab7; border-left: 1px solid #337ab7; border-right: 1px solid #337ab7; padding-bottom: 10px">
                        &nbsp;

                            <div style="padding-left:10px; padding-right:10px" class="form-group">
                                <input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>" id="add_item" class="form-control">
                            </div>

                            <div style="padding-left:10px; padding-right:10px" class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="poTable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr class="active">
                                                    <th><?= lang('product'); ?></th>
                                                    <th class="col-xs-2"><?= lang('quantity'); ?></th>
                                                    <th class="col-xs-2"><?= lang('unit_cost'); ?></th>
                                                    <th class="col-xs-2"><?= lang('subtotal'); ?></th>
                                                    <th style="width:25px;"><i class="fa fa-trash-o"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr class="active">
                                                    <th><?= lang('total'); ?></th>
                                                    <th class="col-xs-2"></th>
                                                    <th class="col-xs-2"></th>
                                                    <th class="col-xs-2 text-right"><span id="gtotal">0.00</span></th>
                                                    <th style="width:25px;"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right" style="padding-left:10px; padding-right:10px">
                                <table class="table table-condensed">
                                    <tbody>
                                        <tr>
                                            <td style="border: none;"><?= strtoupper(lang('opgrav').':'); ?></td>
                                            <td style="border: none;" class="col-xs-2"><span id="gaffected">0.00</span></td>
                                            <th style="border: none; width:25px;"></th>
                                        </tr>
                                        <tr>
                                            <td style="border: none;"><?= strtoupper(lang('opexon').':'); ?></td>
                                            <td style="border: none;" class="col-xs-2"><span id="gexonerated">0.00</span></td>
                                            <th style="border: none; width:25px;"></th>
                                        </tr>
                                        <tr>
                                            <td style="border: none;"><?= strtoupper(lang('product_tax'). " - " . $Settings->default_tax_rate . '%:'); ?></td>
                                            <td style="border: none;" class="col-xs-2"><span id="gtax">0.00</span></td>
                                            <th style="border: none; width:25px;"></th>
                                        </tr>
                                        <tr class="active">
                                            <td style="border: none"><strong><?= strtoupper(lang('importe') . " " . lang('total').':'); ?></strong></td>
                                            <td style="border: none;" class="col-xs-2"><strong><span id="gimptotal">0.00</span></strong></td>
                                            <th style="border: none; width:25px;"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        &nbsp;

                        <div class="form-group">
                            <?= lang('attachment', 'attachment'); ?>
                            <input type="file" name="userfile" class="form-control tip" id="attachment">
                        </div>
                        <div class="form-group">
                            <?= lang("note", 'note'); ?>
                            <?= form_textarea('note', $purchase->note, 'class="form-control redactor" id="note"'); ?>
                        </div>
                        <div class="form-group">
                            <?= form_submit('edit_purchase', lang('edit_purchase'), 'class="btn btn-primary" id="edit_purchase"'); ?>
                            <button type="button" id="reset" class="btn btn-danger"><?= lang('reset'); ?></button>
                        </div>

                        <?php echo form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<input type="hidden" id="fflag" value="0">
<input type="hidden" id="exchange" value="<?= $Exchange->sell ?>">
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
    });
</script>
<script type="text/javascript">
    var spoitems = {};
    var lang = new Array();
    lang['code_error'] = '<?= lang('code_error'); ?>';
    lang['r_u_sure'] = '<?= lang('r_u_sure'); ?>';
    lang['no_match_found'] = '<?= lang('no_match_found'); ?>';
    lang['products_not_found'] = '<?= lang('products_not_found') ?>';
    lang['sale_not_less'] = '<?= lang('sale_not_less') ?>';

    $(document).ready(function() {
       store('spoitems', JSON.stringify(<?= $items; ?>));
    });
    // $(window).bind('beforeunload', function (e) {
    //     localStorage.setItem('remove_spo', true);
    //     var message = "You will loss data!";
    //     return message;

    // });
    // $('#reset').click(function (e) {
    //     $(window).unbind('beforeunload');
    // });
    // $('#edit_purchase').click(function () {
    //     $(window).unbind('beforeunload');
    //     $('form.edit-po-form').submit();
    // });
</script>
<script src="<?= $assets ?>dist/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/pages/purchases.js" type="text/javascript"></script>