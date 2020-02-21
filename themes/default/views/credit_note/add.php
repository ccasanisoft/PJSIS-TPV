<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                     <h3 class="box-title"><?= lang('enter_info'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <?php $attrib = array('class' => 'validation', 'role' => 'form', 'id' => 'form_noteCredit',  'name' => 'form_noteCredit');
                        echo form_open("credit_note/add", $attrib); ?>
                        <div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="col-md-4" id="Select_motive_note_credit_div">
										<?= lang("type_note_credit", "type_note_credit"); ?>
										<div id="cont_input_type_NC">
										<?php echo form_dropdown('Select_motive_note_credit', $motive_NC, '1', 'class="form-control" id="Select_motive_note_credit"');?>
										</div>
									</div>
								
									<div class="col-md-3" id="Select_serie_div">
										<?= lang("number_series", "number_series"); ?>
										<div id="cont_input_numSerie">
										<?php echo form_dropdown('Select_serie', $num_series, '2', 'class="form-control" id="Select_serie"');?>
										</div>
									</div>
									<div class="col-md-3" id="correlative2_div">
										<?= lang("correlative2", "correlative2"); ?>
										<div id="cont_input_correlative2">
										<?php
										$data = array('type'  => 'number','name'  => 'correlative2','id'    => 'correlative2','class' => 'form-control', 'placeholder' => 'Ejemplo: 123');
										echo form_input($data); 
										?>
										</div>
									</div>
                                </div>
                            </div>
							<div class="col-md-12" style="margin-top: 10px;">
								<div class="form-group">
									<div class="col-md-10">
										<?= lang("sustenance", "sustenance"); ?>
										<?php echo form_input('sustenance', '', ' class="form-control" placeholder="Escriba la descripcion del motivo por el cual genera la nota de credito" id="sustenance"'); ?>
									</div>
								</div>
							</div>
							<div class="col-md-12" style="margin-top: 10px; display:none;" id="div_decuentoGlobal">
								<div class="form-group">
									<div class="col-md-4" id="input_decuentoGlobal">
										<?= lang("desct", "desct"); ?>
									</div>
								</div>
							</div>
                            <div class="col-md-12">
                                <div class="form-group">
									<div class="col-md-2">
										<?= form_submit('add_credit_note', lang('add_credit_note'), 'style="margin-top: 25px;" class="btn btn-primary" id="add_credit_note"'); ?>
										<div onclick="mostrarItems();" id="submit_NC_items" name="submit_NC_items" class="btn btn-success" style="display:none; margin-top: 10px;"><?= lang('submit_NC_items'); ?></div>
									</div>
                                </div>
                            </div>
                        </div>
						<div style="padding-left:10px; padding-right:10px;  margin-top: 10px; display:none;" class="row" id="tabla_items">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="poTable" name="poTable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr class="active">
                                                    <th><?= lang('product'); ?></th>
                                                    <th class="col-xs-2" id="header_quantity"><?= lang('quantity'); ?></th>
													<th class="col-xs-2" id="header_value_unit"><?= lang('value_unit'); ?></th>
                                                    <th class="col-xs-2"><?= lang('tax_method'); ?></th>
													<th class="col-xs-2" id="header_importe"><?= lang('importe'); ?></th>
													<th class="col-xs-2"><?= lang('action'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody name="poTableB" id="poTableB">
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        <?php echo form_close(); ?>
                    </div>
                    <div class="clearfix"></div>
					<div class="col-md-12">
					<div class="col-md-6">
                        <div class="form-group">
							<div class="col-md-6">
								<div onclick="limpiarDatos()" id="submit_limpiar" name="submit_limpiar" class="btn btn-primary" style="display:none; margin-top: 10px;"><?= lang('clean'); ?></div>
							</div>
                        </div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<div class="col-md-6">
								<div onclick="ejecutarFormAddNC()" id="submit_agregar_NC" name="submit_agregar_NC" class="btn btn-success" style="display:none; margin-top: 10px;"><?= lang('add_credit_note'); ?></div>
							</div>
						</div>
					</div>
					</div>
                </div>
            </div>
        </div>
		
    </div>
	
<div class="modal" data-easein="flipYIn" id="tsModal" tabindex="-1" role="dialog" aria-labelledby="tsModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="tsModalLabel"><?= lang('edit_item'); ?></h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin-top:10px;">
					<div class="col-md-6">
						<label><?= lang('product'); ?></label>
					</div>
					<div class="col-md-6">
						<label id="name_modal_item"></label>
						<input type='hidden' class='form-control input-sm kb-pad' id='name_product_modal_item' value=''>
					</div>
				</div>
				<div class="row" style="margin-top:10px;" id="modal_quantity">
					<div class="col-md-6">
						<label><?= lang('quantity'); ?></label>
					</div>
					<div class="col-md-6">
						<input type='text' class='form-control input-sm kb-pad' id='quantity_modal_item' value=''>
					</div>
				</div>
				<div class="row" style="margin-top:10px;" id="modal_price">
					<div class="col-md-6">
						<label id="dato_value_unit"><?= lang('value_unit'); ?></label>
					</div>
					<div class="col-md-6">
						<input type='text' class='form-control input-sm kb-pad' id='value_unit_modal_item' value=''>
					</div>
				</div>
				<input type="hidden" id="id_item" name="id_item" value="">
				<!--<div class="row" style="margin-top:10px;">
					<div class="col-md-6">
						<label><?//= lang('importe'); ?></label>
					</div>
					<div class="col-md-6">
						<label id="import_modal_item"></label>
					</div>
				</div>-->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?=lang('close')?></button>
				<button type="button" id="updateItemTable" class="btn btn-primary btn-sm" onclick="actualizarItem()"><?= lang('update') ?></button>
			</div>
		</div>
	</div>
</div>

</section>
<script src="<?= $assets ?>dist/js/pages/note_credit.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>