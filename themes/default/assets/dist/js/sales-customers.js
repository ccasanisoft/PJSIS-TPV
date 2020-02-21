   $(document).on("click","#add_customer", function(e) {
        e.preventDefault();
        $('#ss').remove();
		var documentType = document.getElementById("document_type1").value;
		var name = document.getElementById("cname").value;
		var mail = document.getElementById("cemail").value;
		var phone = document.getElementById("cphone").value;
		var dni = document.getElementById("cf1").value;
		var ruc = document.getElementById("cf2").value;
		var direction = document.getElementById("cdireccion").value;
		var customerType = document.getElementById("customer_type").value;
		var sn = document.getElementById("sn");
        var sj = document.getElementById("sj");
         var t =document.getElementById("token");

      
        var type;
        if(sn.style.color == 'rgb(0, 0, 0)'){
        	type=sn.value;
        	if(dni == ''){
        		dni='--';
        	}
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

  if(sn || sj){

		$.ajax({	
			url: base_url+'customers/insertCustomer',
			type: 'POST',
            dataType:'json',
			data:{'spos_token':t.value,'data':da},
			success:function(rs){
				console.log(rs)
				   if(rs) {
                     location.reload();
                    $('#customerModal').modal('hide');
			}else{
			 console.log('')
			}
		}
		});
   
     
	

}else{
	$('#ss').remove();
	$('#sss').remove();
	createdAlert();
	createdAlert2();
}
       
  });






HideAlert();

document.getElementById("customer_type").value=0;
$(document).on('change', '#customer_type', function(event) {
	event.preventDefault();
	console.log(this.value)
});

 function HideAlert(){
 	
 	$(document).on('change', '#document_type1', function(event) {
 		event.preventDefault();
     

 		 if(this.value != ''){
 		 	if(document.getElementById("c-s").textContent == ''){
            
           $('#c-alert').hide();
           
 		 	}
			
 		   console.log(this.value)
 		    $('#ss').remove();
 		}else{
 			 
 			createdAlert();
 		

             
 		}
 	});
 	$(document).on('change', '#customer_type', function(event) {
 		event.preventDefault();

 		 if(this.value != 0){
			$('#c-alert').hide();
			
 		   console.log(this.value)
 		    $('#sss').remove();
 		}else{
 			 
 			createdAlert2();

             
 		}
 	});

 	  
 }


 
 function createdAlert(){
 	              var htm='';
 	                  htm+='<li id="ss"><span>Seleccione Tipo de documento</span></li>';
           	         $('#c-s').append(htm);
                     $('#c-alert').show();

 }

 function createdAlert2(){
 	              var htm='';
 	                  htm+='<li id="sss"><span>Seleccione Tipo de Cliente</span></li>';
           	         $('#c-s').append(htm);
                     $('#c-alert').show();


 }



function clear(){
	document.getElementById("customer_type").value =0;
	
	document.getElementById("cname").value='';
	document.getElementById("cemail").value='';
	document.getElementById("cphone").value='';
	document.getElementById("cf1").value='';
	document.getElementById("cf2").value='';
	document.getElementById("cdireccion").value='';
	document.getElementById("customer_type").value='';
	document.getElementById("sn").value='';
	document.getElementById("sj").value='';
	selectDefault();
}


 selectDefault();
$(document).on('click', '#sn', function(event) {
event.preventDefault();
	/* Act on the event */
	var dos = document.getElementById("sj");
	
	cambiarcolor(this,dos)
	console.log(this.id)
	
	document.getElementById("cf1").disabled =false;  
	document.getElementById("nombre-persona").textContent='Nombre';
	document.getElementById("document_type1").disabled=false;
	document.getElementById("select2-document_type1-container").textContent="Seleccione Tipo de Documento";

});

$(document).on('click', '#sj', function(event) {
	event.preventDefault();
	/* Act on the event */
	var dos = document.getElementById("sn");
	cambiarcolor(this,dos)
	console.log(this.id)
	document.getElementById("cf1").disabled =true;
	document.getElementById("nombre-persona").textContent='Razon Social';
	document.getElementById("document_type1").disabled=true;
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

function selectDefault(){
	var sn = document.getElementById("sn");
	sn.style.color='#000';
	sn.style.background ='#000';
	console.log(sn.value)
}


function obtenerDatos2(){
	$("#c-s").remove();
 var ruc=document.getElementById('cf2').value;
  if (ruc>9999999999) {
    $('#rucalert2').hide('fad');
      $.ajax({
        type:'get',
          url: 'https://cors-anywhere.herokuapp.com/http://api.ateneaperu.com/api/Sunat/Ruc',
          data:{'sNroDocumento':ruc},
        success: function(datos) {
        	console.log(datos)
        $('#cargando').hide('fade');
           if (datos.success===true) {
              $('#rucalert').hide('fade');
              document.getElementById('cname').value = datos.nombre_o_razon_social; 
              document.getElementById('cdireccion').value = datos.direccion_completa;            
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
        $('#cargando').show('fade');         
        },
    });
  }else{
    $('#rucalert2').show('fad');
  }
}