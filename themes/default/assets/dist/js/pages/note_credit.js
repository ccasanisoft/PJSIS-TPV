
$(document).ready(function(){
	
	$('body').on('click', '#Select_motive_note_credit', function(){

		var cod = document.getElementById("Select_motive_note_credit").value;
		
		if(cod==5){
			// imagen = document.getElementById("add_credit_note");
			// padre = imagen.parentNode;
			// padre.removeChild(imagen);
				$("#poTableB").remove();
				$("#form_div").remove();
				$("#Descuento_global_comprobante").remove();
				document.getElementById('add_credit_note').type = 'hidden';
				document.getElementById("submit_NC_items").style.display = "block";
				document.getElementById("div_decuentoGlobal").style.display = "none";
				//document.getElementById("tabla_items").style.display = "block";
			
		}else{
			if(cod==7){
				$("#poTableB").remove();
				$("#form_div").remove();
				$("#Descuento_global_comprobante").remove();
				document.getElementById('add_credit_note').type = 'hidden';
				document.getElementById("submit_NC_items").style.display = "block";
				document.getElementById("div_decuentoGlobal").style.display = "none";
				//document.getElementById("tabla_items").style.display = "block";
				
			}else{
				
				if(cod==3){
					$("#poTableB").remove();
					$("#form_div").remove();
					$("#Descuento_global_comprobante").remove();
					document.getElementById('add_credit_note').type = 'hidden';
					document.getElementById("submit_NC_items").style.display = "block";
					document.getElementById("div_decuentoGlobal").style.display = "none";
					//document.getElementById("tabla_items").style.display = "block";
				}else{
					$("#poTableB").remove();
					$("#form_div").remove();
					$("#Descuento_global_comprobante").remove();
					document.getElementById("tabla_items").style.display = "none";
					document.getElementById("submit_NC_items").style.display = "none";
					document.getElementById('add_credit_note').type = 'submit';
					document.getElementById("submit_limpiar").style.display = "none";
					document.getElementById("submit_agregar_NC").style.display = "none";
					document.getElementById("div_decuentoGlobal").style.display = "none";
					if(cod==4){
						var input_DescGlobal = document.createElement("input");
						input_DescGlobal.id = "Descuento_global_comprobante";
						input_DescGlobal.setAttribute("type", "number");
						input_DescGlobal.setAttribute("class", "form-control");
						input_DescGlobal.setAttribute("name", "Descuento_global_comprobante");
						document.getElementById("input_decuentoGlobal").appendChild(input_DescGlobal);
						document.getElementById("div_decuentoGlobal").style.display = "block";
					}
				}
				
			}
		}
		
		
		
	});
	
});


 window.onload =function mostrarItems() {

	$(document).on('click', '#submit_NC_items', function (e) {
		
        var correlativo = document.getElementById("correlative2").value;
		var serie = document.getElementById("Select_serie").value;
        var url = base_url+"credit_note/mostrarItemsTB/";
		
		if(correlativo.localeCompare("")==0){
			alert('Se debe ingresar el correlativo');
		}else{
			$.ajax({
					type: 'get',
					url: url,
					data: {correlativoInvoice: correlativo, serieInvoice: serie},
					success: function (datos) {
						
						var result = $.parseJSON(datos);
						
						if(result[0]["id"] >0){
							
							document.getElementById("cont_input_numSerie").style.display = "none";
							document.getElementById("cont_input_type_NC").style.display = "none";
							document.getElementById("cont_input_correlative2").style.display = "none";
							
							//********bloqueo select typeNC*********
							var select_typeNC = document.getElementById("Select_motive_note_credit");
							//var value_select = select_typeNC.value; //El valor seleccionado
							var text_option_typeNC = select_typeNC.options[select_typeNC.selectedIndex].innerText;
							
							var label_select_typeNC = document.createElement("label");
							var contenido_label = document.createTextNode(text_option_typeNC);
							label_select_typeNC.appendChild(contenido_label);
							label_select_typeNC.id = "label_select_typeNC";
							label_select_typeNC.className= "form-control";
							document.getElementById("Select_motive_note_credit_div").appendChild(label_select_typeNC);
							//********bloqueo select typeNC*********
							
							//********bloqueo select NumSerie*********
							var select_typeNC = document.getElementById("Select_serie");
							//var value_select = select_typeNC.value; //El valor seleccionado
							var text_option_NumSerie = select_typeNC.options[select_typeNC.selectedIndex].innerText;
							
							var label_select_NumSerie = document.createElement("label");
							var contenido_NumSerie = document.createTextNode(text_option_NumSerie);
							label_select_NumSerie.appendChild(contenido_NumSerie);
							label_select_NumSerie.id = "label_select_NumSerie";
							label_select_NumSerie.className= "form-control";
							document.getElementById("Select_serie_div").appendChild(label_select_NumSerie);
							//********bloqueo select NumSerie*********
							
							//********bloqueo select correlativo*********
							var label_correlativo = document.createElement("label");
							var contenido_correlativo = document.createTextNode(correlativo);
							label_correlativo.appendChild(contenido_correlativo);
							label_correlativo.id = "label_correlativo";
							label_correlativo.className= "form-control";
							document.getElementById("correlative2_div").appendChild(label_correlativo);
							//********bloqueo select correlativo*********
							
							
							
							var tipo_NC = document.getElementById("Select_motive_note_credit").value;
							
							if(tipo_NC == 5){
								document.getElementById("header_quantity").style.display = "none";
								document.getElementById("header_value_unit").style.display = "none";
								document.getElementById("header_importe").innerHTML = "Descuento";
							}else{
								//document.getElementById("header_quantity").style.display = "block";
								//document.getElementById("header_value_unit").style.display = "block";
								//document.getElementById("header_importe").innerHTML = "Importe";
							}
							
							$("#poTableB").remove();
							
							$("#form_div").remove();
							
							var tbody = document.createElement("tbody");
							tbody.id = "poTableB";
							document.getElementById("poTable").appendChild(tbody);
							
							var form_div = document.createElement("div");
							form_div.id = "form_div";
							document.getElementById("form_noteCredit").appendChild(form_div);
							
							for(var cont=0; cont<result.length; cont++){
								
								var tr = document.createElement("tr");
								tr.id = "tr_"+result[cont]["id"];
								document.getElementById("poTableB").appendChild(tr);
								
								//********************************************NOMBRE*******

								var name = document.createElement("td");
								var contenido_name = document.createTextNode(result[cont]["name"]);
								name.appendChild(contenido_name);
								name.id = "name_"+result[cont]["id"];
								document.getElementById("tr_"+result[cont]["id"]).appendChild(name);
								
								if(tipo_NC == 5){}else{
								
									//**********************************************CANTIDAD*****

									var quantity = document.createElement("td");
									var contenido_quantity = document.createTextNode(result[cont]["cantidad"]);
									quantity.appendChild(contenido_quantity);
									quantity.id = "quantity_"+result[cont]["id"];
									document.getElementById("tr_"+result[cont]["id"]).appendChild(quantity);
									
									//*********************************************PRECIO******

									var unit_price = document.createElement("td");
									var contenido_unit_price = document.createTextNode(result[cont]["precio"]);
									unit_price.appendChild(contenido_unit_price);
									unit_price.id = "unit_price_"+result[cont]["id"];
									document.getElementById("tr_"+result[cont]["id"]).appendChild(unit_price);
								
								}
								
								//*********************************************IMPUESTO******
								
								var tax_m = document.createElement("td");
								var tipoImpuesto="";
								if(result[cont]["impuesto"]==0 || result[cont]["impuesto"]==1 ){
									tipoImpuesto="afecto (IGV ya incluido)";
								}
								if(result[cont]["impuesto"]==2){
									tipoImpuesto="inafecto";
								}
								if(result[cont]["impuesto"]==3){
									tipoImpuesto="exonerado";
								}
								var contenido_tax_m = document.createTextNode(tipoImpuesto);
								tax_m.appendChild(contenido_tax_m);
								tax_m.id = "tax_"+result[cont]["id"];
								document.getElementById("tr_"+result[cont]["id"]).appendChild(tax_m);
								
								//********************************************TOTAL*******

								var importe = document.createElement("td");
								var contenido_import = document.createTextNode(result[cont]["subtotal"]);
								importe.appendChild(contenido_import);
								importe.id = "importe_"+result[cont]["id"];
								document.getElementById("tr_"+result[cont]["id"]).appendChild(importe);
								
								//*********************botones edit y borrar******************************

								var action_td = document.createElement("td");
								action_td.id = "action_td_"+result[cont]["id"];
								document.getElementById("tr_"+result[cont]["id"]).appendChild(action_td);
								
								var action_div = document.createElement("div");
								action_div.id = "action_edit_div_"+result[cont]["id"];
								action_div.className= "btn btn-warning editarJs";
								document.getElementById("action_td_"+result[cont]["id"]).appendChild(action_div);
								
								var action = document.createElement("i");
								action.id = "action_"+result[cont]["id"];
								action.className= "fa fa-edit tip pointer spodel";
								document.getElementById("action_edit_div_"+result[cont]["id"]).appendChild(action);
								//***************
								var action_delete_div = document.createElement("div");
								action_delete_div.id = "action_delete_div_"+result[cont]["id"];
								action_delete_div.className= "btn btn-danger trashJs";
								document.getElementById("action_td_"+result[cont]["id"]).appendChild(action_delete_div);
								
								var action_delete = document.createElement("i");
								action_delete.id = "action_"+result[cont]["id"];
								action_delete.className= "fa fa fa-trash-o tip pointer spodel";
								document.getElementById("action_delete_div_"+result[cont]["id"]).appendChild(action_delete);
								
								//***************************input hidden************************
								
								var input_div = document.createElement("div");
								input_div.id = "input_div_"+result[cont]["id"];
								document.getElementById("form_div").appendChild(input_div);
								
								var input_id = document.createElement("input");
								input_id.id = "product_id_"+result[cont]["id"];
								input_id.setAttribute("type", "hidden");
								input_id.setAttribute("name", "product_id[]");
								input_id.setAttribute("value", result[cont]["id"]);
								document.getElementById("input_div_"+result[cont]["id"]).appendChild(input_id);
								
								if(tipo_NC == 5){
									
									var input_unit_price = document.createElement("input");
									input_unit_price.id = "product_subtotal_"+result[cont]["id"];
									input_unit_price.setAttribute("type", "hidden");
									input_unit_price.setAttribute("name", "product_subtotal[]");
									input_unit_price.setAttribute("value", result[cont]["subtotal"]);
									document.getElementById("input_div_"+result[cont]["id"]).appendChild(input_unit_price);
									
									var input_tax = document.createElement("input");
									input_tax.id = "product_tax_"+result[cont]["id"];
									input_tax.setAttribute("type", "hidden");
									input_tax.setAttribute("name", "product_tax[]");
									input_tax.setAttribute("value", result[cont]["impuesto"]);
									document.getElementById("input_div_"+result[cont]["id"]).appendChild(input_tax);
									
								}else{
									
									if(tipo_NC == 7){
								
										var input_unit_price = document.createElement("input");
										input_unit_price.id = "product_unit_price_"+result[cont]["id"];
										input_unit_price.setAttribute("type", "hidden");
										input_unit_price.setAttribute("name", "product_unit_price[]");
										input_unit_price.setAttribute("value", result[cont]["precio"]);
										document.getElementById("input_div_"+result[cont]["id"]).appendChild(input_unit_price);
									
										var input_quantity = document.createElement("input");
										input_quantity.id = "product_quantity_"+result[cont]["id"];
										input_quantity.setAttribute("type", "hidden");
										input_quantity.setAttribute("name", "product_quantity[]");
										input_quantity.setAttribute("value", result[cont]["cantidad"]);
										document.getElementById("input_div_"+result[cont]["id"]).appendChild(input_quantity);
										
										var input_tax = document.createElement("input");
										input_tax.id = "product_tax_"+result[cont]["id"];
										input_tax.setAttribute("type", "hidden");
										input_tax.setAttribute("name", "product_tax[]");
										input_tax.setAttribute("value", result[cont]["impuesto"]);
										document.getElementById("input_div_"+result[cont]["id"]).appendChild(input_tax);
										
									}else{
										if(tipo_NC == 3){
											var input_quantity = document.createElement("input");
											input_quantity.id = "product_name_"+result[cont]["id"];
											input_quantity.setAttribute("type", "hidden");
											input_quantity.setAttribute("name", "product_name[]");
											input_quantity.setAttribute("value", result[cont]["name"]);
											document.getElementById("input_div_"+result[cont]["id"]).appendChild(input_quantity);
										}
									}
								}
								
							}
						
						document.getElementById("tabla_items").style.display = "block";
						
						/*var elemento1 = document.getElementById("submit_NC_items");
						elemento1.className += " disabled";*/
						document.getElementById("submit_NC_items").style.display = "none";
						document.getElementById("submit_limpiar").style.display = "block";
						document.getElementById("submit_agregar_NC").style.display = "block";
						
					}else{
						alert("El comprobante no existe");
					}
					
                }
            });

		}

    });

}

$(document).on('click', '.editarJs', function (e) {
	
	var id_editar =$(this).attr('id');
	var res = id_editar.substring(16);
	//alert(res);
	
	var tipo_NC = document.getElementById("Select_motive_note_credit").value;
							
	if(tipo_NC == 5){
		
		document.getElementById("modal_quantity").style.display = "none";
		document.getElementById("modal_price").style.display = "block";
		var precio_item = document.getElementById('importe_'+res).innerHTML;
		document.getElementById('name_product_modal_item').type = 'hidden';
		$('#value_unit_modal_item').val(precio_item);
		
	}else{
		if(tipo_NC == 7){
			
			document.getElementById("modal_quantity").style.display = "block";
			document.getElementById("modal_price").style.display = "block";
			var cantidad_item = document.getElementById('quantity_'+res).innerHTML;
			$('#quantity_modal_item').val(cantidad_item);
			var precio_item = document.getElementById('unit_price_'+res).innerHTML;
			document.getElementById('name_product_modal_item').type = 'hidden';
			$('#value_unit_modal_item').val(precio_item);
			
		}else{
			
			document.getElementById("modal_quantity").style.display = "none";
			document.getElementById("modal_price").style.display = "none";
			document.getElementById('name_product_modal_item').type = 'text';
			
		}
	}
	
	
	$('#id_item').val(res);
	var name_item = document.getElementById('name_'+res).innerHTML;
	
	if(tipo_NC == 3){
		document.getElementById("name_modal_item").style.display = "none";
		$('#name_product_modal_item').val(name_item);
	
	}else{
		
		
		document.getElementById("name_modal_item").innerHTML = name_item;
		var impuesto = document.getElementById("product_tax_"+res).value;
		var texto="";
		if(impuesto == 1 || impuesto == 0){
			texto="Valor unitario + IGV (sumado):";
		}else{
			texto="Valor unitario (no tiene IGV):";
		}
		document.getElementById("dato_value_unit").innerHTML = texto;
		
	}
	
	/*var importe_item = document.getElementById('importe_'+res).innerHTML;
    document.getElementById("import_modal_item").innerHTML = importe_item;*/

	
    $('#tsModal').modal({backdrop:'static'});
    return false;
	
});

