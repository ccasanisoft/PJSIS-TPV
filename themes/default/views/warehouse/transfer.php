<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<script type="text/javascript">
    var dt = "";
    var _flag = 1;

    $(document).ready(function() {
        function image(n) {
            if(n !== null) {
                return '<div style="width:32px; margin: 0 auto;"><a href="<?=base_url();?>uploads/'+n+'" class="open-image"><img src="<?=base_url();?>uploads/thumbs/'+n+'" alt="" class="img-responsive"></a></div>';
            }
            return '';
        }
        function method(n) {
            return (n == 0) ? '<span class="label label-primary"><?= lang('inclusive'); ?></span>' : (n == 1) ? '<span class="label label-warning"><?= lang('exclusive'); ?></span>' : '<span class="label label-default"><?= lang('exonerated'); ?></span>';
        }
        function dibujarDt(){
                var _v1 = 0;
                var _v2 = 0;
                var _v3 = 0;


                if($("#warehouse").val()!=""){
                    _v1 = $("#warehouse").val();
                }
                if($("#warehouse2").val()!=""){
                    _v2 = $("#warehouse2").val();
                }
                if($("#date1").val()!=""){
                    _v3 = $("#date1").val();
                }


              var oTable = $('#fileData').dataTable( {
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
                    "iDisplayLength": <?= $Settings->rows_per_page ?>,
                    "aaSorting": [[ 0, "desc" ]],
                    'bProcessing': true, 'bServerSide': true, "bDestroy": true,
                    //'async': true,

                    'sAjaxSource': '<?= site_url('warehouses/get_transfer/') ?>/'+_v1+'/'+_v2+'/'+_v3,
                    'fnServerParams': function ( aoData ) {
                        aoData.push( { "fflag": _flag } );
                    },
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?= $this->security->get_csrf_token_name() ?>",
                            "value": "<?= $this->security->get_csrf_hash() ?>"
                        });
                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                    },
                    "aoColumns": [

                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        /*
                        { "mRender":set_integer,  "bSearchable": false},
                        {"mRender":method},
                        {"bSortable":false, "bSearchable": false},*/
                        ]
                });

              oTable.fnDraw();
              return false;
        }


        dibujarDt();


        $("#btnAll").click(
            function(){
                $("#warehouse, #warehouse2").val(0);
                $("#date1").val("");

                dibujarDt();
            }
        );
        $("#btnSearch").click(
            function(){
                dibujarDt();
            }
        );

    });

</script>
<style type="text/css">
    .table td:first-child { padding: 1px; }
    .table td:nth-child(6), .table td:nth-child(7), .table td:nth-child(8) { text-align: center; }
    .table td:nth-child(9)<?= $Admin ? ', .table td:nth-child(10)' : ''; ?> { text-align: right; }
</style>
<section class="content">
<input type="hidden" id="vurl" value="<?= site_url('products/get_products/') ?>" >
<input type="hidden" id="fflag" value="" >
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('search_by'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('warehouse_ori', 'warehouse_ori'); ?>
                                        <?php
                                        $sw['0'] = lang("select")." ".lang("warehouse_s");
                                        foreach($warehouses as $ware) {
                                            $sw[$ware->id] = $ware->warehouse;
                                        }
                                        ?>
                                        <?= form_dropdown('warehouse', $sw, set_value('warehouse'), 'class="form-control select2 warehouse" id="warehouse"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('warehouse_des', 'warehouse_des'); ?>
                                        <?php
                                        $sw['0'] = lang("select")." ".lang("warehouse_s");
                                        foreach($warehouses as $ware) {
                                            $sw[$ware->id] = $ware->warehouse;
                                        }
                                        ?>
                                        <?= form_dropdown('warehouse2', $sw, set_value('warehouse'), 'class="form-control select2 warehouse" id="warehouse2"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <?= lang("date", "date"); ?>
                                    <?= form_input('date_from', date('Y-m-d'), 'class="form-control datetimepicker" id="date1" required="required"'); ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <br>
                                    <button type="button" id="btnSearch" class="btn btn-success"><?= lang("search2")?></button>
                                    <button type="button" id="btnAll" class="btn btn-primary"><?= lang("see"). " " . lang("all");?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('list_results'); ?></h3>
                </div>
                <div class="box-body">
                        <div class="table-responsive">
                        <table id="fileData" class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
                            <thead>
                            <tr class="active">

                                <th class="col-xs-1"><?= lang("number"); ?></th>
                                <th class="col-xs-1"><?= lang("reference"); ?></th>
                                <th class="col-xs-1"><?= lang("warehouse_ori"); ?></th>
                                <th class="col-xs-1"><?= lang("warehouse_des"); ?></th>
                                <th class="col-xs-1"><?= lang("created_by"); ?></th>
                                <th class="col-xs-1"><?= lang("date"); ?></th>
                                <th class="col-xs-1"><?= lang("actions"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="10" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>

                        <div class="modal fade" id="picModal" tabindex="-1" role="dialog" aria-labelledby="picModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                                        <h4 class="modal-title" id="myModalLabel">title</h4>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img id="product_image" src="" alt="" />
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });
</script>
