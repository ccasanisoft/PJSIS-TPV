<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
<!--script para consumir api de sunat-->
<script>
function obtenerDatos(){
  var ruc=document.getElementById('cf2').value;
  if (ruc>9999999999) {
    $('#rucalert2').hide('fad');
      $.ajax({
        type:'get',
          url: 'https://cors-anywhere.herokuapp.com/http://api.ateneaperu.com/api/Sunat/Ruc',
          data:{'sNroDocumento':ruc},
        success: function(datos) {
        $('#cargando').hide('fade');
           if (datos.success===true) {
              $('#rucalert').hide('fade');
              document.getElementById('name').value = datos.nombre_o_razon_social; 
              document.getElementById('cdireccion').value = datos.direccion_completa;            
           }else{
              $('#rucalert').show('fade');
            }
        },
        error: function() {
          console.log("no se encontro ningun registro");
        }, 
        beforeSend: function(){
        $('#cargando').show('fade');         
        },
    });
  }else{
    $('#rucalert2').show('fad');
  }
}


</script>
<!-------------------------------------------------------------->
<!--estilos para que los div se comporten como radio button-->
<style type="text/css">
  .radio-group{
    position: relative;
}

.radio{
    display:inline-block;
    width:15px;
    height: 15px;
    border-radius: 100%;
    background-color:lightblue;
   border: 5px solid lightblue;
    cursor:pointer;
    margin: 2px 0; 
}

.radio.selected{
    border-color:black;
}
</style>
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->


<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
				</div>
				<div class="box-body">
					<?php echo form_open("customers/add");?>
					<div class="col-md-12">
						<div id="rucalert" class="alert alert-danger" style="display:none;">El Ruc Ingresado No Ha Sido Encontrado</div>
						<div id="rucalert2" class="alert alert-danger" style="display:none;">Ruc cuenta con 11 digitos</div>
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
						<div class="form-group col-md-12">
							<label class="control-label" for="type_persona"><?= $this->lang->line("type_person"); ?></label>

							<div class="radio-group">
     							 <div class='radio' id="rPersona1"style="border: 5px solid black;"name="persona" data-value="<?= $this->lang->line("person1");?>" onclick="javascript:cambiarDatos(this.form,this);"></div>&nbsp;&nbsp;&nbsp;&nbsp;<label><?= $this->lang->line("person1"); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
      							 <div class='radio' id="rPersona2"name="persona" data-value="<?= $this->lang->line("person2");?>" onclick="javascript:cambiarDatos1(this.form,this);"></div><label>&nbsp;&nbsp;&nbsp;&nbsp;<?= $this->lang->line("person2"); ?></label>
     							 
     					    </div>
							<div class="custom-control custom-radio custom-control-inline">
								<!--<?= form_radio('person', set_value('person1'),'id="person1"');?>-->
								<label class="control-label" for="type_person">
							</div>
								 <div class="custom-control custom-radio custom-control-inline">
								 	<input type="radio" checked="true" name="person" id="person1" value="1">
								 	<input type="radio" name="person" id="person2" value="2">
									<!--<?= form_radio('person', set_value('person2'),'class="" id="person2"');?>-->		
								</div>
						</div>

						 <div class="form-group col-sm-4" >
                            <?= lang('document_type', 'document_type'); ?>
                            <?php
                           		 $ctv1[null] = lang("select")." ".lang("document_type");

                            	foreach($document_type as $doc_type) {
                                $ctv1[$doc_type->id] = $doc_type->document_type;
                            	}
                            ?>
              			 <?= form_dropdown('document_type', $ctv1, set_value("type",$customer->document_type), 'class="form-control tip select2" id="document_type"  style="width:100%;"'); ?>
                        </div>
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
						<div class="form-group col-md-3">
							<label class="control-label" for="cf1"><?= $this->lang->line("ccf1"); ?></label>
							<?= form_input('cf1', set_value('cf1'), 'class="form-control input-sm" id="cf1"'); ?>
						</div>
						<!--*****TRJ035 - RENATO - 13/05/2019*********-->
						<div class="form-group row">							
							<div class="col-md-4">						
								<label class="control-label" for="cf2"><?= $this->lang->line("ccf2"); ?> </label>	
								<div class="row">	
									<div class="col-md-8">
										<?= form_input('cf2', set_value('cf2'), 'class="form-control input-sm" id="cf2" maxlength="11"');?>
									</div>
									<div type="conruc" onclick="obtenerDatos()" class="btn btn-info btn-sm glyphicon glyphicon-ok" tabindex="0" data-toggle="tooltip" title="Consulta Sunat">									
									</div>
									<div id="cargando" class="col-md-2" style="display:none;"><img style="width: 30px;height:auto;" src="https://pa1.narvii.com/6558/d6738388bae69543478eb78d0545bf0475ef05a6_hq.gif"></div>

								</div>
							</div>
						</div>
						<!--*****TRJ035 - RENATO - 13/05/2019*********-->
						<div class="form-group col-md-11">
							<label class="control-label" for="code" id="nombre-persona"><?= $this->lang->line("name"); ?></label>
							<?= form_input('name', set_value('name'), 'class="form-control input-sm" id="name"'); ?>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group col-md-11">
									<label class="control-label" for="code">
										<?= lang("address"); ?>
									</label>
									<?= form_input('direccion', '', 'class="form-control input-sm kb-text" id="cdireccion"'); ?>
								</div>
							</div>
						</div>
						
						<div class="form-group col-md-3">
							<label class="control-label" for="phone"><?= $this->lang->line("phone"); ?></label>
							<?= form_input('phone', set_value('phone'), 'class="form-control input-sm" id="phone"');?>
						</div>
						<div class="form-group col-md-8">
							<label class="control-label" for="email_address"><?= $this->lang->line("email_address"); ?></label>
							<?= form_input('email', set_value('email'), 'class="form-control input-sm" id="email_address"'); ?>
						</div>
                       <div class="form-group col-md-11"> 
             			  <?= lang('customers_type', 'customers_type'); ?>
               			 <?php
               				 $ctv[] = lang("select")." ".lang("customers_type");

               				 foreach($customer_type as $cust_type) {
                  				  $ctv[$cust_type->id] = $cust_type->customers_type;
               				 }
              			  ?>
              			 <?= form_dropdown('customer_type', $ctv, set_value("type",$customer->customers_type_id), 'class="form-control tip select2" id="customer_type"  required="required" style="width:100%;"'); ?>
            		</div>		

						<?php switch($this->session->userdata('negocio')){
							case 0: ?>
								<?php break;
							case 1: ?>
								<div class="form-group">
									<label class="control-label" for="custom_field_1"><?= $this->lang->line("digemid"); ?></label>
									<?= form_input('custom_field_1', set_value('custom_field_1'), 'class="form-control input-sm" id="custom_field_1"'); ?>
								</div>
								<?php break;
						} ?>

						<div class="form-group col-md-4">
							<?php echo form_submit('add_customer', $this->lang->line("add_customer"), 'class="btn btn-primary"');?>
						</div>
					</div>
					<?php echo form_close();?>
				</div>
			</div>
		</div>
	</div>
