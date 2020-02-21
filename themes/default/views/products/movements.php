<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<script type="text/javascript">
    var dt = "";


    $(document).ready(function() {
        function center(n) {
            return '<p style="text-align: center">'+ n +'</p>';
        }

        function aLeft(n) {
            return '<p style="text-align: left">'+ n +'</p>';
        }

        function dibujarDt(){//v1,v2,v3){
              var oTable = $('#fileData').dataTable( {
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
                    "aaSorting": [[ 0, "desc" ]],
                    "iDisplayLength": <?= $Settings->rows_per_page ?>,
                    'bProcessing': true, 'bServerSide': true, "bDestroy": true,
                    'async': true,
                    // 'sAjaxSource': $("#vurl").val() + '/' + v1 + '/' + v2 + '/' + v3 ,
                    'sAjaxSource': base_url+'products/get_movements',
                    // 'fnServerParams': function ( aoData ) {
                    //     aoData.push( { "idAlmacen": $("#warehouse").val(), "producto": $("#producto").val(), "idMotivo": $("#motive").val() } );
                    // },
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?= $this->security->get_csrf_token_name() ?>",
                            "value": "<?= $this->security->get_csrf_hash() ?>",
                            // "idAlmacen": $("#warehouse").val(),
                            // "producto": $("#producto").val(),
                            // "idMotivo": $("#motive").val()
                        });

                        aoData.push(
                        { "name": "idAlmacen", "value":  $("#warehouse").val()},
                        { "name": "producto", "value": $( "#producto" ).val() },
                        { "name": "idMotivo", "value": $( "#motive" ).val() },
                        );

                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                    },
                    "aoColumns": [

                        {"mRender":hrld},
                        null,
                        null,
                        null,
                        null,
                        {"mRender":aLeft},
                        null,
                        null
                    ]
                });

              oTable.fnDraw();
              return false;
        }


        // dibujarDt($("#warehouse").val(),$("#producto").val() , $("#motive").val());
        dibujarDt();


        $("#btnAll").click(
            function(){
                $("#warehouse").val(0);
                $("#producto").val("");
                $("#motive").val("");
                // dibujarDt('0','0','0');
                dibujarDt();
            }
        );
        $("#btnSearch").click(
            function(){
                /*if( $("#warehouse").val()=="" ){
                    alert("<?= lang('select'); ?> <?= lang('warehouse_s'); ?>");
                    return false;
                }*/
                var _pp = 0;
                if($("#producto").val()!=""){
                    _pp= $("#producto").val();
                }
                // dibujarDt($("#warehouse").val(),_pp , $("#motive").val());
                dibujarDt();
            }
        );


        $("#producto").keypress(function (e) {
          if (e.which == 13) {
            $("#btnSearch").trigger("click");
          }
        });

    });

</script>
<style type="text/css">
    .table td:first-child { padding: 1px; }
    .table td:nth-child(6), .table td:nth-child(7), .table td:nth-child(8) { text-align: center; }
    .table td:nth-child(9)<?= $Admin ? ', .table td:nth-child(10)' : ''; ?> { text-align: right; }
</style>
<section class="content">
<input type="hidden" id="vurl" value="<?= site_url('products/get_movements/') ?>" >
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
                                        $sw['0'] = lang("select")." ".lang("warehouse_s");
                                        foreach($warehouses as $ware) {
                                            $sw[$ware->id] = $ware->warehouse;
                                        }
                                        ?>
                                        <?= form_dropdown('warehouse', $sw, set_value('warehouse'), 'class="form-control select2 warehouse" id="warehouse"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('product', 'product'); ?>
                                        <?= form_input('producto', set_value('name'), 'class="form-control tip" id="producto"  required="required"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('motive', 'motive'); ?>
                                        <?php
                                        $mm[''] = lang("select")." ".lang("motive");
                                        foreach($motivos as $motivo) {
                                            $mm[$motivo->id] = $motivo->motive;
                                        }
                                        ?>
                                        <?= form_dropdown('motive', $mm, '0', 'class="form-control select22 motive" id="motive"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group pull-right" >
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
                                <th><?= lang("date"); ?></th>
                                <th class="col-xs-1"><?= lang("warehouse_s"); ?></th>
                                <th class="col-xs-2"><?= lang("product"); ?></th>
                                <th><?= lang("quantity"); ?></th>
                                <th><?= lang("type"); ?></th>
                                <th class="col-xs-2"><?= lang("motive"); ?></th>

                                <th><?= lang("reference"); ?></th>
                                <th class="col-xs-1"><?= lang("created_by"); ?></th>

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
