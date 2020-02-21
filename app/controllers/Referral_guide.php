<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Referral_guide extends MY_Controller
{

    function __construct() {
        parent::__construct();


        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        //$this->load->model('credit_note_model');
    }

    function index(){
		
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('referral_guide');
        $bc = array(array('link' => '#', 'page' => lang('referral_guide')));
        $meta = array('page_title' => lang('referral_guide'), 'bc' => $bc);
        $this->page_construct('referral_guide/index', $this->data, $meta);
      
    }
	
    function add($id_sale=null){
		
		/*$this->form_validation->set_rules('nota_GR', lang('note_GR'), 'trim|required');
		$this->form_validation->set_rules('Peso_Total_produc', lang('peso_transp'), 'trim|required|numeric');
		$this->form_validation->set_rules('packetNumber', lang('pack_number'), 'trim|required|numeric');
		$this->form_validation->set_rules('rucReceptor', lang('ruc_recep'), 'trim|required|numeric');
		$this->form_validation->set_rules('nameReceptor', lang('name_recep'), 'trim|required');
		$this->form_validation->set_rules('placa_GR', lang('placa_vehi'), 'trim|required');
		$this->form_validation->set_rules('RucTransportista', lang('ruc_transp'), 'trim|required|numeric');
		$this->form_validation->set_rules('fecha_inicio_trasladoGR', lang('transfer_start_date'), 'trim|required');*/
		$this->form_validation->set_rules('direcc_llegada', lang('direcc_llega'), 'trim|required');
		$this->form_validation->set_rules('direcc_salida', lang('direcc_sali'), 'trim|required');
		//$this->form_validation->set_rules('nameTransportista', lang('name_transp'), 'trim|required');
        
        if ($this->form_validation->run() == true) {
			
			/*$motive_trans=$this->input->post('motive_GR');
			$type_trans=$this->input->post('tipoTransporte_GR');
			$type_dni_receptor=$this->input->post('tipoDNI_Receptor');
			$type_dni_trsnport=$this->input->post('tipoDNI_transportista');
			$distri_salida=$this->input->post('distri_salida');
			$distri_llegada=$this->input->post('distri_llegada');
			
			if($motive_trans <= 0){
				$this->session->set_flashdata('error', "Se debe seleccionar alguna opcion del campo : ". lang("motive_trans"));
                redirect("Referral_guide/add");
			}
			
			if($type_trans <= 0){
				$this->session->set_flashdata('error', lang("mnsj_error_select"). lang("type_trans"));
                redirect("Referral_guide/add");
			}
			
			if($type_dni_receptor <= 0){
				$this->session->set_flashdata('error', lang("mnsj_error_select"). lang("type_dni_receptor"));
                redirect("Referral_guide/add");
			}
			
			if($type_dni_trsnport <= 0){
				$this->session->set_flashdata('error', lang("mnsj_error_select"). lang("type_dni_trsnport"));
                redirect("Referral_guide/add");
			}
			
			if($distri_salida <= 0){
				$this->session->set_flashdata('error', lang("mnsj_error_select"). lang("distri_salida"));
                redirect("Referral_guide/add");
			}
			
			if($distri_llegada <= 0){
				$this->session->set_flashdata('error', lang("mnsj_error_select"). lang("distri_llegada"));
                redirect("Referral_guide/add");
			}
			
			if(sizeof($_POST['product_id']) <= 0){
				$this->session->set_flashdata('error', lang("cant_items_RG"));
                redirect("Referral_guide/add");
			}*/
			
			if($this->input->post('packetNumber')==""){
				$packetNumber =0;
			}else{
				$packetNumber =$this->input->post('packetNumber');
			}
			
			if($this->input->post('placa_GR')==""){
				$placa_GR =0;
			}else{
				$placa_GR =$this->input->post('placa_GR');
			}
			
			if($this->input->post('nameTransportista')==""){
				$nameTransportista =0;
			}else{
				$nameTransportista =$this->input->post('nameTransportista');
			}
			
			/*if($this->input->post('RucTransportista')==""){
				$RucTransportista =0;
			}else{
				$RucTransportista =$this->input->post('RucTransportista');
			}*/
			
			if($this->input->post('tipoDNI_transportista')==0 || $this->input->post('RucTransportista')==""){
				$tipoDNI_transportista =0;
				$RucTransportista =0;
			}else{
				$tipoDNI_transportista =$this->input->post('tipoDNI_transportista');
				$RucTransportista =$this->input->post('RucTransportista');
			}
			
			if($this->input->post('nota_GR')==""){
				$noteGR ="-";
			}else{
				$noteGR =$this->input->post('nota_GR');
			}
			
			
			$header= array(
					  "ruc"=> (string)($this->session->userdata('ruc')),
					  "IssueDate"=> (string)(date("Y")."-".date("m")."-".date("d")),
					  "Note"=> (string)($noteGR),//($this->input->post('nota_GR')),
					  "DescriptionReasonTransfer"=> (string)($this->input->post('name_motive_GR')),//**********************
					  "TotalGrossWeightGRE"=> (string)($this->input->post('Peso_Total_produc')),
					  "NumberPackages"=> (string)($packetNumber), //($this->input->post('packetNumber')),
					  "unitCodeGrossWeightGRE"=> (string)($this->input->post('tipo_medida_produc')),
					  "TypeDocumenttransmitter"=> (string)("6"),
					  "addresseeID"=> (string)($this->input->post('rucReceptor')),
					  "TypeDocumentaddressee"=> (string)($this->input->post('tipoDNI_Receptor')),
					  "addresseeName"=> (string)($this->input->post('nameReceptor')),
					  "motivemovedCode"=> (string)($this->input->post('motive_GR')),//******************************
					  "transfermobility"=> (string)($this->input->post('tipoTransporte_GR')),
					  "LicensePlateID"=> (string)($placa_GR), //($this->input->post('placa_GR')),
					  "DriverPersonID"=> (string)($RucTransportista), //($this->input->post('RucTransportista')),
					  "DriverPersonDocumentType"=> (string)($tipoDNI_transportista), //($this->input->post('tipoDNI_transportista')),
					  "movedstartdate"=> (string)($this->input->post('fecha_inicio_trasladoGR')),
					  "DeliveryUbi"=> (string)($this->input->post('distri_llegada')),
					  "Delivery"=> (string)($this->input->post('direcc_llegada')),
					  "OriginAddressUbi"=> (string)($this->input->post('distri_salida')),
					  "OriginAddress"=> (string)($this->input->post('direcc_salida')),
					  "user_create"=> (string)($this->session->userdata('user_id')),
					  "nameTransportista"=> (string)($nameTransportista), //($this->input->post('nameTransportista'))
					  );
					  
			$cant_items = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
									
			if($cant_items > 0){
			
			$cont=1;
				for ($r = 0; $r < $cant_items; $r++) {
					if($_POST['product_code'][$r]==''){
						$codeproducto='---';
					}else{
						$codeproducto=$_POST['product_code'][$r];
					}					
					$item[$cont] =array(
						"ProductID"=> (string)($_POST['product_id'][$r]),
						"ProductCode"=> (string)($codeproducto),
						"ProductName"=> (string)($_POST['product_name'][$r]),
						"QuantityProduct"=> (string)($_POST['product_quantity'][$r]),
						"unitCode"=> (string)("NIU"),
					); 
					
					if($item[$cont]["QuantityProduct"] <= 0){
						$this->session->set_flashdata('error', lang("QuantityProduct_item"));
						redirect("Referral_guide/add");
					}
					
					/*if($item[$cont]["ProductID"] == 0){

						$item[$cont]["ProductID"] = $this->consult_product($_POST['product_code'][$r], $_POST['product_name'][$r]);
						
					}*/
					
					$cont++;
				}
			}else{
				
			}
			//************************************************************************
			$postData["header"] = $header;
			$postData["item"] = $item;
			
			$urlDoc = "public/api/v1/guia/register";
								
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'POST',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
				)
			));
									
			$url = $this->config->item('ws_almacen');
			$response = file_get_contents($url . $urlDoc, false, $context);
			$res = json_decode($response, true);
			
			//*************************************************************************
			//******************************codigo hash*******************************************
			
			$conslHash["ruc"] = (string)($this->session->userdata('ruc'));
			$conslHash["id_referral_g"] = $res[1];
			
			$urlDoc = "public/api/v1/guia/hash";
								
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'GET',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n",
				'content' => json_encode($conslHash)
				)
			));
									
			$url = $this->config->item('ws_almacen');
			$response = file_get_contents($url . $urlDoc, false, $context);
			$resHASH = json_decode($response, true);
			//*****************************codigo hash*********************************************
			require("funciones.php");
			include("app/class/QRCodeGenerator.class.php");
			$this->generar_pdf1($res, $header, $item, $resHASH["hash"]);
			
			
			$this->session->set_flashdata('message', lang("succes_Referral_guide").$res[0]);
			//redirect("referral_guide");
			redirect("Referral_guide/view/2/" . $res[1]);
			
		}else{
		
			if($id_sale != null){
				$this->data['sale_id']=$id_sale;
			}else{
				$this->data['sale_id']=0;
			}

			$context = stream_context_create(array(
				'http' => array(
				'method' => 'GET',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n"
				)
			));
									
			$urlDoc = "public/api/v1/catalogo03/medida";
			$url_NC = $this->config->item('ws_general');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);
			$res = json_decode($response);
			
			foreach($res as $medida){
				$medida_array[$medida->code]=$medida->description;
					
			}
			
			$this->data['medida_GR']= $medida_array;
			
			// ************************************************
			
			$data_envio = array(
				"ruc"=> (string)($this->session->userdata('ruc'))
			);
			
			$postData["info_envio"] = $data_envio;
			
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'GET',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
				)
			));
									
			$urlDoc = "public/api/v1/motive_guia_R";
			$url_NC = $this->config->item('ws_almacen');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);
			$res = json_decode($response);
			
			$motive_GR[0]="SELECCIONAR";
			
			foreach($res as $medida){
				$motive_GR[$medida->code]=$medida->description;
					
			}
			$this->data['motive_GR']= $motive_GR;
			// *****************************************************
			
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'GET',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n"
				)
			));
									
			$urlDoc = "public/api/v1/ubigeo/departamentos";
			$url_NC = $this->config->item('ws_general');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);
			$res = json_decode($response);
			
			$departamentList[0]="SELECCIONAR";
			
			foreach($res as $medida){
				$departamentList[$medida->depas]=$medida->depas;
					
			}
			
			$this->data['departamentList']= $departamentList;
			// *****************************************************
			
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'GET',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n"
				)
			));
									
			$urlDoc = "public/api/v1/catalogo18/tipoTransporte";
			$url_NC = $this->config->item('ws_general');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);
			$res = json_decode($response);
			
			$tipoTransporteList[0]="SELECCIONAR";
			
			foreach($res as $tipo){
				$tipoTransporteList[$tipo->code]=$tipo->description;
					
			}
			
			$this->data['tipoTransporteList']= $tipoTransporteList;
			// *****************************************************
			
			
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'GET',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n"
				)
			));
									
			$urlDoc = "public/api/v1/catalogo06/tipoDocumento";
			$url_NC = $this->config->item('ws_general');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);
			$res = json_decode($response);
			
			$type_document_ident[0]="SELECCIONAR";
			
			foreach($res as $medida){
				$type_document_ident[$medida->code]=$medida->description;
					
			}
			
			$this->data['type_document_ident']= $type_document_ident;
			
			
			//$this->form_validation->set_rules('correlative2', lang('correlative2'), 'required');
			$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['page_title'] = lang('referral_guide');
			$bc = array(array('link' => '#', 'page' => lang('referral_guide')));
			$meta = array('page_title' => lang('referral_guide'), 'bc' => $bc);
			$this->page_construct('referral_guide/add', $this->data, $meta);
				//$this->session->set_flashdata('warning', lang("valid_NC_correlative_max"));
                //	redirect("referral_guide/add");
		}
                
    }

	function search_provincia($depa){
		$dpr = str_replace("%20", " ", $depa);
		
		$postData["departamento"] = $dpr;
		
		$context = stream_context_create(array(
			'http' => array(
			'method' => 'GET',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
			)
		));
								
		$urlDoc = "public/api/v1/ubigeo/provincia";
		$url_NC = $this->config->item('ws_general');
		$response = file_get_contents($url_NC . $urlDoc, false, $context);

	
		echo $response;
		
		
	}
	
	function search_Distrito($provincia, $depa){
		$prostr = str_replace("%20", " ", $provincia);
		$depastr = str_replace("%20", " ", $depa);
		$postData["provincia"] = $prostr;
		$postData["departamento"] = $depastr;
		
		$context = stream_context_create(array(
			'http' => array(
			'method' => 'GET',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
			)
		));
								
		$urlDoc = "public/api/v1/ubigeo/distrito";
		$url_NC = $this->config->item('ws_general');
		$response = file_get_contents($url_NC . $urlDoc, false, $context);
	
		
		echo $response;
		
		
	}
	
	function search_Producto($product){
	  $postData["producto"] = $product;
	  $postData["ruc"]= (string)($this->session->userdata('ruc'));

		$context = stream_context_create(array(
			'http' => array(
			'method' => 'POST',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
			)
		));
								
		$urlDoc = "public/api/v1/guia/searchProduc";
		$url_NC = $this->config->item('ws_almacen');
		$response = file_get_contents($url_NC . $urlDoc, false, $context);
		echo $response;

  }

	function search_SaleItems($id){
			$postData["venta"] = $id;
			$postData["ruc"]= (string)($this->session->userdata('ruc'));
			
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'POST',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
				)
			));
									
			$urlDoc = "public/api/v1/sale_items";
			$url_NC = $this->config->item('ws_almacen');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);
			echo $response;
	}
	
	public function search_customer($id){
		
			$postData["id_sale"] = $id;
			$postData["ruc"]= (string)($this->session->userdata('ruc'));
			
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'POST',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
				)
			));
									
			$urlDoc = "public/api/v1/customer";
			$url_NC = $this->config->item('ws_almacen');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);

			echo $response;
		
	}
	
	function get_Referral_guide($estado){
    $this->load->library('datatables');

    $this->datatables->select($this->db->dbprefix('referral_guide') . ".id as pid,  date_create, addresseeName, if(transfermobility=1, 'Transporte público', 'Transporte privado'), DriverPersonID, serieNumero, LicensePlateID, DescriptionReasonTransfer, if(T2.estadoSend = 1, 'Error de envío', if(T2.estadoSend = 2, 'Enviado', if(T2.estadoSend = 3, 'Rechazado', if(T2.estadoSend = 4, 'Procesado', '')))) ", false); 
	$this->datatables->from('referral_guide');
	$this->datatables->join('(SELECT referral_guide_id, status as estadoSend FROM tec_send_invoice_gr WHERE estado=1) as T2', 'T2.referral_guide_id=referral_guide.id', 'left');

	$this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'>".
		"<a href='#' onClick=\"MyWindow=window.open('" . site_url('Referral_guide/view/1/$1') . "', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=500,height=600'); return false;\" title='" . lang("view_GR") . "' class='tip btn btn-primary btn-xs'><i class='fa fa-list'></i></a>".
		"<a href='" . site_url('Referral_guide/reenviarXML/$1') . "' onClick=\"return confirm('" . lang('forwarding_x_sale') . "')\" title='" . lang("forwarding_GR") . "' class='tip btn btn-warning btn-xs' ><i class='fa fa-paper-plane-o'></i></a>".
		  //"<a href='#' id='email_$1' onclick='sendEmail()' title='" . lang("email_XML_PDF") . "' class='send-mail tip btn btn-primary btn-xs' ><i class='fa fa-envelope-o'></i></a>".
          //"<a href='" . site_url('Credit_note/recreate_pdf/$1') . "' title='" . lang("recreate_pdf") . "' class='tip btn btn-danger btn-xs' ><i class='fa fa-file-pdf-o'></i></a>".
		  "</div></div>", "pid");
    /*
	$this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'>".
		  "<a href='#' id='email_$1' onclick='sendEmail()' title='" . lang("email_XML_PDF") . "' class='send-mail tip btn btn-primary btn-xs' ><i class='fa fa-envelope-o'></i></a>".
          "<a href='" . site_url('Credit_note/recreate_pdf/$1') . "' title='" . lang("recreate_pdf") . "' class='tip btn btn-danger btn-xs' ><i class='fa fa-file-pdf-o'></i></a>".
		  "<a href='" . site_url('Credit_note/reenviarXML/$1') . "' onClick=\"return confirm('" . lang('forwarding_x_sale') . "')\" title='" . lang("forwarding_NC") . "' class='tip btn btn-warning btn-xs' ><i class='fa fa-paper-plane-o'></i></a>
          </div></div>", "pid");*/
	
	
    $this->datatables->unset_column('pid');
    echo $this->datatables->generate();
  }
	
	function generar_pdf1($respuesta_ws, $header, $items, $cod_ash){

		$impRuc = "";
		$tipo_doc = "GUIA DE REMISION";

		$numComp = $respuesta_ws[0];

		//$arrD1 = explode(" ", $respuesta_ws[2]);

		$row_items_html = "";
		$quantity1="";
		$codigo="";
		$unidad_medida="";
		$nombreProducto1="";

		foreach ($items as $item) {
			$quantity = $item['QuantityProduct'];
			$name = $item['ProductName'];
			$code = $item['ProductCode'];
			$unidad = $item['unitCode'];

                   $quantity1.=$quantity.'<br><br>';
                   $codigo.=$code.'<br><br>';
                   $unidad_medida.=$unidad.'<br><br>';
                   $nombreProducto1.=$name.'<br><br>';
                   
            }
               $row_items_html=' <tr>
       				 				<td valign="top"align="center" HEIGHT="50"style="font-size:8px;border: 1px solid #000000">'.$quantity1.'</td>
       				 				<td valign="top"align="center"HEIGHT="50"style="font-size:8px;border: 1px solid #000000">'.$codigo.'</td>
        			 				<td valign="top"align="center"HEIGHT="50"style="font-size:8px;border: 1px solid #000000">'.$unidad_medida.'</td>
        			 				<td valign="top"HEIGHT="100"style="font-size:8px;border: 1px solid #000000">'.$nombreProducto1.'</td>
								</tr>';

			$l_ruc = "DOCUMENTO DE IDENTIDAD ";//lang("ccf2")."/".lang("ccf1");
			
			$emision="REPRESENTACIÓN IMPRESA DE LA GUIA DE REMISION EMISOR ELECTRÓNICA"; 
		$this->load->library('Pdf');

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle($tipo_doc . " - " . $numComp);
		$pdf->SetHeaderMargin(30);
		$pdf->SetTopMargin(20);
		$pdf->setFooterMargin(20);
		$pdf->SetAutoPageBreak(true);
		$pdf->SetAuthor('Author');
		$pdf->SetDisplayMode('real', 'default');

		$pdf->AddPage();

		$ivPorc = $this->Settings->default_tax_rate;
		$nombreCorto = strtoupper($this->Settings->site_name);

		$razon_social = $this->session->userdata('razon_social');
		$ruc = $this->session->userdata('ruc');
		$direccion = $this->session->userdata('direccion');
		$region = $this->session->userdata('region');
		$pais = $this->session->userdata('pais');
		$region_pais = strtoupper($region . " - " . $pais);
		$imagen = $this->session->userdata('logo');
		$pos_logo = $this->session->userdata('pos_logo');

		$imagen2 = $this->session->userdata('logo_pdf');
		$type_imagen_pdf = $this->session->userdata('type_imagen_pdf');


		$width = 334;
		$height = 102;

		if ($pos_logo == 2) {
			$width = 334;
			$height = 102;
		} else if ($pos_logo == 1) {
			$width = 84;
			$height = 102;
		}

		$_ruc = $l_ruc . " " . $ruc;

		//-----------------------------------QR-------------------------------------------//
		/*$serieNumero = explode('-', $numComp);
		$serie = $serieNumero[0];
		$numero = $serieNumero[1];
		$date = $arrD1[0];
		$hash = $cod_ash;

		$cusDoc = $header['TypeDocumentaddressee'];
		$docType = "09";
		$numDoc = $header['addresseeID'];
		$qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero  . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
		
		$ex1 = new QRCodeGenerator();
		$qr_img = '<img src="' . $ex1->generate($serieNumero[0].$serieNumero[1],$qr) . '" width="80" height="80">';*/

	if ($type_imagen_pdf == 1){	
		$cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen) . '" width="' . $width . '" height="' . $height . '">';
		$cabecera2 = '<div><br>' . $razon_social . '<br>' . $direccion . '<br>' . $region_pais . '<br>' . $_ruc.'</div>';
           } 
	else if ($type_imagen_pdf == 2) {
		$width2 = 370;
		$height2 = 150;
		$cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen2) . '" width="' . $width2 . '" height="' . $height2 . '">';
		$cabecera2 ="";
    }
