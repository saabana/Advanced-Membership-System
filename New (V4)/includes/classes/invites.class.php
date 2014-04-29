<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

class Invites {
	
	protected static $table_name="invites";
	protected static $user_table_name="users";
	protected static $db_fields = array('', 'user_id', 'code');
	
	public $user_id;
	public $code;
	
	private static function generate_code($lenth = 15) { 
	    $aZ09 = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9)); 
	    $out =''; 
	    for($c=0;$c < $lenth;$c++) { 
	       $out .= $aZ09[mt_rand(0,count($aZ09)-1)]; 
	    } 
	    return $out; 
	}
	
	public static function create_invite($user_id, $username, $location) {
		global $database;
		$session = new Session();
		// Genetate the hash.
		$code = self::generate_code();
		$invite = $username."_".$code;
		
		//insert into db the data
		$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$user_id', '$invite')";
		$database->query($sql);

		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>1 New invite code has been created.</div>");
		
		redirect_to($location);
	}
	
	public static function check_invite_code($code) {
    global $database;
	$code = $database->escape_value($code);

	$sql = "SELECT * FROM ".self::$table_name." WHERE code = '{$code}' LIMIT 1";
	
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? true : false;
	}
	
  	public static function count_all($user_id) {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".self::$table_name." WHERE user_id = '{$user_id}'";
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	public static function find_invites($user_id=0) {
    	return self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE user_id= '{$user_id}'");
    }
	
	public static function delete_invite($invite_code, $location) {
		global $database;
		$sql = "DELETE FROM ".self::$table_name." WHERE code = '{$invite_code}' ";
		$database->query($sql);
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Invite code has been deleted successfully.</div>");
		redirect_to("$location");
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
	
	private static function instantiate($record) {
		// Could check that $record exists and is an array
    	$object = new self;
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
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
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}

}

?>