<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers_type extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('Customers_type_model');
    }


    function index()
    {  
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('customers_type');
        $bc = array(array('link' => '#', 'page' => lang('customers_type'))); 
        $meta = array('page_title' => lang('customers_type'), 'bc' => $bc);
        $this->page_construct('customers_type/index', $this->data, $meta);

    }

    function get_customers_type()
    { 
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('Customers_type') . ".id as id, customers_type")
            ->from("tec_customers_type");
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('customers_type/edit/$1') . "' title='" . lang("edit_customer_type") . "' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('customers_type/delete/$1') . "' onClick=\"return confirm('" . lang('delete_customer_type') . "')\" title='" . lang("delete") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id")
       ->unset_column('id');

        echo $this->datatables->generate();
    }

    function add()
    {
        $this->form_validation->set_rules('value', lang("value"), 'required');

        if ($this->form_validation->run() == true) {
            $data = array('customers_type' => $this->input->post('value'));
            
        } elseif ($this->input->post('add_customers_type')) { die("bbb");
            $this->session->set_flashdata('error', validation_errors());
            redirect("customers_type/add");
        }

        if ($this->form_validation->run() == true && $this->Customers_type_model->addCustomersType($data)) { 
            $this->session->set_flashdata('message', lang("customer_type_added"));
            redirect("customers_type");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['customers'] = $this->site->getAllCustomers();
            $this->data['page_title'] = lang('new_customer_type');
            $bc = array(array('link' => site_url('customers_type'), 'page' => lang('customers_type')), array('link' => '#', 'page' => lang('new_customer_type')));
            $meta = array('page_title' => lang('new_customer_type'), 'bc' => $bc);
            $this->page_construct('customers_type/add', $this->data, $meta);

        }
    }

    function edit($id = NULL)
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', $this->lang->line('access_denied'));
            redirect('pos');
        }
        $this->form_validation->set_rules('value', lang("value"), 'required');
        $customers_type = $this->Customers_type_model->getCustomersTypeByID($id); 

        if ($this->form_validation->run() == true) { 
            $data = array('customers_type' => $this->input->post('value')
            );
        } elseif ($this->input->post('customer_type')) { 
            $this->session->set_flashdata('error', validation_errors());
            redirect("customers_type/edit");
        }

        if ($this->form_validation->run() == true && $this->Customers_type_model->updateCustomersType($id, $data)) {
            $this->session->set_flashdata('message', lang("customer_type_updated"));
            redirect("customers_type");
        } else { 
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['customers_type'] = $customers_type;
            $this->data['customers'] = $this->site->getAllCustomers();
            $this->data['page_title'] = lang('customer_type');
            $bc = array(array('link' => site_url('customers_type'), 'page' => lang('customers_type')),array('link' => '#', 'page' => lang('customer_type')));
            $meta = array('page_title' => lang('customer_type'), 'bc' => $bc);
            $this->page_construct('customers_type/edit', $this->data, $meta);
        }
    }


    function delete($id = NULL)
    {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->Customers_type_model->deleteCustomersType($id)) {
            $this->session->set_flashdata('success_message', lang("customer_type_deleted"));
            redirect('customers_type', 'refresh');
        }
    }


}
