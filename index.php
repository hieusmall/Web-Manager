<?php
/*
Plugin Name: Web Manager
Plugin URI:
Description: Plugin tích hợp nhiều chức năng , một sản phẩm của team COD
Text Domain: webManager
Author: COD TEAM
Version: 1.0
Author URI:
*/


define ( 'WM_PLUGIN_PATH', trailingslashit ( plugin_dir_path ( __FILE__ ) ) );
define ( 'WM_VERSION', '1.0' );
define ( 'BACKEND_ASSETS', 'assets/backend');
define ( 'FRONTEND_ASSETS', 'frontend/backend');
define ( 'TEXT_DOMAIN', 'webManager');
define ( 'WM_PLUGIN_NAME', 'Web-Manager');

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
date_default_timezone_set("Asia/Ho_Chi_Minh");


// Define function
include( WM_PLUGIN_PATH . 'functions.php' );
//include( WM_PLUGIN_PATH . 'inc/functions_v2.php' );
include( WM_PLUGIN_PATH . 'admin-panel.php' );
include( WM_PLUGIN_PATH . 'pageTemplate.php' );
include( WM_PLUGIN_PATH . 'shortcode.php' );
?>