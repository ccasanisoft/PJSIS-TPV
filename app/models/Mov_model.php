<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mov_model extends CI_Model
{

    public function __construct() {
        parent::__construct();

    }

    public function addMovement($data = array()) {
        if ($this->db->insert('tec_product_mov', $data)) {
            return true;
        }
        return false;
    }

    public function deleteMovement($id, $idMotivo) {
        if ($this->db->delete('tec_product_mov', array('origin_id' => $id, 'idMotivo' => $idMotivo))) {
            return true;
        }
        return FALSE;
    }

/*
    public function getCustomersTypeByID($id) {
        $q = $this->db->get_where('tec_customers_type', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addCustomersType($data = array()) {
        if ($this->db->insert('tec_customers_type', $data)) {
            return true;
        }
        return false;
    }

    public function updateCustomersType($id, $data = array()) {
        if ($this->db->update('tec_customers_type', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteCustomersType($id) {
        if ($this->db->delete('tec_customers_type', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    */

}
