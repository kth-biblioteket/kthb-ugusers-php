<?php
header('Content-Type: application/json; charset=utf-8');
require('config.php'); //innehåller API-KEY.

if (!isset($_SESSION)) {
	session_start();
}
$errorcode = 0;
$debug = true;
/* 
	TODO 
*/

/********** 

Funktion som hämtar användarinformation från LDAP

**********/
function getuser($kth_id) {
	global $apikey_ldap;
	$ch = curl_init();
	$url = 'https://lib.kth.se/ldap/api/v1/account/' . $kth_id;
	$queryParams = '?token=' . $apikey_ldap;
	curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}

/**********

Funktion som ser till att göra "escape" på de fält som kan innehålla specialtecken som: ",/,\ osv...

**********/
function escapeJsonString($value) {
    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
    $result = str_replace($escapers, $replacements, $value);
    return $result;
}

/**********

Huvudkod

**********/
$language = "en"; 
if(!empty($_POST['language'])) {
	if($_POST['language'] == 'swedish') {
		$language = 'swedish';
	}
}

if(isset($_SESSION['kth_id'])) {
	if(!empty($_POST['searchuser'])) {
		if($_POST['searchuser'] == 1) {
			$uguser 			= getuser($_POST['kthaccount']);
			//$json_data = json_decode($uguser);
			//echo $json_data->ugusers->ugPrimaryAffiliation;
			echo $uguser;
		}
	} else {
		$result = "Error";
		if ($language == 'swedish') {
			$message = "Fel!";
		} else {
			$message = "Error!";
		}
		$data = array(
			"result"  => $result,
			"message" => $message
		);
		$json_data = json_encode($data);
		print $json_data;
	}
}
?>