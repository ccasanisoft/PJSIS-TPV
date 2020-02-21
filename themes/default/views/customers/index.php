<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?><!DOCTYPE html>
    <input type="hidden" id="negocio" name="negocio" value="<?php echo $_SESSION["negocio"];?>">

<script>
    $(document).ready(function () {

        var negocio = $("#negocio").val();
        var columnas;

        switch(negocio){
            case "0":
                columnas = [null, null, null,null,null, null, null, null, {"bSortable":false, "bSearchable": false}]; 
                break;
            case "1":
                columnas = [null, null, null,null,null,null, null, null, {"bSortable":false, "bSearchable": false}];
                break;
        }

        $('#CuData').dataTable({
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
            "aaSorting": [[ 0, "asc" ]], "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('customers/get_customers') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": columnas
            // [null, null, null, null, null, null, null, {"bSortable":false, "bSearchable": false}]
        });
    });
    
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
                    <table id="CuData" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line("name"); ?></th>
                            <th><?php echo $this->lang->line("type_person"); ?></th><!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->
                            <th><?php echo $this->lang->line("document_type"); ?></th><!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->
                            <th><?php echo $this->lang->line("phone"); ?></th>
                            <th><?php echo $this->lang->line("email_address"); ?></th>
                            <th><?php echo $this->lang->line("ccf1"); ?></th>
                            <th><?php echo $this->lang->line("ccf2"); ?></th>
                            <th><?php echo $this->lang->line("customers_type"); ?></th>
                            <?php switch($this->session->userdata('negocio')){
                                case 0: ?>
                                    <?php break;
                                case 1: ?>
                                    <th><?php echo $this->lang->line("digemid"); ?></th> 
                                    <?php break;
                            } ?> 
                            <th style="width:65px;"><?php echo $this->lang->line("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
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
