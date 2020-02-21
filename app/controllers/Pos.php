<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



class Pos extends MY_Controller
{



	function __construct()

	{

		parent::__construct();



		if (!$this->loggedIn) {

			redirect('login');

		}

		$this->load->library('form_validation');

		$this->load->model('pos_model');
		$this->load->model('mov_model');
		$this->load->model('sales_model');
		$this->load->model('warehouses_model');

	}

	function index($sid = null, $eid = null)
	{
		//*****************validaciones**********************
		if ($this->input->get('hold')) {
			$sid = $this->input->get('hold');
		}
		if ($this->input->get('edit')) {
			$eid = $this->input->get('edit');
		}
		if ($this->input->post('eid')) {
			$eid = $this->input->post('eid');
		}
		if ($this->input->post('did')) {
			$did = $this->input->post('did');
		} else {
			$did = null;
		}
		if ($eid && !$this->Admin) {
			$this->session->set_flashdata('error', lang('access_denied'));
			redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'pos');
		}
		if (!$this->Settings->default_customer || !$this->Settings->default_category) {
			$this->session->set_flashdata('warning', lang('please_update_settings'));
			redirect('settings');
		}

		if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
			$register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);
			$this->session->set_userdata($register_data);
		} else {
			$this->session->set_flashdata('error', lang('register_not_open'));
			redirect('pos/open_register');
		}

		$suspend = $this->input->post('suspend') ? true : false;

		$this->form_validation->set_rules('customer', lang("customer"), 'trim|required');
		$this->form_validation->set_rules('canal_id', lang("canal"), 'trim|required');
		//echo "es ".$this->input->post('canal_id'); exit;

		$data;
		if ($this->form_validation->run() == true) {
		//*****************validaciones**********************
		
			//**********variables que no se usan *****************
			$quantity = "quantity";
			$product = "product";
		
			$unit_cost = "unit_cost";
			$tax_rate = "tax_rate";
			//**********variables que no se usan *****************

			//*******************************captura de datos***********************
			$date = date('Y-m-d H:i:s');
			$customer_id = $this->input->post('customer_id');
			$customer_details = $this->pos_model->getCustomerByID($customer_id);
			$customer = $customer_details->name;
			$document_type = $this->input->post('document_type');
			$canal_id = $this->input->post('canal_id');
			$note = $this->tec->clear_tags($this->input->post('spos_note'));
			$custom_field_1 = $this->input->post('custom_field_1'); //tec_sales - opcionales para el comercio - como CMP
			$custom_field_2 = $this->input->post('custom_field_2'); //tec_sales

			$total = 0;
			$product_tax = 0;
			$order_tax = 0;
			$product_discount = 0;
			$order_discount = 0;
			$percentage = '%';
			$settings_tax = $this->Settings->default_tax_rate;

			//---------------------------------------------------------------------

			$afecto = 0;
			$exonerado = 0;
			$exoneradoReal=0;//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
			$gratuita=0;//***************************TRJ088 - KENY PONTE - 02/09/2019********************************** */
			$igv = 0;

			$total_payable = $this->input->post('monto_total');
			$descuento = $this->input->post('descuento') > 0 ? $this->input->post('descuento') : 0;

			// die(json_encode($total_payable . " " . $descuento));

			//----------------------------------------------------------------------

//**********************TRJ068 - ALEXANDER ROCA - 12/08/2019******************			
			$ICBPER= (array)$this->pos_model->user_mail();
			
			if($ICBPER["tax_icbper"] == 0){
				$mult_ICBPER['amount']=0;
			}else{
				$mult_ICBPER= (array)$this->pos_model->tax_ICBPER_amount($ICBPER["tax_icbper"]);
			}
			
			$total_ICBPER=0;
//**********************TRJ068 - ALEXANDER ROCA - 12/08/2019******************

			$i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
			for ($r = 0; $r < $i; $r++) {
				$item_id = $_POST['product_id'][$r];
				$real_unit_price = $_POST['real_unit_price'][$r];
				$item_quantity = $_POST['quantity'][$r];
				$item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : '0';
				$name = $_POST['name'][$r];
				$code = $_POST['code'][$r];
//**********************TRJ068 - ALEXANDER ROCA - 12/08/2019******************
				if($ICBPER["tax_icbper"] > 0){
					$tax_ICBPER = $_POST['value_ICBPER'][$r];
					
					if($tax_ICBPER==1){
						
						if($mult_ICBPER['amount'] > 0){
							$quantity_ICBPER = (int)(round($item_quantity));
						}else{
							$quantity_ICBPER = 0;
						}
						
						if($quantity_ICBPER > 0){
							$unit_price_ICBPER = $mult_ICBPER['amount'];
							$valor_item_ICBPER=$mult_ICBPER['amount'] * round($item_quantity);
						}else{
							$unit_price_ICBPER = 0;
							$valor_item_ICBPER=0;
						}
						
					}else{
						$unit_price_ICBPER = 0;
						$valor_item_ICBPER=0;
						$quantity_ICBPER = 0;
					}
				}else{
					$unit_price_ICBPER = 0;
					$valor_item_ICBPER=0;
					$quantity_ICBPER = 0;
				}
				
				$total_ICBPER = $total_ICBPER + $valor_item_ICBPER;
//**********************TRJ068 - ALEXANDER ROCA - 12/08/2019******************				

				if (isset($item_id) && isset($real_unit_price) && isset($item_quantity)) {
					$product_details = $this->site->getProductByID($item_id);
					$unit_price = $real_unit_price;
			//*******************************captura de datos***********************
			//********************************calculos******************************
			
					// *******calculo del descuento del producto*******si es 0 o no
					$pr_discount = 0;
					if (isset($item_discount)) {
						$discount = $item_discount;
						$dpos = strpos($discount, $percentage);
						if ($dpos !== false) {
							$pds = explode("%", $discount);
							$pr_discount = (($unit_price) * (Float)($pds[0])) / 100;
						} else {
							$pr_discount = $discount;
						}
					}
					// *******calculo del descuento del producto*******
					
					$unit_price = $unit_price - $pr_discount;
					$item_net_price = $unit_price;
					$pr_item_discount = $pr_discount * $item_quantity;
					$product_discount += $pr_item_discount;
					
					$item_affect_price=0;
					$item_non_affected_price=0;
					$item_exonerated_price=0;

					$pr_item_tax = 0;
					$item_tax = 0;
					$item_exonerado = 0;
					$item_exoReal=0;
					$item_afecto = 0;
					$p_igv = "";
					$bool_exonerado = false;
					$bool_exoReal= false;
					$bool_afecto = false;
					$productExonerated=0;//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
					if (isset($settings_tax) && $settings_tax != 0) {

						if ($document_type != 3){
							$tax_method_item_sale=$product_details->tax_method;

							if ($product_details && $product_details->tax_method == 1) { // EXCLUIDO
								$item_tax = (($unit_price) * $settings_tax) / 100;
								$p_igv = $settings_tax;
								$item_afecto += $item_net_price;
								$bool_afecto = true;
								$unit_price +=$item_tax;
								$item_affect_price=$item_net_price;
								$item_non_affected_price=0;
								$item_exonerated_price=0;

									// $unit_price = $unit_price + $pr_item_tax;
									// $item_net_price = $item_net_price + $pr_item_tax;
							} else if ($product_details && $product_details->tax_method == 0) { // INCLUIDO
								$item_tax = (($unit_price) * $settings_tax) / (100 + $settings_tax);
								$p_igv = $settings_tax;
								$item_net_price -= $item_tax;
								$item_afecto += $item_net_price;
								$bool_afecto = true;
								
								$item_affect_price=$item_net_price;
								$item_non_affected_price=0;
								$item_exonerated_price=0;
								
							} else if ($product_details && $product_details->tax_method == 2 ) { // INAFECTO
								$item_tax = 0;
								$p_igv = 0;
								$item_exonerado += $item_net_price;
								$bool_exonerado = true;
								
								$item_affect_price=0;
								$item_non_affected_price=$item_net_price;
								$item_exonerated_price=0;
								//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
							} else if($product_details && $product_details->tax_method == 3){// EXONERADO
								$item_tax = 0;
								$p_igv = 0;
								$item_exoReal += $item_net_price;
								$bool_exoReal = true;
								$productExonerated=1;
								
								$item_affect_price=0;
								$item_non_affected_price=0;
								$item_exonerated_price=$item_net_price;
							}//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************

						} else {
							$item_tax = 0;
							$p_igv = 0;
							$bool_exonerado = true;
							//$item_afecto += $item_net_price;
							//$bool_afecto = true;
							$tax_method_item_sale=2;
							
							if($product_details && $product_details->tax_method == 1){
								$item_tax = (($unit_price) * $settings_tax) / 100;
								$unit_price +=$item_tax;
								$item_non_affected_price=$item_net_price + $item_tax;
								$item_exonerado += ($item_net_price + $item_tax);
								$item_tax=0;
							}else{
								$item_non_affected_price=$item_net_price;
								$item_exonerado += $item_net_price;
							}
							
							$item_affect_price=0;//$item_net_price;
							//$item_non_affected_price=$item_net_price;
							
							$item_exonerated_price=0;
						}

						$pr_item_tax = $item_tax * $item_quantity;

					}

					// if(	$product_details->tax_method == 1){
					// 	$unit_price = $unit_price + $pr_item_tax;
					// 	$item_net_price = $item_net_price + $pr_item_tax;
					// }

					// $product_tax += $pr_item_tax;

					//$subtotal = (($item_net_price * $item_quantity) + $pr_item_tax);
					//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
					$subtotal = ((($item_affect_price + $item_non_affected_price + $item_exonerated_price) * $item_quantity) + $pr_item_tax) + $valor_item_ICBPER;
					//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
					$igv_desc = 0;
					$gvd_desc = 0;
					$porcentaje = 0;
					$prr_discount = 0;
					if ($descuento > 0) {

						if($document_type != 3){

							$porcentaje = $subtotal / $total_payable;
							$prr_discount = $descuento * $porcentaje;

							if ($bool_afecto) {
								$gvd_desc = $prr_discount / (1 + ($settings_tax / 100));
								$igv_desc = $prr_discount - $gvd_desc;
							}

						} else {
							$gvd_desc = $descuento;
							$prr_discount = 0;
							$igv_desc = 0;
						}

					} else {
						$gvd_desc = 0;
						$prr_discount = 0;
						$igv_desc = 0;
					}

					$product_tax += $pr_item_tax;

					// $subtotal = (($item_net_price * $item_quantity) + $pr_item_tax);

					// $new_tax = $this->tec->formatDecimal($subtotal - ($subtotal/(($tax/100)+1)));
					// $interrumpir = true;
//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********					
					$unit_price = $unit_price + $unit_price_ICBPER;
//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
					$products[] = array(
						'product_id' => $item_id,
						'code' => $code,
						'name' => $name,
						'quantity' => $item_quantity,
						'unit_price' => $this->tec->formatDecimal($unit_price),
						'affect_price' => $this->tec->formatDecimal($item_affect_price),
						'non_affected_price' => $this->tec->formatDecimal($item_non_affected_price),
						'exonerated_price' => $this->tec->formatDecimal($item_exonerated_price),
						'discount' => $this->tec->formatDecimal($item_discount),
						'item_discount' => $this->tec->formatDecimal($pr_item_discount),
						'prr_discount' => $this->tec->formatDecimal($prr_discount),
						// 'tax' => $tax,
						'tax' => $this->tec->formatDecimal($item_tax),
						'item_tax' => $this->tec->formatDecimal($pr_item_tax),//$new_tax,//
						'igv' => $p_igv,
						'tax_ICBPER' => $valor_item_ICBPER, //************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
						'quantity_ICBPER' => $quantity_ICBPER, //************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
						'subtotal' => $this->tec->formatDecimal($subtotal),
						'real_unit_price' => $this->tec->formatDecimal($real_unit_price),
						'currency_cost' => $product_details->currency,
						'cost' => $product_details->cost,
						'tax_method' => $tax_method_item_sale//**********************TRJ044 - ALEXANDER ROCA - 23/05/2019******************
					);

					
					//$total += $item_net_price * $item_quantity;
					$total += ($item_exonerated_price + $item_non_affected_price + $item_affect_price) * $item_quantity;

					if ($bool_afecto) {
						$afecto += ($item_afecto * $item_quantity) - $gvd_desc;
						$igv += $pr_item_tax - $igv_desc;
					}

					if ($bool_exonerado) {
						$exonerado += ($item_exonerado * $item_quantity) - $prr_discount;
					}
					//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
					if($bool_exoReal){
						$exoneradoReal += ($item_exoReal * $item_quantity) - $prr_discount;
					}//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
					
				}
				//********************************calculos******************************
			}

			if (empty($products)) {
				$this->form_validation->set_rules('product', lang("order_items"), 'required');
			} else {
				krsort($products);
			}

			if ($this->input->post('order_discount')) {
				$order_discount_id = $this->input->post('order_discount');
				$opos = strpos($order_discount_id, $percentage);
				if ($opos !== false) {
					$ods = explode("%", $order_discount_id);
					$order_discount = $this->tec->formatDecimal((($total + $product_tax) * (Float)($ods[0])) / 100);
				} else {
					$order_discount = $this->tec->formatDecimal($order_discount_id);
				}
			} else {
				$order_discount_id = null;
			}
			$total_discount = $this->tec->formatDecimal($order_discount + $product_discount);

			if ($this->input->post('order_tax')) {
				$order_tax_id = $this->input->post('order_tax');
				$opos = strpos($order_tax_id, $percentage);
				if ($opos !== false) {
					$ots = explode("%", $order_tax_id);
					$order_tax = $this->tec->formatDecimal((($total + $product_tax - $order_discount) * (Float)($ots[0])) / 100);
				} else {
					$order_tax = $this->tec->formatDecimal($order_tax_id);
				}
			} else {
				$order_tax_id = null;
				$order_tax = 0;
			}

			$total_tax = $this->tec->formatDecimal($product_tax + $order_tax);
//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********			
			$grand_subtotal = $this->tec->formatDecimal($this->tec->formatDecimal($total) + $total_tax + $total_ICBPER - $order_discount); //grand_total
			$sub_total = $this->tec->formatDecimal($this->tec->formatDecimal($total) + $total_tax + $total_ICBPER);
//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
			$round_total = $this->tec->roundNumber($grand_subtotal, $this->Settings->rounding);
			$rounding = $this->tec->formatMoney($round_total - $grand_subtotal);
			$grand_total = $grand_subtotal + $rounding; //

			if ($this->input->post('submit_type') == 1) {
				$paid = $this->input->post('amount') ? $this->input->post('amount') : 0;
				if (!$eid) {
					$status = lang('st_due');
					if ($grand_total > $paid && $paid > 0) {
						$status = lang('st_partial');
					} elseif ($grand_total <= $paid) {
						$status = lang('st_paid');
					}
				}
			} else {
				$paid = 0;
				$status = lang('st_due');
			}

			// $op_grav = $this->tec->formatDecimal($grand_total / (( $settings_tax / 100) + 1));
			// $igv = $this->tec->formatDecimal(($grand_total * $settings_tax) / (100 + $settings_tax));

			// $op_grav = $total - $exonerado;
			$tax_total = $igv + $order_tax;
			//****************************TRJ034 - ALEXANDER ROCA - 09/04/2019****************
			if($grand_total > $paid){
				$valorPago=$paid;
				$balance=0;
			}else{
				$valorPago=$grand_total;
				$balance= $paid - $grand_total;
			}
			//****************************TRJ034 - ALEXANDER ROCA - 09/04/2019****************

			$data = array(
				'date' => $date,
				'local_id' => $this->session->userdata('local'),
				'customer_id' => $customer_id,
				'customer_name' => $customer,
				'document_type' => (int)$document_type,
				'currency' => $this->Settings->currency_prefix,
				'exchange' => $this->Exchange->sell,
				'affected' => $this->tec->formatDecimal($afecto),
				'non_affected' => $this->tec->formatDecimal($exonerado),
				'exonerated' => $this->tec->formatDecimal($exoneradoReal),//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
				//'gratuita' => $this->tec->formatDecimal($exonerado),//******************************TRJ088 - KENY PONTE 02/09/2019************ */
				'total' => $this->tec->formatDecimal($afecto + $exonerado + $exoneradoReal), //$total,//$total),//$op_grav//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
				'product_discount' => $product_discount,
				'order_discount_id' => $order_discount_id,
				'order_discount' => $order_discount,//desc
				'total_discount' => $total_discount,
				'product_tax' => $this->tec->formatDecimal($igv),//$product_tax),//$igv
				'order_tax_id' => $order_tax_id,
				'order_tax' => $order_tax,
				'total_tax' => $this->tec->formatDecimal($tax_total),//$total_tax,
				'tax_ICBPER' => $total_ICBPER,//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
				'mult_ICBPER' => $mult_ICBPER['amount'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
				'subtotal' => $sub_total,
				'amount' => $grand_subtotal,
				'grand_total' => $grand_total,
				'total_items' => $this->input->post('total_items'),
				'total_quantity' => $this->input->post('total_quantity'),
				'rounding' => $rounding,
				'paid' => $paid,
				'status' => $status,
				'created_by' => $this->session->userdata('user_id'),
				'note' => $note,
				'custom_field_1' => $custom_field_1,//tec_sales
				'custom_field_2' => $custom_field_2, //tec_sales
				'canal_id' => $canal_id,
				'estado' => 1,
			);

			
			//***********************************TRJ028 - ALEXANDER ROCA - 04/04/2019************************
			$localData = $this->pos_model->getIDwarehouse($this->session->userdata('local'));
		
			$data['warehouse_id']=$localData ->default_warehouse;
				
			if($data['warehouse_id'] == null || !$data['warehouse_id']){
				$localData = $this->pos_model->getIDwarehouseDefault();
				$data['warehouse_id']=$localData ->default_warehouse;
			}

			//***********************************TRJ028 - ALEXANDER ROCA - 04/04/2019************************

			// die(json_encode($afecto ." ". $exonerado));

			if ($suspend) {
				$data['hold_ref'] = $this->input->post('hold_ref');
			}

			if (!$suspend && $paid) {
				///*$amount = $this->tec->formatDecimal($paid > $grand_total ? $grand_total/*($paid - $this->input->post('balance_amount'))*/ : $paid);*/
				$payment = array(
					'date' => $date,
					'amount' => $valorPago,//$amount,//****************************TRJ034 - ALEXANDER ROCA - 09/04/2019****************
					'customer_id' => $customer_id,
					'paid_by' => $this->input->post('paid_by'),
					'cheque_no' => $this->input->post('cheque_no'),
					'cc_no' => $this->input->post('cc_no'),
					'gc_no' => $this->input->post('paying_gift_card_no'),
					'cc_holder' => $this->input->post('cc_holder'),
					'cc_month' => $this->input->post('cc_month'),
					'cc_year' => $this->input->post('cc_year'),
					'cc_type' => $this->input->post('cc_type'),
					'cc_cvv2' => $this->input->post('cc_cvv2'),
					'created_by' => $this->session->userdata('user_id'),
					'note' => $this->input->post('payment_note'),
					'pos_paid' => $this->tec->formatDecimal($this->input->post('amount')),
					'pos_balance' => $balance//$this->tec->formatDecimal($this->input->post('balance_amount'))//****************************TRJ034 - ALEXANDER ROCA - 09/04/2019****************
				);
				$data['paid'] = $valorPago;//$amount;//****************************TRJ034 - ALEXANDER ROCA - 09/04/2019****************
				
			} else {
				$payment = array();
			}
		}

		$this->form_validation->set_rules('document_type', lang("document_type"), 'required');
		if ($this->input->post('submit_type') == 1) {
			$this->form_validation->set_rules('amount', lang("amount"), 'required');
		}
		$this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
		$paso = 1;

		$doc_type = 0;
		if (isset($data['document_type'])) {
			$doc_type = $data['document_type'];
		}
		// $doc_type = $data['document_type']; //$this->input->post('document_type');
		$err_nopaso = "";

		$customer_id = $this->input->post('customer_id');
		$customer_det = $this->pos_model->getCustomerByID($customer_id);


//*validar mout mas de 750*/



		if ($doc_type == 2) {
			if (trim($customer_det->name) == "") {
				$err_nopaso .= lang("verify_name") . "<br>";
				$paso = 0;
			}
			if (strlen($customer_det->cf2) != 11 || !is_numeric($customer_det->cf2)) {
				$err_nopaso .= lang("verify_ruc") . "<br>";
				$paso = 0;
			}
			if (trim($customer_det->direccion) == "") {
				$err_nopaso .= lang("verify_address");
				$paso = 0;
			}
		}
		
		 if($valorPago > 700){/*validar 700*/
		if ($doc_type == 1) {
			if (strlen($customer_det->cf2) != 11 || !is_numeric($customer_det->cf2)) {
				//****************TRJ073 - KENY PONTE - 13/09/2019**************** */
				if($customer_det->document_type_id != null && $customer_det->document_type_id != 0){
					if ($customer_det->document_type_id == 1 && strlen($customer_det->cf1) != 8 && !is_numeric($customer_det->cf1)){
						$err_nopaso .= lang("verify_dniRuc") . "<br>";
						$paso = 0;
					}else{
						if(($customer_det->document_type_id == 4 || $customer_det->document_type_id == 7) && strlen($customer_det->cf1)<=0){
							$err_nopaso .= lang("verify_dniRuc") . "<br>";
							$paso = 0;
						}

						if(($customer_det->document_type_id == 4 || $customer_det->document_type_id == 7) && strlen($customer_det->cf1)<=3){/*validar longitud de dni --*/
							$err_nopaso .= lang("verify_dniRuc") . "<br>";
							$paso = 0;
						}

					}
				}else{
					$err_nopaso .= lang("verify_dniRuc") . "<br>";
					$paso = 0;
				}
				//****************TRJ073 - KENY PONTE - 13/09/2019**************** */

				
			}
		}
 }




		if ($this->form_validation->run() == true && !empty($products) && $paso == 1) {

			if ($suspend) {
				//*********************suspencion de venta --- no se usa x el momento ************
				unset($data['status'], $data['rounding'], $data['document_type'], $data['custom_field_1'], $data['custom_field_2'], $data['subtotal'], $data['estado']);//tec_sales
				if ($this->pos_model->suspendSale($data, $products, $did)) {
					$this->session->set_userdata('rmspos', 1);
					$this->session->set_flashdata('message', lang("sale_saved_to_opened_bill"));
					redirect("pos");
				} else {
					$this->session->set_flashdata('error', lang("action_failed"));
					redirect("pos/" . $did);
				}
				
			} elseif ($eid) {
				unset($data['date'], $data['paid']);
				$data['updated_at'] = date('Y-m-d H:i:s');
				$data['updated_by'] = $this->session->userdata('user_id');
				if ($this->pos_model->updateSale($eid, $data, $products)) {//******modifica registro de venta****
					$this->session->set_userdata('rmspos', 1);
					$this->session->set_flashdata('message', lang("sale_updated"));
					redirect("sales");
				} else {
					$this->session->set_flashdata('error', lang("action_failed"));
					redirect("pos/?edit=" . $eid);
				}
				//*********************suspencion de venta --- no se usa x el momento ************
			} else {
				//**************** pagar en caja************************
				if ($this->input->post('submit_type') == 2) { //Pagar en Caja
					if ($sale = $this->pos_model->addSale($data, $products, $payment, $did)) {
						$this->session->set_userdata('rmspos', 1);
						redirect("pos/index");
					} else {
						$this->session->set_flashdata('error', lang("action_failed"));
						redirect("pos");
					}
				//**************** pagar en caja************************
				} else { //Pagar
				//**************** pagar************************

					// $start = microtime(true);

					//Asignar Nro Comprobante
					if ($data['document_type'] == 2) {			// Factura
						$new_number = (int)$this->Settings->invoice_number + 1;
						$format = $this->Settings->invoice_format;
					} else if ($data['document_type'] == 1) {	// Boleta
						$new_number = (int)$this->Settings->bill_number + 1;
						$format = $this->Settings->bill_format;
					} else if ($data['document_type'] == 3) {	// Nota de Venta
						$new_number = (int)$this->Settings->nventa_number + 1;//*****Renato TRJ023 25/04/2019   ********
						$format = $this->Settings->nventa_format; //*****Renato TRJ023 25/04/2019   ********
					}

					$parte1 = explode('{', $format);
					$parte2 = explode('}', $parte1[1]);
					$parentesis = $parte2[0];
					// $buscar = strpos($parentesis, '+');
					// if ($buscar) {
					// 	$numero = explode('+', $parentesis);
					// 	$new_format = str_pad((string)$new_number, strlen($numero[0]), "0", STR_PAD_LEFT);
					// } else {
					$new_format = str_pad((string)$new_number, strlen($parentesis), "0", STR_PAD_LEFT);
					// }

					$serieNumero = $parte1[0] . $new_format;
						

					if ($this->pos_model->getInvoiceId($serieNumero)){
						$this->session->set_flashdata('error', lang("the_invoice") . $serieNumero . lang("not_unique"));
						redirect("pos");
					}

					$data['invoice_id'] = $serieNumero;

					//Actualiza Nro Comprobante (Settings)
					$this->pos_model->update_invoice_Number($data['document_type']);
                           


					if ($data['document_type'] != 3) {

						//Generar XML
						require("funciones.php");
						if ($respuesta = $this->generar_xml($data, $products, $customer_det)) {
							$data['flg_response'] = $respuesta['flg_response'];
							$data['error_code'] = $respuesta['error_code'];
							$data['response_descrip'] = $respuesta['response_descrip'];
							$data['digest_value'] = $respuesta['digest_value'];
						}


							

					}
					
					//Grabar Comprobante
					if ($sale = $this->pos_model->addSale($data, $products, $payment, $did)) {

						//REGISTRAR EN MOVIMIENTOS
						foreach ($products as $item) {
							$item_id = $item['product_id'];
							$item_quantity = $item['quantity'];
							$dataMove = array(
								'origin_id' => $sale['sale_id'], //$sale2->id,
								'local_id' => $this->session->userdata('local'),
								'warehouse_id' => $this->Settings->default_warehouse,
								'product_id' => $item_id,
								'cant' => $item_quantity,
								'tipo' => 'S',
								'idMotivo' => 5, //Salida por venta
								'ref' => $data['invoice_id'], //$sale2->invoice_id,
								'uCrea' => $this->session->userdata('user_id'),
								'fCrea' => date("Y-m-d H:i:s"),
								'estado' => 1
							);
							$this->mov_model->addMovement($dataMove);
							/*update stock*/
							$ss=$this->warehouses_model->getStockByIdProduc($item['product_id']);
							$arraystock=[
								"stock"=>$ss->stock
							];
							$this->warehouses_model->updateWarehouse_stock($ss->id,$arraystock);
						}
						
				

						/************************TRJ088 - KENY PONTE - 02/09/2019********************* */
						$p=$this->site->getPDFformat();
						$pdf=$p->pdf_format;
/************************TRJ088 - KENY PONTE - 02/09/2019********************* */
						if ($data['document_type'] == 1) {

							//PDF
							include("app/class/QRCodeGenerator.class.php");//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
							/************************TRJ088 - KENY PONTE - 02/09/2019********************* */
							if($pdf==1){
								$this->generar_pdf($sale['sale_id'], $data, $products, $customer_det);
							}else if($pdf==2){
								$this->generar_pdf1($sale['sale_id'], $data, $products, $customer_det);
							}
							/************************TRJ088 - KENY PONTE - 02/09/2019********************* */			
						} else if ($data['document_type'] == 2) {

							//Enviar Comprobante
							$this->enviar_comprobante($data['invoice_id'], $sale['sale_id'], $data, $customer_det);//***********TRJ016 - ALEXANDER ROCA - 29/03/2019************

							//PDF
							include("app/class/QRCodeGenerator.class.php");//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
							/************************TRJ088 - KENY PONTE - 02/09/2019********************* */
							if($pdf==1){
								$this->generar_pdf($sale['sale_id'], $data, $products, $customer_det);
							}else if($pdf==2){
								$this->generar_pdf1($sale['sale_id'], $data, $products, $customer_det);
							}
						/************************TRJ088 - KENY PONTE - 02/09/2019********************* */
						}

						$this->session->set_userdata('rmspos', 1);
						$msg = lang("sale_added");
						if (!empty($sale['message'])) {
							foreach ($sale['message'] as $m) {
								$msg .= '<br>' . $m;
							}
						}

						// $time_elapsed= microtime(true) - $start;
						// die(json_encode($time_elapsed));
						$vista=$this->input->post('envioPos');
						$this->session->set_flashdata('message', $msg);
						redirect("pos/view/" . $sale['sale_id']."/".$vista);

					} else {
						$this->session->set_flashdata('error', lang("action_failed"));
						redirect("pos");
					}

				}
			}

		} else {

			if (isset($sid) && !empty($sid)) {
				$suspended_sale = $this->pos_model->getSuspendedSaleByID($sid);
				$inv_items = $this->pos_model->getSuspendedSaleItems($sid);
				krsort($inv_items);
				$c = rand(100000, 9999999);
				foreach ($inv_items as $item) {
					$row = $this->site->getProductByID($item->product_id);
					if (!$row) {
						$row = json_decode('{}');
					}
					$row->price = $item->affect_price + ($item->item_discount / $item->quantity);
					$row->unit_price = $item->unit_price + ($item->item_discount / $item->quantity) + ($item->item_tax);///$item->quantity);
					$row->real_unit_price = $item->real_unit_price;
					$row->discount = $item->discount;
					$row->qty = $item->quantity;
					$combo_items = false;
					$ri = $this->Settings->item_addition ? $row->id : $c;
					$pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
					$c++;
				}
				$this->data['items'] = json_encode($pr);
				$this->data['sid'] = $sid;
				$this->data['suspend_sale'] = $suspended_sale;
				$this->data['message'] = lang('suspended_sale_loaded');
			}

			if (isset($eid) && !empty($eid)) {
				$sale = $this->pos_model->getSaleByID($eid);
				$inv_items = $this->pos_model->getAllSaleItems($eid);
				krsort($inv_items);
				$c = rand(100000, 9999999);
				foreach ($inv_items as $item) {
					$row = $this->site->getProductByID($item->product_id);
					if (!$row) {
						$row = json_decode('{}');
					}
					$row->price = $item->affect_price;
					$row->unit_price = $item->unit_price;
					$row->real_unit_price = $item->real_unit_price;
					$row->discount = $item->discount;
					$row->qty = $item->quantity;
					$combo_items = false;
					$row->quantity += $item->quantity;
					if ($row->type == 'combo') {
						$combo_items = $this->pos_model->getComboItemsByPID($row->id);
						foreach ($combo_items as $combo_item) {
							$combo_item->quantity += ($combo_item->qty * $item->quantity);
						}
					}
					$ri = $this->Settings->item_addition ? $row->id : $c;
					$pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
					$c++;
				}
				$this->data['items'] = json_encode($pr);
				$this->data['eid'] = $eid;
				$this->data['sale'] = $sale;
				$this->data['message'] = lang('sale_loaded');
			}

			if ($paso == 0) {
				$this->data['error'] = $err_nopaso;
			} else {
				$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			}

			$this->data['reference_note'] = isset($sid) ? $suspended_sale->hold_ref : null;
			$this->data['sid'] = isset($sid) ? $sid : 0;
			$this->data['eid'] = isset($eid) ? $eid : 0;
			$this->data['customers'] = $this->site->getAllCustomers();
			$this->data['document_type']=$this->site->getAllDocumentType();/******TRJ073 - KENY PONTE - 20/08/2019*****/

			$this->data['customer_type'] = $this->site->getAllCustomerType();
			// $this->data['exchange'] = $this->site->getExchange(date("Y-m-d"));
			$this->data["tcp"] = $this->pos_model->products_count($this->Settings->default_category);
			$this->data['products'] = $this->ajaxproducts($this->Settings->default_category, 1);
			$this->data['categories'] = $this->site->getAllCategories();
			$this->data['canals'] = $this->site->getAllCanals();
			$this->data['canal1'] = "1";
			$seting_bags=$this->pos_model->consult_plastic_bags();
			
			if( $seting_bags->tax_icbper == 0){
				$this->data['plastic_bags'] = 0;
			}else{
				$consul_bags = $this->pos_model->consult_plastic_bags_type($seting_bags->tax_icbper);
				$this->data['plastic_bags'] = $consul_bags->amount;
			}
			
			$this->data['message'] = $this->session->flashdata('message');
			$this->data['suspended_sales'] = $this->site->getUserSuspenedSales();
			$this->data['page_title'] = lang('pos');
			$bc = array(array('link' => '#', 'page' => lang('pos')));
			$meta = array('page_title' => lang('pos'), 'bc' => $bc);
			$this->load->view($this->theme . 'pos/index', $this->data, $meta);

		}

	}

	function generar_xml($data, $items, $customer_det)
	{
    

		if ($data['document_type'] == 2) {//factura
			$invoiceTypeCode = "01";
			$documentID = $customer_det->cf2;
			$document = "6";
            // $urlDoc="boleta";//"factura";
		} else if ($data['document_type'] == 1){//boleta

			$invoiceTypeCode = "03";

			//$document = "1";
		//*********************TRJ073 - KENY PONTE - 13/09/2019************/
			$documentID = $customer_det->cf1;
			$doc_id=$this->pos_model->getDocument($documentID);
			$document=(string)$doc_id->document_type_id;
		//********************TRJ073 - KENY PONTE - 13/09/2019**************/	
			// Si el cliente es una empresa
			if($customer_det->cf1 == null && $customer_det->cf2 != null){
				$document = "6";
				$documentID = $customer_det->cf2;
			}

			if ($documentID == "" | $documentID == null) {
				$documentID = "-";
			}
            // $urlDoc="boleta";
		}

		if ($this->session->userdata('beta') == 0) {
			$urlDoc = "post/xml";
		} else {
			$urlDoc = "beta/post/xml";
		}



		$serieNumero = $data['invoice_id'];
		$Date = explode(' ', $data['date'], 2);

		$supplierID = $this->session->userdata('ruc');
		
		$passFirma = $this->session->userdata('pass_firma');
        // $usuSol = $this->session->userdata('user_sol');
        // $passSol = $this->session->userdata('pass_sol');

		$currency_prefix = $this->Settings->currency_prefix;
		$supplierName = $this->session->userdata('razon_social');
		$signatureID = $serieNumero;
		$URI = "#" . $signatureID;
		$supplierDocumentID = "6";

		
		//Obtener el Local
		$local_id = $this->session->userdata('local');
		$local = $this->site->getLocalByID($local_id);

		//Codigo de local
		$localID = $local->cod_sunat; //"0001";

		$header = array(
			"supplierID" => $supplierID,
			"invoiceTypeCode" => $invoiceTypeCode,
			"serieNumero" => $serieNumero,
			"passFirma" => $passFirma,
			"issueDate" => $Date[0],
			"issueTime" => $Date[1],
			"dueDate" => $Date[0],
			"note" => strtolower(numtoletras($data['amount'])),
			"documentCurrencyCode" => $currency_prefix,
			"lineCountNumeric" => $data['total_items'],
			"signatureID" => $signatureID,
			"supplierName" => $supplierName,
			"URI" => $URI,
			"supplierDocumentID" => $supplierDocumentID,
			"addressTypeCode" => $localID,
			"customerDocumentID" => $document,
			"customerID" => $documentID,
			"customerName" => $data['customer_name'],
			"addressLine" => $customer_det->direccion,
			"baseAmount" => (string)(double)($data['amount'] + $data['order_discount']), //(-$data['rounding']) +
			"taxAmount" => (string)$data['product_tax'],
			"taxableAmount" => (string)$data['affected'],//$data['total'],
			"taxSubtotal" => (string)$data['total_tax'],
			"payableAmount" => (string)$data['amount'],
			"allowanceTotalAmount" => (string)$data['order_discount'], //-$data['rounding']) +
			"non_affected" => (string)$data['non_affected'],
			"exonerated" => (string)$data['exonerated'],//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
			"tax_ICBPER" => (string)$data['tax_ICBPER'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
			"mult_ICBPER" => (string)$data['mult_ICBPER']//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
			
		);



		//******************************* TRJ057 - ALEXANDER ROCA - 19/06/2019 ******************
		$porcentaje_desc=round((((100/$header['baseAmount'])*$data['order_discount'])/100),4);
		
		$discountGlobal = array("discount_global" => (string)$porcentaje_desc);
		//******************************* TRJ057 - ALEXANDER ROCA - 19/06/2019 ******************
//*************************************** TRJ021 - ALEXANDER ROCA - 27/03/2019 *******************************
		$cant = 0;
		foreach ($items as $item) {
			// $product_details = $this->site->getProductByID($item['product_id']);
			$cant = $cant + 1;
			$sum_price=$item['affect_price']+$item['non_affected_price']+$item['exonerated_price'];
			
			$itemArray[$cant] = array(
				"itemQuantity" => $item['quantity'],
				"itemLineExtensionAmount" => (string)($this->tec->formatDecimal($sum_price * $item['quantity'])),//(int)$item['quantity']),
				"itemPricingReference" => (string)$item['unit_price'],
				"itemPriceTypeCode" => "01",
				"itemTaxTotalTaxAmount" => (string)$item['item_tax'],
				"itemTaxSubtotalTaxableAmount" => (string)($this->tec->formatDecimal($sum_price * $item['quantity'])),//(int)$item['quantity']),
				"itemTaxSubtotalTaxAmount" => (string)$item['item_tax'],
				"itemPriceAmount" => (string)$sum_price,
				"itemTaxPercent" => (string)(double)$item['igv'],
				"itemDescription" => (string)$item['name'],//$product_details->name,
				"itemSellersID" => (string)$item['code'],//$product_details->code
				//"exonerated" => (string)$item['exonerated'],//**********************TRJ008 - ALEXANDER ROCA - 11/04/2019******************
				"tax_method" => (string)$item['tax_method'],
				"tax_ICBPER" => (string)$item['tax_ICBPER'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
				"quantity_ICBPER" => (string)$item['quantity_ICBPER'],//************TRJ068 - ALEXANDER ROCA - 12/08/2019***********
			);
		}

//*************************************** TRJ021 - ALEXANDER ROCA - 27/03/2019 *******************************
		//datos a enviar
		$postData["header"] = $header;
		$postData["item"] = $itemArray;
		$postData["discountGlobal"] = $discountGlobal;//******************************* TRJ057 - ALEXANDER ROCA - 19/06/2019 ******************
	 
		// Create the context for the request
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => "Authorization: application/json\r\n" .
					"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
			)
		));

        // Send the request
		$url = $this->config->item('aws_url');
		$response = file_get_contents($url . $urlDoc, false, $context);
     
		if ($response) {
			$res = json_decode($response, true);

			$sale_data = array(
				'flg_response' => $res[0],
				'error_code' => $res[1],
				'response_descrip' => $res[2],
				'digest_value' => $res[4]
			);

			return $sale_data;

		} else {
			$sale_data = array(
				'flg_response' => "0",
				'error_code' => "0",
				'response_descrip' => "Error al conectarse con AWS",
				'digest_value' => ""
			);

			return $sale_data;
		}

		return false;

	}

	function enviar_comprobante($serieNumero, $sale_id, $data, $customer_det)
	{

		if ($this->session->userdata('beta') == 0) {
			$urlDoc = "post/envio";
		} else {
			$urlDoc = "beta/post/envio";
		}


		$supplierID = $this->session->userdata('ruc');
		$invoiceTypeCode = "01";

		$file_name = $supplierID . "-" . $invoiceTypeCode . "-" . $serieNumero;

		$usuSol = $this->session->userdata('user_sol');
		$passSol = $this->session->userdata('pass_sol');

		$postData = array(
			'ruc' => $supplierID,
			'typeCode' => $invoiceTypeCode,
			'serieNumero' => $serieNumero,
			'usuSol' => $usuSol,
			'passSol' => $passSol
		);

		// Create the context for the request
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => "Authorization: application/json\r\n" .
					"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
			)
		));

        // Send the request
		$url = $this->config->item('aws_url');
		$response = file_get_contents($url . $urlDoc, false, $context);

		//-------------Envio Sunat---------------//
		if ($response) {
			$res = json_decode($response, true);

			$tipo = "RF";

			// $datos = explode('-', $serieNumero, 2);
			// $serie = $datos[0];
			// $sNumero = $datos[1];

			if ($res[1] != 0) {
				$status = 3;
			} else {
				if ($res[0] == 1) {
					$status = 4;
				} else {
					$status = 1;
				}
			}

			$send_date = date("Y-m-d");

			$reg_invoice = array(
				'issue_date' => $send_date,
				// 'number' => $sNumero,
				'file_name' => $file_name,
				'response_descrip' => $res[3] . ": " . $res[2],
				'reference_date' => $send_date,
				'processed_date' => $send_date,
				'type' => $tipo,
				'flg_response' => $res[0],
				'error_code' => $res[1],
				'status' => $status,
				'uCrea' => $this->session->userdata('user_id'),
				'fCrea' => date('Y-m-d H:i:s'),
				'estado' => 1,
			);
			
			
	//***********TRJ016 - ALEXANDER ROCA - 29/03/2019************
	  if($status != 4){

		$DniRuc=$customer_det->cf2;
		$nameCliente=$customer_det->name;
			$SerieYnumero= $data["invoice_id"];
			$fecha=$data["date"];
			$montoTotal=$data["grand_total"];
			$tipoMoneda=$data["currency"];
		

		$receipt = "<html>Estimado Cliente: <br><br>Mediante la presente se informa que el siguiente comprobante no fue informado correctamente a SUNAT.<br><br>Tipo de documento &#09;&#09;&#09; : Factura Electronica<br>Serie y numero &#09;&#09;&#09;&#09; : " . $SerieYnumero . "<br>Nro RUC/DNI/CE del cliente &#09;&#09; : ".$DniRuc."<br>Razon social cliente &#09;&#09;&#09; : ".$nameCliente."<br>Fecha de emision &#09;&#09;&#09; : " . $fecha . "<br>Importe Total &#09;&#09;&#09;&#09; : " . $montoTotal ." ".$tipoMoneda. "<br>Motivo &#09;&#09;&#09;&#09;&#09; : (".$res[1].") No hubo respuesta del servicio de SUNAT<br><br><br>Sistema TPV<br><br><br>Nota:<br>*En este correo electronico hemos eliminado las tildes para evitar errores en su visualizacion.<br>*Este mensaje es automatico, por favor no responder.<br></html>";
		$subject = "Alerta SistemaTPV - ". $this->Settings->site_name .": Incidencia comprobante ".$SerieYnumero;
		$toUser=(array)$this->pos_model->user_mail ();//************TRJ050 RENATO GAMONAL //
		$toCopia="asysfull@gmail.com";
		$nombreEnvia="Sistema TPV";

		if ($this->tec->send_email($toUser["mail_incidents"], $subject, $receipt, null, $nombreEnvia, null, null, $toCopia, null, null, null )) {//************TRJ050 RENATO GAMONAL //
			//echo json_encode(array('msg' => lang("email_success")));
		} else {
			//echo json_encode(array('msg' => lang("email_failed")));
		}
  
	  }
	  //***********TRJ016 - ALEXANDER ROCA - 29/03/2019************
			

			$this->pos_model->addSendInvoice($reg_invoice, $sale_id);

			return true;
		}

		return false;
	}

	function get_product($code = null)
	{

		if ($this->input->get('code')) {
			$code = $this->input->get('code');
		}

		$combo_items = false;

		if ($product = $this->pos_model->getProductByCode($code)) {

			unset($product->cost, $product->details);

			if($warehouse_stock = $this->pos_model->getStockByProductID($product->id, $this->Settings->default_warehouse)){
				$product->quantity = $warehouse_stock->stock;
			} else {
				$product->quantity = 0;
			}

			$product->qty = 1;

			$product->discount = '0';

			$product->real_unit_price = $product->price;

			$product->unit_price = $this->Settings->default_tax_rate ? ($product->price + (($product->price * $this->Settings->default_tax_rate) / 100)) : $product->price;

			if ($product->type == 'combo') {

				$combo_items = $this->pos_model->getComboItemsByPID($product->id);

			}
			
			//************************TRJ014 - ALEXANDER ROCA - 03/04/2019****************
			$this->session->set_userdata('listaVenta', $this->session->userdata('listaVenta') + 1);
			$product->productID=$product->id;
			$product->id=$this->session->userdata('listaVenta');
			//************************TRJ014 - ALEXANDER ROCA - 03/04/2019****************
//************************TRJ068 - ALEXANDER ROCA - 09/08/2019***************
			echo json_encode(array('id' => str_replace(".", "", microtime(true)), 'item_id' => $product->id, 'label' => $product->name . " (" . $product->code . ")", 'row' => $product, 'tax_ICBPER'=> 0, 'combo_items' => $combo_items));
//************************TRJ068 - ALEXANDER ROCA - 09/08/2019***************
		} else {

			echo null;

		}



	}

	function suggestions()
	{

		$term = $this->input->get('term', true);

		//***********TRJ0003 - ALEXADER ROCA - 13/03/2019************

		$palabraExtraida = explode(" ", $term);
		$validacion="(";

		for($i=0; $i<count($palabraExtraida) ; $i++){

			if($i==count($palabraExtraida)-1){
				$validacion=$validacion." name LIKE '%" . $palabraExtraida[$i] . "%'";
			}else{
				$validacion=$validacion." name LIKE '%" . $palabraExtraida[$i] . "%' and";
			}
		}

		$validacion=$validacion.")";

		$rows = $this->pos_model->getProductNames($term,$validacion);
		//***********TRJ0003 - ALEXADER ROCA - 13/03/2019************

		if ($rows) {

			foreach ($rows as $row) {

				unset($row->cost, $row->details);

				if($warehouse_stock = $this->pos_model->getStockByProductID($row->id, $this->Settings->default_warehouse)){
					$row->quantity = $warehouse_stock->stock;
				} else {
					$row->quantity = 0;
				}

				$row->qty = 1;

				$row->discount = '0';

				$row->real_unit_price = $row->price;

				$row->unit_price = $row->tax ? ($row->price + (($row->price * $row->tax) / 100)) : $row->price;

				$combo_items = false;

				if ($row->type == 'combo') {

					$combo_items = $this->pos_model->getComboItemsByPID($row->id);

				}
				
				//************************TRJ014 - ALEXANDER ROCA - 08/04/2019****************
				$this->session->set_userdata('listaVenta', $this->session->userdata('listaVenta') + 1);
				$row->productID=$row->id;
				$row->id=$this->session->userdata('listaVenta');
				//************************TRJ014 - ALEXANDER ROCA - 08/04/2019***************
//************************TRJ068 - ALEXANDER ROCA - 09/08/2019***************
				$pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_ICBPER'=> 0, 'combo_items' => $combo_items);
//************************TRJ068 - ALEXANDER ROCA - 09/08/2019***************
			}

			echo json_encode($pr);

		} else {

			echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));

		}

	}

	function registers()
	{



		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		$this->data['registers'] = $this->pos_model->getOpenRegisters();

		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('open_registers')));

		$meta = array('page_title' => lang('open_registers'), 'bc' => $bc);

		$this->page_construct('pos/registers', $this->data, $meta);

	}

	function open_register()
	{

		$this->form_validation->set_rules('cash_in_hand', lang("cash_in_hand"), 'trim|required|numeric');



		if ($this->form_validation->run() == true) {

			$data = array(
				'date' => date('Y-m-d H:i:s'),

				'cash_in_hand' => $this->input->post('cash_in_hand'),

				'user_id' => $this->session->userdata('user_id'),

				'status' => 'open',

			);

		}

		if ($this->form_validation->run() == true && $this->pos_model->openRegister($data)) {

			$this->session->set_flashdata('message', lang("welcome_to_pos"));

			redirect("pos");

		} else {



			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');



			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('open_register')));

			$meta = array('page_title' => lang('open_register'), 'bc' => $bc);

			$this->page_construct('pos/open_register', $this->data, $meta);

		}

	}

	function close_register($user_id = null)
	{

		if (!$this->Admin) {

			$user_id = $this->session->userdata('user_id');

		}

		$this->form_validation->set_rules('total_cash', lang("total_cash"), 'trim|required|numeric');

		$this->form_validation->set_rules('total_cheques', lang("total_cheques"), 'trim|required|numeric');

		$this->form_validation->set_rules('total_cc_slips', lang("total_cc_slips"), 'trim|required|numeric');



		if ($this->form_validation->run() == true) {

			if ($this->Admin) {

				$user_register = $user_id ? $this->pos_model->registerData($user_id) : null;

				$rid = $user_register ? $user_register->id : $this->session->userdata('register_id');

				$user_id = $user_register ? $user_register->user_id : $this->session->userdata('user_id');

			} else {

				$rid = $this->session->userdata('register_id');

				$user_id = $this->session->userdata('user_id');

			}

			$data = array(
				'closed_at' => date('Y-m-d H:i:s'),

				'total_cash' => $this->input->post('total_cash'),

				'total_cheques' => $this->input->post('total_cheques'),

				'total_cc_slips' => $this->input->post('total_cc_slips'),
				
				'total_stripe' => $this->input->post('total_stripe'),//**********************TRJ026 - ALEXANDER ROCA - 01/04/2019 **********

				'total_cash_submitted' => $this->input->post('total_cash_submitted'),

				'total_cheques_submitted' => $this->input->post('total_cheques_submitted'),

				'total_cc_slips_submitted' => $this->input->post('total_cc_slips_submitted'),
				
				'total_stripe_submitted' => $this->input->post('total_stripe_submitted'),//**********************TRJ026 - ALEXANDER ROCA - 01/04/2019 **********

				'note' => $this->input->post('note'),

				'status' => 'close',

				'transfer_opened_bills' => $this->input->post('transfer_opened_bills'),

				'closed_by' => $this->session->userdata('user_id'),

			);

		} elseif ($this->input->post('close_register')) {

			$this->session->set_flashdata('error', (validation_errors() ? validation_errors() : $this->session->flashdata('error')));

			redirect("pos");

		}



		if ($this->form_validation->run() == true && $this->pos_model->closeRegister($rid, $user_id, $data)) {

			$this->session->set_flashdata('message', lang("register_closed"));

			redirect("welcome");

		} else {

			if ($this->Admin) {

				$user_register = $user_id ? $this->pos_model->registerData($user_id) : null;

				$register_open_time = $user_register ? $user_register->date : $this->session->userdata('register_open_time');

				$this->data['cash_in_hand'] = $user_register ? $user_register->cash_in_hand : null;

				$this->data['register_open_time'] = $user_register ? $register_open_time : null;

			} else {

				$register_open_time = $this->session->userdata('register_open_time');

				$this->data['cash_in_hand'] = null;

				$this->data['register_open_time'] = null;

			}

			$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

			$this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);

			$this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);

			$this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time, $user_id);

			$this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time, $user_id);

			$this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time, $user_id);

			$this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time);

			$this->data['users'] = $this->tec->getUsers($user_id);

			$this->data['suspended_bills'] = $this->pos_model->getSuspendedsales($user_id);

			$this->data['user_id'] = $user_id;

			$this->load->view($this->theme . 'pos/close_register', $this->data);

		}

	}

	function ajaxproducts($category_id = null, $return = null)
	{

		if ($this->input->get('category_id')) {
			$category_id = $this->input->get('category_id');
		} elseif (!$category_id) {
			$category_id = $this->Settings->default_category;
		}

		if ($this->input->get('per_page') == 'n') {
			$page = 0;
		} else {
			$page = $this->input->get('per_page');
		}

		if ($this->input->get('tcp') == 1) {
			$tcp = true;
		} else {
			$tcp = false;
		}



		$products = $this->pos_model->fetch_products($category_id, $this->Settings->pro_limit, $page);

		$pro = 1;

		$prods = "<div>";

		if ($products) {

			if ($this->Settings->bsty == 1) {

				foreach ($products as $product) {

					$count = $product->id;

					if ($count < 10) {
						$count = "0" . ($count / 100) * 100;
					}

					if ($category_id < 10) {
						$category_id = "0" . ($category_id / 100) * 100;
					}

					$prods .= "<button type=\"button\" data-name=\"" . $product->name . "\" id=\"product-" . $category_id . $count . "\" type=\"button\" value='" . $product->code . "' class=\"btn btn-name btn-default btn-flat product\">" . $product->name . "</button>";

					$pro++;

				}

			} elseif ($this->Settings->bsty == 2) {

				foreach ($products as $product) {

					$count = $product->id;

					if ($count < 10) {
						$count = "0" . ($count / 100) * 100;
					}

					if ($category_id < 10) {
						$category_id = "0" . ($category_id / 100) * 100;
					}

					$prods .= "<button type=\"button\" data-name=\"" . $product->name . "\" id=\"product-" . $category_id . $count . "\" type=\"button\" value='" . $product->code . "' class=\"btn btn-img btn-flat product\"><img src=\"" . base_url() . "uploads/thumbs/" . $product->image . "\" alt=\"" . $product->name . "\" style=\"width: 110px; height: 110px;\"></button>";

					$pro++;

				}

			} elseif ($this->Settings->bsty == 3) {

				foreach ($products as $product) {

					$count = $product->id;

					if ($count < 10) {
						$count = "0" . ($count / 100) * 100;
					}

					if ($category_id < 10) {
						$category_id = "0" . ($category_id / 100) * 100;
					}

					$prods .= "<button type=\"button\" data-name=\"" . $product->name . "\" id=\"product-" . $category_id . $count . "\" type=\"button\" value='" . $product->code . "' class=\"btn btn-both btn-flat product\"><span class=\"bg-img\"><img src=\"" . base_url() . "uploads/thumbs/" . $product->image . "\" alt=\"" . $product->name . "\" style=\"width: 100px; height: 100px;\"></span><span><span>" . $product->name . "</span></span></button>";

					$pro++;

				}

			}

		} else {

			$prods .= '<h4 class="text-center text-info" style="margin-top:50px;">' . lang('category_is_empty') . '</h4>';

		}



		$prods .= "</div>";



		if (!$return) {

			if (!$tcp) {

				echo $prods;

			} else {

				$category_products = $this->pos_model->products_count($category_id);

				header('Content-Type: application/json');

				echo json_encode(array('products' => $prods, 'tcp' => $category_products));

			}

		} else {

			return $prods;

		}



	}

	function view($sale_id = null, $noprint = null)
	{

		require("funciones.php");

		if ($this->input->get('id')) {
			$sale_id = $this->input->get('id');
		}
		$this->data['vista'] = $this->uri->segment(4);//***************TRJ062 - KENY PONTE - 23/09/2019******************* */

		
		

		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

		$this->data['message'] = $this->session->flashdata('message');

		$inv = $this->pos_model->getSaleByID($sale_id);

		$this->data['user'] = $this->site->getUser($inv->created_by);

		$this->tec->view_rights($inv->created_by);

		$this->load->helper('text');

		$this->data['rows'] = $this->pos_model->getAllSaleItems($sale_id);

		$this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);

		$this->data['inv'] = $inv;

		$this->data['sid'] = $sale_id;

		$this->data['noprint'] = $noprint;

		$this->data['modal'] = false;

		$this->data['payments'] = $this->pos_model->getAllSalePayments($sale_id);

		$this->data['created_by'] = $this->site->getUser($inv->created_by);

		$this->data['page_title'] = lang("voucher");
		
		
		//****************TRJ098 - ALEXANDER ROCA -16/10/2019***********
		
		$data_seting = $this->site->getSettings();
		
		if($data_seting->type_Print == 0 || $inv->document_type == 3){
			$this->load->view($this->theme . 'pos/view', $this->data);
		}else{
			//*************************************************************************
			$this->data['file_pdf'] = $this->pos_model->getFileNameBySaleId($sale_id);
			
			
			$this->load->view($this->theme . 'pos/view_pdf', $this->data);
		}
		
		//****************TRJ098 - ALEXANDER ROCA -16/10/2019***********
		

	}

	function email_receipt($sale_id = null, $to = null)

	{

		require("funciones.php");

		if ($this->input->post('id')) {
			$sale_id = $this->input->post('id');
		}

		if ($this->input->post('email')) {
			$to = $this->input->post('email');
		}

		if (!$sale_id || !$to) {
			die();
		}



		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

		$this->data['message'] = $this->session->flashdata('message');

		$inv = $this->pos_model->getSaleByID($sale_id);
		$file = $this->pos_model->getFileNameBySaleId($sale_id);

		$this->tec->view_rights($inv->created_by);

		$this->load->helper('text');

		$this->data['rows'] = $this->pos_model->getAllSaleItems($sale_id);

		$this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);

		$this->data['inv'] = $inv;

		$this->data['sid'] = $sale_id;

		$this->data['noprint'] = null;

		$this->data['modal'] = false;

		$this->data['payments'] = $this->pos_model->getAllSalePayments($sale_id);

		$this->data['created_by'] = $this->site->getUser($inv->created_by);

		$path = base_url() . 'uploads/pdf/';
		$file_name = $file->file_name;

		// $receipt = $this->load->view($this->theme.'pos/view', $this->data, TRUE);
		$arrD1 = explode(" ", $inv->date);
		//***********TRJ007 - ALEXANDER ROCA - 21/03/2019************
		if ($inv->document_type == 2) {
			$descripTipo="Factura";
			$tipo = "01";
		} else {
			$descripTipo="Boleta";
			$tipo = "03";
		}

		$direccion="uploads/xml/";
		
		if (!file_exists($direccion)){
			mkdir($direccion);
		}
		
		$ubicacionArchivo=$direccion.$this->session->userdata('ruc')."-".$tipo."-".$inv->invoice_id.".xml";
		
		if (file_exists($ubicacionArchivo)){
		}else{
			
			$itemArray = array(
				"ruc" => $this->session->userdata('ruc'),
				"tipoComprobante" => $tipo,
				"beta" => $this->session->userdata('beta'),
				"Correlativo" => $inv->invoice_id
			);

		$postData["item"] = $itemArray;

		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => "Authorization: application/json\r\n" .
					"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
			)
		));
		
		$urlDoc = "post/doc/xml";
		$url = $this->config->item('aws_url');
		$response = file_get_contents($url . $urlDoc, false, $context);
		
		if($response){
			$respuestaXml = json_decode($response, true);
			
			$contador=0;
			foreach ($respuestaXml as $resp){
				$datosXml[$contador]=$resp;
				$contador=$contador+1;
			}
		}
		
		$datosConvertidos = implode(array_map("chr", $datosXml));
		
		/*if (file_exists($ubicacionArchivo)){
			
			$archivo = fopen($ubicacionArchivo, "a");
			fwrite($archivo,$datosConvertidos);
			fclose($archivo);
			
		}else{*/
			
			$archivo = fopen($ubicacionArchivo, "w");
			fwrite($archivo,$datosConvertidos);
			fclose($archivo);
			
		   }
		
		//***********TRJ007 - ALEXANDER ROCA - 21/03/2019************

		//***********TRJ0001 - ALEXANDER ROCA - 12/03/2019************
		$tipoModena="";

		if($inv->currency=="PEN"){
			$tipoModena="S/";
		}else{
			$tipoModena="$";
		}
		
		//***********TRJ007 - ALEXANDER ROCA - 21/03/2019************
		if(($this->data['customer']->cf1)==""){
			$documentoIdentid=$this->data['customer']->cf2;
		}else{
			$documentoIdentid=$this->data['customer']->cf1;
		}
		//***********TRJ039 - ALEXANDER ROCA - 03/05/2019************
		$receipt = "<html>Estimado Cliente: <br><br>Por la presente les comunicamos que la empresa " . $this->session->userdata('razon_social') . ", emisora de comprobantes electronicos le ha emitido el siguiente comprobante:<br><br><br>Tipo de documento: " . $descripTipo . "<br><br>Serie y numero: " . $inv->invoice_id . "<br><br>Nro RUC/DNI/CE del cliente: ".$documentoIdentid."<br><br>Razon social cliente: ".$this->data['customer']->name."<br><br>Fecha de emision: " . $arrD1[0] . "<br><br>Importe Total: ".$tipoModena." " . $inv->grand_total . "<br><br><br>Atentamente,<br><br>" . $this->session->userdata('razon_social') . "<br><br><br>Nota:<br>*En este correo electronico hemos eliminado las tildes para evitar errores en su visualizacion.<br></html>";
		//***********TRJ039 - ALEXANDER ROCA - 03/05/2019************
		//***********TRJ007 - ALEXANDER ROCA - 21/03/2019************
		//***********TRJ0001 - ALEXADER ROCA - 12/03/2019************
		//***********TRJ007 - ALEXANDER ROCA - 21/03/2019************
		$subject = $descripTipo . " Electronica " . $inv->invoice_id ." ".$this->session->userdata('razon_social') ;

		
		if ($this->tec->send_email($to, $subject, $receipt, null, null, $path . $file_name, null, null, $inv->invoice_id . '.pdf', $ubicacionArchivo, $inv->invoice_id . '.xml' )) {//***********TRJ007 - ALEXADER ROCA - 20/03/2019************
		//***********TRJ007 - ALEXANDER ROCA - 21/03/2019************
			echo json_encode(array('msg' => lang("email_success")));

		} else {

			echo json_encode(array('msg' => lang("email_failed")));

		}



	}


	function register_details()

	{



		$register_open_time = $this->session->userdata('register_open_time');

		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

		$this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time);

		$this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time);

		$this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time);

		$this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time);

		$this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time);

		$this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time);

		$this->load->view($this->theme . 'pos/register_details', $this->data);

	}



	function today_sale()

	{

		if (!$this->Admin) {

			$this->session->set_flashdata('error', lang('access_denied'));

			redirect($_SERVER["HTTP_REFERER"]);

		}



		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

		$this->data['ccsales'] = $this->pos_model->getTodayCCSales();

		$this->data['cashsales'] = $this->pos_model->getTodayCashSales();

		$this->data['chsales'] = $this->pos_model->getTodayChSales();

		$this->data['stripesales'] = $this->pos_model->getTodayStripeSales();

		$this->data['totalsales'] = $this->pos_model->getTodaySales();

		// $this->data['expenses'] = $this->pos_model->getTodayExpenses();

		$this->load->view($this->theme . 'pos/today_sale', $this->data);

	}



	function shortcuts()

	{

		$this->load->view($this->theme . 'pos/shortcuts', $this->data);

	}



	function mesa()

	{

		$this->load->view($this->theme . 'pos/mesa', $this->data);

	}



	function view_bill()

	{

		$this->load->view($this->theme . 'pos/view_bill', $this->data);

	}



	function promotions()

	{

		$this->load->view($this->theme . 'promotions', $this->data);

	}



	function stripe_balance()

	{

		if (!$this->Owner) {

			return false;

		}

		$this->load->model('stripe_payments');

		return $this->stripe_payments->get_balance();

	}



	function language($lang = false)

	{

		if ($this->input->get('lang')) {

			$lang = $this->input->get('lang');

		}

        //$this->load->helper('cookie');

		$folder = 'app/language/';

		$languagefiles = scandir($folder);

		if (in_array($lang, $languagefiles)) {

			$cookie = array(

				'name' => 'language',

				'value' => $lang,

				'expire' => '31536000',

				'prefix' => 'spos_',

				'secure' => false

			);



			$this->input->set_cookie($cookie);

		}

		redirect($_SERVER["HTTP_REFERER"]);

	}



	function validate_gift_card($no)

	{

		if ($gc = $this->pos_model->getGiftCardByNO(urldecode($no))) {

			if ($gc->expiry) {

				if ($gc->expiry >= date('Y-m-d')) {

					echo json_encode($gc);

				} else {

					echo json_encode(false);

				}

			} else {

				echo json_encode($gc);

			}

		} else {

			echo json_encode(false);

		}

	}



	function search_cmp()
	{
		$custom_field_1 = $this->input->get('custom_field_1');//tec_sales
		$rcmp = $this->site->getDoctorByCmp($custom_field_1);    	//tec_sales
		//return $rcmp;
		echo json_encode($rcmp);
	}


    /* --------------------------------------------------------------------------------------------- */

    /* ------------------GENERA EL PDF Y GUARDA EN LA TABLA DE HISTORIAL "tec_sales_files"------------------------------- */
	function generar_pdf($sale_id, $inv, $items, $customer_det)
	{
		// $inv = $this->pos_model->getSaleByID($sale_id);
         

		//Idioma ---------------
		$region = $this->session->userdata('region');
		$l_ruc = lang("ccf2");
		$l_dni = lang("ccf1");
		$l_address = lang("scf2");
		$arrMes = lang("months");
		$l_qty = lang("qty");
		$l_description = lang("description");
		$l_importe = lang("importe");
		$l_currency = lang("currency_name");
		$op_exo = lang("op_exonerated");
		$op_exoReal=lang("op_exo");//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
		$op_ICBPER=lang('product_tax_ICBPER').' ('.lang('tax_ICBPER').')';//***************TRJ068 - ALEXANDER ROCA - 13/08/2019********************
		//----------------------

		$tipo_doc = "";
		$impRuc = "";
		if ($inv['document_type'] == 1) {
			$tipo_doc = lang('bill_title');
		} else {
			$tipo_doc = lang('invoice_title');
		}
       
		$numComp = $inv['invoice_id'];

		$symbol = "";
		$currency = "";
		if ($inv['currency'] == 'PEN') {
            // $symbol = "S/ ";
			$currency = strtoupper(lang("currency_pen"));
		} else if ($inv['currency'] == 'USD') {
            // $symbol = "$ ";
			$currency = lang("currency_USD");
		}

		$arrD1 = explode(" ", $inv['date']);
		$arrD2 = explode("-", $arrD1[0]);
		$ano = $arrD2[0];
		$dia = $arrD2[2];
		$mes = $arrMes[(int)$arrD2[1] - 1];

		$row_items_html = "";
		foreach ($items as $item) {

			$quantity = $this->tec->formatNumber($item['quantity']);
			$uprice = $this->tec->formatMoney($item['unit_price']);
			$subt = $this->tec->formatMoney($item['subtotal']);
			//***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************
			$nombreProducto=str_replace("<","&lt;",$item['name']);
			$nombreProducto=str_replace(">","&gt;",$nombreProducto);

			$row_items_html .= '<tr>
                    <td align="center" style="font-size:10px">' . $quantity . '</td>
                    <td align="left" style="font-size:10px">' . $nombreProducto . '</td>
                    <td align="right" style="font-size:10px">' . $uprice . '</td>
                    <td align="right" style="font-size:10px">' . $subt . '</td>
                </tr>';
		}//***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************

		$row_items_html .= '
            <tr>
                <td align="center">&nbsp;</td>
                <td align="left"></td>
                <td align="right"></td>
                <td align="right"></td>
            </tr>
		';
		$customer_name = $customer_det->name;
		$customer_address = $customer_det->direccion;

		if ($inv['document_type'] == 2) {
            // $impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" >
            //             <tr>
            //                 <td align="left"  style="font-size:10px">' . $region . ', <u> '.$dia.' </u> de <u> '.$mes.' </u> del <u> '.$ano.' </u> </td>
            //             </tr>
            //             <tr>
            //                 <td align="left"  style="font-size:10px">' . $l_ruc . ': '.$customer_det['cf2'].'</td>
            //             </tr>

            //         </table>';

			$impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" align="left" >
                        <tr>
                                <td align="left" width="20%"  style="font-size:10px"> Señor(es): </td>
                                <td align="left" width="80%"  style="font-size:10px"> ' . $customer_name . '</td>
                        </tr>
                        <tr>
                                <td align="left" width="20%"  style="font-size:10px"> ' . $l_ruc . ': </td>
                                <td align="left" width="80%"  style="font-size:10px"> ' . $customer_det->cf2 . '</td>
                        </tr>
						<tr>
							<td align="left" width="20%"  style="font-size:10px"> ' . $l_address . ': </td>
							<td align="left" width="80%"  style="font-size:10px"> ' . $customer_address . '</td>
						</tr>

                    </table>';
		} else {

            // $impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" >
            //             <tr>
            //                 <td align="left"  style="font-size:10px">' . $region . ', <u> '.$dia.' </u> de <u> '.$mes.' </u> del <u> '.$ano.' </u><br></td>
            //             </tr>
			//         </table>';

			$doc = $l_dni;
			$nro_doc = $customer_det->cf1;

			if($customer_det->cf1 == null && $customer_det->cf2 != null){
				$doc = $l_ruc;
				$nro_doc = $customer_det->cf2;
			}
//***************TRJ043 - ALEXANDER ROCA - 30/04/2019**************
			$impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" align="left">
							<tr>
								<td align="left" width="20%"  style="font-size:10px"> Señor(es): </td>
								<td align="left" width="80%"  style="font-size:10px"> ' . $customer_name . '</td>
							</tr>

							<tr>
								<td align="left" width="20%"  style="font-size:10px"> ' . $doc . ': </td>
								<td align="left" width="80%"  style="font-size:10px"> ' . $nro_doc . '</td>
							</tr>
							<tr>
								<td align="left" width="20%"  style="font-size:10px"> ' . $l_address . ': </td>
								<td align="left" width="80%"  style="font-size:10px"> ' . $customer_address . '</td>
							</tr>
						</table>';
//***************TRJ043 - ALEXANDER ROCA - 30/04/2019**************
            // <tr>
            //     <td align="left" width="20%"  style="font-size:10px"> ' . $l_address . ': </td>
            //     <td align="left" width="80%"  style="font-size:10px"> ' . $customer_address . '</td>
            // </tr>
		}

		$sub = $this->tec->formatMoney($inv['subtotal']);
		$desc = $this->tec->formatMoney($inv['order_discount']); //(-$inv['rounding']) +
		$afc = $this->tec->formatMoney($inv['affected']);
		$exo = $this->tec->formatMoney($inv['non_affected']);
		$exoReal = $this->tec->formatMoney($inv['exonerated']);//****************TRJ008 - ALEXANDER ROCA  - 11/04/2019**********
		$tax_ICBPER = $this->tec->formatMoney($inv['tax_ICBPER']);//***************TRJ068 - ALEXANDER ROCA - 13/08/2019********************
		$gratuita=$this->tec->formatMoney($inv['gratuita']);
		$igv = $this->tec->formatMoney($inv['total_tax']);
		$tot1 = $this->tec->formatMoney($inv['amount']);
		$round = $this->tec->formatMoney($inv['rounding']);
		$tot2 = $this->tec->formatMoney($inv['grand_total']);
		$totLetras = strtoupper(numtoletras($inv['grand_total']) . " " . $currency);

		$this->load->library('Pdf');

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle($tipo_doc . " - " . $numComp);
		$pdf->SetHeaderMargin(30);
		$pdf->SetTopMargin(20);
		$pdf->setFooterMargin(20);
		$pdf->SetAutoPageBreak(true);
		$pdf->SetAuthor('Author');
		$pdf->SetDisplayMode('real', 'default');

		$pdf->AddPage();

		$ivPorc = $this->Settings->default_tax_rate;
		$nombreCorto = strtoupper($this->Settings->site_name);

		$razon_social = $this->session->userdata('razon_social');
		$ruc = $this->session->userdata('ruc');
		$direccion = $this->session->userdata('direccion');
		$region = $this->session->userdata('region');
		$pais = $this->session->userdata('pais');
		$region_pais = strtoupper($region . " - " . $pais);
		$imagen = $this->session->userdata('logo');
		$pos_logo = $this->session->userdata('pos_logo');
//****************TRJ030 - RENATO GAMONAL*******************
		$imagen2 = $this->session->userdata('logo_pdf');
		$type_imagen_pdf = $this->session->userdata('type_imagen_pdf');
//****************TRJ030 - RENATO GAMONAL*******************

		$width = 334;
		$height = 102;

		if ($pos_logo == 2) {
			$width = 334;
			$height = 102;
		} else if ($pos_logo == 1) {
			$width = 84;
			$height = 102;
		}

		$_ruc = $l_ruc . ". " . $ruc;

		//-----------------------------------QR-------------------------------------------//
		$serieNumero = explode('-', $numComp);
		$serie = $serieNumero[0];
		$numero = $serieNumero[1];
		$date = $arrD1[0];
		$hash = $inv['digest_value'];

		if ($inv['document_type'] == 2) {
			$cusDoc = "06";
			$docType = "01";
			$numDoc = $customer_det->cf2;
			$qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
		} else {
			$cusDoc = "01";
			$docType = "03";
			$numDoc = $customer_det->cf1;
			$qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
		}
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
		$ex1 = new QRCodeGenerator();
		$qr_img = '<img src="' . $ex1->generate($serieNumero[0].$serieNumero[1],$qr) . '" width="120" height="120">';
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
		//------------------------------------------------------------------------------//
//****************TRJ030 - RENATO GAMONAL*******************
	if ($type_imagen_pdf == 1){	
		$cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen) . '" width="' . $width . '" height="' . $height . '">';
		$cabecera2 = '<div><br>' . $razon_social . '<br>' . $direccion . '<br>' . $region_pais . '<br>' . $_ruc.'</div>';
           } 
	else if ($type_imagen_pdf == 2) {
		$width2 = 370;
		$height2 = 150;
		$cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen2) . '" width="' . $width2 . '" height="' . $height2 . '">';
		$cabecera2 ="";
    }
