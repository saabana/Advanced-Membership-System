<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*    Last Edited by LEWIS on 32/01/2013                          *
*****************************************************************/

function strip_zeros_from_date( $marked_string="" ) {
  $no_zeros = str_replace('*0', '', $marked_string);
  $cleaned_string = str_replace('*', '', $no_zeros);
  return $cleaned_string;
}

function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
}

function output_message($message="") {
  if (!empty($message)) { 
    return "<p class=\"message\">{$message}</p>";
  } else {
    return "";
  }
}

function generate_id($length = 6) {
	$id = '';
	for ($i=0;$i<$length;$i++){
		$id .= rand(1, 9);
	}
	return $id;
}

function convert_boolean($boolean) {
	if($boolean == 0) {
		$end = "<span class=\"label label-danger\">No</span>";
	} else if($boolean == 1) {
		$end = "<span class=\"label label-default\">Yes</span>";
	}
	return $end;
}

function convert_boo($boolean) {
	if($boolean == 0) {
		$end = "No";
	} else if($boolean == 1) {
		$end = "Yes";
	}
	return $end;
}

function convert_boolean_sus($boolean) {
	if($boolean == 0) {
		$end = "<span class=\"label label-info\">No</span>";
	} else if($boolean == 1) {
		$end = "<span class=\"label label-danger\">Yes</span>";
	}
	return $end;
}

function convert_boolean_full($boolean) {
	if($boolean == 0) {
		$end = "Deactivated";
	} else if($boolean == 1) {
		$end = "Activated";
	}
	return $end;
}

function convert_token_status($enum) {
	if($enum == 'c') {
		$end = "Credited";
	} else if($enum == 'd') {
		$end = "Debited";
	}
	return $end;
}

function convert_user_level($level) {
	global $database;
	
	$sql = "SELECT * FROM user_levels WHERE level_id = '{$level}'";
	$query = $database->query($sql);
	$row = $database->fetch_array($query);
	return $row['level_name'];
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%A %d of %B %Y at %I:%M %p", $unixdatetime);
}

date('l jS \of F Y \a\t h:i:s A');

function date_to_text($date="") {
  $unixdatetime = strtotime($date);
  return strftime("%d %B %Y", $unixdatetime);
}

function date_text_month($date="") {
  $unixdatetime = strtotime($date);
  return strftime("%B", $unixdatetime);
}

function protect($users_level, $group_id, $redirect="index.php") {
	$user_levels = explode(",", $users_level);
	$groups = explode(",", $group_id);
	$flag = false;
	foreach($user_levels as $level){
		if(in_array($level, $groups)) {
			$flag = true;
			break;
		}
	}
	if($flag == false){
		redirect_to($redirect);
	}
}

function partial_protect($users_level, $group_id, $redirect="index.php") {
	$user_levels = explode(",", $users_level);
	$groups = explode(",", $group_id);
	$flag = false;
	foreach($user_levels as $level){
		if(in_array($level, $groups)) {
			$flag = true;
			break;
		}
	}
	return $flag;
}

// Encrypt Password	
function create_salt($size=25){
	$aZ09 = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9)); 
	$database_salt_gen = ''; 
	for($c=0;$c < $size;$c++) { 
		$database_salt_gen .= $aZ09[mt_rand(0,count($aZ09)-1)]; 
	}
	return $database_salt_gen;
}

function encrypt_password($password,$salt=""){
	$iterations = 10;
	if($salt == ""){
		$salt = DATABASE_SALT;
	}
	$hash = crypt($password,$salt);
	for ($i = 0; $i < $iterations; ++$i){
	    $hash = crypt($hash.$password,$salt);
	}
	return $hash;
}

function login_check(){
	$session = new Session();
	if(!$session->is_logged_in()) {redirect_to("../signin.php");}
}

