<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transfers_model extends CI_Model
{

    public function __construct() {
        parent::__construct();

    }

    ////////////////////////////////////////////////////////////////////////CAB
    public function addCab($data = array()) {
        if ($this->db->insert('tec_transfers', $data)) {
            return true;
        }
        return false;
    }

    public function getCabByID($id) {

        $q = $this->db->get_where('transfers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    

    public function getCabDetByID($id) {

        $this->db->select("num, ref, ware1.warehouse as ware1 , ware2.warehouse as ware2 , CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' - ', " . $this->db->dbprefix('users') . ".email) as user, fCrea", FALSE)
            ->join('users', 'users.id=tec_transfers.uCrea', 'left')
            ->join('warehouses as ware1', 'ware1.id = tec_transfers.warehouse_origin_id')
            ->join('warehouses as ware2', 'ware2.id = tec_transfers.warehouse_destiny_id');
            

        $q = $this->db->get_where('tec_transfers', array('tec_transfers.id' => $id));

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getNextNum() {
        $this->db->select('MAX(id)+1 as maximo');
        $q = $this->db->get('tec_transfers');
        //var_dump($q ); exit;    
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteCab($id) {
        if ($this->db->delete('tec_transfers', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }


    /////////////////////////////////////////////////////////////////////////DET
    public function addDet($data = array()) {
        if ($this->db->insert('tec_transfer_items', $data)) {
            return true;
        }
        return false;
    }
    public function getDetByIdCab($idCab) {
        $this->db->select('tec_transfer_items.id, products.code as product_code, products.name as product_name, stock_prev, stock_new')
            ->join('products', 'products.id=tec_transfer_items.product_id')
            ->group_by('tec_transfer_items.id')
            ->order_by('id', 'asc');
        
        $q = $this->db->get_where('tec_transfer_items', array('tec_transfer_items.transfer_id' => $idCab));

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function deleteDet($id) {
        if ($this->db->delete('tec_transfer_items', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

}