//****************TRJ030 - RENATO GAMONAL*******************  
//****************TRJ009 - ALEXANDER ROCA - 19/03/2019 *******************
//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
		$tbl = <<<EOD
            <table border="0" width="100%" cellspacing="0" cellpadding="2" >
                <tr>
                    <td width="65%" align="center" style="font-size:10px">
                        $cabecera
						$cabecera2
                    </td>
                    <td width="35%" valign="middle"><br><br><br><br>
                        <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                            <tr>
                                <td align="center"  style="font-size:10px"><h2>$_ruc</h2></td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#E4E4E4" style="font-size:10px"><h2>$tipo_doc</h2></td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size:10px"><h2>$numComp</h2></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                <td width="65%" align="left">

                        $impRuc

                    </td>

                    <td width="35%">

                        <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                        <tr>
                                <td align="left" style="font-size:10px"> <u>$region</u> ,  <u>     $dia     </u>   de   <u>   $mes   </u>   del   <u>     $ano     </u> </td>
                        </tr>
                        <tr>
                                <td align="left"  style="font-size:10px"> $l_currency: $currency</td>
                        </tr>

                        </table>

                    </td>
                </tr>
            </table>

                <br><br>

                <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                    <tr>
                        <td bgcolor="#E4E4E4" align="center" width="10%" style="font-size:10px"><strong>$l_qty</strong></td>
                        <td bgcolor="#E4E4E4" align="center" width="60%" style="font-size:10px"><strong>$l_description</strong></td>
                        <td bgcolor="#E4E4E4" align="center" width="15%" style="font-size:10px"><strong>Precio Unitario</strong></td>
                        <td bgcolor="#E4E4E4" align="center" width="15%" style="font-size:10px"><strong>$l_importe</strong></td>
                    </tr>
                    $row_items_html
                </table>
                <table border="0" width="100%" cellspacing="0" cellpadding="2" >
					<tr>
                        <td align="left" style="font-size:10px">SON: $totLetras</td>
                    </tr>
                </table>
                <table border="0" width="100%" cellspacing="0" cellpadding="2" >
                    <tr>
                        <td  width="70%" align="center">
							<table border="0" cellspacing="0" cellpadding="2" >
								<tr>
									<td align="center">$qr_img</td>
									<br>
								</tr>
                                <tr>
                                    <td align="center" style="font-size:10px"><i>CANCELADO</i></td>
                                </tr>
                                <BR>
                                <tr>
                                    <td align="center" style="font-size:10px"><u>$region</u> ,  __________<!--$dia--> de __________<!--$mes--> del __________<!--$ano--> </td>
                                </tr>
                            </table>
                        </td>

                        <td align="center" width="30%">

                        <br><br>

                            <table border="1" width="100%" cellspacing="0" cellpadding="2" >
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Sub Total</strong></td>
                                    <td align="right" width="40%" style="font-size:10px">$sub</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Descuento</strong></td>
                                    <td align="right" width="40%" style="font-size:10px">$desc</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Total</strong></td>
                                    <td align="right" width="40%" style="font-size:10px">$tot1</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Op. Gravadas</strong></td>
                                    <td align="right" width="40%" style="font-size:10px">$afc</td>
                                </tr>
								<tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>$op_exoReal</strong></td>
                                    <td align="right" width="40%" style="font-size:10px">$exoReal</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>$op_exo</strong></td>
                                    <td align="right" width="40%" style="font-size:10px">$exo</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>IGV - $ivPorc %</strong></td>
                                    <td align="right" width="40%" style="font-size:10px">$igv</td>
								</tr>
								<tr>
									<td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>$op_ICBPER</strong></td>
									<td align="right" width="40%" style="font-size:10px">$tax_ICBPER</td>
								</tr>
								<tr>
									<td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Redondeo</strong></td>
									<td align="right" width="40%" style="font-size:10px">$round</td>
								</tr>
                                <tr>
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Total Venta</strong></td>
                                    <td align="right" width="40%" style="font-size:10px">$tot2</td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>

                <br>