function check_user_access($user_id){
	$user = User::find_by_id($user_id);
	$user_access = User::get_user_levels($user_id);
	if(!empty($user_access)){	
		foreach($user_access as $access){
			if($access->expires == 1){
				if(strtotime($access->expiry_date) < strtotime(date('Y-m-d h:i:s', time()))){
					User::downgrade_access($access->id, $user->user_id, $access->level_id, $user->user_level);
				}
			}
		}
	}
}

function preprint($data){
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

function clean_value($value){
	global $database;
	return preg_replace("/[^0-9a-z_ ,.(){}-]/i", "", $database->escape_value(trim($value)));
}

$timezones = array (
  '(GMT-12:00) International Date Line West' => 'Pacific/Wake',
  '(GMT-11:00) Midway Island' => 'Pacific/Apia',
  '(GMT-11:00) Samoa' => 'Pacific/Apia',
  '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
  '(GMT-09:00) Alaska' => 'America/Anchorage',
  '(GMT-08:00) Pacific Time (US &amp; Canada); Tijuana' => 'America/Los_Angeles',
  '(GMT-07:00) Arizona' => 'America/Phoenix',
  '(GMT-07:00) Chihuahua' => 'America/Chihuahua',
  '(GMT-07:00) La Paz' => 'America/Chihuahua',
  '(GMT-07:00) Mazatlan' => 'America/Chihuahua',
  '(GMT-07:00) Mountain Time (US &amp; Canada)' => 'America/Denver',
  '(GMT-06:00) Central America' => 'America/Managua',
  '(GMT-06:00) Central Time (US &amp; Canada)' => 'America/Chicago',
  '(GMT-06:00) Guadalajara' => 'America/Mexico_City',
  '(GMT-06:00) Mexico City' => 'America/Mexico_City',
  '(GMT-06:00) Monterrey' => 'America/Mexico_City',
  '(GMT-06:00) Saskatchewan' => 'America/Regina',
  '(GMT-05:00) Bogota' => 'America/Bogota',
  '(GMT-05:00) Eastern Time (US &amp; Canada)' => 'America/New_York',
  '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
  '(GMT-05:00) Lima' => 'America/Bogota',
  '(GMT-05:00) Quito' => 'America/Bogota',
  '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
  '(GMT-04:00) Caracas' => 'America/Caracas',
  '(GMT-04:00) La Paz' => 'America/Caracas',
  '(GMT-04:00) Santiago' => 'America/Santiago',
  '(GMT-03:30) Newfoundland' => 'America/St_Johns',
  '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
  '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
  '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
  '(GMT-03:00) Greenland' => 'America/Godthab',
  '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
  '(GMT-01:00) Azores' => 'Atlantic/Azores',
  '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
  '(GMT) Casablanca' => 'Africa/Casablanca',
  '(GMT) Edinburgh' => 'Europe/London',
  '(GMT) Greenwich Mean Time : Dublin' => 'Europe/London',
  '(GMT) Lisbon' => 'Europe/London',
  '(GMT) London' => 'Europe/London',
  '(GMT) Monrovia' => 'Africa/Casablanca',
  '(GMT+01:00) Amsterdam' => 'Europe/Berlin',
  '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
  '(GMT+01:00) Berlin' => 'Europe/Berlin',
  '(GMT+01:00) Bern' => 'Europe/Berlin',
  '(GMT+01:00) Bratislava' => 'Europe/Belgrade',
  '(GMT+01:00) Brussels' => 'Europe/Paris',
  '(GMT+01:00) Budapest' => 'Europe/Belgrade',
  '(GMT+01:00) Copenhagen' => 'Europe/Paris',
  '(GMT+01:00) Ljubljana' => 'Europe/Belgrade',
  '(GMT+01:00) Madrid' => 'Europe/Paris',
  '(GMT+01:00) Paris' => 'Europe/Paris',
  '(GMT+01:00) Prague' => 'Europe/Belgrade',
  '(GMT+01:00) Rome' => 'Europe/Berlin',
  '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
  '(GMT+01:00) Skopje' => 'Europe/Sarajevo',
  '(GMT+01:00) Stockholm' => 'Europe/Berlin',
  '(GMT+01:00) Vienna' => 'Europe/Berlin',
  '(GMT+01:00) Warsaw' => 'Europe/Sarajevo',
  '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
  '(GMT+01:00) Zagreb' => 'Europe/Sarajevo',
  '(GMT+02:00) Athens' => 'Europe/Istanbul',
  '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
  '(GMT+02:00) Cairo' => 'Africa/Cairo',
  '(GMT+02:00) Harare' => 'Africa/Johannesburg',
  '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
  '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
  '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
  '(GMT+02:00) Kyiv' => 'Europe/Helsinki',
  '(GMT+02:00) Minsk' => 'Europe/Istanbul',
  '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
  '(GMT+02:00) Riga' => 'Europe/Helsinki',
  '(GMT+02:00) Sofia' => 'Europe/Helsinki',
  '(GMT+02:00) Tallinn' => 'Europe/Helsinki',
  '(GMT+02:00) Vilnius' => 'Europe/Helsinki',
  '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
  '(GMT+03:00) Kuwait' => 'Asia/Riyadh',
  '(GMT+03:00) Moscow' => 'Europe/Moscow',
  '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
  '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
  '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
  '(GMT+03:00) Volgograd' => 'Europe/Moscow',
  '(GMT+03:30) Tehran' => 'Asia/Tehran',
  '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
  '(GMT+04:00) Baku' => 'Asia/Tbilisi',
  '(GMT+04:00) Muscat' => 'Asia/Muscat',
  '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
  '(GMT+04:00) Yerevan' => 'Asia/Tbilisi',
  '(GMT+04:30) Kabul' => 'Asia/Kabul',
  '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
  '(GMT+05:00) Islamabad' => 'Asia/Karachi',
  '(GMT+05:00) Karachi' => 'Asia/Karachi',
  '(GMT+05:00) Tashkent' => 'Asia/Karachi',
  '(GMT+05:30) Chennai' => 'Asia/Calcutta',
  '(GMT+05:30) Kolkata' => 'Asia/Calcutta',
  '(GMT+05:30) Mumbai' => 'Asia/Calcutta',
  '(GMT+05:30) New Delhi' => 'Asia/Calcutta',
  '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
  '(GMT+06:00) Almaty' => 'Asia/Novosibirsk',
  '(GMT+06:00) Astana' => 'Asia/Dhaka',
  '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
  '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
  '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
  '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
  '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
  '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
  '(GMT+07:00) Jakarta' => 'Asia/Bangkok',
  '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
  '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
  '(GMT+08:00) Chongqing' => 'Asia/Hong_Kong',
  '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
  '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
  '(GMT+08:00) Kuala Lumpur' => 'Asia/Singapore',
  '(GMT+08:00) Perth' => 'Australia/Perth',
  '(GMT+08:00) Singapore' => 'Asia/Singapore',
  '(GMT+08:00) Taipei' => 'Asia/Taipei',
  '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
  '(GMT+08:00) Urumqi' => 'Asia/Hong_Kong',
  '(GMT+09:00) Osaka' => 'Asia/Tokyo',
  '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
  '(GMT+09:00) Seoul' => 'Asia/Seoul',
  '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
  '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
  '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
  '(GMT+09:30) Darwin' => 'Australia/Darwin',
  '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
  '(GMT+10:00) Canberra' => 'Australia/Sydney',
  '(GMT+10:00) Guam' => 'Pacific/Guam',
  '(GMT+10:00) Hobart' => 'Australia/Hobart',
  '(GMT+10:00) Melbourne' => 'Australia/Sydney',
  '(GMT+10:00) Port Moresby' => 'Pacific/Guam',
  '(GMT+10:00) Sydney' => 'Australia/Sydney',
  '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
  '(GMT+11:00) Magadan' => 'Asia/Magadan',
  '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
  '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
  '(GMT+12:00) Auckland' => 'Pacific/Auckland',
  '(GMT+12:00) Fiji' => 'Pacific/Fiji',
  '(GMT+12:00) Kamchatka' => 'Pacific/Fiji',
  '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
  '(GMT+12:00) Wellington' => 'Pacific/Auckland',
  '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
);

function ago_time($date){
  if(empty($date)){
    return "No date provided";
  }
  
  $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
  $lengths = array("60","60","24","7","4.35","12","10");
  $now = time();
  $unix_date = strtotime($date);
  
  if(empty($unix_date)) {
    return "Bad date";
  }

  if($now > $unix_date) {
    $difference = $now - $unix_date;
    $tense = "ago";
  } else {
    $difference = $unix_date - $now;
    $tense = "from now";
  }
    
  for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
    $difference /= $lengths[$j];
  }

  $difference = round($difference);
 
  if($difference != 1){
    $periods[$j].= "s";
  }
  
  return "$difference $periods[$j] {$tense}";
}

