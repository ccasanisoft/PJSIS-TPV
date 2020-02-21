<?php defined('BASEPATH') OR exit('No direct script access allowed');

class credit_note_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	
	
	public function view_Serie(){
		$this->db->select('id, invoice_format, bill_format');
        $this->db->order_by("id", "asc");
		$this->db->where(array("estado"=>"1"));
        $query = $this->db->get("locals");
		
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}
	
	public function view_Serie_quantity(){
		$this->db->select('id');
        $this->db->where(array("estado"=>"1"));
        $query = $this->db->get("locals");

		$cont=0;
		
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $cont++;
            }
            return $cont;
        }
        return false;
	}
	
	public function valid_Serie_1(){
		$this->db->select('id, invoice_format, bill_format');
        $this->db->order_by("id", "asc");
		$this->db->limit(1);
        $query = $this->db->get("locals");

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
	}
	
	
	public function view_Serie_setting(){
		$this->db->select('setting_id AS id , invoice_format, bill_format');
        $this->db->order_by("setting_id", "asc");
        $query = $this->db->get("settings");

        if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}
	
	
	public function motive_NC(){
		
        $this->db->order_by("id", "asc");
        $query = $this->db->get("credit_note_motive");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}
	
	public function valid_invoice($invoice){
		
        $this->db->order_by("id", "desc");
		$this->db->limit(1);
		$this->db->where(array("invoice_id"=>$invoice));
		$query = $this->db->get("sales");
		
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}
	
	public function valid_customer($id){
		
        $this->db->order_by("id", "desc");
		$this->db->limit(1);
		$this->db->where(array("id"=>$id));
		$query = $this->db->get("customers");
		
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}
	
	public function valid_sale_items($id){
		
		$this->db->order_by("id", "desc");
		$this->db->where(array("sale_id"=>$id));
		$query = $this->db->get("sale_items");
		
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
		
	}
	
	public function format_invoce_NC(){
		
		$this->db->select("note_credit_invoice_format, note_credit_invoice_number, note_credit_bill_format, note_credit_bill_number");
		$this->db->order_by("setting_id", "desc");
		$this->db->limit(1);
		$query = $this->db->get("settings");
		
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
		
	}
	
	public function update_status_sale($Saleid){
		
		$this->db->where("id =",$Saleid);
		$this->db->set('estado', "0");
		
		if($this->db->update('sales')){
			
			return true;
		}
			
		return false;
		
	}
	
	public function insert_file_credit_note($data){
		$this->db->insert('credit_note_files', $data);
	}
	
	public function consult_tipe_motive_nc($id){
        $this->db->where("id =",$id);
		$this->db->limit(1);
        $query = $this->db->get("credit_note_motive");

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
	}
	
	public function consult_credit_note($id){
		$this->db->where("id =",$id);
		$this->db->limit(1);
        $query = $this->db->get("credit_note");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = (array)$row;
            }
            return $data;
        }
        return false;
	}
	
	public function consult_credit_note_items($id){
		$this->db->where("credit_note_id =",$id);
        $query = $this->db->get("credit_note_items");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = (array)$row;
            }
            return $data;
        }
        return false;
	}
	
	public function consult_customer($dni){
		$this->db->where("cf2 =".$dni."");
		$this->db->limit(1);
        $query = $this->db->get("customers");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}

	public function consult_sales($id){
		$this->db->where(array("id"=>$id));
		$this->db->limit(1);
        $query = $this->db->get("sales");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}
	
	public function getNoteCreditID($id)
    {
        $q = $this->db->get_where('credit_note', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
	
	public function getFileNameByNoteCreditId($id)
    {
        $q = $this->db->get_where('credit_note_files', array('credit_note_id' => $id), 1);;
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
	
	public function getFileByNoteCreditID($id)
    {
        $q = $this->db->get_where('credit_note_files', array('credit_note_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function updateFileNoteCredit($file, $noteCreditId){
		
		$this->db->update('credit_note_files', ['file_name' => $file], ['credit_note_id' => $noteCreditId]);
		
	}
	
	public function valid_sale_items_id($id){
		
		$this->db->order_by("id", "desc");
		$this->db->where(array("id"=>$id));
		$query = $this->db->get("sale_items");
		
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
		
	}

}