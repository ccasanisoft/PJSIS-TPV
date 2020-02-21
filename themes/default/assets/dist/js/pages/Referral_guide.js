var datoslist=[];

$('#form_referralGuide').submit(function (evt) {
    
	var sensor=0;
	var strgMenss="Los siguientes campos y condiciones son obligatorios: \n";
	
	var motive_GR = document.getElementById("motive_GR").value;
	if(motive_GR ==0){
		strgMenss +=" * Motivo de translado \n";
		sensor=1;
	}
	
	var tipoTransporte_GR = document.getElementById("tipoTransporte_GR").value;
	if(tipoTransporte_GR ==0){
		strgMenss +=" * Tipo de transporte \n";
		sensor=1;
	}else{
		
		if(tipoTransporte_GR ==2){
		//strgMenss +=" * Tipo de transporte \n";
		//sensor=1;
			var tipoDNI_transportista = document.getElementById("tipoDNI_transportista").value;
			if(tipoDNI_transportista ==0){
				strgMenss +=" * Tipo de documento de identidad - Transportista \n";
				sensor=1;
			}
			
			var RucTransportista = document.getElementById("RucTransportista").value;
			if(RucTransportista ==""){
				strgMenss +=" * RUC/DNI de Transportista \n";
				sensor=1;
			}
			
			var nameTransportista = document.getElementById("nameTransportista").value;
			if(nameTransportista ==""){
				strgMenss +=" * Nombre/Razon social de Transportista \n";
				sensor=1;
			}
			
			var placa_GR = document.getElementById("placa_GR").value;
			if(placa_GR ==""){
				strgMenss +=" * Placa del vehiculo \n";
				sensor=1;
			}
		
		}
		
	}
	
	/*var packetNumber = document.getElementById("packetNumber").value;
	if(packetNumber ==""){
		strgMenss +=" * Número de Paquetes \n";
		sensor=1;
	}*/
	
	/*var nota_GR = document.getElementById("nota_GR").value;
	if(nota_GR ==""){
		strgMenss +=" * Nota \n";
		sensor=1;
	}*/
	
	var fecha_inicio_trasladoGR = document.getElementById("fecha_inicio_trasladoGR").value;
	if(fecha_inicio_trasladoGR ==""){
		strgMenss +=" * Fecha inicio de Translado \n";
		sensor=1;
	}
	
	var nameReceptor = document.getElementById("nameReceptor").value;
	if(nameReceptor ==""){
		strgMenss +=" * Nombre/Razon social de Receptor \n";
		sensor=1;
	}
	
	var tipoDNI_Receptor = document.getElementById("tipoDNI_Receptor").value;
	if(tipoDNI_Receptor ==0){
		strgMenss +=" * Tipo - documento de identidad - Receptor \n";
		sensor=1;
	}
	
	var rucReceptor = document.getElementById("rucReceptor").value;
	if(rucReceptor ==""){
		strgMenss +=" * RUC/DNI de Receptor \n";
		sensor=1;
	}
	
	var Peso_Total_produc = document.getElementById("Peso_Total_produc").value;
	if(Peso_Total_produc ==""){
		strgMenss +=" * Peso total del conjunto de productos \n";
		sensor=1;
	}
	
	var distri_salida = document.getElementById("distri_salida").value;
	if(distri_salida ==0){
		strgMenss +=" * Distrito de salida \n";
		sensor=1;
	}
	
	var direcc_salida = document.getElementById("direcc_salida").value;
	if(direcc_salida ==""){
		strgMenss +=" * Dirección de salida \n";
		sensor=1;
	}
	
	var distri_llegada = document.getElementById("distri_llegada").value;
	if(distri_llegada ==0){
		strgMenss +=" * Distrito de llegada \n";
		sensor=1;
	}
	
	var direcc_llegada = document.getElementById("direcc_llegada").value;
	if(direcc_llegada ==""){
		strgMenss +=" * Dirección de llegada \n";
		sensor=1;
	}
	
	var items_validar=document.getElementsByClassName('quantityProduct');
	if(items_validar.length <=0){
		strgMenss +=" * Se debe tener por lo menos un item \n";
		sensor=1;
	}else{
	
		for(var i=0; i<items_validar.length; i++){
					if(items_validar[i].value <= 0){
						sensor=1;
						strgMenss +=" * Las cantidades de los items no pueden ser 0 o menos \n";
						break;
					}
					
						
		}
	}
	
	if(sensor==1){
		evt.preventDefault();
		//alert(items_validar.length);
		alert(strgMenss);
		document.getElementById("add_GR").className = "btn btn-primary";
		document.getElementById('add_GR').disabled=false;
	}
	
	
});
document.getElementById("provin_salida").disabled=true;
document.getElementById("distri_salida").disabled=true;

