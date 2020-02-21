<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouses extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('Warehouses_model');
        $this->load->model('mov_model');
        $this->load->model('Transfers_model');
    }


    function index()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('warehouse');
        $bc = array(array('link' => '#', 'page' => lang('warehouse')));
        $meta = array('page_title' => lang('warehouse'), 'bc' => $bc);
        $this->page_construct('warehouse/index', $this->data, $meta);

    }

    function get_warehouses()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('warehouses') . ".id as id, warehouse")
            ->from("tec_warehouses");
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('warehouses/edit/$1') . "' title='" . lang("edit_warehouse") . "' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a id='$4 ($3)' href='" . site_url('warehouses/products/$1') . "' title='" . lang("products") . "' class='barcode tip btn btn-primary btn-xs'><i class='fa fa-barcode'></i></a>  <a href='" . site_url('warehouses/delete/$1') . "' onClick=\"return confirm('" . lang('delete_warehouse') . "')\" title='" . lang("delete") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id")
       ->unset_column('id');

        echo $this->datatables->generate();
    }

    function products($id){
        $warehouse = $this->Warehouses_model->getWarehouseByID($id);

        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('products'). ' - Stock';
        $bc = array(array('link' => '#', 'page' => lang('products'). ' - ' .$warehouse->warehouse  ));
        $meta = array('page_title' => lang('products') . ' - ' .$warehouse->warehouse, 'bc' => $bc);

        $this->data['warehouse'] = $warehouse;
        $this->data['id'] = $id;
        $this->page_construct('warehouse/products', $this->data, $meta);
    }

    function get_products_by_warehouse($id) {

        $this->load->library('datatables');

        $this->datatables->select($this->db->dbprefix('products').".code as code, ".$this->db->dbprefix('products').".name as pname, type, ".$this->db->dbprefix('categories').".name as cname, stock, ".$this->db->dbprefix('warehouse_stock').".id as pid ", FALSE);

        $this->datatables->join('categories', 'categories.id=products.category_id')
        ->from('products')
        ->join('tec_warehouse_stock', 'tec_warehouse_stock.product_id = products.id')
        ->where("tec_warehouse_stock.warehouse_id = '".$id."'")
        ->where('products.estado =', 1)
        ->where("stock > 0")
        ->group_by('products.id');

        /*$this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'>
            <a href='".site_url('warehouses/traslado/$1/'.$id)."' title='" . lang("translate") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-arrow-right'></i></a></div></div>", "pid");
*/

        //$this->datatables->unset_column('pid');
        echo $this->datatables->generate();

    }

    function traslado($id, $idWare){
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');


        // $mk = $this->site->getMakerByID($product->maker_id);
        // $maker = "";
        // if($mk){
        //     $maker = $mk->maker;
        // }

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['detalle'] = $this->Warehouses_model->getStockById($id);
        $this->data['warehouse'] = $this->Warehouses_model->getWarehouseByID($this->data['detalle']->warehouse_id);
        $this->data['warehouse_id'] = $idWare;
        $this->data['product'] = $product = $this->site->getProductByID($this->data['detalle']->product_id);

        $this->load->view($this->theme.'warehouse/traslado', $this->data);
    }

    function trasladar_varios(){
        $idWare = $this->input->post('idWare');
        $selecc = explode(",", $this->input->post('seleccionados')) ;
        $id = $selecc[1];

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $idWare;
        $this->data['warehouse'] = $idWare;

        $arr_productos = [];
        $cont = 0;
        for($x = 0; $x < count($selecc); $x++){
            if($selecc[$x]!=""){
                $idP = $selecc[$x];
                $detalle[$cont] = $this->Warehouses_model->getStockDetById($idP);
                $cont++;
            }
        }
        $this->data['detalle'] = $detalle;

        $warehouse = $this->Warehouses_model->getWarehouseByID($idWare);

        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('products'). ' - Stock';
        $bc = array(array('link' => '#', 'page' => lang('products'). ' - ' .$warehouse->warehouse . ' ' .lang("translate")  ));
        $meta = array('page_title' => lang('products') . ' - ' .$warehouse->warehouse . ' ' .lang("translate") , 'bc' => $bc);

        $this->data['warehouse'] = $warehouse;
        $this->data['id'] = $id;

        $rsNumG = $this->Transfers_model->getNextNum();

        $numG = 0;
        if((int)$rsNumG->maximo>0){
            $numG = "TL-". substr( "000000".$rsNumG->maximo , -6) ;
        }else{
            $numG = "TL-000001";
        }

        $this->data['numG'] = $numG;

        $this->page_construct('warehouse/traslado2', $this->data, $meta);
    }

    function post_translate(){
        $totItems = $new_stock = $this->input->post('totItems');

        /////////////////GRABA EN CABECERA

        $dataCab = array(
            'num' =>  $this->input->post('num_g'),
            'ref' =>  $this->input->post('reference'),
            'warehouse_origin_id' =>  $this->input->post('idWarehouse'),
            'warehouse_destiny_id' =>  $this->input->post('warehouse'),
            'estado' =>  1,
            'uCrea' => $this->session->userdata('user_id'),
            'fCrea' => date("Y-m-d H:i:s")

        );

        $this->Transfers_model->addCab($dataCab);

        $cab_id = $this->db->insert_id();

        ////////////////////////////////////////////////////DETALLES////////////////////////////////////////////
        for($itNum = 1; $itNum<=$totItems; $itNum++){
            $new_stock = $this->input->post('new_stock'.$itNum);
            $idDetalle = $this->input->post('idDetalle'.$itNum);
            $idwarehouseD = $this->input->post('warehouse');
            $idProduct = $this->input->post('idProduct'.$itNum);

            $prev_stock = $this->input->post('stock'.$itNum);

            //////////////////////////TRASLADA Y REGISTRA LOS MOVIMIENTOS POR CADA ITEM///////////////////
                $q = $this->db->get_where('tec_warehouse_stock', array('warehouse_id' => $idwarehouseD, 'product_id' => $idProduct), 1);

                ///////////disminuye
                $qD = $this->db->get_where('tec_warehouse_stock', array('id' => $idDetalle), 1);
                $filaD = $qD->row();
                $data1 = array(
                    'stock' => $filaD->stock - $new_stock
                );

                $this->db->update('tec_warehouse_stock', $data1, array('id' => $idDetalle));

                if ( $q->num_rows() > 0) {//EDIT
                    $fila = $q->row();
                    ///////////traslada
                    $data = array(
                        'warehouse_id' => $idwarehouseD,
                        'product_id' => $idProduct,
                        'stock' => $fila->stock + $new_stock
                    );

                    $this->db->update('tec_warehouse_stock', $data, array('id' => $fila->id));


                }else{//NEW
                    $data = array(
                        'warehouse_id' => $idwarehouseD,
                        'product_id' => $idProduct,
                        'stock' => $new_stock
                    );

                    $this->db->insert('tec_warehouse_stock', $data);
                }

                //REGISTRO DE MOVIMIENTOS
                //INGRESO
                $idAlmDestino =  $this->input->post('warehouse');

                $dataM = array(
                    'origin_id' => $cab_id,
                    'local_id' => $this->session->userdata('local'),
                    'warehouse_id' => $idAlmDestino,
                    'product_id' => $idProduct,
                    'cant' => $new_stock,
                    'tipo' => 'I',
                    'idMotivo' => 4, //Ingreso por traslado
                    'ref' => $this->input->post('num_g'),
                    'uCrea' => $this->session->userdata('user_id'),
                    'fCrea' => date("Y-m-d H:i:s"),
                    'estado' => 1
                );
                $this->mov_model->addMovement($dataM);

                //SALIDA
                $idAlmOrigen =  $this->input->post('idWarehouse');

                $dataM = array(
                    'origin_id' => $cab_id,
                    'local_id' => $this->session->userdata('local'),
                    'warehouse_id' => $idAlmOrigen,
                    'product_id' => $idProduct,
                    'cant' => $new_stock,
                    'tipo' => 'S',
                    'idMotivo' => 6, //Salida por traslado
                    'ref' => $this->input->post('num_g'),
                    'uCrea' => $this->session->userdata('user_id'),
                    'fCrea' => date("Y-m-d H:i:s"),
                    'estado' => 1
                );
                $this->mov_model->addMovement($dataM);
            ////////////////////////////////////FIN TRASLADA Y REGISTRA LOS MOVIMIENTOS POR CADA ITEM///////////////////////
            ////////////////REGISTRA EN DETALLE DE TRASLADOS///////////////////////
            $dataDet = array(
                'transfer_id' =>  $cab_id,
                'product_id' =>  $idProduct,
                'stock_prev' =>  $prev_stock,
                'stock_new' =>  $new_stock
            );

            $this->Transfers_model->addDet($dataDet);

        }
        //////////////////////////////////////FIN DE DETALLE///////////////////////////////
        $this->session->set_flashdata('message', lang("warehouse_translated"));
        echo 1;

        //redirect("warehouses");

    }

    function add()
    {
        $this->form_validation->set_rules('value', lang("value"), 'required');

        if ($this->form_validation->run() == true) {
            $data = array('warehouse' => $this->input->post('value'));

        } elseif ($this->input->post('add_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("warehouse/add");
        }

        if ($this->form_validation->run() == true && $this->Warehouses_model->addWarehouse($data)) {
            $this->session->set_flashdata('message', lang("warehouse_added"));
            redirect("warehouses");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['customers'] = $this->site->getAllCustomers();
            $this->data['page_title'] = lang('new_warehouse');
            $bc = array(array('link' => site_url('warehouse'), 'page' => lang('warehouse')), array('link' => '#', 'page' => lang('new_warehouse')));
            $meta = array('page_title' => lang('new_warehouse'), 'bc' => $bc);
            $this->page_construct('warehouse/add', $this->data, $meta);

        }
    }

    function edit($id = NULL)
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', $this->lang->line('access_denied'));
            redirect('pos');
        }
        $this->form_validation->set_rules('value', lang("value"), 'required');
        $warehouse = $this->Warehouses_model->getWarehouseByID($id);

        if ($this->form_validation->run() == true) {
            $data = array('warehouse' => $this->input->post('value'));

        } elseif ($this->input->post('warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("warehouse/edit");
        }

        if ($this->form_validation->run() == true && $this->Warehouses_model->updateWarehouse($id, $data)) {
            $this->session->set_flashdata('message', lang("warehouse_updated"));
            redirect("warehouses");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouse'] = $warehouse;
            $this->data['customers'] = $this->site->getAllCustomers();
            $this->data['page_title'] = lang('warehouse');
            $bc = array(array('link' => site_url('warehouse'), 'page' => lang('warehouse')),array('link' => '#', 'page' => lang('warehouse')));
            $meta = array('page_title' => lang('warehouse'), 'bc' => $bc);
            $this->page_construct('warehouse/edit', $this->data, $meta);
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

        if ($this->Warehouses_model->deleteWarehouse($id)) {
            $this->session->set_flashdata('success_message', lang("warehouse_deleted"));
            redirect('warehouses', 'refresh');
        }
    }

    function transfers()
    {
        $this->data['warehouses'] = $this->site->getAllWarehouses();

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('warehouse');
        $bc = array(array('link' => '#', 'page' => lang('transfer_s')));
        $meta = array('page_title' => lang('transfer_s'), 'bc' => $bc);
        $this->page_construct('warehouse/transfer', $this->data, $meta);

    }


    function get_transfer( $ware1,$ware2,$fec1 )
    {
        //echo "es ". $this->input->post('ware1');
       $actions = "<a href='" . site_url('warehouses/view_transfer/$1') . "' class='tip btn btn-primary btn-xs' title='".$this->lang->line("view_tranfer")."'><i class='fa fa-eye'></i></a>";

        $this->load->library('datatables');
        $this->datatables
        ->select($this->db->dbprefix('transfers') . ".id as id, num, ref, ware1.warehouse as wareh1, ware2.warehouse as wareh2, username, fCrea")
        ->from('tec_transfers')
        ->join('tec_users', 'tec_users.id=transfers.uCrea')
        ->join('tec_warehouses as ware1', 'ware1.id=tec_transfers.warehouse_origin_id')
        ->join('tec_warehouses as ware2', 'ware2.id=tec_transfers.warehouse_destiny_id')
        ->add_column("Actions", "<div class='text-center'><div class='btn-group'>" . $actions . "</div></div>", "id")
        ->unset_column('id');

        if($ware1!=0){
             $this->datatables->where("ware1.id =".$ware1);
        }

        if($ware2!=0){
             $this->datatables->where("ware2.id =".$ware2);
        }

        if($fec1!=0){
            $this->datatables->where("date(tec_transfers.fCrea) ", $fec1);
        }

        echo $this->datatables->generate();
    }

    function view_transfer($idCab){

        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('transfer_s');
        $bc = array(array('link' => '#', 'page' => lang('transfer_s') ));
        $meta = array('page_title' => lang('transfer_s')  , 'bc' => $bc);

        $cab = $this->Transfers_model->getCabDetByID($idCab);
        $det = $this->Transfers_model->getDetByIdCab($idCab);
            //var_dump($det); exit;
        $this->data['cab'] = $cab;
        $this->data['dets'] = $det;

        $this->page_construct('warehouse/transferView', $this->data, $meta);
    }

}