//****************************
	$fechaEmision=$header['IssueDate'];
	$fechaInicioT=$header['movedstartdate'];
	$motivoTranslado=$header['DescriptionReasonTransfer'];
	//$tipoTranslado=$header[''];
	$PesoBruto=$header['TotalGrossWeightGRE'];
	$unidadDePeso=$header['unitCodeGrossWeightGRE'];
	//**********************************
	$postData["tip_transporte"] = $header['transfermobility'];
			
	$context = stream_context_create(array(
		'http' => array(
		'method' => 'GET',
		'header' => "Authorization: application/json\r\n" .
		"Content-Type: application/json\r\n",
		'content' => json_encode($postData)
		)
	));
									
	$urlDoc = "public/api/v1/catalogo18/tipoTransporte_consult";
	$url_NC = $this->config->item('ws_general');
	$response = file_get_contents($url_NC . $urlDoc, false, $context);
	$res = json_decode($response);
	
	$modalidadTransporte=$res->description;
	$nameRazonSocial=$header['addresseeName'];
	$DNIruc=$header['addresseeID'];
	$rucTransportista=$header['DriverPersonID'];
	$nameTransportista=$header['nameTransportista'];
	
	/*if($header['DriverPersonID'] == 0){
		$rucTransportista="-";
	}else{
		$rucTransportista=$header['DriverPersonID'];
	}*/
	
	/*if($nameTransportista == 0){
		$nameTransportista="-";
	}*/
	
	$placa=$header['LicensePlateID'];
	
	if($header['NumberPackages'] == 0){
		$NumberPackages="-";
	}else{
		$NumberPackages = $header['NumberPackages'] ;
	}
	//$UbigeoInicio =$header['OriginAddressUbi'];
	$direccINICIO=$header['OriginAddress'];
	//$UbigeoFin =$header['DeliveryUbi'];
	$direccFIN =$header['Delivery'];
	$notas=$header['Note'];
	
	//*************ubigeo description Inicio***************
	$postData["ubigeo"] = $header['OriginAddressUbi'];
			
	$context = stream_context_create(array(
		'http' => array(
		'method' => 'GET',
		'header' => "Authorization: application/json\r\n" .
		"Content-Type: application/json\r\n",
		'content' => json_encode($postData)
		)
	));
									
	$urlDoc = "public/api/v1/ubigeo/direccion";
	$url_NC = $this->config->item('ws_general');
	$response = file_get_contents($url_NC . $urlDoc, false, $context);
	$res = json_decode($response);
	
	$UbigeoInicio=$res->departamento ." - ". $res->provincia ." - ". $res->distrito ;
	//*************ubigeo description Inicio***************
	
	//*************ubigeo description Inicio***************
	$postData["ubigeo"] = $header['DeliveryUbi'];
			
	$context = stream_context_create(array(
		'http' => array(
		'method' => 'GET',
		'header' => "Authorization: application/json\r\n" .
		"Content-Type: application/json\r\n",
		'content' => json_encode($postData)
		)
	));
									
	$urlDoc = "public/api/v1/ubigeo/direccion";
	$url_NC = $this->config->item('ws_general');
	$response = file_get_contents($url_NC . $urlDoc, false, $context);
	$res = json_decode($response);
	
	$UbigeoFin=$res->departamento ." - ". $res->provincia ." - ". $res->distrito ;
	//*************ubigeo description Inicio***************
	
	