function loadProvinceSalida() {
	
	$('body').on('change', '#depart_salida', function(){
		
		var depart_salida = $("#depart_salida option:selected").text();
		
		$.ajax({
						type: "get",
						url: base_url+'Referral_guide/search_provincia/'+depart_salida,
						dataType: "json",
						success: function (data) {
							document.getElementById("provin_salida").disabled=false;
							document.getElementById("distri_salida").disabled=true;

							$('option', '#provin_salida').remove();
							$('option', '#distri_salida').remove();
							$("#provin_salida").append('<option value="0">SELECCIONAR</option>');
							$("#distri_salida").append('<option value="0">SELECCIONAR</option>');
							$.each(data,function(key, registro) {
								$("#provin_salida").append('<option value='+registro.Provin+'>'+registro.Provin+'</option>');
							  });
						}
					});
		
	});
}

function loadDistritoSalida() {
	
	$('body').on('change', '#provin_salida', function(){
		
		var provin_salida = $("#provin_salida option:selected").text();
		var depart_salida = $("#depart_salida option:selected").text();
		
		
		$.ajax({
						type: "get",
						url: base_url+'Referral_guide/search_Distrito/'+provin_salida+'/'+depart_salida,
						dataType: "json",
						success: function (data) {
                         document.getElementById("distri_salida").disabled=false;
							$('option', '#distri_salida').remove();
							$("#distri_salida").append('<option value="0">SELECCIONAR</option>');
							$.each(data,function(key, registro) {
								console.log(registro)
								$("#distri_salida").append('<option value='+registro.ubigeo+'>'+registro.distrito+'</option>');
							  });
						}
					});
		
	});
}



document.getElementById("provin_llegada").disabled=true;
document.getElementById("distri_llegada").disabled=true;

function loadProvinceLlegada() {
	
	$('body').on('change', '#depart_llegada', function(){
		
		var depart_llegada = $("#depart_llegada option:selected").text();
		
		
		$.ajax({
						type: "get",
						url: base_url+'Referral_guide/search_provincia/'+depart_llegada,
						dataType: "json",
						success: function (data) {

							document.getElementById("provin_llegada").disabled=false;
							document.getElementById("distri_llegada").disabled=true;
							$('option', '#provin_llegada').remove();
							$('option', '#distri_llegada').remove();
							$("#provin_llegada").append('<option value="0">SELECCIONAR</option>');
							$("#distri_llegada").append('<option value="0">SELECCIONAR</option>');
							$.each(data,function(key, registro) {
								$("#provin_llegada").append('<option value='+registro.Provin+'>'+registro.Provin+'</option>');
							  });
						}
					});
		
	});
}

function loadDistritoLlegada() {
	
	$('body').on('change', '#provin_llegada', function(){
		
		var provin_llegada = $("#provin_llegada option:selected").text();
		var depart_llegada = $("#depart_llegada option:selected").text();
		
		
		$.ajax({
						type: "get",
						url: base_url+'Referral_guide/search_Distrito/'+provin_llegada+'/'+depart_llegada,
						dataType: "json",
						success: function (data) {
							document.getElementById("distri_llegada").disabled=false;
							$('option', '#distri_llegada').remove();
							$("#distri_llegada").append('<option value="0">SELECCIONAR</option>');
							$.each(data,function(key, registro) {
								$("#distri_llegada").append('<option value='+registro.ubigeo+'>'+registro.distrito+'</option>');
							  });
						}
					});
		
	});
}

function capturaNameSelect(){
	$('body').on('change', '#motive_GR', function(){
		$("#name_motive_GR").val($("#motive_GR option:selected").text());
	});
}

