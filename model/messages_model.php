<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!class_exists('CI_Model')) { class CI_Model extends Model {} }


class Messages_model extends CI_Model
{
	public function __construct () {
		parent::__construct();
		
		$this->load->library('session');
		$this->load->model('Comm_config_model', 'commconfig');
		$this->load->model('Userconfig_model', 'userconfig');
		$this->load->helper(array('url','date'));
		$this->load->helper('number');
		//$this->load->helper('date');
		$this->baseurl = $this->config->config['base_url'];			
		
	}

	public function check_unread($user_id){
		$sql = "select count(msg_conv_id) as count_unread 
				from 
					message_conversation where receiver_id = '$user_id'
				and 
					msg_status = '0' LIMIT 1";

		$check_unread = $this->db->query($sql);
		if($check_unread->num_rows() == 0){
			return 0;
		}else{
			$show_ur = $check_unread->result_array();
			return $show_ur[0]['count_unread'];
		}
	}

	public function count_inbox_msgs($user_id,$folder_id){
		$sql = "select count(msg_conv_id) as count_unread 
				from 
					message_conversation where receiver_id = '$user_id'
				and 
					msg_status in ('0','1')
				and	
					folder_id = '$folder_id' LIMIT 1";
		$total_msg_count = $this->db->query($sql);
		if($total_msg_count->num_rows() == 0){
			return 0;
		}else{
			$getot = $total_msg_count->result_array();
			return $getot[0]['count_unread'];

		}
	}

	public function show_inbox_msgs($user_id,$folder_id,$limit,$paging_page){
		$sql = "select msg_conv_id,msg_compose_id,sender_id,receiver_id, date_added,msg_conv_sig,msg_status,
(select subject from message_compose where msg_compose_id = message_conversation.msg_compose_id LIMIT 1) as subject,
(select LEFT(regexp_replace(details, E'<.*?>', '', 'g' ),100) from message_compose where msg_compose_id = message_conversation.msg_compose_id LIMIT 1) as details,
(select firstname ||'|'|| lastname ||'|'|| user_photo ||'|'|| department ||'|'|| id_number from users  
	where 
		user_id = message_conversation.sender_id LIMIT 1) as name 
from 
	message_conversation where receiver_id = '$user_id' and msg_status in ('0','1')
and 
folder_id = '$folder_id' order by msg_status asc, msg_conv_id desc limit $limit offset $paging_page";
				
		$show_messages = $this->db->query($sql);
		if($show_messages->num_rows() == 0){
			return 'no_result_found';
		}else{
			$smgs = array();
			foreach($show_messages->result_array() as $sm){
				$sm['details'] = str_replace(array("<br>","<p>")," ",strip_tags($sm['details']));
				$exp_names = explode("|",$sm['name']);
				$sm['sname'] 			=	$exp_names[0] . " " . $exp_names[1];
				$sm['user_photo']		=	$exp_names[2];
				$sm['date_added_human'] = 	timespan($sm['date_added'],time());
				$sm['user_dept']		= 	strtolower($this->commconfig->show_department($exp_names[3]));
				$sm['id_number']		=	$exp_names[4];

				$smgs[] = $sm;
			}
			return $smgs;
		}
	}

	public function show_trash_msgs($user_id,$limit,$paging_page){
		$sql = "select msg_conv_id,msg_compose_id,sender_id,receiver_id, date_added,msg_conv_sig,msg_status,
(select subject from message_compose where msg_compose_id = message_conversation.msg_compose_id LIMIT 1) as subject,
(select firstname ||'|'|| lastname ||'|'|| user_photo ||'|'|| department from users  where user_id = message_conversation.sender_id LIMIT 1) as name 
from 
	message_conversation where receiver_id = '$user_id' and msg_status = '2'
order by msg_status asc, msg_conv_id desc limit $limit offset $paging_page";
				
		$show_messages = $this->db->query($sql);
		if($show_messages->num_rows() == 0){
			return 'no_result_found';
		}else{
			$smgs = array();
			foreach($show_messages->result_array() as $sm){
				$exp_names = explode("|",$sm['name']);
				$sm['sname'] 			=	$exp_names[0] . " " . $exp_names[1];
				$sm['user_photo']		=	$exp_names[2];
				$sm['date_added_human'] = 	timespan($sm['date_added'],time());
				$sm['user_dept']		= 	strtolower($this->commconfig->show_department($exp_names[3]));

				$smgs[] = $sm;
			}
			return $smgs;
		}
	}

