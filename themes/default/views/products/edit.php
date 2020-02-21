<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('update_info'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <?= form_open_multipart("products/edit/".$product->id, 'class="validation"');?>
                        <div class="row">
                            <div class="col-md-6">
                            <div class="form-group">
                                <?= lang('type', 'type'); ?>
                                <?php $opts = array('standard' => lang('standard'), 'combo' => lang('combo'), 'service' => lang('service')); ?>
                                <?= form_dropdown('type', $opts, set_value('type', $product->type), 'class="form-control tip select2" id="type"  required="required" style="width:100%;"'); ?>
                            </div>
                                <div class="form-group">
                                    <?= lang('name', 'name'); ?>
                                    <?= form_input('name', $product->name, 'class="form-control tip" id="name"  required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('code', 'code'); ?> <?= lang('can_use_barcode'); ?>
                                    <?= form_input('code', $product->code, 'class="form-control tip" id="code"  required="required"'); ?>
                                </div>
                                <div class="form-group all">
                                    <?= lang("barcode_symbology", "barcode_symbology") ?>
                                    <?php
                                    $bs = array('code25' => 'Code25', 'code39' => 'Code39', 'code128' => 'Code128', 'ean8' => 'EAN8', 'ean13' => 'EAN13', 'upca ' => 'UPC-A', 'upce' => 'UPC-E');
                                    echo form_dropdown('barcode_symbology', $bs, set_value('barcode_symbology', $product->barcode_symbology), 'class="form-control select2" id="barcode_symbology" required="required" style="width:100%;"');
                                    ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('category', 'category'); ?>
                                    <?php

                                    $cadd= '<select name="category" class="form-control select2 tip" id="category"  required="required" style="width:100%;">';
                                    $cadd.= '<option value="" >'.lang("select")." ".lang("category").'</option>';
                                    foreach($categoriesOrdered as $categoryL) {
                                        $sel1 = "";
                                        $sel2 = "";
                                        $sel3 = "";
                                        $sel4 = "";

                                        if($categoryL->id == $product->category_id){$sel1 = " selected "; }

                                        $cadd.= "<option $sel1 value='".$categoryL->id."'>".$categoryL->name."</option>";
                                        if(isset($categoryL->hijos)){
                                            foreach($categoryL->hijos as $hijos1) {
                                                if($hijos1->id == $product->category_id){$sel2 = " selected "; }
                                                $cadd.= "<option $sel2 value='".$hijos1->id."'>&nbsp;&nbsp;&nbsp;&nbsp;".$hijos1->name."</option>";
                                                $sel2 = "";
                                                if(isset($hijos1->hijos)){
                                                    foreach($hijos1->hijos as $hijos2) {
                                                        if($hijos2->id == $product->category_id){$sel3 = " selected "; }
                                                        $cadd.= "<option $sel3 value='".$hijos2->id."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$hijos2->name."</option>";
                                                        $sel3 = "";
                                                        if(isset($hijos2->hijos)){
                                                            foreach($hijos2->hijos as $hijos3) {
                                                                if($hijos3->id == $product->category_id){ $sel4 = " selected "; }

                                                                $cadd.= "<option $sel4 value='".$hijos3->id."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$hijos3->name."</option>";
                                                                //$sel4 = "";
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $cadd.'</select>';
                                    ?>
                                    <?= $cadd;//exit;?>
                                    <input type="hidden" name="temp" style="display: none;">
                                </div>

                                <!-------------------- -->
                                <div class="form-group all">
                                    <div class="form-group">

                                        <?php switch($this->session->userdata('negocio')){
                                            case 0:
                                                $l_maker = lang("maker");
                                                $l_maker_b = lang('maker','maker');
                                                break;
                                            case 1:
                                                $l_maker = lang("laboratory");
                                                $l_maker_b = lang('laboratory', 'laboratory');
                                                break;
                                        } ?>

                                        <?= $l_maker_b; ?>
                                        <?php
                                        $sp[''] = lang("select")." ".$l_maker;
                                        foreach($mkrs as $mk) {
                                            $sp[$mk->id] = $mk->maker;
                                        }
                                        ?>
                                        <?= form_dropdown('maker', $sp, set_value('maker', $product->maker_id ), 'class="form-control select2 maker" id="maker" style="width:100%;"'); ?>
                                    </div>
                                </div>

                                <?php switch($this->session->userdata('negocio')){
                                    case 0: ?>
                                        <?php break;
                                    case 1: ?>
                                        <div class="form-group" id="st">
                                            <?= lang('active_principle', 'active_principle'); ?>
                                            <?= form_input('custom_field_1', set_value('custom_field_1',  $product->custom_field_1), 'class="form-control tip" id="custom_field_1"'); ?>
                                        </div>
                                        <div class="form-group" id="st">
                                            <?= lang('pharmacological_action', 'pharmacological_action'); ?>
                                            <?= form_input('custom_field_2', set_value('custom_field_2',  $product->custom_field_2), 'class="form-control tip" id="custom_field_2"'); ?>
                                        </div>
                                        <?php break;
                                } ?>

                                <div class="form-group" id="st">
                                    <?= lang('presentation', 'presentation'); ?>
                                    <?= form_input('presentation', set_value('presentation', $product->presentation), 'class="form-control tip" id="presentation"'); ?>
                                </div>

                                <!-- <div class="form-group" id="st">
                                    <?= lang('quantity', 'quantity'); ?>
                                    <?= form_input('quantity', set_value('quantity', $product->quantity), 'class="form-control tip" id="quantity"  required="required"'); ?>
                                </div> -->

                                <div class="form-group" id="st">
                                    <?= lang('alert_quantity', 'alert_quantity'); ?>
                                    <?= form_input('alert_quantity', set_value('alert_quantity', $product->alert_quantity), 'class="form-control tip" id="alert_quantity"  required="required"'); ?>
                                </div>



                                <div class="form-group">
                                    <?= lang('image', 'image'); ?>
                                    <input type="file" name="userfile" id="image">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="ct" style="display:none;">

                                    <div class="form-group">
                                        <?= lang("add_product", "add_item"); ?>
                                        <?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line("add_item") . '"'); ?>
                                    </div>
                                    <div class="control-group table-group">
                                        <label class="table-label" for="combo"><?= lang("combo_products"); ?></label>

                                        <div class="controls table-controls">
                                            <table id="prTable"
                                                   class="table items table-striped table-bordered table-condensed table-hover">
                                                <thead>
                                                <tr>
                                                    <th class="col-xs-9"><?= lang("product_name") . " (" . $this->lang->line("product_code") . ")"; ?></th>
                                                    <th class="col-xs-2"><?= lang("quantity"); ?></th>
                                                    <th class=" col-xs-1 text-center"><i class="fa fa-trash-o trash-opacity-50"></i></th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot></tfoot>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('currency_name', 'currency_name'); ?>
                                    <?php $cy = array('PEN' => lang('currency_pen'), 'USD' => lang('currency_usd')); ?>
                                    <?= form_dropdown('currency', $cy, set_value('currency', $product->currency), 'class="form-control tip select2" id="currency"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('cost', 'cost'); ?>
                                    <?= form_input('cost', $product->cost, 'class="form-control tip" id="cost"  required="required"'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('price', 'price'); ?>
                                    <?= form_input('price', $product->price, 'class="form-control tip" id="price"  required="required"'); ?>
                                </div>
                            </div>

                                <!-- <div class="form-group">
                                    <?= lang('product_tax', 'product_tax'); ?>
                                    <?= form_input('product_tax', $product->tax, 'class="form-control tip" id="product_tax"  required="required"'); ?>
                                </div> -->

                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('tax_method', 'tax_method'); ?>
                                    <?php $tm = array(0 => lang('inclusive'), 1 => lang('exclusive'), 2 => lang('exonerated'), 3 => lang('exo')); ?><!--*******************TRJ058 - ALEXANDER ROCA - 20/06/2019*******************-->
                                    <?= form_dropdown('tax_method', $tm, set_value('tax_method', $product->tax_method), 'class="form-control tip select2" id="tax_method"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <?= lang('details', 'details'); ?>
                            <?= form_textarea('details', $product->details, 'class="form-control tip redactor" id="details"'); ?>
                        </div>

                        <div class="form-group">
                            <?= form_submit('edit_product', lang('edit_product'), 'class="btn btn-primary"'); ?>
                        </div>
                        <?= form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= $assets ?>dist/js/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
    var price = 0; cost = 0; items = {};
    $(document).ready(function() {
        $('#type').change(function(e) {
            var type = $(this).val();
            if(type == 'combo') {
                // $('#st').slideUp();
                $('#ct').slideDown();
                //$('#cost').attr('readonly', true);
            } else if(type == 'service') {
                // $('#st').slideUp();
                $('#ct').slideUp();
                //$('#cost').attr('readonly', false);
            } else {
                $('#ct').slideUp();
                // $('#st').slideDown();
                //$('#cost').attr('readonly', false);
            }
        });

        $("#add_item").autocomplete({
            source: '<?= site_url('products/suggestions'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 200,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_product_item(ui.item);
                    if (row) {
                        $(this).val('');
                    }
                } else {
                    bootbox.alert('<?= lang('no_product_found') ?>');
                }
            }
        });
        $('#add_item').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                $(this).autocomplete("search");
            }
        });

        $(document).on('click', '.del', function () {
            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();
        });


        $(document).on('change', '.rqty', function () {
            var item_id = $(this).attr('data-item');
            items[item_id].row.qty = (parseFloat($(this).val())).toFixed(2);
            add_product_item(null, 1);
        });

        $(document).on('change', '.rprice', function () {
            var item_id = $(this).attr('data-item');
            items[item_id].row.price = (parseFloat($(this).val())).toFixed(2);
            add_product_item(null, 1);
        });

        function add_product_item(item, noitem) {
            if (item == null && noitem == null) {
                return false;
            }
            if(noitem != 1) {
                item_id = item.row.id;
                if (items[item_id]) {
                    items[item_id].row.qty = (parseFloat(items[item_id].row.qty) + 1).toFixed(2);
                } else {
                    items[item_id] = item;
                }
            }
            price = 0;
            cost = 0;

            $("#prTable tbody").empty();
            $.each(items, function () {
                var item = this.row;
                var row_no = item.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + item.id + '"></tr>');
                tr_html = '<td><input name="combo_item_code[]" type="hidden" value="' + item.code + '"><span id="name_' + row_no + '">' + item.name + ' (' + item.code + ')</span></td>';
                tr_html += '<td><input class="form-control text-center rqty" name="combo_item_quantity[]" type="text" value="' + formatDecimal(item.qty) + '" data-id="' + row_no + '" data-item="' + item.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                //tr_html += '<td><input class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(item.price) + '" data-id="' + row_no + '" data-item="' + item.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#prTable");
                //price += formatDecimal(item.price*item.qty);
                cost += formatDecimal(item.cost*item.qty);
            });
            $('#cost').val(cost);
            return true;
        }
        var type = $('#type').val();
        if(type == 'combo') {
            $('#st').slideUp();
            $('#ct').slideDown();
            //$('#cost').attr('readonly', true);
        } else if(type == 'service') {
            $('#st').slideUp();
            $('#ct').slideUp();
            //$('#cost').attr('readonly', false);
        } else {
            $('#ct').slideUp();
            $('#st').slideDown();
            //$('#cost').attr('readonly', false);
        }
        <?php
        if($this->input->post('type') == 'combo') {
            $c = sizeof($_POST['combo_item_code']);
            $items = array();
            for ($r = 0; $r <= $c; $r++) {
                if(isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                    $items[] = array('id' => $_POST['combo_item_id'][$r], 'row' => array('id' => $_POST['combo_item_id'][$r], 'name' => $_POST['combo_item_name'][$r], 'code' => $_POST['combo_item_code'][$r], 'qty' => $_POST['combo_item_quantity'][$r], 'cost' => $_POST['combo_item_cost'][$r]));
                }
            }
            echo '
            var ci = '.json_encode($items).';
            $.each(ci, function() { add_product_item(this); });
            ';
        } elseif(!empty($items)) {
            echo '
            var ci = '.json_encode($items).';
            $.each(ci, function() { add_product_item(this); });
            ';
        }
        ?>
    });




</script>
