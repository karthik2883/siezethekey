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
    
    public function tokenGenerate($number){
        $token_table = get_table_name('token');
        $array = array('tokenCreatedDate'=>DATE,
                       'number'=>$number,
                       'token'=>TOKEN);
        $this->db->insert($token_table,$array);
        $this->twilio_sms_library->send_sms('+91'.$number,'please enter token:'.TOKEN);//918871923988
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
        $where = array('mobile'=>$number);
        $userdata = $this->get_where('user',$where);
        if(!empty($userdata)){
            $this->db->update($user_table,array('token'=>md5($number)),$where);
          //  return $this->db->last_query();
            return $this->get_where('user',$where);
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
	public function sendAlert($number,$lat,$long){
        $user_table = get_table_name("user");
        $contact_table = get_table_name('contacts');
       $sql = "SELECT userId,contactName,contactNumber
                FROM ".$user_table."
                JOIN ".$contact_table." ON contactUserId=userId
                WHERE mobile=?";
                $data = $this->db->query($sql,$number)->result_array();
                if(!empty($data)){
                    foreach($data as $val){
                        $msg = $this->lang->line('USER_ALERT_MESSAGE'). " to find person visit : http://maps.google.com/maps?q=".$lat.",".$long;
                        $this->twilio_sms_library->send_sms('+91'.$val['contactNumber'],$msg);
                    }
                    return $data;
                 }else{
                    return false;
                 }
              
    }
    
    
    
}


?>