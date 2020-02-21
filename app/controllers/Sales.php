<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends MY_Controller
{

  function __construct()
  {
    parent::__construct();

    if (!$this->loggedIn) {
      redirect('login');
    }
    $this->load->library('form_validation');
    // $this->load->library('Pdf');

    $this->load->model('sales_model');
    $this->load->model('pos_model');
    $this->load->model('mov_model');
    $this->load->model('Warehouses_model');

    // require("funciones.php");
    // include("QRCodeGenerator.class.php");

    $this->digital_file_types = 'zip|pdf|doc|docx|xls|xlsx|jpg|png|gif';

  }

  function index()
  {
    $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
    $this->data['page_title'] = lang('sales');
    $bc = array(array('link' => '#', 'page' => lang('sales')));
    $meta = array('page_title' => lang('sales'), 'bc' => $bc);
    $this->page_construct('sales/index', $this->data, $meta);
  }

  function get_sales($estado){

      $this->load->library('datatables');
      $this->datatables->select($this->db->dbprefix('sales') . ".id as pid,  date, customer_name, currency, grand_total, paid, status,
       CASE WHEN document_type = 1 THEN 'BOLETA' WHEN document_type = 2 THEN 'FACTURA' ELSE 'NOTA DE VENTA' END AS vdoctype,
        invoice_id as ndoc, sales.estado, 
         canal, if(T2.estadoSend = 1, 'Error de envío', if(T2.estadoSend = 2, 'Enviado', if(T2.estadoSend = 3, 'Rechazado', if(T2.estadoSend = 4, 'Procesado', '')))) ," . $this->db->dbprefix('locals') . ".code", false); 
         $this->datatables->from('sales');
  

    if ($estado == "DEBE" || $estado =="Pagado" || $estado == 'Parcial') {
      $this->datatables->where('status', $estado);
    }
    else if($estado == 3){
       $this->datatables->where("sales.estado = 0");
    }else if($estado == 1){
       $this->datatables->where("sales.estado = 1");
    }else if($estado == 2){
       $this->datatables->where("sales.estado = 1 OR sales.estado = 0");
    }else{
       $this->datatables->where("sales.estado = 1");
    }

    /*$this->datatables->where("sales.estado = 1 OR sales.estado IS NULL")*/;//**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************

    $this->datatables->join('canals', 'canals.id=sales.canal_id', 'left');
    $this->datatables->join('locals', 'locals.id=sales.local_id', 'left');

  //**********TRJ041 - ALEXANDER ROCA - 29/04/2019**************
  
  $this->datatables->join('(SELECT detalle.sale_id as id_venta, envio.status as estadoSend FROM `tec_send_invoice` envio Inner JOIN tec_send_invoice_items detalle on detalle.send_invoice_id = envio.id WHERE envio.estado=1) as T2', 'T2.id_venta=sales.id', 'left');

  // version 02
  //**********TRJ041 - ALEXANDER ROCA - 29/04/2019**************
    if (!$this->Admin) {
      if (!$this->Caja) {
       $user_id = $this->session->userdata('user_id');
        $this->datatables->where('created_by', $user_id);
      }
    }

    if ($this->Admin) {

  if($this->session->userdata('user_id')==1){
    $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='#' onClick=\"MyWindow=window.open('" . site_url('pos/view/$1/1') . "', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=350,height=600'); return false;\" title='" . lang("view_invoice") . "' class='tip btn btn-primary btn-xs'><i class='fa fa-list'></i></a>
          <a href='" . site_url('sales/payments/$1') . "' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a>
          <a href='" . site_url('sales/add_payment/$1') . "' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a>
          <a href='" . site_url('sales/anular/$1') . "' onClick=\"return confirm('" . lang('disable_x_sale') . "')\" title='" . lang("disable_sale") . "' class='tip btn btn-info btn-xs' ><i class='fa fa-ban'></i></a>
          <a href='" . site_url('sales/recreate_pdf/$1') . "' title='" . lang("recreate_pdf") . "' class='tip btn btn-danger btn-xs' ><i class='fa fa-file-pdf-o'></i></a>
      <a href='" . site_url('sales/reenvioSunatXml/$1') . "' onClick=\"return confirm('" . lang('forwarding_x_sale') . "')\" title='" . lang("forwarding_sale") . "' class='tip btn btn-warning btn-xs' ><i class='fa fa-paper-plane-o'></i></a>
      <a href='" . site_url('sales/reArmarXml/$1') . "' onClick=\"return confirm('" . lang('recreate_xml_Q') . "')\" title='" . lang("recreate_xml") . "' class='tip btn btn-success btn-xs' ><i class='fa fa-file-code-o'></i></a>
      <a href='" . site_url('sales/removeSale/$1') . "' onClick=\"return confirm('" . lang('remove_sale_Q') . "')\" title='" . lang("remove_sale") . "' class='tip btn btn-warning btn-xs' ><i class='fa fa-trash-o'></i></a>
      <a href='" . site_url('Referral_guide/add/$1') . "' onClick=\"return confirm('" . lang('create_Referral_guide') . "')\" title='" . lang("Referral_guide_created") . "' class='tip btn btn-success btn-xs' ><i class='fa fa-truck'></i></a>
          </div></div>", "pid");
  }else{
    $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='#' onClick=\"MyWindow=window.open('" . site_url('pos/view/$1/1') . "', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=350,height=600'); return false;\" title='" . lang("view_invoice") . "' class='tip btn btn-primary btn-xs'><i class='fa fa-list'></i></a>
          <a href='" . site_url('sales/payments/$1') . "' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a>
          <a href='" . site_url('sales/add_payment/$1') . "' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a>
          <a href='" . site_url('sales/anular/$1') . "' onClick=\"return confirm('" . lang('disable_x_sale') . "')\" title='" . lang("disable_sale") . "' class='tip btn btn-info btn-xs' ><i class='fa fa-ban'></i></a>
          <a href='" . site_url('sales/recreate_pdf/$1') . "' title='" . lang("recreate_pdf") . "' class='tip btn btn-danger btn-xs' ><i class='fa fa-file-pdf-o'></i></a>
      <a href='" . site_url('sales/reenvioSunatXml/$1') . "' onClick=\"return confirm('" . lang('forwarding_x_sale') . "')\" title='" . lang("forwarding_sale") . "' class='tip btn btn-warning btn-xs' ><i class='fa fa-paper-plane-o'></i></a>
      <a href='" . site_url('Referral_guide/add/$1') . "' onClick=\"return confirm('" . lang('create_Referral_guide') . "')\" title='" . lang("Referral_guide_created") . "' class='tip btn btn-success btn-xs' ><i class='fa fa-truck'></i></a>
          </div></div>", "pid");
      }
  
    } else {

      $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='#' onClick=\"MyWindow=window.open('" . site_url('pos/view/$1/1') . "', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=350,height=600'); return false;\" title='" . lang("view_invoice") . "' class='tip btn btn-primary btn-xs'><i class='fa fa-list'></i></a>
          <a href='" . site_url('sales/payments/$1') . "' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a>
          <a href='" . site_url('sales/add_payment/$1') . "' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a>
          <a href='" . site_url('sales/anular/$1') . "' onClick=\"return confirm('" . lang('disable_x_sale') . "')\" title='" . lang("disable_sale") . "' class='tip btn btn-info btn-xs' ><i class='fa fa-ban'></i></a>
      <a href='" . site_url('Referral_guide/add/$1') . "' onClick=\"return confirm('" . lang('create_Referral_guide') . "')\" title='" . lang("Referral_guide_created") . "' class='tip btn btn-success btn-xs' ><i class='fa fa-truck'></i></a>
          </div></div>", "pid");
    }


    $this->datatables->unset_column('pid');
    echo $this->datatables->generate();

  }






  function opened()
  {
    $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
    $this->data['page_title'] = lang('opened_bills');
    $bc = array(array('link' => '#', 'page' => lang('opened_bills')));
    $meta = array('page_title' => lang('opened_bills'), 'bc' => $bc);
    $this->page_construct('sales/opened', $this->data, $meta);
  }

  function get_opened_list()
  {

    $this->load->library('datatables');
    $this->datatables
      ->select("id, date, customer_name, hold_ref, CONCAT(total_items, ' (', total_quantity, ')') as items, grand_total", false)
      ->from('suspended_sales');
    if (!$this->Admin) {
      $user_id = $this->session->userdata('user_id');
      $this->datatables->where('created_by', $user_id);
    }
    $this->datatables->add_column(
      "Actions",
      "<div class='text-center'><div class='btn-group'><a href='" . site_url('pos/?hold=$1') . "' title='" . lang("click_to_add") . "' class='tip btn btn-info btn-xs'><i class='fa fa-th-large'></i></a>
      <a href='" . site_url('sales/delete_holded/$1') . "' onClick=\"return confirm('" . lang('alert_x_holded') . "')\" title='" . lang("delete_sale") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>",
      "id"
    )
      ->unset_column('id');

    echo $this->datatables->generate();
  }

  function recreate_pdf($id = null)
  {

     $data_inv = (array)$this->pos_model->getSaleByID($id);

    if ($data_inv['invoice_id'] != null) {

      if ($data_inv['document_type'] != 3) {

        $sale_items = (array)$this->sales_model->getSaleItemsByID($id);
        $customer_det = (array)$this->pos_model->getCustomerByID($data_inv['customer_id']);
      //***************************TRJ088 - KENY PONTE -04/09/2019*************************************

        $p=$this->site->getPDFformat();
        $pdf=$p->pdf_format;
      //***************************TRJ088 - KENY PONTE -04/09/2019*************************************

        require("funciones.php");

        //PDF
        include("app/class/QRCodeGenerator.class.php");//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
       
        if($pdf==1){//***************************TRJ088 - KENY PONTE -04/09/2019*************************************
          if ($this->generar_pdf($id, $data_inv, $sale_items, $customer_det)) {

            $this->session->set_flashdata('message', lang("pdf_recreated"));
            redirect('sales');
          } else {
            $this->session->set_flashdata('error', lang("no_ticket"));
            redirect('sales');
          }
        }
        
        //***************************TRJ088 - KENY PONTE -04/09/2019*************************************
        else if ($pdf==2){
           
          if ($this->generar_pdf1($id, $data_inv, $sale_items, $customer_det)) {
            $this->session->set_flashdata('message', lang("pdf_recreated"));
            redirect('sales');
          }
          } else {
            $this->session->set_flashdata('error', lang("no_ticket"));
            redirect('sales');
          }
        }
        //***************************TRJ088 - KENY PONTE -04/09/2019*************************************
      } else {
         $this->session->set_flashdata('error', lang("not_saled"));
         redirect('sales');
      }

      $this->session->set_flashdata('error', lang("something_wrong"));
      redirect('sales');
    }
  /******************************TRJ088 - KENY PONTE - 03/09/2019*************** */
  function generar_pdf1($sale_id, $inv, $items, $customer_det){
    // $inv = $this->pos_model->getSaleByID($sale_id);

    //Idioma ---------------
    $region = $this->session->userdata('region');
    $l_ruc = lang("ccf2");
    $l_dni = lang("ccf1");
    $l_address = lang("scf2");
    $arrMes = lang("months");
    $l_qty = lang("qty");
    $op_ICBPER=lang('product_tax_ICBPER').' ('.lang('tax_ICBPER').')';//***************TRJ068 - ALEXANDER ROCA - 13/08/2019********************
    $l_description = lang("description");
    $l_importe = lang("importe");
    $l_currency = lang("currency_name");
    $op_exo = lang("op_exonerated");
    $op_exoReal=lang("op_exo");//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
    //----------------------
    $tipo_doc = "";
    $impRuc = "";
    if ($inv['document_type'] == 1) {
      $tipo_doc = lang('bill_title');
    } else {
      $tipo_doc = lang('invoice_title');
    }

    $numComp = $inv['invoice_id'];

    $symbol = "";
    $currency = "";
    if ($inv['currency'] == 'PEN') {
            // $symbol = "S/ ";
      $currency = strtoupper(lang("currency_pen"));
    } else if ($inv['currency'] == 'USD') {
            // $symbol = "$ ";
      $currency = lang("currency_USD");
    }

    $arrD1 = explode(" ", $inv['date']);
    $arrD2 = explode("-", $arrD1[0]);
    $ano = $arrD2[0];
    $dia = $arrD2[2];
    $mes = $arrMes[(int)$arrD2[1] - 1];
    $fecha_emision=$arrD1[0];//********************************TRJ088 - KENY PONTE 02/09/2019******************* */
    $hora_emision=$arrD1[1];//********************************TRJ088 - KENY PONTE 02/09/2019******************* */
    $row_items_html = "";  
//----------------------------------------------------------------
    $fecha = new DateTime($fecha_emision);
    $fecha1 = new DateTime($fecha_emision);
    $fecha->modify('first day of this month'); //muestra el primer dia del mes
    $fecha1->modify('last day of this month');//muestra el ultimo dia del mes
    $fecha_inicio=$fecha->format('Y-m-d'); // 
    $fecha_fin=$fecha1->format('Y-m-d');
  $quantity1="";
  $codigo="";
  $unidad_medida="";
  $nombreProducto1="";
  $uprice1="";
  $subt1="";
//----------------------------------------------------------------
    foreach ($items as $item) {
        $quantity = $this->tec->formatNumber($item['quantity']);
        $uprice = $this->tec->formatMoney($item['unit_price']);
        $subt = $this->tec->formatMoney($item['subtotal']);
        //***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************
        $nombreProducto=str_replace("<","&lt;",$item['name']);
        $nombreProducto=str_replace(">","&gt;",$nombreProducto);
        /*******************************TRJ088 - KENY PONTE -************************************************* */
        $code=$item['code'];
        $row=$this->site->getUMProducto($item['product_id'],$code);
        $presentation=$row->presentation;
        $quantity1.=$quantity.'<br><br>';//acumulador de la lista de cantidades
        $codigo.=$code.'<br><br>';//acumulador de la lista de codigos de los productos
        $unidad_medida.=$presentation.'<br><br>';// unidad medida
        $nombreProducto1.=$nombreProducto.'<br><br>';//nombre del producto
        $uprice1.=$uprice.'<br><br>';//precio unitario
        $subt1.=$subt.'<br><br>';//sub total                 
    }
            
    $row_items_html='    
        <td valign="top"align="center" HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$quantity1.'</td>
        <td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$codigo.'</td>
        <td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$unidad_medida.'</td>
        <td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$nombreProducto1.'</td>
        <td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$uprice1.'</td>
        <td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$subt1.'</td>';
        /*******************TRJ088 - KENY PONTE -**********************/

    $customer_name = $customer_det['name'];
    $customer_address = $customer_det['direccion'];

    if ($inv['document_type'] == 2) {
      $tipo="RAZON SOCIAL";
      $emision="REPRESENTACIÓN IMPRESA DE LA FACTURA ELECTRÓNICA";
      if($customer_det['cf1'] != null){
         $tipo='SEÑOR';
      }
            /**************************TRJ088 - KENY PONTE - *********************************/  
        $impRuc='<br>
              <table border="1"width="100%" cellspacing="0" cellpadding="2" >
                <tr>
                  <td>
                    <table>
                      <tr>
                        <td  style="font-size:8px;"width="20%">'.$tipo.'</td>
                        <td  style="font-size:8px;"width="60%">'.$customer_name.'</td>
                        <td rowspan="2" valign="top" align="right"style="font-size:8px;"width="20%">'.$l_ruc.' : '.$customer_det['cf2'] . '</td>
                      </tr>
                      <tr>
                        <td align="left" style="font-size:8px;">'.$l_address.'</td>
                        <td  style="font-size:8px;">'.$customer_address.'</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            <br>';
            /**********************************TRJ088 - KENY PONTE -*************************************** */
            } else {
              $emision="REPRESENTACIÓN IMPRESA DE LA BOLETA ELECTRÓNICA";

        // $impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" >
        //             <tr>
        //                 <td align="left"  style="font-size:10px">' . $region . ', <u> '.$dia.' </u> de <u> '.$mes.' </u> del <u> '.$ano.' </u><br></td>
        //             </tr>
        //         </table>';
  
        $doc = $l_dni;
        $nro_doc = $customer_det['cf1'];
        $tipo="SEÑOR(A)";
        if($customer_det['cf1'] == null && $customer_det['cf2'] != null){
          $doc = $l_ruc;
          $nro_doc = $customer_det['cf2'];
          $tipo="RAZON SOCIAL";
        }
  //***************TRJ043 - ALEXANDER ROCA - 30/04/2019**************
  /*************************************TRJ088 - KENY PONTE -************************************************** */
        $impRuc = '<br>
              <table border="1"width="100%" rules="none" cellspacing="0" cellpadding="2" >
                <br>
                  <td>
                    <table>
                      <tr>
                        <td  style="font-size:8px;" width="20%">'.$tipo.'</td>
                        <td  style="font-size:8px;"width="60%">'.$customer_name.'</td>
                        <td rowspan="2" valign="top" style="font-size:8px;" width="20%">'.$doc.':'.$nro_doc.'</td>
                      </tr>
                      <tr>
                        <td align="left" style="font-size:8px;">'.$l_address.'</td>
                        <td  style="font-size:8px;">'.$customer_address.'</td>
                      </tr>
                    </table>
                  </td>
                </tr>   
              </table>
              <br>';
  //*****************************************TRJ088 - KENY PONTE -************************************************************** */           
  //***************TRJ043 - ALEXANDER ROCA - 30/04/2019**************
        // <tr>
        //     <td align="left" width="20%"  style="font-size:10px"> ' . $l_address . ': </td>
        //     <td align="left" width="80%"  style="font-size:10px"> ' . $customer_address . '</td>
        // </tr>
      }
      $sub = $this->tec->formatMoney($inv['subtotal']);
      $desc = $this->tec->formatMoney($inv['order_discount']); //(-$inv['rounding']) +
      $afc = $this->tec->formatMoney($inv['affected']);
      $exo = $this->tec->formatMoney($inv['non_affected']);
     $tax_ICBPER = $this->tec->formatMoney($inv['tax_ICBPER']);//***************TRJ068 - ALEXANDER ROCA - 13/08/2019********************
      $exoReal = $this->tec->formatMoney($inv['exonerated']);//****************TRJ008 - ALEXANDER ROCA  - 11/04/2019**********
      $igv = $this->tec->formatMoney($inv['total_tax']);
      $grat=$this->tec->formatMoney(0);
      $tot1 = $this->tec->formatMoney($inv['amount']);
      $round = $this->tec->formatMoney($inv['rounding']);
      $tot2 = $this->tec->formatMoney($inv['grand_total']);
      $totLetras = strtoupper(numtoletras($inv['grand_total']) . " " . $currency);

    $this->load->library('Pdf');

    $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetTitle($tipo_doc . " - " . $numComp);
    $pdf->SetHeaderMargin(30);
    $pdf->SetTopMargin(20);
    $pdf->setFooterMargin(20);
    $pdf->SetAutoPageBreak(true);
    $pdf->SetAuthor('Author');
    $pdf->SetDisplayMode('real', 'default');

    $pdf->AddPage();

    $ivPorc = $this->Settings->default_tax_rate;
    $nombreCorto = strtoupper($this->Settings->site_name);

    $razon_social = $this->session->userdata('razon_social');
    $ruc = $this->session->userdata('ruc');
    $direccion = $this->session->userdata('direccion');
    $region = $this->session->userdata('region');
    $pais = $this->session->userdata('pais');
    $region_pais = strtoupper($region . " - " . $pais);
    $imagen = $this->session->userdata('logo');
    $pos_logo = $this->session->userdata('pos_logo');
//****************TRJ030 - RENATO GAMONAL*******************
    $imagen2 = $this->session->userdata('logo_pdf');
    $type_imagen_pdf = $this->session->userdata('type_imagen_pdf');
//****************TRJ030 - RENATO GAMONAL*******************

    $width = 334;
    $height = 102;

    if ($pos_logo == 2) {
      $width = 334;
      $height = 102;
    } else if ($pos_logo == 1) {
      $width = 84;
      $height = 102;
    }

    $_ruc = $l_ruc . ". " . $ruc;

    //-----------------------------------QR-------------------------------------------//
    $serieNumero = explode('-', $numComp);
    $serie = $serieNumero[0];
    $numero = $serieNumero[1];
    $date = $arrD1[0];
    $hash = $inv['digest_value'];

    if ($inv['document_type'] == 2) {
      $cusDoc = "06";
      $docType = "01";
      $numDoc = $customer_det->cf2;
      $qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
    } else {
      $cusDoc = "01";
      $docType = "03";
      $numDoc = $customer_det->cf1;
      $qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
    }
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
    $ex1 = new QRCodeGenerator();
    $qr_img = '<img src="' . $ex1->generate($serieNumero[0].$serieNumero[1],$qr) . '" width="80" height="80">';
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
    //------------------------------------------------------------------------------//
//****************TRJ030 - RENATO GAMONAL*******************
  if ($type_imagen_pdf == 1){ 
    $cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen) . '" width="' . $width . '" height="' . $height . '">';
    $cabecera2 = '<div><br>' . $razon_social . '<br>' . $direccion . '<br>' . $region_pais . '<br>' . $_ruc.'</div>';
           } 
  else if ($type_imagen_pdf == 2) {
    $width2 = 370;
    $height2 = 150;
    $cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen2) . '" width="' . $width2 . '" height="' . $height2 . '">';
    $cabecera2 ="";
    }
//****************TRJ030 - RENATO GAMONAL*******************  
//****************TRJ009 - ALEXANDER ROCA - 19/03/2019 *******************
//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
//**********************************TRJ088 - KENY PONTE -****************************************** */
$u=$this->site->getURLconsulta();
$url=$u->footer;
//$url=strip_tags($foot,"<br>");
if($tax_ICBPER>0){
  $rowspan=9;
  $bol_plasticas='<tr>
  <td colspan="1"  align="left" style="font-size:7px;border: 1px solid #000000">'.$op_ICBPER.'</td>
  <td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">'.$tax_ICBPER.'</td>
</tr> ';

}
else if($tax_ICBPER<=0){
  $rowspan=8;
  $bol_plasticas='';

}

$tbl1 = <<<EOD
  <table  width="100%" cellspacing="0" cellpadding="2" >
    <tr>
      <td width="70%" align="center" style="font-size:10px">
      $cabecera
      </td>
      <td width="30%" valign="middle"><br><br><br>
        <table border="0.5" width="100%" cellspacing="0" cellpadding="8" >
          <tr>
            <td align="center"  style="font-size:10px"><h2 style="font-size:8px">$_ruc</h2></td>
          </tr>
          <tr>
            <td align="center" bgcolor="#E4E4E4" style="font-size:10px"><h2 style="font-size:8px">$tipo_doc</h2></td>
          </tr>
          <tr>
            <td align="center" style="font-size:10px"><h2 style="font-size:8px">$numComp</h2></td>
          </tr>
        </table><br>
      </td>
    </tr>
  </table>
  <br>
  <table border="1" width="100%" cellspacing="0" cellpadding="" >
    <tr>
      <td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">FECHA DE EMISION</strong></td>
      <td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">HORA DE EMISION</strong></td>
      <td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">FECHA DE VENCIMIENTO</strong></td>
      <td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">FECHA DE INICIO DE PERIODO</strong></td>
      <td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">FECHA DE FIN DE PERIODO</strong></td>
    </tr>

    <tr>
      <td align="center" style="font-size:8px">$fecha_emision</td>
      <td align="center" style="font-size:8px">$hora_emision</td>
      <td align="center" style="font-size:8px"></td>
      <td align="center" style="font-size:8px">$fecha_inicio</td>
      <td align="center" style="font-size:8px">$fecha_fin</td>
    </tr>

  </table><br>

  $impRuc
  
  <br>
  
  <table width="100%" cellspacing="0" >
    <tr height="30">
      <th bgcolor="#1B3752" align="center" width="8%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CANTIDAD</th>
      <th bgcolor="#1B3752" align="center" width="11%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CODIGO</th>
      <th bgcolor="#1B3752" align="center" width="11.5%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">UNIDAD MEDIDA</th>
      <th bgcolor="#1B3752" align="center" width="51%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">DESCRIPCION</th>
      <th bgcolor="#1B3752" align="center" width="9.5%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">PRECIO UNITARIO</th>
      <th bgcolor="#1B3752" align="center" width="9%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">IMPORTE</th>
    </tr>
    <tr>
    $row_items_html
    </tr>
    <tr>
      <td colspan="4" rowspan="$rowspan" style="font-size:9px;"><br>SON:<br> $totLetras</td>
      <td colspan="1" align="left" style="font-size:7px;border: 1px solid #000000">Sub Total</td>
      <td colspan="1" align="right"  style="font-size:8px;border: 1px solid #000000">$sub</td>
    </tr>
    <tr>
      <td colspan="1" align="left"  style="font-size:7px;border: 1px solid #000000">op.gravada</td>
      <td colspan="1" align="right"  style="font-size:8px;border: 1px solid #000000">$afc</td>
    </tr>
    <tr>
      <td colspan="1" align="left"  style="font-size:7px;border: 1px solid #000000">$op_exo</td>
      <td colspan="1" align="right"  style="font-size:8px;border: 1px solid #000000">$exo</td>
    </tr>
    <tr>
      <td colspan="1" align="left" style="font-size:7px;border: 1px solid #000000">$op_exoReal</td>
      <td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">$exoReal</td>
    </tr>
    <tr>
      <td colspan="1"  align="left" style="font-size:7px;border: 1px solid #000000">op.gratuita</td>
      <td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">$grat</td>
    </tr>
    <tr>
      <td colspan="1"  align="left" style="font-size:7px;border: 1px solid #000000">I.G.V-$ivPorc %</td>
      <td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">$igv</td>
    </tr>
   $bol_plasticas
    <tr>
      <td colspan="1"  align="left" style="font-size:7px;border: 1px solid #000000">importe total</td>
      <td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">$tot2</td>
    </tr>
  </table>    
  <br><br>  
  <table  width="100%">
    <tr style="font-size:10px">
      <td align="left" width="20%">$qr_img</td>                     
      <td  width="62%" style="font-size:7px;vertical-align: bottom">$emision $url</td>
    </tr>
  </table>
  <br>
EOD;
/*******************************TRJ088 - KENY PONTE -//************************************************ */
//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
//****************TRJ009 - ALEXANDER ROCA - 19/03/2019 *******************
$pdf->writeHTML($tbl1, true, false, false, false, '');

if(!$file = $this->sales_model->getFileBySaleID($sale_id)){

  if ($inv['document_type'] == 1) {
    $docName = 'boletas';
  } else {
    $docName = 'facturas';
  }

  $nombPDF = randomstring(20);

  //GRABA EN TABLA HISTORIAL
  $rutaTabla = array(
    'sale_id' => $sale_id,
    'file_name' => $docName . '/' . $nombPDF . '.pdf',
  );

  $this->db->insert('sales_files', $rutaTabla);

  //GUARDA EL PDF FISICO
  $pdf->Output(FCPATH . 'uploads/pdf/' . $docName . '/' . $nombPDF . '.pdf', 'F');

  return true;

} else {

  $this->db->update('sales_files', ['file_name' => $file->file_name], ['sale_id' => $sale_id]);

  //GUARDA EL PDF FISICO
  $pdf->Output(FCPATH . 'uploads/pdf/' . $file->file_name, 'F');

  return true;

}

return false;
}

  /*******************************TRJ088 - KENY PONTE 03/09/2019******************* */

  function resend_xml($id = null)
  {

    $data_inv = (array)$this->pos_model->getSaleByID($id);

    if ($data_inv['invoice_id'] != null) {

      if ($data_inv['document_type'] != 3) {

        //Generar XML
        $sale_items = (array)$this->sales_model->getSaleItemsByID($id);
        $customer_det = (array)$this->pos_model->getCustomerByID($data_inv['customer_id']);
        require("funciones.php");
        if ($respuesta = $this->generar_xml($data_inv['invoice_id'], $data_inv, $sale_items, $customer_det)) {

          $res = array(
            'flg_response' => $respuesta['flg_response'],
            'error_code' => $respuesta['error_code'],
            'response_descrip' => $respuesta['response_descrip'],
            'digest_value' => $respuesta['digest_value']
          );

          if ($this->pos_model->updateSaleCab($id, $res)) {
            $this->session->set_flashdata('message', lang("invoice_resend"));
            redirect('sales');
          }
        }
      } else {
        $this->session->set_flashdata('error', lang("no_ticket"));
        redirect('sales');
      }
    } else {
      $this->session->set_flashdata('error', lang("not_saled"));
      redirect('sales');
    }

    $this->session->set_flashdata('error', lang("something_wrong"));
    redirect('sales');
  }

