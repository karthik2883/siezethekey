<?php

class Image_model extends CI_Model {
	function do_upload($pic_type) {
		// $this->load->model();
		$uploadPath = $width = $height = '';
		$this->load->library('image_lib');
		//creating required folders here----------
		$folder = $pic_type;
		$this->makedirs($folder);
		$this->makedirs($folder . '/original/');
		$this->makedirs($folder . '/small');
		$uploadPath = realpath(APPPATH . '../uploads/' . $folder . '/original/');
		$width = 250;
		$height = 250;
		
		$config['upload_path'] = $uploadPath;
		$config['allowed_types'] = IMAGE_TYPES;
		$config['max_size']	= MAX_SIZE;
		$config['encrypt_name'] = true;
		// $config['overwrite']= true;
		// $config['file_name']= "image_".$obj['name']."_".$obj['id'];
		
		$this->load->library('upload', $config);

		if(!$this->upload->do_upload($pic_type)){
			$error = $this->upload->display_errors();
			 print_r($error);die;
		} else {
			$image_data = $this->upload->data(); //upload the image
			$resize['source_image'] = $image_data['full_path'];
			$resize['new_image'] = realpath(APPPATH . '../uploads/'.$folder.'/small/');
			$resize['maintain_ratio'] = true;
			$resize['width'] = $width;
			$resize['height'] = $height;

			//send resize array to image_lib's  initialize function
			$this->image_lib->initialize($resize);
			$this->image_lib->resize();
			$this->image_lib->clear();
			
			return $image_data['file_name'];
		}
		
		return FALSE;
	}

	function do_upload_video($folder){
		$this->makedirs($folder);
		$uploadPath = realpath(APPPATH . '../uploads/' . $folder);	
		
		$config = array(
			'allowed_types'     => VIDEO_TYPES, //only accept these file types
			'max_size'          => MAX_SIZE, //6MB max
			'upload_path'       => $uploadPath, //upload directory
			'encrypt_name'      => TRUE,
			'file_name'         => $_FILES[ITEM_VIDEO]['name']
		 );	
	 	 $data['error'] = "";
	    $this->load->library('upload', $config);
		
		if ( ! $this->upload->do_upload(ITEM_VIDEO)) {		
			$error = $this->upload->display_errors();
			print_r($error);die;
			// return $data;
		}else{ 		
			$video_data = $this->upload->data(); //upload the video
			
			$video_url = $uploadPath."/".$video_data['file_name'];	
			$path_parts = pathinfo($video_url);
			$img = $uploadPath."/".$path_parts['filename'].".jpg";	
			$thumb_path = $uploadPath."/large_".$path_parts['filename'].".jpg";		
			shell_exec("ffmpeg -i ".$video_url."  -y -an -sameq -vcodec mjpeg -f mjpeg -s 640x640 $thumb_path");	
				
			$config = array(
				'source_image'      => $thumb_path, //path to the uploaded image
				'new_image'         => $img, //path to
				'maintain_ratio'    => true,
				'width'             => 214,
				'height'            => 214
			);	
			$this->load->library('image_lib'); 
			//this is the magic line that enables you generate multiple thumbnails				
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			$this->image_lib->clear();		
			return $video_data['file_name'];
		 }
		 return false;
	}
	
	function makedirs($folder='', $mode=DIR_WRITE_MODE, $defaultFolder='uploads') {
		if(!@is_dir(FCPATH . $defaultFolder)){
			mkdir(FCPATH . $defaultFolder, $mode);
		}
		
		if(!empty($folder)){
			if(!@is_dir(FCPATH . $defaultFolder . '/' . $folder)){
				mkdir(FCPATH . $defaultFolder . '/' . $folder, $mode);
			}
		}
			
	}
	
	
	
}

?>