<script>
    $(document).ready(function () {
        $('#GCData').dataTable({
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
            "aaSorting": [[ 1, "desc" ]], "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('summaries/get_summaries') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			//************TRJ042 - ALEXANDER ROCA - 30/04/2019************	
            "aoColumns": [{"mRender":hrsd}, {"mRender":hrsd}, null, {"mRender":hrsd}, null, null, null,null,null, null, {"bSortable":false, "bSearchable": false}]
			//************TRJ042 - ALEXANDER ROCA - 30/04/2019************	
		});
    });
    
// <!-- {"mRender":hrsd} -->
</script>
<!--*****************************TRJ071 - KENY PONTE - 03/12/2019*****************-->
 <script>
   $(document).ready(function(){
        $("button").click(function(){

            $.ajax({
                type: 'GET',
                url: '<?= site_url('Summaries/ConsultTicket'); ?>',
                success: function(data) {
                    //console.log(data);
                    //document.write(data);
                  location.reload();
                }
            });
   });
});
</script>
<!--*****************************TRJ071 - KENY PONTE - 03/12/2019*****************-->

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('list_results'); ?></h3>
                </div>
                <!--*****************************TRJ071 - KENY PONTE - 03/12/2019*****************-->
                <div class="box-header">
				    <button type="button" class="btn btn-info btn-sm"><i class="fa fa-ticket"></i>Consulta Ticket</button>
                </div>
                <!--*****************************TRJ071 - KENY PONTE - 03/12/2019*****************-->
                <div class="box-body">

                    <div class="table-responsive">
                    <table id="GCData" class="table table-bordered table-hover table-striped table-condensed">
                        <thead>
                        <tr>
                            <th class="col-sm-1"><?= lang("issue_date"); ?></th>
                            <th class="col-sm-1"><?= lang("processed_date"); ?></th>
                            <th class="col-sm-1"><?= lang("type"); ?></th>
                            <th class="col-sm-1"><?= lang("reference_date_sale"); ?></th>
                            <th class="col-sm-2"><?= lang("file_name"); ?></th>
                            <th class="col-sm-1"><?= lang("status"); ?></th>
                            <th class="col-sm-1"><?= lang("ticket"); ?></th>   <!--//*****Renato TRJ023 25/04/2019   ********-->
                            <th class="col-sm-1"><?= lang("ticket_status"); ?> </th> <!--//*****TRJ071 - KENY PONTE - 03/12/2019   ********-->
                            <th class="col-sm-1"><?= lang("created_by"); ?></th>
                            <th class="col-sm-1"><?= lang("observations"); ?></th>

                            <th style="width:65px;"><?php echo $this->lang->line("actions"); ?></th><!--*****TRJ042 - ALEXANDER ROCA - 30/04/2019****-->
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
