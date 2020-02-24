<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Products_model extends CI_Model
{


    public function __construct() {
        parent::__construct();

    }

    public function getAllProducts() {

        $this->db->where('estado =', 1);

        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function products_count($category_id = NULL) {
        if ($category_id) {
            $this->db->where('category_id', $category_id);
            $this->db->where('estado', 1);
            return $this->db->count_all_results("products");
        } else {
            $this->db->where('estado', 1);
            return $this->db->count_all_results("products");
        }
    }

    public function fetch_products($limit, $start, $category_id = NULL) {
        $this->db->select('name, code, barcode_symbology, price')
        ->limit($limit, $start)
        ->order_by("code", "asc")
        ->where('estado =', 1);

        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        $q = $this->db->get("products");

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductByCode($code) {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductStock($code) {
        $this->db->select('product_id, SUM(stock) AS stock');

        $q = $this->db->get_where('warehouse_stock', array('product_id' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addProduct($data, $items = array()) {
        if ($this->db->insert('products', $data)) {
            $product_id = $this->db->insert_id();
            if(! empty($items)) {
                foreach ($items as $item) {
                    $item['product_id'] = $product_id;
                    $this->db->insert('combo_items', $item);
                }
            }
            return true;
        }
        return false;
    }

    public function add_products($data = array()) {
        if ($this->db->insert_batch('products', $data)) {
            return true;
        }
        return false;
    }

    public function updatePrice($data = array()) {
        if ($this->db->update_batch('products', $data, 'code')) {
            return true;
        }
        return false;
    }

    public function updateProduct($id, $data = array(), $items = array(), $photo = NULL) {
        if ($photo) { $data['image'] = $photo; }
        if ($this->db->update('products', $data, array('id' => $id))) {
            if(! empty($items)) {
                $this->db->delete('combo_items', array('product_id' => $id));
                foreach ($items as $item) {
                    $item['product_id'] = $id;
                    $this->db->insert('combo_items', $item);
                }
            }
            return true;
        }
        return false;
    }

    public function getComboItemsByPID($product_id) {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('combo_items') . '.quantity as qty, ' . $this->db->dbprefix('products') . '.name as name')
        ->join('products', 'products.code=combo_items.item_code', 'left')
        ->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function deleteProduct($id) {

        if ($this->db->update('products', array('estado' => 0), array('id' => $id))) {
            return true;
        }
        // if ($this->db->delete('products', array('id' => $id))) {
        //     return true;
        // }
        return FALSE;
    }

    public function getProductNames($term, $limit = 10) {
        $this->db->where("type != 'combo' AND (name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%') AND estado = 1");
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

	//**************************TRJ047 - ALEXANDER ROCA - 08/07/2019***************
	public function validate_products($code){
		
		$this->db->where("code",$code);
		$this->db->where("estado",1);
		//$this->db->limit(1);
		$q = $this->db->get('products');
		
		//$q = $this->db->get_where('products', array('code' => $code, 'estado' => 1), 1);
        if ($q->num_rows() > 0) {
            //return $q->row();
			return TRUE;
        }
        return FALSE;
	}
	//**************************TRJ047 - ALEXANDER ROCA - 08/07/2019***************
   

    public function ultimoregistroProducts(){
       $this->db->where("estado",1);
       $this->db->limit(1);
       $this->db->order_by("id", "desc");
      
        $q = $this->db->get('products');
         if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
}
