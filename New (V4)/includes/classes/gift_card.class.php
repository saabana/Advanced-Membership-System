<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

class Gift_Card {
	
	protected static $table_name="gift_cards";
	protected static $package_table_name="gift_card_packages";
	protected static $db_fields = array('id','package_id','code','amount','from','note','purchased','date_used','user_id','status','name');
	
	// Table: gift_cards
	
	public $id;
	public $package_id;
	public $code;
	public $amount;
	public $from;
	public $note;
	public $purchased;
	public $date_used;
	public $user_id;
	public $status;

	// Table: gift_card_packages
	
	// public $id; //already defined above
	public $name;
	// public $amount; //already defined above
	// public $status; //already defined above
	

	public static function create_package($name,$amount,$status){
		global $database;
		global $session;
		if($database->query("INSERT INTO gift_card_packages VALUES('','{$name}','{$amount}','{$status}') ")){
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The package has been updated.</div>");
		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but someting has gone wrong. Please try again.</div>");
		}
		redirect_to(WWW.ADMINDIR."gift_card_packages.php");
	}

	public static function purchase_gift_card($type,$package_id,$name,$from,$email,$public_id,$note){
		global $database;
		global $session;

		if($type == "0"){
			// credit purchase

			$user = User::find_by_id($_SESSION['masdyn']['ams']['user_id']);
			$package_data = self::find_package_by_id($package_id);

			if($user->tokens >= $package_data->amount){
				$code = strtoupper(substr(chunk_split(md5($package_id.$name.$from.$email.time().mt_rand(5,17)),6,"-"), 0, -4));
				$datetime = strftime("%Y-%m-%d %H:%M:%S", time());

															// (id,package_id,code,from,note,purchased,used,date_used,user_id,status)
				if($database->query("INSERT INTO gift_cards VALUES ('','{$package_id}','{$code}','{$package_data->amount}','{$from}','{$note}','{$datetime}','','{$public_id}','0') ")){
					$new_tokens = $user->tokens - $package_data->amount;
					$database->query("UPDATE users SET tokens = '{$new_tokens}' WHERE user_id = '{$user->user_id}' ");

					$message = "Purchase of $package_data->name gift card.";
					$database->query("INSERT INTO purchase_history VALUES('','$user->user_id','$package_data->amount','$message','$datetime')");

					$email_class = new Email();
					unset($_SESSION['email_data']);
					$_SESSION['email_data'] = array(
						"WWW" => WWW,
						"SITE_NAME" => SITE_NAME,
						"gift_card" => $package_data->name,
						"code" => $code
					);
					$email_data = Email::email_template_data(8);		
					$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);

					$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Thank you for purchasing our \"".$package_data->name."\" gift card. We have sent the gift card to your email address.</div>");
					return true;
				} else {
					$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but something has gone wrong, please refresh and try again.</div>");
					return false;
				}
			} else {
				$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you don't have enough credit to purchase this gift card.</div>");
				return false;
			}

		} else if($type == "1"){
			// paypal purchase

			$package_data = self::find_package_by_id($package_id);

			$code = strtoupper(substr(chunk_split(md5($package_id.$name.$from.$email.time().mt_rand(5,17)),6,"-"), 0, -4));
			$datetime = strftime("%Y-%m-%d %H:%M:%S", time());

														// (id,package_id,code,from,note,purchased,used,date_used,user_id,status)
			if($database->query("INSERT INTO gift_cards VALUES ('','{$package_id}','{$code}','{$package_data->amount}','{$from}','{$note}','{$datetime}','','{$public_id}','0') ")){

				$email_class = new Email();
				unset($_SESSION['email_data']);
				$_SESSION['email_data'] = array(
					"WWW" => WWW,
					"SITE_NAME" => SITE_NAME,
					"gift_card" => $package_data->name,
					"code" => $code
				);
				$email_data = Email::email_template_data(8);		
				$email_class->send_email($email, SITE_EMAIL, $email_data->name, $email_data->template_content);

				$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Thank you for purchasing our \"".$package_data->name."\" gift card. We have sent the gift card to your email address.</div>");

			} else {
				$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but something has gone wrong, please refresh and try again.</div>");
			}

		}

	}

	public static function activate_gift_card($user_id,$code){
		global $database;
		global $session;

		$card = self::find_by_sql("SELECT * FROM gift_cards WHERE code = '{$code}' LIMIT 1 ");

		if(!empty($card)){

			$card = $card[0];
			$user = User::find_by_id($user_id);
			$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			$ready = false;

			if($card->user_id != "0"){
				if($card->user_id == $user->user_id){
					$ready = true;
				} else {
					$ready = false;
					$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but that code has been issued specifically for another user.</div>");
				}
			} else {
				$ready = true;
			}
			if($ready == true){
				if($card->status == 0){
					// activate card
					$new_tokens = $card->amount + $user->tokens;
					$database->query("UPDATE users SET tokens = '{$new_tokens}' WHERE user_id = '{$user->user_id}' ");
					$message = "Activation of $card->amount token gift card from $card->from.";
					$database->query("INSERT INTO account_history VALUES('','$user->user_id','$card->amount','$message','$datetime')");
					$database->query("UPDATE gift_cards SET status = '1', date_used = '{$datetime}', user_id = '{$user->user_id}' WHERE id = '{$card->id}' ");

					$return = '<div class="row"> <div class="col-md-12"> <div class="alert alert-success">The gift card has been successfully activated. '.$card->amount.' tokens have just been added to your account.</div> </div> </div> ';
					if($card->note != ""){
						$return .= '<div class="row"> <div class="col-md-12"> <strong>'.$card->from.' just wanted to say:</strong><br /> <p>'.$card->note.'</p> </div> </div>';
					}
					return $return;
				} else if($card->status == 1){
					// display error message - already used
					$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but that code has already been used.</div>");
					return false;
				} else if($card->status == 2){
					// display error message - suspended
					$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but that code has been suspended. Please contact support for further assistance.</div>");
					return false;
				}
			} else {
				return false;
			}
		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but that code doesn't exist.</div>");
			return false;
		}

	}

	public static function edit_package($id,$name,$amount,$status){
		global $database;
		global $session;
		if($database->query("UPDATE gift_card_packages SET name = '{$name}', amount = '{$amount}', status = '{$status}' WHERE id = '{$id}' ")){
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The package has been updated.</div>");
		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but someting has gone wrong. Please try again.</div>");
		}
		redirect_to(WWW.ADMINDIR."gift_card_packages.php");
	}

	public static function delete_package($id){
		global $database;
		global $session;
		if($database->query("DELETE FROM gift_card_packages WHERE id = '{$id}' ")){
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The package has been deleted.</div>");
		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but someting has gone wrong. Please try again.</div>");
		}
		redirect_to(WWW.ADMINDIR."gift_card_packages.php");
	}


	public static function edit_card($id,$package_id,$code,$amount,$from,$user_id,$status,$purchased,$date_used){
		global $database;
		global $session;
		if($database->query("UPDATE gift_cards SET package_id = '{$package_id}', code = '{$code}', amount = '{$amount}', `from` = '{$from}', user_id = '{$user_id}', purchased = '{$purchased}', date_used = '{$date_used}', status = '{$status}' WHERE id = '{$id}' ")){
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The gift card has been updated.</div>");
		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but someting has gone wrong. Please try again.</div>");
		}
		redirect_to(WWW.ADMINDIR."gift_cards.php");
	}

	public static function delete_card($id){
		global $database;
		global $session;
		if($database->query("DELETE FROM gift_cards WHERE id = '{$id}' ")){
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The gift card has been deleted.</div>");
		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but someting has gone wrong. Please try again.</div>");
		}
		redirect_to(WWW.ADMINDIR."gift_cards.php");
	}


	// Common Database Methods
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  	}

	public static function find_all_packages() {
		return self::find_by_sql("SELECT * FROM ".self::$package_table_name);
  	}
  	
  	public static function find_by_id($id=0) {
		$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id = '{$id}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
    }

  	public static function find_package_by_id($id=0) {
		$result_array = self::find_by_sql("SELECT * FROM ".self::$package_table_name." WHERE id = '{$id}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
    }

	public static function count_by_sql($sql) {
		global $database;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}

  	public static function find_by_sql($sql="") {
	    global $database;
	    $result_set = $database->query($sql);
	    $object_array = array();
	    while ($row = $database->fetch_array($result_set)) {
	      $object_array[] = self::instantiate($row);
	    }
	    return $object_array;
    }

	public static function count_all() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".self::$table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}

	private static function instantiate($record) {
    	$object = new self;
		
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
	  $attributes = array();
	  foreach(self::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $database;
	  $clean_attributes = array();
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}

}

?>