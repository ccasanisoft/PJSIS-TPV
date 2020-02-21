    //**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************
	//var metodoImpuestoGlob = [];
	//var nombMetodoImpGlob = [];
	//**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************
	$(document).ready(function() {

        if (get('spoitems')) {
            remove('spoitems');
        }

        if($("#fflag").val()==0){
            loadItems();
        }

        // $("#date").inputmask("yyyy-mm-dd hh:mm", {"placeholder": "yyyy-mm-dd hh:mm"});
        $("#date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
        $("#expiration_date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});

        $("#add_item").autocomplete({
            source: base_url+'sales/suggestions',
            minLength: 1,
            autoFocus: false,
            delay: 200,
            response: function (event, ui) {
       
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                 add_order_item(ui.item);
                $(this).val(''); 

                	if(ui.item.row.code){
                	/*console.log("si tiene")*/
                    sumarCantidadItem(ui.item.row.code)

                	}else{

		/*			arraycode2 =[];
                    var repetidos2 = {};
                  


                   for (var prop in spoitems) {                 
                            arraycode2.push(spoitems[prop]["row"]["name"])
                         }
                        
                       arraycode2.forEach(function(numero){
                           repetidos2[numero] = (repetidos2[numero] || 0) + 1;
                       
                       });
                          console.log(repetidos2[ui.item.label])
                             
                       if(repetidos2[ui.item.label] > 1){
                           console.log("invalido")
                           first_code = Object.keys(spoitems).sort().pop();
                           delete_order_item(first_code);


                        for (var p in spoitems) {                 
                      
                         if(ui.item.label == spoitems[p]["row"]["name"]){

                         	var input = $(".rquantity");

							 spoitems[spoitems[p]["item_id"]].row.qty = parseFloat(spoitems[spoitems[p]["item_id"]].row.qty) + 1;
                             store('spoitems', JSON.stringify(spoitems));
                             loadItems();
                       
                           }
                        
                         }
    
                      }else{
                           console.log("valido")
                       }
*/
                       
                }
         
                




                } else {
                    // bootbox.alert(lang.no_match_found);
                }
            }
        });



/*TRJ128 -Amador ccasani*/
    function sumarCantidadItem(code){
                    arraycode =[];
                    var repetidos = {};
                   for (var prop in spoitems) {                 
                            arraycode.push(spoitems[prop]["row"]["code"])
                         }
                        
                       arraycode.forEach(function(numero){
                           repetidos[numero] = (repetidos[numero] || 0) + 1;
                       
                       });
                              
                       if(repetidos[code] > 1){
                           console.log("invalido")
                           first_code = Object.keys(spoitems).sort().pop();
                       
                           delete_order_item(first_code);
                         
                            var input = $(".rquantity");
                            a = input.attr("data-item");
                        
                            var item_id_pos= [];

                         for (var p in spoitems) {                 
                      
                         if(code == spoitems[p]["row"]["code"]){
                           
                            spoitems[spoitems[p]["item_id"]].row.qty = parseFloat(spoitems[spoitems[p]["item_id"]].row.qty) + 1;
                             store('spoitems', JSON.stringify(spoitems));
                             loadItems();
                       
                           }
                        
                         }
    
                        }else{
                           console.log("valido")
                       }
    }







        $('#add_item').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                // $(this).autocomplete("search");
            }
        });

        $('#add_item').focus();

        $('#reset').click(function (e) {
            bootbox.confirm(lang.r_u_sure, function (result) {
                if (result) {
                    if (get('spoitems')) {
                        remove('spoitems');
                    }

                    window.location.reload();
                }
            });
        });

        $(document).on("change", '.rquantity', function () {
            var row = $(this).closest('tr');
            var val = 0;
            if(!$(this).val() || !isNaN($(this).val())){
                val = $(this).val();
                if( val.trim() === ""){
                    val = 0;
                }
            }
            var new_qty = parseFloat(val);
            item_id = row.attr('data-item-id');
            spoitems[item_id].row.qty = new_qty;
            store('spoitems', JSON.stringify(spoitems));
            loadItems();
        });

        $(document).on("change", '.rprice', function () {
            var row = $(this).closest('tr');
            var val = 0.00;
            if(!$(this).val() || !isNaN($(this).val())){
                val = $(this).val();
                if( val.trim() === ""){
                    val = 0.00;
                }
            }
            var new_price = parseFloat(val).toFixed(3);//*******************TRJ031 - ALEXANDER ROCA - 05/04/2019********************
			//
			item_id = row.attr('data-item-id');
			//idTokenProduc=$(this).attr('data-id');
			//alert(idTokenProduc);
			//spoitems[item_id].row.tax_method = metodoImpuestoGlob[item_id];
            spoitems[item_id].row.price = new_price;
            spoitems[item_id].row.edit = 1;
            store('spoitems', JSON.stringify(spoitems));
            loadItems();
        });
		
		$(document).on("change", '.rPAffect', function () {
            var row = $(this).closest('tr');
            var val = 0.00;
            if(!$(this).val() || !isNaN($(this).val())){
                val = $(this).val();
                if( val.trim() === ""){
                    val = 0.00;
                }
            }
            var new_priceAf = parseFloat(val).toFixed(3);//*******************TRJ031 - ALEXANDER ROCA - 05/04/2019********************
			//
			item_id = row.attr('data-item-id');
			//idTokenProduc=$(this).attr('data-id');
			//alert(idTokenProduc);
			//spoitems[item_id].row.tax_method = metodoImpuestoGlob[item_id];
            spoitems[item_id].affect_priceJs = new_priceAf;
            spoitems[item_id].row.edit = 1;
            store('spoitems', JSON.stringify(spoitems));
            loadItems();
        });
		
		$(document).on("change", '.rPNonAffected', function () {
            var row = $(this).closest('tr');
            var val = 0.00;
            if(!$(this).val() || !isNaN($(this).val())){
                val = $(this).val();
                if( val.trim() === ""){
                    val = 0.00;
                }
            }
            var new_priceNaf = parseFloat(val).toFixed(3);//*******************TRJ031 - ALEXANDER ROCA - 05/04/2019********************
			//
			item_id = row.attr('data-item-id');
			//idTokenProduc=$(this).attr('data-id');
			//alert(idTokenProduc);
			//spoitems[item_id].row.tax_method = metodoImpuestoGlob[item_id];
			/*if(new_priceNaf>0){
				spoitems[item_id].exonerated_priceJs = 0;
			}*/
            spoitems[item_id].non_affected_priceJs = new_priceNaf;
            spoitems[item_id].row.edit = 1;
            store('spoitems', JSON.stringify(spoitems));
            loadItems();
        });
		
		$(document).on("change", '.rPExonerated', function () {
            var row = $(this).closest('tr');
            var val = 0.00;
            if(!$(this).val() || !isNaN($(this).val())){
                val = $(this).val();
                if( val.trim() === ""){
                    val = 0.00;
                }
            }
            var new_priceE = parseFloat(val).toFixed(3);//*******************TRJ031 - ALEXANDER ROCA - 05/04/2019********************
			//
			item_id = row.attr('data-item-id');
			//idTokenProduc=$(this).attr('data-id');
			//alert(idTokenProduc);
			//spoitems[item_id].row.tax_method = metodoImpuestoGlob[item_id];
			/*if(new_priceE>0){
				if($('#document_type').val() != 3){
				spoitems[item_id].non_affected_priceJs = 0;
				}
			}*/
            spoitems[item_id].exonerated_priceJs = new_priceE;
            spoitems[item_id].row.edit = 1;
            store('spoitems', JSON.stringify(spoitems));
            loadItems();
        });

        $(document).on("change", $("#gdiscount"), function () {
            $("#gdiscount").val(Number($("#gdiscount").val()).toFixed(2));
            loadItems();
        });

        $(document).on("change", $("#customer"), function () {

        });

        $('#close').click(function (e) {
            displayOff('warning');
        });

        $('#add_sale').click(function (e) {
			
		
//***********************TRJ033 - ALEXANDER ROCA - 06/04/2019***************************
			//*****************TRJ044 - ALEXANDER ROCA - 24/05/2019***************
			var afecto_validar=document.getElementsByClassName('rPAffect');
			var inafecto_validar=document.getElementsByClassName('rPNonAffected');
			var exonerado_validar=document.getElementsByClassName('rPExonerated');
			
			var valorClase=document.getElementsByClassName('rprice');
			var sumaValores=0;
			//parseFloat(afecto_validar) + parseFloat(inafecto_validar) + parseFloat(exonerado_validar) ;
			var sensorCero=0;
				//window.stop();
				//alert("no existe");
				//bootbox.alert("hola "+valorClase[i].value);
				   
			for(var i=0; i<afecto_validar.length; i++){
					sumaValores =0;
					sumaValores = parseFloat(afecto_validar[i].value) + parseFloat(inafecto_validar[i].value) + parseFloat(exonerado_validar[i].value) ;
				if(sumaValores <= 0){
					sensorCero=1;
					//bootbox.alert("todos los productos deben tener un precio mayor a 0");
					break;
				}
				
					
			}
			//*****************TRJ044 - ALEXANDER ROCA - 24/05/2019***************
			
			if(sensorCero == 0){

        	if($('#document_type').val() == 2){

                    

					code = $("#customer").val();
					e.preventDefault();
					$.ajax({
						type: "get",
						url: base_url+'customers/get_customers_byid/'+code,
						dataType: "json",
						success: function (data) {
                         /*   document.getElementById("customer_typee").value = data.customers_type_id;*/
                                document.getElementById("cdireccione").value = data.direccion;
                                document.getElementById("cf2e").value=data.cf2;
                                document.getElementById("cf1e").value=data.cf1;
                                document.getElementById("cphonee").value=data.phone;
                                document.getElementById("cemaile").value=data.email;
                                document.getElementById("cnamee").value=data.names;

                                document.getElementById("document_type1e").value=data.document_type_id;

                                var sj = document.getElementById("sje");
                                var sn = document.getElementById("sne");
                               
                                document.getElementById("idclienteedit").value=data.id;
                                 var x = document.getElementById("customer_typee");
                                 x.remove(x.value=3);
                                 x.value =data.customers_type_id;

                                if(data.person_type == sn.value){
                                 
                                     sn.style.background ='#000';
                                     sn.style.color      ='#000';


                                }

                                if(data.person_type == sj.value){
                                  
                                     sj.style.background ='#000';
                                     sj.style.color      ='#000';

                                }


                                if(data.document_type_id==6){
                                     document.getElementById("document_type1e").disabled = true;
                                }


                          var ruc = data.cf2;
							var dir = data.direccion;
							if(ruc == null || ruc == ""){
								document.getElementById("warning_text").innerHTML = lang.verify_ruc;
                                console.log(data)
                                $("#customerModalEdit").modal("show");

								displayOn('warning');
								document.body.scrollTop = document.documentElement.scrollTop = 0;
							} else if(dir == null || dir == ""){
								document.getElementById("warning_text").innerHTML = lang.verify_address;
                                 $("#customerModalEdit").modal("show");
								displayOn('warning');
								document.body.scrollTop = document.documentElement.scrollTop = 0;
							} else{

								if (!get('spoitems') || $("#poTable tbody").text() == "") {
									e.preventDefault();
									document.getElementById("warning_text").innerHTML = lang.products_not_found;
									displayOn('warning');
									// $('html,body').animate({ scrollTop: 0 }, 'slow');
									document.body.scrollTop = document.documentElement.scrollTop = 0;
								} else if ($("#gimptotal").val() <= 0){
									e.preventDefault();
									document.getElementById("warning_text").innerHTML = lang.sale_not_less;
									displayOn('warning');
									document.body.scrollTop = document.documentElement.scrollTop = 0;
								} else{
									displayOff('warning');
									$('#add_sale').submit();
								}

							}


                            

						}
					});




				}
                 else if($('#document_type').val() == 1){

					if($("#gimptotal").val() > 700){
					code = $("#customer").val();
					e.preventDefault();

					$.ajax({
						type: "get",
						url: base_url+'customers/get_customers_byid/'+code,
						dataType: "json",
						success: function (data) {
                            console.log(data)
/*                              document.getElementById("customer_typee").value = data.customers_type_id;
*/                                document.getElementById("cdireccione").value = data.direccion;
                                document.getElementById("cf2e").value=data.cf2;
                                document.getElementById("cf1e").value=data.cf1;
                                document.getElementById("cphonee").value=data.phone;
                                document.getElementById("cemaile").value=data.email;
                                document.getElementById("cnamee").value=data.names;
                               

                                 
                                var x = document.getElementById("customer_typee");
                                 x.remove(x.value=3);
                                 x.value =data.customers_type_id;


                                var sj = document.getElementById("sje");
                                var sn = document.getElementById("sne");
                               
                                document.getElementById("idclienteedit").value=data.id

                                if(data.person_type == sn.value){
                                 
                                     sn.style.background ='#000';
                                     sn.style.color      ='#000';
                                  }

                                if(data.person_type == sj.value){
                                  
                                     sj.style.background ='#000';
                                     sj.style.color      ='#000';
                                  }


                                if(data.document_type_id==6){
                                     document.getElementById("document_type1e").disabled = true;
                                }


							var dni = data.cf1;
							var ruc = data.cf2;
							if(ruc == null || ruc == ""){
								if(dni == null || dni == "" || dni == "--"){
								document.getElementById("warning_text").innerHTML = lang.verify_dniRuc;
                                 $("#customerModalEdit").modal("show");
								displayOn('warning');
								document.body.scrollTop = document.documentElement.scrollTop = 0;
								}else{
									if (!get('spoitems') || $("#poTable tbody").text() == "") {
									e.preventDefault();
									document.getElementById("warning_text").innerHTML = lang.products_not_found;
									displayOn('warning');
									// $('html,body').animate({ scrollTop: 0 }, 'slow');
									document.body.scrollTop = document.documentElement.scrollTop = 0;
									} else if ($("#gimptotal").val() <= 0){
										e.preventDefault();
										document.getElementById("warning_text").innerHTML = lang.sale_not_less;
										displayOn('warning');
										document.body.scrollTop = document.documentElement.scrollTop = 0;
									} else{
										displayOff('warning');
										$('#add_sale').submit();
									}
								}

							} else{

								if (!get('spoitems') || $("#poTable tbody").text() == "") {
									e.preventDefault();
									document.getElementById("warning_text").innerHTML = lang.products_not_found;
									displayOn('warning');
									// $('html,body').animate({ scrollTop: 0 }, 'slow');
									document.body.scrollTop = document.documentElement.scrollTop = 0;
								} else if ($("#gimptotal").val() <= 0){
									e.preventDefault();
									document.getElementById("warning_text").innerHTML = lang.sale_not_less;
									displayOn('warning');
									document.body.scrollTop = document.documentElement.scrollTop = 0;
								} else{
									displayOff('warning');
									$('#add_sale').submit();
								}

							}
						}
					});

             }/*endifvalidar 700*/

					
				} else if (!get('spoitems') || $("#poTable tbody").text() == "") {
					e.preventDefault();
					document.getElementById("warning_text").innerHTML = lang.products_not_found;
					displayOn('warning');
					// $('html,body').animate({ scrollTop: 0 }, 'slow');
					document.body.scrollTop = document.documentElement.scrollTop = 0;
				} else if ($("#gimptotal").val() <= 0){
					e.preventDefault();
					document.getElementById("warning_text").innerHTML = lang.sale_not_less;
					displayOn('warning');
					document.body.scrollTop = document.documentElement.scrollTop = 0;
				} else{
					displayOff('warning');
				}





			
			
			
			}else{
				e.preventDefault();
				bootbox.alert("Todos los productos deben tener un precio unitario mayor a 0");
			}
//***********************TRJ033 - ALEXANDER ROCA - 06/04/2019***************************			
        });



        function displayOn(Element) {
            var x = document.getElementById(Element);
            if (x.style.display == 'none') {
                x.style.display = '';
            }
        }

        function displayOff(Element) {
            var x = document.getElementById(Element);
            if (x.style.display == '') {
                x.style.display = 'none';
            }
        }

    });

