<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_model extends CI_Model
{

	public function __construct() {
		parent::__construct();

	}

	public function getSaleByID($id)
    {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getFileBySaleID($id)
    {
        $q = $this->db->get_where('sales_files', array('sale_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSaleItemsByID($id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('sale_items', array('sale_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = (array)$row;//******************************************ALEXANDER ROCA - TRJ017 - 25/03/2019****************
            }
            return $data;
        }
    }

	public function deleteInvoice($id) {
		if($this->db->delete('sale_items', array('sale_id' => $id)) && $this->db->delete('sales', array('id' => $id))) {
			return true;
		}
		return FALSE;
	}

	public function deleteOpenedSale($id) {
		if($this->db->delete('suspended_items', array('suspend_id' => $id)) && $this->db->delete('suspended_sales', array('id' => $id))) {
			return true;
		}
		return FALSE;
	}

	public function getSalePayments($sale_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addPayment($data = array())
    {
        if ($this->db->insert('payments', $data)) {
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['gc_no']);
                $this->db->update('gift_cards', array('balance' => ($gc->balance - $data['amount'])), array('card_no' => $data['gc_no']));
            }
            $this->syncSalePayments($data['sale_id']);
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array())
    {
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $this->syncSalePayments($data['sale_id']);
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->syncSalePayments($opay->sale_id);
            return true;
        }
        return FALSE;
    }

    public function syncSalePayments($id)
    {
        $sale = $this->getSaleByID($id);
        $payments = $this->getSalePayments($id);
        $paid = 0;
        if($payments) {
        	foreach ($payments as $payment) {
        		$paid += $payment->amount;
        	}
        }
        $status = $paid <= 0 ? lang('st_due') : $sale->status;
	    if ($this->tec->formatDecimal($sale->grand_total) > $this->tec->formatDecimal($paid) && $paid > 0) {
            $status = lang('st_partial');
        } elseif ($this->tec->formatDecimal($sale->grand_total) <= $this->tec->formatDecimal($paid)) {
            $status = lang('st_paid');
        }

        if ($this->db->update('sales', array('paid' => $paid, 'status' => $status), array('id' => $id))) {
            return true;
        }

        return FALSE;
    }

    public function addSale($data, $items) {
        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();
            foreach ($items as $item) {
                $item['sale_id'] = $sale_id;
                // unset($item['name'],$item['code']);
                $this->db->insert('sale_items', $item);
            }

            if ($this->addSaleStock($data, $items)){
                return $sale_id;
            }
        }
        return false;
    }

    public function addSaleStock($data, $items) {

        $warehouse_id = $data['warehouse_id'];

        foreach ($items as $item) {

            $idProduct = $item['product_id'];
            $new_stock = $item['quantity'];

            $q = $this->db->get_where('warehouse_stock', array('warehouse_id' => $warehouse_id, 'product_id' => $item['product_id']), 1);

            if ( $q->num_rows() > 0) {//EDIT
                $fila = $q->row();
                $stock = array(
                    'warehouse_id' => $warehouse_id,
                    'product_id' => $idProduct,
                    'stock' => $fila->stock - $new_stock
                );
                $this->db->update('warehouse_stock', $stock, array('id' => $fila->id));

            }else{//NEW
                $stock = array(
                    'warehouse_id' => $warehouse_id,
                    'product_id' => $idProduct,
                    'stock' => 0 - $new_stock
                );
                $this->db->insert('warehouse_stock', $stock);
            }
        }

        return true;
    }
	//***********TRJ0003 - ALEXADER ROCA - 13/03/2019************
    public function getProductNames($term,$validacion, $limit = 100) {//*************** -TRJ0002- ALEXADER ROCA -13/03/2019 *************

        // switch($this->session->userdata('negocio')){
        //     case 0:
                $this->db->where("type != 'combo' AND (" . $validacion . " OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%') AND estado = 1");
	//***********TRJ0003 - ALEXADER ROCA - 13/03/2019************
        //         break;
        //     case 1:
        //         $this->db->where("type != 'combo' AND (name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%' OR custom_field_1 LIKE '%" . $term . "%' OR custom_field_2 LIKE '%" . $term . "%'  ) AND estado = 1");
        //         break;
        // }

        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	//***********************************TRJ028 - ALEXANDER ROCA - 04/04/2019************************
	public function getIDwarehouse ($idLocal){
		
		$q = $this->db->get_where('locals', array('id' => $idLocal), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
		
	}

	public function getIDwarehouseDefault(){
		
		$q = $this->db->get_where('settings', array('setting_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
		
	}
	//***********************************TRJ028 - ALEXANDER ROCA - 04/04/2019************************
	//***************TRJ029 - ALEXANDER ROCA - 05/04/2019*************************
	public function sale ($id){
		
		$q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
		
	}
	
	public function customers_sale ($id){
		
		$q = $this->db->get_where('customers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
		
	}
	
	/*
	public function sale_Invoce($id){
		
		$q = $this->db->get_where('sales', array('sale_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
		
	}*/
	
	public function getSendInvoce($id){//invoice_id){
		//**********TRJ041 - ALEXANDER ROCA - 29/04/2019**************
		$this->db->join('send_invoice_items', 'send_invoice_items.send_invoice_id = send_invoice.id','right');
		//$this->db->where("response_descrip LIKE '%ha sido aceptada%' AND file_name like '%".$invoice_id."%'");
		//$this->db->where("(status = 4 or status = 2 or status = 3) AND send_invoice_items.sale_id = ".$id."");
		$this->db->where("(status = 4 or status = 2) AND send_invoice_items.sale_id = ".$id."");
		$this->db->limit(1);
		$q = $this->db->get('send_invoice');
		//$q = $this->db->get_where('send_invoice', array('id' => $id), 1);
		
        if ($q->num_rows() > 0) {
			
            return true;
        }
        return false;
		//**********TRJ041 - ALEXANDER ROCA - 29/04/2019**************
	}
	//***************TRJ029 - ALEXANDER ROCA - 05/04/2019*************************
	
	public function update_status_send_invoice($id){
		
		$latest=$this->send_invoce_latest($id);
		
		$sql = "UPDATE tec_send_invoice as a JOIN tec_send_invoice_items as b ON b.send_invoice_id = a.id SET a.estado= ?  WHERE a.id != ? and b.sale_id = ? ";
		if($this->db->query($sql, array("0",$latest->send_invoice_id,$id))){
		
			if($this->update_status_send_invoice_items($id,$latest->send_invoice_id)){
			
				return true;
			}
			
        }
        return false;
	}
	
	public function send_invoce_latest($id){
		
		$this->db->select('*');
		$this->db->from('send_invoice envio');
		$this->db->join('send_invoice_items item', 'item.send_invoice_id = envio.id');
		$this->db->where("item.sale_id =".$id." and envio.fCrea=(SELECT max(e.fCrea) FROM tec_send_invoice e RIGHT join tec_send_invoice_items i on i.send_invoice_id=e.id WHERE i.sale_id=".$id.")");
		$q = $this->db->get();
		
        if ($q->num_rows() > 0) {
			
            return $q->row();
        }
        return false;
		
	}
	
	public function update_status_send_invoice_items($Saleid, $SendinvoceId){
		
		$this->db->where("send_invoice_id !=",$SendinvoceId);
		$this->db->where("sale_id =",$Saleid);
		$this->db->set('estado', "0");
		
		if($this->db->update('send_invoice_items')){
			
			return true;
		}
			
		return false;
		
	}
	
	//**********TRJ045 - ALEXANDER ROCA - 21/05/2019**************
	public function update_sale_re_XML($id, $resp){
		
		$this->db->where("id =",$id);
		$this->db->set('flg_response', $resp['flg_response']);
		$this->db->set('error_code', $resp['error_code']);
		$this->db->set('response_descrip', $resp['response_descrip']);
		$this->db->set('digest_value', $resp['digest_value']);
		
		if($this->db->update('sales')){
			
			return true;
		}
			
		return false;
		
	}

	//**********TRJ045 - ALEXANDER ROCA - 21/05/2019**************
	//**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************
	public function remove_sale($id){
		
		$this->db->where("id =",$id);
		$this->db->set('estado', 2);
		
		if($this->db->update('sales')){
			
			return true;
		}
			
		return false;
		
	}
	//**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************
	//*****************TRJ073 - KENY PONTE - 13/09/2019********************* */
    public function getDocument($document){
        $this->db->select('document_type_id');
        $this->db->from('customers');
        $this->db->where('cf1',$document);
        $q=$this->db->get();
        if($q->num_rows() > 0 ){
            return $q->row();
        }
       return false;
        
    }
    //*****************TRJ073 - KENY PONTE - 13/09/2019********************* */
}
