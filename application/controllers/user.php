<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';
class User extends REST_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->helper('responseMessages');
		$this->load->helper('common');
		$this->load->library('twilio_sms_library');
		$this->load->model('user_model');
		$this->load->model('image_model');
		
		
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
		//echo '<pre>';print_r($_SERVER);
	}
	
	/***
	*generate token and send to user
	*@post
	**********/
	public function tokenGenerate_post(){
		$this->form_validation->set_rules('number','Contact Number','required|max_length[11]|min_length[10]|numeric');
		if($this->form_validation->run()==TRUE){
			$number = $this->post('number');
			$data = $this->user_model->tokenGenerate($number);
			$this->response($data);
		}else{
			$this->response(response_fail('FAILED', validation_errors()));
		}
		
		
		
	}
	
	
	
	
	
	
	/****
	*verify token and register user
	*@post
	****/
	
	public function checkToken_post(){
		$this->form_validation->set_rules('number','Contact Number','required|max_length[11]|min_length[10]|numeric');
		$this->form_validation->set_rules('deviceid','Device Id','required');
		//$this->form_validation->set_rules('profile_pic','Profile Pic','required');
		$this->form_validation->set_rules('token','Token','required|exact_length[6]|numeric');
		if($this->form_validation->run()==TRUE){
			$number = $this->post('number');
			$token =  $this->post('token');
			$deviceid = $this->post('deviceid');
			//$image_data = $this->image_model->do_upload('profile_pic');
			if($image_data == false){
			//	$this->response(response_fail('FAILED', "Image have not uploaded, Please try Again"));
			}
			$data = $this->user_model->checkToken($number,$token);
			if($data == true){
				$user_data = $this->user_model->userRegister($number,$deviceid,$image_data);
				
				if(!empty($user_data)){
				$this->response(response_success(array("user"=>"REGISTERED"),"SUCCESS", "NULL"));
				}else{
					$this->response(response_fail('FAILED', "User Already Registered"));
				}
			}else{
				$this->response(response_fail('FAILED', "Invalid Token"));	
			}
			
		}else{
			$this->response(response_fail('FAILED', validation_errors()));
		}
		
		
	}
	
	
	/******
	 *user login
	 *@post
	 *
	 */
	
	public function userLogin_post(){
		 $this->form_validation->set_rules('number','Contact Number','required|max_length[11]|min_length[10]|numeric');
		 $this->form_validation->set_rules('deviceid','Device Id','required');
		
		if($this->form_validation->run()==TRUE){
			$number = $this->post('number');
			$deviceid = $this->post('deviceid');
				$user_data = $this->user_model->userLogin($number,$deviceid);
				
				if($user_data){
						$this->response(response_success($user_data,"SUCCESS", "NULL"));
				}else{
					$this->response(response_fail('FAILED',"Authentication faled"));
				}
		}else{
			$this->response(response_fail('FAILED', validation_errors()));
		}
	}
	
	/***
	 *
	 *add own contact 
	 *@post
	 */
	
	public function contactAdd_post(){
		
		  $this->form_validation->set_rules('contactUserId','UserId','required');
		 $this->form_validation->set_rules('number','Contact Number','required|max_length[11]|min_length[10]|numeric');
		 $this->form_validation->set_rules('name','Name','required');
		
		if($this->form_validation->run()==TRUE){
			$contactUserId = $this->post('contactUserId');
			$number = $this->post('number');
			$name = $this->post('name');
			$image_data = $this->image_model->do_upload('contact_pic');
			if($image_data == false){
				$this->response(response_fail('FAILED', "Image have not uploaded, Please try Again"));
			}
			$user_data = $this->user_model->addUser($contactUserId,$number,$name,$image_data);
			if(!empty($user_data)){
			$this->response(response_success($user_data,"SUCCESS", "NULL"));
			}else{
				$this->response(response_fail('FAILED','Contact already exist!' ));
			}
		}else{
			$this->response(response_fail('FAILED', validation_errors()));
		}
		
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */