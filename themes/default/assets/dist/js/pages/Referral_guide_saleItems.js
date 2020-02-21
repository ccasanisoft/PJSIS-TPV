window.onload = function() {

	document.getElementById("add_item").disabled= true; /*desabilitar busqueda de productos --responsable vadlimir :D*/

	//alert(sale_id);
	/*guia de venta*/
	$.ajax({
		type: "get",
		url: base_url+'Referral_guide/search_SaleItems/'+sale_id,
		dataType: "json",
		success: function (data) {
			console.log(data)

			var cont=0;
			$.each(data,function(key, registro) {
				//alert(registro.name);
					//$("#distri_llegada").append('<option value='+registro.ubigeo+'>'+registro.distrito+'</option>');
					
					//********************
					if(registro.id < 1){
                      registro.id=Math.floor(Math.random() * 100);
					}
					if(registro.code == ''){
						registro.code = 'SC'+ Math.floor(Math.random() * 10);
					}

					

					var tr = document.createElement("tr");
					tr.id = "tr_"+registro.id;
					document.getElementById("poTableB").appendChild(tr);
					//******************
					
					var name = document.createElement("td");
					var contenido_name = document.createTextNode(registro.name+"("+registro.code+")");
					name.appendChild(contenido_name);
					name.id = "name_"+registro.id;
					document.getElementById("tr_"+registro.id).appendChild(name);
					
					//*******************
					
					var cant = document.createElement("td");
					cant.id = "cant_"+registro.id;
					document.getElementById("tr_"+registro.id).appendChild(cant);
					
					//*******************
					
					var tipoUnit = document.createElement("td");
					var contenido_name = document.createTextNode("NIU");
					tipoUnit.appendChild(contenido_name);
					tipoUnit.id = "tipoUnit_"+registro.id;
					document.getElementById("tr_"+registro.id).appendChild(tipoUnit);
					
					//***************
					var action_td = document.createElement("td");
					action_td.id = "action_td_"+registro.id;
					document.getElementById("tr_"+registro.id).appendChild(action_td);
					
					var action_delete_div = document.createElement("div");
					action_delete_div.id = "action_delete_div_"+registro.id;
					action_delete_div.className= "btn btn-danger trashJs";
					document.getElementById("action_td_"+registro.id).appendChild(action_delete_div);
								
					var action_delete = document.createElement("i");
					action_delete.id = "action_"+registro.id;
					action_delete.className= "fa fa fa-trash-o tip pointer spodel";
					document.getElementById("action_delete_div_"+registro.id).appendChild(action_delete);
					
					//*******************
					
					var input_quantity = document.createElement("input");
					input_quantity.id = "Inputcant_"+registro.id;
					input_quantity.setAttribute("name", "product_quantity[]");
					input_quantity.setAttribute("value", registro.quantity);
					input_quantity.setAttribute("class", "quantityProduct");
					document.getElementById("cant_"+registro.id).appendChild(input_quantity);
					
					//***************************input hidden************************
								
								var form_div = document.createElement("div");
								form_div.id = "form_div";
								document.getElementById("form_referralGuide").appendChild(form_div);
								
								var input_div = document.createElement("div");
								input_div.id = "inputs_div_"+registro.id;
								document.getElementById("form_div").appendChild(input_div);
								//**********************************************************
								var input_id = document.createElement("input");
								input_id.id = "product_id_"+registro.id;
								input_id.setAttribute("type", "hidden");
								input_id.setAttribute("name", "product_id[]");
								input_id.setAttribute("value", registro.id);
								document.getElementById("inputs_div_"+registro.id).appendChild(input_id);
								
								//***************************************************
								var input_name = document.createElement("input");
								input_name.id = "product_name_"+registro.id;
								input_name.setAttribute("type", "hidden");
								input_name.setAttribute("name", "product_name[]");
								input_name.setAttribute("value", registro.name);
								document.getElementById("inputs_div_"+registro.id).appendChild(input_name);
								
								//***************************************************
								var input_code = document.createElement("input");
								input_code.id = "product_name_"+registro.id;
								input_code.setAttribute("type", "hidden");
								input_code.setAttribute("name", "product_code[]");
								input_code.setAttribute("value", registro.code);
								document.getElementById("inputs_div_"+registro.id).appendChild(input_code);
								
					
					
					
					cont++;
					
			  });
		}
	});
	
	$.ajax({
		type: "get",
		url: base_url+'Referral_guide/search_customer/'+sale_id,
		dataType: "json",
		success: function (data) {
		
			$.each(data,function(key, registro) {
					$('#nameReceptor').val(registro.name);
					if(registro.cf2!=""){
						$('#rucReceptor').val(registro.cf2);
						$("#tipoDNI_Receptor option[value=6]").attr("selected",true);
					}else{
						$('#rucReceptor').val(registro.cf1);
						$("#tipoDNI_Receptor option[value="+registro.customers_type_id+"]").attr("selected",true);
					}
					
			  });
		}
	});
	
	
	$("#add_item").on("keyup", function() {
     var textSearch= $("#add_item").val();
	 
	 var urlSearch=base_url+'Referral_guide/search_Producto/'+textSearch;
	
		$( function() {
			$( "#add_item" ).autocomplete({
			  source: urlSearch,
			  minLength: 1,
              //focus: showLabel,
              select: showLabel//,
              //change: showLabel
			  
			});
		} );
	 
	});
	
}