$(document).on('click', '#sne', function(event) {
event.preventDefault();
    /* Act on the event */
    var dos = document.getElementById("sje");
    
    cambiarcolor(this,dos)
    console.log(this.id)
    
    document.getElementById("cf1e").disabled =false;  
    document.getElementById("nombre-persona").textContent='Nombre';
    document.getElementById("document_type1").disabled=false;
    document.getElementById("select2-document_type1-container").textContent="Seleccione Tipo de Documento";

});

$(document).on('click', '#sje', function(event) {
    event.preventDefault();
    /* Act on the event */
    var dos = document.getElementById("sne");
    cambiarcolor(this,dos)
    console.log(this.id)
    document.getElementById("cf1e").disabled =true;
    document.getElementById("nombre-persona").textContent='Razon Social';
    document.getElementById("document_type1e").disabled=true;
    document.getElementById("select2-document_type1-container").textContent="Seleccione Tipo de Documento";

});


function cambiarcolor(id,id2){

    if(id.id == id.id){
      
     id.style.background ='#000';
        id.style.color      ='#000';
        id2.style.background ='#ADD8E6';
        id2.style.color      ='#ADD8E6';

    }else{
        id.style.background ='#ADD8E6';
        id.style.color      ='#ADD8E6';

    }

}


/*Actualizar cliente*/
$(document).on('click', '#add_customere', function(event) {
    event.preventDefault();
    var customerType =  document.getElementById("customer_typee").value;
    var direction = document.getElementById("cdireccione").value;
    var ruc = document.getElementById("cf2e").value;

     var dni = document.getElementById("cf1e").value;
     var phone = document.getElementById("cphonee").value;
     var mail = document.getElementById("cemaile").value;
     var name = document.getElementById("cnamee").value;
     var documentType = document.getElementById("document_type1e").value;
     
     var sj = document.getElementById("sje");
     var sn = document.getElementById("sne");
       var t =document.getElementById("token");
     
     var id= document.getElementById("idclienteedit").value;
     var type;
        if(sn.style.color == 'rgb(0, 0, 0)'){
            type=sn.value;
        }else{
            type=sj.value;
            documentType=6;
        }
        da = {
           
            'person_type':type,
            'document_type_id':documentType,
            'name':name,
            'email':mail,
            'phone':phone,
            'cf1':dni,
            'cf2':ruc,
            'direccion':direction,
            'customers_type_id':customerType,
            'estado':1

        }
            $.ajax({    
            url: base_url+'customers/editCustomer',
            type: 'POST',
            dataType:'json',
            data:{'spos_token':t.value,'data':da,'id':id},
            success:function(rs){

                   if(rs) {
                     /*location.reload();*/
                    $('#customerModalEdit').modal('hide');
            }else{
             console.log('')
            }
        }
        });
    
});



