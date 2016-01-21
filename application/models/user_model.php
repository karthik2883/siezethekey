<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {
    
    public function __construct(){
        parent :: __construct();
    }
    
    /**
     *get coonditional data from table
     *@post
     */
    
    public function get_where($table,$data){
        $table_name = get_table_name($table);
        $this->db->select('*');
        $this->db->from($table_name);
        $this->db->where($data);
       $data =  $this->db->get()->row_array();
      
       return $data;
    }
    
     public function get_whereArray($table,$data){
        $table_name = get_table_name($table);
        $this->db->select('*');
        $this->db->from($table_name);
        $this->db->where($data);
       $data =  $this->db->get()->result_array();
       return $data;
    }
    
    /**
     *get count of table
     *@post
     */
     public function get_count($table,$data){
        $table_name = get_table_name($table);
        $this->db->select('count(*) as count');
        $this->db->from($table_name);
        $this->db->where($data);
       $data =  $this->db->get()->row_array();
      
       return $data['count'];
    }
    
    /**
     *get data from table
     *@post
     */
    
    public function get($table_name){
           $table_name = get_table_name($table_name);
        $this->db->select('*');
        $this->db->from($table_name);
        $data =  $this->db->get()->result_array();
        return $data;
    }
    
    /**
     *delete data with condtion
     *@post
     */
    public function dbDelete($table,$where){
        $table_name = get_table_name($table);
        $this->db->delete($table_name,$where);
        if($this->db->affected_rows()){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     *update record by some condtion
     *@post
     */
    public function dbUpdate($table,$data,$where){
        $table_name = get_table_name($table);
        $this->db->update($table_name,$data,$where);
        if($this->db->affected_rows()){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     *get venue list
     *@post
     */
    public function get_venue($table,$where)
    {
        $venue_table = get_table_name('venue');
         $this->db->select('*');
        $this->db->from($venue_table);
        $this->db->where($where);
        $data =  $this->db->get()->result_array();
     
        return $data;
    }
    
    
    public function tokenGenerate($number){
        $token_table = get_table_name('token');
        $array = array('tokenCreatedDate'=>DATE,
                       'number'=>$number,
                       'token'=>TOKEN);
        $this->db->insert($token_table,$array);
        $this->twilio_sms_library->send_sms($number,'please enter token:'.TOKEN);//918871923988
      return   response_success($array,'SUCCESS',"");
    }
    
    
    public function checkToken($number,$token){
        $token_table = get_table_name('token');
        $sql = "SELECT * FROM ".$token_table
                ." WHERE number=? AND token=? and status=1";
        $data = $this->db->query($sql,array($number,$token))->row_array();
        if(!empty($data)){
            $this->db->update($token_table,array('status'=>0),array('number'=>$number,'token'=>$token));
            return true;
        }else{
            return false;
        }
     }
     
     
     public function userRegister($number,$deviceid){
        $user_table = get_table_name('user');
        $data = array('createdDate'=>DATE,
                      'mobile'=>$number,
                      'deviceId'=>$deviceid);
       $userCheck = $this->get_where('user',array('mobile'=>$number));
        
        if(!empty($userCheck)){
            return false;
        }else{
            $this->db->insert($user_table,$data);
            return 1;
        }
      
        
     }
     
     
     /*user login
      *@post
      *
      */
     public function userLogin($number){
        $user_table = get_table_name('user');
        $where = array('mobile'=>$number,'status'=>1);
        $userdata = $this->get_where('user',$where);
      //  print_r($userdata);die;
        if(!empty($userdata)){
            $this->db->update($user_table,array('token'=>md5($number)),$where);
          // return $this->db->last_query();
            $data['user_info'] = $this->get_where('user',$where);
            $data['user_contacts'] = $this->get_whereArray('contacts',array('contactUserId'=>$userdata['userId']));
            return $data;
        }else{
            return false;
        }
                                           
        
     }
     
     
     /*
      *add own contact 
      *@post
      */
     public function addUser($contactUserId,$number,$name,$image_data){
        $user_table = get_table_name('contacts');
        $data = array('createdDate'=>DATE,'contactUserId'=>$contactUserId,'contactNumber'=>$number,'contactName'=>$name,'contactImage'=>$image_data);
        $contact = $this->get_where('contacts',array('contactUserId'=>$contactUserId,'contactNumber'=>$number));
        $count = $this->get_count('contacts',array('contactUserId'=>$contactUserId));
        if(empty($contact)){
            if($count<CONTACT_SIZE){
            $this->db->insert($user_table,$data);
            return $data;
            }else{
            return  "EXCEED";
            }
        }else{
            return false;
        }
     }
     
     
     
     
     /*
	 *send alert for other user
	 *@post
	 */
	public function sendAlert(){
        $user_table = get_table_name("user");
        $contact_table = get_table_name('contacts');
        $number = $this->input->post('number');
        $cityName = $this->input->post('cityName');
        $venueName = $this->input->post('venueName');
        $is_friendsmessage = $this->input->post('is_friendsmessage');
        $is_remindermessage = $this->input->post('is_remindermessage');
        $is_uberlinkmessage = $this->input->post('is_uberlinkmessage');
        $sql = "SELECT userId,contactName,contactNumber
                FROM ".$user_table."
                JOIN ".$contact_table." ON contactUserId=userId
                WHERE mobile=?";
                $data = $this->db->query($sql,$number)->result_array();
                $msg['status'] = false;
                
                /*send message to his added contacts*/
                if($is_friendsmessage){
                if(!empty($data)){
                    foreach($data as $val){
                        $msg = "";
                        $msg .= $this->lang->line('USER_ALERT_MESSAGE');
                        if($cityName){
                        $mss .= " person current location is ".$venueName.", ".$cityName;
                        }
                        $this->twilio_sms_library->send_sms($val['contactNumber'],$msg);
                    }
                        $msg = array('message sent to these contact'=>$data,'status'=>true);
                    }
                 }
            /*send message to person*/
            if($is_remindermessage){
              $sql = "SELECT COUNT(*) count, deviceId
                    FROM ".$user_table." 
                    WHERE mobile = ?";
                $user_info = $this->db->query($sql,$number)->row_array();
                if($user_info['count']>0){
                  $this->notification_model->sendPushOnServer($user_info['deviceId'],$this->lang->line('REMINDER_PERSON'));
                }else{
                    
                   $this->twilio_sms_library->send_sms($number,$this->lang->line('REMINDER_PERSON'));
                }
                $msg['status'] = true;
                 
            }
            
            /*send uber link to person*/
            if($is_uberlinkmessage){
                 $this->twilio_sms_library->send_sms($number,$this->lang->line('UBER_LINK'));
                 $msg['status'] = true;
            }
                
              return $msg;
              
    }
    
    
    
    /***
     *send alert to venue manager
     *@post
     */
    public function alertVenueManager($mgrContact,$alertText){
         $this->twilio_sms_library->send_sms($mgrContact,$alertText);
         return array("data"=>"Alert sent to vanue manager");
    }
    
    /*
	 *delete contacts
	 *@post
	 */
	public function contactDelete(){
        $contactid = $this->input->post('contactId');
        $data = $this->dbDelete('contacts',array('contactId'=>$contactid));
        if($data){
            return true;
        }else{
            return false;
        }
    }
    
    
    	/**
	 *match contact from stk db is registerd or not
	 *@post
	 */
	public function matchContact(){
        
        $user_table = get_table_name('user');
        $contacts = $this->input->post('contacts');
        $contact = str_replace(",","|",$contacts);
        $sql = "SELECT mobile
                FROM ".$user_table."
                WHERE mobile  REGEXP(?)";
        $data = $this->db->query($sql,$contact)->result_array();
        return $data;
    }
		
    
    	/**
	 *deactivate account by userid
	 *@post
	 */
	public function deactivateAccount(){
        $userid = $this->input->post('userId');
        $data = $this->dbUpdate('user',array('status'=>0),array('userId'=>$userid));
        if($data){
            return true;
        }else{
            return false;
        }
        
    }
    
    
}


?>