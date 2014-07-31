<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Main extends CI_Controller {


	function __construct()
	{
		
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Userconfig_model', 'userconfig');
		$this->load->model('Messages_model', 'msg_model');	
		$this->load->helper('url');
		$this->load->database();
		$this->baseurl = $this->config->config['base_url'];		
		$this->page_now = "main";
	}
	
	public function index()
	{
			include("includes/custom_settings.php");	

		$ip_address 	= $this->input->ip_address();
		$idnumber 		= $this->input->post('idnumber');
		$password 		= $this->input->post('password');
		$p 				= $this->input->post('p');

		$sess_msg 		= $this->session->flashdata('msg');
		$sess_msg_type 	= $this->session->flashdata('msg_type');
		if($sess_msg){
			$msg = $sess_msg;
			$msg_type = $sess_msg_type;
		}


		$allowed_start_ip = array('127','192','172','10');						
		$exp_ip = explode(".",$ip_address);
		/* check gallery*/

		$sql = "select gall_head,gall_text,pic_path from gallery where gall_status != '2' order by gall_order asc, gall_head asc";

		$check_gall = $this->db->query($sql);
		if($check_gall->num_rows() > 0){
			$show_gall = array();
			$count = 0;
			foreach($check_gall->result_array() as $cg){
				
				$cg['count'] = $count;
				$show_gall[] = $cg;
				$count++;
				
			}
			$o['show_gall'] = $show_gall;
			$o['show_slideshow_table'] = true;
		}

		if($p == "login")
		{
			$login_type = 0;

			$adServer = "ldap://rmci.net";
		    $ldap = ldap_connect($adServer);
		    $username = $idnumber;
		    $password = $password;

    		$ldaprdn = "RMCI" . "\\" . $username;

	   		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
	   		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

    			$bind = @ldap_bind($ldap, $ldaprdn, $password);

	    		if($bind) 
	    		{

					if(array_key_exists($username, $ldap_users)){
						$idnumber =  $ldap_users[$username];
					}
		        	$login_direct = true;
		        	$ldap_password = md5($password);
		        	@ldap_close($ldap);
		   		} else 
		   		{
					$login_local = true;
					$ldap_password = 0;
		    	}

		}
		

		if($login_direct){
			$sql = "select user_id,id_number,username,firstname,lastname,user_status,user_token,
			user_photo,is_admin,is_disable,access_online
							from users where id_number = '$idnumber' LIMIT 1";


				$check_user = $this->db->query($sql);
				if($check_user->num_rows() == 0){
						$login_local = true;
				}else{
						$u = $check_user->row_array();
						$login_time	= time();
						$session_id = md5(time());
						//$session_id = $this->session->userdata('session_id');

						//check first if local connection
						if(in_array($exp_ip[0], $allowed_start_ip))
						{
							if($exp_ip[0] . ". " . $exp_ip[1] . "." . $exp_ip[2] != "192.168.1")
							{
								$allow_check_login = true;
							}
							else
							{
								$this->session->set_flashdata('msg', 'Sorry, you are not allowed to login outside of the hospital.');
								$this->session->set_flashdata('msg_type', 'danger');
								redirect("$this->baseurl/main/", 'refresh');	
							}
						}else{

							if($u['access_online'] == "1")
							{
								$allow_check_login = true;
							}
							else
							{
								$this->session->set_flashdata('msg', 'Sorry, you are not allowed to login outside of the hospital.');
								$this->session->set_flashdata('msg_type', 'danger');
								redirect("$this->baseurl/main/", 'refresh');	
							}
						}

					if($allow_check_login){

						// check if user has token
						if($u['user_token'] == 0){
							$user_token = md5($u['username'] . "|" . time());
						}else{
							$user_token = $u['user_token'];
						}

						if($u['user_status'] == "2"){
							$this->session->set_flashdata('msg', 'Account disabled. please contact admin.');
							$this->session->set_flashdata('msg_type', 'danger');
							redirect("$this->baseurl/main/", 'refresh');

						}
						if($u['user_status'] == "0"){
							$this->session->set_flashdata('msg', 'Account pending. please contact admin.');
							$this->session->set_flashdata('msg_type', 'danger');
							redirect("$this->baseurl/main/", 'refresh');
						}

						if($u['is_disable'] == "1"){
							$this->session->set_flashdata('msg', 'Account disabled. please contact admin.');
							$this->session->set_flashdata('msg_type', 'danger');
							redirect("$this->baseurl/main/", 'refresh');
						}						

						if($u['user_status'] == "1"){
							
				              
				            	$newdata = array(
									'user_token'	=> $user_token,
				                   	'user_session_id'    => $session_id,
				                   	'lock_screen'	=> 0,
				                   	'logged_in' 	=> TRUE,
				                   	'http_referer' 	=> ''
				              	 );	 
								$this->session->set_userdata($newdata);

								if($u['user_token'] == 0){
									$data = array('session_id' => $session_id, 'date_login' => $login_time, 'user_token' => $user_token,
									 'lock_screen' => 0, 'ldap_password' => $ldap_password);
								}else{
									$data = array('session_id' => $session_id, 'date_login' => $login_time,
										'lock_screen' => 0, 'ldap_password' => $ldap_password);
								}
								$data_where = array('user_id'=> $u['user_id']); 	
								$this->db->update('users', $data, $data_where);

								redirect("$this->baseurl/dashboard/", 'refresh');
						}
					}

				}		
		}

		if($login_local){
			$password = md5($password);
			$sql = "select user_id,id_number,username,firstname,lastname,user_status,user_token,user_photo,is_admin,is_disable 
				from users where id_number = '$idnumber' and password = '$password' LIMIT 1";

			$check_sql_query = true;
		}

		if($check_sql_query){
		
				$check_user = $this->db->query($sql);
				if($check_user->num_rows() == 0){
						$msg = "<strong>Warning</strong> Invalid Username / Password";
						$msg_type = "warning";


				}else{
				
				
						$u = $check_user->row_array();
						$login_time	= time();
						$session_id = md5(time());
						//$session_id = $this->session->userdata('session_id');


						if(in_array($exp_ip[0], $allowed_start_ip))
						{
							if($exp_ip[0] . ". " . $exp_ip[1] . "." . $exp_ip[2] != "192.168.1")
							{
								$allow_check_login = true;
							}
							else
							{
								$this->session->set_flashdata('msg', 'Sorry, you are not allowed to login outside of the hospital.');
								$this->session->set_flashdata('msg_type', 'danger');
								redirect("$this->baseurl/main/", 'refresh');	
							}
						}else{

							if($u['access_online'] == "1")
							{
								$allow_check_login = true;
							}
							else
							{
								$this->session->set_flashdata('msg', 'Sorry, you are not allowed to login outside of the hospital.');
								$this->session->set_flashdata('msg_type', 'danger');
								redirect("$this->baseurl/main/", 'refresh');	
							}
						}


				if($allow_check_login){

						// check if user has token
						if($u['user_token'] == 0){
							$user_token = md5($u['username'] . "|" . time());
						}else{
							$user_token = $u['user_token'];
						}

						if($u['user_status'] == "2"){
							$this->session->set_flashdata('msg', 'Account disabled. please contact admin.');
							$this->session->set_flashdata('msg_type', 'danger');
							redirect("$this->baseurl/main/", 'refresh');

						}
						if($u['user_status'] == "0"){
							$this->session->set_flashdata('msg', 'Account pending. please contact admin.');
							$this->session->set_flashdata('msg_type', 'danger');
							redirect("$this->baseurl/main/", 'refresh');
						}

						if($u['is_disable'] == "1"){
							$this->session->set_flashdata('msg', 'Account disabled. please contact admin.');
							$this->session->set_flashdata('msg_type', 'danger');
							redirect("$this->baseurl/main/", 'refresh');
						}						

						if($u['user_status'] == "1"){
							
				              
				            	$newdata = array(
									'user_token'	=> $user_token,
				                   	'user_session_id'    => $session_id,
				                   	'lock_screen'	=> 0,
				                   	'logged_in' 	=> TRUE,
				                   	'http_referer' 	=> ''
				              	 );	 
								$this->session->set_userdata($newdata);

								if($u['user_token'] == 0){
									$data = array('session_id' => $session_id, 'date_login' => $login_time, 
										'user_token' => $user_token, 'lock_screen' => 0, 'ldap_password' => 0);
								}else{
									$data = array('session_id' => $session_id, 'date_login' => $login_time,
										'lock_screen' => 0, 'ldap_password' => 0);
								}
								$data_where = array('user_id'=> $u['user_id']); 	
								$this->db->update('users', $data, $data_where);

								redirect("$this->baseurl/dashboard/", 'refresh');
						}

				}	
			}
		}

	
	
		$o['page_now'] 	= $this->page_now;
		$o['message'] 	= $msg;		
		$o['msg_type'] 	= $msg_type;

		$this->smarty->registerPlugin("modifier","sslash", "stripslashes");
		$o['baseurl'] = $this->baseurl;
		$this->smarty->assign('o',$o);			
		$this->smarty->view( 'main.tpl',$data);
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */