<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Makers extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('Makers_model');
    }


    function index()
    {   
        switch($this->session->userdata('negocio')){
            case 0:
                $l_makers = lang('makers');
                break;
            case 1:
                $l_makers = lang('laboratorys');
                break;
        }

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = $l_makers;
        $bc = array(array('link' => '#', 'page' => $l_makers)); 
        $meta = array('page_title' => $l_makers, 'bc' => $bc);
        $this->page_construct('makers/index', $this->data, $meta);

    }

    function get_makers()
    { 
        $this->load->library('datatables');

        $this->datatables->select($this->db->dbprefix('makers') . ".id as id, maker")
        ->from("tec_makers");
        //->order_by("laboratory", "asc"); 
        
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('makers/edit/$1') . "' title='" . lang("edit_laboratory") . "' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('makers/delete/$1') . "' onClick=\"return confirm('" . lang('delete_laboratory') . "')\" title='" . lang("delete") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id")
       ->unset_column('id');

        echo $this->datatables->generate();
    }

    function add()
    {

        switch($this->session->userdata('negocio')){
            case 0:
                $l_maker_exists = lang("maker_exists");
                $l_maker_add = lang("add_maker");
                $l_maker_added = lang("maker_added");
                break;
            case 1:
                $l_maker_exists = lang('laboratory_exists');
                $l_maker_add = lang("add_laboratory");
                $l_maker_added = lang("laboratory_added");
                break;
        }

        $this->form_validation->set_rules('value', lang("value"), 'required');

        if ($this->form_validation->run() == true) {
            $q = $this->db->get_where('tec_makers', array('maker' =>$this->input->post('value')), 1);
            if ( $q->num_rows() > 0) {
                $this->session->set_flashdata('error', l_maker_exists);
                redirect("makers/add");
            }
            $data = array('maker' => $this->input->post('value'));

            
        } elseif ($this->input->post('add_warehouse')) { 
            $this->session->set_flashdata('error', validation_errors());
            redirect("makers/add");
        }

        if ($this->form_validation->run() == true && $this->Makers_model->addMaker($data)) { 
            $this->session->set_flashdata('message', $l_maker_added);
            redirect("makers");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['customers'] = $this->site->getAllCustomers();
            $this->data['page_title'] = $l_maker_add;
            $bc = array(array('link' => site_url('makers'), 'page' => lang('warehouse')), array('link' => '#', 'page' => $l_maker_add));
            $meta = array('page_title' => $l_maker_add, 'bc' => $bc);
            $this->page_construct('makers/add', $this->data, $meta);

        }
    }

    function edit($id = NULL)
    {   

        switch($this->session->userdata('negocio')){
            case 0:
                $l_maker = lang('maker');
                $l_maker_updated = lang('maker_updated');
                break;
            case 1:
                $l_maker = lang('laboratory');
                $l_maker_updated = lang('laboratory_updated');
                break;
        }

        if (!$this->Admin) {
            $this->session->set_flashdata('error', $this->lang->line('access_denied'));
            redirect('pos');
        }
        $this->form_validation->set_rules('value', lang("value"), 'required');

        $maker = $this->Makers_model->getMakerByID($id); 

        if ($this->form_validation->run() == true) { 
            $data = array('maker' => $this->input->post('value'));

        } elseif ($this->input->post('maker')) { 
            $this->session->set_flashdata('error', validation_errors());
            redirect("makers/edit");
        }

        if ($this->form_validation->run() == true && $this->Makers_model->updateMaker($id, $data)) { 
            $this->session->set_flashdata('message', $l_maker_updated);
            redirect("makers"); 
        } else {  //die("bb");
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['maker'] = $maker;
            //$this->data['customers'] = $this->site->getAllCustomers();
            $this->data['page_title'] = $l_maker;
            $bc = array(array('link' => site_url('makers'), 'page' => $l_maker),array('link' => '#', 'page' => $l_maker));
            $meta = array('page_title' => $l_maker, 'bc' => $bc);

            $this->page_construct('makers/edit', $this->data, $meta);
        }
    }


    function delete($id = NULL)
    {

        switch($this->session->userdata('negocio')){
            case 0:
                $l_maker_deleted = lang('maker_deleted');
                break;
            case 1:
                $l_maker_deleted = lang('laboratory_deleted');
                break;
        }

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

        if ($this->Makers_model->deleteMaker($id)) {
            $this->session->set_flashdata('success_message', $l_maker_deleted);
            redirect('makers', 'refresh');
        }
    }


}