//   function delete($id = null)
//   {
//     if (DEMO) {
//       $this->session->set_flashdata('error', lang('disabled_in_demo'));
//       redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
//     }

//     if ($this->input->get('id')) {
//       $id = $this->input->get('id');
//     }

//     if (!$this->Admin) {
//       $this->session->set_flashdata('error', lang("access_denied"));
//       redirect('sales');
//     }

//     //REGISTRA EN TABLA MOVIMIENTOS
//     $sale = $this->sales_model->getSaleByID($id);
//     $sale_items = $this->sales_model->getSaleItemsByID($id);

//     foreach ($sale_items as $sale_item) {

//       if ($sale_item->product_id != null) {

//         $rowMot = $this->site->getMotiveByAlias('IAV');
//         $idMotivo = 0;
//         if ($rowMot) {
//           $idMotivo = $rowMot->id;
//         }
//         $dataM = array(
//           'origin_id' => $sale->id,
//           'warehouse_id' => $this->Settings->default_warehouse,
//           'product_id' => $sale_item->product_id,
//           'cant' => $sale_item->quantity,
//           'tipo' => 'I',
//           'idMotivo' => $idMotivo,
//           'ref' => $sale->invoice_id,
//           'uCrea' => $this->session->userdata('user_id'),
//           'fCrea' => date("Y-m-d H:i:s"),
//           'estado' => 1
//         );
//         $this->mov_model->addMovement($dataM);
//       }
//     }

