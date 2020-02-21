<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Warehouses_model extends CI_Model
{

    public function __construct() {
        parent::__construct();

    }

     public function getWarehouses()
    {
        $q = $this->db->get('tec_warehouses');
     
         if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getWarehouseByID($id) {
        $q = $this->db->get_where('tec_warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addWarehouse($data = array()) {
        if ($this->db->insert('tec_warehouses', $data)) {
            return true;
        }
        return false;
    }

    public function updateWarehouse($id, $data = array()) {
        if ($this->db->update('tec_warehouses', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteWarehouse($id) {
        if ($this->db->delete('tec_warehouses', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getStockById($id) {
        $q = $this->db->get_where('tec_warehouse_stock', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

      public function getStockByIdProduc($id) {
        $q = $this->db->get_where('tec_warehouse_stock', array('product_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getStockDetById($id) {

        $this->db->select("tec_warehouse_stock.id, 
                        tec_warehouse_stock.warehouse_id, 
                        tec_warehouse_stock.product_id, 
                        tec_warehouse_stock.stock, 
                        name", FALSE)
                ->join('products', 'products.id=tec_warehouse_stock.product_id')                
                ->where('products.estado =', 1)
                ->group_by('tec_warehouse_stock.id');
                $q = $this->db->get_where('tec_warehouse_stock', array('tec_warehouse_stock.id' => $id), 1);
        //$q = $this->db->get_where('tec_warehouse_stock', array('id' => $id), 1);
//var_dump($q); exit;
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function add_stock($data){
       $this->db->insert('tec_warehouse_stock',$data);
       return TRUE;
    }

       public function updateWarehouse_stock($id, $data = array()) {
        if ($this->db->update('tec_warehouse_stock', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }


}



