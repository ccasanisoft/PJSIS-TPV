<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<style>
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
        border: #eae9e9;
    }
    .table>tbody>tr.active>td, .table>tbody>tr.active>th, .table>tbody>tr>td.active, .table>tbody>tr>th.active, .table>tfoot>tr.active>td, .table>tfoot>tr.active>th, .table>tfoot>tr>td.active, .table>tfoot>tr>th.active, .table>thead>tr.active>td, .table>thead>tr.active>th, .table>thead>tr>td.active, .table>thead>tr>th.active {
        background-color: #eae9e9;
    }
    .table {
        margin-bottom: 10px;
    }
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <!-- <h3 class="box-title"><?= lang('enter_info'); ?></h3> -->
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <?php echo form_open_multipart("sales/add", 'class="validation"'); ?>

                        <div class="alert alert-warning alert-dismissable" style="display: none" id="warning">
                            <button id="close" aria-hidden="true" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-warning"></i> <?= lang('warning'); ?></h4>
                            <p id="warning_text"></p>
                        </div>

                        <div class="row">

                            <div class="col-md-2">
                                <div class="form-group">

                                    <?= lang("document_type", "document_type"); ?>
                                    <?php
                                        $dt[0] = lang("select");
                                        $dt[1] = lang("bill_type");
                                        $dt[2] = lang("invoice");
                                        $dt[3] = lang("nventa"); //*****Renato TRJ023 25/04/2019   ********
                                    ?>
                                    <?= form_dropdown('document_type', $dt, set_value('document_type', 1), 'class="form-control select2 document_type" id="document_type"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>

                          <div class="col-md-3">
                                    <div class="form-group">
                                      
                                        <?= lang('customer', 'customer'); ?>
                                        <?php
                                            $ct[''] = lang("select")." ".lang("customer");
                                            if($customers != null){
                                                foreach($customers as $customer) {
                                                    $ct[$customer->id] = $customer->name;
                                                }
                                            }
                                        ?>

                                        <?= form_dropdown('customer', $ct, set_value('customer'), 'class="form-control select2 tip" id="customer"  required="required" style="width:100%;"'); ?>
                                        
                                </div>

                            </div>
                        <!-- boton cliente -->
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="">Añadir</label>
                                
                                <button type="button" class="form-control btn btn-primary" data-toggle="modal" data-target="#customerModal"><i class="fa fa-users"></i></button>
                            </div>
                        </div>




                            <?php $tomorrow = date('Y-m-d', strtotime("+1 day")); ?>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('issue_date_2', 'issue_date_2'); ?>
                                    <?= form_input('date', set_value('date', date('Y-m-d')), 'class="form-control datetimepicker tip" id="date"  required="required"'); ?>
                                    <!-- H:i -->
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('expiration_date', 'expiration_date'); ?>
                                    <?= form_input('expiration_date', set_value('expiration_date'), 'class="form-control datepicker tip" id="expiration_date"'); ?>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('currency_name', 'currency_name'); ?>
                                    <?php
                                        $cy['PEN'] = lang('currency_pen');
                                        $cy['USD'] = lang('currency_usd');
                                    ?>
                                    <?= form_dropdown('currency_code', $cy, set_value('currency_code', $Settings->currency_prefix), 'class="form-control select2" id="currency_code"  required="required"'); ?>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <!-- <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('warehouse_s', 'warehouse_s'); ?>
                                        <?php
                                            $sw[''] = lang("select")." ".lang("warehouse_s");
                                            if($warehouses != null){
                                                foreach($warehouses as $ware) {
                                                    $sw[$ware->id] = $ware->warehouse;
                                                }
                                            }
                                        ?>
                                        <?= form_dropdown('warehouse', $sw, set_value('warehouse', $Settings->default_warehouse), 'class="form-control select2 warehouse" id="warehouse"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div> -->

                        </div>

                        <!-- <hr> -->
                        &nbsp;

                        <div style="border-top: 1px solid #337ab7; border-bottom: 1px solid #337ab7; border-left: 1px solid #337ab7; border-right: 1px solid #337ab7; padding-bottom: 10px">
                        &nbsp;

                            <div style="padding-left:10px; padding-right:10px" class="form-group">
                                <input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>" id="add_item" class="form-control">
                            </div>

                            <div style="padding-left:10px; padding-right:10px" class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="poTable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr class="active">
                                                    <th><?= lang('product'); ?></th>
													<!--**************TRJ044 - ALEXANDER ROCA - 23/05/2019******************-->
													<?php if($advance_sale == 0){?>
													
														<!--**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************-->
														<th class="col-xs-2"><div style="margin-left:20px;"><?= lang('tax'); ?></div></th>
														<!--**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************-->
														<?php if($plastic_bags > 0){?>
														
															<th class="col-xs-1"><?= lang('quantity'); ?></th>
															<th class="col-xs-2"><?= lang('price'); ?></th>
															<th class="col-xs-1"><?= lang('tax_ICBPER'); ?></th>
														
														<?php }else{?>
														
															<th class="col-xs-2"><?= lang('quantity'); ?></th>
															<th class="col-xs-2"><?= lang('price'); ?></th>
														
														<?php }?>
														<th class="col-xs-2"><?= lang('importe'); ?></th>
													
													<?php }else{?>
													
													<th class="col-xs-1"><?= lang('quantity'); ?></th>
													<th class="col-xs-1"><?= lang('affected'); ?></th>
													<th class="col-xs-1"><?= lang('exonerated'); ?></th>
													<th class="col-xs-1"><?= lang('exo'); ?></th>
													<?php if($plastic_bags > 0){?>
													<th class="col-xs-1"><?= lang('tax_ICBPER'); ?></th>
													<?php }?>
													<th class="col-xs-1"><span class="text-right"><?= lang('importe'); ?></span></th>
													
													<?php }?>
													<!--**************TRJ044 - ALEXANDER ROCA - 23/05/2019******************-->
                                                    
                                                    <th style="width:25px;"><i class="fa fa-trash-o"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
												<?php if($advance_sale == 0){?>
												
													<?php if($plastic_bags > 0){?>
                                                    <td colspan="7"><?= lang('add_product_by_searching_above_field'); ?></td>
													<?php }else{?>
													<td colspan="6"><?= lang('add_product_by_searching_above_field'); ?></td>
													<?php }?>
													
												<?php }else{?>
												
													<?php if($plastic_bags > 0){?>
                                                    <td colspan="8"><?= lang('add_product_by_searching_above_field'); ?></td>
													<?php }else{?>
													<td colspan="7"><?= lang('add_product_by_searching_above_field'); ?></td>
													<?php }?>
													
												<?php }?>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="active">
                                                    <th><?= lang('total_items'); ?>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														&nbsp;&nbsp;
                                                        <span id="tart">0 (0.00)</span>
                                                    </th>
													<?php if($plastic_bags > 0){?>
													
                                                    <th class="col-xs-1"></th>
													<th class="col-xs-1"></th>
													
													<?php }else{?>
													
													<th class="col-xs-2"></th>
													<th class="col-xs-2"></th>
													
													<?php }?>
                                                    <th class="text-right"><?= lang('subtotal'); ?></th>
													<!--**************TRJ044 - ALEXANDER ROCA - 24/05/2019******************-->
													<?php if($advance_sale == 1){?>
													<th class="col-xs-1"></th>
													<?php }?>
													<!--**************TRJ044 - ALEXANDER ROCA - 24/05/2019******************-->
													<?php if($plastic_bags > 0){?>
													<th style="width:25px;"></th>
													<?php }?>
                                                    <th class="col-xs-2 text-right"><span id="gtotal">0.00</span></th>
                                                    <input type="hidden" id="total_pay" value="0.00" name="total_pay">
                                                    <th style="width:25px;"></th>
													
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right" style="padding-left:10px; padding-right:10px">
                                <table class="table table-condensed">
                                    <tbody>
                                        <tr>
                                            <td style="border: none;"><?= strtoupper(lang('discount').':'); ?></td>
                                            <td style="col-md-2; border: none; padding-left:8%; padding-right:25px;" colspan="2"><input class="form-control input-sm kb-pad text-right" name="gdiscount" type="text" value="0.00" id="gdiscount" onClick="this.select();"></td>
                                        </tr>
                                        <tr>
                                            <td style="border: none;"><strong><?= strtoupper(lang('total').':'); ?></strong></td>
                                            <td style="border: none; padding-right:35px;" class="col-xs-2"><strong><span id="gtot">0.00</span></strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: none;"><?= strtoupper(lang('opgrav').':'); ?></td>
                                            <td style="border: none; padding-right:35px;" class="col-xs-2"><span id="gaffected">0.00</span></td>
                                            <!-- <input type="hidden" id="opgra" value="0.00" name="opgra"> -->
                                        </tr>
										<!--**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************-->
										<tr>
                                            <td style="border: none;"><?= strtoupper(lang('opexonReal').':'); ?></td>
                                            <td style="border: none; padding-right:35px;" class="col-xs-2"><span id="gexoneratedReal">0.00</span></td>
                                            <!-- <input type="hidden" id="opexo" value="0.00" name="opexo"> -->
                                        </tr>
										<!--**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************-->
                                        <tr>
                                            <td style="border: none;"><?= strtoupper(lang('opexon').':'); ?></td>
                                            <td style="border: none; padding-right:35px;" class="col-xs-2"><span id="gexonerated">0.00</span></td>
                                            <!-- <input type="hidden" id="opexo" value="0.00" name="opexo"> -->
                                        </tr>
                                        <tr>
                                            <td style="border: none;"><?= strtoupper(lang('product_tax'). " - " . $Settings->default_tax_rate . '%:'); ?></td>
                                            <td style="border: none; padding-right:35px;" class="col-xs-2"><span id="gtax">0.00</span></td>
                                            <!-- <input type="hidden" id="imigv" value="0.00" name="imigv"> -->
                                        </tr>
										<!--***********TRJ068 - ALEXADER ROCA - 02/08/2019************-->
										<?php if($plastic_bags > 0){?>
										<tr>
                                            <td style="border: none;"><?= strtoupper(lang('product_tax_ICBPER').' ('.lang('total_bags').'):'); ?></td>
                                            <td style="border: none; padding-right:35px;" class="col-xs-2"><span id="gtaxICBPER">0.00</span></td>
                                            <!-- <input type="hidden" id="imigv" value="0.00" name="imigv"> -->
                                        </tr>
										<?php }?>
										<!--***********TRJ068 - ALEXADER ROCA - 02/08/2019************-->
                                        <tr class="active">
                                            <td style="border: none"><strong><?= strtoupper(lang('importe') . " " . lang('total').':'); ?></strong></td>
                                            <td style="border: none; padding-right:35px;" class="col-xs-2"><strong><span id="gimptotal">0.00</span></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        &nbsp;

                        <div class="form-group">
                            <?= lang('attachment', 'attachment'); ?>
                            <input type="file" name="userfile" class="form-control tip" id="attachment">
                        </div>
                        <div class="form-group">
                            <?= lang("note", 'note'); ?>
                            <?= form_textarea('note', set_value('note'), 'class="form-control redactor" id="note"'); ?>
                        </div>
                        <div class="form-group">
                            <?= form_submit('add_sale', lang('add_sale'), 'class="btn btn-primary" id="add_sale"'); ?>
                            <button type="button" id="reset" class="btn btn-danger"><?= lang('reset'); ?></button>
                        </div>

                        <?php echo form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>

.stilobtn{
 background: #ADD8E6;
 /*border:3px solid rgba(0,0,0,0.5);*/
border:4px solid #ADD8E6;
 height: 20px;
 width: 20px;
 border-radius: 100%;
 left: 15px;

 
}

button.natural {
    background: #ADD8E6;
   border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  color: #ADD8E6;
  display: inline-block;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 3px;
  text-align: center;
  width: 1.6em; 
  cursor:pointer;
   border:2px solid rgba(0,0,0,0.2);
}

button.juridica {
  background: #ADD8E6;
   border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  color: #ADD8E6;
  display: inline-block;
  margin-left:15px;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 3px;
  text-align: center;
  width: 1.6em; 

  cursor:pointer; 
   border:2px solid rgba(0,0,0,0.2);
}


.button {
  background: none;
  border: 0;
  color: inherit;
  /* cursor: default; */
  font: inherit;
  line-height: normal;
  overflow: visible;
  padding: 0;
  -webkit-user-select: none; /* for button */
   -webkit-appearance: button; /* for input */
     -moz-user-select: none;
      -ms-user-select: none;
}
.pn{
    cursor:pointer;
    visibility: 1;
    display: inline;
    opacity: 9;

}
</style>

<!-- modal cliente -->
<div class="modal fade" data-easein="flipYIn" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="cModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-remove"></i></button>
                <h4 class="modal-title" id="cModalLabel">
                <?=lang('add_customer')?>
                </h4>
            </div>
          
         <form id="customers_form"> 
            <div class="modal-body">
                <div id="c-alert" class="alert alert-danger" style="display:none;">
                    <ul id="c-s">
                       
                    </ul>
                </div>

                <div id="rucalert" class="alert alert-danger" style="display:none;">El Ruc Ingresado No Ha Sido Encontrado</div>
                <div id="rucalert2" class="alert alert-danger" style="display:none;">Ruc cuenta con 11 digitos</div>
                
               <div class="form-group">
                <label class="control-label" for="type_persona"><?= $this->lang->line("type_person"); ?></label>

               <div class=""> 
                <label for="natural" class="sfg"><button class="stilobtn" name='persona' value="1" id="sn"></button></label><span style="margin-left: 5px">Natural</span>
             
                <label  for="juridica" style="margin-left: 15px"><button class="stilobtn" value="2" id="sj"></button></label><span style="margin-left: 5px">Juridica</span>

            </div>
            </div>


           



          <input type="text" hidden name="<?php echo $this->security->get_csrf_token_name(); ?>" id="token" value="<?php echo $this->security->get_csrf_hash(); ?>" />


                       

                    <div class="form-group">
                        <?= lang('document_type', 'document_type'); ?>
                        <?php
                        $ctv1[null] = lang("select")." ".lang("document_type");
                        foreach($document_type as $doc_type) {
                        $ctv1[$doc_type->id] = $doc_type->document_type;
                        }
                        ?>
                        <?= form_dropdown('document_type', $ctv1, set_value("type",$customer->document_type), 'class="form-control tip select2" id="document_type1"  required="required" style="width:100%;"'); ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label" id="nombre-persona" for="code">
                                    <?= lang("name"); ?>
                                </label>
                                <?= form_input('name', '', 'class="form-control input-sm kb-text" id="cname"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label" for="cemail">
                                    <?= lang("email_address"); ?>
                                </label>
                                <?= form_input('email', '', 'class="form-control input-sm kb-text" id="cemail"'); ?>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label" for="phone">
                                    <?= lang("phone"); ?>
                                </label>
                                <?= form_input('phone', '', 'class="form-control input-sm kb-pad" id="cphone"');?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label" for="cf1">
                                    <?= lang("cf2"); ?>
                                </label>
                                <?= form_input('cf1', '', 'class="form-control input-sm kb-text" id="cf1"'); ?>
                            </div>
                        </div>
                        
                        <div class="col-xs-5">
                            
                            <div class="form-group">
                                <label class="control-label" for="cf2">
                                    <?= lang("cf1"); ?>
                                </label>
                                
                                <?= form_input('cf2', '', 'class="form-control input-sm kb-text" id="cf2" maxlength="11"');?>
                                
                            </div>
                        </div>
                        <div class="col-xs-1" style="margin-top:4%;margin-left:-4%">
                            <div type="conruc" onclick="obtenerDatos2()" class="btn btn-info btn-sm glyphicon glyphicon-ok" tabindex="0" data-toggle="tooltip" title="Consulta Sunat"></div>
                            <div id="cargando" class="" style="display:none;"><img style="width: 30px;height:auto;" src="https://pa1.narvii.com/6558/d6738388bae69543478eb78d0545bf0475ef05a6_hq.gif"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label" for="code">
                                    <?= lang("address"); ?>
                                </label>
                                <?= form_input('direccion', '', 'class="form-control input-sm kb-text" id="cdireccion"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?= lang('customers_type', 'customers_type'); ?>
                        <?php
                        $ctv[] = lang("select")." ".lang("customers_type");
                        foreach($customer_type as $cust_type) {
                        $ctv[$cust_type->id] = $cust_type->customers_type;
                        }
                        ?>
                        
                        <!--********** TRJ124***** -->
                        <!-- AMADOR -->
                        <?= form_dropdown('customer_type',$ctv, set_value("type",$customer->customers_type_id),'class="form-control " id="customer_type"  required="required" style="width:100%;"');
                        ?>
                        <!-- END -->
                    </div>
                </div>
                <div class="modal-footer" style="margin-top:0;">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
                    <button type="button" class="btn btn-primary" id="add_customer"> <?=lang('add_customer')?> </button>
                </div>
                   
             </form> 
            </div>
        </div>
    </div>


<!-- modal cliente edit -->
<div class="modal fade" data-easein="flipYIn" id="customerModalEdit" tabindex="-1" role="dialog" aria-labelledby="cModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-remove"></i></button>
                <h4 class="modal-title" id="cModalLabel">
                <?=lang('add_customer')?>
                </h4>
            </div>
          
         <form id="customers_form"> 
            <div class="modal-body">
                <div id="c-alert" class="alert alert-danger" style="display:none;">
                    <ul id="c-s">
                       
                    </ul>
                </div>

                <div id="rucalerte" class="alert alert-danger" style="display:none;">El Ruc Ingresado No Ha Sido Encontrado</div>
                <div id="rucalert2e" class="alert alert-danger" style="display:none;">Ruc cuenta con 11 digitos</div>
                
               <div class="form-group">
                <label class="control-label" for="type_persona"><?= $this->lang->line("type_person"); ?></label>
                <input type="text" hidden="" id="idclienteedit">

               <div class=""> 
                <label for="natural" class="sfg"><button class="stilobtn" name='persona' value="1" id="sne"></button></label><span style="margin-left: 5px">Natural</span>
             
                <label  for="juridica" style="margin-left: 15px"><button class="stilobtn" value="2" id="sje"></button></label><span style="margin-left: 5px">Juridica</span>

            </div>
            </div>


           



          <input type="text" hidden name="<?php echo $this->security->get_csrf_token_name(); ?>" id="token" value="<?php echo $this->security->get_csrf_hash(); ?>" />


                       

                    <div class="form-group">
                        <?= lang('document_type', 'document_type'); ?>
                        <?php
                        $ctv1[null] = lang("select")." ".lang("document_type");
                        foreach($document_type as $doc_type) {
                        $ctv1[$doc_type->id] = $doc_type->document_type;
                        }
                        ?>
                        <?= form_dropdown('document_type', $ctv1, set_value("type",$customer->document_type), 'class="form-control tip select2" id="document_type1e"  required="required" style="width:100%;"'); ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label" id="nombre-persona" for="code">
                                    <?= lang("name"); ?>
                                </label>
                                <?= form_input('name', '', 'class="form-control input-sm kb-text" id="cnamee"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label" for="cemail">
                                    <?= lang("email_address"); ?>
                                </label>
                                <?= form_input('email', '', 'class="form-control input-sm kb-text" id="cemaile"'); ?>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label" for="phone">
                                    <?= lang("phone"); ?>
                                </label>
                                <?= form_input('phone', '', 'class="form-control input-sm kb-pad" id="cphonee"');?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label" for="cf1">
                                    <?= lang("cf2"); ?>
                                </label>
                                <?= form_input('cf1', '', 'class="form-control input-sm kb-text" id="cf1e"'); ?>
                            </div>
                        </div>
                        
                        <div class="col-xs-5">
                            
                            <div class="form-group">
                                <label class="control-label" for="cf2">
                                    <?= lang("cf1"); ?>
                                </label>
                                
                                <?= form_input('cf2', '', 'class="form-control input-sm kb-text" id="cf2e" maxlength="11"');?>
                                
                            </div>
                        </div>
                        <div class="col-xs-1" style="margin-top:4%;margin-left:-4%">
                            <div type="conruc" onclick="obtenerDatos2()" class="btn btn-info btn-sm glyphicon glyphicon-ok" tabindex="0" data-toggle="tooltip" title="Consulta Sunat"></div>
                            <div id="cargandoe" class="" style="display:none;"><img style="width: 30px;height:auto;" src="https://pa1.narvii.com/6558/d6738388bae69543478eb78d0545bf0475ef05a6_hq.gif"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label" for="code">
                                    <?= lang("address"); ?>
                                </label>
                                <?= form_input('direccion', '', 'class="form-control input-sm kb-text" id="cdireccione"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?= lang('customers_type', 'customers_type'); ?>
                        <?php
                        $ctv[] = lang("select")." ".lang("customers_type");
                        foreach($customer_type as $cust_type) {
                        $ctv[$cust_type->id] = $cust_type->customers_type;
                        }
                        ?>
                        
                        <!--********** TRJ124***** -->
                        <!-- AMADOR -->
                        <?= form_dropdown('customer_type',$ctv, set_value("type",$customer->customers_type_id),'class="form-control " id="customer_typee"  required="required" style="width:100%;"');
                        ?>
                        <!-- END -->
                    </div>
                </div>
                <div class="modal-footer" style="margin-top:0;">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
                    <button type="button" class="btn btn-primary" id="add_customere"> Actualizar </button>
                </div>
                   
             </form> 
            </div>
        </div>
    </div>





<input type="hidden" id="fflag" value="1">
<input type="hidden" id="exchange" value="<?= $Exchange->sell ?>">
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var avdSale=<?= $advance_sale?>;
	$(function () {

        var today = new Date();
        var min = new Date().setDate(today.getDate()-3);
        // var in_a_week   =new Date().setDate(today.getDate()+7);
        // var min_ex = new Date().setDate($("#date").val());

        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            maxDate: today,
            minDate: min//restingue fecha en la que se genera una factura - solicitud especial viccar
        });
    });
     // HH:mm
</script>
<script src="<?= $assets ?>dist/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/pages/sales.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/sales-customers.js" type="text/javascript"></script>
<script type="text/javascript">
    var spoitems = {};
    	var plastic_bags='<?=$plastic_bags;?>';
        var lang = new Array();
        lang['code_error'] = '<?= lang('code_error'); ?>';
        lang['r_u_sure'] = '<?= lang('r_u_sure'); ?>';
        lang['no_match_found'] = '<?= lang('no_match_found'); ?>';
        lang['products_not_found'] = '<?= lang('products_not_found') ?>';
        lang['sale_not_less'] = '<?= lang('sale_not_less') ?>';
        lang['verify_ruc'] = '<?= lang('verify_ruc') ?>';
    	lang['verify_dniRuc'] = '<?= lang('verify_dniRuc') ?>';
        lang['verify_address'] = '<?= lang('verify_address') ?>';
    	lang['aviso_tax_ICBPER'] = '<?= lang('aviso_tax_ICBPER') ?>';


</script>
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

    });
</script>
<!--******TRJ044 - ALEXANDER ROCA - 13/05/2019********-->
<script language="javascript">
$(document).ready(function() {

  $('form').keypress(function(e){   
    if(e == 13){
      return false;
    }
  });

  $(document).on('keypress','input',function(e){
    if(e.which == 13){
      return false;
    }
  });
  /*$('input').keypress(function(e){
    if(e.which == 13){
      return false;
    }
  });*/

});
</script>
<!--******TRJ044 - ALEXANDER ROCA - 13/05/2019********-->

