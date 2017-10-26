<?php

class MailedMailchimp {

	private static $initiated = false;

	private static $apiKey;
	private static $listId;

	private static $apiKeyIsValid = null;

	public static function init() {

		if ( ! self::$initiated ) {
			self::init_hooks();
		}

		self::$apiKey = get_option(MAILED__SETTINGS_GROUP . '_mailchimp_apikey');
		self::$listId = get_option(MAILED__SETTINGS_GROUP . '_mailchimp_list_id');

		self::api_key_is_valid();

		// add_settings_error('title', 'asasasa', 'lorem ipsum', 'error');

	}

	/*
		Init hook (once time)
	*/
	private static function init_hooks() {
		self::$initiated = true;
	}

	/*
		Check if auto subscribe is ON
	*/
	public static function is_mailchimp_autosubscribe_on(){

		return !!get_option(MAILED__SETTINGS_GROUP . '_mailchimp_auto_subscribe');

	}

	/*
		Check if integrate mailchimp to list table is ON
	*/
	public static function is_mailchimp_intragrate_list_table_on(){

		return !!get_option(MAILED__SETTINGS_GROUP . '_mailchimp_intragrate_list_table');

	}

	/*
		Check if credentials is not empty
	*/
	private static function is_credentials_defined(){

		return !empty(self::$apiKey) && !empty(self::$listId);

	}

	/*
		Check if credentials is valid
	*/
	private static function is_credentials_valid(){

		return self::is_credentials_defined();

	}

	/*
		Get API server
	*/
	private static function get_api_server(){

		return self::is_credentials_valid() ? substr(self::$apiKey, strpos(self::$apiKey,'-') + 1) : null;

	}

	/*
		Get api URL
	*/
	private static function get_api_url($route){

		if(is_array($route)){

			$newroute = "";

			foreach($route as $key => $value){

				$newroute .= is_null($value) ? "/$key" : "/$key/$value";

			}

			$route = $newroute;

		}

		return self::get_api_server() ? MAILED__MAILCHIMP_API_PROTOCOL . self::get_api_server() . '.' . MAILED__MAILCHIMP_API_ADDRESS . $route : null;

	}

	/*
		Make API Request
	*/
	private static function make_api_request($method, $url, $params = null){

    $ch = curl_init($url);

    $http_header = array(
			'Content-Type: application/json'
    );

    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . self::$apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if(!is_null($params)){
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));                                                                                                                 
    }

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $res = new StdClass;

    $res->status = $httpCode;
    $res->body = json_decode($result);

    return $res;

	}

	/*
		Ping
	*/
	private static function mailchimp_ping(){

		if(is_null(self::$apiKeyIsValid)){
		
			$route = array(
				'ping' => null
			);

			self::$apiKeyIsValid = isset(self::make_api_request('GET', self::get_api_url($route))->body->health_status);

			if(!self::$apiKeyIsValid){
				Mailed::add_alert('API Key inválida');
			}

		}

		return self::$apiKeyIsValid;

	}

	/*
		Check if API key is valid
	*/
	public static function api_key_is_valid(){

		return self::mailchimp_ping();

	}

	/*
		Add member to a list: http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/ 
	*/
	private static function lists_add_member($listId, $params){

		if(empty($params['email_address']))
			return;

		$route = array(
			'lists' => $listId,
			'members' => null
		);

		return self::make_api_request('POST', self::get_api_url($route), $params)->body;

	}

	private static function  lists_get_members($listId, $params = null){

		$route = array(
			'lists' => $listId,
			'members' => null
		);

		return self::make_api_request('GET', self::get_api_url($route), $params)->body->members;

	}

	/*
		Batch process
	*/
	public static function mailchimp_batches($arr){



	}

	/*
		Add register to a configured list
	*/
	public static function mailchimp_add($email, $firstname, $lastname = null){

		$params = array(
			'email_address' => $email,
			'status' => 'subscribed',
			'merge_fields' => array(
				'FNAME' => $firstname
			)
		);

		if(!is_null($lastname))
			$params['merge_fields']['LNAME'] = $lastname;

		return self::lists_add_member(self::$listId, $params);

	}

	/*
		Multiple add or update to a configured list
	*/
	public static function mailchimp_add_multiple($arr){

		if(!is_array($arr))
			return;

		$operations = array();

		foreach($arr as $row){

			$body = array(
				'email_address' => $row[MAILED__FIELD_EMAIL],
				'status_if_new' => 'subscribed',
				'merge_fields' => array(
					'FNAME' => $row[MAILED__FIELD_FIRSTNAME]
				)
			);

			if(!empty($row[MAILED__FIELD_LASTNAME]))
				$body['merge_fields']['LNAME'] = $row[MAILED__FIELD_LASTNAME];

			$operations[] = array(
				'method' => 'PUT',
				'path' => 'lists/' . self::$listId . '/members/' . md5($row[MAILED__FIELD_EMAIL]),
				'body' => json_encode($body)
			);

		}

		return self::make_api_request('POST', self::get_api_url('/batches'), array('operations' => $operations))->status;

	}

	/*
		Get information about members in a list
	*/
	public static function mailchimp_list_members(){

		return self::lists_get_members(self::$listId);

	}



}