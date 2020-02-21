<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('customers_model');
    }

    function index()
    {

    	$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
    	$this->data['page_title'] = lang('customers');
    	$bc = array(array('link' => '#', 'page' => lang('customers')));
    	$meta = array('page_title' => lang('customers'), 'bc' => $bc);
    	$this->page_construct('customers/index', $this->data, $meta);
    }

    function get_customers()
    {

		$actions = "<a href='" . site_url('customers/edit/$1') . "'class='tip btn btn-primary btn-xs' title='".$this->lang->line("edit_customer")."'><i class='fa fa-edit'></i></a>
		<a href='" . site_url('customers/delete/$1') . "' onClick=\"return confirm('". $this->lang->line('alert_x_customer') ."')\" class='tip btn btn-danger btn-xs' title='".$this->lang->line("delete_customer")."'><i class='fa fa-trash-o'></i></a>";

		if (!$this->Admin) {
		$actions = "<a href='" . site_url('customers/delete/$1') . "'onClick=\"return confirm('". $this->lang->line('alert_x_customer') ."')\" class='tip btn btn-danger btn-xs' title='".$this->lang->line("delete_customer")."'><i class='fa fa-trash-o'></i></a>";
		}

		$this->load->library('datatables');
		

		switch($this->session->userdata('negocio')){
			case 0:
			
				$this->datatables->select($this->db->dbprefix('customers') . ".id as id,name, CASE WHEN person_type = 1 THEN  'Natural' WHEN person_type = 2 THEN 'Juridica'  WHEN person_type = 0 THEN 'por definir' END,document_type, phone, email, cf1, cf2,customers_type");

				break;
			case 1:
				$this->datatables->select($this->db->dbprefix('customers') . ".id as id, name,person_type,document_type, phone, email, cf1, cf2,customers_type, custom_field_1");
				break;
		}

		$this->datatables->from("customers")
		->join('tec_customers_type', 'customers_type.id=customers.customers_type_id', 'left')
		->join('tec_document_type', 'document_type.id=customers.document_type_id', 'left')/*********** TRJ073 KENY PONTE  */
		->where('estado=',1)
		->add_column("Actions", "<div class='text-center'><div class='btn-group'>" . $actions . "</div></div>", "id")
    	->unset_column('id');

    	echo $this->datatables->generate();
    }

	function get_customers_byid($code = NULL) {


		if($customer = $this->customers_model->getCustomerByID($code)) {

			echo json_encode(array('id' =>$customer->id,
			 'names' => $customer->name ,
			 'person_type'=>$customer->person_type,/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
			 'document_type_id'=>$customer->document_type_id,/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/

			 /*....TRJ124-AMADOR...*/
			 'customers_type_id'=>$customer->customers_type_id,
			 /*.....*/
			 'cf1' => $customer->cf1 ,
			 'cf2' => $customer->cf2 ,
			 'phone' => $customer->phone ,
			 'email' => $customer->email ,
			 'direccion' => $customer->direccion

			));
		} else {
			echo NULL;
		}

	}



	function add()
	{
	/*	$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');*/
		$this->form_validation->set_rules('email', $this->lang->line("email_address"), 'valid_email');


		if($this->input->post('person')==null){
			$this->form_validation->set_rules('persona', $this->lang->line("persona"), 'required');
		}
		if (!empty($this->input->post('person')) && $this->input->post('person') ==2) {
		/*	if($this->input->post('cf2')==null){
				$this->form_validation->set_rules('cf2', $this->lang->line("ccf2"), 'required');
			}else
			{
				$this->form_validation->set_rules('cf2', $this->lang->line("ccf2"), 'trim|numeric|exact_length[11]|required');
			}*/
		    $docNumber=" ";
          	$docType=6;

        } else if (!empty($this->input->post('person')) && $this->input->post('person') ==1) {
			/*if($this->input->post('document_type')==null){
				$this->form_validation->set_rules('document_type', $this->lang->line("document_type"), 'required');
			}else{
				if($this->input->post('cf1')==null){
					$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'required');
				}
				if($this->input->post('document_type')==0){// documento
						$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'trim|numeric|exact_length[15]|required');
        		}
        		if($this->input->post('document_type')==6){// registro unico de contribuyentes
					//	$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'trim|numeric|exact_length[11]|required');
        		}
        		if(($this->input->post('document_type')==4)||($this->input->post('document_type')==7)){ //pasaporte y carnet de estranjeria
						$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'trim|required'); //'trim|numeric|exact_length[12]|required');
				}		
        		if($this->input->post('document_type')==1){// documento nacional de identidad
						$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'trim|numeric|exact_length[8]|required');
        		}
        		if($this->input->post('cf2') != null){//registro unico de contribuyentes
						$this->form_validation->set_rules('cf2', $this->lang->line("ccf2"), 'trim|numeric|exact_length[11]|required');
				}
			}*/
        		
            $docType=$this->input->post('document_type');
            $docNumber=$this->input->post('cf1');
            if($docNumber == ''){
            	$docNumber ='--';
            }
            /*validar --*/
        }
		/*********************TRJ073 - KENY PONTE - 20/08/2019**************************************/

		if ($this->form_validation->run() == true) {
//*************************************TRJ019 - ALEXANDER ROCA - 26/03/2019 **************************
		
			$data = array(
				'name' => trim($this->input->post('name')),
				'person_type'=>trim($this->input->post('person')),/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
				'document_type_id'=>trim($docType),/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
				'email' => trim($this->input->post('email')),
				'phone' => trim($this->input->post('phone')),
				'cf1' => trim($docNumber),/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
				'cf2' => trim($this->input->post('cf2')),/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
				'direccion' => trim($this->input->post('direccion')),
				'customers_type_id' => $this->input->post('customer_type'),
				'custom_field_1' => trim($this->input->post('custom_field_1')),
				'estado' => 1,//*****************************TRJ059 - ALEXANDER ROCA - 21/06/2019************************
			);
//*************************************TRJ019 - ALEXANDER ROCA - 26/03/2019 **************************
		}

		if ( $this->form_validation->run() == true && $cid = $this->customers_model->addCustomer($data)) {
            if($this->input->is_ajax_request()) {
                echo json_encode(array('status' => 'success', 'msg' =>  $this->lang->line("customer_added"), 'id' => $cid, 'val' => $data['name']));
                die();
            }
            $this->session->set_flashdata('message', $this->lang->line("customer_added"));
            redirect("customers");

		} else {

            if($this->input->is_ajax_request()) {
                echo json_encode(array('status' => 'failed', 'msg' => validation_errors())); die();
            }

			$this->data['customer_type'] = $this->site->getAllCustomerType();
			$this->data['document_type']=$this->site->getAllDocumentType();/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
    		$this->data['page_title'] = lang('add_customer');
    		$bc = array(array('link' => site_url('customers'), 'page' => lang('customers')), array('link' => '#', 'page' => lang('add_customer')));
    		$meta = array('page_title' => lang('add_customer'), 'bc' => $bc);
    		$this->page_construct('customers/add', $this->data, $meta);

		}
	}

	function insertCustomer(){
	
	$data = $this->input->post('data');
	$s = $this->customers_model->addCustomer($data);
	echo json_encode($s);
	}


	function editCustomer(){
	
	$data = $this->input->post('data');
	$id = $this->input->post('id');
	$s = $this->customers_model->updateCustomer($id,$data);

	echo json_encode($data);
	}




	function edit($id = NULL)
	{
        // if (!$this->Admin) {
        //     $this->session->set_flashdata('error', $this->lang->line('access_denied'));
        //     redirect('pos');
        // }
		if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

		$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
		$this->form_validation->set_rules('email', $this->lang->line("email_address"), 'valid_email');

		/*if($this->input->post('cf2') == null){
			$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'required');
		} else{
			$this->form_validation->set_rules('cf2', $this->lang->line("ccf2"), 'trim|numeric|exact_length[11]|required');
		}

		if($this->input->post('cf1') != null){
			$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'trim|numeric|exact_length[8]|required');
		}*/
		/*********************TRJ073 - KENY PONTE - 20/08/2019**************************************/
		//if($this->input->post('person')==null){
		//	$this->form_validation->set_rules('persona', $this->lang->line("persona"), 'required');
		//}
		if (!empty($this->input->post('person')) && $this->input->post('person') ==2) {
			if($this->input->post('cf2')==null){
				$this->form_validation->set_rules('cf2', $this->lang->line("ccf2"), 'required');
			}else
			{
				$this->form_validation->set_rules('cf2', $this->lang->line("ccf2"), 'trim|numeric|exact_length[11]|required');
			}
		    $docNumber=" ";
          	$docType=6;

        } else if (!empty($this->input->post('person')) && $this->input->post('person') ==1) {
        	if($this->input->post('document_type')==null){
				$this->form_validation->set_rules('document_type', $this->lang->line("document_type"), 'required');
			//}
				//if($this->input->post('cf1')==null){
				//$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'required');
			}else
			{
				if($this->input->post('cf1')==null){
					$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'required');
				}
        		if($this->input->post('document_type')==0){//docuemento tributario domiciliado sin ruc
						$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'trim|numeric|exact_length[15]|required');
        		}
        		if(($this->input->post('document_type')==4)||($this->input->post('document_type')==7)){//carnet de estranjeria y pasaporte
						$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'trim|numeric|exact_length[12]|required');
				}		
        		if($this->input->post('document_type')==1){//documento nacional de identidad
						$this->form_validation->set_rules('cf1', $this->lang->line("ccf1"), 'trim|numeric|exact_length[8]|required');
        		}
        		if($this->input->post('cf2') != null){// registro unico de contribuyentes
						$this->form_validation->set_rules('cf2', $this->lang->line("ccf2"), 'trim|numeric|exact_length[11]|required');
				}
        	}	
            $docType=$this->input->post('document_type');
            $docNumber=$this->input->post('cf1');//numero de documento
        }
		/*********************TRJ073 - KENY PONTE - 20/08/2019**************************************/

		if ($this->form_validation->run() == true) {
//*************************************TRJ019 - ALEXANDER ROCA - 26/03/2019 **************************
	
			$data = array(
				'name' => trim($this->input->post('name')),
				'person_type'=>trim($this->input->post('person')),/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
				'document_type_id'=>trim($docType),/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
				'email' => trim($this->input->post('email')),
				'phone' => trim($this->input->post('phone')),
				'cf1' => trim($docNumber),/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
				'cf2' => trim($this->input->post('cf2')),/*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
				'direccion' => trim($this->input->post('direccion')),
				'customers_type_id' => $this->input->post('customer_type'),
				'custom_field_1' => trim($this->input->post('custom_field_1')),
				'estado' => 1,
			);

				//*****************************TRJ059 - ALEXANDER ROCA - 21/06/2019************************
			


			/*$data = array('name' => trim($this->input->post('name')),
				'email' => trim($this->input->post('email')),
				'phone' => trim($this->input->post('phone')),
				'cf1' => trim($this->input->post('cf1')),
				'cf2' => trim($this->input->post('cf2')),
				'direccion' => trim($this->input->post('direccion')),
				'customers_type_id' => $this->input->post('customer_type'),
				'custom_field_1' => trim($this->input->post('custom_field_1'))
			);*/
//*************************************TRJ019 - ALEXANDER ROCA - 26/03/2019 **************************
		}

		if ( $this->form_validation->run() == true && $this->customers_model->updateCustomer($id, $data)) {
            if($this->input->is_ajax_request()) {
                echo json_encode(array('status' => 'success', 'msg' =>  $this->lang->line("customer_updated"), 'id' => $cid, 'val' => $data['name']));
                die();
            }
			$this->session->set_flashdata('message', $this->lang->line("customer_updated"));
			redirect("customers");

		} else {
            if($this->input->is_ajax_request()) {
                echo json_encode(array('status' => 'error', 'msg' =>  validation_errors(), 'id' => $cid, 'val' => $data['name']));
                die();
            }
            $this->data['document_type'] = $this->site->getAllDocumentType();
            $this->data['customer_type'] = $this->site->getAllCustomerType();/***TRJ073 - KENY PONTE - 20/08/2019***/
			$this->data['customer'] = $this->customers_model->getCustomerByID($id);
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
    		$this->data['page_title'] = lang('edit_customer');
    		$bc = array(array('link' => site_url('customers'), 'page' => lang('customers')), array('link' => '#', 'page' => lang('edit_customer')));
    		$meta = array('page_title' => lang('edit_customer'), 'bc' => $bc);
    		$this->page_construct('customers/edit', $this->data, $meta);

		}
	}

	function delete($id = NULL)
	{
		if(DEMO) {
			$this->session->set_flashdata('error', $this->lang->line("disabled_in_demo"));
			redirect('pos');
		}

		if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

		if (!$this->Admin)
		{
			$this->session->set_flashdata('error', lang("access_denied"));
			redirect('pos');
		}

		if ( $this->customers_model->deleteCustomer($id) )
		{
			$this->session->set_flashdata('message', lang("customer_deleted"));
			redirect("customers");
		}

	}


}
