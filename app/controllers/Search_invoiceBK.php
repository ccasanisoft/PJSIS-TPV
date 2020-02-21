<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Search_invoice extends MY_Controller {


    function __construct() {
        parent::__construct();


        $this->load->library('form_validation');
        $this->load->model('products_model');
        // Load the captcha helper
        $this->load->helper('captcha');
    }

    function index($flag) {

        //LIMPIA LAS CARPETAS
        $dir = 'uploads/captcha_images/';
        foreach(glob($dir.'*.*') as $v){
            unlink($v);
        }

        // Captcha configuration
        $config = array(
            'img_path'      => 'uploads/captcha_images/',
            'img_url'       => base_url().'/uploads/captcha_images/',
            'img_width'     => '150',
            'img_height'    => 50,
            'word_length'   => 5,
            'font_size'     => 16
        );
        $captcha = create_captcha($config);

        // Unset previous captcha and store new captcha word
        $this->session->unset_userdata('captchaCode');
        $this->session->set_userdata('captchaCode',$captcha['word']);

        // Send captcha image to view
        $this->data['captchaImg'] = $captcha['image'];

        $this->data['v1'] = $_SESSION["v1"];
        $this->data['v2'] = $_SESSION["v2"];
        $this->data['v3'] = $_SESSION["v3"];
        $this->data['v4'] = $_SESSION["v4"];
        $this->data['v5'] = $_SESSION["v5"];
        $this->data['v6'] = $_SESSION["v6"];


        //exit;
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('products');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $bc = array(array('link' => '#', 'page' => lang('products')));
        $meta = array('page_title' => lang('products'), 'bc' => $bc);
        //$this->page_construct2('invoice/index', $this->data, $meta);

		$this->load->view($this->theme.'invoice/index', $this->data, $meta);
	}

    public function search(){
        $v1 =  $this->input->post('document_type' );
        $v2 =  $this->input->post('correlativo') ;
        $v3 =  $this->input->post('number') ;
        $v4 =  $this->input->post('date') ;
        $fecF = $v4;
        /*$arrF = explode(" ", $v4);
        if(isset($arrF[1])){
            $fecF = $arrF[0];
        }*/

        $v5 =  $this->input->post('ruc') ;
        $v6 =  $this->input->post('total') ;


      // If captcha form is submitted
        $_SESSION["v1"] = $v1;
        $_SESSION["v2"] = $v2;
        $_SESSION["v3"] = $v3;
        $_SESSION["v4"] = $v4;
        $_SESSION["v5"] = $v5;
        $_SESSION["v6"] = $v6;

        $inputCaptcha = $this->input->post('captcha');
        $sessCaptcha = $this->session->userdata('captchaCode');
        if($inputCaptcha === $sessCaptcha){
            if($v1==1){//boletas
                 $q = $this->db->select($this->db->dbprefix('sales_files') . ".id,file_name ")
                    ->from("tec_sales_files")
                    ->join('tec_sales', 'tec_sales_files.sale_id = tec_sales.id')
                    ->join('tec_customers', 'tec_sales.customer_id = tec_customers.id')
                ->where('document_type', $v1)
                ->where("invoice_id  = '".$v2 . "-" .$v3 ."'")
                ->where('DATE(tec_sales.date)', $fecF)
                ->where("grand_total", $v6);
            }else{// facturas
                 $q = $this->db->select($this->db->dbprefix('sales_files') . ".id,file_name ")
                    ->from("tec_sales_files")
                    ->join('tec_sales', 'tec_sales_files.sale_id = tec_sales.id')
                    ->join('tec_customers', 'tec_sales.customer_id = tec_customers.id')
                ->where('document_type', $v1)
                ->where("invoice_id  = '".$v2 . "-" .$v3 ."'")
                ->where('DATE(tec_sales.date)', $fecF)
                ->where("cf2", $v5)
                ->where("grand_total", $v6);
            }


            $q = $this->db->get();
            $data=[];

            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
               $data;
            }
            if(!$data){
                $_SESSION["noencontrado"] = -1;
                $_SESSION["file_name"] = "";

            }else{ //ENCONTRÓ!
                $_SESSION["v1"] = "";
                $_SESSION["v2"] = "";
                $_SESSION["v3"] = "";
                $_SESSION["v4"] = "";
                $_SESSION["v5"] = "";
                $_SESSION["v6"] = "";
                $_SESSION["noencontrado"] = $data[0]->id;
                $_SESSION["file_name"] = $data[0]->file_name;
            }
            $_SESSION["errCaptcha"] = "" ;

            redirect($_SERVER["HTTP_REFERER"]);

        }else{
            $_SESSION["noencontrado"] = "";
            $_SESSION["file_name"] = "";

            $_SESSION["errCaptcha"] = "Error de Captcha, por favor intente otra vez" ;
            redirect($_SERVER["HTTP_REFERER"]);
            echo 'Captcha code was not match, please try again.';
        }

    }

    public function refresh(){
        //LIMPIA LAS CARPETAS
        /*$dir = 'uploads/captcha_images/';
        foreach(glob($dir.'*.*') as $v){
            unlink($v);
        }*/
        // Captcha configuration
        $config = array(
            'img_path'      => 'uploads/captcha_images/',
            'img_url'       => base_url().'/uploads/captcha_images/',
            'img_width'     => '150',
            'img_height'    => 50,
            'word_length'   => 5,
            'font_size'     => 16
        );
        $captcha = create_captcha($config);

        // Unset previous captcha and store new captcha word
        $this->session->unset_userdata('captchaCode');
        $this->session->set_userdata('captchaCode',$captcha['word']);

        // Display captcha image
        echo $captcha['image'];
    }
    function get_results()
    {
        $v1 =  $this->input->post('document_type' );
        $v2 =  $this->input->post('number') ;
        $v3 =  $this->input->post('date') ;
        $v4 =  $this->input->post('ruc') ;

        $fecF = "";
        $arrF = explode(" ", $v3);
        if(isset($arrF[1])){
            $fecF = $arrF[0];
        }

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('sales_files') . ".id as id, tec_customers.name,tec_customers.cf2, CASE WHEN document_type = 1 THEN 'BOLETA' ELSE 'FACTURA' END AS vdoctype, tec_sales.id as ndoc, grand_total, file_name ")
            ->from("tec_sales_files")
            ->join('tec_sales', 'tec_sales_files.sale_id = tec_sales.id')
            ->join('tec_customers', 'tec_sales.customer_id = tec_customers.id');



        if($v1!=0) { $this->datatables->where('document_type', $v1); }
        if($v2!="") { $this->datatables->where("tec_sales.id like '%".$v2."%'"); }
        //if($v3!="") { $this->datatables->where("tec_sales.date like '%".$v3."%'"); }
                    if($v3!="") { $this->datatables->where('DATE(tec_sales.date)', $fecF); }
        if($v4!="") { $this->datatables->where("tec_customers.cf2 like '%".$v4."%'"); }



        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a target='_blank' href='". base_url().'uploads/pdf/' ."$1' title='" . lang("download") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-file-pdf-o'></i></a> </div>", "file_name");
		$this->datatables->unset_column('file_name');
        echo $this->datatables->generate();
    }

    public function limpiarsesion(){
        $_SESSION["v1"] = "";
        $_SESSION["v2"] = "";
        $_SESSION["v3"] = "";
        $_SESSION["v4"] = "";
        $_SESSION["v5"] = "";
        $_SESSION["v6"] = "";

        $_SESSION["noencontrado"] = "";
        $_SESSION["file_name"] = "";
        $_SESSION["errCaptcha"] = "" ;
    }
}
