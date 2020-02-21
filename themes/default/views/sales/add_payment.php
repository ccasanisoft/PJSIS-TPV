<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_payment'); ?></h4>
        </div>

        <?php $attrib = array('id' => 'add-payment-form');
    //echo form_open_multipart("pos/open_register", $attrib); ?>
        <?= form_open_multipart("sales/add_payment/" . $inv->id."/".$inv->customer_id, $attrib); ?>
        <div class="modal-body">
            <div class="alert alert-danger alert-dismissable" id="divError" style="display: none;">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <span id="spanError"></span>
            </div>
            <!--<div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                                <tbody>
                                    <tr>
                                        <td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_items"); ?></td>
                                        <td width="25%" class="text-right"><span id="item_count">0.00</span></td>
                                        <td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_payable"); ?></td>
                                        <td width="25%" class="text-right"><span id="twt">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td style="border-right-color: #FFF !important;"><?= lang("total_paying"); ?></td>
                                        <td class="text-right"><span id="total_paying">0.00</span></td>
                                        <td style="border-right-color: #FFF !important;"><?= lang("balance"); ?></td>
                                        <td class="text-right"><span id="balance">0.00</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>
            </div>-->
            <!-- <p><?= lang('enter_info'); ?></p> -->


            <div class="row">
                <?php if ($Admin) { ?>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= lang("date", "date"); ?>
                        <?= form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i')), 'class="form-control datetimepicker" id="date" required="required"'); ?>
                    </div>
                </div>
                <?php } ?>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= lang("reference", "reference"); ?>
                        <?= form_input('reference', set_value('reference'), 'class="form-control tip" id="reference"'); ?>
                    </div>
                </div>

                <input type="hidden" value="<?php echo $inv->id; ?>" name="sale_id"/>

            </div>

            <?php if($inv->invoice_id==NULL){ //$inv->status=="DEBE" && ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?= lang('note', 'note'); ?>
                                    <textarea name="note" id="note" class="pa form-control kb-text"><?php echo $inv->note; ?></textarea>
                                    <input type="hidden" name="spos_note" value="" id="spos_note" value="<?php echo $inv->note; ?>"/>
                                </div>
                            </div>
                        </div>
                        <!--
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <?= lang('cmp','cmp'); ?>
                                    <input type="text" id="cmp" name="cmp" maxlength="6" class="form-control cmp kb-pad" value="<?php echo $inv->cmp; ?>"></>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <?= lang('doctor','doctor'); ?>
                                    <input type="text" id="doctor" name="doctor"  class="form-control doctor kb-text" value="<?php echo $inv->doctor; ?>"></>
                                </div>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <?= lang("document_type", "document_type"); ?>
                                    <select id="document_type" name="document_type" class="form-control document_type select2" style="width:100%;">
                                        <option value="0"><?= lang("select"); ?></option>
                                        <option value="1" <?php if($inv->document_type==1){echo " selected ";} ?> ><?= lang("bill_type"); ?></option>
                                        <option value="2" <?php if($inv->document_type==2){echo " selected ";} ?>><?= lang("invoice"); ?></option>
                                        <option value="3" <?php if($inv->document_type==3){echo " selected ";} ?>><?= lang("nventa"); ?></option> <!--//*****Renato TRJ023 25/04/2019   ********-->
                                    </select>
                                </div>
                            </div>
                        </div>
<?php }?>