//****************************
	$u=$this->site->getURLconsulta();
	$url=$u->footer;
	$tbl1 = <<<EOD
	<table border="0" width="100%" cellspacing="0" cellpadding="2" >
		<tr>
			<td width="70%" align="center" style="font-size:8px">
			$cabecera
			</td>
			<td width="30%" valign="middle"><br><br><br>
				<table border="1" width="100%" cellspacing="0" cellpadding="2" >
					<tr>
						<td align="center"  style="font-size:10px"><h2 style="font-size:8px">$_ruc</h2></td>
					</tr>
					<tr>
						<td align="center" bgcolor="#E4E4E4" style="font-size:10px"><h2 style="font-size:8px">$tipo_doc</h2></td>
					</tr>
					<tr>
						<td align="center" style="font-size:10px"><h2 style="font-size:8px">$numComp</h2></td>
					</tr>
				</table><br>
			</td>
		</tr>
	</table>
	<br>
	<br>
	
	<table width="100%">
		<tr>
			<td>DATOS DEL INICIO DEL TRASLADO</td>
		</tr>
	</table>
	<br>

	<table width="100%" cellspacing="0" cellpadding="" >
		
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">FECHA DE EMISION:</strong></td>
			<td align="left" style="font-size:10px">$fechaEmision</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">FECHA DE INICIO DE TRANSLADO:</strong></td>
			<td align="left" style="font-size:10px">$fechaInicioT</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">MOTIVO DE TRANSLADO:</strong></td>
			<td align="left" style="font-size:10px">$motivoTranslado</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">MODALIDAD DE TRANSPORTE:</strong></td>
			<td align="left" style="font-size:10px">$modalidadTransporte</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">PESO BRUTO TOTAL DE LA GUÍA ($unidadDePeso):</strong></td>
			<td align="left" style="font-size:10px">$PesoBruto</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">NÚMERO DE PAQUETES:</strong></td>
			<td align="left" style="font-size:10px">$NumberPackages</td>
		</tr>

	</table>
	<br>
	<br>
	<br>
	<table width="100%">
		<tr>
			<td>DATOS DEL DESTINATARIO</td>
		</tr>
	</table>
	<br>

	<table width="100%" cellspacing="0" cellpadding="" >
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN:</strong></td>
			<td align="left" style="font-size:10px">$nameRazonSocial</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">DOCUMENTO DE IDENTIDAD:</strong></td>
			<td align="left" style="font-size:10px">$DNIruc</td>
		</tr>
	</table>
	<br>
	<br>
	<br>
	<table width="100%">
		<tr>
			<td>DATOS DEL TRANSPORTISTA</td>
		</tr>
	</table>
	<br>
	<table border="1" width="100%" cellspacing="0" cellpadding="" >
		<tr>
			<td bgcolor="#1B3752" align="center" width="30%" style="font-size:10px;color: #fefeff">
			<strong style="font-size:7px;color: #fefeff">NUMERO DE RUC / DNI</strong>
			</td>
			<td bgcolor="#1B3752" align="center" width="40%" style="font-size:10px;color: #fefeff">
			<strong style="font-size:7px;color: #fefeff">NOMBRE / RAZON SOCIAL</strong>
			</td>
			<td bgcolor="#1B3752" align="center" width="30%" style="font-size:10px;color: #fefeff">
			<strong style="font-size:7px;color: #fefeff">PLACA DE VEHÍCULO</strong>
			</td>
		</tr>

		<tr>
			<td style="font-size:8px"  align="center">$rucTransportista</td>
			<td style="font-size:8px"  align="center">$nameTransportista</td>
			<td style="font-size:8px"  align="center">$placa</td>
		</tr>

	</table>
	<br>
	<br>
	<br>
	
	<table width="100%">
		<tr>
			<td>DATOS DEL PUNTO DE PARTIDA Y PUNTO DE LLEGADA</td>
		</tr>
	</table>
	<br>

	<table width="100%" cellspacing="0" cellpadding="" >
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:11px">DIRECCIÓN DEL PUNTO DE PARTIDA :</strong></td>
			<td align="left" style="font-size:10px">$UbigeoInicio - $direccINICIO</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:11px">DIRECCIÓN DEL PUNTO DE LLEGADA :</strong></td>
			<td align="left" style="font-size:10px">$UbigeoFin - $direccFIN</td>
		</tr>
	</table>
	<br>
	<br>
	<br>

	<table width="100%">
		<tr>
			<td>PRODUCTOS TRANSPORTADOS</td>
		</tr>
	</table>
	<br>
	<table border="1" width="100%" cellspacing="0" cellpadding="">
		<tr>
		<th bgcolor="#1B3752" align="center" width="8%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CANTIDAD</th>
		<th bgcolor="#1B3752" align="center" width="17%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CODIGO</th>
		<th bgcolor="#1B3752" align="center" width="16%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">UNIDAD MEDIDA</th>
		<th bgcolor="#1B3752" align="center" width="59%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">DESCRIPCION</th>
	
	</tr>
		$row_items_html

	</table>
	<br>
	<br>

	<table width="100%" cellspacing="0" cellpadding="" >
		<tr>
			<td align="left" style="font-size:10px" width="20%"><strong style="font-size:9px">OBSERVACIONES :</strong></td>
			<td align="left" style="font-size:10px">$notas</td>
		</tr>
	</table>
	 
