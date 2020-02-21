<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pos_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }
	//***********TRJ0003 - ALEXADER ROCA - 13/03/2019************
    public function getProductNames($term,$validacion, $limit = 100)//**************** -TRJ0002- ALEXADER ROCA -13/03/2019**********
    {   //Anterior
		//$this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        //Modificado 24 Julio
        $this->db->select("products.id, code, name, category_id, currency, price, image, tax, cost, tax_method, ifnull(stock,0) as quantity, barcode_symbology, type, details, alert_quantity, maker_id, custom_field_1, custom_field_2, presentation", false)
            ->join('warehouse_stock', 'warehouse_stock.product_id=products.id', 'left');
        $this->db->where("(" . $validacion . " OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%') AND estado = 1");
    //***********TRJ0003 - ALEXADER ROCA - 13/03/2019************
    $this->db->group_by("products.id");/*TRJ0003 - AMADOR*/
		$this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getTodaySales()
    {
        $date = date('Y-m-d 00:00:00');
        // $this->db->select('SUM( COALESCE( total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        $this->db->select('SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( total * exchange, 2 ), total ), 0 ) ) AS total, SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2), ' . $this->db->dbprefix('payments') . '.amount), 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)
            ->where('sales.estado !=', 0);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCCSales()
    {
        $date = date('Y-m-d 00:00:00');
        // $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( total * exchange, 2 ), total ), 0 ) ) AS total, SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2), ' . $this->db->dbprefix('payments') . '.amount), 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'CC')
            ->where('sales.estado !=', 0);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashSales()
    {
        $date = date('Y-m-d 00:00:00');
        // $this->db->select('SUM( COALESCE( total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        $this->db->select('SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( total * exchange, 2 ), total ), 0 ) ) AS total, SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2), ' . $this->db->dbprefix('payments') . '.amount), 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'cash')
            ->where('sales.estado !=', 0);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayRefunds()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', false)
            ->join('return_sales', 'return_sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayExpenses()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', false)
            ->where('date >', $date);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashRefunds()
    {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', false)
            ->join('return_sales', 'return_sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'cash');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayChSales()
    {
        $date = date('Y-m-d 00:00:00');
        // $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( total * exchange, 2 ), total ), 0 ) ) AS total, SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2), ' . $this->db->dbprefix('payments') . '.amount), 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'Cheque')
            ->where('sales.estado !=', 0);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayStripeSales()
    {
        $date = date('Y-m-d 00:00:00');
        // $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( total * exchange, 2 ), total ), 0 ) ) AS total, SUM( COALESCE( IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2), ' . $this->db->dbprefix('payments') . '.amount), 0 ) ) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'stripe')
            ->where('sales.estado !=', 0);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        // $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        $this->db->select('SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND(grand_total * exchange, 2), grand_total), 0)) AS total, SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2),  ' . $this->db->dbprefix('payments') . '.amount), 0)) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)
            ->where('sales.estado =', 1);//**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************
        $this->db->where('payments.created_by', $user_id);
		$this->db->where('sales.canal_id', 1);
		
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCCSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        // $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        //**********************TRJ026 - ALEXANDER ROCA - 02/04/2019 **********
		$this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, IFNULL(SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND(grand_total * exchange, 2), grand_total), 0)), 0) AS total, SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2),  ' . $this->db->dbprefix('payments') . '.amount), 0)) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'CC')
            ->where('sales.estado =', 1);//**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************
		$this->db->where('sales.canal_id', 1);
        $this->db->where('payments.created_by', $user_id);
		//**********************TRJ026 - ALEXANDER ROCA - 02/04/2019 **********
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCashSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
		//**********************TRJ026 - ALEXANDER ROCA - 02/04/2019 **********
        // $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        $this->db->select('IFNULL(SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND(grand_total * exchange, 2), grand_total), 0)), 0) AS total,
                           SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2),  ' . $this->db->dbprefix('payments') . '.amount), 0)) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'cash')
            ->where('sales.estado =', 1);//**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************
		$this->db->where('sales.canal_id', 1);
        $this->db->where('payments.created_by', $user_id);
		//**********************TRJ026 - ALEXANDER ROCA - 02/04/2019 **********
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterRefunds($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', false)
            ->join('return_sales', 'return_sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);
            // ->where('sales.estado !=', 0);
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCashRefunds($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', false)
            ->join('return_sales', 'return_sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'cash');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterExpenses($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        // $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE)
        $this->db->select('SUM( COALESCE( IF( currency="USD", ROUND( amount * exchange, 2 ), amount), 0 ) ) AS total', false)
            ->where('date >', $date);
        $this->db->where('created_by', $user_id);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterChSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        // $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        //**********************TRJ026 - ALEXANDER ROCA - 02/04/2019 **********
		$this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques,
            IFNULL(SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND(grand_total * exchange, 2), grand_total), 0)), 0) AS total,
            SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2),  ' . $this->db->dbprefix('payments') . '.amount), 0)) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'Cheque')
            ->where('sales.estado =', 1);//**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************
		$this->db->where('sales.canal_id', 1);
        $this->db->where('payments.created_by', $user_id);
		//**********************TRJ026 - ALEXANDER ROCA - 02/04/2019 **********
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterStripeSales($date, $user_id = null)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
		//**********************TRJ026 - ALEXANDER ROCA - 02/04/2019 **********
        // $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( ' . $this->db->dbprefix('payments') . '.amount, 0 ) ) AS paid', FALSE)
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques,
            IFNULL(SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND(grand_total * exchange, 2), grand_total), 0)), 0) AS total,
            SUM(COALESCE(IF( ' . $this->db->dbprefix('sales') . '.currency="USD", ROUND( ' . $this->db->dbprefix('payments') . '.amount * exchange, 2),  ' . $this->db->dbprefix('payments') . '.amount), 0)) AS paid', false)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'stripe')
			->where('sales.estado =', 1);//**********TRJ074 - ALEXANDER ROCA - 23/08/2019**************
		$this->db->where('sales.canal_id', 1);
        $this->db->where('payments.created_by', $user_id);
		//**********************TRJ026 - ALEXANDER ROCA - 02/04/2019 **********
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function products_count($category_id)
    {
        $this->db->where('category_id', $category_id)->from('products');
        $this->db->where('estado =', 1);
        return $this->db->count_all_results();
    }

    public function fetch_products($category_id, $limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->where('category_id', $category_id);
        $this->db->where('estado =', 1);
        $this->db->order_by("name", "asc");
        $query = $this->db->get("products");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function registerData($user_id)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('registers', array('user_id' => $user_id, 'status' => 'open'), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function openRegister($data)
    {
        if ($this->db->insert('registers', $data)) {
            return true;
        }
        return false;
    }

    public function getOpenRegisters()
    {
        $this->db->select("date, user_id, cash_in_hand, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' - ', " . $this->db->dbprefix('users') . ".email) as user", false)
            ->join('users', 'users.id=pos_register.user_id', 'left');
        $q = $this->db->get_where('registers', array('status' => 'open'));
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;

    }

    public function closeRegister($rid, $user_id, $data)
    {
        if (!$rid) {
            $rid = $this->session->userdata('register_id');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($data['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_sales', array('created_by' => $user_id));
        } elseif ($data['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_sales', array('created_by' => $data['transfer_opened_bills']), array('created_by' => $user_id));
        }
        if ($this->db->update('registers', $data, array('id' => $rid, 'user_id' => $user_id))) {

            return true;
        }
        return false;
    }

    public function getCustomerByID($id)
    {
        $q = $this->db->get_where('customers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getStockByProductID($product_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouse_stock', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductByCode($code)
    {

        // $this->db->select("products.id, code, name, category_id, price, image, tax, cost, tax_method, ifnull(stock,0) as quantity, barcode_symbology, type, details, alert_quantity, maker_id, custom_field_1, custom_field_2, presentation", false)
        //     ->join('warehouse_stock', 'warehouse_stock.product_id=products.id', 'left')
        //     ->where('products.estado =', 1);

        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getInvoiceId($invoice_id)
    {
        $q = $this->db->get_where('sales', array('invoice_id' => $invoice_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateStock($sale_id)
    {
        $items = $this->getAllSaleItems2($sale_id);
        foreach ($items as $item) {
            $item->sale_id = $sale_id;
            $product = $this->site->getProductByID($item->product_id);
            if ($product->type === 'standard') {
                $warehouse_id = $this->Settings->default_warehouse;
                $idProduct = $item->product_id;
                $new_stock = $item->quantity;

                $q = $this->db->get_where('warehouse_stock', array('warehouse_id' => $warehouse_id, 'product_id' => $idProduct), 1);
                if ($q->num_rows() > 0) {//EDIT
                    $fila = $q->row();
                    $stock = array(
                        'warehouse_id' => $warehouse_id,
                        'product_id' => $idProduct,
                        'stock' => $fila->stock - $new_stock
                    );
                    $this->db->update('warehouse_stock', $stock, array('id' => $fila->id));
                } else {//NEW
                    $stock = array(
                        'warehouse_id' => $warehouse_id,
                        'product_id' => $idProduct,
                        'stock' => 0 - $new_stock
                    );
                    $this->db->insert('warehouse_stock', $stock);
                }
            } elseif ($product->type == 'combo') {
                $combo_items = $this->pos_model->getComboItemsByPID($product->id);
                foreach ($combo_items as $combo_item) {
                    $cpr = $this->site->getProductByID($combo_item->id);
                    if ($cpr->type == 'standard') {
                        $warehouse_id = $this->Settings->default_warehouse;
                        $idProduct = $cpr->id;
                        $new_stock = $combo_item->qty * $item->quantity;

                        $q = $this->db->get_where('warehouse_stock', array('warehouse_id' => $warehouse_id, 'product_id' => $idProduct), 1);
                        if ($q->num_rows() > 0) {//EDIT
                            $fila = $q->row();
                            $stock = array(
                                'warehouse_id' => $warehouse_id,
                                'product_id' => $idProduct,
                                'stock' => $fila->stock - $new_stock
                            );
                            $this->db->update('warehouse_stock', $stock, array('id' => $fila->id));
                        } else {//NEW
                            $stock = array(
                                'warehouse_id' => $warehouse_id,
                                'product_id' => $idProduct,
                                'stock' => 0 - $new_stock
                            );
                            $this->db->insert('warehouse_stock', $stock);
                        }
                    }
                }
            }
        }
    }

    public function updateItemStock($item)
    {
//******************************TRJ017 - ALEXANDER ROCA - 26/03/2019 **************************************
        $product = $this->site->getProductByID($item['product_id']);//($item->product_id);
        if ($product->type === 'standard') {
            $warehouse_id = $this->Settings->default_warehouse;
            $idProduct = $item['product_id'];//$item->product_id;
            $new_stock = $item['quantity'];//$item->quantity;

            $q = $this->db->get_where('warehouse_stock', array('warehouse_id' => $warehouse_id, 'product_id' => $idProduct), 1);
            if ($q->num_rows() > 0) {//EDIT
                $fila = $q->row();
                $stock = array(
                    'warehouse_id' => $warehouse_id,
                    'product_id' => $idProduct,
                    'stock' => $fila->stock - $new_stock
                );
                $this->db->update('warehouse_stock', $stock, array('id' => $fila->id));
            } else {//NEW
                $stock = array(
                    'warehouse_id' => $warehouse_id,
                    'product_id' => $idProduct,
                    'stock' => 0 - $new_stock
                );
                $this->db->insert('warehouse_stock', $stock);
            }
        } elseif ($product->type == 'combo') {
            $combo_items = $this->pos_model->getComboItemsByPID($product->id);
            foreach ($combo_items as $combo_item) {
                $cpr = $this->site->getProductByID($combo_item->id);
                if ($cpr->type == 'standard') {
                    $warehouse_id = $this->Settings->default_warehouse;
                    $idProduct = $cpr->id;
                    $new_stock = $combo_item->qty * $item['quantity'];//$item->quantity;
//****************************TRJ017 - ALEXANDER ROCA - 26/03/2019 ***********************************
                    $q = $this->db->get_where('warehouse_stock', array('warehouse_id' => $warehouse_id, 'product_id' => $idProduct), 1);
                    if ($q->num_rows() > 0) {//EDIT
                        $fila = $q->row();
                        $stock = array(
                            'warehouse_id' => $warehouse_id,
                            'product_id' => $idProduct,
                            'stock' => $fila->stock - $new_stock
                        );
                        $this->db->update('warehouse_stock', $stock, array('id' => $fila->id));
                    } else {//NEW
                        $stock = array(
                            'warehouse_id' => $warehouse_id,
                            'product_id' => $idProduct,
                            'stock' => 0 - $new_stock
                        );
                        $this->db->insert('warehouse_stock', $stock);
                    }
                }
            }
        }
    }

    public function update_invoice_Number($document_type)
    {
        //Obtener el Local
        $local_id = $this->session->userdata('local');
        $local = $this->site->getLocalByID($local_id);

        if ($document_type == 2) {          //Factura
            $new_number = (int)$this->Settings->invoice_number + 1;
        } else if ($document_type == 1) {   //Boleta
            $new_number = (int)$this->Settings->bill_number + 1;
        } else if ($document_type == 3) {  //*****Renato TRJ023 25/04/2019   ********
            $new_number = (int)$this->Settings->nventa_number + 1;//*****Renato TRJ023 25/04/2019   ********
        }

        if ($local->invoice_format && $document_type != 3){

            if ($document_type == 2) {
                $this->db->update('locals', array('invoice_number' => $new_number), array('id' => $local_id));
            } else if ($document_type == 1) {
                $this->db->update('locals', array('bill_number' => $new_number), array('id' => $local_id));
            }

        } else {

            if ($document_type == 2) {
                $this->db->update('settings', array('invoice_number' => $new_number), array('setting_id' => 1));
            } else if ($document_type == 1) {
                $this->db->update('settings', array('bill_number' => $new_number), array('setting_id' => 1));
            } else if ($document_type == 3) {
                $this->db->update('settings', array('nventa_number' => $new_number), array('setting_id' => 1));//*****Renato TRJ023 25/04/2019   ********
            }

        }
		
		return true;

    }

    public function addSendInvoice($reg_invoice, $sale_id)
    {

        if ($this->db->insert('send_invoice', $reg_invoice)) {

            $summary_id = $this->db->insert_id();
			
            $reg_item = array(
                'send_invoice_id' => $summary_id,
                'sale_id' => $sale_id,
                'uCrea' => $this->session->userdata('user_id'),
                'fCrea' => date('Y-m-d H:i:s'),
                'estado' => 1,
            );

            $this->db->insert('send_invoice_items', $reg_item);

            return true;
        }

        return false;

    }

    public function addSale($data, $items, $payment = array(), $did = null)
    {
        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();
            foreach ($items as $item) {
                $item['sale_id'] = $sale_id;
                if ($this->db->insert('sale_items', $item)) {

                    if ($payment['amount'] > 0) {
                        $product = $this->site->getProductByID($item['product_id']);
                        if ($product->type === 'standard') {
                            // $this->db->update('products', array('quantity' => ($product->quantity-$item['quantity'])), array('id' => $product->id));
                            $warehouse_id = $data['warehouse_id'];
                            $idProduct = $item['product_id'];
                            $new_stock = $item['quantity'];

                            $q = $this->db->get_where('warehouse_stock', array('warehouse_id' => $warehouse_id, 'product_id' => $idProduct), 1);

                            if ($q->num_rows() > 0) {//EDIT
                                $fila = $q->row();
                                $stock = array(
                                    'warehouse_id' => $warehouse_id,
                                    'product_id' => $idProduct,
                                    'stock' => $fila->stock - $new_stock
                                );
                                $this->db->update('warehouse_stock', $stock, array('id' => $fila->id));

                            }/* else {//NEW
                                $stock = array(
                                    'warehouse_id' => $warehouse_id,
                                    'product_id' => $idProduct,
                                    'stock' => 0 - $new_stock
                                );
                                $this->db->insert('warehouse_stock', $stock);
                            }*/
                            //////
                        } elseif ($product->type == 'combo') {
                            $combo_items = $this->getComboItemsByPID($product->id);
                            foreach ($combo_items as $combo_item) {
                                $cpr = $this->site->getProductByID($combo_item->id);
                                if ($cpr->type == 'standard') {
                                    // $qty = $combo_item->qty * $item['quantity'];
                                    // $this->db->update('products', array('quantity' => ($cpr->quantity-$qty)), array('id' => $cpr->id));
                                    $warehouse_id = $this->Settings->default_warehouse;
                                    $idProduct = $cpr->id;
                                    $new_stock = $combo_item->qty * $item['quantity'];

                                    $q = $this->db->get_where('warehouse_stock', array('warehouse_id' => $warehouse_id, 'product_id' => $idProduct), 1);

                                    if ($q->num_rows() > 0) {//EDIT
                                        $fila = $q->row();
                                        $stock = array(
                                            'warehouse_id' => $warehouse_id,
                                            'product_id' => $idProduct,
                                            'stock' => $fila->stock - $new_stock
                                        );
                                        $this->db->update('warehouse_stock', $stock, array('id' => $fila->id));

                                    }/* else {//NEW
                                        $stock = array(
                                            'warehouse_id' => $warehouse_id,
                                            'product_id' => $idProduct,
                                            'stock' => 0 - $new_stock
                                        );
                                        $this->db->insert('warehouse_stock', $stock);
                                    }*/
                                    /////
                                }
                            }
                        }
                    }

                }
            }

            if ($did) {
                $this->db->delete('suspended_sales', array('id' => $did));
                $this->db->delete('suspended_items', array('suspend_id' => $did));
            }
            $msg = array();
            if (!empty($payment)) {

                if ($payment['paid_by'] == 'gift_card') {
                    $gc = $this->getGiftCardByNO($payment['gc_no']);
                    $this->db->update('gift_cards', array('balance' => ($gc->balance - $payment['amount'])), array('card_no' => $payment['gc_no']));
                }
                unset($payment['cc_cvv2']);
                $payment['sale_id'] = $sale_id;
                $this->db->insert('payments', $payment);

            }

            return array('sale_id' => $sale_id, 'message' => $msg);
        }
        return false;
    }

    function stripe($amount = 0, $card_info = array(), $desc = '')
    {
        $this->load->model('stripe_payments');
        // $card_info = array( "number" => "4242424242424242", "exp_month" => 1, "exp_year" => 2016, "cvc" => "314" );
        // $amount = $amount ? $amount*100 : 3000;
        $amount = $amount * 100;
        if ($amount && !empty($card_info)) {
            $token_info = $this->stripe_payments->create_card_token($card_info);
            if (!isset($token_info['error'])) {
                $token = $token_info->id;
                $data = $this->stripe_payments->insert($token, $desc, $amount, $this->Settings->currency_prefix);
                if (!isset($data['error'])) {
                    $result = array(
                        'transaction_id' => $data->id,
                        'created_at' => date('Y-m-d H:i:s', $data->created),
                        'amount' => ($data->amount / 100),
                        'currency' => strtoupper($data->currency)
                    );
                    return $result;
                } else {
                    return $data;
                }
            } else {
                return $token_info;
            }
        }
        return false;
    }

    public function updateSale($id, $data, $items)
    {
        $oitems = $this->getAllSaleItems($id);
		//************************corrige registro de producto ****************
        foreach ($oitems as $oitem) {
            $product = $this->site->getProductByID($oitem->product_id);
            if ($product->type == 'standard') {
                $this->db->update('products', array('quantity' => ($product->quantity + $oitem->quantity)), array('id' => $product->id));
            } elseif ($product->type == 'combo') {
                $combo_items = $this->getComboItemsByPID($product->id);
                foreach ($combo_items as $combo_item) {
                    $cpr = $this->site->getProductByID($combo_item->id);
                    if ($cpr->type == 'standard') {
                        $qty = $combo_item->qty * $oitem->quantity;
                        $this->db->update('products', array('quantity' => ($cpr->quantity + $qty)), array('id' => $cpr->id));
                    }
                }
            }
        }
		//************************corrige registro de producto ****************

        if ($this->db->update('sales', $data, array('id' => $id)) && $this->db->delete('sale_items', array('sale_id' => $id))) {

            foreach ($items as $item) {
                $item['sale_id'] = $id;
                if ($this->db->insert('sale_items', $item)) {
                    $product = $this->site->getProductByID($item['product_id']);
                    if ($product->type == 'standard') {
                        $this->db->update('products', array('quantity' => ($product->quantity - $item['quantity'])), array('id' => $product->id));
                    } elseif ($product->type == 'combo') {
                        $combo_items = $this->getComboItemsByPID($product->id);
                        foreach ($combo_items as $combo_item) {
                            $cpr = $this->site->getProductByID($combo_item->id);
                            if ($cpr->type == 'standard') {
                                $qty = $combo_item->qty * $item['quantity'];
                                $this->db->update('products', array('quantity' => ($cpr->quantity - $qty)), array('id' => $cpr->id));
                            }
                        }
                    }
                }
            }

            return true;
        }

        return false;
    }

    public function updateSaleCab($id, $data)
    {
        if ($this->db->update('sales', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function suspendSale($data, $items, $did = null)
    {

        if ($did) {

            if ($this->db->update('suspended_sales', $data, array('id' => $did)) && $this->db->delete('suspended_items', array('suspend_id' => $did))) {
                foreach ($items as $item) {
                    unset($item['cost']);
                    $item['suspend_id'] = $did;
                    $this->db->insert('suspended_items', $item);
                }
                return true;
            }

        } else {

            if ($this->db->insert('suspended_sales', $data)) {
                $suspend_id = $this->db->insert_id();
                foreach ($items as $item) {
                    unset($item['cost']);
                    $item['suspend_id'] = $suspend_id;
                    $this->db->insert('suspended_items', $item);
                }
                return $suspend_id;
            }
        }
        return false;
    }

    public function getSaleByID($sale_id)
    {
        $q = $this->db->get_where('sales', array('id' => $sale_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAllSaleItems($sale_id)
    {
        // $this->db->select('sale_items.*, products.name as product_name, products.code as product_code, products.tax_method as tax_method')
        // ->join('products', 'products.id=sale_items.product_id', 'left')
        $this->db->select('sale_items.*, sale_items.name as product_name, sale_items.code as product_code, products.tax_method as tax_method')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->order_by('sale_items.id');
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllSaleItems2($sale_id)
    {
        $this->db->select('sale_items.*, products.name as product_name, products.code as product_code, products.tax_method as tax_method')
            ->join('products', 'products.id=sale_items.product_id')
            ->order_by('sale_items.id');
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        $num = 0;
        if ($q->num_rows() > 0) {
            $num = $q->num_rows() - 1;
            foreach (($q->result()) as $row) {
                $data[$num] = $row;
                $num = $num - 1;
            }
            return (array)$data;
        }
        return false;
    }

    public function getAllSalePayments($sale_id)
    {
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSuspendedSaleByID($id)
    {
        $q = $this->db->get_where('suspended_sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSuspendedSaleItems($id)
    {
        $q = $this->db->get_where('suspended_items', array('suspend_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSuspendedSales($user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->order_by('date', 'desc');
        $q = $this->db->get_where('suspended_sales', array('created_by' => $user_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getGiftCardByNO($no)
    {
        $q = $this->db->get_where('gift_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getComboItemsByPID($product_id)
    {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('combo_items') . '.quantity as qty, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('products') . '.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getFileNameBySaleId($sale_id)
    {
        $q = $this->db->get_where('sales_files', array('sale_id' => $sale_id), 1);;
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
	//***********TRJ016 - ALEXANDER ROCA - 29/03/2019************
	//************TRJ050 RENATO GAMONAL //
	public function user_mail (){
		
		$q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
		
	}
	//************TRJ050 RENATO GAMONAL //
	public function sale ($id){
		
		$q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
		
	}
	//***********TRJ016 - ALEXANDER ROCA - 29/03/2019************
	//***********************************TRJ028 - ALEXANDER ROCA - 04/04/2019************************
	public function getIDwarehouse($idLocal){
		
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
/*

	public function getProductByName($name)
	{
		$q = $this->db->get_where('products', array('name' => $name), 1);
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }

		  return FALSE;
	}
	public function getAllCustomers()
	{
		$q = $this->db->get('customers');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}

			return $data;
		}
	}

	public function getCustomerByID($id)
	{

		$q = $this->db->get_where('customers', array('id' => $id), 1);
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }

		  return FALSE;

	}

	public function getAllProducts()
	{
		$q = $this->db->query('SELECT * FROM products ORDER BY id');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}

			return $data;
		}
	}

	public function getProductByID($id)
	{

		$q = $this->db->get_where('products', array('id' => $id), 1);
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }

		  return FALSE;

	}

	public function getAllTaxRates()
	{
		$q = $this->db->get('tax_rates');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}

			return $data;
		}
	}

	public function getTaxRateByID($id)
	{

		$q = $this->db->get_where('tax_rates', array('id' => $id), 1);
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }

		  return FALSE;

	}



	function getSetting()
	{

		$q = $this->db->get_where('settings', array('setting_id' => 1));
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }

		  return FALSE;

	}




   public function categories_count() {
        return $this->db->count_all("categories");
    }

    public function fetch_categories($limit, $start) {
        $this->db->limit($limit, $start);
		$this->db->order_by("id", "asc");
        $query = $this->db->get("categories");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
   }

   public function bills_count() {
        return $this->db->count_all("suspended_sales");
    }

    public function fetch_bills($limit, $start) {
        $this->db->limit($limit, $start);
		$this->db->order_by("id", "asc");
        $query = $this->db->get("suspended_sales");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
   }

	public function getAllCategories()
	{
		$q = $this->db->get('categories');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}

			return $data;
		}
	}

	public function getCustomerBill($id)
	{

		$q = $this->db->get_where('customer_bill', array('customer_id' => $id));
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }

		  return FALSE;

	}

	public function updateCustomerBill($bID, $saleData, $count, $tax, $total)
	{
		// bill data
		$billData = array(
			'customer_id'		=> $bID,
			'sale_data'			=> $saleData,
			'count'		=> $count,
			'tax' 	=> $tax,
			'total'	=> $total

		);

		if(!$this->getCustomerBill($bID)) {
			if( $this->db->insert('customer_bill', $billData) ) {
				return true;
			}
		} else {
			$this->db->where('customer_id', $bID);
			if($this->db->update('customer_bill', $billData)) {
				return true;
			}
		}

		  return FALSE;

	}

	public function getTodaySales()
	{
		$date = date('Y-m-d');

		$myQuery = "SELECT DATE_FORMAT( date,  '%W, %D %M %Y' ) AS date, SUM( COALESCE( total, 0 ) ) AS total
			FROM sales
			WHERE DATE(date) LIKE '{$date}'";
		$q = $this->db->query($myQuery, false);
		if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }
	}

	public function getTodayCCSales()
	{
		$date = date('Y-m-d');
		$myQuery = "SELECT SUM( COALESCE( total, 0 ) ) AS total
			FROM sales
			WHERE DATE(date) =  '{$date}' AND paid_by = 'CC'
			GROUP BY date";
		$q = $this->db->query($myQuery, false);
		if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }
	}

	public function getTodayCashSales()
	{
		$date = date('Y-m-d');
		$myQuery = "SELECT SUM( COALESCE( total, 0 ) ) AS total
			FROM sales
			WHERE DATE(date) =  '{$date}' AND paid_by = 'cash'
			GROUP BY date";
		$q = $this->db->query($myQuery, false);
		if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }
	}
	public function getTodayChSales()
	{
		$date = date('Y-m-d');
		$myQuery = "SELECT SUM( COALESCE( total, 0 ) ) AS total
			FROM sales
			WHERE DATE(date) =  '{$date}' AND paid_by = 'Cheque'
			GROUP BY date";
		$q = $this->db->query($myQuery, false);
		if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }
	}

	public function getTodaySale()
	{
		$date = date('Y-m-d');
		$myQuery = "SELECT
					(select sum(total) FROM sales WHERE date LIKE '{$date}%') total,
					(select sum(total) FROM sales WHERE paid_by = 'cash' AND date LIKE '{$date}%') ca,
					(select sum(total) FROM sales WHERE paid_by = 'CC' AND date LIKE '{$date}%') cc,
					(select sum(total) FROM sales WHERE paid_by = 'Cheque' AND date LIKE '{$date}%') ch";
		$q = $this->db->query($myQuery, false);
		if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }
	}



	public function getInvoiceBySaleID($sale_id)
	{

		$q = $this->db->get_where('sales', array('id' => $sale_id), 1);
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }

		  return FALSE;

	}

	public function getAllSuspendedItems($suspend_id)
	{
		$this->db->order_by('id');
		$q = $this->db->get_where('suspended_items', array('suspend_id' => $suspend_id));
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}

			return $data;
		}
	}

	public function getSuspendedSaleByID($suspend_id)
	{

		$q = $this->db->get_where('suspended_sales', array('id' => $suspend_id), 1);
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  }

		  return FALSE;

	}

	public function addCustomer($data)
	{

		if($this->db->insert('customers', $data)) {
			return $this->db->insert_id();
		}
		return false;
	}
     */
	 
	public function tax_ICBPER_amount($id){
		
		$q = $this->db->get_where('tax_icbper', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
	}
	
	public function consult_plastic_bags(){
		$this->db->select('tax_icbper');
		$q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}
	
	public function consult_plastic_bags_type($id){
		$this->db->select('amount');
		$q = $this->db->get_where('tax_icbper', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return 0;
	}
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
