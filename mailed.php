<?php

/**
 * @package Mailed
 */
/*
Plugin Name: Mailed Newsletter
Plugin URI: https://github.com/rbgadotti/mailed
Description: A simple newsletter plugin.
Version: 1.0.0
Author: Rafael Gadotti <rbgadotti@gmail.com>
Author URI: https://github.com/rbgadotti
License: GPLv2 or later
Text Domain: mailed
*/

/*
	Make sure we don't expose any info if called directly
*/
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/*
	Info
*/
define( 'MAILED_VERSION', '1.0.0' );
define( 'MAILED_DOMAIN', 'mailed');
define( 'MAILED__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
/*
	Settings
*/
define( 'MAILED__SETTINGS_GROUP', 'mailed_settings');
/*
	Table
*/
define( 'MAILED__TABLE_NAME', 'mailed');
define( 'MAILED__FIELD_ID', MAILED__TABLE_NAME . '_id');
define( 'MAILED__FIELD_EMAIL', MAILED__TABLE_NAME . '_email');
define( 'MAILED__FIELD_FIRSTNAME', MAILED__TABLE_NAME . '_firstname');
define( 'MAILED__FIELD_LASTNAME', MAILED__TABLE_NAME . '_lastname');
define( 'MAILED__FIELD_CREATED_AT', MAILED__TABLE_NAME . '_created_at');
define( 'MAILED__FIELD_EDITED_AT', MAILED__TABLE_NAME . '_edited_at');

/*
	Dependencies
*/
define( 'MAILED__DEPENDENCY_PHPEXCEL', plugin_dir_path( __FILE__ ) . 'PHPExcel/PHPExcel.php');

/*
	List settings
*/
define( 'MAILED__LIST_ITEMS_PER_PAGE', 10);

/*
	Ajax errors
*/
define( 'MAILED__AJAX_ERROR', 'Sem resposta');
define( 'MAILED__AJAX_ERROR_200', 'Cadastro efetuado com sucesso');
define( 'MAILED__AJAX_ERROR_400', 'Verifique as informações e tente novamente');

/*
	Mailchimp
*/
define( 'MAILED__MAILCHIMP_API_PROTOCOL', 'https://');
define( 'MAILED__MAILCHIMP_API_ADDRESS', 'api.mailchimp.com/3.0');

/*
	Include files
*/
require_once( MAILED__PLUGIN_DIR . 'mailed.class.php' );
require_once( MAILED__PLUGIN_DIR . 'mailed.settings.php' );
require_once( MAILED__PLUGIN_DIR . 'mailed.list.php' );
require_once( MAILED__PLUGIN_DIR . 'mailed.mailchimp.php' );

/*
	Hook & actions
*/
register_activation_hook( __FILE__, array( 'Mailed', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Mailed', 'plugin_deactivation' ) );
register_uninstall_hook( __FILE__, array( 'Mailed', 'plugin_uninstall' ) );
/*
	Start modules
*/
add_action( 'init', array( 'MailedSettings', 'init' ) );
add_action( 'init', array( 'MailedMailchimp', 'init' ) );
add_action( 'init', array( 'Mailed', 'init' ) );