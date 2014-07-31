<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!class_exists('CI_Model')) { class CI_Model extends Model {} }


class Userconfig_model extends CI_Model
{
	public function __construct () {
		parent::__construct();
		
		$this->load->library('session');
		$this->load->helper('url');
		$this->baseurl = $this->config->config['base_url'];			
		
	}

	#### CHECK SESSION ##############
	public function check_user_session(){

		$user_session = $this->session->userdata('user_session_id');

		$user_data = $this->session->all_userdata();
		if($user_data['user_session_id'] != ""){
			$status = 'valid';
		}else{
			$status = 'invalid';

			$this->session->set_flashdata('msg', 'Session Expired, please login again.');
			$this->session->set_flashdata('msg_type', 'warning');
			redirect("$this->baseurl/main/", 'refresh');
		}

//		echo "<pre>";
//		print_r($user_data);

		/* check database */
		return $status;
		/*
		*/
	}

	public function check_user_data(){

		$user_session = $this->session->userdata('user_session_id');

		$sql = "select user_id,firstname,lastname,user_photo,lock_screen,is_admin,username,post_bulletin,
		user_token,session_id,user_status,is_disable,id_number,ldap_password from users where session_id = '$user_session' and user_status = '1' LIMIT 1";
		$check_session = $this->db->query($sql);
		if($check_session->num_rows() == 0){
			$this->session->set_flashdata('msg', 'Session Expired, please login again.');
			$this->session->set_flashdata('msg_type', 'warning');
			//redirect("$this->baseurl/main/", 'refresh');
		}else{
			$check_u = $check_session->result_array();


			if($check_u[0]['user_status'] == "2"){
				$this->session->set_flashdata('msg', 'Account disabled. please contact admin.');
				$this->session->set_flashdata('msg_type', 'danger');
				redirect("$this->baseurl/main/", 'refresh');

			}
			if($check_u[0]['user_status'] == "0"){
				$this->session->set_flashdata('msg', 'Account pending. please contact admin.');
				$this->session->set_flashdata('msg_type', 'danger');
				redirect("$this->baseurl/main/", 'refresh');
			}

			if($check_u[0]['is_disable'] == "1"){
				$this->session->set_flashdata('msg', 'Account disabled. please contact admin.');
				$this->session->set_flashdata('msg_type', 'danger');
				redirect("$this->baseurl/main/", 'refresh');
			}

			$check_u[0]['logged_in'] = $this->session->userdata('logged_in');
			$check_u[0]['http_referer'] = $this->session->userdata('http_referer');
		}
			
		return $check_u[0];	
	}

	public function check_userpage_lock(){
		$user_session = $this->check_user_session();
		$user_data = $this->session->all_userdata();
		if($user_session == 'valid' and $user_data['lock_screen'] == "1"){
			$is_lock = "true";
		}else{
			$is_lock = "false";
		}
		return $is_lock;
	}
	#################### CLEAN UP #################
	
	public function cleanup($word)
	{
		$word = trim($word);
		$word = strip_tags($word, " <STRONG> <EM> <U> <BR>");
		$word = str_replace("<IMG","<img",$word);
		//$word = nl2br($word);
		$word = addslashes($word);		
		//$word = strtolower($word);
	
		return $word;	
	}
	
	public function cleanup_text($word)
	{
		$word = trim($word);
		$word = strip_tags($word, "<table> <td> <tr> <tbody> <a> <p style> <style> <STRONG> <EM> <U> <br> <IMG> <img> <font> <p> <embed> <param> <object> <div> <span> <ol> <li> <ul> <blockquote> <h1> <h2> <h3> <h4>");	
		$word = addslashes($word);
	
		return $word;	
	}
	#################### URL SPACELINE #################
	public function urlspaceline($string)
	{
		//$string = trim(str_replace(" ","-",$string));
		//return $string;
		$string = trim(str_replace(array('`','’'),"",$string));
		$string = preg_replace("`\[.*\]`U","",$string);
		$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
		$string = htmlentities($string, ENT_COMPAT, 'utf-8');
		$string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
		$string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);
		return strtolower(trim($string, '-'));			
	}	

	function get_date($date1, $date2)
	{
		$this->date1 = $date1;
		$this->date2 = $date2;
		$this->days = intval((strtotime($this->date1) - strtotime($this->date2)) / 86400);		
		$this->years = date("Y",strtotime($this->date1)) - date("Y",strtotime($this->date2));
		
		$below_year_comp = strtotime($this->date1) - strtotime($this->date2);
		$this->below_years = floor($below_year_comp / 31556927.29); //the average year is 365.242214 days
		
		$this->a = ((strtotime($this->date1) - strtotime($this->date2))) % 86400;		
		$this->hours = intval(($this->a) / 3600);
		$this->a = ($this->a) % 3600;
		$this->minutes = intval(($this->a) / 60);
		$this->a = ($this->a) % 60;
		$this->seconds = $this->a;
		
		return $this;
				
	}

	function check_user_dashboard(){


		$user_data = $this->check_user_data();
		$show_user_dash = array();		
		/* check user default dashboard*/
		$sql = "select d_name,d_path,d_icons,d_color,d_orderby from dashboard
			where
			is_default = '1'
				and d_status = '1' 
			order by d_orderby asc";
		$check_def_dash = $this->db->query($sql);
		if($check_def_dash->num_rows() > 0){
			foreach($check_def_dash->result_array() as $cdd){
				$exp_path = explode("|", $cdd['d_path']);
				if(count($exp_path) == 1){
					$cdd['exp_path'] = $cdd['d_path'];
				}else{

					$show_param = array();
					for($i = 1; $i<= count($exp_path); $i++){
						$show_param[] = $exp_path[$i] . "=" . $user_data[$exp_path[$i]]; 
					}
					$show_param = implode("&", $show_param);
					$cdd['exp_path'] = $exp_path[0]  . "?" . substr($show_param,0,-2);
				}
				$show_user_dash[] = $cdd;
			}
		}

		/* check user assigned dashboards*/
		$sql = "select distinct d_name,d_path,d_icons,d_color,d_orderby from dashboard
			where
				dashboard_id in (select dashboard_id from user_dashboard where user_id = '{$user_data['user_id']}' and ud_status = '1' ) 
				and is_default = '0'
				and d_status = '1' 
			order by d_orderby asc";

		$check_dash = $this->db->query($sql);
		
		if($check_dash->num_rows() > 0){
			foreach($check_dash->result_array() as $cd){
				$exp_path = explode("|", $cd['d_path']);
				if(count($exp_path) == 1){
					$cd['exp_path'] = $cd['d_path'];
				}else{
					$show_param = array();
					for($i = 1; $i<= count($exp_path); $i++){
						$show_param[] = $exp_path[$i] . "=" . $user_data[$exp_path[$i]]; 
					}

					$show_param = implode("&", $show_param);
					$cd['exp_path'] = $exp_path[0]  . "?" . substr($show_param,0,-2);
				}	
				$show_user_dash[] = $cd;
			}
		}
		return $show_user_dash;

	}

	function HumanReadableFilesize($size) {

	    // Adapted from: http://www.php.net/manual/en/function.filesize.php
	 
	    $mod = 1024;
	 
	    $units = explode(' ','B KB MB GB TB PB');
	    for ($i = 0; $size > $mod; $i++) {
	        $size /= $mod;
	    }
 
    	return round($size, 2) . ' ' . $units[$i];
	}	

}