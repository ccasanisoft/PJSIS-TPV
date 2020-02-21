<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchases extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('purchases_model');
        $this->load->model('suppliers_model');
        $this->load->model('mov_model');
        $this->allowed_types = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|zip';
    }

    function index() {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('purchases');
        $bc = array(array('link' => '#', 'page' => lang('purchases')));
        $meta = array('page_title' => lang('purchases'), 'bc' => $bc);
        $this->page_construct('purchases/index', $this->data, $meta);

    }

    function get_purchases() {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->library('datatables');
        $this->datatables->select($this->db->dbprefix('purchases') . ".id as id, date, reference, expiration_date, description, name, currency, total, tax, grand_total, paid, status"); //affected, exonerated, note, warehouse");
        $this->datatables->from('purchases')
        ->join('tec_suppliers', 'tec_suppliers.id=purchases.supplier_id', 'left')
        // ->join('tec_warehouses', 'tec_warehouses.id=purchases.warehouse_id', 'left')
        ->where('purchases.estado =', 1);
		/*
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'>
        <a onclick=\"window.open('".site_url('purchases/view/$1')."', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;\" href='#' title='".lang('view_purchase')."' class='tip btn btn-primary btn-xs'><i class='fa fa-file-text-o'></i></a>
        <a href='" . site_url('purchases/edit/$1') . "' title='" . lang("edit_purchase") . "' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a>
        <a href='".site_url('purchases/payments/$1')."' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a>
        <a href='".site_url('purchases/add_payment/$1')."' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a>
        <a href='" . site_url('purchases/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_purchase') . "')\" title='" . lang("delete_purchase") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id");
		*/
		$this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'>
        <a onclick=\"window.open('".site_url('purchases/view/$1')."', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;\" href='#' title='".lang('view_purchase')."' class='tip btn btn-primary btn-xs'><i class='fa fa-file-text-o'></i></a>
        <a href='".site_url('purchases/payments/$1')."' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a>
        <a href='".site_url('purchases/add_payment/$1')."' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a>
        <a href='" . site_url('purchases/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_purchase') . "')\" title='" . lang("delete_purchase") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();

    }

    function view($id = NULL) {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->data['purchase'] = $this->purchases_model->getPurchaseByID($id);
        $this->data['supplier'] = $this->suppliers_model->getSupplierByID($this->data['purchase']->supplier_id);
        $this->data['items'] = $this->purchases_model->getAllPurchaseItems($id);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('view_purchase');
        $this->load->view($this->theme.'purchases/view', $this->data);

    }

    function add() {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->form_validation->set_rules('date', lang('date'), 'required');

        if ($this->form_validation->run() == true) {
            $total = 0;
            $affected = 0;
            $tax = 0;
            $exonerated = 0;
            $quantity = "quantity";
            $product_id = "product_id";
            $unit_cost = "cost";
            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            $settings_tax = $this->Settings->default_tax_rate;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_qty = $_POST['quantity'][$r];
                $item_cost = $_POST['cost'][$r];
                if( $item_id && $item_qty && $unit_cost ) {

                    if(!($product = $this->site->getProductByID($item_id))) {
                        $this->session->set_flashdata('error', $this->lang->line("product_not_found")." ( ".$item_id." ).");
                        redirect('purchases/add');
                    }

                    $tax_method = $product->tax_method;
                    $item_afec = 0; $item_tax = 0; $item_exon = 0;
                    if (isset($settings_tax) && $settings_tax != 0) {
                        if($tax_method == 0){
                            $item_afec = $item_cost / (1 + ($settings_tax  / 100));
                            $item_tax = $item_cost - $item_afec;
                            $item_exon = 0;
                        }else if($tax_method == 1){
                            $item_afec = $item_cost / (1 + ($settings_tax  / 100));
                            $item_tax = $item_cost - $item_afec;
                            $item_exon = 0;
                        }else if($tax_method == 2){
                            $item_exon = $item_cost;
                            $item_afec = 0;
                            $item_tax = 0;
                        }
                    }

                    $products[] = array(
                        'product_id' => $item_id,
                        'cost' => $item_cost,
                        'quantity' => $item_qty,
                        'subtotal' => ($item_cost*$item_qty),
                        'uCrea' => $this->session->userdata('user_id'),
                        'fCrea' => date("Y-m-d H:i:s"),
                        'estado' => 1
                        );

                    $total += ($item_cost * $item_qty);
                    $affected += ($item_afec * $item_qty);
                    $tax += ($item_tax * $item_qty);
                    $exonerated += ($item_exon * $item_qty);
                }

            }

            if (!isset($products) || empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
                // $this->session->set_flashdata('warning', lang("products_not_found"));
                // redirect("purchases/add");
            } else {
                krsort($products);
            }

            $sub_total = $affected + $exonerated;

            $data = array(
                        'date' => $this->input->post('date'),
                        'local_id' => $this->session->userdata('local'),
                        'expiration_date' => $this->input->post('expiration_date') ? $this->input->post('expiration_date') : NULL,
                        'reference' => $this->input->post('reference'),
                        'description' => $this->input->post('description'),
                        'note' => $this->input->post('note', TRUE),
                        'currency' => $this->input->post('currency_code'),
                        'exchange' => $this->Exchange->sell,
                        'paid' => 0.00,
                        'affected' => $this->tec->formatDecimal($affected),
                        'exonerated' => $this->tec->formatDecimal($exonerated),
                        'tax' => $this->tec->formatDecimal($tax),
                        'total' => $this->tec->formatDecimal($sub_total),
                        'grand_total' => $this->tec->formatDecimal($total),
                        'status' => "DEBE",
                        'warehouse_id' => $this->input->post('warehouse'),
                        'supplier_id' => $this->input->post('supplier'),
                        'uCrea' => $this->session->userdata('user_id'),
                        'fCrea' => date("Y-m-d H:i:s"),
                        'estado' => 1
                    );

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');
                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = $this->allowed_types;
                $config['max_size'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->upload->set_flashdata('error', $error);
                    redirect("purchases/add");
                }

                $data['attachment'] = $this->upload->file_name;

            }
            // $this->tec->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $purchase_id = $this->purchases_model->addPurchase($data, $products)) {

            $this->session->set_userdata('remove_spo', 1);
            $this->session->set_flashdata('message', lang('purchase_added'));

            //AGREGA EN TABLA MOVIMIENTOS
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_qty = $_POST['quantity'][$r];

                $dataM = array(
                            'origin_id' => $purchase_id,
                            'local_id' => $this->session->userdata('local'),
                            'warehouse_id' => $this->input->post('warehouse'),
                            'product_id' => $item_id,
                            'cant' => $item_qty,
                            'tipo' => 'I',
                            'idMotivo' => 1, //Ingreso por compra de productos
                            'ref' => $this->input->post('reference'),
                            'uCrea' => $this->session->userdata('user_id'),
                            'fCrea' => date("Y-m-d H:i:s"),
                            'estado' => 1
                        );
                $this->mov_model->addMovement($dataM);

            }

            redirect("purchases");

        } else {
            // $this->data['dw'] = $this->Settings->default_warehouse;
            // $this->data['dm'] = $this->Settings->currency_prefix;
            // $this->data['exchange'] = $this->site->getExchange(date("Y-m-d"));
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers'] = $this->site->getAllSuppliers();
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['page_title'] = lang('add_purchase');
            $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('add_purchase')));
            $meta = array('page_title' => lang('add_purchase'), 'bc' => $bc);
            $this->page_construct('purchases/add', $this->data, $meta);

        }
    }

    function edit($id = NULL) {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('date', lang('date'), 'required');

        if ($this->form_validation->run() == true) {
            $total = 0;
            $affected = 0;
            $tax = 0;
            $exonerated = 0;
            $quantity = "quantity";
            $product_id = "product_id";
            $unit_cost = "cost";
            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            $settings_tax = $this->Settings->default_tax_rate;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_qty = $_POST['quantity'][$r];
                $item_cost = $_POST['cost'][$r];
                if( $item_id && $item_qty && $unit_cost ) {

                    if(!($product = $this->site->getProductByID($item_id))) {
                        $this->session->set_flashdata('error', $this->lang->line("product_not_found")." ( ".$item_id." ).");
                        redirect('purchases/edit/'.$id);
                    }

                    $tax_method = $product->tax_method;
                    $item_afec = 0; $item_tax = 0; $item_exon = 0;
                    if (isset($settings_tax) && $settings_tax != 0) {
                        if($tax_method == 0){
                            $item_afec = $item_cost / (1 + ($settings_tax  / 100));
                            $item_tax = $item_cost - $item_afec;
                            $item_exon = 0;
                        }else if($tax_method == 1){
                            $item_afec = $item_cost / (1 + ($settings_tax  / 100));
                            $item_tax = $item_cost - $item_afec;
                            $item_exon = 0;
                        }else if($tax_method == 2){
                            $item_exon = $item_cost;
                            $item_afec = 0;
                            $item_tax = 0;
                        }
                    }

                    $products[] = array(
                        'product_id' => $item_id,
                        'cost' => $item_cost,
                        'quantity' => $item_qty,
                        'subtotal' => ($item_cost*$item_qty),
                        'uCrea' => $this->session->userdata('user_id'),
                        'fCrea' => date("Y-m-d H:i:s"),
                        'estado' => 1
                        );

                    $total += ($item_cost * $item_qty);
                    $affected += ($item_afec * $item_qty);
                    $tax += ($item_tax * $item_qty);
                    $exonerated += ($item_exon * $item_qty);

                }
            }

            if (!isset($products) || empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
                // $this->session->set_flashdata('warning', lang("products_not_found"));
                // redirect("purchases/edit");
            } else {
                krsort($products);
            }

            $sub_total = $affected + $exonerated;

            $data = array(
                        'date' => $this->input->post('date'),
                        'expiration_date' => $this->input->post('expiration_date') ? $this->input->post('expiration_date') : NULL,
                        'reference' => $this->input->post('reference'),
                        'description' => $this->input->post('description'),
                        'note' => $this->input->post('note', TRUE),
                        'currency' => $this->Settings->currency_prefix,
                        'exchange' => $this->Exchange->sell,
                        'affected' => $this->tec->formatDecimal($affected),
                        'exonerated' => $this->tec->formatDecimal($exonerated),
                        'tax' => $this->tec->formatDecimal($tax),
                        'total' => $this->tec->formatDecimal($sub_total),
                        'grand_total' => $this->tec->formatDecimal($total),
                        'warehouse_id' => $this->input->post('warehouse'),
                        'supplier_id' => $this->input->post('supplier'),
                        'uActualiza' => $this->session->userdata('user_id'),
                        'fActualiza' => date("Y-m-d H:i:s")
                    );

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');
                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = $this->allowed_types;
                $config['max_size'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->upload->set_flashdata('error', $error);
                    redirect("purchases/add");
                }

                $data['attachment'] = $this->upload->file_name;

            }
            // $this->tec->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->updatePurchase($id, $data, $products)) {

            $this->session->set_userdata('remove_spo', 1);
            $this->session->set_flashdata('message', lang('purchase_updated'));

            //AGREGA EN TABLA MOVIMIENTOS
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_qty = $_POST['quantity'][$r];

                $dataM = array(
                            'origin_id' => $id,
                            'local_id' => $this->session->userdata('local'),
                            'warehouse_id' => $this->input->post('warehouse'),
                            'product_id' => $item_id,
                            'cant' => $item_qty,
                            'tipo' => 'I',
                            'idMotivo' => 1, //Ingreso por compra de productos
                            'ref' => $this->input->post('reference'),
                            'uCrea' => $this->session->userdata('user_id'),
                            'fCrea' => date("Y-m-d H:i:s"),
                            'uActualiza' => $this->session->userdata('user_id'),
                            'fActualiza' => date("Y-m-d H:i:s"),
                            'estado' => 1
                        );

                $this->mov_model->deleteMovement($id, $idMotivo);
                $this->mov_model->addMovement($dataM);

            }

            redirect("purchases");


        } else {

            $this->data['purchase'] = $this->purchases_model->getPurchaseByID($id);
            $inv_items = $this->purchases_model->getAllPurchaseItems($id);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->site->getProductByID($item->product_id);
                $row->qty = $item->quantity;
                $row->cost = $item->cost;
                $ri = $this->Settings->item_addition ? $row->id : $c;
                $pr[$ri] = array('id' => $ri, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
                $c++;
            }

            $this->data['items'] = json_encode($pr);
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            // $this->data['exchange'] = $this->site->getExchange(date("Y-m-d"));
            $this->data['suppliers'] = $this->site->getAllSuppliers();
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['page_title'] = lang('edit_purchase');
            $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('edit_purchase')));
            $meta = array('page_title' => lang('edit_purchase'), 'bc' => $bc);
            $this->page_construct('purchases/edit', $this->data, $meta);

        }
    }

    //////////////////////////////////////////////////////////////////////

    function payments($id = NULL)
    {
        $this->data['payments'] = $this->purchases_model->getPurchasePayments($id);
        $this->load->view($this->theme . 'purchases/payments', $this->data);
    }

    function add_payment($id = NULL, $cid = NULL)
    {
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if ($this->Admin) {
                $date = $this->input->post('date');
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $payment = array(
                'date' => $date,
                'purchase_id' => $id,
                'supplier_id' => $cid,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount-paid'),
                'currency' => $this->Settings->currency_prefix,
                'paid_by' => $this->input->post('paid_by'),
                'note' => $this->input->post('note'),
                'created_by' => $this->session->userdata('user_id'),
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = 2048;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    echo json_encode(array('status' => 'success', 'msg' =>  "Ok",'redirect' =>  1));
                    exit();
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            $this->tec->dd();
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPayment($payment)) {

            $msg = lang("payment_added");
            $this->session->set_flashdata('message', $msg);

            echo json_encode(array('status' => 'success', 'msg' =>  "Ok",'redirect' =>  1));
            exit();

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $purchase = $this->purchases_model->getPurchaseByID($id);
            $this->data['inv'] = $purchase;

            $this->load->view($this->theme . 'purchases/add_payment', $this->data);
        }
    }


    function edit_payment($id = NULL, $sid = NULL)
    {

    	if (!$this->Admin) {
			$this->session->set_flashdata('error', lang("access_denied"));
			redirect($_SERVER["HTTP_REFERER"]);
		}
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            $payment = array(
                'purchase_id' => $sid,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'note' => $this->input->post('note'),
                'updated_by' => $this->session->userdata('user_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            );

            if ($this->Admin) {
                $payment['date'] = $this->input->post('date');
            }

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = 2048;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);

        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            $this->tec->dd();
        }


        if ($this->form_validation->run() == true && $this->purchases_model->updatePayment($id, $payment)) {
            $this->session->set_flashdata('message', lang("payment_updated"));
            redirect("purchases");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $payment = $this->purchases_model->getPaymentByID($id);
            if($payment->paid_by != 'cash') {
            	$this->session->set_flashdata('error', lang('only_cash_can_be_edited'));
            	$this->tec->dd();
            }
            $this->data['payment'] = $payment;
            $this->load->view($this->theme . 'purchases/edit_payment', $this->data);
        }
    }

    function delete_payment($id = NULL)
    {

		if($this->input->get('id')){ $id = $this->input->get('id'); }

		if (!$this->Admin) {
			$this->session->set_flashdata('error', lang("access_denied"));
			redirect($_SERVER["HTTP_REFERER"]);
		}

		if ( $this->purchases_model->deletePayment($id) ) {
			$this->session->set_flashdata('message', lang("payment_deleted"));
			redirect('purchases');
		}
    }

    //////////////////////////////////////////////////////////////////////7


    function delete($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->purchases_model->deletePurchase($id)) {
            $this->session->set_flashdata('message', lang("purchase_deleted"));

            // $this->mov_model->deleteMovement($id, $idMotivo);

            $odata = $this->purchases_model->getPurchaseByID($id);
            $oitems = $this->purchases_model->getAllPurchaseItems($id);

            $warehouse_id = $odata->warehouse_id;

            foreach ($oitems as $oitem) {

                if ($oitem->product_id != null) {

                    $dataM = array(
                        'origin_id' => $id,
                        'local_id' => $this->session->userdata('local'),
                        'warehouse_id' => $warehouse_id, //$this->Settings->default_warehouse,
                        'product_id' => $oitem->product_id,
                        'cant' => $oitem->quantity,
                        'tipo' => 'S',
                        'idMotivo' => 8, //Salida por anulación de compra
                        'ref' => $odata->reference,
                        'uCrea' => $this->session->userdata('user_id'),
                        'fCrea' => date("Y-m-d H:i:s"),
                        'estado' => 1
                    );

                    $this->mov_model->addMovement($dataM);
                }
            }

            $dataP = array(
                'uActualiza' => $this->session->userdata('user_id'),
                'fActualiza' => date("Y-m-d H:i:s")
            );

            $this->purchases_model->updatePurchaseCab($id, $dataP);

            redirect('purchases');
        }
    }

    function suggestions($id = NULL)
    {
        if($id) {
            $row = $this->site->getProductByID($id);
            $row->qty = 1;
            $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
            echo json_encode($pr);
            die();
        }
        $term = $this->input->get('term', TRUE);

		//***********TRJ0003 - ALEXADER ROCA - 13/03/2019************

		$palabraExtraida = explode(" ", $term);

		$validacion="(";



		for($i=0; $i<count($palabraExtraida) ; $i++){

    

			if($i==count($palabraExtraida)-1){

				$validacion=$validacion." name LIKE '%" . $palabraExtraida[$i] . "%'";

			}else{

				$validacion=$validacion." name LIKE '%" . $palabraExtraida[$i] . "%' and";

			}

		}

		

		$validacion=$validacion.")";

		

        $rows = $this->purchases_model->getProductNames($term,$validacion);

		//***********TRJ0003 - ALEXADER ROCA - 13/03/2019************

        if ($rows) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

     /* ----------------------------------------------------------------- */

     function expenses($id = NULL)
    {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('expenses');
        $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('expenses')));
        $meta = array('page_title' => lang('expenses'), 'bc' => $bc);
        $this->page_construct('purchases/expenses', $this->data, $meta);

    }

    function get_expenses($user_id = NULL)
    {

        $detail_link = anchor('purchases/expense_note/$1', '<i class="fa fa-file-text-o"></i> ' . lang('expense_note'), 'data-toggle="modal" data-target="#myModal2"');
        $edit_link = anchor('purchases/edit_expense/$1', '<i class="fa fa-edit"></i> ' . lang('edit_expense'), 'data-toggle="modal" data-target="#myModal"');
        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_expense") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('purchases/delete_expense/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_expense') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('expenses') . ".id as id, date, reference, amount, note, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name) as user, attachment", FALSE)
            ->from('expenses')
            ->join('users', 'users.id=expenses.created_by', 'left')
            ->group_by('expenses.id');

        if ( ! $this->Admin) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a onclick=\"window.open('".site_url('purchases/expense_note/$1')."', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;\" href='#' title='".lang('expense_note')."' class='tip btn btn-primary btn-xs'><i class='fa fa-file-text-o'></i></a> <a href='" . site_url('purchases/edit_expense/$1') . "' title='" . lang("edit_expense") . "' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('purchases/delete_expense/$1') . "' onClick=\"return confirm('" . lang('alert_x_expense') . "')\" title='" . lang("delete_expense") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id");
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }

    function expense_note($id = NULL)
    {
        if ( ! $this->Admin) {
            if($expense->created_by != $this->session->userdata('user_id')) {
                $this->session->set_flashdata('error', lang('access_denied'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'pos');
            }
        }

        $expense = $this->purchases_model->getExpenseByID($id);
        $this->data['user'] = $this->site->getUser($expense->created_by);
        $this->data['expense'] = $expense;
        $this->data['page_title'] = $this->lang->line("expense_note");
        $this->load->view($this->theme . 'purchases/expense_note', $this->data);

    }

    function add_expense()
    {
        $this->load->helper('security');

        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $date = trim($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference') ? $this->input->post('reference') : $this->site->getReference('ex'),
                'amount' => $this->input->post('amount'),
                'currency' => $this->Settings->currency_prefix,
                'exchange' => $this->Exchange->sell,
                'created_by' => $this->session->userdata('user_id'),
                'note' => $this->input->post('note', TRUE)
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = $this->allowed_types;
                $config['max_size'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->tec->print_arrays($data);

        } elseif ($this->input->post('add_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addExpense($data)) {

            $this->session->set_flashdata('message', lang("expense_added"));
            redirect('purchases/expenses');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = lang('add_expense');
            $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => site_url('purchases/expenses'), 'page' => lang('expenses')), array('link' => '#', 'page' => lang('add_expense')));
            $meta = array('page_title' => lang('add_expense'), 'bc' => $bc);
            $this->page_construct('purchases/add_expense', $this->data, $meta);

        }
    }

    function edit_expense($id = NULL)
    {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference', lang("reference"), 'required');
        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $date = trim($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount'),
                'note' => $this->input->post('note', TRUE)
            );
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = $this->allowed_types;
                $config['max_size'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->tec->print_arrays($data);

        } elseif ($this->input->post('edit_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }


        if ($this->form_validation->run() == true && $this->purchases_model->updateExpense($id, $data)) {
            $this->session->set_flashdata('message', lang("expense_updated"));
            redirect("purchases/expenses");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['expense'] = $this->purchases_model->getExpenseByID($id);
            $this->data['page_title'] = lang('edit_expense');
            $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => site_url('purchases/expenses'), 'page' => lang('expenses')), array('link' => '#', 'page' => lang('edit_expense')));
            $meta = array('page_title' => lang('edit_expense'), 'bc' => $bc);
            $this->page_construct('purchases/edit_expense', $this->data, $meta);

        }
    }

    function delete_expense($id = NULL)
    {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $expense = $this->purchases_model->getExpenseByID($id);
        if ($this->purchases_model->deleteExpense($id)) {
            if ($expense->attachment) {
                unlink($this->upload_path . $expense->attachment);
            }
            $this->session->set_flashdata('message', lang("expense_deleted"));
            redirect('purchases/expenses');
        }
    }
}
