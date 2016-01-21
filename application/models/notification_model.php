<?php
/**
 * Notification_model Class extends CI_Model
 *
 * @package    Admin
 * @User   Adminnistrator
 * @author     Ishan <ishan.bhuta@northout.com>
 */

class Notification_model extends CI_Model {
	function Notification_model() {
		parent::__construct();		
	}
	
	/**
	* Send push notification
	*/
	function sendPushOnServer($device_token, $alert, $badge = null)
	{
        // Put private key's passphrase here:
        $passphrase = "123456";
        $liveMode = TRUE;
		$url = ($liveMode)?'ssl://gateway.push.apple.com:2195': 'ssl://gateway.sandbox.push.apple.com:2195';
        // Put your alert message here:
        //$message = 'Driver accept your request He will come soon!';
       
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert','pem/STKPEM.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		
        // Open a connection to the APNS server
        $fp = stream_socket_client(
        $url, $err,
        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
       
        if (!$fp){
             echo "Failed to connect: $err $errstr" . PHP_EOL;		die;
        }else{

			// Create the payload body
			$body['aps'] = array('badge' =>$badge, 'alert' => (string)$alert,'sound' => "default");
		   
			// Encode the payload as JSON
			$payload = json_encode($body);
		   
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $device_token) . pack('n', strlen($payload)) . $payload;
		  // echo $device_token;die;
			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));
			
			fclose($fp);
		}	
	}
	
	

	
	
}
//End of class Notification_model
?>