<!--******TRJ035 - ALEXANDER ROCA - 13/05/2019********-->
<script language="javascript">
$(document).ready(function() {

  $('form').keypress(function(e){   
    if(e == 13){
      return false;
    }
  });

  $('input').keypress(function(e){
    if(e.which == 13){
      return false;
    }
  });

});
</script>
<!--******TRJ035 - ALEXANDER ROCA - 13/05/2019********-->
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
<script>
$('.radio-group .radio').click(function(){
    $(this).parent().find('.radio').removeClass('selected');
    $(this).addClass('selected');
    var val = $(this).attr('data-value');
    //alert(val);
    $(this).parent().find('input').val(val);
});
function cambiarDatos(form,radio){
	var vis=(radio.checked) ? false : true;
	document.querySelector('#nombre-persona').innerText = 'Nombre';
	document.getElementById('document_type').disabled=false;
	document.getElementById('cf1').readOnly=false;
    document.getElementById('person1').checked=true;
	document.getElementById('rPersona1').style.border="thick  solid black";
  
 
	
}
function cambiarDatos1(form,radio){

	document.querySelector('#nombre-persona').innerText = 'Razon Social';
	document.getElementById('document_type').disabled=true;
	document.getElementById('document_type').value=6;
	document.getElementById('cf1').readOnly=true;
	document.getElementById('cf2').readOnly=false;
	document.getElementById('cf1').value="";
	document.getElementById('person2').checked=true;
	document.getElementById('rPersona1').style.border="thick  solid lightblue";
	//document.getElementById('document_type').selectedIndex=0;

	
}



</script>
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
</section>
