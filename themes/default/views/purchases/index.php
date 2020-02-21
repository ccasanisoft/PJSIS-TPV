<script>
    $(document).ready(function() {
        if (get('remove_spo')) {
            if (get('spoitems')) {
                remove('spoitems');
            }
            remove('remove_spo');
        }
        <?php if($this->session->userdata('remove_spo')) { ?>
        if (get('spoitems')) {
            remove('spoitems');
        }
        <?php $this->tec->unset_data('remove_spo'); } ?>
        function attach(x) {
            if(x !== null) {
                return '<a href="<?=base_url();?>uploads/'+x+'" target="_blank" class="btn btn-primary btn-block btn-xs"><i class="fa fa-chain"></i></a>';
            }
            return '';
        }
        $('#purData').dataTable({
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
            "aaSorting": [[ 0, "desc" ]], "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('purchases/get_purchases') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"mRender":hrsd}, null, {"mRender":hrsd}, null, null, {"mRender":method}, {"mRender":currencyFormat}, {"mRender":currencyFormat}, {"mRender":currencyFormat},  {"mRender":currencyFormat}, null,{"bSortable":false, "bSearchable": false}]
            // null,
        });

        function method(n) {
            return (n == "PEN") ? '<center><span class="label label-primary">PEN</span></center>' : '<center><span class="label label-success">USD</span></center>';
        }

    });
</script>
<style type="text/css">.table td:nth-child(3) { text-align: right; }</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('list_results'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="purData" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
                            <thead>
                            <tr class="active">
                                <!-- <th class="col-xs-1"><?= lang('code'); ?></th> -->
                                <th class="col-xs-1"><?= lang('issue_date_2'); ?></th>
                                <th class="col-xs-1"><?= lang('reference'); ?></th>
                                <th class="col-xs-1"><?= lang('expiration_date'); ?></th>
                                <th class="col-xs-1"><?= lang('description'); ?></th>
                                <th class="col-xs-2"><?= lang('supplier'); ?></th>
                                <th style="text-align:center; width: 70px;"><?php echo $this->lang->line("currency_name"); ?></th>
                                <th class="col-xs-1"><?= lang('total'); ?></th>
                                <th class="col-xs-1"><?= lang('tax'); ?></th>
                                <th class="col-xs-1"><?= lang('grand_total'); ?></th>
                                <th class="col-xs-1"><?= lang("paid"); ?></th>
                                <th class="col-xs-1"><?= lang("status"); ?></th>
                                <!-- <th><?= lang('note'); ?></th> -->
                                <!-- <th style="width:25px; padding-right:5px;"><i class="fa fa-chain"></i></th> -->
                                <!-- <th class="col-xs-2"><?= lang('warehouse_s'); ?></th> -->
                                <!-- <th style="width:75px;"><?= lang('actions'); ?></th> -->
                                <th class="col-xs-2" style="text-align:center;"><?php echo $this->lang->line("actions"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
