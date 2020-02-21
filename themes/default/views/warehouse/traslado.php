
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= $product->name; ?> - <?= lang('translate'); ?></h4>
        </div>
        <div class="modal-body">
                <div id="c-alert3" class="alert alert-danger" style="display:none;"></div>
                <?= form_open('warehouses/post_translate', 'id="traslado-form"'); ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('warehouse_s', 'warehouse_s'); ?> <?= lang('destination', 'destination'); ?>
                                <?php
                                $sw[''] = lang("select")." ".lang("warehouse_s");
                                foreach($warehouses as $ware) {
                                    if($warehouse_id!=$ware->id){
                                        $sw[$ware->id] = $ware->warehouse;
                                    }
                                }
                                ?>
                                <?= form_dropdown('warehouse', $sw, set_value("0"), 'class="form-control select2 warehouse" id="warehouse"  required="required" style="width:100%;"'); ?>                                
                        </div>
                    </div>
                
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="prin_activ_search">
                                <?= lang("current"); ?> <?= lang("stock"); ?>
                            </label>
                            <?= form_input('stock', $detalle->stock, 'class="form-control input-sm kb-text" id="stock"');?>
                        </div>
                    </div> 

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="prin_activ_search">
                                <?= lang("stock"); ?> <?= lang("translate"); ?>
                            </label>
                            <?= form_input('new_stock', '', 'class="form-control input-sm kb-text" id="new_stock"');?>
                        </div>
                    </div> 
                    
                    <div class="col-md-2">
                        <button style="margin-bottom: -50px;" type="button" class="btn btn-success" id="btn_search_product"> <?=lang('translate')?> </button>
                        
                    </div>                  
                </div>
                <input type="hidden" id="idDetalle" name="idDetalle" value="<?php echo $detalle->id;?>">
                <input type="hidden" id="idProduct" name="idProduct" value="<?php echo $detalle->product_id;?>">
                <input type="hidden" id="idWarehouse" name="idWarehouse" value="<?php echo $warehouse_id;?>">
                <?= form_close(); ?>

        </div>
            <div class="modal-footer" style="margin-top:0;">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
            </div>
</div>
</div>

<script>
    $(document).ready(function() {
        $("#stock").prop("readonly","readonly")
        $("#btn_search_product").click(
            function(){
                $("#traslado-form").submit();
            }
        );

        $("#traslado-form").on("submit", function(e) { 
            if($("#warehouse").val()==""){ 
                alert("<?= lang('select'); ?>  <?= lang('warehouse_s'); ?> <?= lang('destination'); ?>");
                return false;
            }
            //alert($("#new_stock").val() + ' '+ $("#stock").val() ); return false;

            if( parseInt($("#new_stock").val()) > parseInt($("#stock").val()) ){
                alert("<?= lang('traslado_error'); ?>");
                return false;
            }
            e.preventDefault();
            $.ajax({
                type: "post",
                url: base_url+'warehouses/post_translate',
                data: $( this ).serialize(),
                dataType: "json",
                success: function(res) {
                    
                    
                    window.location.href = '<?= site_url('warehouses/products/'); ?>/<?= $warehouse_id?>';
                }/*,
                error: function(){
                    alert("Error")
                    return false;
                }*/
            });
            return false;
        });


    });
</script>