function showLabel(event, ui) {
                //$('#cityLabel').text(ui.item.label);
				event.preventDefault();
				var productoCode=ui.item.label;
				var part1 = productoCode.split('(');
				var part2 = part1[1].split(')');
				var code =part2[0];
				var nameProduc =part1[0];
				//alert(part1[0]);
				//alert(part2[0]);
				$('#add_item').val("");
								/*Amador*/
								arraycode =[];
								var repetidos = {};
								for (var prop in datoslist) {                 
								arraycode.push(datoslist[prop]['code'])
								
								}
								arraycode.forEach(function(numero){
								repetidos[numero] = (repetidos[numero] || 0) + 1;
								
								});
							if(repetidos[code] > 1){
								console.log('re');
								document.getElementById('Inputcant_'+code).value++;
								

							}else{ /*end amador*/
								console.log('va');

				//********************
					var tr = document.createElement("tr");
					tr.id = "tr_"+code;
					document.getElementById("poTableB").appendChild(tr);
					//******************
					
					var name = document.createElement("td");
					var contenido_name = document.createTextNode(nameProduc+"("+code+")");
					name.appendChild(contenido_name);
					name.id = "name_"+code;
					document.getElementById("tr_"+code).appendChild(name);
					
					//*******************
					
					var cant = document.createElement("td");
					cant.id = "cant_"+code;
					document.getElementById("tr_"+code).appendChild(cant);
					
					//*******************
					
					var tipoUnit = document.createElement("td");
					var contenido_name = document.createTextNode("NIU");
					tipoUnit.appendChild(contenido_name);
					tipoUnit.id = "tipoUnit_"+code;
					document.getElementById("tr_"+code).appendChild(tipoUnit);
					
					//***************
					var action_td = document.createElement("td");
					action_td.id = "action_td_"+code;
					document.getElementById("tr_"+code).appendChild(action_td);
					
					var action_delete_div = document.createElement("div");
					action_delete_div.id = "action_delete_div_"+code;
					action_delete_div.className= "btn btn-danger trashJs";
					document.getElementById("action_td_"+code).appendChild(action_delete_div);
								
					var action_delete = document.createElement("i");
					action_delete.id = "action_"+code;
					action_delete.className= "fa fa fa-trash-o tip pointer spodel";
					document.getElementById("action_delete_div_"+code).appendChild(action_delete);
					
					//*******************
					
					var input_quantity = document.createElement("input");
					input_quantity.id = "Inputcant_"+code;
					input_quantity.setAttribute("name", "product_quantity[]");
					input_quantity.setAttribute("value", "1");
					input_quantity.setAttribute("class", "quantityProduct");
					document.getElementById("cant_"+code).appendChild(input_quantity);
					
					//***************************input hidden************************
								
								var form_div = document.createElement("div");
								form_div.id = "form_div";
								document.getElementById("form_referralGuide").appendChild(form_div);
								
								var input_div = document.createElement("div");
								input_div.id = "inputs_div_"+code;
								document.getElementById("form_div").appendChild(input_div);
								//**********************************************************
								var input_id = document.createElement("input");
								input_id.id = "product_id_"+code;
								input_id.setAttribute("type", "hidden");
								input_id.setAttribute("name", "product_id[]");
								input_id.setAttribute("value", 0);
								document.getElementById("inputs_div_"+code).appendChild(input_id);
								
								//***************************************************
								var input_quantity = document.createElement("input");
								input_quantity.id = "product_name_"+code;
								input_quantity.setAttribute("type", "hidden");
								input_quantity.setAttribute("name", "product_name[]");
								input_quantity.setAttribute("value", nameProduc);
								document.getElementById("inputs_div_"+code).appendChild(input_quantity);
								//**********************************************************
								var input_code = document.createElement("input");
								input_code.id = "product_id_"+code;
								input_code.setAttribute("type", "hidden");
								input_code.setAttribute("name", "product_code[]");
								input_code.setAttribute("value", code);
								document.getElementById("inputs_div_"+code).appendChild(input_code);
					
							}

}

/**************Amador***************/

function addlist(id,code){
	var newdata = {
		id:id,
		code:code
	};
	datoslist.push(newdata);
	localstore(datoslist);

}

function getlist(){
	var store = localStorage.getItem('listadata');
	if(store == null){
		datoslist =[];
	}else{
		datoslist = JSON.parse(store);
	}
	return datoslist;
}

function localstore(lista){
localStorage.setItem('listadata',JSON.stringify(lista));
}

/***************End Amador**********************/



window.onload = function() {
	$("#add_item").on("keyup", function() {
     var textSearch= $("#add_item").val();
	 
	 var urlSearch=base_url+'Referral_guide/search_Producto/'+textSearch;
	 //console.log(urlSearch);
		$( function() {
			$( "#add_item" ).autocomplete({
			  source: urlSearch,
			  minLength: 1,
              //focus: showLabel,
             select:function(event,ui){/*amador*/
			var productoCode=ui.item.label;
			var part1 = productoCode.split('(');
			var part2 = part1[1].split(')');
			var code =part2[0];
			var nameProduc =part1[0];
			var tr = document.getElementById('tr_'+code);
			var conta = 0;          
			addlist(Settings.item_addition++,code);
			showLabel(event,ui);
              }/*end amador*/
			});
		} );
		
		
	 
	});
}


$(document).on('click', '.trashJs', function (e) {
	
	var id_editar =$(this).attr('id');
	var res = id_editar.substring(18);
	$("#inputs_div_"+res).remove();
	$("#tr_"+res).remove();

	for(let i in datoslist){
		if(datoslist[i]['code'] == res){
			delete(datoslist[i]['code']);
		}
	}
	
	return false;
});




