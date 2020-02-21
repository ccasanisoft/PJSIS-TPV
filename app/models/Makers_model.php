<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Makers_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    public function getMakerByID($id) {
        $q = $this->db->get_where('tec_makers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addMaker($data = array()) {
        if ($this->db->insert('tec_makers', $data)) {
            return true;
        }
        return false;
    }

    public function updateMaker($id, $data = array()) {
        if ($this->db->update('tec_makers', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteMaker($id) {
        if ($this->db->delete('tec_makers', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

}
