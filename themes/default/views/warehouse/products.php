<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<script type="text/javascript">
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

        function centrar(n) {
            if(n !== null) {
                return '<div style="text-align:center">'+n+'</div>';
            }
            return '';
        }

        function ver(){
            $("input[name='chkSel[]']").each( function () {
                //console.log(  $( this ).attr("value") );
                $( this ).attr("class","chkSel");
                $("#chkTodo" ).attr("class","chkSel");
                $("#chkTodo" ).removeAttr("style");
                
            });


        }


        $('#fileData').dataTable( {
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
            "aaSorting": [[ 1, "asc" ]], "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('warehouses/get_products_by_warehouse/') ?>/<?= $id?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': function(data){
                   
                    fnCallback(data);
                    ver();
               }});
            },
            "aoColumns": [ 
                {"mRender":centrar},
                null, 
                null, 
                null, 
                null, 
                {"mRender":set_checkbox,"bSortable":false,  "bSearchable": false}                        
            ]
        });
        

    });

function render_checkbox(data, type, full) { 
    var checked = "";
    if (data == true) { checked = "checked='true'" };
    return "";
}
</script>
<style type="text/css">
    .table td:first-child { padding: 1px; }
    .table td:nth-child(6), .table td:nth-child(7), .table td:nth-child(8) { text-align: center; }
    .table td:nth-child(9)<?= $Admin ? ', .table td:nth-child(10)' : ''; ?> { text-align: right; }
</style>

<section class="content">

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">

                <div class="box-body">
                        <div class="table-responsive">
                        <table id="fileData" class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
                            <thead>
                            <tr class="active">
                                <th class="col-xs-1"><?= lang("code"); ?></th>
                                <th class="col-xs-2"><?= lang("name"); ?></th>
                                <th class="col-xs-1"><?= lang("type"); ?></th>
                                <th class="col-xs-1"><?= lang("category"); ?></th>
                                <th class="col-xs-1"><?= lang("stock"); ?></th>
                                <th class="col-xs-1" style="text-align: center;">
                                    <button id="chkTodo" name="chkTodo" /><?= lang('select_all'); ?></button>
                                </th>                                
                               <!-- <th style="width:145px;"><?= lang("actions"); ?></th>-->
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
    <div class="row">
        <div class="col-xs-12">
            <div class="box ">
                <div class="box-header">
                    <div class="pull-left">
                        <button type="button" id="btnRegresar" class="btn btn-primary"><?= lang("back_to")?></button>
                    </div>
                    <div class="pull-right">
                    <h3 class="box-title"><?= lang('with_select'); ?></h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <button type="button" id="btnTrasladar" class="btn btn-success"><?= lang("translate")?></button>
                 </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?= form_open('warehouses/trasladar_varios', 'id="traslado-form2"'); ?>
    <input type="hidden" id="seleccionados" name="seleccionados" value="">
    <input type="hidden" id="idWare" name="idWare" value="<?php echo $id;?>">
<!--    <input type="submit" value="continuar" >-->
<?= form_close(); ?>

<input type="hidden" id="selAll" value="<?= lang('select_all'); ?> ">
<input type="hidden" id="selNone" value="<?= lang('select_none'); ?> ">
<script type="text/javascript">
    var _flag= 0;
    $(document).ready(function () {
        _flag= 0;
        $('#chkTodo').click(function () {
            if(_flag==0){ 
                _flag = 1;
                $("input[name='chkSel[]']").each( function () {
                    $(this).prop( "checked", true );
                    $('#chkTodo').html($("#selNone").val());
                });                
            }else if(_flag==1){ 
                _flag = 0;

                $("input[name='chkSel[]']").each( function () {
                    $(this).prop( "checked", false );
                    $('#chkTodo').html($("#selAll").val());
                    
                });
                
            }

            return false;
        });

        $(document).on("click", "input[name='chkTodo']", function () {
            alert("FECK");
            //if (this.checked) {}
        });
        $("#btnRegresar").click(
            function(){
                window.location.href = '<?= site_url('/warehouses'); ?>';
            }
        );

        $("#btnTrasladar").click(
            function(){
                var _sel = "";
                $("input[name='chkSel[]']").each( function () {
                    if($(this).prop('checked')) {
                        _sel = _sel + "," +$(this).val();
                    }
                });
                if(_sel==""){
                    alert("Seleccione al menos un producto");
                    return false;
                }
                $("#seleccionados").val(_sel);
                $("#traslado-form2").submit();
            }
        );
    });
</script>