<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Home extends CI_Controller {


	function __construct()
	{
		
		parent::__construct();
		$this->load->library('session');
		$this->load->model('Userconfig_model', 'userconfig');	
		$this->load->model('Messages_model', 'msg_model');	
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('url');
		$this->baseurl = $this->config->config['base_url'];		
		$this->page_now = "home";
	}
	
	public function index()
	{
		include("includes/configurations.php");	

		$sess_msg 		= $this->session->flashdata('msg');
		$sess_msg_type 	= $this->session->flashdata('msg_type');
		if($sess_msg){
			$msg = $sess_msg;
			$msg_type = $sess_msg_type;
		}

		/* check bulletin board*/
		$sql = "select bstatus,bulletin_id,bsubject,bcontent,date_added,views from bulletinboard where bstatus != '2' 
			order by
				bulletin_id desc limit 10 offset 0";

		$check_message = $this->db->query($sql);
		if($check_message->num_rows() == 0){
				$msg = "No bulletin found";
				$msg_type = "info";	
		}else{
				$msg_content = array();
				foreach($check_message->result_array() as $cm){
					$cm['date_added_human'] = 	timespan($cm['date_added'],time());
					$msg_content[] = $cm;
				}
				$o['msg_content'] = $msg_content;
				$o['show_bulletin_table'] = true;
		}


		/* check birthdays*/
		$date_today = date("m-d",time());
		$date_tomorrow = date("m-d",mktime(0,0,0,date("m"),date("d") + 1, date("Y")));	
		$date_upcoming = date("m-d",mktime(0,0,0,date("m"),date("d") + 3, date("Y")));	
	
	//echo "[$user_login_id]";
	$list_arr = array('2205','2201','2187','2199','2190','2188','2204','2194','2191','2777','2189','11','10','359','360');
	//echo "[]";
	/*Check birthday today*/
		$sql = "select user_photo,id_number,firstname,lastname,department,user_id,birthdate,
					(select department_name from department where department_id = users.department LIMIT 1) as department_name	
				from 
					users 
				where 
				substr(birthdate,6) = '$date_today'  and user_status = '1' and is_disable != '1'";

		$check_birthday = $this->db->query($sql);
		if($check_birthday->num_rows() == 0){
			$check_birthday_tomorrow = true;
		}else{
			$show_birthday = array();
			foreach($check_birthday->result_array() as $cb){
				$show_birthday[] = $cb;
			}
			$o['show_birthday'] = $show_birthday;
			$o['show_bday_today_table'] = true;

		}

		if($check_birthday_tomorrow){

			$sql = "select user_photo,id_number,firstname,lastname,department,user_id,birthdate,
					(select department_name from department where department_id = users.department LIMIT 1) as department_name	
				from 
					users 
				where 
				substr(birthdate,6) = '$date_tomorrow'  and user_status = '1' and is_disable != '1'";

			$check_birthday = $this->db->query($sql);
			if($check_birthday->num_rows()> 0){
				$show_birthday = array();
				foreach($check_birthday->result_array() as $cb){
					$show_birthday[] = $cb;
				}
				$o['show_birthday'] = $show_birthday;
				$o['show_bday_today_table'] = true;		
			}	
		}	

			/* check meetings*/
			$check_approved_meetings = $this->msg_model->get_request_meetings($user_data['user_id'],1,2);
			if($check_approved_meetings != 'no_result_found'){
				$o['cameet'] = $check_approved_meetings;
				$o['show_approved_table'] = true;
			}

		$show_calendar = array();
			foreach($check_approved_meetings as $cu){
					$exp = explode(" ", $cu['meeting_color']);
					$className = array('event' => $exp[0]);

					if($cu['is_allday'] == "1"){
						$start = date("Y-m-d",strtotime($cu['date_from']));
						$end = date("Y-m-d",strtotime($cu['date_to']));		
						$show_calendar[] = array('id' => "{$cu['meeting_user_id']}",  'title'=>  "{$cu['subject']}",
						'start' => "$start", 'end'=> "$end", 'className' => $exp[0], 
						'icon'=> "{$cu['meeting_icon']}" );	

					}else{						
						$start = date("Y-m-d\TH:i:s",strtotime($cu['date_from']));
						$end = date("Y-m-d\TH:i:s",strtotime($cu['date_to']));
						$show_calendar[] = array('id' => "{$cu['meeting_user_id']}",  'title'=>  $cu['subject'],
						'start' => $start, 'end'=> $end, 'className' => $exp[0], 
						'icon'=> "{$cu['meeting_icon']}", 'allDay'=> false );						
					}

		}
		$json_econde_cal = json_encode($show_calendar);
		$o['json_econde_cal'] = $json_econde_cal;
							
		$o['page_now'] = $this->page_now;
		$o['message'] = $msg;		
		$o['msg_type'] = $msg_type;			
		$this->smarty->registerPlugin("modifier","sslash", "stripslashes");
		$o['baseurl'] = $this->baseurl;
		$this->smarty->assign('o',$o);			
		$this->smarty->view( 'home.tpl',$data);
	}


	function send_birthday_greetings(){
		include("includes/configurations.php");
		$birthday_user_id 		= $this->uri->segment(3);
		$compose_signature = md5(time() . "_" . $user_data['user_id']);

		$data = array('user_id' => $user_data['user_id'], 
					'send_to_ids' => $birthday_user_id,
					'msg_type'=> 0, 
					'is_parent'	=> 1,
					'msg_comp_sig'=> $compose_signature, 
					'subject'=> "Happy Birthday!", 
					'details'=> "Bithday Greetings here...", 
					'is_reply'=> 0,
					'date_added'=> time());
		$this->db->set($data);
		$this->db->insert('message_compose');
		$last_inserted_id = $this->db->insert_id();
		redirect("$this->baseurl/compose/comp_users/$last_inserted_id/$compose_signature", 'refresh');	
	}

	public function logout(){

		include("includes/configurations.php");	
	
		$data = array('session_id' => '','ldap_password' => 0);
		$data_where = array('user_id'=> $user_data['user_id']); 	
		$this->db->update('users', $data, $data_where);

	
		$unsetdata = array(
			'user_token'	=> '',
			'user_id'		=> '',
          	'username'  	=> '',
          	'firstname'  	=> '',
          	'lastname'  	=> '',
          	'user_photo'  	=> '',
           	'user_session_id'    => '',
           	'lock_screen'	=> 0,
           	'logged_in' 	=> FALSE,
           	'http_referer' 	=> '',
           	'is_admin'		=> ''
      	 );
		$this->session->unset_userdata($unsetdata);
		
		$_SESSION = array();

		$this->session->sess_destroy();

		$this->session->set_flashdata('msg', 'Logout success.');
		$this->session->set_flashdata('msg_type', 'success');
		redirect("$this->baseurl/", 'refresh');	

	}

	public function lock(){
		include("includes/configurations.php");	

		$this->load->library('user_agent');
		$page_refer =  $this->agent->referrer();
		$update_lockdata = array(
			'lock_screen'	=> 1,
           	'http_referer' 	=> $page_refer
			);
		$this->session->set_userdata($update_lockdata);
		$data = array('lock_screen' => 1);
		$data_where = array('user_id'=> $user_data['user_id'], 'session_id'=> $user_data['session_id']); 	
		$this->db->update('users', $data, $data_where);
		
		$user_data = $this->userconfig->check_user_data();
		redirect("$this->baseurl/lock", 'refresh');	

	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */