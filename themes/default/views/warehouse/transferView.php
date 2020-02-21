<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
<div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                
                <div class="box-body">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                <div class="form-group">
                                    <label class="control-label" for="prin_activ_search">
                                        <?= lang("number"); ?>
                                    </label>
                                    <?= form_input('num_g', $cab->num, 'class="form-control input-sm kb-text" id="num_g" readonly ');?>
                                </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                <div class="form-group">
                                    <label class="control-label" for="prin_activ_search">
                                        <?= lang("reference"); ?>
                                    </label>
                                    <?= form_input('reference', $cab->ref, 'class="form-control input-sm kb-text" id="reference" readonly');?>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                <div class="form-group">
                                    <label class="control-label" for="prin_activ_search">
                                        <?= lang("warehouse_des"); ?>
                                    </label>
                                    <?= form_input('destiny', $cab->ware2, 'class="form-control input-sm kb-text" id="destiny" readonly');?>
                                </div>
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
                <div class="box-body">
                <div id="c-alert3" class="alert alert-danger" style="display:none;"></div>
                
                <?php 
                $cont= 1;
                foreach($dets as $det){?>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="prin_activ_search">
                                <?= lang("product"); ?>
                            </label>
                            <?= form_input('namep'.$cont, $det->product_name, 'class="form-control input-sm kb-text" readonly id="namep'.$cont.'" ');?>
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="prin_activ_search">
                                <?= lang("stock_prev"); ?>
                            </label>
                            <?= form_input('stock'.$cont, $det->stock_prev, 'class="form-control input-sm kb-text" readonly id="stock'.$cont.'" ');?>
                        </div>
                    </div> 

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" for="prin_activ_search">
                                <?= lang("stock_new"); ?>
                            </label>
                            <?= form_input('new_stock'.$cont, $det->stock_new, 'class="form-control input-sm kb-text stockT" readonly num="'.$cont.'" id="new_stock'.$cont.'"');?>
                        </div>
                    </div> 
                    

                </div>

                    
                <?php $cont++;}?>

                
                </div>
            </div>
        </div>
    </div>
                                
    <div class="row">
        <div class="col-xs-12">
            <div class="pull-right">
                <button type="button" id="btnRegresar" class="btn btn-primary"><?= lang("back_to")?></button>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    var _flag= 0;
    var _marcador= 0;
    $(document).ready(function () {
       
        $("#btnRegresar").click(
            function(){
                     window.history.back();
            }
        );

        
        $(document).on('focus','.stockT', function(e){
            
            var num =   $(this).attr("num") ;
            
            _marcador = $("#new_stock"+ num ).val();
           
            e.preventDefault(); 
            return false;
         }
        );

        $(document).on('keyup','.stockT', function(e){
            
            var num =   $(this).attr("num") ;
            
            var val1 = $("#new_stock"+ num ).val();
            var val2 = $("#stock"+ num ).val();
            
            if(val1==""){return false;}

            if(!(isNumeric(val1)) ){
                $("#new_stock"+ num ).val(0);
                return false;
            }

            var val1 = parseInt(val1);
            var val2 = parseInt(val2);

            if(val1 > val2){
                $("#new_stock"+ num ).val(val2);
            }
            
            e.preventDefault(); 
         }
        );

        $("#btnTrasladar").click(
            function(){
                var _fflag1 = 1;

                if( $("#warehouse").val() ==""){
                    alert("<?php echo lang('select')." ".lang('warehouse_s'); ?>");       
                    return false;
                }
                $(".stockT").each( function () {
                   if( $.trim($( this ).val()) =="" || $( this ).val()==0){
                    _fflag1 = 0;
                   }
                });
                if(_fflag1==0){
                    alert("<?php echo lang('alert_stock'); ?>");    
                    return false;
                }                
                $("#traslado-form").submit();

            }
        );


        $("#traslado-form").on("submit", function(e) { 

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


        function isNumeric(n) {
          return !isNaN(parseFloat(n)) && isFinite(n);
        }
    });
</script>



