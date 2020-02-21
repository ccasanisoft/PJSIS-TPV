<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    function __construct() {
        parent::__construct();
        define("DEMO", 0);
        define("PROTECT_USER", 0);
        $this->Settings = $this->site->getSettings();
        $this->lang->load('app', $this->Settings->language);
        $this->Settings->pin_code = $this->Settings->pin_code ? md5($this->Settings->pin_code) : NULL;
        $this->theme = $this->Settings->theme.'/views/';
        $this->data['assets'] = base_url() . 'themes/default/assets/';
        $this->data['Local'] = "";

        //Obtener el Local
        $local_id = $this->session->userdata('local');

        if($local = $this->site->getLocalByID($local_id)){

            $this->data['Local'] = $local->code;

            //Obtener el Almacen
            if ($local->default_warehouse){
                $default_warehouse = $local->default_warehouse;
            } else {
                $default_warehouse = $this->Settings->default_warehouse;
            }
            $this->Settings->default_warehouse = $default_warehouse;

            //Obtener el Formato de Factura
            if ($local->invoice_format){
                $invoice_format = $local->invoice_format;
                $invoice_number = $local->invoice_number;
            } else {
                $invoice_format = $this->Settings->invoice_format;
                $invoice_number = $this->Settings->invoice_number;
            }
            $this->Settings->invoice_format = $invoice_format;
            $this->Settings->invoice_number = $invoice_number;

            //Obtener el Formato de Boleta
            if ($local->bill_format){
                $bill_format = $local->bill_format;
                $bill_number = $local->bill_number;
            } else {
                $bill_format = $this->Settings->bill_format;
                $bill_number = $this->Settings->bill_number;
            }
            $this->Settings->bill_format = $bill_format;
            $this->Settings->bill_number = $bill_number;

        }

        $this->data['Settings'] = $this->Settings;
        $this->loggedIn = $this->tec->logged_in();
        $this->data['loggedIn'] = $this->loggedIn;
        $this->data['categories'] = $this->site->getAllCategories();

        //$this->Admin = $this->tec->in_group('admin') ? TRUE : NULL;
        $this->Admin = $this->tec->in_group(1) ? TRUE : NULL;
        $this->data['Admin'] = $this->Admin;

        $this->Caja = $this->tec->in_group(3) ? TRUE : NULL;
        $this->data['Caja'] = $this->Caja;

        $this->m = strtolower($this->router->fetch_class());
        $this->v = strtolower($this->router->fetch_method());
        $this->data['m']= $this->m;
        $this->data['v'] = $this->v;

        //Tipo de cambio
		
		//*********************TRJ013 - Alexander roca - 11/06/2019********************
		$this->Exchange = $this->site->getExchange(date("Y-m-d"));
        $this->data['Exchange'] = $this->Exchange;
		if($this->site->getExchange(date("Y-m-d")) == false){
			$this->Exchange = $this->site->getExchangeID();
			$this->data['Exchange'] = $this->Exchange;
		}
		//*********************TRJ013 - Alexander roca - 11/06/2019********************
		
    }

    function page_construct($page, $data = array(), $meta = array()) {
        if(empty($meta)) { $meta['page_title'] = $data['page_title']; }
        $meta['message'] = isset($data['message']) ? $data['message'] : $this->session->flashdata('message');
        $meta['error'] = isset($data['error']) ? $data['error'] : $this->session->flashdata('error');
        $meta['warning'] = isset($data['warning']) ? $data['warning'] : $this->session->flashdata('warning');
        $meta['ip_address'] = $this->input->ip_address();
        $meta['Admin'] = $data['Admin'];
        $meta['loggedIn'] = $data['loggedIn'];
        $meta['Settings'] = $data['Settings'];
        $meta['Exchange'] = $data['Exchange'];
        $meta['Local'] = $data['Local'];
        $meta['assets'] = $data['assets'];
        $meta['suspended_sales'] = $this->site->getUserSuspenedSales();
        $meta['qty_alert_num'] = $this->site->getQtyAlerts();
        $this->load->view($this->theme . 'header', $meta);
        $this->load->view($this->theme . $page, $data);
        $this->load->view($this->theme . 'footer');
    }
























}
