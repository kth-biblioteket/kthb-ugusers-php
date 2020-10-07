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
function getldapuserbyaccount($kth_id) {
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

Funktion som hämtar användarinformation från LDAP

**********/
function getldapuserbycn($firstname, $lastname) {
	global $apikey_ldap;
	$ch = curl_init();
	$namefilter = $firstname . "* " . $lastname . "*";
	$url = 'https://lib.kth.se/ldap/api/v1/users/' . $namefilter;
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

Funktion som hämtar användarinformation från KTH Profiles

**********/
function getkthprofile_by_kth_id($kth_id) {
	global $KTH_API_KEY_PROFILES;
	$ch = curl_init();
	$url = 'https://api.kth.se/api/profile/v1/kthid/' . $kth_id;
	$queryParams = '?api_key=' . $KTH_API_KEY_PROFILES;
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

Funktion som hämtar användarinformation från KTH Profiles

**********/
function getkthprofile_by_kth_username($kth_username) {
	global $KTH_API_KEY_PROFILES;
	$ch = curl_init();
	$url = 'https://api.kth.se/api/profile/v1/user/' . $kth_username;
	$queryParams = '?api_key=' . $KTH_API_KEY_PROFILES;
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
$userinfo = '';
$kthprofile = '';
if(isset($_SESSION['kth_id'])) {
	//echo 'post searchuser: ' . $_POST['searchuser'];
	if(!empty($_POST['searchuser'])) {
		if($_POST['searchuser'] == 1) {
			if ($_POST['type'] == 'ldap') {
				if($_POST['kthaccount'] != '' ){
					$userinfo = getldapuserbyaccount($_POST['kthaccount']);
				} 
				elseif ($_POST['firstname'] != '' || $_POST['lastname'] != ''){
					$userinfo = getldapuserbycn($_POST['firstname'], $_POST['lastname'] );
				}
			} 
			if ($_POST['type'] == 'kthprofile') {
				$userinfo = getkthprofile_by_kth_username($_POST['kthaccount']);
			}

			if ($_POST['type'] == 'kthprofilebykthid') {
				$userinfo = getkthprofile_by_kth_id($_POST['kthid']);
			}
			//$json_data = json_decode($uguser);
			//echo $json_data->ugusers->ugPrimaryAffiliation;
			echo $userinfo;
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
else {
	echo "error, not authorized";
}
?>