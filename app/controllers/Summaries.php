<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Summaries extends MY_Controller
{

    function __construct() {
        parent::__construct();


        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('summaries_model');
    }


    function index()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('summaries');
        $bc = array(array('link' => '#', 'page' => lang('summaries')));
        $meta = array('page_title' => lang('summaries'), 'bc' => $bc);
        $this->page_construct('summaries/index', $this->data, $meta);
    }

    function get_summaries()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('send_invoice') . ".id as pid, issue_date, processed_date, if(type = 'RB','Registro de Boleta', if(type = 'AB','Anulación de Boleta', if(type = 'AF','Anulación de Factura',''))),reference_date , file_name, if(status = 1, 'Error de envío', if(status = 2, 'Envío correcto', if(status = 3, 'Rechazado', if(status = 4, 'Declarado correctamente', '')))) as estado, ticket, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name) as created_by, observations", FALSE)
            ->join('users', 'users.id=send_invoice.uCrea', 'left')
            ->where('type NOT LIKE "%RF%"')
			->where('estado = 1')
            ->from("send_invoice");
		
		$this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('summaries/reenvioSunatResum/$1') . "' onClick=\"return confirm('" . lang('forwarding_x_resum') . "')\" title='" . lang("forwarding_resum") . "' class='tip btn btn-warning btn-xs' ><i class='fa fa-paper-plane-o'></i></a>
          </div></div>", "pid");

        $this->datatables->unset_column('pid');
        echo $this->datatables->generate();
    }

    function invoices()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('invoices');
        $bc = array(array('link' => '#', 'page' => lang('invoices')));
        $meta = array('page_title' => lang('invoices'), 'bc' => $bc);
        $this->page_construct('summaries/invoices', $this->data, $meta);
    }

    function get_invoices()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('send_invoice') . ".id as id, issue_date, 'Registro de Factura', file_name, if(status = 1, 'Error de envío', if(status = 2, 'Envío correcto', if(status = 3, 'Rechazado', if(status = 4, 'Declarado correctamente', '')))) as estado, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name) as created_by, observations", FALSE)
            ->join('users', 'users.id=send_invoice.uCrea', 'left')
            ->where('type LIKE "%RF%"')
            ->from("send_invoice");

        $this->datatables->unset_column('id');

        echo $this->datatables->generate();
    }

    function add()
    {
        $today = date("Y-m-d");
        $this->form_validation->set_rules('reference_date', lang("reference_date"), 'trim|date_less_than['. $today .']|required'); //date_unique[send_invoice.processed_date]

        $send = false;
        $send_summary = null;
        if ($this->form_validation->run() == true) {

            $reference_date = $this->input->post('reference_date');
            if($this->summaries_model->date_unique($reference_date) == true){
                $send_summary = $this->summaries_model->addSummary($reference_date);
                $send = true;
            }else{
                $this->session->set_flashdata('error', lang("validation_date_unique"));
                redirect("summaries/add");
            }

        } elseif ($this->input->post('add_summary')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("summaries/add");
        }

        if ($send == true && $send_summary) {
            $this->session->set_flashdata('message', lang("summary_added"));
            redirect("summaries");
        } else {
            if(!$send_summary && $send){
                $this->session->set_flashdata('warning', lang("summary_not_found"));
                redirect("summaries/add");
            } else{
                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['page_title'] = lang('add_summary');
                $bc = array(array('link' => site_url('summaries'), 'page' => lang('add_summary')), array('link' => '#', 'page' => lang('add_summary')));
                $meta = array('page_title' => lang('add_summary'), 'bc' => $bc);
                $this->page_construct('summaries/add', $this->data, $meta);
            }
        }
    }

	//************TRJ042 - ALEXANDER ROCA - 30/04/2019************	
	function reenvioSunatResum($id = null){
		
		
		$send = false;
        $send_summary = null;
		
		$fechaReenvio=$this->summaries_model->fechaResumen($id);
		
		if($fechaReenvio->status == 1 ){
			
			if($fechaReenvio->estado == 1){
		
				$send_summary = $this->summaries_model->addSummaryReenvio($fechaReenvio->processed_date, $fechaReenvio->type, $fechaReenvio->reference_date);
				$send = true;
				
				if ($send == true && $send_summary) {
					if($this->summaries_model->updateSendInvoiceReenvio($id)){
						$this->session->set_flashdata('message', lang("summary_forward")." - ".lang("summary_forward_file").$fechaReenvio->file_name);
						redirect("summaries");
					}else{
						$this->session->set_flashdata('error', lang("error_summary_forward")." - error :".$fechaReenvio->file_name);
						redirect("summaries");
					}
				} else {
					if(!$send_summary && $send){
						//$this->session->set_flashdata('warning', lang("summary_not_found"));
						//redirect("summaries/add");
						$this->session->set_flashdata('error', lang("error_summary_forward")." - error :".$fechaReenvio->file_name);
						redirect("summaries");
					} else{
						//$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
						//$this->data['page_title'] = lang('add_summary');
						//$bc = array(array('link' => site_url('summaries'), 'page' => lang('add_summary')), array('link' => '#', 'page' => lang('add_summary')));
						//$meta = array('page_title' => lang('add_summary'), 'bc' => $bc);
						//$this->page_construct('summaries/add', $this->data, $meta);
						$this->session->set_flashdata('error', lang("error_summary_forward")." - error :".$fechaReenvio->file_name);
						//redirect("summaries", $this->data);
						redirect("summaries");
					}
				}
		
			}else{
				$this->session->set_flashdata('warning', lang("summary_forward_error").$fechaReenvio->file_name);
			//redirect("summaries/add");
			redirect("summaries");
			}
		}else{
			$this->session->set_flashdata('warning', lang("error_summary_forward_ok").$fechaReenvio->file_name);
			//redirect("summaries/add");
			redirect("summaries");
		}
		
		
	}
	//************TRJ042 - ALEXANDER ROCA - 30/04/2019************	
    //************TRJ071 - KENY PONTE - 03/12/2019****************
    function ConsultTicket(){
		$supplierID = $this->session->userdata('ruc');
		$usuSol = $this->session->userdata('user_sol');
		$passSol = $this->session->userdata('pass_sol');
		$urlDoc = "get/envios";
		$url = $this->config->item('aws_url');
		$consult = $this->summaries_model->consultTicket();
		if($consult!=false){
			

			echo json_encode($consult);
			foreach($consult as $row){
				$file_name = $row->file_name;
				$ticket= $row->ticket;
					
				$postData = array(
							'ruc' => $supplierID,
							'usuSol' => $usuSol,
							'passSol' => $passSol,
							'ticket'=> $ticket,
							'filename' => $file_name
				);
		
				$context = stream_context_create(array(
							'http' => array(
							'method' => 'POST',
							'header' => "Authorization: application/json\r\n" .
							"Content-Type: application/json\r\n",
							'content' => json_encode($postData)
						)
					)
				);
		
				$response=file_get_contents($url.$urlDoc, false, $context);
				$res = json_decode($response, TRUE);
				if($res[0]==0){
					$respuesta="error al conectarse con sunat";
				}else{
					$codigo=2;
					$cadena_de_texto=$res[2];
					$cadena_buscada='HA SIDO ACEPTADO';
					$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
				
					if($posicion_coincidencia==false){
						$cominucacion_baja = strpos($cadena_de_texto,'COMUNICACION DE BAJA');
						if($cominucacion_baja==true){
							$codigo=2;//anulado 5/procesado 4
						}
					}else{
						$codigo=4;//procesado
					}
					if(empty($res[2]))
					{ 
						$codigo=2;//enviado
						$respuesta = "Sin CDR en AWS";
					} 
					else {
						$respuesta = $res[2];
					}
				}
			
				$this->summaries_model->insertStatusTicket($respuesta, $file_name, $ticket,$codigo);		
			}
			$this->session->set_flashdata('message','Consulta de estado de resumenes fue realizado con exito');
		}else{
			$this->session->set_flashdata('warning','Todo los resumenes se encuentran con el estado consultado');	
		}
	}
	function consultStatusTicket($id=null){
		//$this->input->get($data);
		if ($this->input->get('id')) {
			$id = $this->input->get('id');
        }	
        
        
		$response=$this->summaries_model->ConsultTicktesForID($id);
		$ticket=$response->ticket;
		$file_name=$response->file_name;
		$supplierID = $this->session->userdata('ruc');
		$usuSol = $this->session->userdata('user_sol');
		$passSol = $this->session->userdata('pass_sol');
		$urlDoc = "get/envios";
        $url = $this->config->item('aws_url');
       
		$postData = array(
			'ruc' => $supplierID,
			'usuSol' => $usuSol,
			'passSol' => $passSol,
			'ticket'=> $ticket,
			'filename' => $file_name
		);
		$context = stream_context_create(array(
			'http' => array(
			'method' => 'POST',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
		  )
		)
		);
		$codigo=2;
		$response=file_get_contents($url.$urlDoc, false, $context);
		$res = json_decode($response, TRUE);
		if($res[0]==0){
			$respuesta="Error al conectarse con sunat";
		}else{
			$cadena_de_texto=$res[2];
			$cadena_buscada='HA SIDO ACEPTADO';
			$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
					
			if($posicion_coincidencia==false){
				$cominucacion_baja = strpos($cadena_de_texto,'COMUNICACION DE BAJA');
				if($cominucacion_baja==true){
					$codigo= 4;//anulado
				}
			}else{
				$codigo=4;//procesado
			}
		
			if(empty($res[2]))
			{ 
				$codigo=2;
				$respuesta = "Sin CDR en AWS";
			} 
			else {
				$respuesta = $res[2];
			}
		}
		$this->summaries_model->insertStatusTicket($respuesta, $file_name, $ticket,$codigo);
		$this->session->set_flashdata('message','Consulta del estado del resumen '.$file_name.' fue confirmado con exito');
		redirect("summaries");
		
	}

    //************TRJ071 - KENY PONTE - 03/12/2019 ***************
}
