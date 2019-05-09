<?php
/**
 * Name : Function.php
 * @author Hieu Nguyen
 * @email bird7color@gmail.com
 * @version 1.0
 */

$method = $_SERVER['REQUEST_METHOD'];
$query = webManagerLib::queryToArray($_SERVER['QUERY_STRING']);

add_action('init', array( 'webManagerLib', 'init' )); // Main Hook
if ( class_exists('webManagerLib', false) ) return;

class webManagerLib {
    const FORM_TABLE_NAME = 'web_manager_form';
    const POPUP_TABLE_NAME = 'web_manager_popup';
    const BACKEND_TEMPLATE = 'templates/backend/';
    const FRONTEND_TEMPLATE = 'templates/frontend/';
    const PLUGIN_PATH = WM_PLUGIN_PATH;
    const VERSION = WM_VERSION;

    public static function init() {
        // Check login
        if (!is_admin() && !is_user_logged_in()) {
            return "Bạn cần phải đăng nhập";
        }

        // else
        add_action('admin_menu', array( __CLASS__, 'admin_menu' ), 5);

        // Add shortcode
        add_shortcode('wmForm', array(__CLASS__, 'getWMFormShortCode'));
        if (!is_admin()) {
            // add stylesheets for the plugin's backend
//            add_action('admin_enqueue_scripts', array( __CLASS__, 'load_custom_be_styles' ));
            add_action('wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ));
        } else {

        }
    }

    public static function load_custom_be_styles() {
        wp_register_style('webManageBEStyles', plugin_dir_url( __FILE__ ) . 'css/backend.css', false, '0.0.1' );
        wp_enqueue_style( 'webManageBEStyles' );
    }

    public static function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script(self::ID, self::PLUGIN_PATH . 'js/q2w3-fixed-widget.min.js', array('jquery'), self::VERSION, true);
    }

    public static function admin_menu() {
        add_menu_page('Web Manager',
            'Web Manager', 'manage_options',
            'webManagerGeneral', array(__CLASS__, 'webManagerGeneral') ,
            'dashicons-chart-line', 4 );

        add_submenu_page( 'webManagerGeneral', 'Form',
            'Form', 'manage_options',
            'webManagerForm', array(__CLASS__, 'webManagerForm') );

        add_submenu_page( 'webManagerGeneral', 'Popup',
            'Popup', 'manage_options',
            'webManagerPopup', array(__CLASS__, 'webManagerPopup') );
    }

    public static function webManagerGeneral() {
        include (self::PLUGIN_PATH . self::BACKEND_TEMPLATE . 'general.php');
    }

    public static function webManagerForm() {
        include (self::PLUGIN_PATH . self::BACKEND_TEMPLATE . 'form.php');
    }

    public static function getWMFormShortCode($att, $content) {
        $html = '<form class="wm-form">
                    <div class="form-group">
                        <label for="wmFieldName">Họ tên</label>
                        <input type="text" class="form-control" name="name" id="wmFieldName" placeholder="Điền tên">
                    </div>
                    <div class="form-group">
                        <label for="wmFieldEmail">Email</label>
                        <input type="email" class="form-control" name="email" id="wmFieldEmail" placeholder="Điền Email">
                    </div>
                    <div class="form-group">
                        <label for="wmFieldPhone">Số điện thoại</label>
                        <input type="text" class="form-control" name="phone" id="wmFieldPhone" placeholder="Điền tên">
                    </div>
                    <div class="form-group">
                        <label for="wmFieldNote">Bạn cần hỗ trợ về dịch vụ gì</label>
                        <textarea name="note" id="wmFieldNote" cols="30" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>';
        return $html;
    }

    public static function webManagerPopup() {
        include (self::PLUGIN_PATH . self::BACKEND_TEMPLATE . 'popup.php');
    }

    public static function queryToArray($qry)
    {
        $result = array();
        //string must contain at least one = and cannot be in first position
        if(strpos($qry,'=')) {

            if(strpos($qry,'?')!==false) {
                $q = parse_url($qry);
                $qry = $q['query'];
            }
        }else {
            return false;
        }

        foreach (explode('&', $qry) as $couple) {
            list ($key, $val) = explode('=', $couple);
            $result[$key] = $val;
        }

        return empty($result) ? false : $result;
    }

    public static function wmNewForm() {

    }

    /*public static function checkTheStorage() {
        if ( !function_exists( 'maybe_create_table' ) ) {
            require_once ABSPATH . '/wp-admin/install-helper.php';
        }
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // Database table name.
        $tableFormName = array(
            "name" => self::FORM_TABLE_NAME,
            "sql" => ""
        );
        $tablePopupName = self::POPUP_TABLE_NAME;
        $arr = array($tableFormName, $tablePopupName);

        $response = array();

        foreach ($arr as $tableName) {
            $safe_table_name = "khachhang";
            $tableName = $wpdb->prefix . $tableName;
            // Tạo bảng nếu chưa có
            $create_sql = "
                CREATE TABLE IF NOT EXISTS $tableName (
                  `id_form` bigint(20) NOT NULL AUTO_INCREMENT,
                  `ho_ten` varchar(255) NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `sodienthoai` int(15) NOT NULL,
                  `loi_nhan` text NOT NULL,
                  `dichvu` varchar(255) NULL,
                  `tinh_trang` boolean NOT NULL,
                  `thoi_gian` timestamp NOT NULL,
                  PRIMARY KEY (`id_form`)
                ) ENGINE = InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
              ";
            dbDelta( $create_sql );
        }

        // Create database table SQL.
        $create_ddl = '';
    }*/
}