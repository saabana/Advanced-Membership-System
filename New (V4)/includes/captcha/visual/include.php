<?php 

$_FORM_TYPE = 0;

function printCaptcha($formId = NULL, $type = NULL, $fieldName = NULL, $accessibilityFieldName = NULL) {
	require_once('includes/captcha/visual/inc/visualcaptcha.class.php');
	
	$visualCaptcha = new \visualCaptcha\captcha($formId,$type,$fieldName,$accessibilityFieldName);
	$visualCaptcha->show();
}

function validCaptcha($formId = NULL, $type = NULL, $fieldName = NULL, $accessibilityFieldName = NULL) {
	require_once('includes/captcha/visual/inc/visualcaptcha.class.php');
	
	$visualCaptcha = new \visualCaptcha\captcha($formId,$type,$fieldName,$accessibilityFieldName);
	return $visualCaptcha->isValid();
}

?>