	public function count_trash_msgs($user_id){
		$sql = "select count(msg_conv_id) as count_unread 
				from 
					message_conversation where receiver_id = '$user_id'
				and 
					msg_status = '2' LIMIT 1";
		$total_msg_count = $this->db->query($sql);
		if($total_msg_count->num_rows() == 0){
			return 0;
		}else{
			$getot = $total_msg_count->result_array();
			return $getot[0]['count_unread'];

		}
	}

	public function check_user_folder($folder_id,$user_id){
		$sql = "select folder_name from message_folders where msg_folder_id = '$folder_id' and user_id = '$user_id'
				and folder_status = '1' LIMIT 1";
		$check_fol = $this->db->query($sql);
		if($check_fol->num_rows() == 0){
			return 'inbox';
		}else{

			$sf = $check_fol->result_array();
			return $sf[0]['folder_name'];
		}
	}

	public function read_message_content($message_id,$signature,$user_id){


		$sql = "select msg_conv_id, msg_compose_id,sender_id,receiver_id,date_added, 
				msg_conv_sig, msg_status, folder_id
				from 
					message_conversation 
				where 
					msg_conv_id = '$message_id' 
				and 
					receiver_id = '$user_id'
				and	
					msg_conv_sig = '$signature' 
				and
					msg_status != '3' 
				LIMIT 1";
				$check_message = $this->db->query($sql);
				if($check_message->num_rows() == 0){
					return 0;
				}else{
					$get_comp = $check_message->result_array();

					if($get_comp[0]['folder_id'] == 0){
						$get_comp[0]['folder_name'] = 'inbox';
					}else{
						$get_comp[0]['folder_name'] = $this->check_user_folder($get_comp[0]['folder_id'],$get_comp[0]['receiver_id']);
					}
					/* if message status = 0; update it to read*/
					if($get_comp[0]['msg_status'] == 0){
						$data = array('msg_status'=> 1, 'date_read'=>time());
						$this->db->simple_query("SET NAMES 'utf-8'");  
						$data_where = array('msg_conv_id'=> $message_id, 'msg_conv_sig' => $signature); 		
						$this->db->set($data);
						$this->db->update('message_conversation', $data, $data_where);
					}

					/* check sender name */
					$sender = $this->commconfig->check_user_name_for_msg($get_comp[0]['sender_id']);
					$get_comp[0]['sender_name'] = $sender;

					/* check sender message content*/
					$sql = "select subject,details,is_reply,date_sent,msg_type,cc_users,send_to_ids 
							from 
								message_compose 
							where 
								user_id = '{$get_comp[0]['sender_id']}'
							and
								compose_status = '1'
							and
								msg_compose_id = '{$get_comp[0]['msg_compose_id']}'
							LIMIT 1";
					$check_mc = $this->db->query($sql);
					if($check_mc->num_rows() == 0){
						return 0;
					}else{
						$datestring = "%h:%i %a, %M %d %Y";
						$show_mc = $check_mc->result_array();
						$show_mc[0]['date_sent_human'] = mdate($datestring, $show_mc[0]['date_sent']);
						$get_comp[0]['message_content'] = $show_mc[0];
					}

					/* check attachments*/
					$sql = "select attachment_id,att_name,file_size,upload_type,att_path from message_attachment
							where 
								msg_compose_id='{$get_comp[0]['msg_compose_id']}' 
							and 
								user_id = '{$get_comp[0]['sender_id']}'
							order by attachment_id desc";
					$check_attachment = $this->db->query($sql);
					if($check_attachment->num_rows() == 0){
						$get_comp[0]['attachment_count'] = 0;
					}else{
						//HumanReadableFilesize
						$get_comp[0]['attachment_count'] = $check_attachment->num_rows();
						$show_attachment = array();
						foreach($check_attachment->result_array() as $ca){
							//$ca['file_size'] = $this->userconfig->HumanReadableFilesize($ca['file_size']);
							$ca['file_size'] = byte_format($ca['file_size']);
							$show_attachment[] = $ca;
						}
						$get_comp[0]['show_attachment'] = $show_attachment;
					}
					return $get_comp[0];
				}			
	}