function email_shortcodes($string){
  $find_array = array('{{WWW}}','{{SITE_NAME}}','{{first_name}}','{{last_name}}','{{username}}','{{email}}','{{password}}','{{plain_password}}','{{code}}','{{ip_address}}','{{current_datetime}}','{{gift_card}}','{{name}}','{{link}}');

  (isset($_SESSION['email_data']['WWW'])) ? $WWW = $_SESSION['email_data']['WWW'] : $WWW = "";
  (isset($_SESSION['email_data']['SITE_NAME'])) ? $SITE_NAME = $_SESSION['email_data']['SITE_NAME'] : $SITE_NAME = "";
  (isset($_SESSION['email_data']['first_name'])) ? $first_name = $_SESSION['email_data']['first_name'] : $first_name = "";
  (isset($_SESSION['email_data']['last_name'])) ? $last_name = $_SESSION['email_data']['last_name'] : $last_name = "";
  (isset($_SESSION['email_data']['username'])) ? $username = $_SESSION['email_data']['username'] : $username = "";
  (isset($_SESSION['email_data']['email'])) ? $email = $_SESSION['email_data']['email'] : $email = "";
  (isset($_SESSION['email_data']['password'])) ? $password = $_SESSION['email_data']['password'] : $password = "";
  (isset($_SESSION['email_data']['plain_password'])) ? $plain_password = $_SESSION['email_data']['plain_password'] : $plain_password = "";
  (isset($_SESSION['email_data']['code'])) ? $code = $_SESSION['email_data']['code'] : $code = "";
  (isset($_SESSION['email_data']['ip_address'])) ? $ip_address = $_SESSION['email_data']['ip_address'] : $ip_address = "";
  (isset($_SESSION['email_data']['current_datetime'])) ? $current_datetime = $_SESSION['email_data']['current_datetime'] : $current_datetime = "";
  (isset($_SESSION['email_data']['gift_card'])) ? $gift_card = $_SESSION['email_data']['gift_card'] : $gift_card = "";
  (isset($_SESSION['email_data']['name'])) ? $name = $_SESSION['email_data']['name'] : $name = "";
  (isset($_SESSION['email_data']['link'])) ? $link = $_SESSION['email_data']['link'] : $link = "";

  $replace_array = array($WWW,$SITE_NAME,$first_name,$last_name,$username,$email,$password,$plain_password,$code,$ip_address,datetime_to_text($current_datetime),$gift_card,$name,$link);
  return str_ireplace($find_array, $replace_array, $string); 
}

