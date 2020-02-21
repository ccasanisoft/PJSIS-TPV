<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Locals extends MY_Controller
{

    function __construct() {
        parent::__construct();


        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('locals_model');
    }


    function index()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('locals');
        $bc = array(array('link' => '#', 'page' => lang('locals')));
        $meta = array('page_title' => lang('locals'), 'bc' => $bc);
        $this->page_construct('locals/index', $this->data, $meta);
    }

    function get_locals()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('locals') . ".id as id, code, name, 	address, " . $this->db->dbprefix('warehouses') . ".warehouse, invoice_format, bill_format", FALSE)
            ->join('warehouses', 'warehouses.id=locals.default_warehouse', 'left')
            ->where('estado = 1')
            ->from("locals");

        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('locals/edit/$1') . "' title='" . lang("edit_local") . "' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('locals/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_local') . "')\" title='" . lang("delete_local") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id, code, name");

        $this->datatables->unset_column('id');

        echo $this->datatables->generate();
    }

    function add() {

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->form_validation->set_rules('code', lang('local_code'), 'trim|is_unique[locals.code]|required');
        $this->form_validation->set_rules('name', lang('local_name'), 'trim|is_unique[locals.name]|required');

        if ($this->form_validation->run() == true) {

            $data = array(
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'cod_sunat' => $this->input->post('cod_sunat'),
                'uCrea' => $this->session->userdata('user_id'),
                'fCrea' => date("Y-m-d H:i:s"),
                'estado' => 1
            );

            if ($this->input->post('address')) {
                $data['address'] = $this->input->post('address');
            }

            if ($this->input->post('default_warehouse') && $this->input->post('default_warehouse') != "") {
                $data['default_warehouse'] = $this->input->post('default_warehouse');
            }

            if ($this->input->post('invoice_format')) {
                $invoice_format = $this->input->post('invoice_format');
                $parte1=explode('{',$invoice_format);
                $parte2=explode('}',$parte1[1]);

                $parentesis= $parte2[0];

                $buscar = strpos($parentesis,'+');
                if ($buscar){
                    $numero = explode('+',$parentesis);
                    $data['invoice_number'] = (int)$numero[1];
                    $format = $parte1[0].'{'.$numero[0].'}';
                    $data['invoice_format'] = $format;
                } else {
                    $data['invoice_format'] = $invoice_format;
                    $data['invoice_number'] = 0;
                }
            }

            if ($this->input->post('bill_format')) {
                $bill_format = $this->input->post('bill_format');
                $parte1=explode('{',$bill_format);
                $parte2=explode('}',$parte1[1]);

                $parentesis= $parte2[0];

                $buscar = strpos($parentesis,'+');
                if($buscar){
                    $numero = explode('+',$parentesis);
                    $data['bill_number'] = (int)$numero[1];
                    $format = $parte1[0].'{'.$numero[0].'}';
                    $data['bill_format'] = $format;
                } else {
                    $data['bill_format'] = $bill_format;
                    $data['bill_number'] = 0;
                }
            }

        }

        if ($this->form_validation->run() == true && $this->locals_model->addLocal($data)) {

            $this->session->set_flashdata('message', lang('local_added'));
            redirect("locals");

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['settings'] = $this->site->getSettings();
            $this->data['page_title'] = lang('add_local');
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => site_url('locals'), 'page' => lang('locals')), array('link' => '#', 'page' => lang('add_local')));
            $meta = array('page_title' => lang('add_local'), 'bc' => $bc);
            $this->page_construct('locals/add', $this->data, $meta);
        }
    }

    function edit($id = NULL) {

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('code', lang('local_code'), 'required');
        $this->form_validation->set_rules('name', lang('local_name'), 'required');

        if ($this->form_validation->run() == true) {

            $data = array(
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'address' => $this->input->post('address'),
                'cod_sunat' => $this->input->post('cod_sunat'),
                'default_warehouse' => $this->input->post('default_warehouse'),
                'uActualiza' => $this->session->userdata('user_id'),
                'fActualiza' => date("Y-m-d H:i:s")
            );

            if ($this->input->post('invoice_format')) {

                $invoice_format = $this->input->post('invoice_format');

                $parte1=explode('{',$invoice_format);
                $parte2=explode('}',$parte1[1]);

                $parentesis= $parte2[0];

                $buscar = strpos($parentesis,'+');
                if($buscar){
                    $numero = explode('+',$parentesis);
                    $data['invoice_number'] = (int)$numero[1];
                    $format = $parte1[0].'{'.$numero[0].'}';
                    $data['invoice_format'] = $format;
                } else {
                    $data['invoice_format'] = $invoice_format;
                }
            } else if($invoice_format == ""){
                $data['invoice_format'] = NULL;
                $data['invoice_number'] = 0;
            }

            if ($this->input->post('bill_format')) {

                $bill_format = $this->input->post('bill_format');

                $parte1=explode('{',$bill_format);
                $parte2=explode('}',$parte1[1]);

                $parentesis= $parte2[0];

                $buscar = strpos($parentesis,'+');
                if($buscar){
                    $numero = explode('+',$parentesis);
                    $data['bill_number'] = (int)$numero[1];
                    $format = $parte1[0].'{'.$numero[0].'}';
                    $data['bill_format'] = $format;
                } else {
                    $data['bill_format'] = $bill_format;
                }
            } else if($bill_format == ""){
                $data['bill_format'] = NULL;
                $data['bill_number'] = 0;
            }

        }

        if ($this->form_validation->run() == true && $this->locals_model->updateLocal($id, $data)) {

            $this->session->set_flashdata('message', lang('local_updated'));
            redirect("locals/edit/".$id);

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['local'] = $this->site->getLocalByID($id);
            $this->data['settings'] = $this->site->getSettings();
            $this->data['page_title'] = lang('new_local');
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => site_url('locals'), 'page' => lang('locals')), array('link' => '#', 'page' => lang('edit_local')));
            $meta = array('page_title' => lang('edit_local'), 'bc' => $bc);
            $this->page_construct('locals/edit', $this->data, $meta);

        }
    }

    function delete($id = NULL) {

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

        if ($this->locals_model->deleteLocal($id)) {
            $this->session->set_flashdata('message', lang("local_deleted"));
            redirect('locals');
        }
    }


}
