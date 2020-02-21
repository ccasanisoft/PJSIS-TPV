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

        function methodCu(n) {
            return (n == "PEN") ? '<center><span class="label label-primary">PEN</span></center>' : '<center><span class="label label-success">USD</span></center>';
        }

        function pType(n) {
            // return (n == "standard") ? '<center><span class="label label-primary"><?= lang('standard'); ?></span></center>' : (n == "service") ? '<center><span class="label label-warning"><?= lang('service'); ?></span></center>' : '<center><span class="label label-default"><?= lang('combo'); ?></span></center>';
            return (n == "standard") ? '<span><?= lang('standard'); ?></span>' : (n == "service") ? '<span><?= lang('service'); ?></span>' : '<span><?= lang('combo'); ?></span>';
        }

        function dibujarDt(flag){
              var oTable = $('#fileData').dataTable( {
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
                    "aaSorting": [[ 2, "ASC" ]],
                    "iDisplayLength": <?= $Settings->rows_per_page ?>,
                    'bProcessing': true, 'bServerSide': true, "bDestroy": true,
                    'async': true,
                    'sAjaxSource': $("#vurl").val() + '/' + flag,
                    'fnServerParams': function ( aoData ) {
                        aoData.push( { "fflag": _flag } );
                    },
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?= $this->security->get_csrf_token_name() ?>",
                            "value": "<?= $this->security->get_csrf_hash() ?>",
                            "fflag": _flag,
                        });
                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});                     
                    },
                    
                    "aoColumns": [
                        {"mRender":image,"bSortable":false},
                        null,
                        null,
                        {"mRender":pType},
                        null,
                        { "mRender":set_integer,  "bSearchable": false},
                        {"mRender":method},
                        {"mRender":methodCu},
                        <?= $Admin ? '{"mRender":currencyFormat},' : ''; ?> {"mRender":currencyFormat},
                        //{ "bSearchable": false},
                        {"bSortable":false, "bSearchable": false},
                        ]                        

                });
 
              oTable.fnDraw();
              return false;
        }


        dibujarDt(0);
        $('#fileData').on('click', '.image', function() {
            var a_href = $(this).attr('href');
            var code = $(this).attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src',a_href);
            $('#picModal').modal();
            return false;
        });
        $('#fileData').on('click', '.barcode', function() {
            var a_href = $(this).attr('href');
            var code = $(this).attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src',a_href);
            $('#picModal').modal();
            return false;
        });
        $('#fileData').on('click', '.open-image', function() {
            var a_href = $(this).attr('href');
            var code = $(this).closest('tr').find('.image').attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src',a_href);
            $('#picModal').modal();
            return false;
        });

        $("#btnAll").click(
            function(){
                $("#warehouse").val("");
                dibujarDt(0);
            }
        );
        $("#btnSearch").click(
            function(){
                if( $("#warehouse").val()=="" ){
                    alert("<?= lang('select'); ?> <?= lang('warehouse_s'); ?>");
                    return false;
                }
                dibujarDt($("#warehouse").val());
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('warehouse_s', 'warehouse_s'); ?>
                                        <?php
                                        $sw[''] = lang("select")." ".lang("warehouse_s");
                                        foreach($warehouses as $ware) {
                                            $sw[$ware->id] = $ware->warehouse;
                                        }
                                        ?>
                                        <?= form_dropdown('warehouse', $sw, set_value('warehouse'), 'class="form-control select2 warehouse" id="warehouse"  required="required" style="width:100%;"'); ?>
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
                                <th style="max-width:30px;"><?= lang("image"); ?> </th>
                                <th class="col-xs-1"><?= lang("code"); ?></th>
                                <th><?= lang("name"); ?></th>
                                <th class="col-xs-1"><?= lang("type"); ?></th>
                                <th class="col-xs-1"><?= lang("category"); ?></th>
                                <th class="col-xs-1"><?= lang("quantity"); ?></th>
                                <!-- <th class="col-xs-1"><?= lang("tax"); ?></th> -->
                                <th class="col-xs-1"><?= lang("method"); ?></th>
                                <th style="text-align:center; max-width:20px;"><?php echo $this->lang->line("currency_name"); ?></th>
                                <?php if($Admin) { ?>
                                <th class="col-xs-1"><?= lang("pcost"); ?></th>
                                <?php } ?>
                                <th class="col-xs-1"><?= lang("pprice"); ?></th>
                                <th style="width:145px;"><?= lang("actions"); ?></th>
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