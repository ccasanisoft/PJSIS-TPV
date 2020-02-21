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
                        <?php $attrib = array('class' => 'validation', 'role' => 'form', 'id' => 'form_referralGuide',  'name' => 'form_referralGuide');
                        echo form_open("referral_guide/add", $attrib); ?>
                        <div class="row">
						
						<label for="">DATOS GENERALES:</label>
                            <div class="col-md-12">
                                <div class="col-sm-5">
                                    <label for="">Motivo de traslado:</label>
                                    <div id="cont_input_type_NC">
										<?php echo form_dropdown('motive_GR', $motive_GR, '0', 'class="form-control" id="motive_GR" onclick="javascript:capturaNameSelect();"');?>
										</div>
									<input type="hidden" name="name_motive_GR" id="name_motive_GR" value="0"  />
                                </div>
								<div class="col-sm-4">
                                    <label for="">Tipo de transporte:</label>
                                    <div id="cont_input_type_NC">
										<?php echo form_dropdown('tipoTransporte_GR', $tipoTransporteList, '0', 'class="form-control" id="tipoTransporte_GR"');?>
										</div>
                                </div>
                                
                            </div>
							<div class="col-md-12" style="margin-top:10px">
							<div class="col-sm-7">
                                <label for="">Nota:</label>
                                    <div>
                                        <?php
										    $data = array('type' => 'text','name' => 'nota_GR','id' => 'nota_GR','class' => 'form-control', 'placeholder' => 'Ejemplo:Mensaje con indicaciones sobre el trsnporte de productos');
										    echo form_input($data); 
										?>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                <label for="">Fecha de inicio de traslado:</label>
                                    <div>
                                        <?php
										    $data = array('type' => 'date','name' => 'fecha_inicio_trasladoGR','class' => 'form-control','id' => 'fecha_inicio_trasladoGR');
										    echo form_input($data); 
										?>
                                    </div>
                                </div>
							</div>

                            <div class="col-md-12" style="margin-top:10px">    
                                <div class="col-sm-4">
                                <label for="">Nombre/Razon social de Receptor</label>
                                    <div>
                                        <?php
										    $data = array('type'  => 'text','name' => 'nameReceptor','id' => 'nameReceptor','class' => 'form-control', 'placeholder' => 'Ejemplo:Empresa S.A.C');
										    echo form_input($data); 
										?>
                                    </div>
                                </div>
								<div class="col-sm-3">
                                    <label for="">Tipo - documento de identidad - Receptor:</label>
                                    <div>
										<?php echo form_dropdown('tipoDNI_Receptor', $type_document_ident, '0', 'class="form-control" id="tipoDNI_Receptor"');?>
										</div>
									</div>
                                <div class="col-sm-2">
                                <label for="">RUC/DNI de Receptor:</label>
                                    <div>
                                        <?php
										    $data = array('type'  => 'text','name'  => 'rucReceptor','id'    => 'rucReceptor','class' => 'form-control', 'placeholder' => 'Ejemplo: 00000000000');
										    echo form_input($data); 
										?>
                                    </div>
                                </div>
                            </div>
							<hr class="col-xs-12">
                            <div class="col-md-12">
                            <label for="">DATOS DEL TRANSPORTISTA:</label>
								<div class="form-group">
                                    <div class="col-sm-3">
                                        <label for="recepcionista">Placa del vehiculo:</label>
									    <div >
                                            <?php
										        $data = array('type' => 'text','name' => 'placa_GR','id' => 'placa_GR','class' => 'form-control', 'placeholder' => 'Ejemplo: F5U-597');
										        echo form_input($data); 
										    ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                    <label for="">Tipo de documento de identidad:</label>
                                    <div>
										<?php echo form_dropdown('tipoDNI_transportista', $type_document_ident, '0', 'class="form-control" id="tipoDNI_transportista"');?>
										</div>
									</div>
                                    <div class="col-sm-3">
                                        <label for="recepcionista">RUC/DNI de Transportista:</label>
									    <div >
                                            <?php
									        	$data = array('type'  => 'text','name'  => 'RucTransportista','id'    => 'RucTransportista','class' => 'form-control', 'placeholder' => 'Ejemplo:12345678');
										        echo form_input($data); 
									    	?>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="col-md-12" style="margin-top:10px">    
                                <div class="col-sm-9">
                                <label for="">Nombre/Razon social de transportista:</label>
                                    <div>
                                        <?php
										    $data = array('type'  => 'text','name' => 'nameTransportista','id' => 'nameTransportista','class' => 'form-control', 'placeholder' => 'Ejemplo:Alberto Benavides Quispe');
										    echo form_input($data); 
										?>
                                    </div>
                                </div>
                            </div>
							<hr class="col-xs-12">
                            <div class="col-md-12">
							<label for="">DATOS DEL PESO DE LOS PRODUCTOS:</label>
								<div class="form-group">
								<div class="col-sm-3">
                                    <label for="">Unidad de Medida:</label>
                                    <div id="cont_input_type_NC">
										<?php echo form_dropdown('tipo_medida_produc', $medida_GR, '1', 'class="form-control" id="tipo_medida_produc"');?>
										</div>
                                </div>
								<div class="col-sm-3">
                                        <label for="recepcionista">Peso total del conjunto de productos:</label>
									    <div >
                                            <?php
										        $data = array('type'  => 'text','name' => 'Peso_Total_produc','id' => 'Peso_Total_produc','class' => 'form-control', 'placeholder' => 'Ejemplo: 120');
										        echo form_input($data); 
										    ?>
                                        </div>
                                    </div>
									<div class="col-sm-3">
                                        <label for="recepcionista">Número de Paquetes:</label>
									    <div >
                                            <?php
										        $data = array('type'  => 'text','name'  => 'packetNumber','id'    => 'packetNumber','class' => 'form-control', 'placeholder' => 'Ejemplo: 5');
										        echo form_input($data); 
										    ?>
                                        </div>
                                    </div>
								</div>
							</div>
							<hr class="col-xs-12">
                            <div class="col-md-12">
                            <label for="">SALIDA:</label>
								<div class="form-group">
                                    <div class="col-sm-2">
                                    <label for="">Departamento:</label>
                                    <div id="cont_input_type_NC">
										<?php echo form_dropdown('depart_salida', $departamentList, '1', 'class="form-control" id="depart_salida" onclick="javascript:loadProvinceSalida();"');?>
										</div>

									</div>
									<div class="col-sm-3">
                                    <label for="">Provincia:</label>
										<div id="cont_input_type_NC">
										<select class="form-control" name="provin_salida" id="provin_salida" onclick="javascript:loadDistritoSalida();">
										<option>SELECCIONAR</option>
										</select>
										</div>
									</div>
									<div class="col-sm-4">
                                    <label for="">distrito:</label>
										<div id="cont_input_type_NC">
										<select class="form-control" name="distri_salida" id="distri_salida">
										<option value=0>SELECCIONAR</option>
										</select>
										</div>
									</div>
                                </div>
								<div class="col-md-9">    
                                <label for="">Dirección :</label>
                                    <div>
                                        <?php
										    $data = array('type'  => 'text','name' => 'direcc_salida','id' => 'direcc_salida','class' => 'form-control', 'placeholder' => 'Ejemplo: Av. los manglares 1035');
										    echo form_input($data); 
										?>
                                    </div>
                            </div>
                            </div>
							<hr class="col-xs-12">
                            <div class="col-md-12">
                            <label for="">LLEGADA:</label>
								<div class="form-group">
                                    <div class="col-sm-2">
                                    <label for="">Departamento:</label>
                                    <div id="cont_input_type_NC">
										<?php echo form_dropdown('depart_llegada', $departamentList, '1', 'class="form-control" id="depart_llegada" onclick="javascript:loadProvinceLlegada();"');?>
										</div>
									</div>
									<div class="col-sm-3">
                                    <label for="">Provincia:</label>
										<div id="cont_input_type_NC">
										<select class="form-control" name="provin_llegada" id="provin_llegada" onclick="javascript:loadDistritoLlegada();">
										<option>SELECCIONAR</option>
										</select>
										</div>
									</div>
									<div class="col-sm-4">
                                    <label for="">Distrito:</label>
										<div id="cont_input_type_NC">
										<select class="form-control" name="distri_llegada" id="distri_llegada">
										<option value=0>SELECCIONAR</option>
										</select>
										</div>
									</div>
                                </div>
								<div class="col-md-9">    
                                <label for="">Dirección :</label>
                                    <div>
                                        <?php
										    $data = array('type'  => 'text','name' => 'direcc_llegada','id' => 'direcc_llegada','class' => 'form-control', 'placeholder' => 'Ejemplo: jr. los robles 147');
										    echo form_input($data); 
										?>
                                    </div>
                            </div>
                            </div>
                            </div>
                          <br/><br/>
                            <div class="col-md-10" style="border-top: 1px solid #337ab7; border-bottom: 1px solid #337ab7; border-left: 1px solid #337ab7; border-right: 1px solid #337ab7; padding-bottom: 10px">
                                &nbsp;

                                <div style="padding-left:10px; padding-right:10px" class="form-group ">
                                     <input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>" id="add_item" class="form-control">
                                </div>
                                <div style="padding-left:10px; padding-right:10px" class="row">
                                    <div >
                                        <div class="table-responsive">
                                            <table id="poTable" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr class="active">
                                                        <th><?= lang('product'); ?></th>
														<th class="col-xs-1"><?= lang('quantity'); ?></th>	
														<th class="col-xs-1"><?= lang('unit_of_measurement'); ?></th>
                                                        <th style="width:25px;"><i class="fa fa-trash-o"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="poTableB">
                                                    <!--<tr>
													    <td colspan="6"><?= lang('add_product_by_searching_above_field'); ?></td>
                                                    </tr>-->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-top:10px">
                                <div class="form-group">
                                    <?= form_submit('add_GR', 'Generar Guia De Remision', 'class="btn btn-primary" id="add_GR" '); ?>
                                    <button type="button" id="reset" class="btn btn-danger"><?= lang('reset'); ?></button>
                                </div>
                            </div>
                         </div>
                         <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>                      
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="<?= $assets ?>dist/js/pages/Referral_guide.js" type="text/javascript"></script>

<?php if($sale_id != 0){ ?>
<script>
var sale_id = <?=$sale_id;?>;
</script>
<script src="<?= $assets ?>dist/js/pages/Referral_guide_saleItems.js" type="text/javascript"></script>
<?php } ?>