$(document).on('click', '.trashJs', function (e) {
	
	var id_editar =$(this).attr('id');
	var res = id_editar.substring(18);
	$("#input_div_"+res).remove();
	$("#tr_"+res).remove();
	return false;
});

function actualizarItem(){
	
	var validar_cantidad=1;
	var validar_precio=1;
	var id_item = document.getElementById("id_item").value;
	var tipo_NC = document.getElementById("Select_motive_note_credit").value;
	
	
	if(tipo_NC == 3){
		var name = document.getElementById("name_product_modal_item").value;
	}else{
	
		var precio = document.getElementById("value_unit_modal_item").value;
		var impuesto = document.getElementById("product_tax_"+id_item).value;
					
		if(tipo_NC == 5){
			var precio_TB = document.getElementById('importe_'+id_item).innerHTML;
		}else{
			var cantidad_TB = document.getElementById('quantity_'+id_item).innerHTML;
			var cantidad = document.getElementById("quantity_modal_item").value;
			var precio_TB = document.getElementById('unit_price_'+id_item).innerHTML;
			
			if(parseFloat(cantidad) <= parseFloat(cantidad_TB) && (parseFloat(cantidad) > 0)){
				validar_cantidad=1;
			}else{
				validar_cantidad=0;
			}
		}
		
		if((parseFloat(precio) <= parseFloat(precio_TB)) && (parseFloat(precio) > 0)){
			validar_precio=1;
		}else{
			validar_precio=0;
		}
		
	}
	
	if(validar_precio == 1){
		
		if(validar_cantidad == 1){
			
			if(tipo_NC == 3){
				
				if(name.length > 0){
					document.getElementById('name_'+id_item).innerHTML = name;
				}else{
					alert("el nombre del Producto o servicio no puede estar vacio.");
				}
				
			}else{
			
				if(tipo_NC == 5){
					document.getElementById('importe_'+id_item).innerHTML = precio;
				}else{
					
					document.getElementById('unit_price_'+id_item).innerHTML = precio;
					document.getElementById('quantity_'+id_item).innerHTML = cantidad;
					document.getElementById('importe_'+id_item).innerHTML = (parseFloat(precio) * parseFloat(cantidad));
					
				}
				
			}
	
		
		//$('#input_div_'+id_item+' #product_quantity').val(cantidad);
		
		$("#input_div_"+id_item).remove();
		
		var input_div = document.createElement("div");
		input_div.id = "input_div_"+id_item;
		document.getElementById("form_div").appendChild(input_div);
						
		var input_id = document.createElement("input");
		input_id.id = "product_id_"+id_item;
		input_id.setAttribute("type", "hidden");
		input_id.setAttribute("name", "product_id[]");
		input_id.setAttribute("value", id_item);
		document.getElementById("input_div_"+id_item).appendChild(input_id);
								
		
		if(tipo_NC == 3){
			
			var input_quantity = document.createElement("input");
			input_quantity.id = "product_name_"+id_item;
			input_quantity.setAttribute("type", "hidden");
			input_quantity.setAttribute("name", "product_name[]");
			input_quantity.setAttribute("value", name);
			document.getElementById("input_div_"+id_item).appendChild(input_quantity);
			
		}else{
		
			if(tipo_NC == 5){
				var input_unit_price = document.createElement("input");
				input_unit_price.id = "product_subtotal_"+id_item;
				input_unit_price.setAttribute("type", "hidden");
				input_unit_price.setAttribute("name", "product_subtotal[]");
				input_unit_price.setAttribute("value", precio);
				document.getElementById("input_div_"+id_item).appendChild(input_unit_price);
			}else{
				
				var input_unit_price = document.createElement("input");
				input_unit_price.id = "product_unit_price_"+id_item;
				input_unit_price.setAttribute("type", "hidden");
				input_unit_price.setAttribute("name", "product_unit_price[]");
				input_unit_price.setAttribute("value", precio);
				document.getElementById("input_div_"+id_item).appendChild(input_unit_price);
				
				var input_quantity = document.createElement("input");
				input_quantity.id = "product_quantity_"+id_item;
				input_quantity.setAttribute("type", "hidden");
				input_quantity.setAttribute("name", "product_quantity[]");
				input_quantity.setAttribute("value", cantidad);
				document.getElementById("input_div_"+id_item).appendChild(input_quantity);
			}
			
			var input_tax = document.createElement("input");
			input_tax.id = "product_tax_"+id_item;
			input_tax.setAttribute("type", "hidden");
			input_tax.setAttribute("name", "product_tax[]");
			input_tax.setAttribute("value", impuesto);
			document.getElementById("input_div_"+id_item).appendChild(input_tax);
		
		}
		
		
		
		$('#tsModal').modal('hide');
		
		}else{
			alert("la cantidad de este producto debe ser igual o menor a la que tenie actualmente");
		}
	
	
	}else{
		alert("el precio de este producto debe ser igual o menor a la que tenie actualmente");
	}
	
}

function limpiarDatos(){
	location.reload();
}

function ejecutarFormAddNC(){
	
	var sustento = document.getElementById("sustenance").value;
	
	/*if(sustento.length <= 5){
		alert('El campo "Motivo o Sustento" debe tener más de 5 caracteres.');
	}else{
		//alert('ok');
		document.form_noteCredit.submit();
	}*/document.form_noteCredit.submit();
	
}

/*
//calcular el importe 
$(document).ready(function() {

  $('input').keypress(function(e){
	  var id_input =$(this).attr('id');
	  if(id_input == "quantity_modal_item"){
		  var precio = document.getElementById("value_unit_modal_item").value;
		  var cantidad = document.getElementById("quantity_modal_item").value;
		  document.getElementById("import_modal_item").innerHTML = (parseFloat(precio) * parseFloat(cantidad));
	  }
	 
	  if(id_input == "value_unit_modal_item"){
		  var precio = document.getElementById("value_unit_modal_item").value;
		  var cantidad = document.getElementById("quantity_modal_item").value;
		  document.getElementById("import_modal_item").innerHTML = (parseFloat(precio) * parseFloat(cantidad));
	  }
  });

});*/