//     if ($this->sales_model->deleteInvoice($id)) {
//       $this->session->set_flashdata('message', lang("invoice_deleted"));
//       redirect('sales');
//     }
//   }

  function anular($id = null)
  {
    if (DEMO) {
      $this->session->set_flashdata('error', lang('disabled_in_demo'));
      redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
    }

    if ($this->input->get('id')) {
      $id = $this->input->get('id');
    }

    if (!$this->Admin) {
      $this->session->set_flashdata('error', lang("access_denied"));
      redirect('sales');
    }

    //REGISTRA EN TABLA MOVIMIENTOS
    $sale = $this->sales_model->getSaleByID($id);
    $sale_items = $this->sales_model->getSaleItemsByID($id);

    if ($sale->invoice_id != null) { //$sale->status != "DEBE"

        $warehouse_id = $sale->warehouse_id;


      foreach ($sale_items as $sale_item) {
    //***********************TRJ035 - ALEXANDER ROCA - 15/04/2019 ************
        if ($sale_item['product_id'] != null) {
      
          $dataM = array(
            'origin_id' => $sale->id,
            'local_id' => $this->session->userdata('local'),
            'warehouse_id' => $warehouse_id, //$this->Settings->default_warehouse,
            'product_id' => $sale_item['product_id'],
            'cant' => $sale_item['quantity'],
            'tipo' => 'I',
            'idMotivo' => 3, //Ingreso por anulación de venta
            'ref' => $sale->invoice_id,
            'uCrea' => $this->session->userdata('user_id'),
            'fCrea' => date("Y-m-d H:i:s"),
            'estado' => 1
          );

          $this->mov_model->addMovement($dataM);

          //ACTUALIZAR STOCK A ALMACEN POR DEFECTO
          $q = $this->db->get_where('tec_warehouse_stock', array('warehouse_id' => $warehouse_id, 'product_id' => $sale_item['product_id']), 1); //$this->Settings->default_warehouse
      
          if ($q->num_rows() > 0) {
            //return
            $fila1 = $q->row();
            $data1 = array(
              'stock' => $fila1->stock + $sale_item['quantity']
            );
            $this->db->update('tec_warehouse_stock', $data1, array('warehouse_id' => $warehouse_id, 'product_id' => $sale_item['product_id'])); //$this->Settings->default_warehouse
          } else {
            $data1 = array(
              'warehouse_id' => $warehouse_id, //$this->Settings->default_warehouse
              'product_id' => $sale_item['product_id'],
              'stock' => $fila1->stock + $sale_item['quantity']
            );
            $this->db->insert('tec_warehouse_stock', $data1);
          }
          /////////FIN DE ACTUALIZAR STOCK A ALMACEN POR DEFECTO
        }

      }
    //***********************TRJ035 - ALEXANDER ROCA - 15/04/2019 ************
    }

    $dataU->estado = 0;
    $dataU->updated_by = $this->session->userdata('user_id');
    $dataU->updated_at = date("Y-m-d H:i:s");


    if ($this->pos_model->updateSaleCab($id, $dataU)) {
      $this->session->set_flashdata('message', lang("invoice_disabled"));
      redirect('sales');
    }

  }

  function delete_holded($id = null)
  {

    if ($this->input->get('id')) {
      $id = $this->input->get('id');
    }

    if (!$this->Admin) {
      $this->session->set_flashdata('error', lang("access_denied"));
      redirect('sales/opened');
    }

    if ($this->sales_model->deleteOpenedSale($id)) {
      $this->session->set_flashdata('message', lang("opened_bill_deleted"));
      redirect('sales/opened');
    }

  }

  /* -------------------------------------------------------------------------------- */

  function payments($id = null)
  {
    $this->data['payments'] = $this->sales_model->getSalePayments($id);
    $this->load->view($this->theme . 'sales/payments', $this->data);
  }

  function payment_note($id = null)
  {
    $payment = $this->sales_model->getPaymentByID($id);
    $inv = $this->sales_model->getSaleByID($payment->sale_id);
    $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
    $this->data['inv'] = $inv;
    $this->data['payment'] = $payment;
    $this->data['page_title'] = $this->lang->line("payment_note");

    $this->load->view($this->theme . 'sales/payment_note', $this->data);
  }

  function add_payment($id = null, $cid = null)
  {

    // $this->load->library('Pdf');

    $this->load->helper('security');
    if ($this->input->get('id')) {
      $id = $this->input->get('id');
    }

    $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
    $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
    $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');

    if ($this->input->post('invoice') == null) { //$this->input->post('status')=='DEBE' &&
      $this->form_validation->set_rules('document_type', lang("document_type"), 'required');
    }

    if ($this->form_validation->run() == true) {

      if ($this->Admin) {
        $date = $this->input->post('date');
      } else {
        $date = date('Y-m-d H:i:s');
      }

      if ($this->input->post('invoice') == null) { //$this->input->post('status')=='DEBE' &&
        //VALIDACIONES
        // $sales = $this->sales_model->getSaleByID($this->input->post('sale_id'));
        $customer_det = (array)$this->pos_model->getCustomerByID($cid);

        $paso = 1;
        $doc_type = $this->input->post('document_type');
        $err_nopaso = "";
    



    
        if ($paso == 0) {
          echo json_encode(array('status' => 'failed', 'msg' => $err_nopaso));
          die();
          /*$this->data['error'] = $err_nopaso;
                    $this->session->set_flashdata('error', $err_nopaso);
                    redirect($_SERVER["HTTP_REFERER"]);*/

        } else {

          $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
          // echo json_encode(array('status' => 'failed', 'msg' => validation_errors())); die();
        }

        // $amount = $this->tec->formatDecimal($paid > $grand_total ? ($paid - $this->input->post('balance_amount')) : $paid);
        // $payment['pos_paid'] = $this->tec->formatDecimal($this->input->post('amount'));
        // $payment['pos_balance'] = $this->tec->formatDecimal($this->input->post('balance_amount'));

      }

      $payment = array(
        'date' => $date,
        'sale_id' => $id,
        'customer_id' => $cid,
        'reference' => $this->input->post('reference'),
        'amount' => $this->input->post('amount-paid'),
        'currency' => $this->Settings->currency_prefix,
        'paid_by' => $this->input->post('paid_by'),
        'cheque_no' => $this->input->post('cheque_no'),
        'gc_no' => $this->input->post('gift_card_no'),
        'cc_no' => $this->input->post('pcc_no'),
        'cc_holder' => $this->input->post('pcc_holder'),
        'cc_month' => $this->input->post('pcc_month'),
        'cc_year' => $this->input->post('pcc_year'),
        'cc_type' => $this->input->post('pcc_type'),
        'note' => $this->input->post('note'),
        'created_by' => $this->session->userdata('user_id'),
      );

      if ($_FILES['userfile']['size'] > 0) {
        $this->load->library('upload');
        $config['upload_path'] = 'files/';
        $config['allowed_types'] = $this->digital_file_types;
        $config['max_size'] = 2048;
        $config['overwrite'] = false;
        $config['encrypt_name'] = true;
        $this->upload->initialize($config);
        if (!$this->upload->do_upload()) {
          $error = $this->upload->display_errors();
          $this->session->set_flashdata('error', $error);
          //redirect($_SERVER["HTTP_REFERER"]);
          echo json_encode(array('status' => 'success', 'msg' => "Ok", 'redirect' => 1));
          exit();
        }
        $photo = $this->upload->file_name;
        $payment['attachment'] = $photo;
      }

      // $this->sma->print_arrays($payment);

    } elseif ($this->input->post('add_payment')) {
      $this->session->set_flashdata('error', validation_errors());
      $this->tec->dd();
    }

    //Pago en Caja
    if ($this->form_validation->run() == true) {

      if ($this->input->post('invoice') == null) { //$this->input->post('status')=='DEBE' &&

        // $start = microtime(true);
        $document_type = (int)$this->input->post('document_type');
        $note = $this->tec->clear_tags($this->input->post('spos_note'));

        //Asignar Nro Comprobante
        if ($document_type == 2) {
          $new_number = (int)$this->Settings->invoice_number + 1;
          $format = $this->Settings->invoice_format;
                } else if ($document_type == 1) {
          $new_number = (int)$this->Settings->bill_number + 1;
          $format = $this->Settings->bill_format;
                } else if ($document_type == 3) {
          $new_number = (int)$this->Settings->nventa_number + 1;//*****Renato TRJ023 25/04/2019   ********
          $format = $this->Settings->nventa_format; //*****Renato TRJ023 25/04/2019   ********
        }
    
        $parte1 = explode('{', $format);
        $parte2 = explode('}', $parte1[1]);
        $parentesis = $parte2[0];
    
        $buscar = strpos($parentesis, '+');
    
        if ($buscar) {
          $numero = explode('+', $parentesis);
          $new_format = str_pad((string)$new_number, strlen($numero[0]), "0", STR_PAD_LEFT);
        } else {
          $new_format = str_pad((string)$new_number, strlen($parentesis), "0", STR_PAD_LEFT);
        }
    
    
        $serieNumero = $parte1[0] . $new_format;
    

                if ($this->pos_model->getInvoiceId($serieNumero)){
                    echo json_encode(array('status' => 'failed', 'msg' => lang("the_invoice") . $serieNumero . lang("not_unique")));
                    exit();
                }
      
        //Actualiza Nro Comprobante (Settings)
    //$this->pos_model->update_invoice_Number($document_type);
        if($this->pos_model->update_invoice_Number($document_type)){
    
      if ($document_type != 3) {

        //Generar XML
        $data_inv = (array)$this->pos_model->getSaleByID($id);
        $data_inv['document_type'] = $document_type;
        $sale_items = (array)$this->sales_model->getSaleItemsByID($id);
        
        // $customer_det = (array)$this->pos_model->getCustomerByID($data_inv['customer_id']);
              require("funciones.php");
        if ($respuesta = $this->generar_xml($serieNumero, $data_inv, $sale_items, $customer_det)) {
        $flg_response = $respuesta['flg_response'];
        $error_code = $respuesta['error_code'];
        $response_descrip = $respuesta['response_descrip'];
        $digest_value = $respuesta['digest_value'];
        // $this->pos_model->send_bill($id);
        }
        
        
        
      }
    }
    
        //Grabar Comprobante
        $datasales = array(
          'document_type' => $document_type,
          'note' => $note,
          'invoice_id' => $serieNumero,
          'flg_response' => $flg_response,
          'error_code' => $error_code,
          'response_descrip' => $response_descrip,
          'digest_value' => $digest_value,
          //'cmp' => $this->input->post('cmp'),
          //'doctor' => $this->input->post('doctor')
        );

      }
    }

    if ($this->form_validation->run() == true && $this->sales_model->addPayment($payment)) {

      $msg = lang("payment_added");
      $this->session->set_flashdata('message', $msg);

      if ($this->input->post('status') == 'DEBE') {

        if ($this->input->post('invoice') == null) {
      
          if ($this->pos_model->updateSaleCab($id, $datasales)) {
        
            //REGISTRA EN TABLA MOVIMIENTOS
            foreach ($sale_items as $sale_item) {

              // $sale_item = (array)$sale_item;
        //***********************TRJ017 - ALEXANDER ROCA - 26/03/2019 ************
              if ($sale_item['product_id'] != null) { //$sale_item['product_id']
        //***********************TRJ017 - ALEXANDER ROCA - 26/03/2019 ************
                $dataM = array(
                  'origin_id' => $id,
                  'local_id' => $this->session->userdata('local'),
                  'warehouse_id' => $this->Settings->default_warehouse,
                  'product_id' => $sale_item['product_id'],//$sale_item->product_id, 
                  'cant' => $sale_item['quantity'],//$sale_item->quantity, //$sale_item['quantity'],
                  'tipo' => 'S',
                  'idMotivo' => 5, //Salida por venta
                  'ref' => $serieNumero,
                  'uCrea' => $this->session->userdata('user_id'),
                  'fCrea' => date("Y-m-d H:i:s"),
                  'estado' => 1
                );
        
        
                $this->mov_model->addMovement($dataM);
                $this->pos_model->updateItemStock($sale_item);
        
        
              }
            }
            // $this->pos_model->updateStock($id);
            /************************TRJ088 - KENY PONTE - 02/09/2019********************* */
            $p=$this->site->getPDFformat();
            $pdf=$p->pdf_format;
            
            /************************TRJ088 - KENY PONTE - 02/09/2019********************* */
            if ($document_type == 1) {

              //PDF
              include("app/class/QRCodeGenerator.class.php");//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
              //*****************TRJ088 -KENY PONTE - 03/09/2019************* */
              if($pdf==1){
                $this->generar_pdf($id, $data_inv, $sale_items, $customer_det);
              }else if($pdf==2){
                $this->generar_pdf1($id, $data_inv, $sale_items, $customer_det);
              }
              //*******************TRJ088 - KENY PONTE 03/09/2019*************** */
            } else if ($document_type == 2) {

              //Enviar Comprobante
              $this->enviar_comprobante($serieNumero, $id, null, "pago", $customer_det);//***********TRJ016 - ALEXANDER ROCA - 29/03/2019************

              //PDF
              include("app/class/QRCodeGenerator.class.php");//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
              /***********************************TRJ088 - KENY PONTE - 03/09/2019****************** */
              if($pdf==1){
                $this->generar_pdf($id, $data_inv, $sale_items, $customer_det);
              }else if($pdf==2){
                $this->generar_pdf1($id, $data_inv, $sale_items, $customer_det);
              }
              /*******************************TRJ088 - KENY PONTE - 03/09/2019********************************** */
            }

            // $time_elapsed= microtime(true) - $start;
            // $this->session->set_flashdata('message', $time_elapsed);
            // echo json_encode(array('status' => 'success', 'msg' =>  "Ok",'redirect' =>  2,'idd' =>  $id ));
            // exit();

            //Redireccion
            echo json_encode(array('status' => 'success', 'msg' => "Ok", 'redirect' => 2, 'idd' => $id));
            exit();

          
          } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $sale = $this->sales_model->getSaleByID($id);
            $this->data['inv'] = $sale;

            $this->load->view($this->theme . 'sales/add_payment', $this->data);
          }
        } else {
          //Redireccion
          echo json_encode(array('status' => 'success', 'msg' => "Ok", 'redirect' => 1));
          // echo json_encode(array('status' => 'success', 'msg' =>  "Ok",'redirect' =>  2,'idd' =>  $id ));
          exit();
        }

      } else if ($this->input->post('status') == 'Pagado') {
        echo json_encode(array('status' => 'success', 'msg' => "Ok", 'redirect' => 1));
        exit();
      } else if ($this->input->post('status') == 'Parcial') {
        echo json_encode(array('status' => 'success', 'msg' => "Ok", 'redirect' => 1));
        exit();
      }

    } else {

      $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
      $sale = $this->sales_model->getSaleByID($id);
      $this->data['inv'] = $sale;

      $this->load->view($this->theme . 'sales/add_payment', $this->data);
    }
  }

  function generar_xml($serieNumero, $data, $items, $customer_det)
  {

    if ($data['document_type'] == 2) {

      $invoiceTypeCode = "01";
      $documentID = $customer_det['cf2'];
      $document = "6";

    } else if ($data['document_type'] == 1) {

      $invoiceTypeCode = "03";
     // $document = "1";
      $documentID = $customer_det['cf1'];
      //**************************TRJ073 - KENY PONTE - 13/09/2019********************* */
      $doc_type=$this->sales_model->getDocument($documentID);
      $document=(string)$doc_type->document_type_id;
      //**************************TRJ073 - KENY PONTE - 13/09/2019********************* */

      // Si el cliente es una empresa
      if($customer_det['cf1'] == null && $customer_det['cf2'] != null){
        $document = "6";
        $documentID = $customer_det['cf2'];
      }

      if ($documentID == "" | $documentID == null) {
        $documentID = "-";
      }

    }

    if ($this->session->userdata('beta') == 0) {
      $urlDoc = "post/xml";
    } else {
      $urlDoc = "beta/post/xml";
    }

    $Date = explode(' ', $data['date'], 2);

    $supplierID = $this->session->userdata('ruc');

    $passFirma = $this->session->userdata('pass_firma');
    // $usuSol = $this->session->userdata('user_sol');
    // $passSol = $this->session->userdata('pass_sol');
  //******************************TRJ033 - ALEXANDER ROCA - 08/04/2019**********************
    $currency_prefix = (string)$data['currency'];//$this->Settings->currency_prefix;
  //******************************TRJ033 - ALEXANDER ROCA - 08/04/2019**********************
    $supplierName = $this->session->userdata('razon_social');
    $signatureID = $serieNumero;
    $URI = "#" . $signatureID;
    $supplierDocumentID = "6";
    //$localID = "0001";
  //******************************TRJ027 - ALEXANDER ROCA - 03/04/2019**********************
  //Obtener el Local
  $local_id = $this->session->userdata('local');
  $local = $this->site->getLocalByID($local_id);

  //Codigo de local
  $localID = $local->cod_sunat; //"0001";
  //******************************TRJ027 - ALEXANDER ROCA - 03/04/2019**********************
    $header = array(
      "supplierID" => $supplierID,
      "invoiceTypeCode" => $invoiceTypeCode,
      "serieNumero" => $serieNumero,
      "passFirma" => $passFirma,
      "issueDate" => $Date[0],
      "issueTime" => $Date[1],
      "dueDate" => $Date[0],
      "note" => strtolower(numtoletras($data['amount'])),
      "documentCurrencyCode" => $currency_prefix,
      "lineCountNumeric" => (string)$data['total_items'],
      "signatureID" => $signatureID,
      "supplierName" => $supplierName,
      "URI" => $URI,
      "supplierDocumentID" => $supplierDocumentID,
      "addressTypeCode" => $localID,
      "customerDocumentID" => $document,
      "customerID" => $documentID,
      "customerName" => $data['customer_name'],
      "addressLine" => $customer_det['direccion'],
      "baseAmount" => (string)(double)($data['amount'] + $data['order_discount']), //(-$data['rounding']) +
      "taxAmount" => (string)$data['product_tax'],
      "taxableAmount" => (string)$data['affected'],//$data['total'],
      "taxSubtotal" => (string)$data['total_tax'],
      "payableAmount" => (string)$data['amount'],
      "allowanceTotalAmount" => (string)$data['order_discount'], //(double)((-$data['rounding']) +
      "non_affected" => (string)$data['non_affected'],
    "exonerated" => (string)$data['exonerated'],//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
    "tax_ICBPER" => (string)$data['tax_ICBPER'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
    "mult_ICBPER" => (string)$data['mult_ICBPER']//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
    );
  
  //******************************* TRJ057 - ALEXANDER ROCA - 20/06/2019 ******************
    $porcentaje_desc=round((((100/$header['baseAmount'])*$data['order_discount'])/100),4);
    
    $discountGlobal = array("discount_global" => (string)$porcentaje_desc);
  //******************************* TRJ057 - ALEXANDER ROCA - 20/06/2019 ******************

    $cant = 0;
    foreach ($items as $item) {
      //$item = (array)$item;

      // if ($item['product_id'] != NULL) {
      $product_details = (array)$this->site->getProductByID($item['product_id']);
      //     $name = $product_details['name'];
      //     $code = $product_details['code'];
      // }else{
      //     $name = $item['name'];
      //     $code = $item['code'];
      // }
//*************************************** TRJ021 - ALEXANDER ROCA - 27/03/2019 *******************************
//*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************
      $cant = $cant + 1;
    $sum_price=$item['affect_price']+$item['non_affected_price']+$item['exonerated_price'];
    
    if($item['tax_method'] ==0 || $item['tax_method'] ==1){
      $amount_applies_tax=$item['affect_price'];
    }else{
      if($item['tax_method'] ==3){
        $amount_applies_tax=$item['exonerated_price'];
      }else{
        $amount_applies_tax=$item['non_affected_price'];
      }
    }
    
      $itemArray[$cant] = array(
        "itemQuantity" => $item['quantity'],
        "itemLineExtensionAmount" => (string)($this->tec->formatDecimal($amount_applies_tax * $item['quantity'])),//$item['affect_price'] * $item['quantity'])),//(int)$item['quantity']),
        "itemPricingReference" => (string)$item['unit_price'],
        "itemPriceTypeCode" => "01",
        "itemTaxTotalTaxAmount" => (string)$item['item_tax'],
        "itemTaxSubtotalTaxableAmount" => (string)($this->tec->formatDecimal($amount_applies_tax * $item['quantity'])),//$item['affect_price'] * $item['quantity'])),//(int)$item['quantity']),
        "itemTaxSubtotalTaxAmount" => (string)$item['item_tax'],
        "itemPriceAmount" => (string)$sum_price,//$item['affect_price'],
        "itemTaxPercent" => (string)(double)$item['igv'],
        "itemDescription" => (string)$item['name'],//(string)$product_details['name'], //$item['name'],//$name,
        "itemSellersID" => (string)$product_details['code'], //$item['code']//$code
    //"exonerated" => (string)$item['exonerated_price'],//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
    "tax_method" => (string)$item['tax_method'],
    "tax_ICBPER" => (string)$item['tax_ICBPER'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
    "quantity_ICBPER" => (string)$item['quantity_ICBPER'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
      );
    }
//*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************
//*************************************** TRJ021 - ALEXANDER ROCA - 27/03/2019 *******************************
    //datos a enviar
    $postData["header"] = $header;
    $postData["item"] = $itemArray;
  $postData["discountGlobal"] = $discountGlobal;//******************************* TRJ057 - ALEXANDER ROCA - 20/06/2019 ******************

    // Create the context for the request
    $context = stream_context_create(array(
      'http' => array(
        'method' => 'POST',
        'header' => "Authorization: application/json\r\n" .
          "Content-Type: application/json\r\n",
        'content' => json_encode($postData)
      )
    ));

    // Send the request
    $url = $this->config->item('aws_url');
    $response = file_get_contents($url . $urlDoc, false, $context);

    if ($response) {
      $res = json_decode($response, true);

      $sale_data = array(
        'flg_response' => $res[0],
        'error_code' => $res[1],
        'response_descrip' => $res[2],
        'digest_value' => $res[4]
      );

      return $sale_data;

    } else {
      $sale_data = array(
        'flg_response' => "0",
        'error_code' => "0",
        'response_descrip' => "Error al conectarse con AWS",
        'digest_value' => ""
      );

      return $sale_data;
    }

    return false;

  }

function enviar_comprobante($serieNumero, $sale_id, $data=null, $identificador, $customer_det)
  {

    if ($this->session->userdata('beta') == 0) {
      $urlDoc = "post/envio";
    } else {
      $urlDoc = "beta/post/envio";
    }


    $supplierID = $this->session->userdata('ruc');
    $invoiceTypeCode = "01";

    $file_name = $supplierID . "-" . $invoiceTypeCode . "-" . $serieNumero;

    $usuSol = $this->session->userdata('user_sol');
    $passSol = $this->session->userdata('pass_sol');

    $postData = array(
      'ruc' => $supplierID,
      'typeCode' => $invoiceTypeCode,
      'serieNumero' => $serieNumero,
      'usuSol' => $usuSol,
      'passSol' => $passSol
    );

    // Create the context for the request
    $context = stream_context_create(array(
      'http' => array(
        'method' => 'POST',
        'header' => "Authorization: application/json\r\n" .
          "Content-Type: application/json\r\n",
        'content' => json_encode($postData)
      )
    ));

    // Send the request
    $url = $this->config->item('aws_url');

    $response = file_get_contents($url . $urlDoc, false, $context);

//-------------Envio Sunat---------------//
    if ($response) {
      $res = json_decode($response, true);
      $tipo = "RF";

      //$datos = explode('-', $serieNumero, 2);
      //$serie = $datos[0];
      //$sNumero = $datos[1];

      if ($res[1] != 0) {
        $status = 3;
      } else {
        if ($res[0] == 1) {
          $status = 4;
        } else {
          $status = 1;
        }
      }
    
      $send_date = date("Y-m-d");

      $reg_invoice = array(
        'issue_date' => $send_date,
        //'number' => $sNumero,
        'file_name' => $file_name,
        'response_descrip' => $res[3] . ": " . $res[2],
        'reference_date' => $send_date,
        'processed_date' => $send_date,
        'type' => $tipo,
        'flg_response' => $res[0],
        'error_code' => $res[1],
        'status' => $status,
        'uCrea' => $this->session->userdata('user_id'),
        'fCrea' => date('Y-m-d H:i:s'),
        'estado' => 1,
      );

    
    //***********TRJ016 - ALEXANDER ROCA - 29/03/2019************
    if($status != 4){

    $DniRuc=$customer_det["cf2"];
    $nameCliente=$customer_det["name"];
    
    if($identificador=="addSale"){
      $SerieYnumero= $data["invoice_id"];
      $fecha=$data["date"];
      $montoTotal=$data["grand_total"];
      $tipoMoneda=$data["currency"];
    }else{
      $datosVenta=(array)$this->pos_model->sale ($sale_id);
      $SerieYnumero= $datosVenta["invoice_id"];
      $fecha=$datosVenta["date"];
      $montoTotal=$datosVenta["grand_total"];
      $tipoMoneda=$datosVenta["currency"];
    }

    $receipt = "<html>Estimado Cliente: <br><br>Mediante la presente se informa que el siguiente comprobante no fue informado correctamente a SUNAT.<br><br>Tipo de documento &#09;&#09;&#09; : Factura Electronica<br>Serie y numero &#09;&#09;&#09;&#09; : " . $SerieYnumero . "<br>Nro RUC/DNI/CE del cliente &#09;&#09; : ".$DniRuc."<br>Razon social cliente &#09;&#09;&#09; : ".$nameCliente."<br>Fecha de emision &#09;&#09;&#09; : " . $fecha . "<br>Importe Total &#09;&#09;&#09;&#09; : " . $montoTotal ." ".$tipoMoneda. "<br>Motivo &#09;&#09;&#09;&#09;&#09; : (".$res[1].") No hubo respuesta del servicio de SUNAT<br><br><br>Sistema TPV<br><br><br>Nota:<br>*En este correo electronico hemos eliminado las tildes para evitar errores en su visualizacion.<br>*Este mensaje es automatico, por favor no responder.<br></html>";
    $subject = "Alerta SistemaTPV - ". $this->Settings->site_name .": Incidencia comprobante ".$SerieYnumero;
    $toUser=(array)$this->pos_model->user_mail ();//************TRJ050 RENATO GAMONAL //
    $toCopia="asysfull@gmail.com";
    $nombreEnvia="Sistema TPV";

    if ($this->tec->send_email($toUser["mail_incidents"], $subject, $receipt, null, $nombreEnvia, null, null, $toCopia , null, null, null )) {//************TRJ050 RENATO GAMONAL //
      //echo json_encode(array('msg' => lang("email_success")));
    } else {
      //echo json_encode(array('msg' => lang("email_failed")));
    }
  
    }
    //***********TRJ016 - ALEXANDER ROCA - 29/03/2019************

      $this->pos_model->addSendInvoice($reg_invoice, $sale_id);

      return true;
    }else{


      /*cuando no hay respuesta de java*/
    
      $status = 1;
       $send_date = date("Y-m-d");

      $reg_invoice = array(
        'issue_date' => $send_date,
        //'number' => $sNumero,
        'file_name' => $file_name,
        'response_descrip' => $res[3] . ": " . $res[2],
        'reference_date' => $send_date,
        'processed_date' => $send_date,
        'type' => $tipo,
        'flg_response' => $res[0],
        'error_code' => $res[1],
        'status' => $status,
        'uCrea' => $this->session->userdata('user_id'),
        'fCrea' => date('Y-m-d H:i:s'),
        'estado' => 1,
      );


        $datosVenta=(array)$this->pos_model->sale ($sale_id);
        $SerieYnumero= $datosVenta["invoice_id"];
        $fecha=$datosVenta["date"];
        $montoTotal=$datosVenta["grand_total"];
        $tipoMoneda=$datosVenta["currency"];
        
    $this->pos_model->addSendInvoice($reg_invoice, $sale_id);



    }

  }



  function edit_payment($id = null, $sid = null)
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
        'sale_id' => $sid,
        'reference' => $this->input->post('reference'),
        'amount' => $this->input->post('amount-paid'),
        'paid_by' => $this->input->post('paid_by'),
        'cheque_no' => $this->input->post('cheque_no'),
        'gc_no' => $this->input->post('gift_card_no'),
        'cc_no' => $this->input->post('pcc_no'),
        'cc_holder' => $this->input->post('pcc_holder'),
        'cc_month' => $this->input->post('pcc_month'),
        'cc_year' => $this->input->post('pcc_year'),
        'cc_type' => $this->input->post('pcc_type'),
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
        $config['overwrite'] = false;
        $config['encrypt_name'] = true;
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


    if ($this->form_validation->run() == true && $this->sales_model->updatePayment($id, $payment)) {
      $this->session->set_flashdata('message', lang("payment_updated"));
      redirect("sales");
    } else {

      $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
      $payment = $this->sales_model->getPaymentByID($id);
      if ($payment->paid_by != 'cash') {
        $this->session->set_flashdata('error', lang('only_cash_can_be_edited'));
        $this->tec->dd();
      }
      $this->data['payment'] = $payment;
      $this->load->view($this->theme . 'sales/edit_payment', $this->data);
    }
  }

  function delete_payment($id = null)
  {

    if ($this->input->get('id')) {
      $id = $this->input->get('id');
    }

    if (!$this->Admin) {
      $this->session->set_flashdata('error', lang("access_denied"));
      redirect($_SERVER["HTTP_REFERER"]);
    }

    if ($this->sales_model->deletePayment($id)) {
      $this->session->set_flashdata('message', lang("payment_deleted"));
      redirect('sales');
    }
  }

  function add()
  {

    // $this->load->library('Pdf');

    if (!$this->Admin) {
      $this->session->set_flashdata('error', lang('access_denied'));
      redirect('pos');
    }

    $this->form_validation->set_rules('date', lang('date'), 'required');

    if ($this->form_validation->run() == true) {

      $total = 0;
      $affected = 0;
      $tax = 0;
      $exonerated = 0;
    $exoneradoReal=0;//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
      $quantity = "quantity";
      $product_id = "product_id";
      $unit_price = "price";
      $discount = $this->input->post('gdiscount');
      $total_pay = $this->input->post('total_pay');
      $document_type = (int)$this->input->post('document_type');

      $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
      $settings_tax = $this->Settings->default_tax_rate;
      $count = 0;
      $countt = 0;
    
    //**********************TRJ068 - ALEXANDER ROCA - 12/08/2019******************      
    $ICBPER= (array)$this->pos_model->user_mail();
      
    if($ICBPER["tax_icbper"] == 0){
    $mult_ICBPER=0;
    }else{
    $mult_ICBPER= (array)$this->pos_model->tax_ICBPER_amount($ICBPER["tax_icbper"]);
    }
      
    $total_ICBPER=0;
    //**********************TRJ068 - ALEXANDER ROCA - 12/08/2019******************
    
      for ($r = 0; $r < $i; $r++) {
        $item_id = $_POST['product_id'][$r];
        $item_qty = $_POST['quantity'][$r];
        $item_price = $_POST['price'][$r];
       
    //*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************
    $item_affect_price = 0;
    $item_non_affected_price = 0;
    $item_exonerated_price = 0;
    
    $item_affect_price = $_POST['affect_price'][$r];
    $item_non_affected_price = $_POST['non_affected_price'][$r];
    $item_exonerated_price = $_POST['exonerated_price'][$r];
    
    //*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************
    
        $tax_method = $_POST['tax_method'][$r];
        $real_unit_price = $_POST['real_unit_price'][$r];
        $currency_cost = $_POST['currency_cost'][$r];
        $cost = $_POST['cost'][$r];
        $name = $_POST['name'][$r];
        $code = $_POST['code'][$r];
//**********************TRJ068 - ALEXANDER ROCA - 12/08/2019******************        
    if($ICBPER["tax_icbper"] > 0){
          $tax_ICBPER = $_POST['value_ICBPER'][$r];
          
          if($tax_ICBPER==1){
            
            if($mult_ICBPER['amount'] > 0){
              $quantity_ICBPER = (int)(round($item_qty));
            }else{
              $quantity_ICBPER = 0;
            }
            
            if($quantity_ICBPER > 0){
              $unit_price_ICBPER = $mult_ICBPER['amount'];
              $valor_item_ICBPER=$mult_ICBPER['amount'] * round($item_qty);
            }else{
              $unit_price_ICBPER = 0;
              $valor_item_ICBPER=0;
            }
            
          }else{
            $unit_price_ICBPER = 0;
            $valor_item_ICBPER=0;
            $quantity_ICBPER = 0;
          }
        }else{
          $unit_price_ICBPER = 0;
          $valor_item_ICBPER=0;
          $quantity_ICBPER = 0;
        }
        
        $total_ICBPER = $total_ICBPER + $valor_item_ICBPER;
//**********************TRJ068 - ALEXANDER ROCA - 12/08/2019******************  

        if ($item_id && $item_qty && $unit_price) {

          // if(!($this->site->getProductByID($item_id))) {
          //     $this->session->set_flashdata('error', $this->lang->line("product_not_found")." ( ".$item_id." ).");
          //     redirect('sales/add');
          // }

          $pr_item_tax = 0;
          $item_afec = 0;
          $item_tax = 0;
          $item_exon = 0;
      $item_exoReal = 0;//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
          $p_igv = "";
          $bool_exonerado = false;
      $bool_exoReal = false;//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
          $bool_afecto = false;
      //$productExonerated=0;//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
      
      
      
          if (isset($settings_tax) && $settings_tax != 0) {

            if ($document_type != 3){
//*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************        
        $item_afec = $item_affect_price / (1 + ($settings_tax / 100));
        $item_tax = $item_affect_price - $item_afec;
        $item_exoReal = $item_exonerated_price;
        $item_exon = $item_non_affected_price;
        
        if($item_affect_price>0){
          $tax_method = 0;
        }else{
          if($item_exonerated_price>0){
            
            if($item_exonerated_price > $item_non_affected_price){
              $tax_method = 3;
            }else{
              $tax_method = 2;
            }
            
          }else{
            if($item_non_affected_price>0){
              $tax_method = 2;
            }else{
              $tax_method = null;
            }
          }
        }
        
            if ($tax_method == 0) {// AFECTO
              
              $p_igv = $settings_tax;
              $bool_afecto = true;
            } else if ($tax_method == 1) {// AFECTO
              
              $p_igv = $settings_tax;
              $bool_afecto = true;
            } else if ($tax_method == 2) {// INAFECTO
              
              $p_igv = 0;
              
              //**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
        } else if($tax_method == 3){// EXONERADO
        
        $p_igv = 0;
        
        }//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
//*************TRJ044 - ALEXANDER ROCA - 20/05/2019***************
            } else {// NOTA DE VENTA
        $tax_method = 2;
        $item_afec = $item_affect_price;// / (1 + ($settings_tax / 100));
        //$item_tax = $item_affect_price - $item_afec;
        $item_exoReal = $item_exonerated_price;
        $item_exon = $item_non_affected_price;
      
              //$item_exon = 0;
              //$item_afec = $item_price;
              $item_tax = 0;
              $p_igv = 0;
              $bool_afecto = true;
            }

            $pr_item_tax = $item_tax * $item_qty;

          }

          $subtotal = (($item_affect_price + $item_exoReal + $item_exon) * $item_qty) + $valor_item_ICBPER;//$item_price * $item_qty;
          $igv_desc = 0;
          $gvd_desc = 0;
          $porcentaje = 0;
          $prr_discount = 0;
          if ($discount > 0) {

            if($document_type != 3){

            $porcentaje = $subtotal / $total_pay;
            $prr_discount = $discount * $porcentaje;

            if ($bool_afecto) {
              $gvd_desc = $prr_discount / (1 + ($settings_tax / 100));
              $igv_desc = $prr_discount - $gvd_desc;
            }

            } else {
                $gvd_desc = $discount;
                $prr_discount = 0;
                $igv_desc = 0;
            }

          } else {
            $gvd_desc = 0;
            $prr_discount = 0;
            $igv_desc = 0;
          }

          if ($code == "") {
            $item_id = null;
          }

          //Se agregó code & name | Diego
      
      $unit_price_item=(double)$item_affect_price + (double)$item_non_affected_price + (double)$item_exonerated_price;
      
//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********         
      $unit_price_item = $unit_price_item + $unit_price_ICBPER;
//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
      
          $products[] = array(
            'product_id' => $item_id,
            'code' => $code,
            'name' => $name,
            'quantity' => $item_qty,
            'unit_price' => $this->tec->formatDecimal($unit_price_item),
            'affect_price' => $this->tec->formatDecimal($item_affect_price - $item_tax),//$this->tec->formatDecimal($item_price - $item_tax),
      'non_affected_price' => $this->tec->formatDecimal($item_non_affected_price),//$this->tec->formatDecimal($item_price - $item_tax),
      'exonerated_price' => $this->tec->formatDecimal($item_exonerated_price),//$this->tec->formatDecimal($item_price - $item_tax),
            'discount' => 0,
            'item_discount' => 0.00,
            'prr_discount' => $this->tec->formatDecimal($prr_discount),
            'tax' => $this->tec->formatDecimal($item_tax),
            'item_tax' => $this->tec->formatDecimal($pr_item_tax),//$new_tax,//
            'igv' => $p_igv,
      'tax_ICBPER' => $valor_item_ICBPER, //************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
      'quantity_ICBPER' => $quantity_ICBPER, //************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
            'subtotal' => $subtotal,
            'real_unit_price' => $this->tec->formatDecimal($real_unit_price),
            'currency_cost' => $currency_cost,
            'cost' => $this->tec->formatDecimal($cost),
      //'tax_method' => $productExonerated//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
      'tax_method' => (int)$tax_method
          );

          $total += (((double)$item_affect_price + (double)$item_non_affected_price + (double)$item_exonerated_price) * $item_qty);

          //if ($bool_afecto) {
            $affected += ($item_afec * $item_qty) - $gvd_desc;
            $tax += $pr_item_tax - $igv_desc; //($item_tax * $item_qty) - $igv_desc;
          //}

          //if ($bool_exonerado) {
        if($item_exon > 0 ){
          $exonerated += ($item_exon * $item_qty) - $prr_discount;
        }else{
          $exonerated += ($item_exon * $item_qty);
        }
        
          //}
      //**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
      //if($bool_exoReal){
        if($item_exoReal > 0){
          $exoneradoReal += ($item_exoReal * $item_qty) - $prr_discount;
        }else{
          $exoneradoReal += ($item_exoReal * $item_qty);
        }
      
      //}//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************

          $count = $count + 1;
          $countt += $item_qty;

        }
      }

      if (!isset($products) || empty($products)) {
        $this->form_validation->set_rules('product', lang("order_items"), 'required');
        // $this->session->set_flashdata('warning', lang("products_not_found"));
        // redirect("sales/add");
      } else {
        krsort($products);
      }

      if ($this->form_validation->run() == true) {
//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
        $sub_total = $affected + $exonerated + $tax + $exoneradoReal + $total_ICBPER;
        $grand_subtotal = $total + $total_ICBPER - $discount;//$total - $discount;
//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
        $customer_det = (array)$this->pos_model->getCustomerByID($this->input->post('customer'));

        $data = array(
          'date' => $this->input->post('date') . " " . date('H:i:s'),
          'local_id' => $this->session->userdata('local'),
          'expiration_date' => $this->input->post('expiration_date') ? $this->input->post('expiration_date') : null,
          'customer_id' => $this->input->post('customer'),
          'customer_name' => $customer_det['name'],
          'document_type' => $document_type,
          'currency' => $this->input->post('currency_code'),
          'exchange' => $this->Exchange->sell,
          'affected' => $this->tec->formatDecimal($affected),
          'non_affected' => $this->tec->formatDecimal($exonerated),
      'exonerated' => $this->tec->formatDecimal($exoneradoReal),//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
          'total' => $this->tec->formatDecimal($affected + $exonerated + $exoneradoReal),//$total),//$op_grav//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019****************** 
          'product_discount' => 0,
          'order_discount_id' => $discount,
          'order_discount' => $discount,//desc
          'total_discount' => $discount,
          'product_tax' => $this->tec->formatDecimal($tax),//$product_tax),//$igv
          'order_tax_id' => 0,
          'order_tax' => 0,
          'total_tax' => $this->tec->formatDecimal($tax),//$total_tax,
      'tax_ICBPER' => $total_ICBPER,//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
      'mult_ICBPER' => $unit_price_ICBPER,//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
          'subtotal' => $this->tec->formatDecimal($total + $total_ICBPER),
          'amount' => $this->tec->formatDecimal($grand_subtotal),
          'grand_total' => $this->tec->formatDecimal($sub_total),
          'total_items' => $count,
          'total_quantity' => $countt,
          'rounding' => 0,
          'paid' => 0,
          'status' => "DEBE",
          'created_by' => $this->session->userdata('user_id'),
          'note' => $this->input->post('note', true),
          // 'custom_field_1' => Null,//tec_sales
          // 'custom_field_2' => Null, //tec_sales
          'canal_id' => 2,
          'estado' => 1,
        );
    

        if ($_FILES['userfile']['size'] > 0) {

          $this->load->library('upload');
          $config['upload_path'] = 'uploads/';
          $config['allowed_types'] = $this->allowed_types;
          $config['max_size'] = '2000';
          $config['overwrite'] = false;
          $config['encrypt_name'] = true;
          $this->upload->initialize($config);

          if (!$this->upload->do_upload()) {
            $error = $this->upload->display_errors();
            $this->upload->set_flashdata('error', $error);
            redirect("sales/add");
          }

          $data['attachment'] = $this->upload->file_name;

        }

        if ($data['document_type'] == 2) {
          $new_number = (int)$this->Settings->invoice_number + 1;
          $format = $this->Settings->invoice_format;
                } else if ($data['document_type'] == 1) {
          $new_number = (int)$this->Settings->bill_number + 1;
          $format = $this->Settings->bill_format;
                } else if ($data['document_type'] == 3) {
                    $new_number = (int)$this->Settings->nventa_number + 1; //*****Renato TRJ023 25/04/2019   ********
                    $format = $this->Settings->nventa_format; //*****Renato TRJ023 25/04/2019   ********
        }

        $parte1 = explode('{', $format);
        $parte2 = explode('}', $parte1[1]);
        $parentesis = $parte2[0];
        $buscar = strpos($parentesis, '+');
        if ($buscar) {
          $numero = explode('+', $parentesis);
          $new_format = str_pad((string)$new_number, strlen($numero[0]), "0", STR_PAD_LEFT);
        } else {
          $new_format = str_pad((string)$new_number, strlen($parentesis), "0", STR_PAD_LEFT);
        }

        $serieNumero = $parte1[0] . $new_format;

                if ($this->pos_model->getInvoiceId($serieNumero)){
                    $this->session->set_flashdata('error', lang("the_invoice") . $serieNumero . lang("not_unique"));
                    redirect("sales/add");
                }

        $data['invoice_id'] = $serieNumero;

        if ($this->form_validation->run() == true) {

          //Actualiza Nro Comprobante (Settings)
          $this->pos_model->update_invoice_Number($data['document_type']);

                    if ($data['document_type'] != 3) {

          //Generar XML
          require("funciones.php");
          if ($respuesta = $this->generar_xml($serieNumero, $data, $products, $customer_det)) {
            $data['flg_response'] = $respuesta['flg_response'];
            $data['error_code'] = $respuesta['error_code'];
            $data['response_descrip'] = $respuesta['response_descrip'];
            $data['digest_value'] = $respuesta['digest_value'];
          }

        }
      }

    }

    }
  //***********************************TRJ028 - ALEXANDER ROCA - 04/04/2019************************
  $localData = $this->pos_model->getIDwarehouse($this->session->userdata('local'));
          
  $data['warehouse_id']=$localData ->default_warehouse;
          
  if($data['warehouse_id'] == null || !$data['warehouse_id']){
    $localData = $this->pos_model->getIDwarehouseDefault();
    $data['warehouse_id']=$localData ->default_warehouse;
  }
  //***********************************TRJ028 - ALEXANDER ROCA - 04/04/2019************************
    if ($this->form_validation->run() == true && $sale = $this->sales_model->addSale($data, $products)) {

      $this->session->set_userdata('remove_spo', 1);
      $this->session->set_flashdata('message', lang('sale_added'));

      //REGISTRAR EN MOVIMIENTOS
      foreach ($products as $item) {

        if ($item['product_id'] != null) {

          $item_id = $item['product_id'];
          $item_quantity = $item['quantity'];
          $dataMove = array(
            'origin_id' => $sale, //$sale2->id,
            'local_id' => $this->session->userdata('local'),
            'warehouse_id' => $this->Settings->default_warehouse,
            'product_id' => $item_id,
            'cant' => $item_quantity,
            'tipo' => 'S',
            'idMotivo' => 5, //Salida por venta
            'ref' => $data['invoice_id'], //$sale2->invoice_id,
            'uCrea' => $this->session->userdata('user_id'),
            'fCrea' => date("Y-m-d H:i:s"),
            'estado' => 1
          );

          $this->mov_model->addMovement($dataMove);
      //$this->pos_model->updateItemStock($item);//************************************TRJ017 - ALEXANDER ROCA - 26/03/2019 ***************
        }
      }
      $p=$this->site->getPDFformat();
      $pdf=$p->pdf_format;
      /************************TRJ088 - KENY PONTE - 02/09/2019********************* */
      if ($document_type == 1) {

        //PDF
        include("app/class/QRCodeGenerator.class.php");//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
        //*****************TRJ088 -KENY PONTE - 03/09/2019************* */
        if($pdf==1){
          $this->generar_pdf($sale, $data, $products, $customer_det);
        }else if($pdf==2){
          $this->generar_pdf1($sale, $data, $products, $customer_det);
        }
        //*******************TRJ088 - KENY PONTE 03/09/2019*************** */
      } else if ($document_type == 2) {

        //Enviar Comprobante
        //send_envoice add
        $this->enviar_comprobante($serieNumero, $sale, null, "pago", $customer_det);//***********TRJ016 - ALEXANDER ROCA - 29/03/2019************

        //PDF
        include("app/class/QRCodeGenerator.class.php");//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
        /***********************************TRJ088 - KENY PONTE - 03/09/2019****************** */
        if($pdf==1){
          $this->generar_pdf($sale, $data, $products, $customer_det);
        }else if($pdf==2){
          $this->generar_pdf1($sale, $data, $products, $customer_det);
        }

      }
      redirect("sales");

    } else {
      // $this->data['dw'] = $this->Settings->default_warehouse;
      // $this->data['dm'] = $this->Settings->currency_prefix;
      // $this->data['exchange'] = $this->site->getExchange(date("Y-m-d"));
      $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
      $this->data['customers'] = $this->site->getAllCustomers();
      $this->data['document_type'] = $this->site->getAllDocumentType();
      $this->data['customer_type']=$this->site->getAllCustomerType();
      $this->data['warehouses'] = $this->site->getAllWarehouses();
    $advSale=(array)$this->site->getAdvanced_sale();
    $this->data['advance_sale'] = $advSale['advanced_sale'];
    
    $seting_bags=$this->pos_model->consult_plastic_bags();
      

      if( $seting_bags->tax_icbper == 0){
    $this->data['plastic_bags'] = 0;
    }else{
    $consul_bags = $this->pos_model->consult_plastic_bags_type($seting_bags->tax_icbper);
    $this->data['plastic_bags'] = $consul_bags->amount;
    }


    
      $this->data['page_title'] = lang('add_sale');
      $bc = array(array('link' => site_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('add_sale')));
      $meta = array('page_title' => lang('add_sale'), 'bc' => $bc);
      $this->page_construct('sales/add', $this->data, $meta);

    }
  }

function suggestions($id = null)
  {
  $affect_priceJs=null;
  $non_affected_priceJs=null;
  $exonerated_priceJs=null;
    
    if ($id) {
      $row = $this->site->getProductByID($id);
      $row->qty = 1;
    //***********TRJ068 - ALEXADER ROCA - 02/08/2019************
      $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_ICBPER'=> 0 , 'affect_priceJs'=>$affect_priceJs, 'non_affected_priceJs'=>$non_affected_priceJs, 'exonerated_priceJs'=>$exonerated_priceJs);
      //***********TRJ068 - ALEXADER ROCA - 02/08/2019************
    echo json_encode($pr);
      die();
    }
    $term = $this->input->get('term', true);
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

    $rows = $this->sales_model->getProductNames($term,$validacion);
  //***********TRJ0003 - ALEXADER ROCA - 13/03/2019************
    if ($rows) {
      foreach ($rows as $row) {
      
    //********************************TRJ064 - ALEXANDER ROCA - 03/07/2019**********************
    $this->session->set_userdata('listaVenta', $this->session->userdata('listaVenta') + 1);
    $row->productID=$row->id;
    $row->id=$this->session->userdata('listaVenta');
    //********************************TRJ064 - ALEXANDER ROCA - 03/07/2019**********************
      //***********TRJ068 - ALEXADER ROCA - 02/08/2019************ 
        $row->qty = 1;
        $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_ICBPER'=> 0 , 'affect_priceJs'=>$affect_priceJs, 'non_affected_priceJs'=>$non_affected_priceJs, 'exonerated_priceJs'=>$exonerated_priceJs);
    //***********TRJ068 - ALEXADER ROCA - 02/08/2019************
      }
      echo json_encode($pr);
    } else {
      $row = array(
        'id' => substr(str_replace(".", "", microtime(true)), 0, 10),
        'qty' => 1,
        'name' => $term,
        'code' => "",
        'currency' => "",
        'price' => "0.00",
        'cost' => "0.00",
        'tax_method' => 0
      );

      // $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row["id"], 'label' => $row["name"] . " (" . $row["code"] . ")", 'row' => $row);
    //***********TRJ068 - ALEXADER ROCA - 02/08/2019************
      $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row["id"], 'label' => $row["name"], 'row' => $row , 'tax_ICBPER'=> 0, 'affect_priceJs'=>$affect_priceJs, 'non_affected_priceJs'=>$non_affected_priceJs, 'exonerated_priceJs'=>$exonerated_priceJs);
      echo json_encode($pr);
    //***********TRJ068 - ALEXADER ROCA - 02/08/2019************
      // echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
    }
  }



  /* --------------------------------------------------------------------------------------------- */

  /* ------------------GENERA EL PDF Y GUARDA EN LA TABLA DE HISTORIAL "tec_sales_files"------------------------------- */
  function generar_pdf($sale_id, $inv, $items, $customer_det)
  {

    //Idioma ---------------
    $region = $this->session->userdata('region');
    $l_ruc = lang("ccf2");
    $l_dni = lang("ccf1");
    $l_address = lang("scf2");
    $arrMes = lang("months");
    $l_qty = lang("qty");
    $l_description = lang("description");
    $l_importe = lang("importe");
    $l_currency = lang("currency_name");
    $op_exo = lang("op_exonerated");
  $op_exoReal=lang("op_exo");//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
  $op_ICBPER=lang('product_tax_ICBPER').' ('.lang('tax_ICBPER').')';//***************TRJ068 - ALEXANDER ROCA - 13/08/2019********************
    //----------------------

    $tipo_doc = "";
    $impRuc = "";
    if ($inv['document_type'] == 1) {
      $tipo_doc = lang('bill_title');
    } else {
      $tipo_doc = lang('invoice_title');
    }

    $numComp = $inv['invoice_id'];

    $symbol = "";
    $currency = "";
    if ($inv['currency'] == 'PEN') {
      // $symbol = "S/ ";
      $currency = strtoupper(lang("currency_pen"));
    } else if ($inv['currency'] == 'USD') {
      // $symbol = "$ ";
      $currency = lang("currency_USD");
    }

    $arrD1 = explode(" ", $inv['date']);
    $arrD2 = explode("-", $arrD1[0]);
    $ano = $arrD2[0];
    $dia = $arrD2[2];
    $mes = $arrMes[(int)$arrD2[1] - 1];

    $row_items_html = "";
    foreach ($items as $item) {

      $item = (array)$item;

      $quantity = $this->tec->formatNumber($item['quantity']);
      $uprice = $this->tec->formatMoney($item['unit_price']);
      $subt = $this->tec->formatMoney($item['subtotal']);
    //***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************
    $nombreProducto=str_replace("<","&lt;",$item['name']);
    $nombreProducto=str_replace(">","&gt;",$nombreProducto);
    
      $row_items_html .= '<tr>
                    <td align="center" style="font-size:10px">' . $quantity . '</td>
                    <td align="left" style="font-size:10px">' . $nombreProducto . '</td>
                    <td align="right" style="font-size:10px">' . $uprice . '</td>
                    <td align="right" style="font-size:10px">' . $subt . '</td>
                </tr>';
    }//***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************

    $row_items_html .= '
            <tr>
                <td align="center">&nbsp;</td>
                <td align="left"></td>
                <td align="right"></td>
                <td align="right"></td>
            </tr>
        ';

    $customer_name = $customer_det['name'];
    $customer_address = $customer_det['direccion'];

    if ($inv['document_type'] == 2) {
      // $impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" >
      //             <tr>
      //                 <td align="left"  style="font-size:10px">' . $region . ', <u> '.$dia.' </u> de <u> '.$mes.' </u> del <u> '.$ano.' </u> </td>
      //             </tr>
      //             <tr>
      //                 <td align="left"  style="font-size:10px">' . $l_ruc . ': '.$customer_det['cf2'].'</td>
      //             </tr>

      //         </table>';

      $impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" align="left" >
                            <tr>
                                <td align="left" width="20%"  style="font-size:10px"> Señor(es): </td>
                                <td align="left" width="80%"  style="font-size:10px"> ' . $customer_name . '</td>
                            </tr>
                            <tr>
                                <td align="left" width="20%"  style="font-size:10px"> ' . $l_ruc . ': </td>
                                <td align="left" width="80%"  style="font-size:10px"> ' . $customer_det['cf2'] . '</td>
                            </tr>
                            <tr>
                                <td align="left" width="20%"  style="font-size:10px"> ' . $l_address . ': </td>
                                <td align="left" width="80%"  style="font-size:10px"> ' . $customer_address . '</td>
                            </tr>

                        </table>';
    } else {

      // $impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" >
      //             <tr>
      //                 <td align="left"  style="font-size:10px">' . $region . ', <u> '.$dia.' </u> de <u> '.$mes.' </u> del <u> '.$ano.' </u><br></td>
      //             </tr>
      //         </table>';

            $doc = $l_dni;
      $nro_doc = $customer_det['cf1'];

      if($customer_det['cf1'] == null && $customer_det['cf2'] != null){
        $doc = $l_ruc;
        $nro_doc = $customer_det['cf2'];
      }
//***************TRJ043 - ALEXANDER ROCA - 30/04/2019**************
      $impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" align="left">
                        <tr>
                            <td align="left" width="20%"  style="font-size:10px"> Señor(es): </td>
                            <td align="left" width="80%"  style="font-size:10px"> ' . $customer_name . '</td>
                        </tr>

                        <tr>
                <td align="left" width="20%"  style="font-size:10px"> ' . $doc . ': </td>
                <td align="left" width="80%"  style="font-size:10px"> ' . $nro_doc . '</td>
                        </tr>
            <tr>
                                <td align="left" width="20%"  style="font-size:10px"> ' . $l_address . ': </td>
                                <td align="left" width="80%"  style="font-size:10px"> ' . $customer_address . '</td>
                        </tr>
                    </table>';
//***************TRJ043 - ALEXANDER ROCA - 30/04/2019**************
      // <tr>
      //     <td align="left" width="20%"  style="font-size:10px"> ' . $l_address . ': </td>
      //     <td align="left" width="80%"  style="font-size:10px"> ' . $customer_address . '</td>
      // </tr>
    }

    $sub = $this->tec->formatMoney($inv['subtotal']);
    $desc = $this->tec->formatMoney($inv['order_discount']); //(-$inv['rounding']) +
    $afc = $this->tec->formatMoney($inv['affected']);
    $exo = $this->tec->formatMoney($inv['non_affected']);
  $tax_ICBPER = $this->tec->formatMoney($inv['tax_ICBPER']);//***************TRJ068 - ALEXANDER ROCA - 13/08/2019********************
  $exoReal = $this->tec->formatMoney($inv['exonerated']);//****************TRJ008 - ALEXANDER ROCA  - 11/04/2019**********
    $igv = $this->tec->formatMoney($inv['total_tax']);
    $tot1 = $this->tec->formatMoney($inv['amount']);
    $round = $this->tec->formatMoney($inv['rounding']);
    $tot2 = $this->tec->formatMoney($inv['grand_total']);
    $totLetras = strtoupper(numtoletras($inv['grand_total']) . " " . $currency);

    $this->load->library('Pdf');

    $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetTitle($tipo_doc . " - " . $numComp);
    $pdf->SetHeaderMargin(30);
    $pdf->SetTopMargin(20);
    $pdf->setFooterMargin(20);
    $pdf->SetAutoPageBreak(true);
    $pdf->SetAuthor('Author');
    $pdf->SetDisplayMode('real', 'default');

    $pdf->AddPage();

    $ivPorc = $this->Settings->default_tax_rate;
    $nombreCorto = strtoupper($this->Settings->site_name);

    $razon_social = $this->session->userdata('razon_social');
    $ruc = $this->session->userdata('ruc');
    $direccion = $this->session->userdata('direccion');
    $region = $this->session->userdata('region');
    $pais = $this->session->userdata('pais');
    $region_pais = strtoupper($region . " - " . $pais);
    $imagen = $this->session->userdata('logo');
    $pos_logo = $this->session->userdata('pos_logo');
  //****************TRJ030 - RENATO GAMONAL*******************
  $imagen2 = $this->session->userdata('logo_pdf');
  $type_imagen_pdf = $this->session->userdata('type_imagen_pdf');
//****************TRJ030 - RENATO GAMONAL*******************

    $width = 334;
    $height = 102;

    if ($pos_logo == 2) {
      $width = 334;
      $height = 102;
    } else if ($pos_logo == 1) {
      $width = 84;
      $height = 102;
    }

    $_ruc = $l_ruc . ". " . $ruc;

    //-----------------------------------QR-------------------------------------------//
    $serieNumero = explode('-', $numComp);
    $serie = $serieNumero[0];
    $numero = $serieNumero[1];
    $date = $arrD1[0];
    $hash = $inv['digest_value'];

    if ($inv['document_type'] == 2) {
      $cusDoc = "06";
      $docType = "01";
      $numDoc = $customer_det['cf2'];
      $qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
    } else {
      $cusDoc = "01";
      $docType = "03";
      $numDoc = $customer_det['cf1'];
      $qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
    }
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
    $ex1 = new QRCodeGenerator();
    $qr_img = '<img src="' . $ex1->generate($serieNumero[0].$serieNumero[1],$qr) . '" width="120" height="120">';
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
    //------------------------------------------------------------------------------//
//****************TRJ030 - RENATO GAMONAL*******************
  if ($type_imagen_pdf == 1){ 
    $cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen) . '" width="' . $width . '" height="' . $height . '">';
    $cabecera2 = '<div><br>' . $razon_social . '<br>' . $direccion . '<br>' . $region_pais . '<br>' . $_ruc.' </div>';
           } 
  else if ($type_imagen_pdf == 2) {
    $width2 = 370;
    $height2 = 150;
    $cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen2) . '" width="' . $width2 . '" height="' . $height2 . '">';
    $cabecera2 ="";
    }
