<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Compose extends CI_Controller {


	function __construct()
	{
		
		parent::__construct();
		$this->load->library('session');
		$this->load->model('Userconfig_model', 'userconfig');	
		$this->load->model('Comm_config_model', 'commconfig');	
		$this->load->model('Messages_model', 'msg_model');
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper(array('url','date'));
		
		$this->baseurl = $this->config->config['base_url'];		
		$this->page_now = "compose";
	}

	public function create_message(){
		include("includes/configurations.php");	

		$compose_type 		= $this->uri->segment(3);
		$msg_type_in = array('users','groups');
		$compose_signature = md5(time() . "_" . $user_data['user_id']);
		if(in_array("$compose_type",$msg_type_in)){
			if($compose_type == "users"){
				$group_type = "0";
				$comp_function = "comp_users";
			}else{
				$group_type = "1";
				$comp_function = "comp_groups";
			}	

				$data = array(	'user_id' => $user_data['user_id'], 
								'msg_type'=> $group_type, 
								'is_parent'	=> 1,
								'msg_comp_sig'=> $compose_signature, 
								'date_added'=> time());
				$this->db->set($data);
				$this->db->insert('message_compose');
				$last_inserted_id = $this->db->insert_id();
				redirect("$this->baseurl/compose/$comp_function/$last_inserted_id/$compose_signature", 'refresh');

		}else{
			$this->session->set_flashdata('msg', 'error, invalid type of message, please try again');
			$this->session->set_flashdata('msg_type', 'danger');
			redirect("$this->baseurl/messages/inbox", 'refresh');	
		}
	}
	public function comp_users(){

		include("includes/configurations.php");		

		$message_id 		= $this->uri->segment(3);
		$msg_signature 		= $this->uri->segment(4);

		$sql = "select msg_compose_id,msg_type,user_id,send_to_ids,user_lookups,subject,details,cc_users,is_reply 
				from 
					message_compose 
				where
					user_id = '{$user_data['user_id']}'
				and
					msg_comp_sig = '$msg_signature'
				and	
					msg_compose_id = '$message_id'
				and	
					compose_status = '0'
				and
					msg_type = '0'
				LIMIT 1
				";

		$check_compose = $this->db->query($sql);
		if($check_compose->num_rows() == 0){

			//$this->session->set_flashdata('msg', 'danger, invalid type of message, please try again');
			//$this->session->set_flashdata('msg_type', 'danger');
			redirect("$this->baseurl/messages/inbox", 'refresh');	
		}else{
			$get_com_details = $check_compose->result_array();

			//check send to ids
			//echo $get_com_details[0]['send_to_ids'];
			if($get_com_details[0]['send_to_ids'] != "0"){
				
				$exp_user = explode("|",$get_com_details[0]['send_to_ids']);
				$user_to = array();
				for($i=0;$i<count($exp_user); $i++)
				{
					$name = $this->commconfig->check_user_name_for_msg($exp_user[$i]);

					if($get_com_details[0]['is_reply'] == 1 && $i == 0){
						$user_to[] = array('id'=>  "{$name['user_id']}",'text' => "{$name['firstname']}" . " " . "{$name['lastname']}",'locked' => "true");
					}else{
						$user_to[] = array('id'=>  "{$name['user_id']}",'text' => "{$name['firstname']}" . " " . "{$name['lastname']}");
					}

				}
				$o['default_user_to'] = json_encode($user_to);
				$o['has_saved_user_to'] = 1;
			}

			if($get_com_details[0]['cc_users'] != "0"){

				$exp_user_cc = explode("|",$get_com_details[0]['cc_users']);
				$user_cc = array();
				for($i=0;$i<count($exp_user_cc); $i++)
				{
					$name = $this->commconfig->check_user_name_for_msg($exp_user_cc[$i]);
					$user_cc[] = array('id'=>  "{$name['user_id']}",'text' => "{$name['firstname']}" . " " . "{$name['lastname']}");

				}
				$o['default_user_cc'] = json_encode($user_cc);
				$o['has_saved_user_cc'] = 1;

			}
			$o['gmd'] =  $get_com_details[0];
		
			$o['page_now'] = 'compose_user';
			$o['message'] = $msg;		
			$o['msg_type'] = $msg_type;			
			$this->smarty->registerPlugin("modifier","sslash", "stripslashes");
			$o['baseurl'] = $this->baseurl;
			$this->smarty->assign('o',$o);			
			$this->smarty->view( 'messages/compose_user.tpl',$data);	
		}	

	}

	public function comp_groups(){

		include("includes/configurations.php");		

		$message_id 		= $this->uri->segment(3);
		$msg_signature 		= $this->uri->segment(4);

		$sql = "select msg_compose_id,msg_type,user_id,send_to_ids,user_lookups,subject,details,cc_users 
				from 
					message_compose 
				where
					user_id = '{$user_data['user_id']}'
				and
					msg_comp_sig = '$msg_signature'
				and	
					msg_compose_id = '$message_id'
				and	
					compose_status = '0'
				and
					msg_type = '1'
				LIMIT 1
				";
		$check_compose = $this->db->query($sql);
		if($check_compose->num_rows() == 0){

			$this->session->set_flashdata('msg', 'error, invalid type of message, please try again');
			$this->session->set_flashdata('msg_type', 'error');
			redirect("$this->baseurl/messages/inbox", 'refresh');	
		}else{
			$get_com_details = $check_compose->result_array();
			$o['gmd'] =  $get_com_details[0];

			//check send to ids
			//echo $get_com_details[0]['send_to_ids'];
			if($get_com_details[0]['send_to_ids'] != "0"){
				
				$exp_user = explode("|",$get_com_details[0]['send_to_ids']);
				$user_to = array();
				for($i=0;$i<count($exp_user); $i++)
				{
					$grp_to = $this->commconfig->check_user_groups_for_msg($exp_user[$i]);
					$user_to[] = array('id'=>  "{$grp_to['group_id']}",'text' => "{$grp_to['group_name']}");

				}
				$o['default_user_to'] = json_encode($user_to);
				$o['has_saved_user_to'] = 1;
			}

			if($get_com_details[0]['cc_users'] != "0"){

				$exp_user_cc = explode("|",$get_com_details[0]['cc_users']);
				$user_cc = array();
				for($i=0;$i<count($exp_user_cc); $i++)
				{
					$name = $this->commconfig->check_user_name_for_msg($exp_user_cc[$i]);
					$user_cc[] = array('id'=>  "{$name['user_id']}",'text' => "{$name['firstname']}" . " " . "{$name['lastname']}");

				}
				$o['default_user_cc'] = json_encode($user_cc);
				$o['has_saved_user_cc'] = 1;

			}		

			// comp groups all
			$search_users_group = $this->commconfig->search_compose_groups_all($user_data['user_id']);
			$o['search_users_group'] = $search_users_group;
			$o['page_now'] = 'compose_group';
			$o['message'] = $msg;		
			$o['msg_type'] = $msg_type;			
			$this->smarty->registerPlugin("modifier","sslash", "stripslashes");
			$o['baseurl'] = $this->baseurl;
			$this->smarty->assign('o',$o);			
			$this->smarty->view( 'messages/compose_group.tpl',$data);	
		}		
	}

	function search_users(){

		include("includes/configurations.php");	
		#checkif user admin
		$term 			 	= $this->input->get('term');

		if(strlen($term) > 2){
			//https://github.com/ivaynberg/select2/wiki/PHP-Example
			$search_users = $this->commconfig->search_compose_users($term,$user_data['user_id']);
			echo $search_users;
		}
	}

	function search_groups(){

		include("includes/configurations.php");	
		#checkif user admin
		$term 			 	= $this->input->get('term');

		if(strlen($term) > 2){
			//https://github.com/ivaynberg/select2/wiki/PHP-Example
			$search_users = $this->commconfig->search_compose_groups($term,$user_data['user_id']);
			echo $search_users;
		}
	}

	function delete_compose_message(){

		include("includes/configurations.php");	
		####################	
		$compose_id 			= $this->input->post('compose_id');
		$this->db->delete('message_compose', array('msg_compose_id' => $compose_id, 'user_id'=> $user_data['user_id'])); 
		echo 1;

	}


	function delete_selected_messages(){
		include("includes/configurations.php");	
		$checked 				= $this->input->post('checked');	
		$msg_window 			= $this->input->post('msg_window');

		if($msg_window == 'draft'){
				for($i=0;$i<count($checked); $i++){
					$this->db->delete('message_compose', array('msg_compose_id' => $checked[$i], 'user_id'=> $user_data['user_id'])); 
				}
		}

		if($msg_window == 'inbox' or $msg_window == 'search'){

				for($i=0;$i<count($checked); $i++){
					$data = array('msg_status'=> 2);
					$this->db->simple_query("SET NAMES 'utf-8'");  
					$data_where = array('msg_conv_id' => $checked[$i], 'receiver_id'=> $user_data['user_id']); 		
					$this->db->set($data);
					$this->db->update('message_conversation', $data, $data_where);

				}
		}

		if($msg_window == 'trash'){
			for($i=0;$i<count($checked); $i++){
				$data = array('msg_status'=> 3);
				$this->db->simple_query("SET NAMES 'utf-8'");  
				$data_where = array('msg_conv_id' => $checked[$i], 'receiver_id'=> $user_data['user_id']); 		
				$this->db->set($data);
				$this->db->update('message_conversation', $data, $data_where);

			}
		}	

		if($msg_window == 'sent'){
			for($i=0;$i<count($checked); $i++){
				$data = array('is_hide'=> 1);
				$this->db->simple_query("SET NAMES 'utf-8'");  
				$data_where = array('msg_compose_id' => $checked[$i], 'user_id'=> $user_data['user_id']); 		
				$this->db->set($data);
				$this->db->update('message_compose', $data, $data_where);

			}			
		}	
		$this->session->set_flashdata('msg', 'message successfully deleted');
		$this->session->set_flashdata('msg_type', 'success');
		echo $this->baseurl . '/messages/' . $msg_window;
	}

	function undo_selected_item(){

		include("includes/configurations.php");	
		$checked 				= $this->input->post('checked');	
		$msg_window 			= $this->input->post('msg_window');

				for($i=0;$i<count($checked); $i++){
					$data = array('msg_status'=> 1, 'folder_id' => 0);
					$this->db->simple_query("SET NAMES 'utf-8'");  
					$data_where = array('msg_conv_id' => $checked[$i], 'receiver_id'=> $user_data['user_id']); 		
					$this->db->set($data);
					$this->db->update('message_conversation', $data, $data_where);

				}
		$this->session->set_flashdata('msg', 'message successfully moved to inbox');
		$this->session->set_flashdata('msg_type', 'success');
		echo $this->baseurl . '/messages/inbox';	
	}
	function compose_save_draft_user(){

		include("includes/configurations.php");	
		####################	
		$send_to 			= $this->input->post('send_to');	
		$send_cc 			= $this->input->post('send_cc');	
		$compose_id 		= $this->input->post('compose_id');	
		$subject 			= $this->userconfig->cleanup($this->input->post('subject'));	
		$msg_content 		= $this->userconfig->cleanup_text($this->input->post('msg_content'));	

		if($send_cc == 0){
			$send_cc = 0;
		}else{
			$send_cc 	= implode("|",$send_cc);
		}
		$send_to 	= implode("|",$send_to);
		

		$data = array('send_to_ids'=> $send_to,'cc_users' => $send_cc,'subject'=> $subject,'details' => $msg_content);
		$this->db->simple_query("SET NAMES 'utf-8'");  
		$data_where = array('msg_compose_id'=> $compose_id, 'user_id' => $user_data['user_id']); 		
		$this->db->set($data);
		$this->db->update('message_compose', $data, $data_where);

		echo 1;
		//echo print_r($data);

	}

	function upload_file(){

		//include("includes/configurations.php");	
		$msg_compose_id		= $this->input->post('msg_compose_id');	
		$user_id 	 		= $this->input->post('user_id');
		$file_element_name = 'userfile';


	
		$config['upload_path'] = './assets/attachments/';
		$config['allowed_types'] = 'jpeg|jpg|doc|docx|xls';
		$config['max_size']	= '10240'; // 50MB
		$config['encrypt_name'] = TRUE;
		// Validate the file type			
		//$fileParts = md5(time() . "_" . $_FILES['Filedata']['name']);
		//$config['file_name'] = $fileParts;
		$this->load->library('upload', $config);


		if ( ! $this->upload->do_upload('Filedata'))
		{
			$error = array('error' => $this->upload->display_errors());
			echo strip_tags($error['error']);
		}
		else
		{
			$data2 = array('upload_data' => $this->upload->data());
			$new_filename 			= $data2['upload_data']['file_name'];
			$original_filename 		= $data2['upload_data']['client_name'];
			$file_size 				= $data2['upload_data']['file_size'];
			$upload_type 			= $data2['upload_data']['file_ext'];

			$data = array('msg_compose_id' => $msg_compose_id, 'att_name'=> $original_filename, 
				'att_path'=> "$new_filename",'user_id'=> $user_id, 'file_size' => $file_size,
				'upload_type'=> $upload_type);
			$this->db->set($data);
			$this->db->insert('message_attachment');
			echo 1;
		}
	}

	function show_attachments(){
		include("includes/configurations.php");	
		$id		= $this->input->post('id');		
		$sql = "select msg_compose_id,attachment_id,att_name,att_path from message_attachment where msg_compose_id = '$id' and user_id = '{$user_data['user_id']}' order by attachment_id desc";
		$check_attach = $this->db->query($sql);
		if($check_attach->num_rows() == 0){
			echo '<div class="alert alert-warning fade in"><i class="fa-fw fa fa-warning"></i> No Attachment found.</div>';
		}else{
			$show_attachment = array();
			foreach($check_attach->result_array() as $ca){
				$show_attachment[] = $ca;
			}
			$o['show_a'] = $show_attachment;
			$o['show_attachment_table'] = true;
			$o['message'] = $msg;		
			$o['msg_type'] = $msg_type;			
			$this->smarty->registerPlugin("modifier","sslash", "stripslashes");
			$o['baseurl'] = $this->baseurl;
			$this->smarty->assign('o',$o);			
			$this->smarty->view( '_div/attachments.tpl',$data);	

		}
	}

	function delete_selected_attachments(){

		include("includes/configurations.php");	
		$id		= $this->input->post('id');	
		$mid 	= $this->input->post('mid');	
		$fname 	= $this->input->post('fname');	
		
		//unlink("assets/attachments/" . $fname);- if file will be deleted, forwarded messages with attachment cannot be located
		$this->db->delete('message_attachment', array('attachment_id' => $id, 'msg_compose_id' => $mid, 'user_id'=> $user_data['user_id'])); 
		echo 1;	
	}

	function send_message(){

		include("includes/configurations.php");	
		####################	
		$send_to 			= $this->input->post('send_to');	
		$send_cc 			= $this->input->post('send_cc');	
		$compose_id 		= $this->input->post('compose_id');	
		$msg_type 			= $this->input->post('msg_type');	
		$subject 			= $this->userconfig->cleanup($this->input->post('subject'));	
		$msg_content 		= $this->userconfig->cleanup_text($this->input->post('msg_content'));	

		if($send_cc == 0){
			$send_cc = 0;
		}else{
			$send_cc_orig = $send_cc;
			$send_cc 	= implode("|",$send_cc);

		}
		$send_to_orig = $send_to;
		$send_to 	= implode("|",$send_to);
		

		$this->db->trans_start();

		$data = array('send_to_ids'=> $send_to,'cc_users' => $send_cc,'subject'=> $subject,'details' => $msg_content,'compose_status' => 1, 'date_sent' => time());
		$this->db->simple_query("SET NAMES 'utf-8'");  
		$data_where = array('msg_compose_id'=> $compose_id, 'user_id' => $user_data['user_id']); 		
		$this->db->set($data);
		$this->db->update('message_compose', $data, $data_where);

		/* send message compose to receiver */

		//check if message type send as group or users
		if($msg_type == 0){
			for($i=0;$i<count($send_to_orig); $i++)
			{
					$msg_conv_sig = md5($send_to_orig[$i] . "_" . $compose_id . "_" . $user_data['uesr_id'] . "_" . rand(0000,9999));
					$data = array(	'sender_id' => $user_data['user_id'], 
									'receiver_id'=> $send_to_orig[$i], 
									'msg_compose_id'	=> $compose_id,
									'sent_type'=> 0, 
									'date_added'=> time(),
									'msg_conv_sig' => $msg_conv_sig,
									'is_cc' => 0
								);
					$this->db->set($data);
					$this->db->insert('message_conversation');
			}
		}else{

			for($i=0;$i<count($send_to_orig); $i++)
			{
				$sql = "select user_id from user_group_member where group_id = '{$send_to_orig[$i]}'";
				
				$check_gmem = $this->db->query($sql);
				if($check_gmem->num_rows() == 0){}
				else{

					foreach($check_gmem->result_array() as $cg){
						$msg_conv_sig = md5($send_to_orig[$i] . "_" . $compose_id . "_" . $user_data['uesr_id'] . "_" . rand(0000,9999));
						$data = array(	'sender_id' => $user_data['user_id'], 
										'receiver_id'=> $cg['user_id'], 
										'msg_compose_id'	=> $compose_id,
										'sent_type'=> 0, 
										'date_added'=> time(),
										'msg_conv_sig' => $msg_conv_sig,
										'is_cc' => 0
									);
						$this->db->set($data);
						$this->db->insert('message_conversation');
					}
					
				}
			}			
		}

		if($send_cc != 0){
			/* send message compose to receiver */
			for($i=0;$i<count($send_cc_orig); $i++)
			{
					$msg_conv_sig = md5($send_cc_orig[$i] . "_" . $compose_id . "_" . $user_data['uesr_id'] . "_" . rand(0000,9999));
					$data = array(	'sender_id' => $user_data['user_id'], 
									'receiver_id'=> $send_cc_orig[$i], 
									'msg_compose_id'	=> $compose_id,
									'sent_type'=> 0, 
									'date_added'=> time(),
									'msg_conv_sig' => $msg_conv_sig,
									'is_cc' => 1
								);
					$this->db->set($data);
					$this->db->insert('message_conversation');
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
    		echo 'error';
		}else{

			$this->session->set_flashdata('msg', 'Message successfully sent');
			$this->session->set_flashdata('msg_type', 'success');
			echo 'success';
		}
	}

	/* Add new folder*/

	function add_new_folder(){
		include("includes/configurations.php");	
		####################	
		$folder_name 			= $this->userconfig->cleanup($this->input->post('folder_name'));

		$data = array(	'user_id' => $user_data['user_id'], 
						'folder_name'=> $folder_name, 
						'date_added'=> time()
					);
		$this->db->set($data);
		$this->db->insert('message_folders');
		echo 1;		
	}


	function show_addressbook(){
		include("includes/configurations.php");	
		$popup_type 		= $this->uri->segment(3);

		$sql = "select group_name,group_id,gtype from user_group where gstatus = '1' order by group_name asc";

		$check_def = $this->db->query($sql);
		if($check_def->num_rows() > 0){
			$show_default_group = array();
			$show_my_group = array();
			foreach($check_def->result_array() as $cu){
				if($cu['gtype'] == '1'){
					$show_default_group[] = $cu;
				}else{
					$show_my_group[] = $cu;
				}
				//$show_comp_group[] = array('id'=>  "{$cu['group_id']}",'text' => "{$cu['group_name']}");
			}
		}
		$o['popup_type']			= $popup_type;
		$o['show_default_group'] = $show_default_group;
		$o['show_my_group'] = $show_my_group;
		/* check default groups */
		$o['message'] = $msg;		
		$o['msg_type'] = $msg_type;			
		$this->smarty->registerPlugin("modifier","sslash", "stripslashes");
		$o['baseurl'] = $this->baseurl;
		$this->smarty->assign('o',$o);			
		$this->smarty->view( 'messages/groups_lookup.tpl',$data);			
	}

	public function include_group_comp(){
		include("includes/configurations.php");	
		$group_id 			= $this->input->post('group_id');	
		
		$sql = "select user_id from user_group_member where group_id = '$group_id'";
				
		$check_gmem = $this->db->query($sql);
		if($check_gmem->num_rows() == 0){}
		else{
			$show_comp_group = array();
			foreach($check_gmem->result_array() as $cg){
				$check_name = $this->commconfig->check_user_name_for_msg($cg['user_id']);
				//$show_comp_group[] = array('id'=>  "{$cg['user_id']}",'text' => "{$check_name['firstname']} {$check_name['lastname']}");
				$show_comp_group[] = "{$cg['user_id']}:{$check_name['firstname']} {$check_name['lastname']}";
			}
			$result = implode(",",$show_comp_group);
			//echo str_replace(array('[', ']'), '', json_encode($show_comp_group));
			//echo json_encode($show_comp_group);
			echo $result;
		}
	}

}
