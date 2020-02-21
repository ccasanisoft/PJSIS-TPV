<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->lang->load('auth', $this->Settings->language);
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->load->model('auth_model');
        $this->load->library('ion_auth');
        $this->load->helper('basic_helper');
    }

    function index() {
        if (!$this->loggedIn) {
            redirect('login');
        } elseif($this->Admin) {
            redirect('admin');
        } else {
            redirect('index');
        }
    }

    function users() {
        if (!$this->loggedIn) {
            redirect('login');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->site->getAllUsers();
        $bc = array(array('link' => '#', 'page' => lang('users')));
        $meta = array('page_title' => lang('users'), 'bc' => $bc);
        $this->data['page_title'] = lang('users');
        $this->page_construct('auth/index', $this->data, $meta);
    }

    function profile($id = NULL) {
        if (!$this->ion_auth->logged_in() || !$this->Admin && $id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$id || empty($id)) {
            redirect('auth');
        }

        $this->data['title'] = lang('profile');

        $user = $this->ion_auth->user($id)->row();
        $groups = $this->ion_auth->groups()->result_array(); //var_dump($groups); exit;
        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->data['user'] = $user;
        $this->data['groups'] = $groups;

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['password'] = array(
            'name' => 'password',
            'id' => 'password',
            'class' => 'form-control',
            'type' => 'password',
            'value' => ''
            );
        $this->data['password_confirm'] = array(
            'name' => 'password_confirm',
            'id' => 'password_confirm',
            'class' => 'form-control',
            'type' => 'password',
            'value' => ''
            );
        $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
        $this->data['old_password'] = array(
            'name' => 'old',
            'id' => 'old',
            'class' => 'form-control',
            'type' => 'password',
            );
        $this->data['new_password'] = array(
            'name' => 'new',
            'id' => 'new',
            'type' => 'password',
            'class' => 'form-control',
            'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
            );
        $this->data['new_password_confirm'] = array(
            'name' => 'new_confirm',
            'id' => 'new_confirm',
            'type' => 'password',
            'class' => 'form-control',
            'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
            );
        $this->data['user_id'] = array(
            'name' => 'user_id',
            'id' => 'user_id',
            'type' => 'hidden',
            'value' => $user->id,
            );

        $this->data['id'] = $id;

        $this->data['page_title'] = lang('profile');
        $bc = array(array('link' => site_url('users'), 'page' => lang('users')), array('link' => '#', 'page' => lang('profile')));
        $meta = array('page_title' => lang('profile'), 'bc' => $bc);
        $this->page_construct('auth/profile', $this->data, $meta);
    }

    public function captcha_check($cap) {
        $expiration = time() - 300; // 5 minutes limit
        $this->db->query("DELETE FROM captcha WHERE captcha_time < " . $expiration);

        $sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
        $binds = array($cap, $this->input->ip_address(), $expiration);
        $query = $this->db->query($sql, $binds);
        $row = $query->row();
        if ($row->count == 0) {
            $this->form_validation->set_message('captcha_check', lang('captcha_wrong'));
            return FALSE;
        } else {
            return TRUE;
        }
    }
	//Renato jeje :3
	function API($ruta){
		//$url = "http://wservicios.actecperu.com/upservices/";
		$url = "http://localhost:8000/WS_renato/upservices/";
		$respuesta = $url . $ruta;
		return $respuesta;		
		}
	//Renato jeje :3	

	
		
    function login($m = NULL) {
		
		if($this->Settings->captcha) {
            $this->form_validation->set_rules('captcha', lang('captcha'), 'required|callback_captcha_check');
        }

        if ($this->form_validation->run() == true) {

            $remember = (bool) $this->input->post('remember');
            $local = $this->input->post('local');

            $this->session->set_userdata(['local' => $local]);
			$this->session->set_userdata(['listaVenta' => 0]);//************************TRJ014 - ALEXANDER ROCA - 03/04/2019****************

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
                if ($this->Settings->mmode) {
                    if (!$this->ion_auth->in_group('admin')) {
                        $this->session->set_flashdata('error', lang('site_is_offline_plz_try_later'));
                        redirect('auth/logout');
                    }
                }

                if($this->session->userdata("group_id")==1){
                   $referrer = $this->session->userdata('requested_page') ? $this->session->userdata('requested_page') : '/'; 
               }else{
               $referrer = $this->session->userdata('requested_page') ? $this->session->userdata('requested_page') : 'pos';
               }
               


				
				//****************TRJ046 - ALEXANDER ROCA - 14/05/2019*******************
				$data_seting=$this->site->getSettings();
				
                $customer_data = array(
                                    'razon_social' => "prueba",//$nombre,
                                    'ruc' => "12345678912",//$ruc,
                                    'direccion' => "Jr. Montevideo No. 801 Int. 31 CC. Agua Marina",//$direc,
                                    'region' => "lima",//$region_ws,//"Lima",
                                    'pais' => "peru",//$pais,
                                    'negocio' => 0,//$rubro,     //0:General, 1:Farmacia
                                    'beta' => $data_seting->beta,//1,        //0:No Beta, 1:Beta
                                    'habilita_btn_pago' => $data_seting->habilita_btn_pago,//1,
                                    'habilita_btn_caja' => $data_seting->habilita_btn_caja,//0,
                                    'logo' => $data_seting->logo_auth,//"logo-empresa.jpg",
                                    'pos_logo' => $data_seting->pos_logo,//2,     //1:Vertical 2:Horizontal
									//****************TRJ030 - RENATO GAMONAL*******************
									'logo_pdf' => $data_seting->logo_pdf,//"logo-pdf.png",
									'type_imagen_pdf' => $data_seting->type_imagen_pdf,//2
									//****************TRJ030 - RENATO GAMONAL*******************
									//****************TRJ067 - ALEXANDER ROCA - 18/07/2019*******************
									'protocol' => $data_seting->protocol,
									'smtp_host' => $data_seting->smtp_host,
									'smtp_user' => $data_seting->smtp_user,
									'smtp_pass' => $data_seting->smtp_pass,
									'smtp_port' => $data_seting->smtp_port,
									'mailpath' => $data_seting->mailpath
									//****************TRJ067 - ALEXANDER ROCA - 18/07/2019*******************
                                );

                switch($customer_data['beta']){
                    case 0:
                         $customer_data['pass_firma'] ="VtR6Jx3MTexhSeiC";
                         $customer_data['user_sol'] = "FANOKA28";
                         $customer_data['pass_sol'] ="Carpio2019";
                        break;
                    case 1:
                        $customer_data['pass_firma'] = "123456";
                        $customer_data['user_sol'] = "MODDATOS";
                        $customer_data['pass_sol'] = "moddatos";
                        break;
                }
				//****************TRJ046 - ALEXANDER ROCA - 14/05/2019*******************
                // die(json_encode($customer_data));

                $this->session->set_userdata($customer_data);

                if($this->site->getExchange(date("Y-m-d")) == FALSE){
                    $this->addExchange();
                }
				
				
				//****************TRJ025 - ALEXANDER ROCA - 13/05/2019*******************
				if(strlen($customer_data['razon_social']) > 0){
					if(strlen($customer_data['ruc']) > 0){
						if(strlen($customer_data['direccion']) > 0){
							if(strlen($customer_data['pais']) > 0){
								if(strlen($customer_data['negocio']) > 0){
									if(strlen($customer_data['pass_firma']) > 0){
										if(strlen($customer_data['user_sol']) > 0){
											if(strlen($customer_data['pass_sol']) > 0){
												$this->session->set_flashdata('message', $this->ion_auth->messages());
												redirect($referrer);
											}else{
												$this->session->set_flashdata('error', lang('login_consult_data'));
												redirect('login');
											}
										}else{
											$this->session->set_flashdata('error', lang('login_consult_data'));
											redirect('login');
										}
									}else{
										$this->session->set_flashdata('error', lang('login_consult_data'));
										redirect('login');
									}
								}else{
									$this->session->set_flashdata('error', lang('login_consult_data'));
									redirect('login');
								}
							}else{
								$this->session->set_flashdata('error', lang('login_consult_data'));
								redirect('login');
							}
						}else{
							$this->session->set_flashdata('error', lang('login_consult_data'));
							redirect('login');
						}
					}else{
						$this->session->set_flashdata('error', lang('login_consult_data'));
						redirect('login');
					}
					
				}else{
					$this->session->set_flashdata('error', lang('login_consult_data'));
					redirect('login');
				}
			//****************TRJ025 - ALEXANDER ROCA - 13/05/2019*******************
                
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect('login');
            }
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['message'] = $this->session->flashdata('message');
            if($this->Settings->captcha) {
                $this->load->helper('captcha');
                $vals = array(
                    'img_path' => './assets/captcha/',
                    'img_url' => site_url() . 'assets/captcha/',
                    'img_width' => 150,
                    'img_height' => 34,
                    );
                $cap = create_captcha($vals);
                $capdata = array(
                    'captcha_time' => $cap['time'],
                    'ip_address' => $this->input->ip_address(),
                    'word' => $cap['word']
                    );

                $query = $this->db->insert_string('captcha', $capdata);
                $this->db->query($query);
                $this->data['image'] = $cap['image'];
                $this->data['captcha'] = array('name' => 'captcha',
                    'id' => 'captcha',
                    'type' => 'text',
                    'class' => 'form-control',
                    'required' => 'required',
                    'placeholder' => lang('type_captcha')
                    );
            }

            $this->data['page_title'] = lang('login');
            $this->data['locals'] = $this->site->getAllLocals();

            $this->load->view($this->theme.'auth/login', $this->data);
        }
    }

    function addExchange(){
		//*********************TRJ013 - Alexander roca - 11/06/2019********************
		try{
		
			$file = fopen("http://e-consulta.sunat.gob.pe/cl-at-ittipcam/tcS01Alias","r");

			$n=0;
			while(!feof($file))  //captura de encabezados
			{
				$fila = fgets($file);  //captura de linea
				$sent[$n] = $fila;
				$n++;
			}
			fclose($file);

			// $Fecha = trim(strip_tags($sent[56]));
			// $monthYear = explode(" - ", $Fecha) ;
			// $month = $monthYear[0];
			// $year = $monthYear[1];

			// $numMes = 0;
			// $months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre");

			// $num = 1;
			// foreach($months as $mes){

			//     if($mes == $month){
			//         $numMes = $num;
			//     }
			//     $num = $num + 1;
			// }

			$m = 95; $f=0; $fila = 3;
			while($sent[$m] < 32 & $sent[$m+8] > 0)
			{
				$Dias[trim(strip_tags($sent[$m]))] = array(
					"dia" => (int)trim(strip_tags($sent[$m])),
					"compra" => (double)trim(strip_tags($sent[$m+4])),
					"venta" => (double)trim(strip_tags($sent[$m+8]))
					// "fila" => $m
				);

				if($f==$fila)
				{
					$m=$m+4;
					$fila=$fila+4;
				}
				$m=$m+14;
				$f++;
			}

			$mayor = 0;
			$buy = 0.000;
			$sell = 0.000;

			foreach($Dias as $dia)
			{
				if(empty($nro))
				{
					$nro = $dia["dia"];
					$mayor = $nro;
					$buy = $dia["compra"];
					$sell = $dia["venta"];
				}
				else
				{
					if($dia["dia"] > $mayor)
					{
						$mayor = $dia["dia"];
						$buy = $dia["compra"];
						$sell = $dia["venta"];
					}
				}
			}

			// $date = date_create($year . "-" . $numMes . "-" . $mayor);
			// date_format($date, 'Y-m-d')

			$data = array(
				'date' => date("Y-m-d"),
				'currency' => "USD",
				// 'coin_from' => "USD",
				// 'coin_to'=> "PEN",
				// 'exchange' => $cambio,
				'buy' => $buy,
				'sell' => $sell,
				'uCrea' => $this->session->userdata('user_id'),
				'fCrea' => date("Y-m-d H:i:s"),
				'estado' => 1
			);

			$this->auth_model->addExchange($data);
			
			return 0;
			
		//}catch(Exception $e){
		}catch(Services_Soundcloud_Invalid_Http_Response_Code_Exception $e){
			return 1;
		}finally{
			return 2;
		}
		//*********************TRJ013 - Alexander roca - 11/06/2019********************
    }

    function reload_captcha(){
        $this->load->helper('captcha');
        $vals = array(
            'img_path' => './assets/captcha/',
            'img_url' => site_url() . 'assets/captcha/',
            'img_width' => 150,
            'img_height' => 34,
            );
        $cap = create_captcha($vals);
        $capdata = array(
            'captcha_time' => $cap['time'],
            'ip_address' => $this->input->ip_address(),
            'word' => $cap['word']
            );
        $query = $this->db->insert_string('captcha', $capdata);
        $this->db->query($query);

        echo $cap['image'];
    }

    function logout($m = NULL) {

        $logout = $this->ion_auth->logout();
        $this->session->set_flashdata('message', $this->ion_auth->messages());

        redirect('login/'.$m);
    }

    function change_password() {
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
        $this->form_validation->set_rules('old_password', lang('old_password'), 'required');
        $this->form_validation->set_rules('new_password', lang('new_password'), 'required|max_length[25]');
        $this->form_validation->set_rules('new_password_confirm', lang('confirm_password'), 'required|matches[new_password]');

        $user = $this->ion_auth->user()->row();

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('auth/profile/'.$user->id.'/#cpassword');
        } else {
            if(DEMO) {
                $this->session->set_flashdata('error', lang('disabled_in_demo'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
            }

            if(PROTECT_USER) {
                $this->session->set_flashdata('error', lang('disabled_in_demo'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
            }

            $identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

            $change = $this->ion_auth->change_password($identity, $this->input->post('old_password'), $this->input->post('new_password'));

            if ($change) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->logout();
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect('auth/profile/'.$user->id.'/#cpassword');
            }
        }
    }

    function forgot_password() {
        $this->form_validation->set_rules('forgot_email', lang('email_address'), 'required|valid_email');

        if ($this->form_validation->run() == false) {
            $error = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $error);
            redirect("login#forgot_password");
        } else {

            $identity = $this->ion_auth->where('email', strtolower($this->input->post('forgot_email')))->users()->row();
            if (empty($identity)) {
                $this->ion_auth->set_message('forgot_password_email_not_found');
                $this->session->set_flashdata('error', $this->ion_auth->messages());
                redirect("login#forgot_password");
            }

            $forgotten = $this->ion_auth->forgotten_password($identity->email);

            if ($forgotten) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("login#forgot_password");
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect("login#forgot_password");
            }
        }
    }

    public function reset_password($code = NULL) {
        if (!$code) {
            show_404();
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user) {

            $this->form_validation->set_rules('new', lang('password'), 'required|min_length[8]|max_length[25]|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', lang('confirm_password'), 'required');

            if ($this->form_validation->run() == false) {

                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->data['message'] = $this->session->flashdata('message');
                $this->data['title'] = lang('reset_password');
                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password'] = array(
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password',
                    'class' => 'form-control',
                    'pattern' => '^.{8}.*$',
                    );
                $this->data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password',
                    'class' => 'form-control',
                    'pattern' => '^.{8}.*$',
                    );
                $this->data['user_id'] = array(
                    'name' => 'user_id',
                    'id' => 'user_id',
                    'type' => 'hidden',
                    'value' => $user->id,
                    );
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;
                $this->data['identity_label'] = $user->email;
                $this->data['page_title'] = lang('reset_password');
                $this->load->view($this->theme.'auth/reset_password', $this->data);
            } else {
                if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {

                    $this->ion_auth->clear_forgotten_password_code($code);
                    show_error(lang('error_csrf'));

                } else {
                    $identity = $user->email;

                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

                    if ($change) {
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        redirect('login');
                    } else {
                        $this->session->set_flashdata('error', $this->ion_auth->errors());
                        redirect('auth/reset_password/' . $code);
                    }
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->ion_auth->errors());
            redirect("login#forgot_password");
        }
    }

    function activate($id, $code = false) {

        if ($code !== false) {
            $activation = $this->ion_auth->activate($id, $code);
        } else if ($this->Admin) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            if ($this->Admin) {
              redirect($_SERVER["HTTP_REFERER"]);
          } else {
              redirect("auth/login");
          }
      } else {
        $this->session->set_flashdata('error', $this->ion_auth->errors());
        redirect("forgot_password");
    }
}