EOD;
//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
//****************TRJ009 - ALEXANDER ROCA - 19/03/2019 *******************
		$pdf->writeHTML($tbl, true, false, false, false, '');

		$nombPDF = randomstring(20);

		if ($inv['document_type'] == 1) {

            ///GRABA EN TABLA HISTORIAL
			$rutaTabla = array(
				'sale_id' => $sale_id,
				'file_name' => 'boletas/' . $nombPDF . '.pdf',
			);
			$this->db->insert('sales_files', $rutaTabla);
            //GUARDA EL PDF FISICO
			$pdf->Output(FCPATH . 'uploads/pdf/boletas/' . $nombPDF . '.pdf', 'F');

			return true;

		} else {

            ///GRABA EN TABLA HISTORIAL
			$rutaTabla = array(
				'sale_id' => $sale_id,
				'file_name' => 'facturas/' . $nombPDF . '.pdf',
			);
			$this->db->insert('sales_files', $rutaTabla);
            //GUARDA EL PDF FISICO
			$pdf->Output(FCPATH . 'uploads/pdf/facturas/' . $nombPDF . '.pdf', 'F');

			return true;
		}

		return false;

	}/***********TRJ088 - KENY PONTE - ******************** */
	function generar_pdf1($sale_id, $inv, $items, $customer_det){
		// $inv = $this->pos_model->getSaleByID($sale_id);



		//Idioma ---------------
		$region = $this->session->userdata('region');
		$l_ruc = lang("ccf2");
		$l_dni = lang("ccf1");
		$l_address = lang("scf2");
		$arrMes = lang("months");
		$l_qty = lang("qty");
		$l_description = lang("description");
		$l_importe = lang("importe");
		$l_currency = lang("currency_name");
		$op_exo = lang("op_exonerated");
		$op_exoReal=lang("op_exo");//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
		//----------------------
		$tipo_doc = "";
		$impRuc = "";
		if ($inv['document_type'] == 1) {
			$tipo_doc = lang('bill_title');
		} else {
			$tipo_doc = lang('invoice_title');
		}

		$numComp = $inv['invoice_id'];

		$symbol = "";
		$currency = "";
		if ($inv['currency'] == 'PEN') {
            // $symbol = "S/ ";
			$currency = strtoupper(lang("currency_pen"));
		} else if ($inv['currency'] == 'USD') {
            // $symbol = "$ ";
			$currency = lang("currency_USD");
		}

		$arrD1 = explode(" ", $inv['date']);
		$arrD2 = explode("-", $arrD1[0]);
		$ano = $arrD2[0];
		$dia = $arrD2[2];
		$mes = $arrMes[(int)$arrD2[1] - 1];
		$fecha_emision=$arrD1[0];//********************************TRJ088 - KENY PONTE 02/09/2019******************* */
		$hora_emision=$arrD1[1];//********************************TRJ088 - KENY PONTE 02/09/2019******************* */
		$row_items_html = "";	
		//----------------------------------------------------------------
	
	//----------------------------------------------------------------
		/*******************************TRJ088 - KENY PONTE -************************************************* */
		$fecha = new DateTime($fecha_emision);
		$fecha1 = new DateTime($fecha_emision);
		$fecha->modify('first day of this month'); //muestra el primer dia del mes
		$fecha1->modify('last day of this month');//muestra el ultimo dia del mes
		$fecha_inicio=$fecha->format('Y-m-d'); // 
		$fecha_fin=$fecha1->format('Y-m-d');
		/***********************TRJ088 -  KENY PONTE - 12/09/2019**********************************/
		foreach ($items as $item) {
			$quantity = $this->tec->formatNumber($item['quantity']);
			$uprice = $this->tec->formatMoney($item['unit_price']);
			$subt = $this->tec->formatMoney($item['subtotal']);
			//***********************************TRJ024 - ALEXANDER ROCA - 01/04/2019**********************
			$nombreProducto=str_replace("<","&lt;",$item['name']);
			$nombreProducto=str_replace(">","&gt;",$nombreProducto);
			$code=$item['code'];
			$id=$item['product_id'];
			$row=$this->site->getUMProducto($id,$code);//*******************TRJ088 - KENY PONTE - 11/09/2019*********** */
			$presentation=$row->presentation;//**************************TRJ088 - KENY PONTE - 11/09/2019************* */
			
			
            $quantity1.=$quantity.'<br><br>';//acumulador de la lista de cantidades
			$codigo.=$code.'<br><br>';//acumulador de la lista de codigos de los productos
            $unidad_medida.=$presentation.'<br><br>';//unidad de medida
        	$nombreProducto1.=$nombreProducto.'<br><br>';//nombre del producto
            $uprice1.=$uprice.'<br><br>';//precio unitario
            $subt1.=$subt.'<br><br>';//sub total
                   
			}
            $row_items_html=' <tr>
       				<td valign="top"align="center" HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$quantity1.'</td>
       				<td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$codigo.'</td>
        			<td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$unidad_medida.'</td>
        			<td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$nombreProducto1.'</td>
        			<td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$uprice1.'</td>
        			<td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$subt1.'</td>
					</tr>';
				/*******************TRJ088 - KENY PONTE -**********************/

		$customer_name = $customer_det->name;
		$customer_address = $customer_det->direccion;

		if ($inv['document_type'] == 2) {
			$emision="REPRESENTACIÓN IMPRESA DE LA FACTURA ELECTRÓNICA";
	  /**************************TRJ088 - KENY PONTE - *********************************/
			if($customer_det->cf1 != null){
				$tipo='SEÑOR(A)';
			}else{
			$tipo='RAZON SOCIAL';
			}
				$impRuc='<br>
							<table border="1"width="100%" cellspacing="0" cellpadding="2" >
								<tr>
									<td>
										<table>
											<tr>
												<td  style="font-size:8px;"width="20%">'.$tipo.'</td>
												<td  style="font-size:8px;"width="60%">'.$customer_name.'</td>
												<td rowspan="2" valign="top" align="right"style="font-size:8px;"width="20%">'.$l_ruc.' : '.$customer_det->cf2. '</td>
											</tr>
											<tr>
												<td align="left" style="font-size:8px;">'.$l_address.'</td>
												<td  style="font-size:8px;">'.$customer_address.'</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						<br>';
						/**********************************TRJ088 - KENY PONTE -*************************************** */
            } else {
				$emision="REPRESENTACIÓN IMPRESA DE LA BOLETA ELECTRÓNICA";
				// $impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" >
				//             <tr>
				//                 <td align="left"  style="font-size:10px">' . $region . ', <u> '.$dia.' </u> de <u> '.$mes.' </u> del <u> '.$ano.' </u><br></td>
				//             </tr>
				//         </table>';
	
				$doc = $l_dni;
				$nro_doc = $customer_det->cf1;
					$tipo='SEÑOR(A)';	/*************************************TRJ088 - KENY PONTE -************************************************** */
				if($customer_det->cf1 == null && $customer_det->cf2 != null){
					$doc = $l_ruc;
					$nro_doc = $customer_det->cf2;
					$tipo='RAZON SOCIAL';	/*************************************TRJ088 - KENY PONTE -************************************************** */
				}
				
	//***************TRJ043 - ALEXANDER ROCA - 30/04/2019**************
	/*************************************TRJ088 - KENY PONTE -************************************************** */
				$impRuc = '<br>
							<table border="1"width="100%" rules="none" cellspacing="0" cellpadding="2" >
								<br>
									<td>
										<table>
											<tr>
												<td  style="font-size:8px;" width="20%">'.$tipo.'</td>
												<td  style="font-size:8px;"width="60%">'.$customer_name.'</td>
												<td rowspan="2" valign="top" style="font-size:8px;" width="20%">'.$doc.':'.$nro_doc.'</td>
											</tr>
											<tr>
												<td align="left" style="font-size:8px;">'.$l_address.'</td>
												<td  style="font-size:8px;">'.$customer_address.'</td>
											</tr>
										</table>
									</td>
								</tr>		
							</table>
							<br>';
	//*****************************************TRJ088 - KENY PONTE -************************************************************** */						
	//***************TRJ043 - ALEXANDER ROCA - 30/04/2019**************
				// <tr>
				//     <td align="left" width="20%"  style="font-size:10px"> ' . $l_address . ': </td>
				//     <td align="left" width="80%"  style="font-size:10px"> ' . $customer_address . '</td>
				// </tr>
			}
			$sub = $this->tec->formatMoney($inv['subtotal']);
			$desc = $this->tec->formatMoney($inv['order_discount']); //(-$inv['rounding']) +
			$afc = $this->tec->formatMoney($inv['affected']);
			$exo = $this->tec->formatMoney($inv['non_affected']);
			$exoReal = $this->tec->formatMoney($inv['exonerated']);//****************TRJ008 - ALEXANDER ROCA  - 11/04/2019**********
			$tax_ICBPER = $this->tec->formatMoney($inv['tax_ICBPER']);//***************TRJ068 - ALEXANDER ROCA - 13/08/2019********************
			$grat=$this->tec->formatMoney(0);
			$igv = $this->tec->formatMoney($inv['total_tax']);
			$tot1 = $this->tec->formatMoney($inv['amount']);
			$round = $this->tec->formatMoney($inv['rounding']);
			$tot2 = $this->tec->formatMoney($inv['grand_total']);
			$totLetras = strtoupper(numtoletras($inv['grand_total']) . " " . $currency);

		$this->load->library('Pdf');

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle($tipo_doc . " - " . $numComp);
		$pdf->SetHeaderMargin(30);
		$pdf->SetTopMargin(20);
		$pdf->setFooterMargin(20);
		$pdf->SetAutoPageBreak(true);
		$pdf->SetAuthor('Author');
		$pdf->SetDisplayMode('real', 'default');

		$pdf->AddPage();

		$ivPorc = $this->Settings->default_tax_rate;
		$nombreCorto = strtoupper($this->Settings->site_name);

		$razon_social = $this->session->userdata('razon_social');
		$ruc = $this->session->userdata('ruc');
		$direccion = $this->session->userdata('direccion');
		$region = $this->session->userdata('region');
		$pais = $this->session->userdata('pais');
		$region_pais = strtoupper($region . " - " . $pais);
		$imagen = $this->session->userdata('logo');
		$pos_logo = $this->session->userdata('pos_logo');
//****************TRJ030 - RENATO GAMONAL*******************
		$imagen2 = $this->session->userdata('logo_pdf');
		$type_imagen_pdf = $this->session->userdata('type_imagen_pdf');
//****************TRJ030 - RENATO GAMONAL*******************

		$width = 334;
		$height = 102;

		if ($pos_logo == 2) {
			$width = 334;
			$height = 102;
		} else if ($pos_logo == 1) {
			$width = 84;
			$height = 102;
		}

		$_ruc = $l_ruc . ". " . $ruc;

		//-----------------------------------QR-------------------------------------------//
		$serieNumero = explode('-', $numComp);
		$serie = $serieNumero[0];
		$numero = $serieNumero[1];
		$date = $arrD1[0];
		$hash = $inv['digest_value'];

		if ($inv['document_type'] == 2) {
			$cusDoc = "06";
			$docType = "01";
			$numDoc = $customer_det->cf2;
			$qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
		} else {
			$cusDoc = "01";
			$docType = "03";
			$numDoc = $customer_det->cf1;
			$qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
		}
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
		$ex1 = new QRCodeGenerator();
		$qr_img = '<img src="' . $ex1->generate($serieNumero[0].$serieNumero[1],$qr) . '" width="80" height="80">';
//****************TRJ011 - ALEXANDER ROCA - 19/03/2019 *******************
		//------------------------------------------------------------------------------//
//****************TRJ030 - RENATO GAMONAL*******************
	if ($type_imagen_pdf == 1){	
		$cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen) . '" width="' . $width . '" height="' . $height . '">';
		$cabecera2 = '<div><br>' . $razon_social . '<br>' . $direccion . '<br>' . $region_pais . '<br>' . $_ruc.'</div>';
           } 
	else if ($type_imagen_pdf == 2) {
		$width2 = 370;
		$height2 = 150;
		$cabecera = '<img class="aligncenter" src="' . base_url('uploads/' . $imagen2) . '" width="' . $width2 . '" height="' . $height2 . '">';
		$cabecera2 ="";
    }