<input type="hidden" name="status" id="status" value="<?php echo $inv->status;?>">
<input type="hidden" name="invoice" id="invoice" value="<?php echo $inv->invoice_id; ?>">

            <div class="clearfix"></div>
            <div id="payments">
            <!-- ($inv->grand_total - $inv->paid) > 0 ? $this->tec->formatDecimal($inv->grand_total - $inv->paid) -->
            <!-- $inv->paid == 0 ? $this->tec->formatMoney($inv->grand_total + $inv->rounding) : (($inv->grand_total - $inv->paid) > 0 ? $this->tec->formatMoney($inv->grand_total - $inv->paid) : 0) -->
                <div class="well well-sm well">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="payment">
                                    <div class="form-group">
                                        <?= lang("amount", "amount"); ?>
                                        <input name="amount-paid" type="text" id="amount"
                                        value="<?= ($inv->grand_total - $inv->paid) > 0 ? $this->tec->formatMoney($inv->grand_total - $inv->paid) : 0 ?>"
                                        class="pa form-control kb-pad amount" required="required"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <?= lang("paying_by", "paid_by"); ?>
                                    <select name="paid_by" id="paid_by" class="form-control paid_by select2" style="width:100%"
                                    required="required">
                                    <option value="cash"><?= lang("cash"); ?></option>
                                    <option value="CC"><?= lang("cc"); ?></option>
                                    <option value="Cheque"><?= lang("cheque"); ?></option>
                                    <option value="gift_card"><?= lang("gift_card"); ?></option>
                                    <?= isset($Settings->stripe) ? '<option value="stripe">' . lang("stripe") . '</option>' : ''; ?>
                                    <option value="other"><?= lang("other"); ?></option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group gc" style="display: none;">
                        <?= lang("gift_card_no", "gift_card_no"); ?>
                        <input name="gift_card_no" type="text" id="gift_card_no" class="pa form-control kb-pad"/>

                        <div id="gc_details"></div>
                    </div>

                    <!-- <div class="pcc" style="display:none;">
                        <div class="form-group">
                            <input type="text" id="swipe" class="form-control swipe swipe_input"
                            placeholder="<?= lang('focus_swipe_here') ?>"/>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input name="pcc_no" type="text" id="pcc_no" class="form-control"
                                    placeholder="<?= lang('cc_no') ?>"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">

                                    <input name="pcc_holder" type="text" id="pcc_holder" class="form-control"
                                    placeholder="<?= lang('cc_holder') ?>"/>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <select name="pcc_type" id="pcc_type" class="form-control pcc_type select2" style="width:100%"
                                    placeholder="<?= lang('card_type') ?>">
                                    <option value="Visa"><?= lang("Visa"); ?></option>
                                    <option value="MasterCard"><?= lang("MasterCard"); ?></option>
                                    <option value="Amex"><?= lang("Amex"); ?></option>
                                    <option value="Discover"><?= lang("Discover"); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input name="pcc_month" type="text" id="pcc_month" class="form-control"
                                placeholder="<?= lang('month') ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">

                                <input name="pcc_year" type="text" id="pcc_year" class="form-control"
                                placeholder="<?= lang('year') ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input name="pcc_ccv" type="text" id="pcc_cvv2" class="form-control" placeholder="<?= lang('cvv2') ?>" />
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="pcheque" style="display:none;">
                    <div class="form-group"><?= lang("cheque_no", "cheque_no"); ?>
                        <input name="cheque_no" type="text" id="cheque_no" class="form-control cheque_no"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

    </div>

    <div class="form-group">
        <?= lang("attachment", "attachment") ?>
        <input id="attachment" type="file" name="userfile" class="form-control file">
    </div>

    <div class="form-group">
        <?= lang("note", "note"); ?>
        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control redactor" id="note"'); ?>
    </div>

</div>
<div class="modal-footer">
    <?php echo form_submit('add_payment', lang('add_payment'), 'class="btn btn-primary" id="boton"'); ?>
</div>
</div>
<input type="hidden" id="redirect1" value="<?php echo $_SERVER["HTTP_REFERER"];?>">
<input type="hidden" id="redirect2" value="<?php echo "pos/view/";?>">

<?php echo form_close(); ?>
</div>