function deactivate($id = NULL) {
    if(!$this->Admin) {
        $this->session->set_flashdata('warning', lang("access_denied"));
        redirect($_SERVER["HTTP_REFERER"]);
    }
    $id = $this->config->item('use_mongodb', 'ion_auth') ? (string) $id : (int) $id;
    $this->form_validation->set_rules('confirm', lang("confirm"), 'required');

    if ($this->form_validation->run() == FALSE) {
        if($this->input->post('deactivate')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'auth/deactivate_user', $this->data);
        }
    } else {

        if ($this->input->post('confirm') == 'yes') {
            if ($id != $this->input->post('id')) {
                show_error(lang('error_csrf'));
            }

            if ($this->ion_auth->logged_in() && $this->Admin) {
                $this->ion_auth->deactivate($id);
                $this->session->set_flashdata('message', $this->ion_auth->messages());
            }
        }

        redirect($_SERVER["HTTP_REFERER"]);
    }
}

function create_user() {
    $this->load->helper('email');



    if (!$this->Admin) {
        $this->session->set_flashdata('warning', lang("access_denied"));
        redirect($_SERVER["HTTP_REFERER"]);
    }

    $this->data['title'] = lang('add_user');
    $this->form_validation->set_rules('username', lang("username"), 'trim|is_unique[users.username]|alpha_dash');
    //$this->form_validation->set_rules('email', lang("email"), 'trim|is_unique[users.email]');
    $this->form_validation->set_rules('email', lang('email'), 'trim|required|valid_email');

    $this->form_validation->set_rules('password', lang('password', 'password'), 'required|min_length[5]|max_length[20]');
    $this->form_validation->set_rules('confirm_password', lang('confirm_password', 'confirm_password'), 'required|min_length[5]|max_length[20]|matches[password]');

    if ($this->form_validation->run() == true) {

        $username = strtolower($this->input->post('username'));
        $email = strtolower($this->input->post('email'));
        $password = $this->input->post('password');
        // $notify = $this->input->post('notify');

        $additional_data = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'phone' => $this->input->post('phone'),
            'gender' => $this->input->post('gender'),
            'group_id' => $this->input->post('group') ? $this->input->post('group') : '2',
            );
        $active = $this->input->post('status');

    }
    if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $active)) { //, $notify

        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect("auth/users");
    } else {

        $this->data['error'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('error')));
        $this->data['groups'] = $this->ion_auth->groups()->result_array();
        $this->data['page_title'] = lang('add_user');
        $bc = array(array('link' => site_url('users'), 'page' => lang('users')), array('link' => '#', 'page' => lang('add_user')));
        $meta = array('page_title' => lang('add_user'), 'bc' => $bc);
        $this->page_construct('auth/create_user', $this->data, $meta);
    }
}