//****************TRJ030 - RENATO GAMONAL*******************  
//****************TRJ009 - ALEXANDER ROCA - 19/03/2019 *******************
//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
//**********************************TRJ088 - KENY PONTE -****************************************** */
$u=$this->site->getURLconsulta();
$url=$u->footer;
if($tax_ICBPER>0){
	$rowspan=9;
	$bol_plasticas='<tr>
	<td colspan="1"  align="left" style="font-size:7px;border: 1px solid #000000">'.$op_ICBPER.'</td>
	<td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">'.$tax_ICBPER.'</td>
  </tr> ';
  
  }
  else if($tax_ICBPER<=0){
	$rowspan=8;
	$bol_plasticas='';
  }
  
$tbl1 = <<<EOD
	<table border="0" width="100%" cellspacing="0" cellpadding="2" >
		<tr>
			<td width="70%" align="center" style="font-size:10px">
			$cabecera
			</td>
			<td width="30%" valign="middle"><br><br><br>
				<table border="1" width="100%" cellspacing="0" cellpadding="8" >
					<tr>
						<td align="center"  style="font-size:10px"><h2 style="font-size:8px">$_ruc</h2></td>
					</tr>
					<tr>
						<td align="center" bgcolor="#E4E4E4" style="font-size:10px"><h2 style="font-size:8px">$tipo_doc</h2></td>
					</tr>
					<tr>
						<td align="center" style="font-size:10px"><h2 style="font-size:8px">$numComp</h2></td>
					</tr>
				</table><br>
			</td>
		</tr>
	</table>
	<br>
	<table border="1" width="100%" cellspacing="0" cellpadding="" >
		<tr>
			<td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">FECHA DE EMISION</strong></td>
			<td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">HORA DE EMISION</strong></td>
			<td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">FECHA DE VENCIMIENTO</strong></td>
			<td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">FECHA DE INICIO DE PERIODO</strong></td>
			<td bgcolor="#1B3752" align="center" width="20%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">FECHA DE FIN DE PERIODO</strong></td>
		</tr>

		<tr>
			<td align="center"style="font-size:8px">$fecha_emision</td>
			<td align="center"style="font-size:8px">$hora_emision</td>
			<td align="center"style="font-size:8px"></td>
			<td align="center"style="font-size:8px">$fecha_inicio</td>
			<td align="center"style="font-size:8px">$fecha_fin</td>
		</tr>

	</table><br>

	$impRuc
	
	<br>
	<table  width="100%" cellspacing="0" >
	<tr height="30">
		<th bgcolor="#1B3752" align="center" width="7.5%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CANTIDAD</th>
		<th bgcolor="#1B3752" align="center" width="11%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CODIGO</th>
		<th bgcolor="#1B3752" align="center" width="12%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">UNIDAD MEDIDA</th>
		<th bgcolor="#1B3752" align="center" width="51%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">DESCRIPCION</th>
		<th bgcolor="#1B3752" align="center" width="9.5%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">PRECIO UNITARIO</th>
		<th bgcolor="#1B3752" align="center" width="9%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">IMPORTE</th>
	</tr>

		$row_items_html
		<tr>
			<td colspan="4" rowspan="$rowspan" style="font-size:10px">SON:<br> $totLetras</td>
			<td colspan="1" align="left" style="border: 1px solid #000000;font-size:7px">Sub Total</td>
			<td colspan="1" align="right"  style="font-size:8px;border: 1px solid #000000">$sub</td>
		</tr>
		<tr>
			<td colspan="1" align="left"  style="border: 1px solid #000000;font-size:7px">op.gravada</td>
			<td colspan="1" align="right"  style="font-size:8px;border: 1px solid #000000">$afc</td>
		</tr>
		<tr>
			<td colspan="1" align="left"  style="border: 1px solid #000000;font-size:7px">$op_exo</td>
			<td colspan="1" align="right"  style="font-size:8px;border: 1px solid #000000">$exo</td>
		</tr>
		<tr>
			<td colspan="1" align="left" style="border: 1px solid #000000;font-size:7px">$op_exoReal</td>
			<td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">$exoReal</td>
		</tr>
		<tr>
			<td colspan="1"  align="left" style="border: 1px solid #000000;font-size:7px">op.gratuita</td>
			<td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">$grat</td>
		</tr>
		<tr>
			<td colspan="1"  align="left" style="border: 1px solid #000000;font-size:7px">I.G.V-$ivPorc %</td>
			<td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">$igv</td>
		</tr>
		$bol_plasticas
		<tr>
			<td colspan="1"  align="left" style="border: 1px solid #000000;font-size:7px">importe total</td>
			<td colspan="1" align="right" style="font-size:8px;border: 1px solid #000000">$tot2</td>
		</tr>
	</table>    
	<br><br>	
	<table>
		<tr style="font-size:10px">
			<td align="left" width="20%">$qr_img</td>                     
			<td  width="62%" style="font-size:7px;vertical-align: bottom">$emision $url</td>
		</tr>
	</table>
	<br>
