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
    const ID = 'webManager';
    const FORM_TABLE_NAME = 'wm_form';
    const POPUP_TABLE_NAME = 'wm_popup';
    const TICKET_TABLE_NAME = 'wm_ticket';

    const BACKEND_TEMPLATE = 'templates/backend/';
    const FRONTEND_TEMPLATE = 'templates/frontend/';
    const PLUGIN_PATH = WM_PLUGIN_PATH;
    const ASSET = 'assets/';
    const BACKEND_ASSET = self::ASSET . 'backend/';
    const FRONTEND_ASSET = self::ASSET . 'frontend/';
    const VERSION = WM_VERSION;

    const ROUTES = [
        'newTicket', 'listForm' , 'newForm','readForm'
    ];


    const TO_CARESOFT_NOW_ON = 'on';
    const TO_CARESOFT_NOW_OFF = 'off';
    const FORM_TO_CARESOFT_CHOICE = array(
        self::TO_CARESOFT_NOW_ON => 'on',
        self::TO_CARESOFT_NOW_OFF => 'off'
    );


    public static function init() {
        // Check login
        if (!is_admin() && !is_user_logged_in()) {
            return "Bạn cần phải đăng nhập";
        }

        // If login
        add_action('admin_menu', array( __CLASS__, 'admin_menu' ), 5);
        // add stylesheets for the plugin's backend
        add_action('admin_enqueue_scripts', array( __CLASS__, 'load_admin_custom_be_styles' ));
//        add_action('wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend_scripts' ));
        add_action('wp_footer', array(__CLASS__, 'enqueue_frontend_scripts'));

        // Add shortcode
        add_shortcode('wmForm', array(__CLASS__, 'getWMFormShortCode'));
        add_shortcode('wmPopup', array(__CLASS__, 'getWMPopupShortCode'));

        foreach (self::ROUTES as $action) {
            $fn =  $action . 'API';
            add_action( 'wp_ajax_'.$action, array(__CLASS__, $fn) );
            add_action( 'wp_ajax_nopriv_'.$action, array(__CLASS__, $fn) );
        }
    }

    public static function load_admin_custom_be_styles() {
        wp_register_style('webManageBEStyles', plugin_dir_url(__FILE__) . self::BACKEND_ASSET . 'css/wm_backend.css', false, '0.0.1' );
        wp_enqueue_style( 'webManageBEStyles' );
        wp_enqueue_script('jquery');
        wp_enqueue_script(self::ID, plugin_dir_url(__FILE__) . self::BACKEND_ASSET . 'js/wm_backend.js', array('jquery'), self::VERSION, true);
    }

    public static function enqueue_frontend_scripts() {
        wp_register_style('webManageFEStyles', plugin_dir_url(__FILE__) . self::FRONTEND_ASSET . 'css/wm_style.css', true, '0.0.1' );
        wp_enqueue_style( 'webManageFEStyles' );
        wp_enqueue_script('webManageFEScript');
        wp_enqueue_script(self::ID, plugin_dir_url(__FILE__) . self::FRONTEND_ASSET . 'js/wm_app.js', array('jquery'), self::VERSION, true);
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
        $form_id = isset($att['form_id']) && !is_null($att['form_id']) && (int)$att['form_id'] > 0 ? $att['form_id'] : false;
        if (!$form_id) {
            $formHtml = "Oops";
        } else {
            $formStr = '';
            // Get this form
            self::wmReadForm($form_id, function ($err, $form) use (&$formStr, &$formHtml) {
                if (!$err && $form) {
                    $formStr = self::getFETemplate((array)$form, 'form');
                }

                $formHtml = htmlspecialchars_decode($formStr);
            });
        }
        return $formHtml;
    }

    public static function getWMPopupShortCode($att, $content) {
        $popup_id = isset($att['popup_id']) && !is_null($att['popup_id']) && (int)$att['popup_id'] > 0 ? $att['popup_id'] : false;
        if (!$popup_id) {
            $popupHtml = "Pop Oops";
        } else {
            $popupStr = htmlspecialchars(file_get_contents(self::PLUGIN_PATH . self::FRONTEND_ASSET . 'popup.html'));
            $popupHtml = htmlspecialchars_decode($popupStr);
        }

        return $popupHtml;
    }

    public static function getFETemplate($arr,$template) {
        $hardStr = htmlspecialchars(file_get_contents(self::PLUGIN_PATH . self::FRONTEND_ASSET  . $template . '.html'));
        $str = self::interpolate($hardStr, $arr);
        return $str;
    }

    public static function interpolate($str, $data) {
        $str = gettype($str) == 'string' && strlen($str) > 0 ? $str : '';
        $data = gettype($data) == 'array' && !is_null($data) ? $data : array();

        // For each key in the data object, insert its value into the string at the corresponding placeholder
        foreach($data as $key => $replace){
            $find = '{'.$key.'}';
            $str = str_replace($find,$replace, $str);
        }
        return $str;
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

    public static function wmNewForm($formArr, $callback) {
        global $wpdb;
        $formTable = $wpdb->prefix . self::FORM_TABLE_NAME;
        $result =  $wpdb->insert( $formTable, $formArr);

        if ($result) {
            $callback(false);
        } else {
            $callback("Cannot create new Form");
        }

    }

    public static function wmReadForm($form_id, $callback) {
        $form_id = isset($form_id) && !is_null($form_id) && (int)$form_id > 0 ? $form_id : false;
        if (!$form_id) {
            $callback("Mising some required field");
        } else {
            global $wpdb;
            $tableName =  $wpdb->prefix . self::FORM_TABLE_NAME;
            $result = $wpdb->get_results( "SELECT * FROM {$tableName} WHERE form_id = {$form_id} LIMIT 1", OBJECT );
            $form = null;
            if ($result) $form = $result[0];
            if (!$form) {
                $callback("Cannot get this form", null);
            } else {
                $callback(false, $form);
            }
        }
    }

    public static function wmUpdateForm($form_id, $callback) {

    }


    public static function listFormAPI() {
        global $wpdb;
        $tableName = $wpdb->prefix . self::FORM_TABLE_NAME;

        $results = $wpdb->get_results("SELECT * FROM {$tableName}", OBJECT );
        if (!$results) {
            wp_send_json_error("Cannot find list form", 400);
        } else {
            wp_send_json_success($results);
        }
        die();
    }

    public static function readFormAPI() {
        $form_id = isset($_REQUEST['form_id']) && in_array(gettype($_REQUEST['form_id']), ["string", "number"]) && (int)$_REQUEST['form_id'] > 0 ? (int)$_REQUEST['form_id'] : false;
        if (!$form_id) {
            wp_send_json_error('Mising some required field', 401);
        } else {
            self::wmReadForm($form_id, function ($err, $form) {
                if (!$err && $form) {
                    wp_send_json_success($form);
                } else {
                    wp_send_json_error('Cannot find this form', 405);
                }
            });
        }
        die();
    }

    public static function newFormAPI() {
        $form = isset($_REQUEST['form']) && is_array($_REQUEST['form']) && count($_REQUEST['form']) > 0 ? $_REQUEST['form'] : [] ;
        if (!$form) {
            wp_send_json_error('Missing required field', 401);
        } else {
            $title = isset($form['title']) && is_string($form['title']) && strlen($form['title']) > 0 ? $form['title'] : false;

            $directional = isset($form['directional']) && is_string($form['directional']) ? $form['directional'] : null;
            $to_caresoft_now = isset($form['to_caresoft_now']) && in_array($form['to_caresoft_now'], ['on','off']) ? $form['to_caresoft_now'] : 'off' ;
            $caresoft_id = isset($form['caresoft_id']) && in_array(gettype($form['caresoft_id']), ['number', 'string']) ? $form['caresoft_id'] : null;

            if (!$title) {
                wp_send_json_error('Missing required field', 402);
            } else {
                $newForm = array(
                    'title' => $title,
                    'directional' => $directional,
                    'to_caresoft_now' => $to_caresoft_now,
                    'caresoft_id' => $caresoft_id
                );

                self::wmNewForm($newForm, function ($err) {
                    if (!$err) {
                        wp_send_json_success(true);
                    } else {
                        wp_send_json_error('Cannot create new form', 405);
                    }
                });
            }
        }
        die();
    }

    public static function updateFormAPI() {
        $form_id = isset($_REQUEST['form_id']) && in_array(gettype($_REQUEST['form_id']), ["string", "number"]) && (int)$_REQUEST['form_id'] > 0 ? (int)$_REQUEST['form_id'] : false;
        if (!$form_id) {
            wp_send_json_error('Mising some required field', 401);
        } else {
            self::wmReadForm($form_id, function ($err, $formData) use (&$form_id) {
                if (!$err && $formData) {
                    self::wmUpdateForm($form_id, function ($err) {
                        if (!$err) {
                            wp_send_json_success(true);
                        } else {
                            wp_send_json_error('Cannot update this form', 405);
                        }
                    });
                } else {
                    wp_send_json_error('Cannot find this form', 402);
                }
            });
        }
        die();
    }


    public static function newTicketAPI() {
        //do bên js để dạng json nên giá trị trả về dùng phải encode
        $ticket = isset($_REQUEST['ticket']) && gettype($_REQUEST['ticket']) == 'array' && count($_REQUEST['ticket']) > 0 ? $_REQUEST['ticket'] : false;
        if (!$ticket) {
            wp_send_json_error('Unknowwn', 401);
        } else {
            $name = isset($ticket['name']) && gettype($ticket['name']) == "string" && strlen(trim($ticket['name'])) > 0 ? trim($ticket['name']) : false;
            $email = isset($ticket['email']) && gettype($ticket['email']) == "string" && strlen(trim($ticket['email'])) ? $ticket['email'] : false;
            $phone = isset($ticket['phone']) && gettype($ticket['phone']) == "string" && strlen(trim($ticket['phone'])) > 9 && strlen(trim($ticket['phone'])) < 15 ? $ticket['phone'] : false;
            $note = isset($ticket['note']) && gettype($ticket['note']) == "string" && strlen(trim($ticket['note'])) > 0 ? $ticket['note'] : false ;
            $detail = isset($ticket['detail']) && gettype($ticket['detail']) == 'array' && count($ticket['detail']) > 0 ? $ticket['detail'] : false;
            $form_id = isset($ticket['form_id']) && !is_null($ticket['form_id']) && (int)$ticket['form_id'] > 0 ? (int)$ticket['form_id'] : false;
            $time =  date('Y-m-d H:i:s');

            if ($form_id && $name && $phone) {
                // Check this form and send to caresoft
                $newTicket = array(
                    'name' => $name,
                    'phone' => $phone,
                    'created_at' => $time,
                    'updated_at' => $time,
                    'form_id' => $form_id
                );
                if ($email) $newTicket['email'] = $email;
                if ($note) $newTicket['note'] = $note;
                if ($detail) $newTicket['detail'] = json_encode($detail);

                self::wmReadForm($form_id, function ($err, $formData) use (&$newTicket) {
                    if (!$err && $formData) {
                        // Insert new ticket to database
                        global $wpdb;
                        $ticketTable = $wpdb->prefix . self::TICKET_TABLE_NAME;

                        // Check and Send data to CareSoft
                        $checkToCareSoftNow = $formData->to_caresoft_now == self::TO_CARESOFT_NOW_ON;
                        if ($checkToCareSoftNow) {
                            $ticketDetail = isset($newTicket['detail']) && !is_null($newTicket['detail']) ? json_decode($newTicket['detail']) : "";
                            $title = $ticketDetail->origin . ' - '. $newTicket['name'] . ' - '. $ticketDetail->orgin . $ticketDetail->pathname . ' - ' . $formData->title ;
                            $ticketComment = $newTicket['note'] ? $newTicket['note'] : "";
                            $email = $newTicket['email'] ? $newTicket['email'] : null;
                            $name = $newTicket['name'];
                            $phone = $newTicket['phone'];
                            $caresoft_id = isset($formData->caresoft_id) ? $formData->caresoft_id : null;
                            $options = array($title, $ticketComment, $email, $phone, $name, $caresoft_id);
//                            $ticketCareSoft = self::sendTicketToCareSoft($options);
//
//                            // If can't send to caresoft
//                            if (!$ticketCareSoft) {
//                                // if not find ticketCarsoft
//
//                            } else {
//                                $newTicket['caresoft_ticket'] = json_encode($ticketCareSoft);
//                            }
                        }

                        $result =  $wpdb->insert( $ticketTable, $newTicket);
                        if ($result) {
                            wp_send_json_success(true);
                        } else {
                            wp_send_json_error("Cannot create new ticket",405);
                        }
                    } else {
                        wp_send_json_error("Cannot find this form" , 403);
                    }
                });
            } else {
                wp_send_json_error('Missing require field', 402);
            }
        }
        die();
    }


    public function sendTicketToCareSoft($options) {
        $options = isset($options) && !is_null($options) && gettype($options) == 'array' ? $options : false;
        if (!$options)
            return false;

        list($title , $ticket_comment, $email, $phone, $username, $caresoft_id) = $options;

        $caresoft_id = isset($caresoft_id) && !is_null($caresoft_id) && (int)$caresoft_id > 0 ? $caresoft_id : 42124;
        $title = isset($title) && gettype($title) == "string" && strlen($title) > 0 ? $title : false;
        $ticket_comment = isset($ticket_comment) && gettype($ticket_comment) == "string" && strlen($ticket_comment) > 0 ? $ticket_comment : false;
        $email = isset($email) && !is_null($email) && strlen($email) > 0 ? $email : "";
        $phone = isset($phone) && !is_null($phone) && strlen($phone) > 9 && strlen($phone) < 15 ? $phone : false;
        $email = isset($email) && !is_null($email) && strlen($email) > 0 ? $email : false;
        $username = isset($username) && !is_null($username) && strlen($username) > 0 ? $username : false;

        if ($title && $username && $phone) {
            $urlSend = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets";
            $agent = self::getAgentsAssignee();
            $custom_field = '{"id": "3406", "value": "41875"},{"id": "1448", "value": "41890"},{"id": "1416", "value": "'.$caresoft_id.'"}';
            $postStr = '{"ticket": {"ticket_subject": "'.$title.'","ticket_comment":  "'.$ticket_comment.'","email": "'.$email.'","phone": "'.$phone.'","username": "'.$username.'","ticket_priority": "Normal", "service_id" : "950022","assignee_id": "'.$agent.'","custom_fields": ['.$custom_field.']}}';
            $sendResult = json_decode(self::sendPostData($urlSend, $postStr));
            $success = isset($sendResult->code) && $sendResult->code == 'ok' ? true : false;
            $ticketCaresoft = isset($sendResult->ticket) && !is_null($sendResult->ticket) && gettype($sendResult->ticket) == 'object' ? $sendResult->ticket : [];
            if ($success && $ticketCaresoft) {
                return $ticketCaresoft;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getAgentsAssignee() {
        $urlGet = "https://api.caresoft.vn/tmvngocdung/api/v1/agents";
        $resultGet = self::getPostData($urlGet);
        $argsGet = json_decode($resultGet,true);
        $agents = $argsGet["agents"];
        $ids = array();
        $ex = array(15413222, 16185696,16595190,15594858,16650858,19196733,19831065,19833102,19833687,19834329,19834410,22480929,23943516,24881607,24882753,24883884);
        foreach ($agents as $val) {
            if (!in_array((int)$val['id'], $ex)) {
                array_push($ids,(int)$val['id']);
            }
        }
        $rand_keys = array_rand($ids);
        $id = $ids[$rand_keys];
        return $id;
    }

    public static function getPostData($url) {
        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_CAINFO, '/etc/ssl/cacert.pem');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer 8IQwZ6_shBeMuh0"));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function sendPostData($url, $post){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer 8IQwZ6_shBeMuh0"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function postContact($apikey, $campaignid, $fullname, $email, $addcontacturl){
        $data = array (
            'name' => $fullname,
            'email' => $email,
            'campaign' => array('campaignId'=>$campaignid),
            'ipAddress'=>  $_SERVER['REMOTE_ADDR'],
        );
        $data_string = json_encode($data);
        $ch = curl_init($addcontacturl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-Auth-Token: api-key '.$apikey,
            )
        );

        $result = curl_exec($ch); // Print this If you want to verfify
        $state_result = json_decode($result);
    }

    public static function getContact($apikey, $email, $getcontacturl){
        $chmmn = curl_init($getcontacturl );
        curl_setopt($chmmn, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($chmmn, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chmmn, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-Auth-Token: api-key '.$apikey,
            )
        );
        $resultmn = curl_exec($chmmn);
        $resultmn = json_decode($resultmn);
        return $resultmn;
    }

    public static function dateTimeNow() {
        return date('Y-m-d H:i:s');
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