//****************TRJ030 - RENATO GAMONAL*******************  
//****************TRJ009 - ALEXANDER ROCA - 19/03/2019 *******************
//****************TRJ008 - ALEXANDER ROCA  - 11/04/2019**********
//***************TRJ068 - ALEXANDER ROCA - 13/08/2019********************
    $tbl = <<<EOD
            <table border="0" width="100%" cellspacing="0" cellpadding="2" >
                <tr>
                    <td width="65%" align="center" style="font-size:10px">
                        $cabecera
            $cabecera2
                    </td>
                    <td width="35%" valign="middle"><br><br><br><br>
                        <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                            <tr>
                                <td align="center"  style="font-size:10px"><h2>$_ruc</h2></td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#E4E4E4" style="font-size:10px"><h2>$tipo_doc</h2></td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size:10px"><h2>$numComp</h2></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="65%" align="left">

                        $impRuc

                    </td>

                    <td width="35%">

                        <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                            <tr>
                                <td align="left" style="font-size:10px"> <u>$region</u> ,  <u>     $dia     </u>   de   <u>   $mes   </u>   del   <u>     $ano     </u> </td>
                            </tr>
                            <tr>
                                <td align="left"  style="font-size:10px"> $l_currency: $currency</td>
                            </tr>

                        </table>

                    </td>
                </tr>
            </table>

                <br><br>

                <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                    <tr>
                        <td bgcolor="#E4E4E4" align="center" width="10%" style="font-size:10px"><strong>$l_qty</strong></td>
                        <td bgcolor="#E4E4E4" align="center" width="60%" style="font-size:10px"><strong>$l_description</strong></td>
                        <td bgcolor="#E4E4E4" align="center" width="15%" style="font-size:10px"><strong>Precio Unitario</strong></td>
                        <td bgcolor="#E4E4E4" align="center" width="15%" style="font-size:10px"><strong>$l_importe</strong></td>
                    </tr>
                    $row_items_html
                </table>
                <table border="0" width="100%" cellspacing="0" cellpadding="2" >
                    <tr>
                        <td align="left" style="font-size:10px">SON: $totLetras</td>
                    </tr>
                </table>
                <table border="0" width="100%" cellspacing="0" cellpadding="2" >
                    <tr>
                        <td  width="70%" align="center">
                            <table border="0" cellspacing="0" cellpadding="2" >
                                <tr>
                                    <td align="center">$qr_img</td>
                                    <br>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size:10px"><i>CANCELADO</i></td>
                                </tr>
                <BR>
                                <tr>
                                    <td align="center" style="font-size:10px"><u>$region</u> ,  __________<!--$dia--> de __________<!--$mes--> del __________<!--$ano--> </td>
                                </tr>
                            </table>
                        </td>

                        <td align="center" width="30%">

                        <br><br>

                        <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                            <tr>
                                <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Sub Total</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$sub</td>
                            </tr>
                            <tr>
                                <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Descuento</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$desc</td>
                            </tr>
                            <tr>
                                <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Total</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$tot1</td>
                            </tr>
                            <tr>
                                <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Op. Gravadas</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$afc</td>
                            </tr>
              <tr>
                                <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>$op_exoReal</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$exoReal</td>
                            </tr>
                            <tr>
                                <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>$op_exo</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$exo</td>
                            </tr>
                            <tr>
                                <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>IGV - $ivPorc %</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$igv</td>
                            </tr>
              <tr>
                                <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>$op_ICBPER</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$tax_ICBPER</td>
                            </tr>
                            <tr>
                  <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Redondeo</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$round</td>
                            </tr>
                            <tr>
                                <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Total Venta</strong></td>
                                <td align="right" width="40%" style="font-size:10px">$tot2</td>
                            </tr>
                        </table>

                        </td>
                    </tr>
                </table>

                <br>