function obtenerDatos2(){
    $("#c-s").remove();
 var ruc=document.getElementById('cf2e').value;
  if (ruc>9999999999) {
    $('#rucalert2e').hide('fad');
      $.ajax({
        type:'get',
          url: 'https://cors-anywhere.herokuapp.com/http://api.ateneaperu.com/api/Sunat/Ruc',
          data:{'sNroDocumento':ruc},
        success: function(datos) {
            console.log(datos)
        $('#cargandoe').hide('fade');
           if (datos.success===true) {
              $('#rucalerte').hide('fade');
              document.getElementById('cnamee').value = datos.nombre_o_razon_social; 
              document.getElementById('cdireccione').value = datos.direccion_completa;            
           }else{
               var htm='<li><span>El Ruc Ingresado No Ha Sido Encontrado</span></li>'
                  $('#c-s').append(htm);
              $('#c-alert').show('fade');
            }
        },
        error: function() {
          console.log("no se encontro ningun registro");
        }, 
        beforeSend: function(){
        $('#cargandoe').show('fade');         
        },
    });
  }else{
    $('#rucalert2e').show('fad');
  }
};


function loadItems() {

    if (get('spoitems')) {
		
		var avanzado="", normal="";
		if(avdSale == 1){
			normal=" display: none;";
		}else{
			avanzado=" display: none;";
		}
		
        total = 0;
        affected = 0;
        tax = 0;
		taxICBPER = 0;
        exonerated = 0;
		exoneratedReal=0;//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************

        count = 0;
        countt = 0;

        discount = parseFloat($("#gdiscount").val()) ? parseFloat($("#gdiscount").val()) : 0;
        total_payable = parseFloat($("#gtotal").val());

        $("#poTable tbody").empty();

        spoitems = JSON.parse(get('spoitems'));

        $.each(spoitems, function () {

            var item = this;

            var item_id = Settings.item_addition == 1 ? item.item_id : item.id;
            spoitems[item_id] = item;

            //var product_id = item.row.id, item_currency = item.row.currency, item_price = item.row.price, item_qty = item.row.qty, item_code = item.row.code, item_tax_method = item.row.tax_method,
            //item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
			//********************************TRJ064 - ALEXANDER ROCA - 03/07/2019**********************
			var product_id = item.row.productID, item_currency = item.row.currency, item_price = item.row.price, item_qty = item.row.qty, item_code = item.row.code, item_tax_method = item.row.tax_method, item_tax_ICBPER = item.tax_ICBPER,
            item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
			//********************************TRJ064 - ALEXANDER ROCA - 03/07/2019**********************
			
			//***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************
			item_name=item_name.replace("<","&lt;");
			item_name=item_name.replace(">","&gt;");
			//***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************

            var real_unit_price = item.row.real_unit_price;

            if(item.row.edit != 1){
                if(item_price > 0 && item_currency != $("#currency_code").val()){
                    if(item_currency == "PEN"){
                        item_price = parseFloat(item_price / $("#exchange").val()).toFixed(2);
                        spoitems[item_id].row.price = item_price;
                    }
                    if(item_currency == "USD"){
                        item_price = parseFloat(item_price * $("#exchange").val()).toFixed(2);
                        spoitems[item_id].row.price = item_price;
                    }
                }
            }

            var item_afec = 0, item_tax = 0, item_afec = 0, item_exon = 0;
			var affect_price=0, non_affected_price=0, exonerated_price=0;//*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************
			
			if(item.affect_priceJs == null){
				affect_price=0;
			}else{
				affect_price=item.affect_priceJs;
				item_afec = parseFloat(affect_price) / (1 + (parseFloat(Settings.default_tax_rate) / 100));
                item_tax = parseFloat(affect_price) - item_afec;
			}
			
			if(item.non_affected_priceJs == null){
				non_affected_price=0;
			}else{
				non_affected_price=item.non_affected_priceJs;
			}
			
			if(item.exonerated_priceJs == null){
				exonerated_price=0;
			}else{
				exonerated_price=item.exonerated_priceJs;
			}
			
			
            var bool_exonerado = false, bool_afecto = false, bool_exoneradoReal = false;//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
			var row_no = (new Date).getTime();//**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************
//*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************
            if($('#document_type').val() != 3){

                if(item_tax_method == 0 ){ //Incluido
					
                    item_exon = 0;
                    bool_afecto = true;
					//**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************
					//combBoxMetodo='<td ><div style=" top:'+posicion[row_no]+'px; position: absolute; z-index: 3;"  class="dropdown" ><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">Dropdown<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li role="presentation"><a role="menuitem" tabindex="-1" >Incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >No incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Inafecto</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Exonerado</a></li><lu></div></td>';
					combBoxMetodo='<td style="padding:2px;'+normal+'"><div class="dropdown" style="float: right; margin-right:20px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1_'+row_no+'" data-toggle="dropdown" aria-expanded="true">Incluido<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=0><a role="menuitem" tabindex="-1" >Incluido</a></li><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=2><a role="menuitem" tabindex="-1" >Inafecto</a></li><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=3><a role="menuitem" tabindex="-1" >Exonerado</a></li></lu></div></td>';
	
					
					if(item.affect_priceJs == null){
						affect_price=item_price;
						item_afec = parseFloat(affect_price) / (1 + (parseFloat(Settings.default_tax_rate) / 100));
						item_tax = parseFloat(affect_price) - item_afec;
					}
					
				}else if(item_tax_method == 1){ //Excluido
                    if(item.row.edit != 1){
                        item_price =  parseFloat( parseFloat(item_price) * (1 + (parseFloat(Settings.default_tax_rate) / 100)) ).toFixed(2);
                        spoitems[item_id].row.price = item_price;
                    }
                    
                    item_exon = 0;
                    bool_afecto = true;

					//combBoxMetodo='<td ><div style=" top:'+posicion[row_no]+'px; position: absolute; z-index: 3;"  class="dropdown" ><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">Incluido<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li role="presentation"><a role="menuitem" tabindex="-1" >Incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >No incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Inafecto</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Exonerado</a></li><lu></div></td>';
					combBoxMetodo='<td style="padding:2px;'+normal+'"><div class="dropdown" style="float: right; margin-right:20px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1_'+row_no+'" data-toggle="dropdown" aria-expanded="true">Incluido<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=0><a role="menuitem" tabindex="-1" >Incluido</a></li><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=2><a role="menuitem" tabindex="-1" >Inafecto</a></li><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=3><a role="menuitem" tabindex="-1" >Exonerado</a></li></lu></div></td>';

					if(item.affect_priceJs == null){
						affect_price=item_price;
						item_afec = parseFloat(affect_price) / (1 + (parseFloat(Settings.default_tax_rate) / 100));
						item_tax = parseFloat(affect_price) - item_afec;
					}
					
				}else if(item_tax_method == 2){ //Inafecto
                    item_exon = parseFloat(item_price);
                    
                    bool_exonerado = true;
					//combBoxMetodo='<td ><div style=" top:'+posicion[row_no]+'px; position: absolute; z-index: 3;"  class="dropdown" ><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">Inafecto<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li role="presentation"><a role="menuitem" tabindex="-1" >Incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >No incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Inafecto</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Exonerado</a></li><lu></div></td>';
					combBoxMetodo='<td style="padding:2px;'+normal+'"><div class="dropdown" style="float: right; margin-right:20px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1_'+row_no+'" data-toggle="dropdown" aria-expanded="true">Inafecto<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=0><a role="menuitem" tabindex="-1" >Incluido</a></li><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=2><a role="menuitem" tabindex="-1" >Inafecto</a></li><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=3><a role="menuitem" tabindex="-1" >Exonerado</a></li></lu></div></td>';
					
				
					if(item.non_affected_priceJs == null){
						non_affected_price=item_price;
					}
					
                }else if(item_tax_method == 3){ //Exonerado
                    item_exon = parseFloat(item_price);
                    bool_exoneradoReal = true;
					//combBoxMetodo='<td ><div style=" top:'+posicion[row_no]+'px; position: absolute; z-index: 3;"  class="dropdown" ><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">Exonerado<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li role="presentation"><a role="menuitem" tabindex="-1" >Incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >No incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Inafecto</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Exonerado</a></li><lu></div></td>';
					combBoxMetodo='<td style="padding:2px;'+normal+'"><div class="dropdown" style="float: right; margin-right:20px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1_'+row_no+'" data-toggle="dropdown" aria-expanded="true">Exonerado<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=0><a role="menuitem" tabindex="-1" >Incluido</a></li><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=2><a role="menuitem" tabindex="-1" >Inafecto</a></li><li data-ul='+row_no+' role="presentation" onclick="modif_tax_met()" style="cursor:pointer" id=3><a role="menuitem" tabindex="-1" >Exonerado</a></li></lu></div></td>';

					if(item.exonerated_priceJs == null){
						exonerated_price=item_price;
					}
				}//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************

            } else {
                item_exon = parseFloat(item_price);
				//combBoxMetodo='<td ><div style=" top:'+posicion[row_no]+'px; position: absolute; z-index: 3;"  class="dropdown" ><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">Exonerado<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li role="presentation"><a role="menuitem" tabindex="-1" >Incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >No incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Inafecto</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Exonerado</a></li><lu></div></td>';
				combBoxMetodo='<td style="padding:2px;'+normal+'"><div class="dropdown" style="float: right; margin-right:20px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1_'+row_no+'" data-toggle="dropdown" aria-expanded="true">Seleccionar<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li data-ul='+row_no+' role="presentation" style="cursor:pointer" id=0><a role="menuitem" tabindex="-1" >Incluido</a></li><li data-ul='+row_no+' role="presentation" style="cursor:pointer" id=2><a role="menuitem" tabindex="-1" >Inafecto</a></li><li data-ul='+row_no+' role="presentation" style="cursor:pointer" id=3><a role="menuitem" tabindex="-1" >Exonerado</a></li></lu></div></td>';
                //**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************
				
                item_tax = 0;
                bool_exonerado = true;
				
				if(item.non_affected_priceJs == null){
						non_affected_price=item_price;
					}
				affect_price=0;
				item_afec = 0;
				exonerated_price=0;
				
            }
//*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************
            spoitems[item_id].row.edit = 1;
            store('spoitems', JSON.stringify(spoitems));

            //total += (parseFloat(item_price) * parseFloat(item_qty));
			total += (parseFloat((parseFloat(affect_price) + parseFloat(non_affected_price) + parseFloat(exonerated_price))) * parseFloat(item_qty));

            var igv_desc = 0, gvd_desc = 0, porcentaje = 0, prr_discount = 0
            subtotal = (parseFloat(item_price) * parseFloat(item_qty));
            if(discount > 0){

                if($('#document_type').val() != 3){

                    porcentaje = (parseFloat(subtotal) / parseFloat(total_payable));
                    prr_discount = parseFloat(discount) * parseFloat(porcentaje);

                    if(bool_afecto){
                        gvd_desc = (parseFloat(prr_discount) / (1 + ((parseFloat(Settings.default_tax_rate) / 100))));
                        igv_desc = prr_discount - gvd_desc;
                    }

                } else {
                    gvd_desc = parseFloat(discount);
                    prr_discount = 0;
                    igv_desc = 0;
                }

            }else{
                gvd_desc = 0;
                prr_discount = 0;
                igv_desc = 0;
            }


				affected += ((parseFloat(item_afec) * parseFloat(item_qty)) - parseFloat(gvd_desc));
                tax += ((parseFloat(item_tax) * parseFloat(item_qty)) - parseFloat(igv_desc));
				
				if(parseFloat(non_affected_price) > 0){
					exonerated += ((parseFloat(non_affected_price) * parseFloat(item_qty)) - parseFloat(prr_discount));
				}else{
					exonerated += ((parseFloat(non_affected_price) * parseFloat(item_qty)));
				}
				
				if(parseFloat(exonerated_price) > 0){
					//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
					exoneratedReal += ((parseFloat(exonerated_price) * parseFloat(item_qty)) - parseFloat(prr_discount));
					//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
				}else{
					//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
					exoneratedReal += ((parseFloat(exonerated_price) * parseFloat(item_qty)));
					//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
				}

				

			
			
            var newTr = $('<tr id="' + row_no + '" class="' + item_id + '" data-item-id="' + item_id + '"></tr>');
            tr_html = '<td style="min-width:100px;">';
            tr_html += '<input name="product_id[]" type="hidden" class="rid" value="' + product_id + '">';
            tr_html += '<input name="tax_method[]" type="hidden" class="rid" value="' + item_tax_method + '" id="tax_method_'+row_no+'">';
            tr_html += '<input name="real_unit_price[]" type="hidden" class="rid" value="' + real_unit_price + '">';
            tr_html += '<input name="currency_cost[]" type="hidden" class="rid" value="' + item_currency + '">';
            tr_html += '<input name="cost[]" type="hidden" class="rid" value="' + item.row.cost + '">';
            tr_html += '<input name="name[]" type="hidden" class="rid" value="' + item_name + '">';
            tr_html += '<input name="code[]" type="hidden" class="rid" value="' + item_code + '">';
            tr_html += '<span class="sname" id="name_' + row_no + '">' + item_name + ' (' + item_code + ')</span></td>';
			//**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************
			tr_html += combBoxMetodo; //'<td ><div style=" top:'+posicion[row_no]+'px; position: absolute; z-index: 3;"  class="dropdown" ><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">Dropdown<span class="caret"></span></button><lu class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><li role="presentation"><a role="menuitem" tabindex="-1" >Incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >No incluido</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Inafecto</a></li><li role="presentation"><a role="menuitem" tabindex="-1" >Exonerado</a></li><lu></div></td>';
            //**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************
			
			tr_html += '<td style="padding:2px;"><input class="form-control input-sm kb-pad text-center rquantity" name="quantity[]" type="text" value="' + item_qty + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" "></td>';
            tr_html += '<td style="padding:2px; min-width:80px;'+normal+'"><input class="form-control input-sm kb-pad text-center rprice" name="price[]" type="text" value="' + item_price + '" data-id="' + row_no + '" data-item="' + item_id + '" id="price_' + row_no + '" onClick="this.select();"></td>';
//*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************			
			
			tr_html += '<td style="padding:2px; min-width:80px;'+avanzado+'"><input class="form-control input-sm kb-pad text-center rPAffect" name="affect_price[]" type="text" value="' + affect_price + '" data-id="' + row_no + '" data-item="' + item_id + '" id="price_' + row_no + '" onClick="this.select();"></td>';
			
			tr_html += '<td style="padding:2px; min-width:80px;'+avanzado+'"><input class="form-control input-sm kb-pad text-center rPNonAffected" name="non_affected_price[]" type="text" value="' + non_affected_price + '" data-id="' + row_no + '" data-item="' + item_id + '" id="price_' + row_no + '" onClick="this.select();"></td>';
			
			tr_html += '<td style="padding:2px; min-width:80px;'+avanzado+'"><input class="form-control input-sm kb-pad text-center rPExonerated" name="exonerated_price[]" type="text" value="' + exonerated_price + '" data-id="' + row_no + '" data-item="' + item_id + '" id="price_' + row_no + '" onClick="this.select();"></td>';
			
//*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************
				var importe=0;
				importe=parseFloat(affect_price) + parseFloat(non_affected_price) + parseFloat(exonerated_price);
            //tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(parseFloat(item_price) * parseFloat(item_qty)) + '</span></td>';
			//***********TRJ068 - ALEXADER ROCA - 02/08/2019************
			
			var subTotal_ICBPER=0;
			var check_ICBPER='', input_text_ICBPER='"0"';
			
			if(plastic_bags > 0 ){
				ocultar_ICBPER='';
				
				if(item_tax_ICBPER == 0){
					check_ICBPER='';
					input_text_ICBPER='"0"';
					subTotal_ICBPER=0;
				}else{
					check_ICBPER='checked';
					input_text_ICBPER='"1"';
					subTotal_ICBPER=(plastic_bags * Math.round(parseFloat(item_qty)));
					taxICBPER += (0.10 * Math.round(parseFloat(item_qty)));
				}
				
			}else{
				ocultar_ICBPER=' display:none; ';
			}
			
			/*if(item_tax_ICBPER == 0){
				tr_html += '<td class="text-center" style="padding:1px;" data-ul='+row_no+' ><input onclick="chececkTaxBP();" class="" name="tax_ICBPER[]" type="checkbox" value="' + item_qty + '" data-id="' + row_no + '" data-item="' + item_id + '" id="tax_ICBPER_' + row_no + '">';
				tr_html += '<input value="0" class="" name="value_ICBPER[]" type="hidden" id="value_ICBPER_' + row_no +'"></td>';
				tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(parseFloat(importe) * parseFloat(item_qty)) + '</span></td>';
			}else{*/
				
				tr_html += '<td class="text-center" style="padding:1px; '+ocultar_ICBPER+'" data-ul='+row_no+'><input onclick="chececkTaxBP();" class="" name="tax_ICBPER[]" type="checkbox" value="' + item_qty + '" data-id="' + row_no + '" data-item="' + item_id + '" id="tax_ICBPER_' + row_no + '" '+check_ICBPER+'>';
				tr_html += '<input value='+input_text_ICBPER+' class="" name="value_ICBPER[]" type="hidden" id="value_ICBPER_' + row_no +'"></td>';
				tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney((parseFloat(importe) * parseFloat(item_qty)) + subTotal_ICBPER) + '</span></td>';
			//}
			//***********TRJ068 - ALEXADER ROCA - 02/08/2019************
			
            tr_html += '<td class="text-center"><i class="fa fa-trash-o tip pointer spodel" id="' + row_no + '" onclick="javascript:delete_order_item(' + item.item_id + ')" title="Remove"></i></td>';
            newTr.html(tr_html);
            newTr.prependTo("#poTable");

            count = count + 1;
            countt += parseFloat(item_qty);

        });

        // $("#gdiscount").val(formatMoney(discount));

        item_count = count + ' (' + formatMoney(countt) + ')'
        $("#tart").text(item_count);

        importe_total = affected + exonerated + tax + exoneratedReal + taxICBPER;//***********TRJ068 - ALEXADER ROCA - 02/08/2019************

        grand_affected = formatMoney(affected);
        $("#gaffected").text(grand_affected);
        $("#opgra").val(affected);

        grand_tax = formatMoney(tax);
        $("#gtax").text(grand_tax);
        $("#imigv").val(tax);
		//***********TRJ068 - ALEXADER ROCA - 02/08/2019************
		grand_taxICBPER = formatMoney(taxICBPER);
        $("#gtaxICBPER").text(grand_taxICBPER);
        //$("#imigv").val(taxICBPER);
		//***********TRJ068 - ALEXADER ROCA - 02/08/2019************
        grand_exonerated = formatMoney(exonerated);
        $("#gexonerated").text(grand_exonerated);
        $("#opexo").val(exonerated);
		
		//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
		grand_exonerated = formatMoney(exoneratedReal);
        $("#gexoneratedReal").text(grand_exonerated);
        $("#opexoReal").val(exoneratedReal);
		//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
        imp_total = formatMoney(importe_total);
        $("#gimptotal").text(imp_total);
        $("#gimptotal").val(importe_total);

        grand_total = formatMoney(total + taxICBPER);
        $("#gtot").text(formatMoney((total + taxICBPER) - discount));
        $("#gtotal").val(total);

        // $("#total_pay").val(total - discount);
        $("#total_pay").val(total);

        $("#gtotal").text(grand_total);
        $('#add_item').focus();
    }
}