	function check_folder_name($folder_id){
		$sql = "select folder_name from message_folders where msg_folder_id = '$folder_id' and folder_status = '1' LIMIT 1";
		$check_folder = $this->db->query($sql);
		if($check_folder->num_rows() == 0){
			return 'inbox';
		}else{
			$check_fol = $check_folder->result_array();
			return $check_fol[0]['folder_name'];
		}
	}
	function checkuser_folder($user_id){
		$sql = "select folder_name,msg_folder_id, (select count(msg_conv_id) from message_conversation where folder_id=message_folders.msg_folder_id and receiver_id=message_folders.user_id LIMIT 1) as message_count from message_folders where user_id = '$user_id'
		and folder_status = '1' order by orderby asc, folder_name asc";
		$check_folders = $this->db->query($sql);
		if($check_folders->num_rows() == 0){
			return 'no_folder_found';
		}else{
			$show_folders = array();
			foreach($check_folders->result_array() as $cf){
				$show_folders[] = $cf;
			}
			return $show_folders;
		}
	}

	function dd_folders($user_id){

		$show_dd_folder = array();
		$show_dd_folder[0] = "Public Folder (inbox)";

		$sql = "select folder_name,msg_folder_id from message_folders where user_id = '$user_id'
		and folder_status = '1' order by orderby asc, folder_name asc";
		$check_dd_folder = $this->db->query($sql);
		if($check_dd_folder->num_rows() > 0){


			foreach($check_dd_folder->result_array() as $cdd){
				$show_dd_folder[$cdd['msg_folder_id']] = $cdd['folder_name'];
			}
			

		}
		return $show_dd_folder;
	}

	/* Meetings model */
	function check_unread_meetings($user_id){
		$sql = "select count(mu.meeting_user_id) as count 
		from
			meetings_user as mu
		JOIN
			meetings as m
		on
		mu.meeting_id = m.meeting_id
		where 
			mu.user_id = '$user_id' 
		and 
			mu.is_host = '0' 
		and 
			mu.user_action = '0'
		and 
		m.meeting_status = '1' LIMIT 1";
		$count_met = $this->db->query($sql);
		if($count_met->num_rows() == 0){
			return 0;
		}else{
			$get_count = $count_met->result_array();

			return $get_count[0]['count'];
		}
	}

	function get_request_meetings($user_id, $user_action, $show_before_months = 0){
		if($show_before_months != 0){
			$date_between_from 	= date("Y-m-d",mktime(0,0,0,date('m') - $show_before_months, date('d'),date('Y')));
			$date_between_after = date("Y-m-d",mktime(0,0,0,date('m') + $show_before_months, date('d'),date('Y')));
			$between_query = "and (m.date_from BETWEEN '$date_between_from' and '$date_between_after')";
		}
		$sql = "select mu.meeting_user_id,mu.meeting_id,m.user_id as host_id,m.subject,m.is_allday,
			m.date_from, m.date_from_time, m.date_to,m.date_to_time,m.meeting_icon,m.meeting_color,mu.user_action,m.date_added,
			(select firstname ||'|'|| lastname ||'|'|| user_photo ||'|'|| department ||'|'|| id_number from users 
				where 
					user_id = m.user_id LIMIT 1) as name  
		from
			meetings_user as mu
		JOIN
			meetings as m
		on
		mu.meeting_id = m.meeting_id
		where 
			mu.user_id = '$user_id' 
		
		and 
			mu.user_action = '$user_action'
		and 
		m.meeting_status = '1' $between_query
		order by mu.user_action asc, mu.meeting_id desc";
/*
and 
			mu.is_host = '0' 
*/
		$count_met = $this->db->query($sql);
		if($count_met->num_rows() == 0){
			return 'no_result_found';
		}else{
			$show_result = array();
			foreach($count_met->result_array() as $cm){

				$cm['date_from'] = date("F d, Y h:i a", strtotime($cm['date_from'] . " " . $cm['date_from_time']));
				$cm['date_to'] = date("F d, Y h:i a", strtotime($cm['date_to'] . " " . $cm['date_to_time']));

				$exp_names = explode("|",$cm['name']);
				$cm['sname'] 			=	$exp_names[0] . " " . $exp_names[1];
				$cm['user_photo']		=	$exp_names[2];
				$cm['user_dept']		= 	strtolower($this->commconfig->show_department($exp_names[3]));	
				$cm['date_added_human'] = 	timespan($cm['date_added'],time());

				$show_result[] = $cm;
			}

			return $show_result;
		}		
	}

