<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Products extends MY_Controller
{

    function __construct() {
        parent::__construct();


        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('products_model');

    }

    function index() {
        //var_dump($this->Admin);exit;
        //exit;
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('products');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $bc = array(array('link' => '#', 'page' => lang('products')));
        $meta = array('page_title' => lang('products'), 'bc' => $bc);
        $this->page_construct('products/index', $this->data, $meta);
    }



    function get_products($idAlmacen) {


        $this->load->library('datatables');

        if($idAlmacen==0){
            if ($this->Admin) {
                $this->datatables->select($this->db->dbprefix('products').".id as pid, ".$this->db->dbprefix('products').".image as image, ".$this->db->dbprefix('products').".code as code, ".$this->db->dbprefix('products').".name as pname, type, ".$this->db->dbprefix('categories').".name as cname, COALESCE(SUM(stock),0) as stock, tax_method, currency, cost, price,  barcode_symbology", false);
            } else {
                $this->datatables->select($this->db->dbprefix('products').".id as pid, ".$this->db->dbprefix('products').".image as image, ".$this->db->dbprefix('products').".code as code, ".$this->db->dbprefix('products').".name as pname, type, ".$this->db->dbprefix('categories').".name as cname, SUM(stock) as stock, tax_method, currency, price, barcode_symbology", false);
            }

            $this->datatables->join('categories', 'categories.id=products.category_id' , 'left')//************TRJ036 - ALEXANDER ROCA - 26/04/2019************
            ->from('products')
            ->join('tec_warehouse_stock', 'tec_warehouse_stock.product_id=products.id' , 'left')
            ->where('products.estado =', 1)
            ->group_by('products.id');
        }else{
            if ($this->Admin) {
                $this->datatables->select($this->db->dbprefix('products').".id as pid, ".$this->db->dbprefix('products').".image as image, ".$this->db->dbprefix('products').".code as code, ".$this->db->dbprefix('products').".name as pname, type, ".$this->db->dbprefix('categories').".name as cname, stock, tax_method, currency, cost, price,  barcode_symbology", false);
            } else {
                $this->datatables->select($this->db->dbprefix('products').".id as pid, ".$this->db->dbprefix('products').".image as image, ".$this->db->dbprefix('products').".code as code, ".$this->db->dbprefix('products').".name as pname, type, ".$this->db->dbprefix('categories').".name as cname, stock, tax_method, currency, price, barcode_symbology", false);
            }

            $this->datatables->join('categories', 'categories.id=products.category_id')
            ->from('products')
            ->join('tec_warehouse_stock', 'tec_warehouse_stock.product_id=products.id AND warehouse_id='.$idAlmacen, 'right') //, 'left'
            //->where('warehouse_id', $idAlmacen)
            ->where('products.estado =', 1)
            ->group_by('products.id');
        }

        //$this->datatables->order_by('quantity','DESC');

        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='".site_url('products/view/$1')."' title='" . lang("view") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-file-text-o'></i></a><a onclick=\"window.open('".site_url('products/single_barcode/$1')."', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;\" href='#' title='".lang('print_barcodes')."' class='tip btn btn-default btn-xs'><i class='fa fa-print'></i></a> <a onclick=\"window.open('".site_url('products/single_label/$1')."', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;\" href='#' title='".lang('print_labels')."' class='tip btn btn-default btn-xs'><i class='fa fa-print'></i></a> <a id='$4 ($3)' href='" . site_url('products/gen_barcode/$3/$5') . "' title='" . lang("view_barcode") . "' class='barcode tip btn btn-primary btn-xs'><i class='fa fa-barcode'></i></a> <a class='tip image btn btn-primary btn-xs' id='$4 ($3)' href='" . base_url('uploads/$2') . "' title='" . lang("view_image") . "'><i class='fa fa-picture-o'></i></a> <a href='" . site_url('products/edit/$1') . "' title='" . lang("edit_product") . "' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('products/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_product') . "')\" title='" . lang("delete_product") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "pid, image, code, pname, barcode_symbology");

        $this->datatables->unset_column('pid')->unset_column('barcode_symbology');
        echo $this->datatables->generate();
        


    }
   



    function get_products_search() {
        $this->load->library('datatables');

        switch($this->session->userdata('negocio')){
            case 0:
                $this->datatables->select($this->db->dbprefix('products').".id as pid,  ".$this->db->dbprefix('products').".code as code2, concat('<a href=\'#\' style=\'text-decoration:underline\'>',  ".$this->db->dbprefix('products').".code,'</a>') as code,  ".$this->db->dbprefix('products').".name as pname,  ".$this->db->dbprefix('categories').".name as cname, maker, price, IFNULL(stock, 0) as stock, price - cost as margen  ", FALSE); //quantity
                break;
            case 1:
                $this->datatables->select($this->db->dbprefix('products').".id as pid,  ".$this->db->dbprefix('products').".code as code2, concat('<a href=\'#\' style=\'text-decoration:underline\'>',  ".$this->db->dbprefix('products').".code,'</a>') as code,  ".$this->db->dbprefix('products').".name as pname, custom_field_1, custom_field_2,  ".$this->db->dbprefix('categories').".name as cname, maker, price, IFNULL(stock, 0) as stock, price - cost as margen  ", FALSE); //quantity
                break;
        }

        $this->datatables->join('categories', 'categories.id=products.category_id')
        ->join('makers', 'makers.id=products.maker_id', 'left')
        ->join('tec_warehouse_stock', 'tec_warehouse_stock.product_id=products.id AND warehouse_id='.$this->Settings->default_warehouse, 'left')
        ->from('products')
        //->where('category_id', $this->input->post('product_search') )
        ->where('products.estado =', 1)
        // ->where('stock >', 0)
        ->group_by('products.id');

        $v1 =  $this->input->post('product_search' );
        $v2 =  $this->input->post('prin_activ_search') ;
        $v3 =  $this->input->post('acc_farm_search') ;

        if(trim($v1)!=""){
             $this->datatables->where($this->db->dbprefix('products').".name like '%".$v1."%'");
        }
        if(trim($v2)!=""){
             $this->datatables->where("custom_field_1 like '%".$v2."%'");
        }
        if(trim($v3)!=""){
             $this->datatables->where("custom_field_2 like '%".$v3."%'");
        }

        //$this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='".site_url('products/view/$1')."' title='" . lang("view") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-file-text-o'></i></a></div></div>", "pid");

        $this->datatables->add_column("Actions", "<input type='hidden' value='$1'><div class='text-center'><div class='btn-group'><button  vid='$1' title='" . lang("add") . "' class='tip btn btn-primary btn-xs' ><i class='fa fa-plus'></i></button></div></div>", "code2");


        //$this->datatables->unset_column('pid')->unset_column('barcode_symbology');
        $this->datatables->unset_column('pid');
        $this->datatables->unset_column('code2');
        echo $this->datatables->generate();

    }



    function view($id = NULL) {
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $product = $this->site->getProductByID($id);

        $mk = $this->site->getMakerByID($product->maker_id);
        $maker = "";
        if($mk){
            $maker = $mk->maker;
        }
        $this->data['maker'] = $maker;
        $this->data['product'] = $product;
        $this->data['category'] = $this->site->getCategoryByID($product->category_id);
        $this->data['combo_items'] = $product->type == 'combo' ? $this->products_model->getComboItemsByPID($id) : NULL;
        $this->load->view($this->theme.'products/view', $this->data);

    }

    function barcode($product_code = NULL) {
        if ($this->input->get('code')) {
            $product_code = $this->input->get('code');
        }

        $data['product_details'] = $this->products_model->getProductByCode($product_code);
        $data['img'] = "<img src='" . base_url() . "index.php?products/gen_barcode&code={$product_code}' alt='{$product_code}' />";
        $this->load->view('barcode', $data);

    }

    function product_barcode($product_code = NULL, $bcs = 'code39', $height = 60) {
        if ($this->input->get('code')) {
            $product_code = $this->input->get('code');
        }
        return "<img src='" . base_url() . "products/gen_barcode/{$product_code}/{$bcs}/{$height}' alt='{$product_code}' />";
    }

    function gen_barcode($product_code = NULL, $bcs = 'code39', $height = 60, $text = 1) {
        $drawText = ($text != 1) ? FALSE : TRUE;
        $this->load->library('zend');
        $this->zend->load('Zend/Barcode');
        $barcodeOptions = array('text' => $product_code, 'barHeight' => $height, 'drawText' => $drawText);
        $rendererOptions = array('imageType' => 'png', 'horizontalPosition' => 'center', 'verticalPosition' => 'middle');
        $imageResource = Zend_Barcode::render($bcs, 'image', $barcodeOptions, $rendererOptions);
        return $imageResource;
    }


    function print_barcodes() {
        $this->load->library('pagination');

        $per_page = $this->input->get('per_page') ? $this->input->get('per_page') : 0;

        $config['base_url'] = site_url('products/print_barcodes');
        $config['total_rows'] = $this->products_model->products_count();
        $config['per_page'] = 20;
        $config['num_links'] = 5;

        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $this->pagination->initialize($config);

        $products = $this->products_model->fetch_products($config['per_page'], $per_page);
        $r = 1;
        $html = "";
        $html .= '<table class="table table-bordered">
        <tbody><tr>';
        foreach ($products as $pr) {
            if ($r != 1) {
                $rw = (bool)($r & 1);
                $html .= $rw ? '</tr><tr>' : '';
            }
            $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $pr->name . '</strong><br>' . $this->product_barcode($pr->code, $pr->barcode_symbology, 60); // . '<br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $pr->price . '</span></td>';
            $r++;
        }
        $html .= '</tr></tbody>
        </table>';

        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_barcodes");
        $this->load->view($this->theme.'products/print_barcodes', $this->data);

    }

    function print_labels() {
        $this->load->library('pagination');

        $per_page = $this->input->get('per_page') ? $this->input->get('per_page') : 0;

        $config['base_url'] = site_url('products/print_labels');
        $config['total_rows'] = $this->products_model->products_count();
        $config['per_page'] = 10;
        $config['num_links'] = 5;

        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $this->pagination->initialize($config);

        $products = $this->products_model->fetch_products($config['per_page'], $per_page);

        $html = "";

        foreach ($products as $pr) {
            $html .= '<div class="labels"><strong>' . $pr->name . '</strong><br>' . $this->product_barcode($pr->code, $pr->barcode_symbology, 25) . '<br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $pr->price . '</span></div>';
        }

        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_labels");
        $this->load->view($this->theme.'products/print_labels', $this->data);

    }

    function single_barcode($product_id = NULL)
    {

        $product = $this->site->getProductByID($product_id);
        $detail = $this->products_model->getProductStock($product_id);

        $html = "";
        $html .= '<table class="table table-bordered">
        <tbody><tr>';
        if($detail->stock > 0) {
            for ($r = 1; $r <= $detail->stock; $r++) {
                if ($r != 1) {
                    $rw = (bool)($r & 1);
                    $html .= $rw ? '</tr><tr>' : '';
                }
                $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 60); //. ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $product->price . '</span></td>';
            }
        } else {
            for ($r = 1; $r <= 20; $r++) {
            if ($r != 1) {
                $rw = (bool)($r & 1);
                $html .= $rw ? '</tr><tr>' : '';
            }
            $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 60); //. ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $product->price . '</span></td>';
        }
        }
        $html .= '</tr></tbody>
        </table>';

        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_barcodes");
        $this->load->view($this->theme . 'products/single_barcode', $this->data);
    }

    function single_label($product_id = NULL, $warehouse_id = NULL)
    {

        $product = $this->site->getProductByID($product_id);
        $detail = $this->products_model->getProductStock($product_id);
        $html = "";
        if($detail->stock > 0) {
            for ($r = 1; $r <= $detail->stock; $r++) {
                $html .= '<div class="labels"><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 25) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $product->price . '</span></div>';
            }
        } else {
            for ($r = 1; $r <= 10; $r++) {
                $html .= '<div class="labels"><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 25) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $product->price . '</span></div>';
            }
        }
        $this->data['html'] = $html;
        $this->data['page_title'] = lang("barcode_label");
        $this->load->view($this->theme . 'products/single_label', $this->data);

    }


    function add() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->data['categoriesOrdered'] = $this->site->getAllCategoriesOrdered();
        $this->data['mkrs'] = $this->site->getAllMakers();
        //**************************TRJ047 - ALEXANDER ROCA - 08/07/2019***************
        $this->form_validation->set_rules('code', lang("product_code"), 'trim|min_length[2]|max_length[50]|required|alpha_numeric');
        //$this->form_validation->set_rules('code', lang("product_code"), 'trim|is_unique[products.code]|min_length[2]|max_length[50]|required|alpha_numeric');
        //**************************TRJ047 - ALEXANDER ROCA - 08/07/2019***************
        $this->form_validation->set_rules('name', lang("product_name"), 'required');
        $this->form_validation->set_rules('category', lang("category"), 'required');
        $this->form_validation->set_rules('price', lang("product_price"), 'required|is_numeric');
        $this->form_validation->set_rules('cost', lang("product_cost"), 'required|is_numeric');
        // $this->form_validation->set_rules('product_tax', lang("product_tax"), 'required|is_numeric');
        $this->form_validation->set_rules('quantity', lang("quantity"), 'is_numeric');
        $this->form_validation->set_rules('alert_quantity', lang("alert_quantity"), 'is_numeric');
        $this->form_validation->set_rules('currency', lang("currency_name"), 'required');

        if ($this->form_validation->run() == true) {

            $data = array(
                'type' => $this->input->post('type'),
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'category_id' => $this->input->post('category'),
                'currency' => $this->input->post('currency'),
                'price' => $this->input->post('price'),
                'cost' => $this->input->post('cost'),
                // 'tax' => $this->input->post('product_tax'),
                'tax_method' => $this->input->post('tax_method'),
                'quantity' => $this->input->post('quantity'),
                'alert_quantity' => $this->input->post('alert_quantity'),
                'details' => $this->input->post('details'),
                'maker_id' => $this->input->post('maker'),
                'custom_field_1' => $this->input->post('custom_field_1'),
                'custom_field_2' => $this->input->post('custom_field_2'),
                'presentation' => $this->input->post('presentation'),
                'uCrea' => $this->session->userdata('user_id'),
                'fCrea' => date("Y-m-d H:i:s"),
                'estado' => 1
                );

            if ($this->input->post('type') == 'combo') {
                $c = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                        $items[] = array(
                            'item_code' => $_POST['combo_item_code'][$r],
                            'quantity' => $_POST['combo_item_quantity'][$r]
                        );
                    }
                }
            } else {
                $items = array();
            }

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '500';
                $config['max_width'] = '800';
                $config['max_height'] = '800';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/add", 'refresh');
                }

                $photo = $this->upload->file_name;
                $data['image'] = $photo;

                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/' . $photo;
                $config['new_image'] = 'uploads/thumbs/' . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 110;
                $config['height'] = 110;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    $this->session->set_flashdata('error', $this->image_lib->display_errors());
                    redirect("products/add");
                }

            }
            // $this->tec->print_arrays($data, $items);
        }
        
        if($this->products_model->validate_products($this->input->post('code'))==false){

            if ($this->form_validation->run() == true && $this->products_model->addProduct($data, $items)) {

                $this->session->set_flashdata('message', lang("product_added"));
                redirect('products');

            } else {

                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['categories'] = $this->site->getAllCategories();
                $this->data['page_title'] = lang('add_product');
                $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('add_product')));
                $meta = array('page_title' => lang('add_product'), 'bc' => $bc);
                $this->page_construct('products/add', $this->data, $meta);

            }
            
        }else{
            $this->session->set_flashdata('error', lang("error_code_product"));
            redirect("products/add");
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
        $this->data['categoriesOrdered'] = $this->site->getAllCategoriesOrdered();
        $this->data['mkrs'] = $this->site->getAllMakers();
        $pr_details = $this->site->getProductByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang("product_code"), 'is_unique[products.code]');
        }
        $this->form_validation->set_rules('code', lang("product_code"), 'trim|min_length[2]|max_length[50]|required|alpha_numeric');
        $this->form_validation->set_rules('name', lang("product_name"), 'required');
        $this->form_validation->set_rules('category', lang("category"), 'required');
        $this->form_validation->set_rules('price', lang("product_price"), 'required|is_numeric');
        $this->form_validation->set_rules('cost', lang("product_cost"), 'required|is_numeric');
        // $this->form_validation->set_rules('product_tax', lang("product_tax"), 'required|is_numeric');
        $this->form_validation->set_rules('quantity', lang("quantity"), 'is_numeric');
        $this->form_validation->set_rules('alert_quantity', lang("alert_quantity"), 'is_numeric');
        $this->form_validation->set_rules('currency', lang("currency_name"), 'required');

        if ($this->form_validation->run() == true) {

            $data = array(
                'type' => $this->input->post('type'),
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'category_id' => $this->input->post('category'),
                'currency' => $this->input->post('currency'),
                'price' => $this->input->post('price'),
                'cost' => $this->input->post('cost'),
                // 'tax' => $this->input->post('product_tax'),
                'tax_method' => $this->input->post('tax_method'),
                'quantity' => $this->input->post('quantity'),
                'alert_quantity' => $this->input->post('alert_quantity'),
                'details' => $this->input->post('details'),
                'maker_id' => $this->input->post('maker'),
                'custom_field_1' => $this->input->post('custom_field_1'),
                'custom_field_2' => $this->input->post('custom_field_2'),
                'presentation' => $this->input->post('presentation'),
                'uActualiza' => $this->session->userdata('user_id'),
                'fActualiza' => date("Y-m-d H:i:s"),
                );

            if ($this->input->post('type') == 'combo') {
                $c = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                        $items[] = array(
                            'item_code' => $_POST['combo_item_code'][$r],
                            'quantity' => $_POST['combo_item_quantity'][$r]
                        );
                    }
                }
            } else {
                $items = array();
            }

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '500';
                $config['max_width'] = '800';
                $config['max_height'] = '800';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->upload->set_flashdata('error', $error);
                    redirect("products/edit/" . $id);
                }

                $photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/' . $photo;
                $config['new_image'] = 'uploads/thumbs/' . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 110;
                $config['height'] = 110;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    $this->upload->set_flashdata('error', $this->image_lib->display_errors());
                    redirect("products/edit/" . $id);
                }

            } else {
                $photo = NULL;
            }

        }

        if ($this->form_validation->run() == true && $this->products_model->updateProduct($id, $data, $items, $photo)) {

            $this->session->set_flashdata('message', lang("product_updated"));
            redirect("products");

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $product = $this->site->getProductByID($id);
            if($product->type == 'combo') {
                $combo_items = $this->products_model->getComboItemsByPID($id);
                foreach ($combo_items as $combo_item) {
                    $cpr = $this->site->getProductByID($combo_item->id);
                    $cpr->qty = $combo_item->qty;
                    $items[] = array('id' => $cpr->id, 'row' => $cpr);
                }
                $this->data['items'] = $items;
            }
            $this->data['product'] = $product;
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['page_title'] = lang('edit_product');
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('edit_product')));
            $meta = array('page_title' => lang('edit_product'), 'bc' => $bc);
            $this->page_construct('products/edit', $this->data, $meta);

        }
    }

    function import() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang("disabled_in_demo"));
                redirect('pos');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '500';
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/import");
                }


                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("uploads/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                array_shift($arrResult);

                switch($this->session->userdata('negocio')){
                    case 0:
                        $keys = array('code', 'name', 'cost', 'price', 'category', 'maker', 'presentation');
                        break;
                    case 1:
                        $keys = array('code', 'name', 'cost', 'price', 'category', 'maker', 'custom_field_1', 'custom_field_2', 'presentation');
                        break;
                }

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                if (sizeof($final) > 1001) {
                    $this->session->set_flashdata('error', lang("more_than_allowed"));
                    redirect("products/import");
                }

                $fila = 0;

                foreach ($final as $csv_pr) {

                    $fila = $fila + 1;

                    if ($this->products_model->getProductByCode($csv_pr['code'])) {
                        $this->session->set_flashdata('error', lang("check_product_code") . " (" . $csv_pr['code'] . "). " . lang("code_already_exist"));
                        redirect("products/import");
                    }
                    // if (!is_numeric($csv_pr['tax'])) {
                    //     $this->session->set_flashdata('error', lang("check_product_tax") . " (" . $csv_pr['tax'] . "). " . lang("tax_not_numeric"));
                    //     redirect("products/import");
                    // }
                    if(! ($category = $this->site->getCategoryByCode($csv_pr['category']))) {
                        $this->session->set_flashdata('error', lang("check_category") . " (" . $csv_pr['category'] . "). " . lang("category_x_exist"). " (Fila: " . $fila . ")");
                        redirect("products/import");
                    }
                    if (!is_numeric($csv_pr['price'])) {
                        $this->session->set_flashdata('error', lang("check_product_price") . " (" . $csv_pr['price'] . "). " . lang("price_not_numeric"));
                        redirect("products/import");
                    }

                    switch($this->session->userdata('negocio')){
                        case 0:

                            if(!empty($csv_pr['maker']))
                            {
                                if(! ($maker = $this->site->getMakerByName(utf8_encode($csv_pr['maker'])))) {
                                    $this->session->set_flashdata('error', $csv_pr['maker']. '. ' . lang("maker") . ' no encontrado'. " (Fila: " . $fila . ")");
                                    redirect("products/import");
                                    // $csv_pr['laboratory'] = '';
                                }
                            }

                            $data[] = array(
                                'type' => 'standard',
                                'code' => $csv_pr['code'],
                                'name' => utf8_encode($csv_pr['name']),
                                'cost' => !empty($csv_pr['cost']) ? $csv_pr['cost'] : NULL,//$cost,//$csv_pr['cost'],
                                // 'tax' => $csv_pr['tax'],
                                'tax_method' => '0',
                                'price' => $csv_pr['price'],
                                'category_id' => $category->id,
                                'maker_id' => !empty($csv_pr['maker']) ? $maker->id : NULL,//$laboratory,//$csv_pr['laboratory_id']
                                'presentation' => !empty($csv_pr['presentation']) ? utf8_encode($csv_pr['presentation']) : NULL,
                                'uCrea' => $this->session->userdata('user_id'),
                                'fCrea' => date("Y-m-d H:i:s"),
                                'estado' => 1
                            );
                            break;

                        case 1:

                            if(!empty($csv_pr['maker']))
                            {
                                if(! ($maker = $this->site->getMakerByName(utf8_encode($csv_pr['maker'])))) {
                                    $this->session->set_flashdata('error', $csv_pr['maker']. '. ' . lang("laboratory") . ' no encontrado'. " (Fila: " . $fila . ")");
                                    redirect("products/import");
                                    // $csv_pr['laboratory'] = '';
                                }
                            }

                            $data[] = array(
                                'type' => 'standard',
                                'code' => $csv_pr['code'],
                                'name' => utf8_encode($csv_pr['name']),
                                'cost' => !empty($csv_pr['cost']) ? $csv_pr['cost'] : NULL,//$cost,//$csv_pr['cost'],
                                // 'tax' => $csv_pr['tax'],
                                'tax_method' => '0',
                                'price' => $csv_pr['price'],
                                'category_id' => $category->id,
                                'maker_id' => !empty($csv_pr['maker']) ? $maker->id : NULL,//$laboratory,//$csv_pr['laboratory_id']
                                'custom_field_1' => !empty($csv_pr['custom_field_1']) ? utf8_encode($csv_pr['custom_field_1']) : NULL,
                                'custom_field_2' => !empty($csv_pr['custom_field_2']) ? utf8_encode($csv_pr['custom_field_2']) : NULL,
                                'presentation' => !empty($csv_pr['presentation']) ? utf8_encode($csv_pr['presentation']) : NULL,
                                'uCrea' => $this->session->userdata('user_id'),
                                'fCrea' => date("Y-m-d H:i:s"),
                                'estado' => 1
                            );
                            break;
                    }
                }
                //print_r($data); die();
            }
        }

        if ($this->form_validation->run() == true && $this->products_model->add_products($data)) {

            $this->session->set_flashdata('message', lang("products_added"));
            redirect('products');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['page_title'] = lang('import_products');
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('import_products')));
            $meta = array('page_title' => lang('import_products'), 'bc' => $bc);
            $this->page_construct('products/import', $this->data, $meta);

        }
    }


    function delete($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        if ($this->products_model->deleteProduct($id)) {
            $this->session->set_flashdata('message', lang("product_deleted"));

            $dataP = array(
                'uActualiza' => $this->session->userdata('user_id'),
                'fActualiza' => date("Y-m-d H:i:s")
            );

            $this->products_model->updateProduct($id, $dataP);

            redirect('products');
        }

    }

    function suggestions()
    {
         $term = $this->input->get('term', TRUE);

         $rows = $this->products_model->getProductNames($term);
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

    function movements() {
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('movements_search');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['motivos'] = $this->site->getAllmotives();

        $bc = array(array('link' => '#', 'page' => lang('products')));
        $meta = array('page_title' => lang('products') . " - " . lang('movements_search'), 'bc' => $bc);
        $this->page_construct('products/movements', $this->data, $meta);
    }


    function get_movements(){//$idAlmacen, $producto, $idMotivo) {
        $this->load->library('datatables');
        $this->datatables->select(
            //$this->db->dbprefix('product_mov').".id as pid,
            $this->db->dbprefix('product_mov').".fCrea,
            ".$this->db->dbprefix('warehouses').".warehouse as warehouse_name,
            ".$this->db->dbprefix('products').".name as product_name,
            ".$this->db->dbprefix('product_mov').".cant as quantity,
            tipo,
            ".$this->db->dbprefix('mov_motive').".motive as motivo,
            ref,
            user1.username as user1");
        $this->datatables->from('tec_product_mov');
        $this->datatables->join('tec_products', 'tec_products.id=tec_product_mov.product_id' , 'left');
        $this->datatables->join('tec_warehouses', 'tec_warehouses.id=tec_product_mov.warehouse_id' , 'left');
        $this->datatables->join('tec_mov_motive', 'tec_mov_motive.id=tec_product_mov.idMotivo' , 'left');

        $this->datatables->join('tec_users as user1', 'user1.id=tec_product_mov.uCrea' , 'left');
        $this->datatables->join('tec_users as user2', 'user2.id=tec_product_mov.uActualiza' , 'left');

        $this->datatables->where('products.estado !=',0);

        $idAlmacen =  $this->input->post('idAlmacen');
        $producto =  $this->input->post('producto') ;
        $idMotivo =  $this->input->post('idMotivo') ;

        if($idAlmacen!=0) {
            $this->datatables->where('tec_product_mov.warehouse_id',$idAlmacen );
        }


        if( trim($producto) !="") {
            $this->datatables->where("tec_products.name like '%".$producto."%'");
        }


        if($idMotivo!=0) {
            $this->datatables->where('tec_product_mov.idMotivo',$idMotivo );
        }

        echo $this->datatables->generate();

    }

    
    }
    
