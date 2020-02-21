<script>
    $(document).ready(function () {
        $('#GCData').dataTable({
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
            "aaSorting": [[ 1, "desc" ]], "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('summaries/get_invoices') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"mRender":hrsd}, null, null, null, null, null]
        });
    });

// <!-- {"mRender":hrsd} -->
</script>


<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('list_results'); ?></h3>
                </div>
                <div class="box-body">

                    <div class="table-responsive">
                    <table id="GCData" class="table table-bordered table-hover table-striped table-condensed">
                        <thead>
                        <tr>
                            <th class="col-sm-1"><?= lang("issue_date"); ?></th>
                            <th class="col-sm-1"><?= lang("type"); ?></th>
                            <th class="col-sm-2"><?= lang("file_name"); ?></th>
                            <th class="col-sm-1"><?= lang("status"); ?></th>
                            <th class="col-sm-1"><?= lang("created_by"); ?></th>
                            <th class="col-sm-1"><?= lang("observations"); ?></th>
                            <!-- <th style="width:65px;"><?php echo $this->lang->line("actions"); ?></th> -->
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
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
