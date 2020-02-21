<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title"><?= lang('update_info'); ?></h3>
        </div>
        <div class="box-body">
          <?php echo form_open("suppliers/edit/".$supplier->id);?>

          <div class="col-md-6">
			<!--******TRJ035 - ALEXANDER ROCA - 13/05/2019********-->
            <div class="row form-group">
			<div class="col-sm-12">
				<label class="control-label" for="cf1"><?= $this->lang->line("scf1"); ?></label>
				<div class="row">
					<div class="col-8 col-sm-11">
					<?= form_input('cf1', set_value('cf1', $supplier->cf1), 'class="form-control input-sm" id="cf1"'); ?>
					</div>
					<div type="conruc" onclick="obtenerDatos()" class="btn btn-info btn-sm glyphicon glyphicon-ok" tabindex="0" data-toggle="tooltip" title="Consulta Sunat"></div>
				</div>
			</div>
			</div>
			<!--******TRJ035 - ALEXANDER ROCA - 13/05/2019********-->
			
            <div class="form-group">
              <label class="control-label" for="code"><?= $this->lang->line("name"); ?></label>
              <?= form_input('name', set_value('name', $supplier->name), 'class="form-control input-sm" id="name"'); ?>
            </div>

            <div class="form-group">
              <label class="control-label" for="email_address"><?= $this->lang->line("email_address"); ?></label>
              <?= form_input('email', set_value('email', $supplier->email), 'class="form-control input-sm" id="email_address"'); ?>
            </div>

            <div class="form-group">
              <label class="control-label" for="phone"><?= $this->lang->line("phone"); ?></label>
              <?= form_input('phone', set_value('phone', $supplier->phone), 'class="form-control input-sm" id="phone"');?>
            </div>

            <div class="form-group">
				<label class="control-label" for="cf2"><?= $this->lang->line("scf2"); ?></label>
				<?= form_input('cf2', set_value('cf2', $supplier->cf2), 'class="form-control input-sm" id="cf2"');?>
              
            </div>
			
            <?php switch($this->session->userdata('negocio')){
							case 0: ?>
								<?php break;
							case 1: ?>
                <div class="form-group">
                  <label class="control-label" for="custom_field_1"><?= $this->lang->line("digemid"); ?></label>
                  <?= form_input('custom_field_1', set_value('custom_field_1', $supplier->custom_field_1), 'class="form-control input-sm" id="custom_field_1"');?>
                </div>
								<?php break;
						} ?> 


            <div class="form-group">
              <?php echo form_submit('edit_supplier', $this->lang->line("edit_supplier"), 'class="btn btn-primary"');?>
            </div>
          </div>
          <?php echo form_close();?>
        </div>
      </div>
    </div>
  </div>
<script>
function obtenerDatos(){
	
	var ruc=document.getElementById("cf1").value;
	
	let url = `https://cors-anywhere.herokuapp.com/http://api.ateneaperu.com/api/Sunat/Ruc?sNroDocumento=` + ruc;
	
	const api = new XMLHttpRequest();
	api.open('GET', url, true);
	api.send();
	
	if(ruc > 9999999999)
	{$('#rucalert2').hide('fade')
	api.onreadystatechange = function(){
		
		if(this.status == 200 && this.readyState == 4){
			
	     	let datos = JSON.parse(this.responseText);
			
			if(jQuery.isEmptyObject(datos.ruc)){$('#rucalert').show('fade')}else{
			$('#rucalert').hide('fade');
			document.getElementById('name').value = datos.nombre_o_razon_social; 
			document.getElementById('cf2').value = datos.direccion_completa;
			}

			}
		
	}		}else{$('#rucalert2').show('fade')}
}
</script>
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
</section>
