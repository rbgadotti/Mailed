<?php

class MailedSettings {

	private static $initiated = false;
	private static $settings_error = array();

	public static function init() {

		if ( ! self::$initiated ) {
			self::init_hooks();
		}

		add_action( 'admin_init', array( 'MailedSettings', 'register_settings') );
		add_action( 'admin_menu', array( 'MailedSettings', 'register_settings_menu') );

	}

	/*
		Init hook (once time)
	*/
	private static function init_hooks() {
		self::$initiated = true;
	}

	public static function register_settings(){

		// MailedSettings::add_settings_error('Invalid API Key', 'mailchimp-invalid-key', 'API Key inválida', 'error');
		self::show_settings_error();

		/*
			MailChimp
		*/
		register_setting( MAILED__SETTINGS_GROUP, MAILED__SETTINGS_GROUP . '_mailchimp_apikey', 'string' );
		register_setting( MAILED__SETTINGS_GROUP, MAILED__SETTINGS_GROUP . '_mailchimp_list_id', 'string' );
		register_setting( MAILED__SETTINGS_GROUP, MAILED__SETTINGS_GROUP . '_mailchimp_auto_subscribe', 'boolean' );
		register_setting( MAILED__SETTINGS_GROUP, MAILED__SETTINGS_GROUP . '_mailchimp_intragrate_list_table', 'boolean' );
		/*
			Ajax
		*/
		register_setting( MAILED__SETTINGS_GROUP, MAILED__SETTINGS_GROUP . '_ajax_message_200', 'string' );
		register_setting( MAILED__SETTINGS_GROUP, MAILED__SETTINGS_GROUP . '_ajax_message_400', 'string' );

	}

	public static function register_settings_menu(){

		add_options_page( 'Mailed Newsletter Settings', 'Mailed', 'manage_options', 'mailed_settings', array('MailedSettings', 'register_settings_page') );

	}

	public static function register_settings_page(){

		require_once MAILED__PLUGIN_DIR . 'view.settings.php';

	}

	public static function add_settings_error($title, $slug, $message, $type = 'error'){
	
		self::$settings_error[] = array(
			'title' => $title,
			'slug' => $slug,
			'message' => $message,
			'type' => $type
		);

	}

	private static function show_settings_error(){

		foreach(self::$settings_error as $setting_error){
			
			add_settings_error(
				$setting_error['title'],
				$setting_error['slug'],
				$setting_error['message'],
				$setting_error['type']
			);

		}

	}



}