function edit_user($id = NULL) {
    if ($this->input->post('id')) {
        $id = $this->input->post('id');
    }
    $this->data['title'] = lang("edit_user");

    if (!$this->loggedIn || !$this->Admin && $id != $this->session->userdata('user_id')) {
        $this->session->set_flashdata('warning', lang("access_denied"));
        redirect($_SERVER["HTTP_REFERER"]);
    }

    $user = $this->ion_auth->user($id)->row();

    if($user->username != $this->input->post('username')) {
        $this->form_validation->set_rules('username', lang("username"), 'trim|is_unique[users.username]');
    }
    if($user->email != $this->input->post('email')) {
        //$this->form_validation->set_rules('email', lang("email"), 'trim|is_unique[users.email]');
        $this->form_validation->set_rules('email', lang('email'), 'trim|required|valid_email');
    }

    if ($this->form_validation->run() === TRUE) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

        if(PROTECT_USER) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

        if ($this->Admin) {
            if($id == $this->session->userdata('user_id')) {
                $data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'phone' => $this->input->post('phone'),
                    'gender' => $this->input->post('gender'),
                    );
            } else {
                $data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'username' => $this->input->post('username'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'gender' => $this->input->post('gender'),
                    'active' => $this->input->post('status'),
                    'group_id' => $this->input->post('group'),
                    );
            }
        } else {
            $data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone' => $this->input->post('phone'),
                'gender' => $this->input->post('gender'),
                );
        }

        if ($this->Admin) {
            if ($this->input->post('password')) {
                $this->form_validation->set_rules('password', lang('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
                $this->form_validation->set_rules('password_confirm', lang('edit_user_validation_password_confirm_label'), 'required');

                $data['password'] = $this->input->post('password');
            }
        }
            //$this->sma->print_arrays($data);

    }
    if ($this->form_validation->run() === TRUE && $this->ion_auth->update($user->id, $data)) {
        $this->session->set_flashdata('message', lang('user_updated'));
        redirect("auth/profile/" . $id);
    }
    else {
        $this->session->set_flashdata('error', validation_errors());
        redirect($_SERVER["HTTP_REFERER"]);
    }
}


function _get_csrf_nonce() {
    $this->load->helper('string');
    $key = random_string('alnum', 8);
    $value = random_string('alnum', 20);
    $this->session->set_flashdata('csrfkey', $key);
    $this->session->set_flashdata('csrfvalue', $value);

    return array($key => $value);
}

function _valid_csrf_nonce() {
    if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
    $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function _render_page($view, $data = null, $render = false) {

    $this->viewdata = (empty($data)) ? $this->data : $data;
    $view_html = $this->load->view('header', $this->viewdata, $render);
    $view_html .= $this->load->view($view, $this->viewdata, $render);
    $view_html = $this->load->view('footer', $this->viewdata, $render);

    if (!$render)
        return $view_html;
}

    function delete($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

        if(PROTECT_USER) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

        if (!$this->Admin || $id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->auth_model->delete_user($id)) {
            $this->session->set_flashdata('message', lang('user_deleted'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

}