	function check_meeting_details($meeting_id){
		$sql = "select m.user_id,m.subject,m.content,m.date_from, m.date_from_time, m.date_to,m.date_to_time,m.date_added,
			(select firstname ||'|'|| lastname ||'|'|| user_photo ||'|'|| department ||'|'|| id_number from users 
				where 
					user_id = m.user_id LIMIT 1) as name  
		from
			meetings as m
		where 
			m.meeting_id = '$meeting_id' 
		and 
			m.meeting_status = '1' LIMIT 1";

		$count_met = $this->db->query($sql);
		if($count_met->num_rows() == 0){
			return 'no_result_found';
		}else{
			$show_result = array();
			foreach($count_met->result_array() as $cm){

				$cm['date_from'] = date("F d, Y h:i a", strtotime($cm['date_from'] . " " . $cm['date_from_time']));
				$cm['date_to'] = date("F d, Y h:i a", strtotime($cm['date_to'] . " " . $cm['date_to_time']));

				$exp_names = explode("|",$cm['name']);
				$cm['sname'] 			=	$exp_names[0] . " " . $exp_names[1];
				$cm['user_photo']		=	$exp_names[2];
				$cm['user_dept']		= 	ucfirst(strtolower($this->commconfig->show_department($exp_names[3])));	
				$cm['date_added_human'] = 	timespan($cm['date_added'],time());

				/* check meeting attendies*/

				$sql = "select meeting_user_id,user_id,user_action,reasons,is_host,
				(select firstname ||'|'|| lastname ||'|'|| user_photo ||'|'|| department ||'|'|| id_number from users  
				where 
				user_id = meetings_user.user_id LIMIT 1) as name  from meetings_user
						where
							meeting_id = '$meeting_id' order by meeting_user_id asc";

				$check_meet = $this->db->query($sql);
				if($check_meet->num_rows() == 0){
					$cm['total_invites'] = 0;
					$cm['attendees'] = 0;
				}else{
					$atten = array();
					$cm['total_invites'] = $check_meet->num_rows();
					foreach($check_meet->result_array() as $sm){

						$exp_names = explode("|",$sm['name']);
						$sm['sname'] 			=	ucfirst($exp_names[0]) . " " . ucfirst($exp_names[1]);
						$sm['user_photo']		=	$exp_names[2];
						$sm['user_dept']		= 	ucfirst(strtolower($this->commconfig->show_department($exp_names[3])));
						$sm['id_number']		=	$exp_names[4];				
						$atten[] = $sm;
					}
					$cm['attendees'] = $atten;
				}				
				$show_result[] = $cm;
			}

			return $show_result[0];
		}			
	}

	/* ARCHIVES */
	public function count_archive_msgs($user_id,$folder_id){
		$sql = "select count(msg_conv_id) as count_unread 
				from 
					message_archive where receiver_id = '$user_id'
				and 
					msg_status in ('0','1')
				and	
					folder_id = '$folder_id' LIMIT 1";
		$total_msg_count = $this->db->query($sql);
		if($total_msg_count->num_rows() == 0){
			return 0;
		}else{
			$getot = $total_msg_count->result_array();
			return $getot[0]['count_unread'];

		}
	}	