<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/parse-track-data.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/pages/modal.js" type="text/javascript"></script>
<script type="text/javascript" charset="UTF-8">
    $(document).on('change', '#note', function (e) {
        var n = $(this).val();
        store('spos_note', n);
        $('#spos_note').val(n);
    });
    $(document).ready(function () {

        $('#gift_card_no').inputmask("9999 9999 9999 9999");
        $(document).on('change', '.paid_by', function () {
            var p_val = $(this).val();
            if (p_val == 'gift_card') {
                $('.gc').slideDown();
                $('.ngc').slideUp('fast');
                setTimeout(function(){ $('#gift_card_no').focus(); }, 10);
                $('#amount').attr('readonly', true);
            } else {
                $('.ngc').slideDown();
                $('.gc').slideUp('fast');
                $('#gc_details').html('');
                $('#amount').attr('readonly', false);
            }
            if (p_val == 'cash' || p_val == 'other') {
                $('.pcash').slideDown();
                $('.pcheque').slideUp('fast');
                $('.pcc').slideUp('fast');
                setTimeout(function(){ $('#amount').focus(); }, 10);
            } else if (p_val == 'CC' || p_val == 'stripe') {
                $('.pcc').slideDown();
                $('.pcheque').slideUp('fast');
                $('.pcash').slideUp('fast');
                setTimeout(function(){ $('#swipe').val('').focus(); }, 10);
            } else if (p_val == 'Cheque') {
                $('.pcheque').slideDown();
                $('.pcc').slideUp('fast');
                $('.pcash').slideUp('fast');
                setTimeout(function(){ $('#cheque_no').focus(); }, 10);
            } else {
                $('.pcheque').hide();
                $('.pcc').hide();
                $('.pcash').hide();
            }
        });

        $(document).on('change', '#gift_card_no', function () {
            var cn = $(this).val() ? $(this).val() : '';
            if (cn != '') {
                $.ajax({
                    type: "get", async: false,
                    url: base_url + "pos/validate_gift_card/" + cn,
                    dataType: "json",
                    success: function (data) {
                        if (data === false) {
                            bootbox.alert('<?= lang('incorrect_gift_card'); ?>');
                        } else {
                            $('#gc_details').html('<?= lang('card_no'); ?>: ' + data.card_no + '<br><?= lang('value'); ?>: ' + data.value + '<?= lang('balance'); ?>: ' + data.balance);
                            var g_total = <?= $this->tec->formatDecimal($inv->grand_total - $inv->paid); ?>;
                            $('#amount').val((g_total > data.balance) ? data.balance : g_total).change().focus();
                        }
                    }
                });
            }
            return false;
        });

        $('.swipe').keypress( function (e) {
            var TrackData = $(this).val() ? $(this).val() : '';
            if(TrackData != '') {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    var p = new SwipeParserObj(TrackData);

                    if(p.hasTrack1)
                    {

                        var CardType = null;
                        var ccn1 = p.account.charAt(0);
                        if(ccn1 == 4)
                            CardType = 'Visa';
                        else if(ccn1 == 5)
                            CardType = 'MasterCard';
                        else if(ccn1 == 3)
                            CardType = 'Amex';
                        else if(ccn1 == 6)
                            CardType = 'Discover';
                        else
                            CardType = 'Visa';

                        $('#pcc_no').val(p.account).change();
                        $('#pcc_holder').val(p.account_name).change();
                        $('#pcc_month').val(p.exp_month).change();
                        $('#pcc_year').val(p.exp_year).change();
                        $('#pcc_cvv2').val('');
                        $('#pcc_type').select2('val', CardType);

                    } else {
                        $('#pcc_no').val('').change();
                        $('#pcc_holder').val('').change();
                        $('#pcc_month').val('').change();
                        $('#pcc_year').val('').change();
                        $('#pcc_cvv2').val('').change();
                        $('#pcc_type').val('').change();
                    }

                    $('#pcc_cvv2').focus();
                }
            }

        }).blur(function (e) {
            $(this).val('');
        }).focus( function (e) {
            $(this).val('');
        });

        $('#pcc_no').change(function (e) {
            var cn = $(this).val();
            var ccn1 = cn.charAt(0);
            if(ccn1 == 4)
                CardType = 'Visa';
            else if(ccn1 == 5)
                CardType = 'MasterCard';
            else if(ccn1 == 3)
                CardType = 'Amex';
            else if(ccn1 == 6)
                CardType = 'Discover';
            else
                CardType = 'Visa';

            $('#pcc_type').select2('val', CardType);
        });

    $("#add-payment-form").on("submit", function(e) {
        var boton = document.getElementById('boton');
        boton.disabled = true;
        e.preventDefault();

        $.ajax({
            type: "post",
            //url: base_url+'sales/add_payment/',
            url: base_url+ "sales/add_payment/<?php echo $inv->id?>/<?php echo $inv->customer_id?>" ,
            data: $( this ).serialize(),
            dataType: "json",
            success: function(res) {
               // return false;
                if(res.status=="failed"){
                    $("#divError").attr("style","display: block");
                    $("#spanError").html(res.msg);
                    $('#posModal').scrollTop(0);
                    //reference
                }else{
                    if(res.redirect==1){
                        location.href= $("#redirect1").val();

                    }
                    if(res.redirect==2){
                        location.href= $("#redirect2").val()+res.idd;
                    }
                }

            },
              error: function (xhr, ajaxOptions, thrownError) {// return false
                location.href= $("#redirect2").val() + "/<?php echo $inv->id;?>";
                console.log("Error");
                //alert("Error");
               // console.log(xhr.msg);
              }
        });
        return false;
    });

        /*$("#cmp").keyup(function() {
            $.ajax({
                url: base_url+'pos/search_cmp',
                type:'get',
                data: {cmp : $("#cmp").val()},
                dataType: 'json',
                success: function(response) {
                    if(response.name!=""){
                      $("#doctor").val(response.name);
                      $('#doctor_val').val(response.name);
                    }else{
                      $("#doctor").val("");
                      $('#doctor_val').val("");
                    }
                },
                error: function(xhr) {
                //Do Something to handle error
                }
            });
        });*/

    });
</script>

<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
    });
</script>