EOD;
/*******************************TRJ088 - KENY PONTE -//************************************************ */
//***************TRJ008 - ALEXANDER ROCA - 11/04/2019********************
//****************TRJ009 - ALEXANDER ROCA - 19/03/2019 *******************
$pdf->writeHTML($tbl1, true, false, false, false, '');

$nombPDF = randomstring(20);

if ($inv['document_type'] == 1) {

	///GRABA EN TABLA HISTORIAL
	$rutaTabla = array(
		'sale_id' => $sale_id,
		'file_name' => 'boletas/' . $nombPDF . '.pdf',
	);
	$this->db->insert('sales_files', $rutaTabla);
	//GUARDA EL PDF FISICO
	$pdf->Output(FCPATH . 'uploads/pdf/boletas/' . $nombPDF . '.pdf', 'F');

	return true;

} else {

	///GRABA EN TABLA HISTORIAL
	$rutaTabla = array(
		'sale_id' => $sale_id,
		'file_name' => 'facturas/' . $nombPDF . '.pdf',
	);
	$this->db->insert('sales_files', $rutaTabla);
	//GUARDA EL PDF FISICO
	$pdf->Output(FCPATH . 'uploads/pdf/facturas/' . $nombPDF . '.pdf', 'F');

	return true;
}

return false;		
}


}
