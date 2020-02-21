<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Locals_model extends CI_Model
{

    public function __construct() {
        parent::__construct();

    }

    public function addLocal($data) {
        if ($this->db->insert('locals', $data)) {
            return true;
        }
        return false;
    }

    public function updateLocal($id, $data = NULL) {
        if ($this->db->update('locals', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteLocal($id) {
        if ($this->db->update('locals', array('estado' => 0), array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

}
