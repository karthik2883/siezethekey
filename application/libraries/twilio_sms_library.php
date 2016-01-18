

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
* @author Ishan Bhuta ishan.bhuta@northout.com
*/
	
	class twilio_sms_library {
		
		/*parameters requires
			$smsRecieverNo //who recieves this number
			$textMessage //message text
		*/
		public function send_sms($smsRecieverNo, $textMessage) {
			
			/*getting data from config file*/
			$configFileUrl = APPPATH.'/config/twilio_config.php';
			require($configFileUrl);

			/*including twilio third party sdk*/ 
			$url = APPPATH.'libraries/twilio_sms/services/twilio.php';
		   	
			require_once($url);
			$account_sid = $config['account_sid']; // Your Twilio account sid
			$auth_token = $config['auth_token']; // Your Twilio auth token
			$client = new Services_Twilio($account_sid, $auth_token);
			/*
						$http = new Services_Twilio_TinyHttp(
				'https://api.twilio.com',
				array('curlopts' => array(
					CURLOPT_SSL_VERIFYPEER => true,
					CURLOPT_SSL_VERIFYHOST => 2,
				))
			);

		$client = new Services_Twilio($account_sid, $auth_token, "2010-04-01", $http);
			*/
			try {
				$message = $client->account->messages->sendMessage($config['registered_no'], $smsRecieverNo, $textMessage);
				// $message = $message->sid;
				$responseArray = array(
					'type'=>"Success",
					'message'=>"sms send successfully",
					'responseCode'=>200,
				);
			} catch (Services_Twilio_RestException $e) {
			    $message = $e->getMessage();
			    $responseArray = array(
					'type'=>"Error",
					'message'=>$message,
					'responseCode'=>203,
				);	
			}
			return $responseArray;
		}
	}
	
 ?>