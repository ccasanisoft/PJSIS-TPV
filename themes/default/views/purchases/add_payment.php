<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_payment'); ?></h4>
        </div>

        <?php $attrib = array('id' => 'add-payment-form'); ?>
        <?= form_open_multipart("purchases/add_payment/" . $inv->id . "/" . $inv->supplier_id, $attrib); ?>
        <div class="modal-body">
            <div class="alert alert-danger alert-dismissable" id="divError" style="display: none;">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <span id="spanError"></span>
            </div>
            <!-- <p><?= lang('enter_info'); ?></p> -->


            <div class="row">
                <?php if ($Admin) { ?>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= lang("date", "date"); ?>
                        <?= form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i')), 'class="form-control datetimepicker" id="date" required="required"'); ?>
                    </div>
                </div>
                <?php
            } ?>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= lang("reference", "reference"); ?>
                        <?= form_input('reference', set_value('reference'), 'class="form-control tip" id="reference"'); ?>
                    </div>
                </div>

                <input type="hidden" value="<?php echo $inv->id; ?>" name="purchase_id"/>

            </div>

<input type="hidden" name="status" id="status" value="<?php echo $inv->status; ?>">

            <div class="clearfix"></div>
            <div id="payments">
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
                                    <?= isset($Settings->stripe) ? '<option value="stripe">' . lang("stripe") . '</option>' : ''; ?>
                                    <option value="other"><?= lang("other"); ?></option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="clearfix"></div>

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
<input type="hidden" id="redirect" value="<?php echo $_SERVER["HTTP_REFERER"]; ?>">

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

        $(document).on('change', '.paid_by', function () {
            var p_val = $(this).val();
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
            //url: base_url+'purchases/add_payment/',
            url: base_url+ "purchases/add_payment/<?php echo $inv->id ?>/<?php echo $inv->supplier_id ?>" ,
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
                        location.href= $("#redirect").val();
                    }
                }

            },
              error: function (xhr, ajaxOptions, thrownError) {
                console.log("Error");
                return false
              }
        });
        return false;
    });

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
