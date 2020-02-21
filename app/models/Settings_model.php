<?php  if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Settings_model extends CI_Model
{
	
	public function __construct() {
		parent::__construct();
	}
	
	public function updateSetting($data = array()) {


            $value = array("site_name"=>$data["site_name"],
					"tel"=>$data["tel"],
					"language"=>$data["language"],
					"currency_prefix"=>$data["currency_prefix"],
					"default_tax_rate"=>$data["default_tax_rate"],
					"default_discount"=>$data["default_discount"],
					"rows_per_page"=>$data["rows_per_page"],
					"bsty"=>$data["bsty"],
					"pro_limit"=>$data["pro_limit"],
					"display_kb"=>$data["display_kb"],
					"mail_incidents"=>$data["mail_incidents"],
					"default_category"=>$data["default_category"],
					"default_customer"=>$data["default_customer"],
					"default_warehouse"=>$data["default_warehouse"],
					"invoice_format"=>$data["invoice_format"],
					"bill_format"=>$data["bill_format"],
					"nventa_format"=>$data["nventa_format"],
					"dateformat"=>$data["dateformat"],
					"timeformat"=>$data["timeformat"],
					"header"=>$data["header"],
					"footer"=>$data["footer"],
					"default_email"=>$data["default_email"],
					"protocol"=>$data["protocol"],
					"smtp_host"=>$data["smtp_host"],
					"smtp_pass"=>$data["smtp_pass"],
					"smtp_user"=>$data["smtp_user"],
					"smtp_port"=>$data["smtp_port"],
					"smtp_crypto"=>$data["smtp_crypto"],
					"pin_code"=>$data["pin_code"],
					"focus_add_item"=>$data["focus_add_item"],
					"add_customer"=>$data["add_customer"],
					"toggle_category_slider"=>$data["toggle_category_slider"],
					"cancel_sale"=>$data["cancel_sale"],
					"suspend_sale"=>$data["suspend_sale"],
					"print_order"=>$data["print_order"],
					"print_bill"=>$data["print_bill"],
					"finalize_sale"=>$data["finalize_sale"],
					"today_sale"=>$data["today_sale"],
					"open_hold_bills"=>$data["open_hold_bills"],
					"close_register"=>$data["close_register"],
					"rounding"=>$data["rounding"],
					"item_addition"=>$data["item_addition"],
					"advanced_sale"=>$data["advanced_sale"],
					"tax_icbper"=>$data["tax_icbper"],
					"pdf_format"=>$data["pdf_format"],
				    "type_Print"=>$data["type_Print"]);
           
       
           	 $this->db->where("setting_id",1);
             $up=$this->db->update("settings",$value);
           	
           if($up){
           	return true;
           }
           return false;
	}
	

}