EOD;
/*
$tbl1 = <<<EOD
	<table border="0" width="100%" cellspacing="0" cellpadding="2" >
		<tr>
			<td width="70%" align="center" style="font-size:8px">
			$cabecera
			</td>
			<td width="30%" valign="middle"><br><br><br>
				<table border="1" width="100%" cellspacing="0" cellpadding="2" >
					<tr>
						<td align="center"  style="font-size:10px"><h2 style="font-size:8px">$_ruc</h2></td>
					</tr>
					<tr>
						<td align="center" bgcolor="#E4E4E4" style="font-size:10px"><h2 style="font-size:8px">$tipo_doc</h2></td>
					</tr>
					<tr>
						<td align="center" style="font-size:10px"><h2 style="font-size:8px">$numComp</h2></td>
					</tr>
				</table><br>
			</td>
		</tr>
	</table>
	<br>
	<br>
	
	<table width="100%">
		<tr>
			<td>DATOS DEL INICIO DEL TRASLADO</td>
		</tr>
	</table>
	<br>
	<br>
	<table width="100%" cellspacing="0" cellpadding="" >
		
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">FECHA DE EMISION:</strong></td>
			<td align="left" style="font-size:10px">$fechaEmision</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">FECHA DE INICIO DE TRANSLADO:</strong></td>
			<td align="left" style="font-size:10px">$fechaInicioT</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">MOTIVO DE TRANSLADO:</strong></td>
			<td align="left" style="font-size:10px">$motivoTranslado</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">MODALIDAD DE TRANSPORTE:</strong></td>
			<td align="left" style="font-size:10px">$modalidadTransporte</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">PESO BRUTO TOTAL DE LA GUÍA ($unidadDePeso):</strong></td>
			<td align="left" style="font-size:10px">$PesoBruto</td>
		</tr>

	</table>
	<br>
	<br>
	
	<table width="100%">
		<tr>
			<td>DATOS DEL DESTINATARIO</td>
		</tr>
	</table>
	<br>
	<br>
	<table width="100%" cellspacing="0" cellpadding="" >
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN:</strong></td>
			<td align="left" style="font-size:10px">$nameRazonSocial</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">DOCUMENTO DE IDENTIDAD:</strong></td>
			<td align="left" style="font-size:10px">$DNIruc</td>
		</tr>
	</table>
	<br>
	<br>
	<table width="100%">
		<tr>
			<td>DATOS DEL TRANSPORTISTA</td>
		</tr>
	</table>
	<br>
	<table border="1" width="100%" cellspacing="0" cellpadding="" >
		<tr>
			<td bgcolor="#1B3752" align="center" width="50%" style="font-size:10px;color: #fefeff">
			<strong style="font-size:7px;color: #fefeff">NUMERO DE RUC / DNI</strong>
			</td>
			<td bgcolor="#1B3752" align="center" width="50%" style="font-size:10px;color: #fefeff">
			<strong style="font-size:7px;color: #fefeff">NOMBRE / RAZON SOCIAL</strong>
			</td>
		</tr>

		<tr>
			<td style="font-size:8px"  align="center">$rucTransportista</td>
			<td style="font-size:8px"  align="center">$nameTransportista</td>
		</tr>

	</table>
	<br>
	<br>
	
	<table width="100%">
		<tr>
			<td>DATOS DEL PUNTO DE PARTIDA Y PUNTO DE LLEGADA</td>
		</tr>
	</table>
	<br>
	<br>
	<table width="100%" cellspacing="0" cellpadding="" >
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:11px">DIRECCIÓN DEL PUNTO DE PARTIDA :</strong></td>
			<td align="left" style="font-size:10px">$UbigeoInicio - $direccINICIO</td>
		</tr>
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:11px">DIRECCIÓN DEL PUNTO DE LLEGADA :</strong></td>
			<td align="left" style="font-size:10px">$UbigeoFin - $direccFIN</td>
		</tr>
	</table>
	<br>
	<br>

	<table border="1" width="100%" cellspacing="0" cellpadding="">
		<tr>
		<th bgcolor="#1B3752" align="center" width="8%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CANTIDAD</th>
		<th bgcolor="#1B3752" align="center" width="17%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CODIGO</th>
		<th bgcolor="#1B3752" align="center" width="16%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">UNIDAD MEDIDA</th>
		<th bgcolor="#1B3752" align="center" width="59%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">DESCRIPCION</th>
	
	</tr>
		$row_items_html

	</table>
	<br>
	<br>
	<table width="100%" cellspacing="0" cellpadding="" >
		<tr>
			<td align="left" style="font-size:10px" width="40%"><strong style="font-size:9px">OBSERVACIONES :</strong></td>
			<td align="left" style="font-size:10px">$notas</td>
		</tr>
	</table>
	<br>
	  
	<p>LEYENDA</p>
	<table border="1" width="100%" cellspacing="0" cellpadding="2">
		<tr>
			<td style="font-size:7px">
				OPERACION SUJETA AL SISTEMA DE PAGO DE OBLIGACIONES TRIBUTARIAS CON EL GOBIERNO CENTRAL BCO.DE LA NACION NRO:00101044054/12.00 (OPERACIONES AFECTADAS 8/700.00 SOLES POR CLIENTE) RESOLUCION DE SUPERINTENDENCIA N° 207-20004/SUNAT
			</td>
		</tr>
	</table>
	<br><br>	
	<table>
		<tr style="font-size:7px">
		<td align="left" width="10%">$qr_img</td>                     
		<td  width="62%" style="font-size:7px;vertical-align: bottom"></td>
		</tr>
	</table>
	<br>
EOD;
*/
$pdf->writeHTML($tbl1, true, false, false, false, '');
		
		
			$postData["ruc"] = $this->session->userdata('ruc');
			$postData["id_referral_g"] = $respuesta_ws[1];
			
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'GET',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
				)
			));
									
			$urlDoc = "public/api/v1/guia/pdfFile_consult";
			$url_NC = $this->config->item('ws_almacen');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);
			$respuesta = json_decode($response);
			
		if(!$file = $respuesta->file_name){
			$nombPDF = randomstring(20);

				$postData["ruc"] = $this->session->userdata('ruc');
				$postData["id_referral_g"] = $respuesta_ws[1];
				$postData["file_name"] = $nombPDF . '.pdf';

				$context = stream_context_create(array(
				'http' => array(
				'method' => 'POST',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
				)
				));
										
				$urlDoc = "public/api/v1/guia/pdfFile_create";
				$url_NC = $this->config->item('ws_almacen');
				$response = file_get_contents($url_NC . $urlDoc, false, $context);

				$pdf->Output(FCPATH . 'uploads/pdf/guia_remision/' . $nombPDF . '.pdf', 'F');

				return true;

		}else{
			
			$pdf->Output(FCPATH . 'uploads/pdf/guia_remision/' . $file, 'F');

			return true;
		}

		return false;

	}

	function email_receipt($sale_id = null, $to = null){
		require("funciones.php");

		if ($this->input->post('id')) {
			$sale_id = $this->input->post('id');
		}

		if ($this->input->post('email')) {
			$to = $this->input->post('email');
		}

		if (!$sale_id || !$to) {
			die();
		}
		
		$id=$sale_id;
		
		//*****************************************
			$postData["ruc"] = $this->session->userdata('ruc');
			$postData["id_referral_g"] = $id;
			
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'GET',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
				)
			));
									
			$urlDoc = "public/api/v1/guia/pdfFile_consult";
			$url_NC = $this->config->item('ws_almacen');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);
			$respuesta = json_decode($response);
		//***************************************** 
		
		//*****************************************
		$postData["ruc"] = $this->session->userdata('ruc');
			$postData["id_referral_g"] = $id;
			
			$context = stream_context_create(array(
				'http' => array(
				'method' => 'GET',
				'header' => "Authorization: application/json\r\n" .
				"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
				)
			));
									
			$urlDoc = "public/api/v1/guia/header";
			$url_NC = $this->config->item('ws_almacen');
			$response = file_get_contents($url_NC . $urlDoc, false, $context);
			$respuesta_header = json_decode($response);
		//*****************************************
		

		//$inv = $this->credit_note_model->getNoteCreditID($id);

		$this->load->helper('text');
		
		$path = base_url() . 'uploads/pdf/guia_remision/';
		$file_name = $respuesta->file_name;

		$arrD1 = explode(" ", $respuesta_header->IssueDate);
		
			$descripTipo="Guia de remision";
			$tipo = "09";
		
		$direccion="uploads/xml/";
		
		if (!file_exists($direccion)){
			mkdir($direccion);
		}
		
		$ubicacionArchivo=$direccion.$this->session->userdata('ruc')."-".$tipo."-".$respuesta_header->serieNumero.".xml";
		
		if (file_exists($ubicacionArchivo)){
		}else{
			
			$itemArray = array(
				"ruc" => $this->session->userdata('ruc'),
				"tipoComprobante" => $tipo,
				"beta" => $this->session->userdata('beta'),
				"Correlativo" => $respuesta_header->serieNumero
			);

		$postData["item"] = $itemArray;

		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => "Authorization: application/json\r\n" .
					"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
			)
		));
		
		$urlDoc = "post/doc/xml";
		$url = $this->config->item('aws_url');
		$response = file_get_contents($url . $urlDoc, false, $context);
		
		if($response){
			$respuestaXml = json_decode($response, true);
			
			$contador=0;
			foreach ($respuestaXml as $resp){
				$datosXml[$contador]=$resp;
				$contador=$contador+1;
			}
		}
		
		$datosConvertidos = implode(array_map("chr", $datosXml));
		
			
			$archivo = fopen($ubicacionArchivo, "w");
			fwrite($archivo,$datosConvertidos);
			fclose($archivo);
			
		   }
		
		$documentoIdentid=$respuesta_header->addresseeID;
		
		$receipt = "<html>Estimado Cliente: <br><br>Por la presente les comunicamos que la empresa " . $this->session->userdata('razon_social') . ", emisora de comprobantes electronicos le ha emitido el siguiente comprobante:<br><br><br>Tipo de documento: " . $descripTipo . "<br><br>Serie y numero: " . $respuesta_header->serieNumero . "<br><br>Nro RUC/DNI/CE del cliente: ". $documentoIdentid ."<br><br>Razon social cliente: ". $respuesta_header->addresseeName ."<br><br>Fecha de emision: " . $arrD1[0] ."<br><br>Direccion de origen: " . $respuesta_header->OriginAddress  ."<br><br>Direccion de Entrega: " . $respuesta_header->Delivery  ."<br><br>Numero de placa: " . $respuesta_header->LicensePlateID . "<br><br><br>Atentamente,<br><br>" . $this->session->userdata('razon_social') . "<br><br><br>Nota:<br>*En este correo electronico hemos eliminado las tildes para evitar errores en su visualizacion.<br></html>";
		
		$subject = $descripTipo . " Electronica " . $respuesta_header->serieNumero ." ".$this->session->userdata('razon_social') ;
		
		if ($this->tec->send_email($to, $subject, $receipt, null, null, $path . $file_name, null, null, $respuesta_header->serieNumero . '.pdf', $ubicacionArchivo, $respuesta_header->serieNumero . '.xml' )) {
		
			echo json_encode(array('msg' => lang("email_success")));

		} else {

			echo json_encode(array('msg' => lang("email_failed")));

		}
	}
	
	function consult_product($code, $name){
		
		$postData["codeP"] = (string)($code);
		$postData["nameP"] = (string)($name);
		$postData["ruc"]= (string)($this->session->userdata('ruc'));
		
		$context = stream_context_create(array(
			'http' => array(
			'method' => 'POST',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
			)
		));
							
		$urlDoc = "public/api/v1/guia/product";
		$url_NC = $this->config->item('ws_almacen');
		$response = file_get_contents($url_NC . $urlDoc, false, $context);
		$respuesta_producto = json_decode($response);
		
		//$code_producto = explode($respuesta_producto->id,'"');
		return $respuesta_producto->id;
		//return $response;
	}
	
	function view($noprint = null, $GR_id = null){
		
		$postData["ruc"] = $this->session->userdata('ruc');
		$postData["id_referral_g"] = $GR_id;
			
		$context = stream_context_create(array(
			'http' => array(
			'method' => 'GET',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
			)
		));
									
		$urlDoc = "public/api/v1/guia/pdfFile_consult";
		$url_NC = $this->config->item('ws_almacen');
		$response = file_get_contents($url_NC . $urlDoc, false, $context);
		$respuesta = json_decode($response);
		
		$this->data['file_pdf'] = $respuesta->file_name;
		$this->data['noprint'] = $noprint;
		$this->data['vista'] = $noprint;
		$this->data['id'] = $GR_id;
			
		$this->load->view($this->theme . 'referral_guide/view_pdf', $this->data);
		
	}
	
	function reenviarXML($id=null){
		
		$postData["ruc"] = $this->session->userdata('ruc');
		$postData["id_referral_g"] = $id;
		$postData["user_create"] = $this->session->userdata('user_id');
			
		$context = stream_context_create(array(
			'http' => array(
			'method' => 'POST',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
			)
		));
									
		$urlDoc = "public/api/v1/guia/ReenviarXml";
		$url_NC = $this->config->item('ws_almacen');
		$response = file_get_contents($url_NC . $urlDoc, false, $context);
		$respuesta = json_decode($response);
		
		if(!$respuesta){
			
			$this->session->set_flashdata('warning', "Error al reenviar");
			redirect("referral_guide");
			
		}else{
			
			if($respuesta[0] == 4){
				
				//*****************************************
				$postData["ruc"] = $this->session->userdata('ruc');
				$postData["id_referral_g"] = $id;
				
				$context = stream_context_create(array(
					'http' => array(
					'method' => 'GET',
					'header' => "Authorization: application/json\r\n" .
					"Content-Type: application/json\r\n",
					'content' => json_encode($postData)
					)
				));
										
				$urlDoc = "public/api/v1/guia/header";
				$url_NC = $this->config->item('ws_almacen');
				$response = file_get_contents($url_NC . $urlDoc, false, $context);
				$respuesta_header = json_decode($response);
				//*****************************************
				
				$header= array(
					  "ruc"=> (string)($this->session->userdata('ruc')),
					  "IssueDate"=> (string)($respuesta_header->IssueDate),//(date("Y")."-".date("m")."-".date("d")),
					  "Note"=> (string)($respuesta_header->Note),//($noteGR),
					  "DescriptionReasonTransfer"=> (string)($respuesta_header->DescriptionReasonTransfer),//($this->input->post('name_motive_GR')),
					  "TotalGrossWeightGRE"=> (string)($respuesta_header->TotalGrossWeightGRE),//($this->input->post('Peso_Total_produc')),
					  "NumberPackages"=> (string)($respuesta_header->NumberPackages),//($packetNumber), 
					  "unitCodeGrossWeightGRE"=> (string)($respuesta_header->unitCodeGrossWeightGRE),//($this->input->post('tipo_medida_produc')),
					  "TypeDocumenttransmitter"=> (string)("6"),
					  "addresseeID"=> (string)($respuesta_header->addresseeID),//($this->input->post('rucReceptor')),
					  "TypeDocumentaddressee"=> (string)($respuesta_header->TypeDocumentaddressee),//($this->input->post('tipoDNI_Receptor')),
					  "addresseeName"=> (string)($respuesta_header->addresseeName),//($this->input->post('nameReceptor')),
					  "motivemovedCode"=> (string)($respuesta_header->motivemovedCode),//($this->input->post('motive_GR')),
					  "transfermobility"=> (string)($respuesta_header->transfermobility),//($this->input->post('tipoTransporte_GR')),
					  "LicensePlateID"=> (string)($respuesta_header->LicensePlateID),//($placa_GR), 
					  "DriverPersonID"=> (string)($respuesta_header->DriverPersonID),//($RucTransportista), 
					  "DriverPersonDocumentType"=> (string)($respuesta_header->DriverPersonDocumentType),//($tipoDNI_transportista),
					  "movedstartdate"=> (string)($respuesta_header->movedstartdate),//($this->input->post('fecha_inicio_trasladoGR')),
					  "DeliveryUbi"=> (string)($respuesta_header->DeliveryUbi),//($this->input->post('distri_llegada')),
					  "Delivery"=> (string)($respuesta_header->Delivery),//($this->input->post('direcc_llegada')),
					  "OriginAddressUbi"=> (string)($respuesta_header->OriginAddressUbi),//($this->input->post('distri_salida')),
					  "OriginAddress"=> (string)($respuesta_header->OriginAddress),//($this->input->post('direcc_salida')),
					  "user_create"=> (string)($respuesta_header->user_create),//($this->session->userdata('user_id')),
					  "nameTransportista"=> (string)($respuesta_header->nameTransportista)//($nameTransportista)
					  );
				
				//*****************************************
				$postData["ruc"] = $this->session->userdata('ruc');
				$postData["id_referral_g"] = $id;
				
				$context = stream_context_create(array(
					'http' => array(
					'method' => 'GET',
					'header' => "Authorization: application/json\r\n" .
					"Content-Type: application/json\r\n",
					'content' => json_encode($postData)
					)
				));
										
				$urlDoc = "public/api/v1/guia/items";
				$url_NC = $this->config->item('ws_almacen');
				$response = file_get_contents($url_NC . $urlDoc, false, $context);
				$respuesta_item = json_decode($response);
				//*****************************************
				$cont=1;
				foreach($respuesta_item as $r_item){
					
					$item[$cont] =array(
						"ProductID"=> (string)($r_item->ProductID),
						"ProductCode"=> (string)($r_item->ProductCode),
						"ProductName"=> (string)($r_item->ProductName),
						"QuantityProduct"=> (string)($r_item->QuantityProduct),
						"unitCode"=> (string)($r_item->unitCode)
					);
					$cont++;
				}
				
				//******************************codigo hash*******************************************
			
				$conslHash["ruc"] = (string)($this->session->userdata('ruc'));
				$conslHash["id_referral_g"] = $id;
				
				$urlDoc = "public/api/v1/guia/hash";
									
				$context = stream_context_create(array(
					'http' => array(
					'method' => 'GET',
					'header' => "Authorization: application/json\r\n" .
					"Content-Type: application/json\r\n",
					'content' => json_encode($conslHash)
					)
				));
										
				$url = $this->config->item('ws_almacen');
				$response = file_get_contents($url . $urlDoc, false, $context);
				$resHASH = json_decode($response, true);
				//*****************************codigo hash*********************************************
				
				$res[0]=$respuesta[1];
				$res[1]=$id;
				
				require("funciones.php");
				include("app/class/QRCodeGenerator.class.php");
				$this->generar_pdf1($res, $header, $item, $resHASH["hash"]);
				
				
				$this->session->set_flashdata('message', lang("succes_Referral_guide").$respuesta[1]);
				redirect("referral_guide");
			
			}else{
				
				if($respuesta[0] == 0){
				
					$this->session->set_flashdata('warning', "Solo se pueden reenviar Guías con error de envio");
					redirect("referral_guide");
			
				}else{
					
					$this->session->set_flashdata('error', "Error al reenviar Guía de remision :".$respuesta[1]);
					redirect("referral_guide");
					
				}
				
			}
			
		}
		
	}
	
}
