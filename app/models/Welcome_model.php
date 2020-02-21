<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function topProducts($user_id = NULL)
    {
        $m = date('Y-m');
        if(!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select($this->db->dbprefix('products').".code as product_code, ".$this->db->dbprefix('products').".name as product_name, sum(".$this->db->dbprefix('sale_items').".quantity) as quantity")
        ->join('products', 'products.id=sale_items.product_id', 'left')
        ->join('sales', 'sales.id=sale_items.sale_id', 'left')
        ->where('sales.estado !=', 0)
        // ->where('sales.status !=', "DEBE")
        ->where('sales.invoice_id !=', NULL)
        ->order_by("sum(".$this->db->dbprefix('sale_items').".quantity)", 'desc')
        ->group_by('sale_items.product_id')
        ->limit(10)
        ->like('sales.date', $m, 'both');
        if($user_id) {
            $this->db->where('created_by', $user_id);
        }
        $q = $this->db->get('sale_items');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getChartData($user_id = NULL) {
        if(!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $myQuery = "SELECT S.month, S.total, S.tax, S.discount
            FROM (	SELECT	date_format(date, '%Y-%m') Month, SUM(IF(currency='USD', ROUND(total * exchange, 2), total)) total, SUM(IF(currency='USD', ROUND(total_tax * exchange, 2), total_tax)) tax, SUM(IF(currency='USD', ROUND(total_discount * exchange, 2), total_discount)) discount
                FROM ".$this->db->dbprefix('sales')."
                WHERE ".$this->db->dbprefix('sales').".date >= date_sub( now( ) , INTERVAL 12 MONTH ) AND estado = 1 AND invoice_id IS NOT NULL "; //status != 'DEBE'  invoice_id IS NOT NULL

        if($user_id) {
            $myQuery .= " AND created_by = ".$user_id." ";
        }

		$myQuery .= "GROUP BY date_format(date, '%Y-%m')) S
					GROUP BY S.Month
                    ORDER BY S.Month";

        $q = $this->db->query($myQuery);

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUserGroups() {
        $this->db->order_by('id', 'desc');
        $q = $this->db->get("users_groups");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function userGroups() {
        $ugs = $this->getUserGroups();
        if ($ugs) {
            foreach ($ugs as $ug) {
                $this->db->update('users', array('group_id' => $ug->group_id), array('id' => $ug->user_id));
            }
            return true;
        }
        return false;
    }

    /*productos con mayor salida x mes*/

    public function getTopProductxmonth(){
          $m = date('Y-m');
          /* $m = '2020-01';*/
        if(!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
       
        $this->db->select('grand_total')->get_compiled_select();
        $this->db->where('estado',1,'date',$m);
         $query = $this->db->get('tec_sales');
         $array=[];
         $suma=0;
        foreach ($query->result() as $row)
        {
                 $suma=$suma+$row->grand_total;
                 

        }
        array_push($array,number_format($suma,2));
       return json_encode($array[0]);
    }

}
