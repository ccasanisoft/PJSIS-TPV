<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Summaries_model extends CI_Model
{

    public function __construct() {
        parent::__construct();

    }

    public function addSummary($fecha) {

        // $fecha = date('2018-10-29');
        $issueDate = date("Y-m-d");
        $RB = false;
        $AB = false;
        $AF = false;
//*************************TRJ032 - ALEXANDER ROCA - 06/04/2019*******************		
        //if(!$this->getNullSales($fecha)){
//*************************TRJ032 - ALEXANDER ROCA - 06/04/2019*******************			
            $summary = $this->getSummaryByDate($issueDate);
            if($summary->number){
                $number1 = (int)$summary->number + 1;
                $number2 = $number1 + 1;
            }else{
                $number1 = 1;
                $number2 = 2;
            }

            if($this->getSummaryAllSales($fecha,$number1)){
                $RB = true;
            }

            if($this->getSummaryUndoSales($fecha,$number2,1)){
                $AB = true;
            }

            $summary_voided = $this->getSummaryByDate($issueDate, $baja = true);
            if($summary_voided->number){
                $number3 = (int)$summary_voided->number + 1;
            }else{
                $number3 = 1;
            }

            if($this->getSummaryUndoSales($fecha,$number3,2)){
                $AF = true;
            }

            if($RB||$AB||$AF){
                return true;
            }
//*************************TRJ032 - ALEXANDER ROCA - 06/04/2019*******************
        /*}else{
            // die("Resumen no enviado, existen ventas con precio 0");
            return false;
        }*/
//*************************TRJ032 - ALEXANDER ROCA - 06/04/2019*******************
        return false;
    }

    public function getSummaryAllSales($date, $num = 1)
    {
        $endDate = strtotime ( '+1 day' , strtotime ( $date ) ) ;
        $endDate = date('Y-m-d 00:00:00', $endDate );

        $this->db->select('sales.*, customers.document_type_id as tipo_documento, customers.cf1 as dni, customers.cf2 as ruc', FALSE)
        ->join('customers', 'customers.id=sales.customer_id', 'left')
        ->order_by('sales.date')
        // ->where('sales.status !=', "DEBE")
        ->where('sales.invoice_id !=', NULL)
        ->where('sales.date >', $date)
        ->where('sales.date <', $endDate)
		->where('sales.estado !=', 2);//************TRJ074 - ALEXANDER ROCA - 23/08/2019************	

        $q = $this->db->get_where('sales', array('document_type' => 1));

        if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
            }


            if($this->send_summary($date, $date, $data, $num)){
                return true;
            }

            return true;
		}

        return false;
    }

    public function getSummaryUndoSales($date, $num = 2, $document_type = 1)
    {
        $endDate = strtotime ( '+1 day' , strtotime ( $date ) ) ;
        $endDate = date('Y-m-d 00:00:00', $endDate );

        $this->db->select('sales.*, customers.document_type_id as tipo_documento, customers.cf1 as dni, customers.cf2 as ruc', FALSE)
        ->join('customers', 'customers.id=sales.customer_id', 'left')
        ->order_by('sales.date')
        // ->where('sales.status !=', "DEBE")
        ->where('sales.invoice_id !=', NULL)
        ->where('sales.updated_at >', $date)
        ->where('sales.updated_at <', $endDate)
        ->where('sales.estado =', 0);

        $q = $this->db->get_where('sales', array('document_type' => $document_type));

        $dateb = true;
        if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
                $data[] = $row;

                if($dateb){
                    $datet = explode(' ', $row->date, 2);
                    $firstDate = $datet[0];
                    $dateb = false;
                }
            }

            $items = (array)$data;
            $summary;
            $processed_date = $date;
            $date = $firstDate;

            $count = 0;
            foreach ($items as $item) {

                $count = $count + 1;
                $datef = explode(' ', $item->date, 2);
                $referenceDate = $datef[0];

                if($date != $referenceDate){
                    if($document_type == 2){
                        $this->send_summary_voided($processed_date, $date, $summary, $num, 0);
                    }else if($document_type == 1){
                        $this->send_summary($processed_date, $date, $summary, $num, 0);
                    }
                    $num = $num + 1;
                    unset($summary);
                    $summary[] = $item;
                }else{
                    $summary[] = $item;
                }

                $date = $referenceDate;

                if($q->num_rows() == $count){
                    if($document_type == 2){
                        $this->send_summary_voided($processed_date, $date, $summary, $num, 0);
                    }else if($document_type == 1){
                        $this->send_summary($processed_date, $date, $summary, $num, 0);
                    }
                }
            }

            return true;
        }
        return false;
    }

    public function send_summary($processed_date, $referenceDate, $items, $numero, $estado = 1){

        // $items = $this->getSummarySales($referenceDate, $estado, $endDate);
        // die(json_encode($items));
        if($items){
            //Header
            $type = "RC";
            $supplierID = $this->session->userdata('ruc');
            $date = date("Ymd");

            $passFirma = $this->session->userdata('pass_firma');
            $usuSol = $this->session->userdata('user_sol');
            $passSol = $this->session->userdata('pass_sol');

            $issueDate = date("Y-m-d");
            $supplierName = $this->session->userdata('razon_social');

            if($this->session->userdata('beta') == 0){
                $urlDoc = "post/xmlenvio";
            }else{
                $urlDoc = "beta/post/xmlenvio";
            }

            $header = array(
                "type" => $type,
                "serieNumero" => (string)$numero,
                "supplierID" => $supplierID,
                "date" => $date,
                "passFirma" => $passFirma,
                "usuSol" => $usuSol,
                "passSol" => $passSol,
                "issueDate" => $issueDate,
                "referenceDate" => $referenceDate,
                "supplierName" => $supplierName
            );

            $sales_ids;
            $cant= 0;
            foreach ($items as $item) {

                if($estado == 0){
                    $conditionCode = "3";
                }else{
                    $conditionCode = "1";
                }

                $documentTypeCode = "03";
                $documentID = $item->dni;
                if($documentID == "" | $documentID == null){
                    $documentID = $item->ruc;
                }
                $document=$item->tipo_documento;//*******TRJ073 - KENY PONTE - 17/09/2019********* */
                //$document="1";

                $serieNumero = $item->invoice_id;

                $cant= $cant + 1;
                $itemArray[$cant] = array(
                                    "documentTypeCode" => $documentTypeCode,
                                    "serieNumero" => $serieNumero,
                                    "customerID" => $documentID,
                                    "additionalAccountID" => $document,
                                    "conditionCode" => $conditionCode,
                                    "totalAmount" => $item->grand_total,
                                    "paidAmount" => $item->total,
                                    "instructionID" => "01",
                                    "taxTotalTaxAmount" => $item->total_tax,
                                    "taxSubtotalTaxAmount" => $item->product_tax,
                                    "currencyCode" => $item->currency,
									"affected" => $item->affected,
									"non_affected" => $item->non_affected,
									"exonerated" => $item->exonerated,
									"tax_ICBPER" => $item->tax_ICBPER
                                    );

                $sales_ids[] = $item->id;

            }

            //datos a enviar
            $postData["header"] = $header;
            $postData["item"] = $itemArray;

            // Create the context for the request
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Authorization: application/json\r\n".
                                "Content-Type: application/json\r\n",
                    'content' => json_encode($postData)
                )
            ));

            // Send the request
            $url = $this->config->item('aws_url');
            $response = file_get_contents($url . $urlDoc, FALSE, $context);

            // Check for errors
            // if($response === FALSE){
                // die(json_encode($postData));
            // }

            if($response){

                $res = json_decode($response, TRUE);

                if($estado == 0){
                    $tipo = "AB";
                }else{
                    $tipo = "RB";
                }

                if($res[1] != 0 || $res[0] == 0){
                    $status = 1;
                }else{
                    $status = 2;
                }

                $summary = array(
                    'issue_date' => $issueDate,
                    'number' => $numero,
                    'file_name' => $supplierID."-".$type."-".$date."-".$numero,
                    'ticket' => $res[2],//*****Renato TRJ023 25/04/2019   ********
                    'reference_date' => $referenceDate,
                    'processed_date' => $processed_date,
                    'type' => $tipo,
                    'flg_response' => $res[0],
                    'error_code' => $res[1],
                    'status' => $status,
                    'uCrea' => $this->session->userdata('user_id'),
                    'fCrea' => date('Y-m-d H:i:s'),
                    'estado' => 1,
                );

                if($this->db->insert('send_invoice', $summary)){

                    $summary_id = $this->db->insert_id();

                    $data = array(
                        'send_invoice_id' => $summary_id,
                        'sale_id' => NULL,
                        'uCrea' => $this->session->userdata('user_id'),
                        'fCrea' => date('Y-m-d H:i:s'),
                        'estado' => 1,
                    );

                    foreach($sales_ids as $sale){
                        $data["sale_id"] = $sale;
                        $this->db->insert('send_invoice_items', $data);
                    }

                    return true;
                }

            }
        }

		return false;
    }

    public function send_summary_voided($processed_date, $referenceDate, $items, $numero, $estado = 1){

        if($items){

            $type = "RA";
            $supplierID = $this->session->userdata('ruc');
            $date = date("Ymd");

            $passFirma = $this->session->userdata('pass_firma');
            $usuSol = $this->session->userdata('user_sol');
            $passSol = $this->session->userdata('pass_sol');

            $issueDate = date("Y-m-d");
            $supplierName = $this->session->userdata('razon_social');

            if($this->session->userdata('beta') == 0){
                $urlDoc = "post/xmlenvio";
            }else{
                $urlDoc = "beta/post/xmlenvio";
            }

            $header = array(
                "type" => $type,
                "serieNumero" => (string)$numero,
                "supplierID" => $supplierID,
                "date" => $date,
                "passFirma" => $passFirma,
                "usuSol" => $usuSol,
                "passSol" => $passSol,
                "issueDate" => $issueDate,
                "referenceDate" => $referenceDate,
                "supplierName" => $supplierName
            );

            $sales_ids;
            $cant= 0;
            foreach ($items as $item) {

                $documentTypeCode = "01";
                $documentID = $item->ruc;
                $serieNumero = $item->invoice_id;
                $datos = explode('-', $serieNumero, 2);
                $serie = $datos[0];
                $sNumero = $datos[1];
                $reasonDescription = "CANCELACION";

                $cant= $cant + 1;
                $itemArray[$cant] = array(
                                    "documentTypeCode" => $documentTypeCode,
                                    "serie" => $serie,
                                    "numero" => $sNumero,
                                    "reasonDescription" => $item->grand_total
                                    );

                $sales_ids[] = $item->id;

            }

            //datos a enviar
            $postData["header"] = $header;
            $postData["item"] = $itemArray;

            // Create the context for the request
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Authorization: application/json\r\n".
                                "Content-Type: application/json\r\n",
                    'content' => json_encode($postData)
                )
            ));

            // Send the request
            $url = $this->config->item('aws_url');
            $response = file_get_contents($url . $urlDoc, FALSE, $context);

            // Check for errors
            // if($response === FALSE){
            //     die(json_encode($postData));
            // }

            if($response){

                $res = json_decode($response, TRUE);

                if($estado == 0){
                    $tipo = "AF";
                }else{
                    $tipo = "RF";
                }

                if($res[1] != 0 || $res[0] == 0){
                    $status = 1;
                }else{
                    $status = 2;
                }

                $summary_voided = array(
                    'issue_date' => $issueDate,
                    'number' => $numero,
                    'file_name' => $supplierID."-".$type."-".$date."-".$numero,
                    'ticket' => $res[2],//*****Renato TRJ023 25/04/2019   ********,
                    'reference_date' => $referenceDate,
                    'processed_date' => $processed_date,
                    'type' => $tipo,
                    'flg_response' => $res[0],
                    'error_code' => $res[1],
                    'status' => $status,
                    'uCrea' => $this->session->userdata('user_id'),
                    'fCrea' => date('Y-m-d H:i:s'),
                    'uActualiza' => NULL,
                    'fActualiza' => NULL,
                    'estado' => 1,
                );

                if($this->db->insert('send_invoice', $summary_voided)){

                    $summary_id = $this->db->insert_id();

                    $data = array(
                        'send_invoice_id' => $summary_id,
                        'sale_id' => NULL,
                        'uCrea' => $this->session->userdata('user_id'),
                        'fCrea' => date('Y-m-d H:i:s'),
                        'estado' => 1,
                    );

                    foreach($sales_ids as $sale){
                        $data["sale_id"] = $sale;
                        $this->db->insert('send_invoice_items', $data);
                    }

                    return true;
                }

            }
        }

		return false;
    }

    public function getSummaryByDate($fecha, $baja = null)
    {

        $this->db->select("send_invoice.*", FALSE)
        ->order_by('number', 'desc')
        ->where("issue_date LIKE '%" . $fecha . "%'");

        $this->db->where("type NOT LIKE '%RF%'");

        if($baja){
            $this->db->where("type LIKE '%AF%'");
        }else{
            $this->db->where("type NOT LIKE '%AF%'");
        }

        $q = $this->db->get_where('send_invoice', array('estado' => 1), 1);
          if( $q->num_rows() > 0 )
          {
            return $q->row();
          }
          return FALSE;
    }

    public function getNullSales($date, $endDate = NULL)
    {
        if(!$endDate){
            $endDate = strtotime ( '+1 day' , strtotime ( $date ) ) ;
            $endDate = date('Y-m-d 00:00:00', $endDate );
        }

        $this->db->select('sales.*', FALSE)
        ->where('sales.date >', $date)
        ->where('sales.date <', $endDate);

        $q = $this->db->get_where('sales', array('grand_total' => 0));

          if( $q->num_rows() > 0 )
          {
            return $q->row();
          }
          return FALSE;
    }

    public function updateSaleCab($id, $data )
    {
        if(  $this->db->update('sales', $data, array('id' => $id))   ) {
            return TRUE;
        }
        return false;
    }

    public function date_unique($str)
    {
        $q = $this->db->limit(1)
        ->where("type NOT LIKE '%RF%'")
        ->get_where('send_invoice', array('processed_date' => $str));
        // die(json_encode($q->num_rows()));
		
        $unique = false;
        if($q->num_rows() == 0){
            $unique = true;
        }

        return $unique;
    }

	public function fechaResumen($id){
		
		$q = $this->db->get_where('send_invoice', array('id' => $id), 1);
		
		if( $q->num_rows() > 0 )
          {
            return $q->row();
          }
          return FALSE;
		
	}
	
	//************TRJ042 - ALEXANDER ROCA - 30/04/2019************	
	public function getSummaryUndoSalesReenvio($date, $num = 2, $document_type = 1, $dateReferencia)
    {
        $endDate = strtotime ( '+1 day' , strtotime ( $date ) ) ;
        $endDate = date('Y-m-d 00:00:00', $endDate );
		
		$endDate2 = strtotime ( '+1 day' , strtotime ( $dateReferencia ) ) ;
        $endDate2 = date('Y-m-d 00:00:00', $endDate2 );

        $this->db->select('sales.*,customers.document_type_id as tipo_documento, customers.cf1 as dni, customers.cf2 as ruc', FALSE)
        ->join('customers', 'customers.id=sales.customer_id', 'left')
        ->order_by('sales.date')
        // ->where('sales.status !=', "DEBE")
        ->where('sales.invoice_id !=', NULL)
        ->where('sales.updated_at >', $date)
        ->where('sales.updated_at <', $endDate)
		->where('sales.date >', $dateReferencia)
        ->where('sales.date <', $endDate2)
        ->where('sales.estado =', 0);

        $q = $this->db->get_where('sales', array('document_type' => $document_type));

        $dateb = true;
        if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
                $data[] = $row;

                if($dateb){
                    $datet = explode(' ', $row->date, 2);
                    $firstDate = $datet[0];
                    $dateb = false;
                }
            }

            $items = (array)$data;
            $summary;
            $processed_date = $date;
            $date = $firstDate;

            $count = 0;
            foreach ($items as $item) {

                $count = $count + 1;
                $datef = explode(' ', $item->date, 2);
                $referenceDate = $datef[0];

                if($date != $referenceDate){
                    if($document_type == 2){
                        $this->send_summary_voided($processed_date, $date, $summary, $num, 0);
                    }else if($document_type == 1){
                        $this->send_summary($processed_date, $date, $summary, $num, 0);
                    }
                    $num = $num + 1;
                    unset($summary);
                    $summary[] = $item;
                }else{
                    $summary[] = $item;
                }

                $date = $referenceDate;

                if($q->num_rows() == $count){
                    if($document_type == 2){
                        $this->send_summary_voided($processed_date, $date, $summary, $num, 0);
                    }else if($document_type == 1){
                        $this->send_summary($processed_date, $date, $summary, $num, 0);
                    }
                }
            }

            return true;
        }
        return false;
    }
	
	public function addSummaryReenvio($fecha, $tipo, $fechaReferencia) {

        // $fecha = date('2018-10-29');
        $issueDate = date("Y-m-d");
        $RB = false;
        $AB = false;
        $AF = false;
		
		if($tipo=='RB'){
            $summary1 = $this->getSummaryByDate($issueDate);
			
            if($summary1->number){
                $number1 = (int)$summary1->number + 1;
            }else{
                $number1 = 1;
            }

            if($this->getSummaryAllSales($fecha,$number1)){
                $RB = true;
            }
			
			if($RB){
                return true;
            }
			
			return false;
			
		}
			
			
			
		if($tipo=='AB'){	
			$summary2 = $this->getSummaryByDate($issueDate);
			
            if($summary2->number){
                $number2 = (int)$summary2->number + 1;
            }else{
                $number2 = 1;
            }

            if($this->getSummaryUndoSalesReenvio($fecha,$number2,1,$fechaReferencia)){
                $AB = true;
            }
			
			if($AB){
                return true;
            }
			return false;
		}
			

		if($tipo=='AF'){
            $summary_voided = $this->getSummaryByDate($issueDate, $baja = true);
            if($summary_voided->number){
                $number3 = (int)$summary_voided->number + 1;
            }else{
                $number3 = 1;
            }

            if($this->getSummaryUndoSalesReenvio($fecha,$number3,2,$fechaReferencia)){
                $AF = true;
            }

            if($AF){
                return true;
            }
			return false;
		}
			
			
        return false;
    }
	
	public function updateSendInvoiceReenvio($id){
		if(  $this->db->update('send_invoice', array('estado'=>0), array('id' => $id))   ) {
		
			if($this->updateSendInvoiceItemsReenvio($id)){
				return TRUE;
			}
        }
        return false;
	}
	
	public function updateSendInvoiceItemsReenvio($id){
		if(  $this->db->update('send_invoice_items', array('estado'=>0), array('send_invoice_id' => $id))   ) {
            return TRUE;
        }
        return false;
	}
	
	//************TRJ042 - ALEXANDER ROCA - 30/04/2019************	
    //***********TRJO71 - KENY PONTE -3/12/2019*********************** */
    public function consultTicket(){
		
		$query = $this->db->query("SELECT ticket, file_name FROM `tec_send_invoice`
        WHERE status_ticket='' AND estado = 1 AND  ticket > 99999999999999");
        if($query){
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;	
	}

	public function insertStatusTicket($respuesta, $file_name, $ticket,$codigo){
	try {
        $sp = $this->db->query("CALL insert_consult_ticket('".$respuesta."','".$file_name."','".$ticket."',".$codigo.")");
       
		
		} catch (mysqli_sql_exception $e) {
             throw $e;
        }
	
	}
	
    public function ConsultTicktesForID($id){
       $query =  $this->db->query("SELECT file_name,ticket FROM tec_send_invoice WHERE  id=".$id);
       if($query->row()>0){
            foreach ($query->result() as $row) {
                $data = $row;
             }
            return $data;	
            }
    }
  //*********************TRJ071 - KENY PONTE - 3/12/2019********** */  
}