function add_order_item(item) {
    //console.log(item);
    var item_id = Settings.item_addition == 1 ? item.item_id : item.id;
    if (spoitems[item_id]) {
        spoitems[item_id].row.qty = parseFloat(spoitems[item_id].row.qty) + 1;
    } else {
        spoitems[item_id] = item;
    }
	
    spoitems[item_id].row.real_unit_price = spoitems[item_id].row.price;
    store('spoitems', JSON.stringify(spoitems));
    loadItems();
    return true;
}

function delete_order_item(itemb) {
    delete spoitems[itemb];
    store('spoitems', JSON.stringify(spoitems));
    loadItems();
}

/*
$(document).ready(function() {
	
	$("#tax_met").click(function (e) {
	 alert ("hola");
	  $("#tax_met option[value='2']").attr("selected", true);
	});
});*/

//**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************
function modif_tax_met() {
	
	//$("#tax_met").click(function (e) {
	 //alert ("hola");
	 var metodoImpuesto="";
	 var producto="";
	 
	 $('body').on('click', 'li', function(){
		 
		metodoImpuesto=$(this).attr('id');

		producto=$(this).attr('data-ul');
		
		var nombMetodoImp="";
		  if(metodoImpuesto =="0"){
			  nombMetodoImp="Incluido";
			  
		  }
		  if(metodoImpuesto =="1"){
			  nombMetodoImp="No incluido";
		  }
		  if(metodoImpuesto =="2"){
			  nombMetodoImp="Inafecto";
		  }
		  if(metodoImpuesto =="3"){
			  nombMetodoImp="Exonerado";
		  }
	  
		  var idBoton="dropdownMenu1_"+producto;
		  //alert(metodoImpuesto);
		  document.getElementById("tax_method_"+producto).value = '"'+metodoImpuesto+'"';
		  document.getElementById(idBoton).innerHTML = nombMetodoImp+'<span class="caret"></span>';
		  //posicion[row_no]=37+(count*37);
		  //metodoImpuestoGlob[producto]=metodoImpuesto;
		  //nombMetodoImpGlob[producto]=nombMetodoImp;
		  var row = $(this).closest('tr');
		  var item_id = row.attr('data-item-id');
			spoitems[item_id].row.tax_method = metodoImpuesto;
            spoitems[item_id].row.edit = 1;
            store('spoitems', JSON.stringify(spoitems));
            loadItems();
			//$("#tax_met option[value='2']").attr("selected", true);
			//});
		
      });
//**************TRJ038 - ALEXANDER ROCA - 17/04/2019******************

}

function chececkTaxBP(){
	
	$('body').on('click', 'td', function(){
		//$( this ).attr( 'checked', true);
		//this.checked = true;
		//alert("click");
		var row = $(this).closest('tr');
		var item_id = row.attr('data-item-id');
		producto=$(this).attr('data-ul');
		
		var idBoton="tax_ICBPER_"+producto;
		
		document.getElementById(idBoton).checked = true;
		
		if(spoitems[item_id].tax_ICBPER == 0){
			spoitems[item_id].tax_ICBPER = 1;
			alert("Se va realizar el cobro del impuesto a las bolsas por la cantidad de productos de este Item (cantidad redondeada, si la cantidad es menor a 0.5 el monto del impuesto a las bolsas de este item sera 0)");
			//break;
		}else{
			spoitems[item_id].tax_ICBPER = 0;
			//alert("desactivado");
			//break;
		}
		spoitems[item_id].edit = 1;
            store('spoitems', JSON.stringify(spoitems));
            loadItems();
		
		
	});
	
}
