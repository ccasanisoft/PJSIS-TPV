<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchases_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getPurchaseByID($id) {
        $q = $this->db->get_where('purchases', array('id' => $id), 1);
        if( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllPurchaseItems($purchase_id) {
        $this->db->select('purchase_items.*, products.code as product_code, products.name as product_name')
            ->join('products', 'products.id=purchase_items.product_id', 'left')
            ->group_by('purchase_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addPurchase($data, $items) {
        if ($this->db->insert('purchases', $data)) {
            $purchase_id = $this->db->insert_id();
            foreach ($items as $item) {
                $item['purchase_id'] = $purchase_id;
                $this->db->insert('purchase_items', $item);
            }

            if ($this->addPurchaseStock($data, $items)){
                return $purchase_id;
            }
        }
        return false;
    }

    public function getPurchasePayments($purchase_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('purchase_pay', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('purchase_pay', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addPayment($data = array())
    {
        if ($this->db->insert('purchase_pay', $data)) {
            $this->syncPurchasePayments($data['purchase_id']);
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array())
    {
        if ($this->db->update('purchase_pay', $data, array('id' => $id))) {
            $this->syncPurchasePayments($data['purchase_id']);
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('purchase_pay', array('id' => $id))) {
            $this->syncPurchasePayments($opay->purchase_id);
            return true;
        }
        return FALSE;
    }

    public function syncPurchasePayments($id)
    {
        $purchase = $this->getPurchaseByID($id);
        $payments = $this->getPurchasePayments($id);
        $paid = 0;
        if($payments) {
        	foreach ($payments as $payment) {
        		$paid += $payment->amount;
        	}
        }
        $status = $paid <= 0 ? lang('st_due') : $purchase->status;
	    if ($this->tec->formatDecimal($purchase->grand_total) > $this->tec->formatDecimal($paid) && $paid > 0) {
            $status = lang('st_partial');
        } elseif ($this->tec->formatDecimal($purchase->grand_total) <= $this->tec->formatDecimal($paid)) {
            $status = lang('st_paid');
        }

        if ($this->db->update('purchases', array('paid' => $paid, 'status' => $status), array('id' => $id))) {
            return true;
        }

        return FALSE;
    }

    public function addPurchaseStock($data, $items) {

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
                    'stock' => $fila->stock + $new_stock
                );
                $this->db->update('warehouse_stock', $stock, array('id' => $fila->id));

            }else{//NEW
                $stock = array(
                    'warehouse_id' => $warehouse_id,
                    'product_id' => $idProduct,
                    'stock' => $new_stock
                );
                $this->db->insert('warehouse_stock', $stock);
            }
        }

        return true;
    }

    public function updatePurchaseCab($id, $data )
    {
        if(  $this->db->update('purchases', $data, array('id' => $id))   ) {
            return TRUE;
        }
        return false;
    }

    public function updatePurchase($id, $data = NULL, $items = array()) {
        $oitems = $this->getAllPurchaseItems($id);
        // foreach ($oitems as $oitem) {
        //     $product = $this->site->getProductByID($oitem->product_id);
        //     // $this->db->update('products', array('quantity' => ($product->quantity-$oitem->quantity)), array('id' => $product->id));
        // }
        $this->restPurchaseStock($data['warehouse_id'], $oitems);

        if ($this->db->update('purchases', $data, array('id' => $id)) && $this->db->delete('purchase_items', array('purchase_id' => $id))) {
            foreach ($items as $item) {
                $item['purchase_id'] = $id;
                if($this->db->insert('purchase_items', $item)) {
                    $product = $this->site->getProductByID($item['product_id']);
                    // $this->db->update('products', array('quantity' => ($product->quantity+$item['quantity'])), array('id' => $product->id));
                }
            }
            if ($this->addPurchaseStock($data, $items)){
                return true;
            }
        }
        return false;
    }

    public function deletePurchase($id) {
        $odata = $this->getPurchaseByID($id);
        $oitems = $this->getAllPurchaseItems($id);

        // foreach ($oitems as $oitem) {
            // $product = $this->site->getProductByID($oitem->product_id);
        //     // $this->db->update('products', array('quantity' => ($product->quantity-$oitem->quantity)), array('id' => $product->id));
        // }
        if ($this->restPurchaseStock($odata->warehouse_id, $oitems)){

            $this->db->update('purchases', array('estado' => 0), array('id' => $id));
            $this->db->update('purchase_items', array('estado' => 0), array('purchase_id' => $id));

            return true;
        }

        // if ($this->db->delete('purchases', array('id' => $id)) && $this->db->delete('purchase_items', array('purchase_id' => $id))) {
        //     return true;
        // }
        return FALSE;
    }

    public function restPurchaseStock($warehouse_id, $items) {

        // $warehouse_id = $data['warehouse_id'];

        foreach ($items as $item) {

            $idProduct = $item->product_id;
            $rest_stock = $item->quantity;

            $q = $this->db->get_where('warehouse_stock', array('warehouse_id' => $warehouse_id, 'product_id' => $item->product_id), 1);

            if ( $q->num_rows() > 0) {//EDIT
                $fila = $q->row();
                $stock = array(
                    'warehouse_id' => $warehouse_id,
                    'product_id' => $idProduct,
                    'stock' => $fila->stock - $rest_stock
                );
                $this->db->update('warehouse_stock', $stock, array('id' => $fila->id));

            }else{//NEW
                $stock = array(
                    'warehouse_id' => $warehouse_id,
                    'product_id' => $idProduct,
                    'stock' => 0 - $rest_stock
                );
                $this->db->insert('warehouse_stock', $stock);
            }
        }

        return true;
    }

	//***********TRJ0003 - ALEXADER ROCA - 13/03/2019************

    public function getProductNames($term,$validacion ,$limit = 100) {//*************** -TRJ0002- ALEXADER ROCA -13/03/2019 *************



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

    public function getExpenseByID($id) {
        $q = $this->db->get_where('expenses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addExpense($data = array()) {
        if ($this->db->insert('expenses', $data)) {
            return true;
        }
        return false;
    }

    public function updateExpense($id, $data = array()) {
        if ($this->db->update('expenses', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteExpense($id) {
        if ($this->db->delete('expenses', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

}