EOD;
//***************TRJ068 - ALEXANDER ROCA - 13/08/2019********************
//****************TRJ008 - ALEXANDER ROCA  - 11/04/2019**********
//****************TRJ009 - ALEXANDER ROCA - 19/03/2019 *******************
    $pdf->writeHTML($tbl, true, false, false, false, '');

    if(!$file = $this->sales_model->getFileBySaleID($sale_id)){

      if ($inv['document_type'] == 1) {
        $docName = 'boletas';
      } else {
        $docName = 'facturas';
      }

      $nombPDF = randomstring(20);

      //GRABA EN TABLA HISTORIAL
      $rutaTabla = array(
        'sale_id' => $sale_id,
        'file_name' => $docName . '/' . $nombPDF . '.pdf',
      );

      $this->db->insert('sales_files', $rutaTabla);

      //GUARDA EL PDF FISICO
      $pdf->Output(FCPATH . 'uploads/pdf/' . $docName . '/' . $nombPDF . '.pdf', 'F');

      return true;

    } else {

      $this->db->update('sales_files', ['file_name' => $file->file_name], ['sale_id' => $sale_id]);

      //GUARDA EL PDF FISICO
      $pdf->Output(FCPATH . 'uploads/pdf/' . $file->file_name, 'F');

      return true;

    }

    return false;
  }

  //***************TRJ029 - ALEXANDER ROCA - 05/04/2019*************************
  function reenvioSunatXml($id = null)
  {

    
    try{
    
      //**********TRJ041 - ALEXANDER ROCA - 29/04/2019**************
      $capturaVenta = $this->sales_model->sale($id);
 
      
      if($this->sales_model->getSendInvoce($id)){
        
        $this->session->set_flashdata('error', "La factura ".$capturaVenta->invoice_id." ".lang("send_ok_error"));
        redirect('sales');
        
      }else{
      
        $data =array(
          'document_type' => $capturaVenta->document_type,
          'invoice_id' =>$capturaVenta->invoice_id,
          'customer_id' =>$capturaVenta->customer_id,
        );
        
        $customer_det = (array)$this->sales_model->customers_sale($data['customer_id']);
        
        if ($data['document_type'] == 2) {
          
        $this->enviar_comprobante($data['invoice_id'], $id, null, "pago", $customer_det);
        
        }else{
          $this->session->set_flashdata('error', lang("type_error"));
          redirect('sales');
        }
        
        $this->sales_model->update_status_send_invoice($id);
        
        $this->session->set_flashdata('message', $capturaVenta->invoice_id ." - ".lang("invoice_forwarding"));
        redirect('sales');
      
      
      }//**********TRJ041 - ALEXANDER ROCA - 29/04/2019**************
    
    }catch(Exception $e){
      
    $this->session->set_flashdata('error', lang("type_error")." - error :".$e);
    redirect('sales');
    
    }
    
    
  }
  //***************TRJ029 - ALEXANDER ROCA - 05/04/2019*************************
  
  //**********TRJ045 - ALEXANDER ROCA - 21/05/2019**************
  function reArmarXml($id = null)
  {
  $capturaVenta = (array)$this->sales_model->sale($id);
  
  
  
  if($capturaVenta['flg_response'] == 1 && $capturaVenta['error_code'] == 0){
    
    $this->session->set_flashdata('warning', $capturaVenta['invoice_id'] ." - ".lang("recreate_Xml_error_access"));
    redirect('sales');
    
  }else{
  
    $serieNumero = $capturaVenta['invoice_id'];
    $customer_det = (array)$this->pos_model->getCustomerByID($capturaVenta['customer_id']);
    
    $data = array(
        'document_type' => $capturaVenta['document_type'],
        'date' => $capturaVenta['date'],
        'currency' => $capturaVenta['currency'],
        'amount' => $capturaVenta['amount'],
        'total_items' => $capturaVenta['total_items'],
        'customer_name' => $capturaVenta['customer_name'],
        'order_discount' => $capturaVenta['order_discount'],
        'product_tax' => $capturaVenta['product_tax'],
        'affected' => $capturaVenta['affected'],
        'total_tax' => $capturaVenta['total_tax'],
        'exonerated' => $capturaVenta['exonerated'],
        'non_affected' => $capturaVenta['non_affected'],
        'tax_ICBPER' => (string)$capturaVenta['tax_ICBPER'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
        'mult_ICBPER' => (string)$capturaVenta['mult_ICBPER']//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
      );
      
    $items=$this->sales_model->getSaleItemsByID($id);
    
    foreach ($items as $item){
      
      $products[] = array(
        'quantity' => $item['quantity'],
        'affect_price' => $item['affect_price'],
        'non_affected_price' => $item['non_affected_price'],
        'exonerated_price' => $item['exonerated_price'],
        'unit_price' => $item['unit_price'],
        'item_tax' => $item['item_tax'],
        'igv' => $item['igv'],
        'name' => $item['name'],
        'product_id' => $item['product_id'],
        "tax_method" => $item['tax_method'],
        "tax_ICBPER" => (string)$item['tax_ICBPER'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
        "quantity_ICBPER" => (string)$item['quantity_ICBPER'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
      );
        
    }
      
    require("funciones.php");
    
    if ($respuesta = $this->generar_xml($serieNumero, $data, $products, $customer_det)) {
      $resp['flg_response'] = $respuesta['flg_response'];
      $resp['error_code'] = $respuesta['error_code'];
      $resp['response_descrip'] = $respuesta['response_descrip'];
      $resp['digest_value'] = $respuesta['digest_value'];
    }
    
    if($this->sales_model->update_sale_re_XML($id, $resp)){
      $this->session->set_flashdata('message', $capturaVenta['invoice_id'] ." - ".lang("recreate_Xml_succes"));
      redirect('sales');
    }
  
  }
  
  
  
  
  $this->session->set_flashdata('error', $capturaVenta['invoice_id'] ." - ".lang("recreate_Xml_error"));
  redirect('sales');
  
  
  }
  //**********TRJ045 - ALEXANDER ROCA - 21/05/2019**************
  //**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************
  function removeSale($id = null){
    
    if($this->sales_model->remove_sale($id)){
      
      $capturaVenta = (array)$this->sales_model->sale($id);
      $this->session->set_flashdata('message', lang("proof")." ".$capturaVenta['invoice_id'] ." ".lang("remove_sale_succes"));
      redirect('sales');
    }else{
      $this->session->set_flashdata('error', lang("remove_sale_error"));
      redirect('sales');
    }
    
  }
  //**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************
  
  }


