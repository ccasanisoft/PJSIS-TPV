<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getQtyAlerts() {
        $this->db->join('warehouse_stock', 'warehouse_stock.product_id=products.id')
        ->where('stock < alert_quantity', NULL, FALSE)
        ->where('products.estado !=', 0);
        return $this->db->count_all_results('products');
    }

    public function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllCustomers()
    {
		$this->db->where('estado', 1);
        $q = $this->db->get('customers');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCustomerType()
    {
        $q = $this->db->get('tec_customers_type');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    /*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/
    public function getAllDocumentType()
    {
        // $this->db->get('tec_document_type');

       //  $q=$this->db->where('id',6);
         $this->db->from('tec_document_type');
        $this->db->where('id !=',6);
         $this->db->where('id !=',8);
		 $this->db->where('id !=',0);
        $q=$this->db->get();
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    /*<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->*/

    public function getAllSuppliers()
    {
        $q = $this->db->get('suppliers');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllUsers()
    {
        $this->db->select("users.id as id, first_name, last_name, email, company, " . $this->db->dbprefix('groups') . ".name as group, active")
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id');
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUser($id = NULL)
    {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getExchange($date)
    {
        $q = $this->db->get_where('exchange', array('date' => $date), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductByID($id)
    {
        $q = $this->db->get_where('products', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getMotiveByAlias($alias)
    {
        $q = $this->db->get_where('mov_motive', array('alias' => $alias), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllCategories()
    {
        $this->db->order_by('code');
        $q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllWarehouses()
    {
        $this->db->order_by('warehouse');
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllmotives()
    {
        $this->db->order_by('motive');
        $q = $this->db->get('mov_motive');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllMakers()
    {
        $this->db->order_by('id');
        $q = $this->db->get('makers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getMakerByID($id)
    {
        $q = $this->db->get_where('makers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getMakerByName($name)
    {
        $q = $this->db->get_where('makers', array('maker' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllCanals()
    {
        $this->db->order_by('id');
        $q = $this->db->get('canals');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCanalByName($name)
    {
        $q = $this->db->get_where('canals', array('canal' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getDoctorByCmp($custom_field_1)//tec_sales
    {
        $q = $this->db->get_where('tec_doctors', array('cmp' => $custom_field_1), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllCategoriesOrdered()
    {   //$this->db->where('date >=', $dt)->order_by('date')->limit(5);
        $this->db->where("parent_category_id = 0")->order_by('code');
        $q = $this->db->get('categories');
        //get_where('products', array('id' => $id), 1)

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $this->db->where("parent_category_id = " . $row->id)->order_by('code');
                $q2 = $this->db->get('categories');
                $data2 = [];
                foreach (($q2->result()) as $row2) {
                        //nivel 3
                        $this->db->where("parent_category_id = " . $row2->id)->order_by('code');
                        $q3 = $this->db->get('categories');
                        $data3 = [];
                        foreach (($q3->result()) as $row3) {
                            $data3[] = $row3;
                            //nivel 4
                            $this->db->where("parent_category_id = " . $row3->id)->order_by('code');
                            $q4 = $this->db->get('categories');
                            $data4 = [];
                            foreach (($q4->result()) as $row4) {
                                $data4[] = $row4;
                            }
                            if(count($data4)>0)
                            {$row3->hijos = $data4; }
                        }
                        if(count($data3)>0)
                        {$row2->hijos = $data3; }

                    $data2[] = $row2;
                }
                if(count($data2)>0)
                {$row->hijos = $data2;  }

                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCategoryByID($id)
    {
        $q = $this->db->get_where('categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCategoryByCode($code)
    {
        $q = $this->db->get_where('categories', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCard($no)
    {
        $q = $this->db->get_where('gift_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getUpcomingEvents()
    {
        $dt = date('Y-m-d');
        $this->db->where('date >=', $dt)->order_by('date')->limit(5);
        if ($this->Settings->restrict_calendar) {
            $q = $this->db->get_where('calendar', array('user_id' => $this->session->userdata('iser_id')));
        } else {
            $q = $this->db->get('calendar');
        }
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUserGroup($user_id = NULL) {
        if($group_id = $this->getUserGroupID($user_id)) {
            $q = $this->db->get_where('groups', array('id' => $group_id), 1);
            if ($q->num_rows() > 0) {
                return $q->row();
            }
        }
        return FALSE;
    }

    public function getUserGroupID($user_id = NULL) {
        if($user = $this->getUser($user_id)) {
            return $user->group_id;
        }
        return FALSE;
    }

    public function getUserSuspenedSales()
    {
        $user_id = $this->session->userdata('user_id');
        $this->db->select('id, date, customer_name, hold_ref')
        ->order_by('id desc');
        //->limit(10);
        $q = $this->db->get_where('suspended_sales', array('created_by' => $user_id));
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getLocalByID($id)
    {
        $q = $this->db->get_where('locals', array('estado' => 1, 'id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllLocals()
    {
        $this->db->where('estado =', 1);

        $q = $this->db->get('locals');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

	//**************TRJ044 - ALEXANDER ROCA - 23/05/2019******************
	public function getAdvanced_sale(){
		
		$this->db->select('advanced_sale');
		$this->db->from('settings');
		$q = $this->db->get();
		
        if ($q->num_rows() > 0) {
			
            return $q->row();
        }
        return false;
		
	}
	//**************TRJ044 - ALEXANDER ROCA - 23/05/2019******************
	//*********************TRJ013 - Alexander roca - 11/06/2019********************
	public function getExchangeID()
    {
		$this->db->select('*');
		$this->db->from('exchange');
		$this->db->order_by('id', 'desc');
		$this->db->limit(1);
		$q = $this->db->get();
		
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    //*********************TRJ013 - Alexander roca - 11/06/2019********************
    //**************************TRJ088 - KENY PONTE - 02/09/2019************** */
    public function getPDFformat(){
		
		$this->db->select('pdf_format');
		$this->db->from('settings');
		$q = $this->db->get();
		
        if ($q->num_rows() > 0) {
			
            return $q->row();
        }
        return false;
		
    }
    public function getURLconsulta(){
		
		$this->db->select('footer');
		$this->db->from('settings');
		$q = $this->db->get();
		
        if ($q->num_rows() > 0) {
			
            return $q->row();
        }
        return false;
		
    }
     public function getUMProducto($id_product,$codigo){
		
		$this->db->select('presentation');
        $this->db->from('products');
        $this->db->where('id',$id_product );
        $this->db->or_where('code',$codigo);
		$q = $this->db->get();
		
        if ($q->num_rows() > 0) {
			
            return $q->row();
        }
        return false;
		
	}
	//****************************TRJ088 - KENY PONTE - 02/09/2019************** */
}