	public function show_archive_msgs($user_id,$folder_id,$limit,$paging_page){
		$sql = "select folder_id,msg_conv_id,msg_compose_id,sender_id,receiver_id, date_added,msg_conv_sig,msg_status,
(select subject from message_compose where msg_compose_id = message_archive.msg_compose_id LIMIT 1) as subject,
(select LEFT(regexp_replace(details, E'<.*?>', '', 'g' ),100) from message_compose where msg_compose_id = message_archive.msg_compose_id LIMIT 1) as details,
(select firstname ||'|'|| lastname ||'|'|| user_photo ||'|'|| department ||'|'|| id_number from users  
	where 
		user_id = message_archive.sender_id LIMIT 1) as name 
from 
	message_archive where receiver_id = '$user_id' and msg_status in ('0','1')
 order by msg_status asc, msg_conv_id desc limit $limit offset $paging_page";
				
		$show_messages = $this->db->query($sql);
		if($show_messages->num_rows() == 0){
			return 'no_result_found';
		}else{
			$smgs = array();
			foreach($show_messages->result_array() as $sm){

				if($sm['folder_id'] == 0){
					$sm['folder_name'] = "inbox";
				}else{
					$sm['folder_name'] = $this->msg_model->check_folder_name($cs['folder_id']);
				}
									
				$sm['details'] = str_replace(array("<br>","<p>")," ",strip_tags($sm['details']));
				$exp_names = explode("|",$sm['name']);
				$sm['sname'] 			=	$exp_names[0] . " " . $exp_names[1];
				$sm['user_photo']		=	$exp_names[2];
				$sm['date_added_human'] = 	timespan($sm['date_added'],time());
				$sm['user_dept']		= 	strtolower($this->commconfig->show_department($exp_names[3]));
				$sm['id_number']		=	$exp_names[4];

				$smgs[] = $sm;
			}
			return $smgs;
		}
	}	

	public function read_archive_message_content($message_id,$signature,$user_id){

		$sql = "select msg_conv_id, msg_compose_id,sender_id,receiver_id,date_added, 
				msg_conv_sig, msg_status, folder_id
				from 
					message_archive
				where 
					msg_conv_id = '$message_id' 
				and 
					receiver_id = '$user_id'
				and	
					msg_conv_sig = '$signature' 
				and
					msg_status != '3' 
				LIMIT 1";
				$check_message = $this->db->query($sql);
				if($check_message->num_rows() == 0){
					return 0;
				}else{
					$get_comp = $check_message->result_array();

					if($get_comp[0]['folder_id'] == 0){
						$get_comp[0]['folder_name'] = 'inbox';
					}else{
						$get_comp[0]['folder_name'] = $this->check_user_folder($get_comp[0]['folder_id'],$get_comp[0]['receiver_id']);
					}
					/* if message status = 0; update it to read*/
					if($get_comp[0]['msg_status'] == 0){
						$data = array('msg_status'=> 1, 'date_read'=>time());
						$this->db->simple_query("SET NAMES 'utf-8'");  
						$data_where = array('msg_conv_id'=> $message_id, 'msg_conv_sig' => $signature); 		
						$this->db->set($data);
						$this->db->update('message_conversation', $data, $data_where);
					}

					/* check sender name */
					$sender = $this->commconfig->check_user_name_for_msg($get_comp[0]['sender_id']);
					$get_comp[0]['sender_name'] = $sender;

					/* check sender message content*/
					$sql = "select subject,details,is_reply,date_sent,msg_type,cc_users,send_to_ids 
							from 
								message_compose 
							where 
								user_id = '{$get_comp[0]['sender_id']}'
							and
								compose_status = '1'
							and
								msg_compose_id = '{$get_comp[0]['msg_compose_id']}'
							LIMIT 1";
					$check_mc = $this->db->query($sql);
					if($check_mc->num_rows() == 0){
						return 0;
					}else{
						$datestring = "%h:%i %a, %M %d %Y";
						$show_mc = $check_mc->result_array();
						$show_mc[0]['date_sent_human'] = mdate($datestring, $show_mc[0]['date_sent']);
						$get_comp[0]['message_content'] = $show_mc[0];
					}

					/* check attachments*/
					$sql = "select attachment_id,att_name,file_size,upload_type,att_path from message_attachment
							where 
								msg_compose_id='{$get_comp[0]['msg_compose_id']}' 
							and 
								user_id = '{$get_comp[0]['sender_id']}'
							order by attachment_id desc";
					$check_attachment = $this->db->query($sql);
					if($check_attachment->num_rows() == 0){
						$get_comp[0]['attachment_count'] = 0;
					}else{
						//HumanReadableFilesize
						$get_comp[0]['attachment_count'] = $check_attachment->num_rows();
						$show_attachment = array();
						foreach($check_attachment->result_array() as $ca){
							$ca['file_size'] = $this->userconfig->HumanReadableFilesize($ca['file_size']);
							$show_attachment[] = $ca;
						}
						$get_comp[0]['show_attachment'] = $show_attachment;
					}
					return $get_comp[0];
				}			
	}	
}