function display_countries(){
  return '<option value="United Kingdom">United Kingdom</option> 
          <option value="United States">United States</option> 
          <option value="Afghanistan">Afghanistan</option> 
          <option value="Albania">Albania</option> 
          <option value="Algeria">Algeria</option> 
          <option value="American Samoa">American Samoa</option> 
          <option value="Andorra">Andorra</option> 
          <option value="Angola">Angola</option> 
          <option value="Anguilla">Anguilla</option> 
          <option value="Antarctica">Antarctica</option> 
          <option value="Antigua and Barbuda">Antigua and Barbuda</option> 
          <option value="Argentina">Argentina</option> 
          <option value="Armenia">Armenia</option> 
          <option value="Aruba">Aruba</option> 
          <option value="Australia">Australia</option> 
          <option value="Austria">Austria</option> 
          <option value="Azerbaijan">Azerbaijan</option> 
          <option value="Bahamas">Bahamas</option> 
          <option value="Bahrain">Bahrain</option> 
          <option value="Bangladesh">Bangladesh</option> 
          <option value="Barbados">Barbados</option> 
          <option value="Belarus">Belarus</option> 
          <option value="Belgium">Belgium</option> 
          <option value="Belize">Belize</option> 
          <option value="Benin">Benin</option> 
          <option value="Bermuda">Bermuda</option> 
          <option value="Bhutan">Bhutan</option> 
          <option value="Bolivia">Bolivia</option> 
          <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option> 
          <option value="Botswana">Botswana</option> 
          <option value="Bouvet Island">Bouvet Island</option> 
          <option value="Brazil">Brazil</option> 
          <option value="British Indian Ocean Territory">British Indian Ocean Territory</option> 
          <option value="Brunei Darussalam">Brunei Darussalam</option> 
          <option value="Bulgaria">Bulgaria</option> 
          <option value="Burkina Faso">Burkina Faso</option> 
          <option value="Burundi">Burundi</option> 
          <option value="Cambodia">Cambodia</option> 
          <option value="Cameroon">Cameroon</option> 
          <option value="Canada">Canada</option> 
          <option value="Cape Verde">Cape Verde</option> 
          <option value="Cayman Islands">Cayman Islands</option> 
          <option value="Central African Republic">Central African Republic</option> 
          <option value="Chad">Chad</option> 
          <option value="Chile">Chile</option> 
          <option value="China">China</option> 
          <option value="Christmas Island">Christmas Island</option> 
          <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
          <option value="Colombia">Colombia</option> 
          <option value="Comoros">Comoros</option> 
          <option value="Congo">Congo</option> 
          <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
          <option value="Cook Islands">Cook Islands</option> 
          <option value="Costa Rica">Costa Rica</option> 
          <option value="Cote D\'ivoire">Cote D\'ivoire</option> 
          <option value="Croatia">Croatia</option> 
          <option value="Cuba">Cuba</option> 
          <option value="Cyprus">Cyprus</option> 
          <option value="Czech Republic">Czech Republic</option> 
          <option value="Denmark">Denmark</option> 
          <option value="Djibouti">Djibouti</option> 
          <option value="Dominica">Dominica</option> 
          <option value="Dominican Republic">Dominican Republic</option> 
          <option value="Ecuador">Ecuador</option> 
          <option value="Egypt">Egypt</option> 
          <option value="El Salvador">El Salvador</option> 
          <option value="Equatorial Guinea">Equatorial Guinea</option> 
          <option value="Eritrea">Eritrea</option> 
          <option value="Estonia">Estonia</option> 
          <option value="Ethiopia">Ethiopia</option> 
          <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
          <option value="Faroe Islands">Faroe Islands</option> 
          <option value="Fiji">Fiji</option> 
          <option value="Finland">Finland</option> 
          <option value="France">France</option> 
          <option value="French Guiana">French Guiana</option> 
          <option value="French Polynesia">French Polynesia</option> 
          <option value="French Southern Territories">French Southern Territories</option> 
          <option value="Gabon">Gabon</option> 
          <option value="Gambia">Gambia</option> 
          <option value="Georgia">Georgia</option> 
          <option value="Germany">Germany</option> 
          <option value="Ghana">Ghana</option> 
          <option value="Gibraltar">Gibraltar</option> 
          <option value="Greece">Greece</option> 
          <option value="Greenland">Greenland</option> 
          <option value="Grenada">Grenada</option> 
          <option value="Guadeloupe">Guadeloupe</option> 
          <option value="Guam">Guam</option> 
          <option value="Guatemala">Guatemala</option> 
          <option value="Guinea">Guinea</option> 
          <option value="Guinea-bissau">Guinea-bissau</option> 
          <option value="Guyana">Guyana</option> 
          <option value="Haiti">Haiti</option> 
          <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option> 
          <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option> 
          <option value="Honduras">Honduras</option> 
          <option value="Hong Kong">Hong Kong</option> 
          <option value="Hungary">Hungary</option> 
          <option value="Iceland">Iceland</option> 
          <option value="India">India</option> 
          <option value="Indonesia">Indonesia</option> 
          <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option> 
          <option value="Iraq">Iraq</option> 
          <option value="Ireland">Ireland</option> 
          <option value="Israel">Israel</option> 
          <option value="Italy">Italy</option> 
          <option value="Jamaica">Jamaica</option> 
          <option value="Japan">Japan</option> 
          <option value="Jordan">Jordan</option> 
          <option value="Kazakhstan">Kazakhstan</option> 
          <option value="Kenya">Kenya</option> 
          <option value="Kiribati">Kiribati</option> 
          <option value="Korea, Democratic People\'s Republic of">Korea, Democratic People\'s Republic of</option> 
          <option value="Korea, Republic of">Korea, Republic of</option> 
          <option value="Kuwait">Kuwait</option> 
          <option value="Kyrgyzstan">Kyrgyzstan</option> 
          <option value="Lao People\'s Democratic Republic">Lao People\'s Democratic Republic</option> 
          <option value="Latvia">Latvia</option> 
          <option value="Lebanon">Lebanon</option> 
          <option value="Lesotho">Lesotho</option> 
          <option value="Liberia">Liberia</option> 
          <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option> 
          <option value="Liechtenstein">Liechtenstein</option> 
          <option value="Lithuania">Lithuania</option> 
          <option value="Luxembourg">Luxembourg</option> 
          <option value="Macao">Macao</option> 
          <option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option> 
          <option value="Madagascar">Madagascar</option> 
          <option value="Malawi">Malawi</option> 
          <option value="Malaysia">Malaysia</option> 
          <option value="Maldives">Maldives</option> 
          <option value="Mali">Mali</option> 
          <option value="Malta">Malta</option> 
          <option value="Marshall Islands">Marshall Islands</option> 
          <option value="Martinique">Martinique</option> 
          <option value="Mauritania">Mauritania</option> 
          <option value="Mauritius">Mauritius</option> 
          <option value="Mayotte">Mayotte</option> 
          <option value="Mexico">Mexico</option> 
          <option value="Micronesia, Federated States of">Micronesia, Federated States of</option> 
          <option value="Moldova, Republic of">Moldova, Republic of</option> 
          <option value="Monaco">Monaco</option> 
          <option value="Mongolia">Mongolia</option> 
          <option value="Montserrat">Montserrat</option> 
          <option value="Morocco">Morocco</option> 
          <option value="Mozambique">Mozambique</option> 
          <option value="Myanmar">Myanmar</option> 
          <option value="Namibia">Namibia</option> 
          <option value="Nauru">Nauru</option> 
          <option value="Nepal">Nepal</option> 
          <option value="Netherlands">Netherlands</option> 
          <option value="Netherlands Antilles">Netherlands Antilles</option> 
          <option value="New Caledonia">New Caledonia</option> 
          <option value="New Zealand">New Zealand</option> 
          <option value="Nicaragua">Nicaragua</option> 
          <option value="Niger">Niger</option> 
          <option value="Nigeria">Nigeria</option> 
          <option value="Niue">Niue</option> 
          <option value="Norfolk Island">Norfolk Island</option> 
          <option value="Northern Mariana Islands">Northern Mariana Islands</option> 
          <option value="Norway">Norway</option> 
          <option value="Oman">Oman</option> 
          <option value="Pakistan">Pakistan</option> 
          <option value="Palau">Palau</option> 
          <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option> 
          <option value="Panama">Panama</option> 
          <option value="Papua New Guinea">Papua New Guinea</option> 
          <option value="Paraguay">Paraguay</option> 
          <option value="Peru">Peru</option> 
          <option value="Philippines">Philippines</option> 
          <option value="Pitcairn">Pitcairn</option> 
          <option value="Poland">Poland</option> 
          <option value="Portugal">Portugal</option> 
          <option value="Puerto Rico">Puerto Rico</option> 
          <option value="Qatar">Qatar</option> 
          <option value="Reunion">Reunion</option> 
          <option value="Romania">Romania</option> 
          <option value="Russian Federation">Russian Federation</option> 
          <option value="Rwanda">Rwanda</option> 
          <option value="Saint Helena">Saint Helena</option> 
          <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option> 
          <option value="Saint Lucia">Saint Lucia</option> 
          <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option> 
          <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option> 
          <option value="Samoa">Samoa</option> 
          <option value="San Marino">San Marino</option> 
          <option value="Sao Tome and Principe">Sao Tome and Principe</option> 
          <option value="Saudi Arabia">Saudi Arabia</option> 
          <option value="Senegal">Senegal</option> 
          <option value="Serbia and Montenegro">Serbia and Montenegro</option> 
          <option value="Seychelles">Seychelles</option> 
          <option value="Sierra Leone">Sierra Leone</option> 
          <option value="Singapore">Singapore</option> 
          <option value="Slovakia">Slovakia</option> 
          <option value="Slovenia">Slovenia</option> 
          <option value="Solomon Islands">Solomon Islands</option> 
          <option value="Somalia">Somalia</option> 
          <option value="South Africa">South Africa</option> 
          <option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option> 
          <option value="Spain">Spain</option> 
          <option value="Sri Lanka">Sri Lanka</option> 
          <option value="Sudan">Sudan</option> 
          <option value="Suriname">Suriname</option> 
          <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option> 
          <option value="Swaziland">Swaziland</option> 
          <option value="Sweden">Sweden</option> 
          <option value="Switzerland">Switzerland</option> 
          <option value="Syrian Arab Republic">Syrian Arab Republic</option> 
          <option value="Taiwan, Province of China">Taiwan, Province of China</option> 
          <option value="Tajikistan">Tajikistan</option> 
          <option value="Tanzania, United Republic of">Tanzania, United Republic of</option> 
          <option value="Thailand">Thailand</option> 
          <option value="Timor-leste">Timor-leste</option> 
          <option value="Togo">Togo</option> 
          <option value="Tokelau">Tokelau</option> 
          <option value="Tonga">Tonga</option> 
          <option value="Trinidad and Tobago">Trinidad and Tobago</option> 
          <option value="Tunisia">Tunisia</option> 
          <option value="Turkey">Turkey</option> 
          <option value="Turkmenistan">Turkmenistan</option> 
          <option value="Turks and Caicos Islands">Turks and Caicos Islands</option> 
          <option value="Tuvalu">Tuvalu</option> 
          <option value="Uganda">Uganda</option> 
          <option value="Ukraine">Ukraine</option> 
          <option value="United Arab Emirates">United Arab Emirates</option> 
          <option value="United Kingdom">United Kingdom</option> 
          <option value="United States">United States</option> 
          <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option> 
          <option value="Uruguay">Uruguay</option> 
          <option value="Uzbekistan">Uzbekistan</option> 
          <option value="Vanuatu">Vanuatu</option> 
          <option value="Venezuela">Venezuela</option> 
          <option value="Viet Nam">Viet Nam</option> 
          <option value="Virgin Islands, British">Virgin Islands, British</option> 
          <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option> 
          <option value="Wallis and Futuna">Wallis and Futuna</option> 
          <option value="Western Sahara">Western Sahara</option> 
          <option value="Yemen">Yemen</option> 
          <option value="Zambia">Zambia</option> 
          <option value="Zimbabwe">Zimbabwe</option>';
}

?>