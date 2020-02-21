<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Credit_note extends MY_Controller
{

    function __construct() {
        parent::__construct();


        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('credit_note_model');
    }


    function index()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('credit_note');
        $bc = array(array('link' => '#', 'page' => lang('credit_note')));
        $meta = array('page_title' => lang('credit_note'), 'bc' => $bc);
        $this->page_construct('credit_note/index', $this->data, $meta);
    }
	
	function add()
    {
		$this->form_validation->set_rules('correlative2', lang('correlative2'), 'required');
        
        if ($this->form_validation->run() == true) {
			
			$correclativo=$this->input->post('correlative2');
			
			if($correclativo <= 0){
				$this->session->set_flashdata('warning', lang("valid_NC_correlative"));
                redirect("credit_note/add");
			}else{
				
				if($correclativo > 10000000){
					$this->session->set_flashdata('warning', lang("valid_NC_correlative_max"));
					redirect("credit_note/add");
					
				}else{
					
					$sustenance=$this->input->post('sustenance');
					
					if(strlen($sustenance)<=0){
						$this->session->set_flashdata('error', lang("valid_NC_sustenance"));
						redirect("credit_note/add");
					}else{
						
						if(strlen($sustenance)<=5){
							$this->session->set_flashdata('warning', lang("valid_NC_sustenance_min"));
							redirect("credit_note/add");
						}else{
							
							$serieLocal = $this->input->post('Select_serie');
							$local=substr($serieLocal, 0, 1);
							$typeComprobante=substr($serieLocal, 1, 1);
							$serieComprobante=substr($serieLocal, 1);
							
							$resto=($correclativo%1000000);
							if($resto == $correclativo){
								
								$resto=($correclativo%100000);
								if($resto == $correclativo){
									
									$resto=($correclativo%10000);
									if($resto == $correclativo){
										
										$resto=($correclativo%1000);
										if($resto == $correclativo){
											
											$resto=($correclativo%100);
											if($resto == $correclativo){
												
												$resto=($correclativo%10);
												if($resto == $correclativo){
													$invoice_comprobante=$serieComprobante."-000000".$correclativo;
												}else{
													$invoice_comprobante=$serieComprobante."-00000".$correclativo;
												}
												
											}else{
												$invoice_comprobante=$serieComprobante."-0000".$correclativo;
											}
											
										}else{
											$invoice_comprobante=$serieComprobante."-000".$correclativo;
										}
										
									}else{
										$invoice_comprobante=$serieComprobante."-00".$correclativo;
									}
									
								}else{
									$invoice_comprobante=$serieComprobante."-0".$correclativo;
								}
								
							}else{
								$invoice_comprobante=$serieComprobante."-".$correclativo;
							}
							
							$resultado=$this->credit_note_model->valid_invoice($invoice_comprobante);
							
							if($resultado==false){
								$this->session->set_flashdata('error', lang("valid_NC_num_invoice").$invoice_comprobante.lang("not_exist"));
								redirect("credit_note/add");
							}else{
								
								if(strcmp($typeComprobante,"F")==0){
									$typeInvoice=1; //"01";
								}else{
									$typeInvoice=3; //"03";
								}
								
								$date=date("Y")."-".date("m")."-".date("d");
								$hora=date("H").":".date("i").":".date("s");
								//echo $date ." ".$hora;
								
								
								
								foreach($resultado as $resul_dato){
									
									if($resul_dato->estado==0){
										$this->session->set_flashdata('error', lang("valid_NC_num_invoice").$invoice_comprobante.lang("status_canceled"));
										redirect("credit_note/add");
									}
									
									$customer_data=$this->credit_note_model->valid_customer($resul_dato->customer_id);
									
									if($customer_data==false){
										$this->session->set_flashdata('error', lang("customer_not_exist"));
										redirect("credit_note/add");
									}else{
									
										foreach($customer_data as $customer){
											
											$id_customer=$customer->id;
											
											if(strcmp($customer->cf2,"")==0){
												$customer_document=$customer->cf1;
												$customer_document_type=$customer->document_type_id;//***********TRJ073 - KENY PONTE  - 15/09/2019********* */
											
											}else{
												$customer_document=$customer->cf2;
												$customer_document_type="6";//**********TRJ0734 - KENY PONTE 17/09/2019 */
											}
										}
										$Series_correlatives=$this->credit_note_model->format_invoce_NC();
										
										foreach($Series_correlatives as $Serie_correlative){
											if(strcmp($typeComprobante,"F")==0){
												$serieNum=$Serie_correlative->note_credit_invoice_format;
												$correlative=$Serie_correlative->note_credit_invoice_number + 1;
											}else{
												$serieNum=$Serie_correlative->note_credit_bill_format;
												$correlative=$Serie_correlative->note_credit_bill_number + 1;
											}
										}
										
										$serieNum = explode("-", $serieNum);
										
										$restoNC=($correlative%1000000);
										if($restoNC == $correlative){
											
											$restoNC=($correlative%100000);
											if($restoNC == $correlative){
												
												$restoNC=($correlative%10000);
												if($restoNC == $correlative){
													
													$restoNC=($correlative%1000);
													if($restoNC == $correlative){
														
														$restoNC=($correlative%100);
														if($restoNC == $correlative){
															
															$restoNC=($correlative%10);
															if($restoNC == $correlative){
																$invoice_NC=$serieNum[0]."-000000".$correlative;
															}else{
																$invoice_NC=$serieNum[0]."-00000".$correlative;
															}
															
														}else{
															$invoice_NC=$serieNum[0]."-0000".$correlative;
														}
														
													}else{
														$invoice_NC=$serieNum[0]."-000".$correlative;
													}
													
												}else{
													$invoice_NC=$serieNum[0]."-00".$correlative;
												}
												
											}else{
												$invoice_NC=$serieNum[0]."-0".$correlative;
											}
											
										}else{
											$invoice_NC=$serieNum[0]."-".$correlative;
										}
										
										$sale_id=$resul_dato->id;
															
										$data= array(
											"sale_id"=> $sale_id,
											"responseCode"=> (string)("0".$this->input->post('Select_motive_note_credit')),//"07",
											"invoiceTypeCode"=> (string)("07"),// valor con el que sunat reconoce que el comprobante es nota de credito
											"serieNumero"=> (string)($invoice_NC),//("FC01-0000123"),
											"issueDate"=> (string)($date),//"2019-06-07",
											"issueTime"=> (string)($hora),//"20:25:41",
											"documentCurrencyCode"=> (string)($resul_dato->currency),//"PEN",
											"supplierDocumentID"=> (string)("6"), // valor con el que sunat reconoce que el documento del cliente final es RUC
											"customerDocumentID"=> (string)($customer_document_type), // valor con el que sunat reconoce que el documento del cliente emisor es RUC
											"customerID"=> (string)($customer_document),//($id_customer),//"20544105494",
											"customerName"=> (string)($resul_dato->customer_name),//"ACTEC PERU S.A.C.",
											"taxAmount"=> (string)($resul_dato->product_tax),//"6.65",
											"taxableAmount"=> (string)($resul_dato->affected),//"36.95",
											"non_affected" => (string)($resul_dato->non_affected),// inafecto
											"exonerated" => (string)($resul_dato->exonerated),// exonerado
											"taxSubtotal"=> (string)($resul_dato->total_tax),//"6.65",
											"payableAmount"=> (string)($resul_dato->amount),//"108.6"
											"user_create"=> $this->session->userdata('user_id'),//"1",
											"date_create"=> ($date." ".$hora)//"2019-07-08 10:10:01"
										);
										
									}
								}

								$tipo_note_credit = $this->input->post('Select_motive_note_credit');

								if($tipo_note_credit ==5 || $tipo_note_credit ==7 || $tipo_note_credit ==3){

									$cant_items = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
									
									if($cant_items > 0){
										
										$cont=1;
										
										$tax=0;
										$exonerado=0;
										$inafecto=0;
										$afecto=0;
										$sumaTotalCompro=0;
										
										for ($r = 0; $r < $cant_items; $r++) {
											
											$item_id = $_POST['product_id'][$r];
											
											if($tipo_note_credit ==3){}else{
											
												if($tipo_note_credit ==5){
													$item_qty = 1;
													$item_price_con_IGV = $_POST['product_subtotal'][$r];
												}else{
													if($tipo_note_credit ==7){
														$item_qty = $_POST['product_quantity'][$r];
														$item_price_con_IGV = $_POST['product_unit_price'][$r];
													}
												}

												
												$item_type_tax = $_POST['product_tax'][$r];
												
												$sumaTotalCompro = $sumaTotalCompro + ($item_price_con_IGV * $item_qty);
												
												if($item_type_tax == 1 ||$item_type_tax == 0){
													
													$item_price_sin_IGV= ($item_price_con_IGV/1.18);
													$item_IGV= $item_price_con_IGV - $item_price_sin_IGV;
													$afecto= $afecto + ($item_price_sin_IGV*$item_qty);
												
												}else{
													if($item_type_tax == 2){
														$item_price_sin_IGV=$item_price_con_IGV;
														$inafecto= $inafecto + ($item_price_sin_IGV*$item_qty);
														$item_IGV= 0;
													}else{
														if($item_type_tax == 3){
															$item_price_sin_IGV=$item_price_con_IGV;
															$exonerado = $exonerado + ($item_price_sin_IGV*$item_qty);
															$item_IGV= 0;
														}
													}
												}
												$tax= $tax + ($item_IGV*$item_qty);
												$item_IGVs = $item_IGV * $item_qty;
											
											}
											

											$result_items = $this->credit_note_model->valid_sale_items_id($item_id);

											//$sum_price = $result_item->affect_price + $result_item->non_affected_price + $result_item->exonerated_price;
											foreach($result_items as $result_item){
												
												
												$name = $result_item->name;
												
												if($tipo_note_credit ==3){
													$item_price_con_IGV = $result_item->unit_price;
													$item_qty = $result_item->quantity;
													$item_IGVs = $result_item->item_tax;
													$item_price_sin_IGV = $result_item->affect_price + $result_item->non_affected_price + $result_item->exonerated_price;
													$name = "DICE: ".$result_item->name." DEBE DECIR:".$_POST['product_name'][$r];
												}
											
												$items[$cont]= array(
														"Quantity"=> (string)($item_qty),//($result_item->quantity),//"2",
														"LineExtensionAmount"=> (string)($this->tec->formatDecimal($item_price_con_IGV * $item_qty)),//"20",//con igv
														"PricingReference"=> (string)($this->tec->formatDecimal($item_price_con_IGV)),//"50",//con igv
														"PriceTypeCode"=> (string)("01"),
														"TaxTotalTaxAmount"=> (string)($this->tec->formatDecimal($item_IGVs)),//"3.6",
														"TaxSubtotalTaxableAmount"=> (string)($this->tec->formatDecimal($item_price_sin_IGV * $item_qty)),//"20",//total precio sin igv
														"PriceAmount"=> (string)($this->tec->formatDecimal($item_price_sin_IGV)),//"40",//sin igv
														"TaxSubtotalTaxAmount"=> (string)($this->tec->formatDecimal($item_IGVs)),//"3.6",
														"TaxPercent"=> (string)($result_item->igv),//"18",
														"tax_method"=> ($result_item->tax_method),//"18",
														"Description"=> (string)($name),//"Producto Prueba 01",
														"itemSellersID"=> (string)($result_item->code),//"123"
												);
											}
											
											$cont = $cont + 1;

										}
										
										if($tipo_note_credit ==3){
											$data["taxAmount"] = (string)(0);
											$data["taxableAmount"] = (string)(0);
											$data["non_affected"] = (string)(0);
											$data["exonerated"] = (string)(0);
											$data["taxSubtotal"] = (string)(0);
											$data["payableAmount"] = (string)(0);
										}else{
											$data["taxAmount"] = (string)($this->tec->formatDecimal($tax));
											$data["taxableAmount"] = (string)($this->tec->formatDecimal($afecto));
											$data["non_affected"] = (string)($this->tec->formatDecimal($inafecto));
											$data["exonerated"] = (string)($this->tec->formatDecimal($exonerado));
											$data["taxSubtotal"] = (string)($this->tec->formatDecimal($tax));
											$data["payableAmount"] = (string)($this->tec->formatDecimal($sumaTotalCompro));
										}
									
									}else{
										$this->session->set_flashdata('warning', lang("items_min"));
										redirect("credit_note/add");
									}
									
									
								}else{
									
									if($tipo_note_credit ==4){
										
										$descuento_global = $this->input->post('Descuento_global_comprobante');
										
										/*if(strlen($descuento_global)<=0 || $descuento_global>0){
											
											$this->session->set_flashdata('warning', lang("descuento_global_valid_null"));
											redirect("credit_note/add");
											
										}else{*/

										$contador=1;
											if($data["payableAmount"] >= $descuento_global && $descuento_global > 0){
												
												$item_price_sin_IGV= ($descuento_global/1.18);
												$item_IGV= $descuento_global - $item_price_sin_IGV;
										
												$items[$contador]= array(
														"Quantity"=> (string)(1),//"2",
														"LineExtensionAmount"=> (string)($this->tec->formatDecimal($descuento_global)),//"20",//con igv
														"PricingReference"=> (string)($this->tec->formatDecimal($descuento_global)),//"50",//con igv
														"PriceTypeCode"=> (string)("01"),
														"TaxTotalTaxAmount"=> (string)($this->tec->formatDecimal($item_IGV)),//"3.6",
														"TaxSubtotalTaxableAmount"=> (string)($this->tec->formatDecimal($item_price_sin_IGV)),//"20",//total precio sin igv
														"PriceAmount"=> (string)($this->tec->formatDecimal($descuento_global)),//"40",//sin igv//**************************************
														"TaxSubtotalTaxAmount"=> (string)($this->tec->formatDecimal($item_IGV)),//"3.6",
														"TaxPercent"=> (string)(18),//"18",
														"tax_method"=> (string)(0),//"18",
														"Description"=> (string)($sustenance),//"Producto Prueba 01",
														"itemSellersID"=> (string)(0),//"123"
												);
												
												
												$data["taxAmount"] = (string)($this->tec->formatDecimal($item_IGV));
												$data["taxableAmount"] = (string)($this->tec->formatDecimal($item_price_sin_IGV));
												$data["non_affected"] = (string)(0);
												$data["exonerated"] = (string)(0);
												$data["taxSubtotal"] = (string)($this->tec->formatDecimal($item_IGV));
												$data["payableAmount"] = (string)($this->tec->formatDecimal($descuento_global));
												
												
											}else{
												$this->session->set_flashdata('warning', lang("descuento_global_valid_monto"));
												redirect("credit_note/add");
											}
										
										//}
										
										
									}else{
								
										$result_items = $this->credit_note_model->valid_sale_items($sale_id);
										$cont=1;
										
										foreach($result_items as $result_item){
											
											$sum_price = $result_item->affect_price + $result_item->non_affected_price + $result_item->exonerated_price;
											
											$items[$cont]= array(
													"Quantity"=> (string)($result_item->quantity),//"2",
													"LineExtensionAmount"=> (string)($this->tec->formatDecimal($result_item->unit_price * $result_item->quantity)),//"20",//con igv
													"PricingReference"=> (string)($result_item->unit_price),//"50",//con igv
													"PriceTypeCode"=> (string)("01"),
													"TaxTotalTaxAmount"=> (string)($result_item->item_tax),//"3.6",
													"TaxSubtotalTaxableAmount"=> (string)($this->tec->formatDecimal($sum_price * $result_item->quantity)),//"20",//total precio sin igv
													"PriceAmount"=> (string)($sum_price),//"40",//sin igv
													"TaxSubtotalTaxAmount"=> (string)($result_item->item_tax),//"3.6",
													"TaxPercent"=> (string)($result_item->igv),//"18",
													"tax_method"=> ($result_item->tax_method),//"18",
													"Description"=> (string)($result_item->name),//"Producto Prueba 01",
													"itemSellersID"=> (string)($result_item->code),//"123"
											);
											
											$cont = $cont + 1;
										}
									}
								
								}
								
								
								
								$data["referenceID"] = (string)($invoice_comprobante);
								$data["DocumentTypeCode"] = (string)($typeInvoice);
								$data["description"] = (string)($sustenance);
								
								$biz= array(
											"supplierID"=> (string)($this->session->userdata('ruc')),
											"passFirma"=> (string)($this->session->userdata('pass_firma')),
											"supplierName"=> (string)($this->session->userdata('razon_social')),
											"beta"=> (string)$this->session->userdata('beta')
										);
								
								$data_envio = array(
												"ruc"=> (string)($this->session->userdata('ruc')), //"00000000000",
												//"typeCode" => "07",
												//"serieNumero" => "FC01-0000049",
												"usuSol" => $this->session->userdata('user_sol'),//"MODDATOS",
												"passSol" => $this->session->userdata('pass_sol')//"moddatos"
								);
								
								
								$postData["header"] = $data;
								$postData["item"] = $items;
								$postData["information_additional"] = $biz;
								$postData["info_envio"] = $data_envio;
								
								$context = stream_context_create(array(
									'http' => array(
										'method' => 'POST',
										'header' => "Authorization: application/json\r\n" .
											"Content-Type: application/json\r\n",
										'content' => json_encode($postData)
									)
								));
								
								$urlDoc = "register_note_credit";//"post/doc/xml";
								$url_NC = $this->config->item('wsnc_url');
								$response = file_get_contents($url_NC . $urlDoc, false, $context);
								
								$res = json_decode($response, true);
								//var_dump ($res);
								
								// $this->generar_xml_NC($data, $items, $customer_document); // generacion de XML desde codeigniter
								
								/*if(strcmp($res[0], "error con WS")==0){
								//if(strcmp($res, "error con WS")==0){
								}else{
									$this->credit_note_model->update_status_sale($sale_id);
								}*/
								 /************************TRJ088 - KENY PONTE - 02/09/2019********************* */
						$p=$this->site->getPDFformat();
						$pdf=$p->pdf_format;
            /************************TRJ088 - KENY PONTE - 02/09/2019********************* */
								
								if($res[1]>0){
								
									require("funciones.php");
									include("app/class/QRCodeGenerator.class.php");
									if ($pdf==1) {
									$this->generar_pdf($res[1], $data, $items, $res[2]);
									}else if ($pdf==2) {
										$this->generar_pdf1($res[1], $data, $items, $res[2]);

									}
									$this->session->set_flashdata('message', lang("succes_NC").$res[0]);
									// $this->session->set_flashdata('message', lang("succes_NC").$res);
									redirect('credit_note');
								}else{
									$this->session->set_flashdata('error', lang("NC_validated_exi"));
									redirect("credit_note/add");
								}
								
							}
							
						}
					}
				}
				
			}
			
        }else{
			
			$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['page_title'] = lang('add_credit_note');
			
			$valid_num_ser=$this->credit_note_model->valid_Serie_1();
			
			$series_locals=$this->credit_note_model->view_Serie();
			
			if($valid_num_ser->invoice_format == null){
				
				$series_setting = $this->credit_note_model->view_Serie_setting();
				
				if($this->credit_note_model->view_Serie_quantity()>1){
					$cont=0;
					
					foreach($series_locals as $serie_local){

						if($serie_local->id == 1){
							foreach($series_setting as $serie_setting){
								$serieF = explode("-", $serie_setting->invoice_format);
								$all_series[$serie_setting->id.$serieF[0]] = $serieF[0];
								$serieB = explode("-", $serie_setting->bill_format);
								$all_series[$serie_setting->id.$serieB[0]] = $serieB[0];
							
							}
						}else{
							$serieF = explode("-", $serie_local->invoice_format);
							$all_series[$serie_local->id.$serieF[0]]=$serieF[0];
							$serieB = explode("-", $serie_local->bill_format);
							$all_series[$serie_local->id.$serieB[0]] = $serieB[0];
						}
						
						$cont++;
					}
					
				}else{
					foreach($series_setting as $serie_setting){
						$serieF = explode("-", $serie_setting->invoice_format);
						$all_series[$serie_setting->id.$serieF[0]] = $serieF[0];
						$serieB = explode("-", $serie_setting->bill_format);
						$all_series[$serie_setting->id.$serieB[0]] = $serieB[0];
					}
				}
				
			}else{
				
				foreach($series_locals as $serie_local){

						$serieF = explode("-", $serie_local->invoice_format);	
						$all_series[$serie_local->id.$serieF[0]]=$serieF[0];
						$serieB = explode("-", $serie_local->bill_format);
						$all_series[$serie_local->id.$serieB[0]] = $serieB[0];

					}
				
			}
			
			$this->data['num_series']= $all_series;
			
			$motives=$this->credit_note_model->motive_NC();
			
			foreach($motives as $motive){
				$motive_array[$motive->id]=$motive->description_NC;
				
			}
			$this->data['motive_NC']= $motive_array;
			$bc = array(array('link' => site_url('credit_note'), 'page' => lang('credit_note')), array('link' => '#', 'page' => lang('add_credit_note')));
            $meta = array('page_title' => lang('add_credit_note'), 'bc' => $bc);
            $this->page_construct('credit_note/add', $this->data, $meta);
			
		}
		

        
    }
	
	public function generar_xml_NC($data, $items, $customer_document){
		
		
		$header= array(
			"responseCode"=> (string)($data['responseCode']),//"07",
			"invoiceTypeCode"=> (string)("07"),
			"serieNumero"=> (string)($data['serieNumero']),//("FC01-0000123"),
			"supplierID"=> (string)($this->session->userdata('ruc')),
			"passFirma"=> (string)($this->session->userdata('pass_firma')),
			"issueDate"=> (string)($data['issueDate']),//"2019-06-07",
			"issueTime"=> (string)($data['issueTime']),//"20:25:41",
			"documentCurrencyCode"=> (string)($data['documentCurrencyCode']),//"PEN",
			"signatureID"=> (string)($data['serieNumero']),//("FC01-0000123"),//"IDSignST",
			"supplierName"=> (string)($this->session->userdata('razon_social')),
			"URI"=> (string)("#".$data['serieNumero']),//("#FC01-0000123"),
			"supplierDocumentID"=> (string)("6"),
			"customerDocumentID"=> (string)($data['customerDocumentID']),
			"customerID"=> (string)($customer_document),//"20544105494",
			"customerName"=> (string)($data['customerName']),//"ACTEC PERU S.A.C.",
			"taxAmount"=> (string)($data['taxAmount']),//"6.65",
			"taxableAmount"=> (string)($data['taxableAmount']),//"36.95",
			"taxSubtotal"=> (string)($data['taxSubtotal']),//"6.65",
			"payableAmount"=> (string)($data['payableAmount']),//"108.6"
			);
			
		$cont=1;
								
		foreach($items as $item_prod){
									
				$item[$cont]= array(
					"itemQuantity"=> (string)($item_prod['Quantity']),
					"itemLineExtensionAmount"=> (string)($item_prod['LineExtensionAmount']),
					"itemPricingReference"=> (string)($item_prod['PricingReference']),
					"itemPriceTypeCode"=> (string)($item_prod['PriceTypeCode']),
					"itemTaxTotalTaxAmount"=> (string)($item_prod['TaxTotalTaxAmount']),
					"itemTaxSubtotalTaxableAmount"=> (string)($item_prod['TaxSubtotalTaxableAmount']),
					"itemPriceAmount"=> (string)($item_prod['PriceAmount']),
					"itemTaxSubtotalTaxAmount"=> ($item_prod['TaxSubtotalTaxAmount']),
					"itemTaxPercent"=> (string)($item_prod['TaxPercent']),
					"itemDescription"=> (string)($item_prod['Description']),
					"itemSellersID"=> (string)($item_prod['SellersID']),
					);
									
				$cont = $cont + 1;
		}
								
		//var_dump($item);
								
		$reference = array(
			"referenceID" => (string)($data['referenceID']),
			"DocumentTypeCode" => (string)($data['DocumentTypeCode']),
			"description" => (string)($data['description']),
		);
		
		
		$postData["header"] = $header;
		$postData["item"] = $item;
		$postData["reference"] = $reference;
								
		if ($this->session->userdata('beta') == 0) {
			$urlDoc = "post/xml";
		} else {
			$urlDoc = "beta/post/xml";
		}
								
		$context = stream_context_create(array(
			'http' => array(
			'method' => 'POST',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
			)
		));
								
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

		//return $sale_data;

		} else {
			$sale_data = array(
			'flg_response' => "0",
			'error_code' => "0",
			'response_descrip' => "Error al conectarse con AWS",
			'digest_value' => ""
			);

		//return $sale_data;
		}
		
		
								
		
	}
	
	
	function generar_pdf($sale_id, $inv, $items, $cod_ash)
	{
		
		
		$tipo_notaCredit = $this->credit_note_model->consult_tipe_motive_nc($inv['responseCode']);
		$tipo_nc = $tipo_notaCredit->description_NC;
		
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
		$op_exoReal=lang("op_exo");
		//----------------------

		$tipo_doc = "";
		$impRuc = "";
		$tipo_doc = lang('credit_note_title');

		$numComp = $inv['serieNumero'];

		$symbol = "";
		$currency = "";
		if ($inv['documentCurrencyCode'] == 'PEN') {
            // $symbol = "S/ ";
			$currency = strtoupper(lang("currency_pen"));
		} else if ($inv['documentCurrencyCode'] == 'USD') {
            // $symbol = "$ ";
			$currency = lang("currency_USD");
		}

		$arrD1 = explode(" ", $inv['issueDate']);
		$arrD2 = explode("-", $arrD1[0]);
		$ano = $arrD2[0];
		$dia = $arrD2[2];
		$mes = $arrMes[(int)$arrD2[1] - 1];

		$row_items_html = "";
		foreach ($items as $item) {

			$quantity = $this->tec->formatNumber($item['Quantity']);
			$uprice = $this->tec->formatMoney($item['PricingReference']);
			$subt = $this->tec->formatMoney($item['LineExtensionAmount']);
			
			$nombreProducto=str_replace("<","&lt;",$item['Description']);
			$nombreProducto=str_replace(">","&gt;",$nombreProducto);

			$row_items_html .= '<tr>
                    <td align="center" style="font-size:10px">' . $quantity . '</td>
                    <td align="left" style="font-size:10px">' . $nombreProducto . '</td>
                    <td align="right" style="font-size:10px">' . $uprice . '</td>
                    <td align="right" style="font-size:10px">' . $subt . '</td>
                </tr>';
		}

		$row_items_html .= '
            <tr>
                <td align="center">&nbsp;</td>
                <td align="left"></td>
                <td align="right"></td>
                <td align="right"></td>
            </tr>
		';
		
			$customer_name = $inv['customerName'];
			$nro_doc_ruc =$inv['customerID'];
			$nro_doc_dni = $inv['customerID'];
		
		
			//$typeDoc=(String)($inv['DocumentTypeCode']);
			$serieCorre_comprobante=(String)($inv['referenceID']);
			$sub = $this->tec->formatMoney($inv['payableAmount']);
			$desc = $this->tec->formatMoney(0);
			$afc = $this->tec->formatMoney($inv['taxableAmount']);
			$exo = $this->tec->formatMoney($inv['non_affected']);
			$exoReal = $this->tec->formatMoney($inv['exonerated']);
			$igv = $this->tec->formatMoney($inv['taxSubtotal']);
			$tot1 = $this->tec->formatMoney($inv['payableAmount']);
			//$round = $this->tec->formatMoney($venta->rounding);//$resultado['rounding']);
			$tot2 = $this->tec->formatMoney($inv['payableAmount']);
			$totLetras = strtoupper(numtoletras($this->tec->formatMoney($inv['payableAmount'])) . " " . $currency);
			
			$l_ruc = lang("ccf2")."/".lang("ccf1");
			
			
			
			
			/*$detec=substr((String)$serieCorre_comprobante,0,1);
			$typeDoc=0;
		
			
			if(strcmp($detec,"F")===0){
				$typeDoc=1;
			}else{
				$typeDoc=3;
			}*/
		

		//if ($typeDoc==1) {
            
			$impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" align="left" >
						<tr>
                                <td align="left" width="28%"  style="font-size:10px"> Comprobante: </td>
                                <td align="left" width="72%"  style="font-size:10px"> ' . $serieCorre_comprobante . '</td>
                        </tr>
                        <tr>
                                <td align="left" width="28%"  style="font-size:10px"> Señor(es): </td>
                                <td align="left" width="72%"  style="font-size:10px"> ' . $customer_name . '</td>
                        </tr>
                        <tr>
                                <td align="left" width="28%"  style="font-size:10px"> ' . $l_ruc . ': </td>
                                <td align="left" width="72%"  style="font-size:10px"> ' . $nro_doc_ruc . '</td>
                        </tr>
						<tr>
								<td align="left" width="28%"  style="font-size:10px"> Tipo de moneda: </td>
								<td align="left" width="72%"  style="font-size:10px"> ' . $currency . '</td>
						</tr>
						<tr>
								<td align="left" width="28%"  style="font-size:10px"> Motivo o sustento: </td>
								<td align="left" width="72%"  style="font-size:10px"> ' . $inv['description'] . '</td>
						</tr>

                    </table>';
		/*} else {
			
			if ($typeDoc==3) {

				$doc = $l_dni;
				
				if($nro_doc_dni == null && $nro_doc_ruc != null){
					$doc = $l_ruc;
					$nro_doc = $nro_doc_ruc;
				}

				$impRuc = '<table border="1" width="100%" cellspacing="0" cellpadding="2" align="left">
								<tr>
									<td align="left" width="28%"  style="font-size:10px"> Comprobante: </td>
									<td align="left" width="72%"  style="font-size:10px"> ' . $serieCorre_comprobante . '</td>
								</tr>
								<tr>
									<td align="left" width="28%"  style="font-size:10px"> Señor(es): </td>
									<td align="left" width="72%"  style="font-size:10px"> ' . $customer_name . '</td>
								</tr>

								<tr>
									<td align="left" width="28%"  style="font-size:10px"> ' . $doc . ': </td>
									<td align="left" width="72%"  style="font-size:10px"> ' . $nro_doc_dni . '</td>
								</tr>
								<tr>
									<td align="left" width="28%"  style="font-size:10px"> Tipo de moneda: </td>
									<td align="left" width="72%"  style="font-size:10px"> ' . $currency . '</td>
								</tr>
								<tr>
									<td align="left" width="28%"  style="font-size:10px"> Motivo o sustento: </td>
									<td align="left" width="72%"  style="font-size:10px"> ' . $inv['description'] . '</td>
								</tr>
							</table>';
			}else{
				$impRuc = '';
			}
						
		}*/

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
		$imagen2 = $this->session->userdata('logo_pdf');
		$type_imagen_pdf = $this->session->userdata('type_imagen_pdf');

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
		$hash = $cod_ash;

		if ($typeDoc == 2) {
			$cusDoc = "06";
			$docType = "01";
			$numDoc = $nro_doc_ruc;
			$qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
		} else {
			$cusDoc = "01";
			$docType = "03";
			$numDoc = $nro_doc_dni;
			$qr = $ruc . "|" . $docType . "|" . $serie . "|" . $numero . "|" . $igv . "|" . $tot1 . "|" . $date . "|" . $cusDoc . "|" . $numDoc . "|" . $hash;
		}
		$ex1 = new QRCodeGenerator();
		$qr_img = '<img src="' . $ex1->generate($serieNumero[0].$serieNumero[1],$qr) . '" width="120" height="120">';

		//------------------------------------------------------------------------------//
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
                                <td align="center"  style="font-weight:bold; font-size:11px">$tipo_nc</td>
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
                                    <td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Total Venta</strong></td>
                                    <td align="right" width="40%" style="font-size:10px">$tot2</td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>

                <br>
EOD;
$datoExtraidoDePdf='<tr>
									<td bgcolor="#E4E4E4" align="left" width="60%" style="font-size:10px"><strong>Redondeo</strong></td>
									<td align="right" width="40%" style="font-size:10px">$round</td>
								</tr>';

		
		$pdf->writeHTML($tbl, true, false, false, false, '');
		
		if(!$file = $this->credit_note_model->getFileByNoteCreditID($sale_id)){
			
			$nombPDF = randomstring(20);


			if ($inv["DocumentTypeCode"] == 3){

				///GRABA EN TABLA HISTORIAL
				$rutaTabla = array(
					'credit_note_id' => $sale_id,
					'file_name' => 'boletas/' . $nombPDF . '.pdf',
				);
				//$this->db->insert('sales_files', $rutaTabla);
				$this->credit_note_model->insert_file_credit_note($rutaTabla);
				//GUARDA EL PDF FISICO
				$pdf->Output(FCPATH . 'uploads/pdf/note_credit/boletas/' . $nombPDF . '.pdf', 'F');

				return true;

			} else {

				///GRABA EN TABLA HISTORIAL
				$rutaTabla = array(
					'credit_note_id' => $sale_id,
					'file_name' => 'facturas/' . $nombPDF . '.pdf',
				);
				//$this->db->insert('sales_files', $rutaTabla);
				$this->credit_note_model->insert_file_credit_note($rutaTabla);
				//GUARDA EL PDF FISICO
				$pdf->Output(FCPATH . 'uploads/pdf/note_credit/facturas/' . $nombPDF . '.pdf', 'F');

				return true;
			}
		}else{
			//$this->db->update('sales_files', ['file_name' => $file->file_name], ['sale_id' => $sale_id]);
			$this->credit_note_model->updateFileNoteCredit($file->file_name, $sale_id);

			//GUARDA EL PDF FISICO
			$pdf->Output(FCPATH . 'uploads/pdf/note_credit/' . $file->file_name, 'F');

			return true;
		}

		return false;

	}
	//*******************************TRJ088 - KENY PONTE - 04/09/2019*********************
	function generar_pdf1($sale_id, $inv, $items, $cod_ash){
		// $inv = $this->pos_model->getSaleByID($sale_id);
		$tipo_notaCredit = $this->credit_note_model->consult_tipe_motive_nc($inv['responseCode']);
		$tipo_nc = $tipo_notaCredit->description_NC;
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
		$tipo_doc = lang('credit_note_title');

		$numComp = $inv['serieNumero'];

		$symbol = "";
		$currency = "";

		if ($inv['documentCurrencyCode'] == 'PEN') {
            // $symbol = "S/ ";
			$currency = strtoupper(lang("currency_pen"));
		} else if ($inv['documentCurrencyCode'] == 'USD') {
            // $symbol = "$ ";
			$currency = lang("currency_USD");
		}


		$arrD1 = explode(" ", $inv['issueDate']);
		$arrD2 = explode("-", $arrD1[0]);

		$ano = $arrD2[0];
		$dia = $arrD2[2];
		$mes = $arrMes[(int)$arrD2[1] - 1];
		$fecha_emision=$arrD1[0];//********************************TRJ088 - KENY PONTE 02/09/2019******************* */
		$hora_emision=$inv['issueTime'];//********************************TRJ088 - KENY PONTE 02/09/2019******************* */
		$row_items_html = "";	
		/*******************************TRJ088 - KENY PONTE -************************************************* */
		foreach ($items as $item) {
			$quantity = $this->tec->formatNumber($item['Quantity']);
			$uprice = $this->tec->formatMoney($item['PricingReference']);
			$subt = $this->tec->formatMoney($item['LineExtensionAmount']);
			
			$nombreProducto=str_replace("<","&lt;",$item['Description']);
			$nombreProducto=str_replace(">","&gt;",$nombreProducto);
			$code=$item['itemSellersID'];
			$row=$this->site->getUMProducto($item['itemProductID'],$code);
			$presentation=$row->presentation;
                   $quantity1.=$quantity.'<br><br>';//cantidad mas cantidad
                   $codigo.=$code.'<br><br>';
                   $unidad_medida.=$presentation.'<br><br>';
                    $nombreProducto1.=$nombreProducto.'<br><br>';//nombre del producto
                    $uprice1.=$uprice.'<br><br>';//precio unitario
                    $subt1.=$subt.'<br><br>';//sub total
                   
            }
               $row_items_html=' <tr>
       				 				<td valign="top"align="center" HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$quantity1.'</td>
       				 				<td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$codigo.'</td>
        			 				<td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$unidad_medida.'</td>
        			 				<td valign="top"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$nombreProducto1.'</td>
        			 				<td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$uprice1.'</td>
        			 				<td valign="top"align="center"HEIGHT="200"style="font-size:8px;border: 1px solid #000000">'.$subt1.'</td>
								</tr>';
				/*******************TRJ088 - KENY PONTE -**********************/

		$customer_name = $inv['customerName'];
		$nro_doc_ruc =$inv['customerID'];
		$nro_doc_dni = $inv['customerID'];
		
		
		$serieCorre_comprobante=(String)($inv['referenceID']);
			$sub = $this->tec->formatMoney($inv['payableAmount']);
			$desc = $this->tec->formatMoney(0);
			$afc = $this->tec->formatMoney($inv['taxableAmount']);
			$exo = $this->tec->formatMoney($inv['non_affected']);
			$exoReal = $this->tec->formatMoney($inv['exonerated']);
			$igv = $this->tec->formatMoney($inv['taxSubtotal']);
			$grat=$this->tec->formatMoney(0);
			$tot1 = $this->tec->formatMoney($inv['payableAmount']);
			//$round = $this->tec->formatMoney($venta->rounding);//$resultado['rounding']);
			$tot2 = $this->tec->formatMoney($inv['payableAmount']);
			$totLetras = strtoupper(numtoletras($this->tec->formatMoney($inv['payableAmount'])) . " " . $currency);
			
			$l_ruc = lang("ccf2")."/".lang("ccf1");
			
			

		//if ($inv['document_type'] == 2) {
		
			/**************************TRJ088 - KENY PONTE - *********************************/ 
			$emision="REPRESENTACIÓN IMPRESA DE LA NOTA DE CREDITO ELECTRÓNICA"; 
				$impRuc='<br>
							<table border="1"width="100%" cellspacing="0" cellpadding="2" >
								<tr>
									<td>
										<table>
											<tr>
												<td  style="font-size:8px;"width="20%">señor(es):</td>
												<td  style="font-size:8px;"width="60%">'.$customer_name.'</td>
												<td rowspan="2" valign="top" style="font-size:8px;"width="20%">'.$l_ruc.' : '.$nro_doc_ruc . '</td>
											</tr>
											<tr>
												<td align="left" style="font-size:8px;">COMPROBANTE</td>
												<td  style="font-size:8px;">'.$serieCorre_comprobante.'</td>
											</tr>

										</table>
									</td>
								</tr>
							</table>
							<br><br>
							<table border="1" cellpadding="2">
								<tr>
									<td  style="font-size:8px;"width="40%">MOTIVO O SUSTENTO</td>
									<td rowspan="2"style="font-size:8px;"width="60%"> '.$inv['description'].'</td>
								</tr>
							</table>
						<br>';
						/**********************************TRJ088 - KENY PONTE *************************/
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
$tbl1 = <<<EOD
	<table border="0" width="100%" cellspacing="0" cellpadding="2" >
		<tr>
			<td width="70%" align="center" style="font-size:8px">
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
			<td bgcolor="#1B3752" align="center" width="60%" style="font-size:10px;color: #fefeff"><strong style="font-size:7px;color: #fefeff">TIPO DE NOTA DE CREDITO</strong></td>
		</tr>

		<tr>
			<td style="font-size:8px"  align="center">$fecha_emision</td>
			<td style="font-size:8px"  align="center">$hora_emision</td>
			<td style="font-size:8px"  align="center">$tipo_nc</td>
		</tr>

	</table><br>

	$impRuc
	
	<br>
	
	<table  width="100%" cellspacing="0" >
		<tr height="30">
		<th bgcolor="#1B3752" align="center" width="8%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CANTIDAD</th>
		<th bgcolor="#1B3752" align="center" width="11%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">CODIGO</th>
		<th bgcolor="#1B3752" align="center" width="11%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">UNIDAD MEDIDA</th>
		<th bgcolor="#1B3752" align="center" width="51%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">DESCRIPCION</th>
		<th bgcolor="#1B3752" align="center" width="11%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">PRECIO UNITARIO</th>
		<th bgcolor="#1B3752" align="center" width="8%" style="font-size:7px;color: #fefeff;border: 1px solid #000000">IMPORTE</th>
		</tr>
		$row_items_html
		<tr>
			<td colspan="4" rowspan="8" style="font-size:10px">SON:<br> $totLetras</td>
			<td colspan="1" align="left" style="border: 1px solid #000000;font-size:7px">Sub Total</td>
			<td colspan="1" align="right"  style="font-size:8px;border: 1px solid #000000">$sub</td>
		</tr>
		<tr>
			<td colspan="1" align="left"  style="border: 1px solid #000000;font-size:7px">op.gravadas</td>
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
		
		if(!$file = $this->credit_note_model->getFileByNoteCreditID($sale_id)){
			
			$nombPDF = randomstring(20);


			if ($inv["DocumentTypeCode"] == 3){

				///GRABA EN TABLA HISTORIAL
				$rutaTabla = array(
					'credit_note_id' => $sale_id,
					'file_name' => 'boletas/' . $nombPDF . '.pdf',
				);
				//$this->db->insert('sales_files', $rutaTabla);
				$this->credit_note_model->insert_file_credit_note($rutaTabla);
				//GUARDA EL PDF FISICO
				$pdf->Output(FCPATH . 'uploads/pdf/note_credit/boletas/' . $nombPDF . '.pdf', 'F');

				return true;

			} else {

				///GRABA EN TABLA HISTORIAL
				$rutaTabla = array(
					'credit_note_id' => $sale_id,
					'file_name' => 'facturas/' . $nombPDF . '.pdf',
				);
				//$this->db->insert('sales_files', $rutaTabla);
				$this->credit_note_model->insert_file_credit_note($rutaTabla);
				//GUARDA EL PDF FISICO
				$pdf->Output(FCPATH . 'uploads/pdf/note_credit/facturas/' . $nombPDF . '.pdf', 'F');

				return true;
			}
		}else{
			//$this->db->update('sales_files', ['file_name' => $file->file_name], ['sale_id' => $sale_id]);
			$this->credit_note_model->updateFileNoteCredit($file->file_name, $sale_id);

			//GUARDA EL PDF FISICO
			$pdf->Output(FCPATH . 'uploads/pdf/note_credit/' . $file->file_name, 'F');

			return true;
		}

		return false;

	}
  /*******************************TRJ088 - KENY PONTE 03/09/2019******************* */

	function get_credit_note($estado)
  {
    $this->load->library('datatables');

    $this->datatables->select($this->db->dbprefix('credit_note') . ".id as pid,  date_create, customerName, documentCurrencyCode, serieNumero, description_NC, referenceID, description, if(T2.estadoSend = 1, 'Error de envío', if(T2.estadoSend = 2, 'Enviado', if(T2.estadoSend = 3, 'Rechazado', if(T2.estadoSend = 4, 'Procesado', '')))) ", false); 
	$this->datatables->from('credit_note');
	$this->datatables->join('credit_note_motive', 'credit_note_motive.id=credit_note.responseCode', 'left');
	$this->datatables->join('(SELECT note_credit_id, status as estadoSend FROM tec_send_invoice_nc WHERE estado=1) as T2', 'T2.note_credit_id=credit_note.id', 'left');
    
	/*$this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='#' onClick=\"MyWindow=window.open('" . site_url('pos/view/$1/1') . "', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=350,height=600'); return false;\" title='" . lang("view_invoice") . "' class='tip btn btn-primary btn-xs'><i class='fa fa-list'></i></a>
          <a href='" . site_url('sales/recreate_pdf/$1') . "' title='" . lang("recreate_pdf") . "' class='tip btn btn-danger btn-xs' ><i class='fa fa-file-pdf-o'></i></a>
		  <a href='" . site_url('sales/reenvioSunatXml/$1') . "' onClick=\"return confirm('" . lang('forwarding_x_sale') . "')\" title='" . lang("forwarding_sale") . "' class='tip btn btn-warning btn-xs' ><i class='fa fa-paper-plane-o'></i></a>
		  <a href='" . site_url('sales/reArmarXml/$1') . "' onClick=\"return confirm('" . lang('recreate_xml_Q') . "')\" title='" . lang("recreate_xml") . "' class='tip btn btn-success btn-xs' ><i class='fa fa-file-code-o'></i></a>
          </div></div>", "pid");*/
	$this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'>".
		  //"<a href='" . site_url('sales/reenvioSunatXml/$1') . "' onClick=\"return confirm('" . lang('forwarding_x_credit_note') . "')\" title='" . lang("email_XML_PDF") . "' class='tip btn btn-warning btn-xs' ><i class='fa fa-envelope-o'></i></a>".
		  "<a href='#' id='email_$1' onclick='sendEmail()' title='" . lang("email_XML_PDF") . "' class='send-mail tip btn btn-primary btn-xs' ><i class='fa fa-envelope-o'></i></a>".
          "<a href='" . site_url('Credit_note/recreate_pdf/$1') . "' title='" . lang("recreate_pdf") . "' class='tip btn btn-danger btn-xs' ><i class='fa fa-file-pdf-o'></i></a>".
		  "<a href='" . site_url('Credit_note/reenviarXML/$1') . "' onClick=\"return confirm('" . lang('forwarding_x_sale') . "')\" title='" . lang("forwarding_NC") . "' class='tip btn btn-warning btn-xs' ><i class='fa fa-paper-plane-o'></i></a>
          </div></div>", "pid");
	
	
    $this->datatables->unset_column('pid');
    echo $this->datatables->generate();
  }

  function recreate_pdf($id = null)
  {

    $note_credit_header = (array)$this->credit_note_model->consult_credit_note($id);
	
	foreach($note_credit_header as $nc){
	
	$noteCredit= array(
		'customerID'=>$nc['customerID'],
		'sale_id'=>$nc['sale_id'],
		'responseCode'=>$nc['responseCode'],
		'invoiceTypeCode'=>$nc['invoiceTypeCode'],
		'serieNumero'=>$nc['serieNumero'],
		'issueDate'=>$nc['issueDate'],
		'issueTime'=>$nc['issueTime'],
		'documentCurrencyCode'=>$nc['documentCurrencyCode'],
		'supplierDocumentID'=>$nc['supplierDocumentID'],
		'customerDocumentID'=>$nc['customerDocumentID'],
		'customerName'=>$nc['customerName'],
		'taxAmount'=>$nc['taxAmount'],
		'taxableAmount'=>$nc['taxableAmount'],
		'taxSubtotal'=>$nc['taxSubtotal'],
		'payableAmount'=>$nc['payableAmount'],
		'user_create'=>$nc['user_create'],
		'date_create'=>$nc['date_create'],
		'referenceID'=> $nc['referenceID'],
		'DocumentTypeCode' => $nc['DocumentTypeCode'],
		'description' => $nc['description'],
		'non_affected'=> $nc['non_affected'],
		'exonerated'=> $nc['exonerated']);
	
	}
	//**************************TRJ088 - KENY PONTE - 04/09/2019 *************
	$p=$this->site->getPDFformat();
	$pdf=$p->pdf_format;
	//****************************TRJ088 - KENY PONTE - 04/09/2019 ************
    if ($noteCredit['customerID'] != null) {


        $note_credit_items = $this->credit_note_model->consult_credit_note_items($id);
		//$venta = $this->credit_note_model->consult_sales($noteCredit['sale_id']);
		
			require("funciones.php");
			include("app/class/QRCodeGenerator.class.php");
			if($pdf==1){				
				if ($this->generar_pdf($id, $noteCredit, $note_credit_items, $note_credit_header['digest_value'])) {

			  		$this->session->set_flashdata('message', lang("pdf_recreated"));
			 		 redirect('credit_note');
				}
				}else if ($pdf==2) {
					if ($this->generar_pdf1($id, $noteCredit, $note_credit_items, $note_credit_header['digest_value'])) {

			  		$this->session->set_flashdata('message', lang("pdf_recreated"));
			 		 redirect('credit_note');
				}
			}
     
    } else {
      $this->session->set_flashdata('error', lang("not_saled"));
      redirect('credit_note');
    }

    $this->session->set_flashdata('error', lang("something_wrong"));
    redirect('credit_note');
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
		
		$id=substr($sale_id,6);
		
		//$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

		//$this->data['message'] = $this->session->flashdata('message');

		$inv = $this->credit_note_model->getNoteCreditID($id);
		$file = $this->credit_note_model->getFileNameByNoteCreditId($id);
		
		//$this->tec->view_rights($inv->user_create);

		$this->load->helper('text');

		//$this->data['rows'] = $this->pos_model->getAllSaleItems($sale_id);

		//$this->data['customer'] = $inv->customerID;

		//$this->data['inv'] = $inv;

		//$this->data['sid'] = $id;

		//$this->data['noprint'] = null;

		//$this->data['modal'] = false;

		//$this->data['payments'] = $this->pos_model->getAllSalePayments($sale_id);

		//$this->data['created_by'] = $this->site->getUser($inv->user_create);
		
		$path = base_url() . 'uploads/pdf/note_credit/';
		$file_name = $file->file_name;

		// $receipt = $this->load->view($this->theme.'pos/view', $this->data, TRUE);
		$arrD1 = explode(" ", $inv->date_create);
		
			$descripTipo="Nota de credito";
			$tipo = "07";
		
		$direccion="uploads/xml/";
		
		if (!file_exists($direccion)){
			mkdir($direccion);
		}
		
		$ubicacionArchivo=$direccion.$this->session->userdata('ruc')."-".$tipo."-".$inv->serieNumero.".xml";
		
		if (file_exists($ubicacionArchivo)){
		}else{
			
			$itemArray = array(
				"ruc" => $this->session->userdata('ruc'),
				"tipoComprobante" => $tipo,
				"Correlativo" => $inv->serieNumero
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
		
			
			$archivo = fopen($ubicacionArchivo, "w");
			fwrite($archivo,$datosConvertidos);
			fclose($archivo);
			
		   }
		
		$tipoModena="";

		if($inv->documentCurrencyCode=="PEN"){
			$tipoModena="S/";
		}else{
			$tipoModena="$";
		}
		
		/*if(($this->data['customer']->cf1)==""){
			$documentoIdentid=$this->data['customer']->cf2;
		}else{
			$documentoIdentid=$this->data['customer']->cf1;
		}*/
		
		$documentoIdentid=$inv->customerID;
		
		$receipt = "<html>Estimado Cliente: <br><br>Por la presente les comunicamos que la empresa " . $this->session->userdata('razon_social') . ", emisora de comprobantes electronicos le ha emitido el siguiente comprobante:<br><br><br>Tipo de documento: " . $descripTipo . "<br><br>Serie y numero: " . $inv->serieNumero . "<br><br>Nro RUC/DNI/CE del cliente: ".$documentoIdentid."<br><br>Razon social cliente: ".$inv->customerName."<br><br>Fecha de emision: " . $arrD1[0] . "<br><br>Importe Total: ".$tipoModena." " . $inv->payableAmount . "<br><br><br>Atentamente,<br><br>" . $this->session->userdata('razon_social') . "<br><br><br>Nota:<br>*En este correo electronico hemos eliminado las tildes para evitar errores en su visualizacion.<br></html>";
		
		$subject = $descripTipo . " Electronica " . $inv->serieNumero ." ".$this->session->userdata('razon_social') ;

		
		if ($this->tec->send_email($to, $subject, $receipt, null, null, $path . $file_name, null, null, $inv->serieNumero . '.pdf', $ubicacionArchivo, $inv->serieNumero . '.xml' )) {//***********TRJ007 - ALEXADER ROCA - 20/03/2019************
		
			echo json_encode(array('msg' => lang("email_success")));

		} else {

			echo json_encode(array('msg' => lang("email_failed")));

		}
		

	}


	function mostrarItemsTB(){
		
		$correclativo=$this->input->get('correlativoInvoice');
		$serie=$this->input->get('serieInvoice');
		$serie=substr($serie, 1);
		
		$resto=($correclativo%1000000);
		if($resto == $correclativo){
			
			$resto=($correclativo%100000);
			if($resto == $correclativo){
				
				$resto=($correclativo%10000);
				if($resto == $correclativo){

					$resto=($correclativo%1000);
					if($resto == $correclativo){
	
						$resto=($correclativo%100);
						if($resto == $correclativo){
							
							$resto=($correclativo%10);
							if($resto == $correclativo){
								$invoice_comprobante=$serie."-000000".$correclativo;
							}else{
								$invoice_comprobante=$serie."-00000".$correclativo;
							}
							
											}else{
							$invoice_comprobante=$serie."-0000".$correclativo;
						}
						
					}else{
						$invoice_comprobante=$serie."-000".$correclativo;
					}

				}else{
					$invoice_comprobante=$serie."-00".$correclativo;
				}
				
			}else{
				$invoice_comprobante=$serie."-0".$correclativo;
			}
			
		}else{
			$invoice_comprobante=$serie."-".$correclativo;
		}
		
		$resultado=$this->credit_note_model->valid_invoice($invoice_comprobante);
		
		if($resultado==false){
			$cont =0;
			$items[$cont]= array(
									"id"=> (string)(0),
									"code"=> (string)(0),
									"cantidad"=> (string)(0),
									"precio"=> (string)(0),
									"subtotal"=> (string)(0),
									"impuesto"=> (string)(0),
									"name"=> (string)(0)
										);
			echo json_encode($items);
			
		}else{
			
			foreach($resultado as $result){
				$id_sale=$result->id;
			}
			
			$result_items = $this->credit_note_model->valid_sale_items($id_sale);
			
			$cont =0;
				foreach($result_items as $result_i){
					$items[$cont]= array(
									"id"=> (string)($result_i->id),
									"code"=> (string)($result_i->code),
									"cantidad"=> (string)($result_i->quantity),
									"precio"=> (string)($result_i->unit_price),
									"subtotal"=> (string)($result_i->subtotal),
									"impuesto"=> (string)($result_i->tax_method),
									"name"=> (string)($result_i->name)
										);
					$cont = $cont + 1;
				}
				echo json_encode($items);
			
			
		}
		
	}

	function reenviarXML($id = null){
		
		$data=array("id"=>$id);
		
		$biz= array(
			"supplierID"=> (string)($this->session->userdata('ruc')),
			"passFirma"=> (string)($this->session->userdata('pass_firma')),
			"supplierName"=> (string)($this->session->userdata('razon_social')),
			"beta"=> (string)$this->session->userdata('beta')
		);
								
		$data_envio = array(
			"ruc"=> (string)($this->session->userdata('ruc')), //"00000000000",
			"usuSol" => $this->session->userdata('user_sol'),//"MODDATOS",
			"passSol" => $this->session->userdata('pass_sol')//"moddatos"
		);
								
								
		$postData["header"] = $data;
		$postData["information_additional"] = $biz;
		$postData["info_envio"] = $data_envio;
		
		
		$context = stream_context_create(array(
					'http' => array(
					'method' => 'POST',
					'header' => "Authorization: application/json\r\n" .
					"Content-Type: application/json\r\n",
					'content' => json_encode($postData)
					)
					));
								
		$urlDoc = "reenvio_note_credit";//"post/doc/xml";
		$url_NC = $this->config->item('wsnc_url');
		$response = file_get_contents($url_NC . $urlDoc, false, $context);
		$res = json_decode($response, true);
		
		$this->session->set_flashdata($res[1], $res[0]." - ".$res[2]);
		redirect('credit_note');
		
	}
	
}