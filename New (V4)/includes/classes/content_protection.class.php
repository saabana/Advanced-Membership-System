<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

class Content_Protection {
	
	protected static $table_name="protected_content";
	protected static $db_fields = array('id','user_id','name','amount','timed','timed_type','timed_amount','description','status','users','link');
	
	// Table: protected_content
	
	public $id;
	public $user_id;
	public $name;
	public $amount;
	public $timed;
	public $timed_type;
	public $timed_amount;
	public $description;
	public $status;
	public $users;
	public $link;


	public static function protect($id,$user_level){
		global $database;
		global $session;

		$user_id = $_SESSION['masdyn']['ams']['user_id'];

		$protected = self::find_by_id($id);
		$users = explode(",", $protected->users);

		if(in_array(ADMIN_LEVEL, explode(",", $user_level))){
			return "approved";
		} else {
			if(in_array($user_id, $users)){
				return "approved";
			} else {
				return '<div class="row">

		<div class="col-md-12">
			<div class="protected-page">
				<h2>Protected Content</h2>
				<div class="protected-description">
					<p>'.$protected->description.'</p>
					<button class="btn btn-primary" onclick="purchase_access(\''.$protected->id.'\',\''.$protected->amount.'\');">Purchase Access ('.$protected->amount.' Tokens)</button>
				</div>
			</div>
		</div>
	</div>';
			}
		}
		
	}

	public static function purchase_access($id){
		global $database;
		global $session;

		$user_id = $_SESSION['masdyn']['ams']['user_id'];
		$datetime = strftime("%Y-%m-%d %H:%M:%S", time());

		$protected = self::find_by_id($id);

		if(empty($users)){
			$in = false;
		} else {
			$users = explode(",", $protected->users);
			if(!in_array($user_id,$users)){
				$in = false;
			} else {
				$in = true;
			}
		}

		if($in == false){
			$user = User::find_by_id($user_id);
			$new_tokens = $user->tokens - $protected->amount;
			$database->query("UPDATE users SET tokens = '{$new_tokens}' WHERE user_id = '{$user->user_id}' ");

			$message = "Purchase of protected content: $protected->name.";
			$database->query("INSERT INTO purchase_history VALUES('','$user->user_id','$protected->amount','$message','$datetime')");

			if(empty($users)){
				$new_users = $user->user_id;
			} else {
				$users[] = $user->user_id;
				$new_users = implode(",", $users);
			}
			$database->query("UPDATE protected_content SET users = '{$new_users}' WHERE id = '{$id}' ");

			$email_class = new Email();
			unset($_SESSION['email_data']);
			$_SESSION['email_data'] = array(
				"WWW" => WWW,
				"SITE_NAME" => SITE_NAME,
				"name" => $protected->name,
				"link" => $protected->link
			);
			$email_data = Email::email_template_data(9);		
			$email_class->send_email($user->email, SITE_EMAIL, $email_data->name, $email_data->template_content);

			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Thank you for purchasing access to this page. You can now see the protected content below.</div>");

			return true;

		} else {
			$session->message("<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button>You have already purchased access to this page.</div>");
			return false;
		}
	}

	public static function create_protection($name,$description,$amount,$link,$status){
		global $database;
		global $session;

		$database->query("INSERT INTO protected_content VALUES('','{$name}','{$amount}','{$description}','{$status}','','{$link}') ");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Protection successfully created.</div>");

		redirect_to("content_protection.php");

	}

	public static function update_protection($id,$name,$description,$amount,$link,$status){
		global $database;
		global $session;

		$database->query("UPDATE protected_content SET name = '{$name}', description = '{$description}', amount = '{$amount}', link = '{$link}', status = '{$status}' WHERE id = '{$id}' ");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Protection successfully updated.</div>");

		redirect_to("content_protection.php");
	}

	public static function delete_protection($id){
		global $database;
		global $session;

		$database->query("DELETE FROM protected_content WHERE id = '{$id}' ");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Protection successfully deleted.</div>");

		redirect_to("content_protection.php");
	}

  	public static function get_purchased_content($user_id) {
		return self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE find_in_set('{$user_id}',users)");
    }


	// Common Database Methods
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  	}
  	
  	public static function find_by_id($id=0) {
		$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id = '{$id}' LIMIT 1");
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