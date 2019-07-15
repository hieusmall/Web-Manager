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
    protected $query;

    const ID = 'webManager';
    const FORM_TABLE_NAME = 'wm_form';
    const POPUP_TABLE_NAME = 'wm_popup';
    const TICKET_TABLE_NAME = 'wm_ticket';
    const YOUTUBE_TABLE_NAME = 'wm_youtube_v3';
    const CS_ANGENT_TABLE_NAME = 'wm_cs_agent';
    const AB_CONTENT_TABLE_NAME = 'wm_ab_content';

    const BACKEND_TEMPLATE = 'templates/backend/';
    const FRONTEND_TEMPLATE = 'templates/frontend/';
    const PLUGIN_PATH = WM_PLUGIN_PATH;
    const PLUGIN_NAME = WM_PLUGIN_NAME;
    const ASSET = 'assets/';
    const BACKEND_ASSET = self::ASSET . 'backend/';
    const FRONTEND_ASSET = self::ASSET . 'frontend/';
    const VENDOR_ASSET = self::ASSET . 'vendor/';

    const VERSION = WM_VERSION;

    /*const PAGES = ["webManagerGeneral","webManagerForm","webManagerPopup","webManagerTicket"];
    const ROUTES = ['newTicket','listTicket', 'ticketsDTableFilterSource', 'ticketsDataTable', 'ticketToCareSoftNow', 'ticketCharts', 'readTicket', 'deleteTicket', 'updateTicket',
        'listForm' , 'newForm','readForm', 'updateForm', 'deleteForm',
        'listPopup' , 'newPopup','readPopup', 'updatePopup', 'deletePopup'];*/
    const PAGES = ["webManagerGeneral","webManagerForm","webManagerPopup","webManagerTicket","webManagerAfBeContent"];
    const ROUTES = ['newTicket','listTicket', 'ticketsDTableFilterSource', 'ticketsDataTable',
        'ticketToCareSoftNow', 'ticketCharts', 'readTicket', 'deleteTicket', 'updateTicket',
        'listForm' , 'newForm','readForm', 'updateForm', 'deleteForm',
        'listPopup' , 'newPopup','readPopup', 'updatePopup', 'deletePopup'];

    const ROUTES_GENERAL = ["allTicketChartDonut"];

    const TO_CARESOFT_NOW_ON = 'on';
    const TO_CARESOFT_NOW_OFF = 'off';
    const FORM_TO_CARESOFT_CHOICE = array(
        self::TO_CARESOFT_NOW_ON => 'on',
        self::TO_CARESOFT_NOW_OFF => 'off'
    );


    public function __construct()
    {
        $this->query = self::queryToArray($_SERVER['QUERY_STRING']);
    }


    /*public static function setUpStorage() {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $wm_ticket_table = 'SET NAMES utf8mb4;
                            SET FOREIGN_KEY_CHECKS = 0;
                            CREATE TABLE IF NOT EXISTS `'.$prefix.'wm_ticket`  (
                              `ticket_id` bigint(20) NOT NULL AUTO_INCREMENT,
                              `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                              `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                              `note` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                              `email` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                              `detail` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                              `created_at` datetime(0) NULL DEFAULT NULL,
                              `updated_at` datetime(0) NULL DEFAULT NULL,
                              `form_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
                              `caresoft_ticket` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                              `branchs` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                              `ticket_data_custom` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                              `ticket_data` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                              PRIMARY KEY (`ticket_id`) USING BTREE
                            ) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;
                            SET FOREIGN_KEY_CHECKS = 1;';

        $wm_form_table = "SET NAMES utf8mb4;
                        SET FOREIGN_KEY_CHECKS = 0;
                        CREATE TABLE IF NOT EXISTS `".$prefix."wm_form`  (
                          `form_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID form để phân loại',
                          `title` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Tiêu đề của form',
                          `to_caresoft_now` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Sau khi submit đưa lên caresoft luôn hoặc không',
                          `form_custom_template` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                          `directional` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Url chuyển hướng ',
                          `created_at` datetime(0) NULL DEFAULT NULL COMMENT 'Ngày tạo',
                          `updated_at` datetime(0) NULL DEFAULT NULL COMMENT 'Ngày cập nhật',
                          `caresoft_id` bigint(10) UNSIGNED NULL DEFAULT NULL COMMENT 'ID phân loại nguồn lead Caresoft',
                          `nguon_phieu` bigint(10) UNSIGNED NULL DEFAULT NULL COMMENT 'ID phân loại nguồn lead Caresoft',
                          `chi_tiet_nguon_phieu` bigint(10) UNSIGNED NULL DEFAULT NULL COMMENT 'ID phân loại nguồn lead Caresoft',
                          `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                          PRIMARY KEY (`form_id`) USING BTREE
                        ) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;
                        SET FOREIGN_KEY_CHECKS = 1;";

        $wm_popup_table = "SET NAMES utf8mb4;
                        SET FOREIGN_KEY_CHECKS = 0;
                        CREATE TABLE IF NOT EXISTS `".$prefix."wm_popup`  (
                          `popup_id` bigint(20) NOT NULL AUTO_INCREMENT,
                          `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                          `bg_image_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                          `form_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
                          `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                          `direction_background` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                          `delay_show_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                          `created_at` datetime(0) NULL DEFAULT NULL,
                          `updated_at` datetime(0) NULL DEFAULT NULL,
                          PRIMARY KEY (`popup_id`) USING BTREE
                        ) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;
                        SET FOREIGN_KEY_CHECKS = 1;";

        $wm_cs_angent_table = "SET NAMES utf8mb4;
                            SET FOREIGN_KEY_CHECKS = 0;
                            DROP TABLE IF EXISTS `".$prefix."wm_cs_agent`;
                            CREATE TABLE `".$prefix."wm_cs_agent`  (
                              `id` bigint(20) UNSIGNED NULL DEFAULT NULL,
                              `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                              `phone_no` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                              `agent_id` int(30) UNSIGNED NULL DEFAULT NULL,
                              `group_id` int(10) UNSIGNED NULL DEFAULT NULL,
                              `group_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                              `role_id` tinyint(10) NULL DEFAULT NULL,
                              `created_at` datetime(0) NULL DEFAULT NULL,
                              `updated_at` datetime(0) NULL DEFAULT NULL
                            ) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;
                            
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (15413222, '0', '0988750275', 3720, 7734, 'SEO', 1, '2017-11-08 10:05:07', '2019-05-13 15:31:56');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (15595552, '0', '0905211562', 3716, 7730, 'Tư vấn', 2, '2017-11-13 14:23:58', '2019-05-15 08:04:51');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (16185586, '0', '0778787094', 3717, 7730, 'Tư vấn', 2, '2017-11-29 15:26:10', '2019-05-09 12:41:07');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (16185618, '0', '0938033342', 3718, 7730, 'Tư vấn', 2, '2017-11-29 15:26:39', '2019-05-12 08:06:45');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (16185632, '0', '0903106166', 3719, 7730, 'Tư vấn', 2, '2017-11-29 15:26:58', '2019-05-16 14:40:55');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (16185650, '0', '0393789224', 3787, 7730, 'Tư vấn', 2, '2017-11-29 15:27:20', '2019-02-27 11:00:08');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (16185662, '0', '0913808913', 3788, 7730, 'Tư vấn', 2, '2017-11-29 15:27:43', '2019-05-08 10:42:25');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (16644168, '0', NULL, 3789, 7730, 'Tư vấn', 2, '2017-12-12 19:40:55', '2019-04-25 14:35:35');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (16650858, '0', '0902491623', 3790, 7730, 'Tư vấn', 2, '2017-12-13 08:12:14', '2019-05-12 07:52:41');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (16650900, '0', '0387530124', 3791, 7730, 'Tư vấn', 2, '2017-12-13 08:14:20', '2019-05-08 10:41:37');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (19193307, '0', '0906993790', 5476, 8445, 'OFFLINE', 2, '2018-01-25 09:24:19', '2019-02-23 14:33:55');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (19831065, '0', '0981182410', 3794, 7732, 'CSKH', 2, '2018-02-03 17:00:12', '2019-05-15 17:25:53');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (19833102, '0', '0335136839', 3795, 7732, 'CSKH', 2, '2018-02-03 17:37:10', '2019-04-29 08:20:20');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (19833687, '0', '0932717322', 3797, 7732, 'CSKH', 2, '2018-02-03 17:48:26', '2019-04-25 14:39:09');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (19834329, '0', '02866720777', 3796, 7732, 'CSKH', 2, '2018-02-03 18:03:59', '2019-04-28 09:05:16');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (19834410, '0', '02866720666', 3798, 7732, 'CSKH', 2, '2018-02-03 18:05:54', '2019-05-14 10:54:50');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (21621747, '0', '0988016901', 6327, 10425, 'CRM', 2, '2018-03-08 11:04:05', '2019-03-25 15:40:03');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (22109397, '0', '0966032703', 5477, 8445, 'OFFLINE', 2, '2018-03-15 15:22:42', '2019-04-25 14:45:57');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (22480929, '0', '0938837379', 3799, 7732, 'CSKH', 2, '2018-03-21 11:25:42', '2019-01-24 12:37:17');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (23943516, '0', '0903870721', 4641, 7732, 'CSKH', 2, '2018-03-29 17:13:46', '2019-05-16 11:14:33');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (24881607, '0', '02822001001', 3793, 7732, 'CSKH', 2, '2018-04-13 15:37:50', '2019-04-23 08:52:59');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (24882753, '0', '02822003003', 4643, 7732, 'CSKH', 2, '2018-04-13 15:49:17', '2019-04-25 11:32:12');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (24883884, '0', '02822005005', 4644, 7732, 'CSKH', 2, '2018-04-13 16:01:45', '2019-04-26 08:30:32');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (26273483, '0', '0984352069', 4645, 7730, 'Tư vấn', 2, '2018-05-04 15:08:39', '2019-04-28 13:03:13');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (27101339, '0', '0974992827', 6326, 10425, 'CRM', 2, '2018-05-16 14:32:04', '2019-03-20 15:19:12');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (27237800, '0', '0963233642', 4647, 7730, 'Tư vấn', 2, '2018-05-18 15:50:20', '2019-04-25 14:30:46');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (27932594, '0', '0703912909', 4864, 7730, 'Tư vấn', 2, '2018-05-26 13:47:00', '2019-03-29 09:48:01');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (27933860, '0', '0932779550', 4865, 7730, 'Tư vấn', 2, '2018-05-26 14:06:40', '2019-04-16 16:17:51');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (29128598, '0', '0966073907', 5469, 8445, 'OFFLINE', 2, '2018-06-13 11:14:06', '2018-12-27 14:55:35');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (29232044, '0', '0373070711', 4866, 7730, 'Tư vấn', 2, '2018-06-14 16:04:16', '2019-04-25 14:29:52');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (30119395, '0', '0283868006', 4868, 7732, 'CSKH', 2, '2018-06-26 15:16:41', '2019-05-14 11:02:44');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (31761056, '0', '0902630789', 4870, 7730, 'Tư vấn', 2, '2018-07-20 09:51:54', '2019-05-15 10:58:26');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (32295564, '0', '0385506131', 1063, 7730, 'Tư vấn', 2, '2018-07-24 17:07:16', '2019-03-20 08:16:48');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (33254085, '0', '0395447396', 1065, 7716, 'Default Group', 2, '2018-08-04 10:58:00', '2019-05-15 12:06:28');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (35505124, '0', '0936241032', 5470, 7716, 'Default Group', 2, '2018-08-23 11:30:28', '2019-05-16 19:13:21');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (35509019, '0', '0908912751', 5471, 7716, 'Default Group', 2, '2018-08-23 11:54:52', '2019-05-16 16:27:38');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (35559324, '0', '0906373634', 5478, 8445, 'OFFLINE', 2, '2018-08-23 17:04:51', '2019-03-16 14:29:42');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (36262305, '0', '0965375050', 5472, 7716, 'Default Group', 2, '2018-08-29 08:27:23', '2019-05-16 08:14:40');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (37097574, '0', '0946303776', 5474, 7716, 'Default Group', 2, '2018-09-08 08:23:44', '2019-05-17 08:17:59');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (37490466, '0', '0724749654', 5473, 7716, 'Default Group', 2, '2018-09-12 10:53:12', '2019-05-17 08:11:40');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (41534947, '0', '0932726267', 5479, 8445, 'OFFLINE', 2, '2018-10-25 10:02:08', '2019-05-10 15:58:55');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (41535217, '0', '0909692632', 6321, 8445, 'OFFLINE', 2, '2018-10-25 10:04:11', '2019-01-09 08:31:49');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (44548172, '0', '0979036446', 6322, 7732, 'CSKH', 2, '2018-11-22 16:00:18', '2019-05-04 15:09:54');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (58939375, '0', '0909123456', 4646, 7732, 'CSKH', 2, '2019-03-20 14:01:31', '2019-04-25 14:34:26');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (58968877, '0', '0909321654', 6323, 8445, 'OFFLINE', 2, '2019-03-20 14:26:09', '2019-03-25 14:54:08');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (58976149, '0', '0979013101', 6324, 10425, 'CRM', 2, '2019-03-20 15:08:06', '2019-05-07 15:39:43');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (58976491, '0', '0902969623', 6329, 10425, 'CRM', 2, '2019-03-20 15:09:56', '2019-03-20 15:09:56');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (58976632, '0', '0989017801', 6325, 10425, 'CRM', 2, '2019-03-20 15:10:51', '2019-03-20 15:10:51');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (67681036, '0', '0947772037', 6328, 7732, 'CSKH', 2, '2019-04-22 14:44:15', '2019-05-04 10:54:25');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (67681348, '0', '0972568261', 6330, 7732, 'CSKH', 2, '2019-04-22 14:45:53', '2019-05-15 15:28:49');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (67681753, '0', '0336564024', 7707, 7732, 'CSKH', 2, '2019-04-22 14:48:03', '2019-05-15 09:36:11');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (67682638, '0', '0852887177', 7708, 7732, 'CSKH', 2, '2019-04-22 14:52:48', '2019-05-10 16:06:52');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (67682731, '0', '0933219295', 7709, 7732, 'CSKH', 2, '2019-04-22 14:53:14', '2019-05-16 17:59:19');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (15594858, '0', '0911240024', 3715, 7730, 'Tư vấn', 4, '2017-11-13 14:20:13', '2019-05-17 08:22:27');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (16185696, '0', '0938768379', 3792, 7732, 'CSKH', 4, '2017-11-29 15:28:22', '2019-05-15 11:05:37');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (27745625, '0', '0902656518', 5475, 8445, 'OFFLINE', 4, '2018-05-24 08:46:13', '2018-09-21 09:49:24');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (27921668, '0', '0906313635', 4648, 8445, 'OFFLINE', 4, '2018-05-26 10:49:36', '2019-01-21 16:29:27');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (33812661, '0', '0777800554', 1066, 8445, 'OFFLINE', 4, '2018-08-10 09:10:40', '2018-11-23 13:59:57');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (58150540, '0', '0966017901', 4642, 10425, 'CRM', 4, '2019-03-14 09:39:16', '2019-05-15 15:26:12');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (58151458, '0', '0901822969', 1064, 10425, 'CRM', 4, '2019-03-14 09:45:09', '2019-03-19 09:44:02');
                            INSERT INTO `".$prefix."wm_cs_agent` VALUES (31720865, '0', '0704666306', 4867, 7716, 'Default Group', 11, '2018-07-19 16:10:11', '2019-03-26 16:14:52');
                            SET FOREIGN_KEY_CHECKS = 1;";

        $storage = array($wm_ticket_table, $wm_form_table, $wm_popup_table, $wm_cs_angent_table);
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        foreach ($storage as $sql) {
            $done = dbDelta( $sql );
        }

    }*/


    public static function init() {
        // Setup storage
        // self::setUpStorage();


//         add_action('wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend_scripts' ));
        add_action('wp_footer', array(__CLASS__, 'enqueue_frontend_scripts'));

        // Add shortcode
        add_shortcode('wmForm', array(__CLASS__, 'getWMFormShortCode'));
        add_shortcode('wmPopup', array(__CLASS__, 'getWMPopupShortCode'));

        foreach (self::ROUTES as $action) {
            $fn =  $action . 'API';
            add_action( 'wp_ajax_'.$action, array(__CLASS__, $fn) );
            add_action( 'wp_ajax_nopriv_'.$action, array(__CLASS__, $fn) );
        }

        // If login
        add_action('admin_menu', array( __CLASS__, 'admin_menu' ), 5);

        $query = webManagerLib::queryToArray($_SERVER['QUERY_STRING']);
        $isPage = isset($query["page"]) && in_array($query['page'] ,self::PAGES) ? true : false;
        if ($isPage) {
            // Check login
            if (!is_admin() && !is_user_logged_in()) {
                return "Bạn cần phải đăng nhập";
            }
            // add stylesheets for the plugin's backend
            add_action('admin_enqueue_scripts', array( __CLASS__, 'load_admin_custom_be_styles' ));
        }


        // After Before content
        add_filter( 'the_content', array( __CLASS__, 'theme_slug_filter_the_content' ));
    }


    function theme_slug_filter_the_content( $content ) {
        $custom_content = "";
        try {
            $item = self::getABContentItem();
            if ($item) {
                $itemContent = $item->content && strlen($item->content) ? $item->content : false;
                $location = is_array($item->location) && count($item->location) ? $item->location : false ;
                if ($location && $itemContent) {
                    foreach ($location as $key => $a) {
                        if (count($location) == 1) {
                            switch ($a) {
                                case "top" :
                                    $custom_content .= $itemContent . $content;
                                    break;
                                case "bottom" :
                                    $custom_content .= $content . $itemContent;
                                    break;
                            }
                        }
                        if (count($location) == 2) {
                            if ($key == 0) {
                                $custom_content .= $itemContent;
                            } else {
                                $custom_content .= $content . $itemContent;
                            }
                        }
                    }
                }
            }
        } catch (Exception $exception) {
            return "";
        }

//        $custom_content .= $content;
        return $custom_content;
    }

    public static function load_admin_custom_be_styles() {

        /*
         * I recommend to add additional conditions just to not to load the scipts on each page
         * like:
         * if ( !in_array('post-new.php','post.php') ) return;
         */
        if ( ! did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }

        wp_enqueue_style('WMBE_bootstrap_style', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'bootstrap/css/bootstrap.min.css', true, self::VERSION );
        wp_enqueue_script('WMBE_popper', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'bootstrap/popper.min.js', array('jquery'), self::VERSION, true);
        wp_enqueue_script('WMBE_bootstrap_scripts', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'bootstrap/js/bootstrap.min.js', array('jquery'), self::VERSION, true);

        wp_enqueue_style('WMBE_apexcharts_style', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'apexcharts/apexcharts.css', true, self::VERSION );
        wp_enqueue_script('WMBE_apexcharts_scripts', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'apexcharts/apexcharts.min.js', array('jquery'), self::VERSION, true);

        wp_enqueue_style('WMBE_fontawesome_style', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . '@fontawesome/css/all.min.css', true, self::VERSION );
        wp_enqueue_style('WMBE_sweetalert_style', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'sweetalert/sweetalert2.min.css', true, self::VERSION );
        wp_enqueue_script('WMBE_sweetalert_scripts', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'sweetalert/sweetalert2.min.js', array('jquery'), self::VERSION, true);


        $query = webManagerLib::queryToArray($_SERVER['QUERY_STRING']);
        $isFormPage = isset($query["currentPage"]) && in_array($query["currentPage"], ['formUpdate', 'formNew']) ? true : false;
        $isListPage = !isset($query["currentPage"]) || in_array($query["currentPage"], ['listForm','listPopup','listTicket']) ? true : false;
        if ($isFormPage) {
            wp_enqueue_script('WMBE_formBuilder', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'form-builder.min.js', array('jquery'), self::VERSION, true);
        }
        if ($isListPage) {

            wp_enqueue_style('WMBE_dataTables_style', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'dataTables/datatables.min.css', true, self::VERSION );
            wp_enqueue_script('WMBE_dataTables_scripts', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'dataTables/datatables.min.js', array('jquery'), self::VERSION, true);
            wp_enqueue_script('WMBE_dataTables_pdfmake', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'dataTables/pdfmake.min.js', array('jquery'), self::VERSION, true);
            wp_enqueue_script('WMBE_dataTables_vfs_fonts', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'dataTables/vfs_fonts.js', array('jquery'), self::VERSION, true);

            wp_enqueue_style('WMBE_bootstrapSelect_style', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'bootstrapSelect/css/bootstrap-select.min.css', true, self::VERSION );
            wp_enqueue_script('WMBE_bootstrapSelect_scripts', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'bootstrapSelect/js/bootstrap-select.min.js', array('jquery'), self::VERSION, true);

            wp_enqueue_script('WMBE_moment_scripts', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'moment.min.js', array('jquery'), self::VERSION, true);
            wp_enqueue_style('WMBE_daterangepicker_style', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'daterangepicker/daterangepicker.css', true, self::VERSION );
            wp_enqueue_script('WMBE_daterangepicker_scripts', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'daterangepicker/daterangepicker.js', array('jquery'), self::VERSION, true);
        }

        wp_enqueue_style('webManageBEStyles', plugin_dir_url(__FILE__) . self::BACKEND_ASSET . 'css/wm_backend.css', false, self::VERSION );
        wp_enqueue_script('webManageBEScripts', plugin_dir_url(__FILE__) . self::BACKEND_ASSET . 'js/wm_backend.js', array('jquery'), self::VERSION, true);

        // Add Code Mirror
        $_wm = [
            "css" => [
                "codeEditor" => wp_enqueue_code_editor(array('type' => 'text/css'))
            ],
            "js" => [
                "codeEditor" => wp_enqueue_code_editor(array('type' => 'text/javascript',
                "file" => plugin_dir_url(__FILE__) . self::BACKEND_ASSET . 'js/afbeContentExtras.js'))
            ],
            "html" => [
                "codeEditor" => wp_enqueue_code_editor(array('type' => 'text/html',
                "file" => plugin_dir_url(__FILE__) . self::BACKEND_ASSET . 'afbeContentExtras.html'))
            ],

        ];
        wp_localize_script('webManageBEScripts', '_wm', $_wm);
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
    }

    public static function enqueue_frontend_scripts() {
        wp_register_style('webManageFEStyles', plugin_dir_url(__FILE__) . self::FRONTEND_ASSET . 'css/wm_style.css', true, '0.0.1' );
        wp_enqueue_style( 'webManageFEStyles' );
        wp_enqueue_script('webManageFEScript');
//        wp_enqueue_script(self::ID . 'popper_app', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'bootstrap/popper.min.js', array('jquery'), self::VERSION, true);
        wp_enqueue_script(self::ID . 'bootstrap_app', plugin_dir_url(__FILE__) . self::VENDOR_ASSET . 'bootstrap/js/bootstrap.min.js', array('jquery'), self::VERSION, true);
        wp_enqueue_script(self::ID . 'main_app', plugin_dir_url(__FILE__) . self::FRONTEND_ASSET . 'js/wm_app.js', array('jquery'), self::VERSION, true);
        wp_localize_script(self::ID . 'main_app', 'wmGlobal', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'pluginsUrl' => plugins_url() . "/" . self::PLUGIN_NAME,
            'vendorAssets' => plugins_url() . "/" . self::PLUGIN_NAME . "/" . self::VENDOR_ASSET
        ));
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

        add_submenu_page( 'webManagerGeneral', 'Ticket',
            'Ticket', 'manage_options',
            'webManagerTicket', array(__CLASS__, 'webManagerTicket') );
        add_submenu_page( 'webManagerGeneral', 'After Before Content',
            'After Before Content', 'manage_options',
            'webManagerAfBeContent', array(__CLASS__, 'webManagerAfBeContent') );
    }


    public static function webManagerGeneral() {
        include (self::PLUGIN_PATH . self::BACKEND_TEMPLATE . 'general.php');
    }


    public static function webManagerTicket() {
        include (self::PLUGIN_PATH . self::BACKEND_TEMPLATE . 'ticket.php');
    }

    public static function webManagerAfBeContent() {
        include (self::PLUGIN_PATH . self::BACKEND_TEMPLATE . 'afbeContent.php');
    }


    public static function wmListTickets($callback) {
        global $wpdb;
        $tableName =  $wpdb->prefix . self::TICKET_TABLE_NAME;
        $result = $wpdb->get_results( "SELECT * FROM {$tableName}", OBJECT );
        $tickets = null;
        $err = false;
        if ($result) {
            $tickets = $result;
            // convert string data
            array_map(function ($obj) {
                $obj->caresoft_ticket = !is_null($obj->caresoft_ticket) ? json_decode($obj->caresoft_ticket) : null;
                $obj->detail = !is_null($obj->detail) ? json_decode($obj->detail) : null;
                $obj->ticket_data_custom = !is_null($obj->ticket_data_custom) ? json_decode($obj->ticket_data_custom) : null;
                return $obj;
            }, $tickets);
        }
        if (!$tickets) $err = "Cannot get this tickets";
        $callback($err, $tickets);
    }

    public static function wmTableTicket() {
        $tickets = [];

        webManagerLib::wmListTickets(function ($err, $list) use (&$tickets) {
            if (!$err && $list) {
                $tickets = $list;
            }
        });

        $thead = '<thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">Chọn toàn bộ</label>
                        <input id="cb-select-all-1" type="checkbox">
                    </td>
                    <th scope="col" class="manage-column column-phone column-primary">
                        <a href="#"><span>Số điện thoại</span></a>
                    </th>
                    <th scope="col" class="manage-column column-name"><span class="wm-text-brand-blue">Họ tên</span></th>
                    <th scope="col" class="manage-column column-email"><span class="wm-text-brand-blue">Email</span></th>
                    <th scope="col" class="manage-column column-note"><span class="wm-text-brand-blue">Ghi Chú</span></th>
                    <th scope="col" class="manage-column column-note">
                        <a href="#">
                            <span>Thời gian</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" class="manage-column column-caresoft"><span class="wm-text-brand-blue">Ticket CareSoft</span></th>
                </tr>
            </thead>';
        $trs = '';
        foreach ($tickets as $key => $ticket) {
            $ticket_id = $ticket->ticket_id;
            $name = $ticket->name;
            $phone = $ticket->phone;
            $note = $ticket->note;
            $email = $ticket->email;
            $created = (new DateTime($ticket->created_at))->format('d-m-Y h:m');
            $caresoft_ticket = !is_null($ticket->caresoft_ticket) ? json_decode($ticket->caresoft_ticket) : false;


            $thCheckBox = '<th scope="row" class="check-column">
                                <label class="screen-reader-text" for="cb-select-'.$ticket_id.'">Chọn '.$name.'</label>
                                <input id="cb-select-'.$ticket_id.'" type="checkbox" name="tickets[]" value="'.$ticket_id.'">
                            </th>';
            $tdTicketPhone = '<td class="phone column-phone has-row-actions column-primary page-phone" data-colname="Số điện thoại">
                                <a class="row-title" href="#"><span class="">'.$phone.'</span></a>
                                <div class="row-actions">
                                    <span class="view"><a href="#">Chi Tiết</a></span> | 
                                    <span class="trash"><a href="#">Delete</a></span> | 
                                    <span class="toCareSoft"><a href="#">Lên CareSoft</a></span>
                                </div>
                            </td>';
            $tdTicketName = '<td class="name column-name"><span class="text-primary">'.$name.'</span></td>';
            $tdTicketEmail = '<td class="email column-email"><span>'.$email.'</span></td>';
            $tdTicketNote = '<td>'.$note.'</td>';
            $tdTicketCreated = '<td class="created column-created"><span class="text-center">'.$created.'</span></td>';
            $tdCareSoft = '<td class="caresoft column-caresoft">'.$caresoft_ticket->ticket_id.'</td>';
            $trs .= '<tr>';
            $trs .= $thCheckBox;
            $trs .= $tdTicketPhone;
            $trs .= $tdTicketName;
            $trs .= $tdTicketEmail;
            $trs .= $tdTicketNote;
            $trs .= $tdTicketCreated;
            $trs .= $tdCareSoft;
            $trs .= '</tr>';
        }
        $tbody = '<tbody>'.$trs.'</tbody>';
        $table = '<table class="wmListFormTable wp-list-table widefat fixed striped posts">
            '.$thead.'
            '.$tbody.'
        </table>';

        return $table;
    }

    public static function listTicketAPI() {
        $tickets = [];
        webManagerLib::wmListTickets(function ($err, $list) use (&$tickets) {
            if (!$err && $list) {
                wp_send_json_success($list);
            } else {
                wp_send_json_error("Cannot find list ticket", 400);
            }
        });
        die();
    }

    public static function ticketsDataTableAPI() {
        header("Content-Type: application/json");
        $request = $_GET;
        $dataTables = $request;
        // Check some data data table
        if (isset($dataTables['action'])) unset($dataTables['action']);
        list($draw, $columns, $order, $start, $length, $search, $form_id, $caresoft_ticket, $startdate, $enddate, $utm_source, $post_id) = array_values($dataTables);

        $order = is_array($order) && count($order) > 0 ? $order : false;
        $search = is_array($search) && count($search) > 0 && isset($search['value']) && trim(strlen($search['value'])) > 0 ? $search : false;
        // Get filter values
        $filterByFormIds = isset($form_id) && !is_null($form_id) && is_array($form_id) && count($form_id) > 0 ? $form_id : false;
        $hasCareSoftTicket = isset($caresoft_ticket) && !is_null($caresoft_ticket) ? $caresoft_ticket : false;
        $startdate = isset($startdate) && !is_null($startdate) && $startdate ? self::dateTimeToYMD($startdate) : false;
        $enddate = isset($enddate) && !is_null($enddate) && $enddate ? self::dateTimeToYMD($enddate) : false;
        $utm_source = isset($utm_source) && is_array($utm_source) ? $utm_source : false;
        $post_id = isset($post_id) && is_array($post_id) ? $post_id : false;

        $isFilter = $filterByFormIds || $hasCareSoftTicket || $utm_source || $post_id || $startdate || $enddate ? true : false;

        global $wpdb;
        $tableTicket = $wpdb->prefix . self::TICKET_TABLE_NAME;
        $recordsTotal = $wpdb->get_results( "SELECT COUNT(*) FROM {$tableTicket} ", OBJECT );
        $filters = array();
        $data = false;

        $query = "select * from $tableTicket";
        if ($isFilter) {
            $query .= " where";

            // If request filter by form id
            if ($filterByFormIds) {
                $ids = join("','", $filterByFormIds);
                $query .= " form_id in ('$ids')";
            }
            if ($hasCareSoftTicket && $filterByFormIds) {
                $query .= ' and';
            }
            if ($hasCareSoftTicket == 'yes') {
                $query .= " caresoft_ticket is not null";
            }
            if ($hasCareSoftTicket == 'no') {
                $query .= " caresoft_ticket is null";
            }
            if ($utm_source) {
                if ($hasCareSoftTicket || $filterByFormIds) {
                    $query .= " and";
                }
                $query .= " (";
                foreach ($utm_source as $k => $source) {
                    if ($k > 0) : $query.= " or"; endif;
                    $query .= " sources like '%utm_source=$source%'";
                }
                $query .= " )";
            }

            if ($post_id) {
                if ($hasCareSoftTicket || $filterByFormIds || $utm_source) {
                    $query .= " and";
                }
                $postIds = join("','",$post_id);
                $query .= " post_id in ('$postIds')";
            }

            if ($startdate || $enddate) {
                if ($filterByFormIds || $hasCareSoftTicket || $utm_source || $post_id) $query .= " and";
                if ($startdate && $enddate) {
                    if ($startdate != $enddate) {
                        $enddateTomorrow = date('Y-m-d',strtotime($enddate . "+1 days"));
                        $query .= " created_at between '$startdate' and '$enddateTomorrow'";
                    } else {
                        $query .= " created_at like '%$startdate%'";
                    }
                } elseif ($startdate) {
                    $query .= " created_at like '%$startdate%'";
                } elseif ($enddate) {
                    $query .= " created_at like '%$enddate%'";
                }
            }
        }


        $queryTotalFiltered = str_replace("*", "count(*) as total_filter", $query);
        $recordsFiltered = $wpdb->get_results( $queryTotalFiltered, OBJECT );
        $recordsFiltered = $recordsFiltered[0]->total_filter;

        if ($search) {
            list($value, $regex) = array_values($search);
            $searchs = ['phone', 'name', 'email'];
            foreach ($searchs as $k => $col) {
                if ($k > 0 ) :
                    $query .= ' or';
                elseif($isFilter) :
                    $query .= " and";
                else :
                    $query .= ' where';
                endif;
                $query .= " $col like '%$value%' ";
            }
        }

        if ($order) {
            $query .= " order by";
            foreach ($order as $item) {
                $index = $item['column'];
                $type = $item['dir'];
                $column = $columns[$index];
                $columnName = $column['name'] && strlen($column['name']) > 0 ? $column['name'] : "created_at" ;
                $query .= " $columnName $type";
            }
        }

        $query .= " limit $length";
        if ($start > 0 && $start <= 10) {
            $query .= " ,$start";
        } else {
            $query .= " offset $start";
        }

        // If has filter action
        $data = $wpdb->get_results( $query, OBJECT );

        // json response data
        $json_data = array(
            "draw" => (int)$draw,
            "recordsTotal" => (int)((array)$recordsTotal[0])['COUNT(*)'],
            "recordsFiltered"=> $recordsFiltered,
            "data" => []
        );

        // Check this data is exist
        if ($data) {
            // convert string data to Object
            $data = array_map(function ($obj) {
                $detail = !is_null($obj->detail) ? json_decode($obj->detail) : null;
                $form_id = $obj->form_id ? $obj->form_id : false;
                $ticket_data = !is_null($obj->ticket_data) ? json_decode($obj->ticket_data) : null;
                $ticket_data_custom = !is_null($obj->ticket_data_custom) ? json_decode($obj->ticket_data_custom) : null;
                $caresoft_ticket = !is_null($obj->caresoft_ticket) ? json_decode($obj->caresoft_ticket) : null;
                $sources = !is_null($obj->sources) ? $obj->sources : null;
                $post = !is_null($obj->post_id) ? get_post($obj->post_id) : null;
                if ($post) {
                    $post->post_url =  esc_url( get_permalink( $obj->post_id ) );
                }


                if ($sources) {
                    parse_str($obj->sources, $sources);
                }

                $form = null;
                self::wmReadForm($form_id, function ($err , $formData) use (&$form) {
                    if (!$err && $formData)
                        $form = $formData;
                });

                $obj->ticket_id = intval($obj->ticket_id);
                $obj->form = $form;
                $obj->detail = $detail;
                $obj->ticket_data = $ticket_data;
                $obj->ticket_data_custom = $ticket_data_custom;
                $obj->caresoft_ticket = $caresoft_ticket;
                $obj->sources = $sources;
                $obj->post = $post;
                return $obj;
            }, $data);
            $json_data["data"] = $data;
        }

        $json_data['input'] = array(
            "draw" => (int)$draw,
            "columns" => $columns,
            "order" => $order,
            "start" => (int)$start,
            "length" => (int)$length,
            "search" => $search,
        );
        if (isset($dataTables["_"]))
            $json_data['input']['_'] = $dataTables["_"];

        wp_send_json($json_data);
        die();
    }

    public static function ticketsDTableFilterSourceAPI() {
        $request = $_GET;
        $code = 400;
        $res = array(
            'success' => false
        );
        global $wpdb;
        $tableName = $wpdb->prefix . self::TICKET_TABLE_NAME;
        $query = "select detail from $tableName where detail is not null";
        $details = $wpdb->get_results($query, OBJECT);
        $filters = array_map(function($obj) {
            $queryString = json_decode($obj->detail)->search;
            return $queryString;
        }, $details);
        $filters = array_filter($filters, function($search) {
            return $search;
        });
        $filters = array_values(array_unique($filters));
        $res['success'] = true;
        $res['data'] = $filters;
        $code = 200;
        wp_send_json($res, $code);
        die();
    }

    public static function ticketDataTableFormFilter() {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TICKET_TABLE_NAME;
        $query = "select distinct form_id from $tableName where form_id is not null";
        $formIds = $wpdb->get_results($query, OBJECT);
        $options = "";

        foreach ($formIds as $obj) {
            $formId = $obj->form_id;
            self::wmReadForm($formId, function ($err, $formData) use (&$options) {
                if (!$err && $formData) {
                    $options .= "<option value='$formData->form_id'>$formData->name</option>";
                }
            });
        }

        $formFilterHtml = '<div class="form-group col-md-3">
            <label for="ticketFilterByForm">Từ Form</label>
            <select multiple class="form-control filterSelectpicker" name="form_id" id="ticketFilterByForm">
                '.$options.'
            </select>
        </div>';

        return $formFilterHtml;
    }

    public static function ticketdataTableCareSoftFilter() {

        $options = '<option value="">--Care Soft--</option>
                        <option value="yes">Đã Tạo Ticket</option>
                        <option value="no">Chưa Có Thông Tin</option>';

        $careSoftFilter = '<div class="form-group col-md-3">
                        <label for="ticketFilterByCareSoftStt">Tình Trạng CareSoft</label>
                        <select class="form-control filterSelectpicker" name="caresoft_ticket" id="ticketFilterByCareSoftStt">'.$options.'</select>
                    </div>';

        return $careSoftFilter;
    }

    public static function ticketDataTablePostFilter() {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TICKET_TABLE_NAME;
        $query = "select distinct post_id from $tableName where post_id is not null";
        $postIds = $wpdb->get_results($query, OBJECT);
        $options = "";
        foreach ($postIds as $obj) {
            $postData = get_post($obj->post_id);
            if (!$postData) continue;
            $options .= "<option value='$obj->post_id'>$postData->post_title</option>";
        }

        $postFilterHtml = '<div class="form-group col-md-3">
            <label for="ticketFilterByPost">Nguồn Từ Page</label>
            <select multiple class="form-control filterSelectpicker" name="post_id" id="ticketFilterByPost">
            '.$options.'
            </select>
        </div>';

        return $postFilterHtml;
    }

    public static function ticketDataTableUtmSourceFilter() {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TICKET_TABLE_NAME;
        $query = "select sources from $tableName where sources is not null and sources like '%utm_source%'";

        $sourcesType = $wpdb->get_results($query, OBJECT);

        $utmSourcesData = array_map(function ($obj) {
            parse_str($obj->sources, $sources);
            $obj->sources = $sources;
            return $sources["utm_source"];
        }, $sourcesType);
        $options = "";

        $utmSourcesData = array_unique($utmSourcesData);

        foreach ($utmSourcesData as $val) {
            $options .= "<option value='$val'>$val</option>";
        }

        $sourcesFilterHtml = '<div class="form-group col-md-3">
            <label for="ticketFilterByUtmSource">UTM Source</label>
            <select multiple class="form-control filterSelectpicker" name="utm_source" id="ticketFilterByUtmSource">
                '.$options.'
            </select>
        </div>';
        return $sourcesFilterHtml;
    }

    public static function ticketToCareSoftNowAPI() {
        $req = $_POST;
        $res = array(
            'success' => false
        );
        $code = 400;
        $ticket_id = isset($req['ticket_id']) && (int)$req['ticket_id'] > 0 ? (int)$req['ticket_id'] : false;
        if ($ticket_id) {
            self::wmReadTicket($ticket_id, function ($err, $ticket) use (&$code, &$res) {
                if (!$err && $ticket) {
                    $options = self::setUpTicketToCareSoft($ticket);
                    $ticketCareSoft = self::sendTicketToCareSoft($options);
                    $ticketUpdate = array();

                    // If can't send to caresoft
                    if (!$ticketCareSoft) {
                        // if not find ticketCarsoft
                        $code = 405;
                        $res['msg'] = "Some thing wrog";
                    } else {
                        $code = 200;
                        $ticketUpdate['caresoft_ticket'] =  json_encode($ticketCareSoft);
                        $res['msg'] = "Send Done";
                        $res["success"] = true;

                        self::wmUpdateTicket($ticket->ticket_id, $ticketUpdate, function ($err) use (&$res) {
                            if (!$err) {
                                $res['msg'] = "Update and send Done";
                            } else {
                                $res['msg'] = $err;
                            }
                        });
                    }
                } else {
                    $res['msg'] = "Cannot find this ticket";
                }
            });
        } else {
            $res['msg'] = "Missing required field";
        }
        wp_send_json($res, $code);
        die();
    }

    public static function ticketChartsAPI() {
        $req = $_GET;
        $chartsData = array();
        $res = array(
            "success" => false
        );
        $code = 400;
        $filterSql = "";
        $dates = isset($req['dates']) && is_array($req['dates']) ? $req['dates'] : [];
        $startdate = isset($req['startdate']) && !is_null($req['startdate']) ? self::dateTimeToYMD($req['startdate']) : false;
        $enddate = isset($req['enddate']) && !is_null($req['enddate']) ? self::dateTimeToYMD($req['enddate']) : false;
        $form_ids = isset($req['form_id']) && is_array($req['form_id']) && count($req['form_id']) > 0 ? $req['form_id'] : false;

        $forms = false;
        if ($form_ids) {
            $forms = array();
            foreach ($form_ids as $form_id) {
                self::wmReadForm($form_id, function ($err, $form) use (&$forms) {
                    if (!$err && $form) {
                        array_push($forms, $form);
                    }
                });
            }
        }

        global $wpdb;
        $tableTicket = $wpdb->prefix . self::TICKET_TABLE_NAME;
        $sql = "select count(*) from $tableTicket where";

        if ($forms) {
            foreach ($forms as $k => $form) {
                $name = $form->name;
                $form_id = $form->form_id;
                // $sql .= " form_id = $form_id and created_at like";
                $formChart = array(
                    'name'=>$name,
                    'data'=> []
                );
                foreach ($dates as $key => $date) {
                    $created = self::dateTimeToYMD($date);
                    $query = $sql . " form_id = $form_id and created_at like '%$created%'";
                    $result = $wpdb->get_results($query, OBJECT);
                    $result = (array)$result[0];
                    $y = (int)$result['count(*)'];
                    array_push($formChart['data'], (object)array(
                        'x' => $date,
                        'y' => $y
                    ));
                }

                array_push($chartsData, $formChart);
            }
        } else {
            $sql .= " created_at like";
            $formChart = array(
                'name'=> "Leads",
                'data'=> []
            );
            foreach ($dates as $key => $date) {
                $created = self::dateTimeToYMD($date);
                $query = $sql . " '%$created%'";
                $result = $wpdb->get_results($query, OBJECT);
                $result = (array)$result[0];
                $y = (int)$result['count(*)'];
                array_push($formChart["data"], array(
                        'x' => $date,
                        'y' => $y
                    )
                );
            }

            array_push($chartsData, $formChart);
        }

        $code = 200;
        $res['success'] = true;
        $res['data'] = $chartsData;
        wp_send_json($res, $code);
        die();
    }

    public static function getFETemplate($arr,$template) {
        $hardStr = htmlspecialchars(file_get_contents(self::PLUGIN_PATH . self::FRONTEND_ASSET  . $template . '.html'));
        $str = self::interpolate($hardStr, $arr);
        return $str;
    }

    public static function interpolate($str, $data) {
        $str = gettype($str) == 'string' && strlen($str) > 0 ? $str : '';
        $data = gettype($data) == 'array' && !is_null($data) ? $data : array();
        $data["idHtml"] = self::stringToSlug($data["name"]);

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

    public static function wmReadAllPopup($callback) {
        global $wpdb;
        $tableName =  $wpdb->prefix . self::POPUP_TABLE_NAME;
        $result = $wpdb->get_results( "SELECT * FROM {$tableName}", OBJECT );
        $popups = null;
        $err = false;
        if ($result) $popups = $result;
        if (!$popups) $err = "Cannot get this popup";
        $callback($err, $popups);
    }

    public static function wmReadPopup($popup_id, $callback) {
        $popup_id = isset($popup_id) && !is_null($popup_id) && (int)$popup_id > 0 ? $popup_id : false;
        if (!$popup_id) {
            $callback("Mising some required field");
        } else {
            global $wpdb;
            $tableName =  $wpdb->prefix . self::POPUP_TABLE_NAME;
            $sql = 'SELECT * FROM '.$tableName.' WHERE popup_id = '.$popup_id.' LIMIT 1';
            $result = $wpdb->get_results( $sql, OBJECT );
            $popup = null;
            if ($result) $popup = $result[0];

            if (!$popup) {
                $callback("Cannot get this form", null);
            } else {
                $callback(false, $popup);
            }
        }
    }

    public static function wmNewPopup($popup, $callback) {
        global $wpdb;
        $popupTable = $wpdb->prefix . self::POPUP_TABLE_NAME;
        $result =  $wpdb->insert( $popupTable, $popup);
        if ($result) {
            $callback(false);
        } else {
            $callback("Cannot create new Popup");
        }
    }

    public static function wmUpdatePopup($popup_id , $popupData, $callback) {
        $error = false;
        $dataCallback = null;
        $popup_id = isset($popup_id) && (int)$popup_id > 0 ? $popup_id : false;
        $popupData = isset($popupData) && gettype($popupData) == "array" ? $popupData : false;
        if (!$popup_id && !$popupData) {
            $error = "Missing require data to update";
        } else {
            self::wmReadPopup($popup_id, function ($er ,$popup) use (&$popupData , &$error, &$dataCallback) {
                if (!$er) {
                    global $wpdb;
                    $popupTable = $wpdb->prefix . self::POPUP_TABLE_NAME;
                    $result =  $wpdb->update($popupTable, $popupData, array('popup_id' => $popup->popup_id));
                    if ($result) {
                        $dataCallback = $result;
                    } else {
                        $error = "Cannot Updated popup";
                    }
                } else {
                    $error = $er;
                }
            });
        }

        $callback($error, $dataCallback);
    }

    public static function wmDeletePopup($popup_id, $callback) {
        $popup_id = isset($popup_id) && !is_null($popup_id) && (int)$popup_id > 0 ? $popup_id : false;
        if (!$popup_id) {
            $callback("Mising some required field");
        } else {
            global $wpdb;
            $tableName =  $wpdb->prefix . self::POPUP_TABLE_NAME;
            $popupDeleted = array("popup_id" => $popup_id);
            $result = $wpdb->delete($tableName, $popupDeleted);
            if (!$result) {
                $callback("Cannot delete this popup");
            } else {
                $callback(false);
            }
        }
    }

    public static function listPopupAPI() {
        self::wmReadPopup(function ($err, $popups) {
            if (!$err && $popups) {
                wp_send_json_success($popups);
            } else {
                wp_send_json_error("Cannot find list form", 400);
            }
        });
        die();
    }

    public static function newPopupAPI() {

        $popupData = isset($_REQUEST['popup']) && gettype($_REQUEST['popup']) == 'array' && count($_REQUEST['popup']) > 0 ? $_REQUEST['popup'] : false;

        if (!$popupData) {
            wp_send_json_error("Missing some field", 400);
        } else {
            $title = isset($popupData['title']) && gettype($popupData['title']) == "string" && strlen($popupData['title']) > 0 ? $popupData['title'] : false;

            // Check the require field
            $bg_image_id = isset($popupData['bg_image_id']) && !is_null($popupData['bg_image_id']) && (int)$popupData['bg_image_id'] > 0 ? $popupData['bg_image_id'] : null;
            $content = isset($popupData['content']) && !is_null($popupData['content'])  && gettype($popupData['content']) == "string" ? str_replace('\"', '"', $popupData['content']) : null;
            $delay_show_time = isset($popupData['delay_show_time']) && !is_null($popupData['delay_show_time']) && (int)$popupData['delay_show_time'] > 0 ? $popupData['delay_show_time'] : null;
            $direction_background = isset($popupData['direction_background']) && !is_null($popupData['direction_background']) && gettype($popupData['direction_background']) == "string" && filter_var($popupData['direction_background'], FILTER_VALIDATE_URL) ? $popupData['direction_background'] : null;
            $form_id = isset($popupData['form_id']) && (int)$popupData['form_id'] > 0 ? (int)$popupData['form_id'] : null;
            $time =  date('Y-m-d H:i:s');

            // If missing required field
            if (!$title)
                wp_send_json_error("Missing require fields", 401);

            $arr = array(
                'title' => $title,
                'bg_image_id' => $bg_image_id,
                'form_id' => $form_id,
                'content' => $content,
                'direction_background' => $direction_background,
                'delay_show_time' => $delay_show_time,
                'created_at' => $time,
                'updated_at' => $time
            );

            self::wmNewPopup($arr, function ($err) {
                if (!$err) {
                    wp_send_json_success(true);
                } else {
                    wp_send_json_error("Cannot create new popup", 402);
                }
            });
        }
        die();
    }

    public static function readPopupAPI() {
        $popup_id = isset($_REQUEST['popup_id']) && in_array(gettype($_REQUEST['popup_id']), ["string", "number"]) && (int)$_REQUEST['popup_id'] > 0 ? (int)$_REQUEST['popup_id'] : false;
        if (!$popup_id) {
            wp_send_json_error('Mising some required field', 401);
        } else {
            self::wmReadPopup($popup_id, function ($err, $popup) {
                if (!$err && $popup) {
                    wp_send_json_success($popup);
                } else {
                    wp_send_json_error('Cannot find this popup', 405);
                }
            });
        }
        die();
    }

    public static function updatePopupAPI() {
        $popupUpdated = isset($_REQUEST['popup']) && gettype($_REQUEST['popup']) == "array" && (int)$_REQUEST['popup'] > 0 ? $_REQUEST['popup'] : false;
        if (!$popupUpdated) {
            wp_send_json_error('Mising some required field', 401);
        } else {
            $popup_id = isset($popupUpdated['popup_id']) && in_array(gettype($popupUpdated['popup_id']), ["string", "number"]) && (int)$popupUpdated['popup_id'] > 0 ? (int)$popupUpdated['popup_id'] : false;
            if (!$popup_id) {
                wp_send_json_error('Mising some required field', 402);
            } else {
                $title = isset($popupUpdated['title']) && gettype($popupUpdated['title']) == "string" && strlen($popupUpdated['title']) > 0 ? $popupUpdated['title'] : null;
                $content = isset($popupUpdated['content']) && gettype($popupUpdated['content']) == "string" && strlen($popupUpdated['content']) > 0 ? self::htmlVerifyCodeToDb($popupUpdated['content']) : null;
                $bg_image_id = isset($popupUpdated['bg_image_id']) && (int)$popupUpdated['bg_image_id'] > 0 ? $popupUpdated['bg_image_id'] : null;
                $direction_background = isset($popupUpdated['direction_background']) && gettype($popupUpdated['direction_background']) == "string" ? $popupUpdated['direction_background'] : null;
                $form_id = isset($popupUpdated['form_id']) && (int)$popupUpdated['form_id'] > 0 ? $popupUpdated['form_id'] : null;
                $delay_show_time = isset($popupUpdated['delay_show_time']) && (int)$popupUpdated['delay_show_time'] > 0 ? $popupUpdated['delay_show_time'] : null;
                $updated_at = self::dateTimeNow();

                $args = array(
                    'title'=> $title,
                    'content'=> $content,
                    'bg_image_id'=> $bg_image_id,
                    'direction_background'=> $direction_background,
                    'form_id'=> $form_id,
                    'delay_show_time'=> $delay_show_time,
                    'updated_at'=> $updated_at
                );

                self::wmUpdatePopup($popup_id, $args, function ($err) {
                    if (!$err) {
                        wp_send_json_success(true);
                    } else {
                        wp_send_json_error($err, 405);
                    }
                });
            }
        }
        die();
    }

    public static function deletePopupAPI() {
        $popup_id = isset($_REQUEST['popup_id']) && in_array(gettype($_REQUEST['popup_id']), ["string", "number"]) && (int)$_REQUEST['popup_id'] > 0 ? (int)$_REQUEST['popup_id'] : false;
        if (!$popup_id) {
            wp_send_json_error('Mising some required field', 401);
        } else {
            self::wmDeletePopup($popup_id, function ($err) {
                if (!$err) {
                    wp_send_json_success(true);
                } else {
                    wp_send_json_error('Cannot delete this popup', 402);
                }
            });
        }
        die();
    }



    public static function getABContentItem() {
        global $wpdb;
        $tableName = $wpdb->prefix . self::AB_CONTENT_TABLE_NAME;
        $oldContent = $wpdb->get_results("select * from $tableName", OBJECT);
        if ($oldContent && is_array($oldContent) && count($oldContent)) {
            $oldContent = $oldContent[0];

            $oldContent->location = is_null($oldContent->location) ? null : json_decode($oldContent->location);

            return $oldContent;
        } else {
            return false;
        }
    }

    public static function abContentHandler($data) {

        $htmlContent = [];
        $htmlContent["content"] = isset($data["content"]) && is_string($data["content"]) && strlen($data["content"]) ? $data["content"] : null;
        $htmlContent["location"] = isset($data["location"]) && is_array($data["location"]) && count($data["location"]) ? json_encode($data["location"]) : null;
        $timeNow = self::dateTimeNow();

        $oldContent = self::getABContentItem();
        global $wpdb;
        $tableName = $wpdb->prefix . self::AB_CONTENT_TABLE_NAME;
        if ($oldContent) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            $updateContent = $wpdb->update($tableName, $htmlContent, ["id"=>$oldContent->id]);
            return $updateContent;
        } else {
            $htmlContent["created_at"] = $timeNow;
            $htmlContent["updated_at"] = $timeNow;
            $newContent = $wpdb->insert($tableName,$htmlContent);
            return $newContent;
        }

    }

    public static function webManagerForm() {
        include (self::PLUGIN_PATH . self::BACKEND_TEMPLATE . 'form.php');
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
            if ($result) {
                $form = $result[0];
                $form->form_custom_template = isset($form->form_custom_template) && !is_null($form->form_custom_template) ? json_decode($form->form_custom_template) : null;
                $form->caresoft_setting = isset($form->caresoft_setting) && !is_null($form->caresoft_setting) ? json_decode($form->caresoft_setting) : null;
            }
            if (!$form) {
                $callback("Cannot get this form", null);
            } else {
                $callback(false, $form);
            }
        }
    }

    public static function wmUpdateForm($form_id, $formData, $callback) {
        $form_id = isset($form_id) && !is_null($form_id) && (int)$form_id > 0 ? $form_id : false;
        $formData = isset($formData) && is_array($formData) && count($formData) > 0 ? $formData : false;
        if (!$form_id) {
            $callback("Mising some required field");
        } elseif (!$formData) {
            $callback("Mising some data to update");
        } else {
            global $wpdb;
            $tableName =  $wpdb->prefix . self::FORM_TABLE_NAME;
            $result = $wpdb->update($tableName, $formData, array('form_id' => $form_id));
            if ($result) {
                $callback(false);
            } else {
                $callback("Cannot update this form");
            }
        }
    }

    public static function wmReadAllForm($callback) {
        global $wpdb;
        $tableName =  $wpdb->prefix . self::FORM_TABLE_NAME;
        $result = $wpdb->get_results( "SELECT * FROM {$tableName}", OBJECT );
        $forms = null;
        $err = false;
        if ($result) $forms = $result;
        if (!$forms) $err = "Cannot get this form";
        $callback($err, $forms);
    }

    public static function wmDeleteForm($form_id, $callback) {
        $form_id = isset($form_id) && !is_null($form_id) && (int)$form_id > 0 ? $form_id : false;
        if (!$form_id) {
            $callback("Mising some required field");
        } else {
            global $wpdb;
            $tableName =  $wpdb->prefix . self::FORM_TABLE_NAME;
            $formDeleted = array("form_id" => $form_id);
            $result = $wpdb->delete($tableName, $formDeleted);
            if (!$result) {
                $callback("Cannot delete this form");
            } else {
                $callback(false);
            }
        }
    }

    public static function listFormAPI() {
        self::wmReadAllForm(function ($err, $forms) {
            if (!$err && $forms) {
                wp_send_json_success($forms);
            } else {
                wp_send_json_error("Cannot find list form", 400);
            }
        });
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
            $title = isset($form['title']) && is_string($form['title']) && strlen($form['title']) > 0 ? $form['title'] : null;
            $name = isset($form['name']) && is_string($form['name']) && strlen($form['name']) > 0 ? $form['name'] : false;
            $directional = isset($form['directional']) && is_string($form['directional']) ? $form['directional'] : null;
            $to_caresoft_now = isset($form['to_caresoft_now']) && in_array($form['to_caresoft_now'], ['on','off']) ? $form['to_caresoft_now'] : 'off' ;
            $caresoft_id = isset($form['caresoft_id']) && in_array(gettype($form['caresoft_id']), ['number', 'string']) && strlen((string)$form['caresoft_id']) == 5 ? $form['caresoft_id'] : null;
            $nguon_phieu = isset($form['nguon_phieu']) && (int)$form['nguon_phieu'] ? (int)$form['nguon_phieu'] : null ;
            $chi_tiet_nguon_phieu = isset($form['chi_tiet_nguon_phieu']) && (int)$form['chi_tiet_nguon_phieu'] ? (int)$form['chi_tiet_nguon_phieu'] : null ;
            $time =  self::dateTimeNow();

            $form_custom_template = isset($form['form_custom_template']) && gettype($form['form_custom_template']) == 'array' ? json_encode($form['form_custom_template']) : null;
            $caresoft_setting = isset($form['caresoft_setting']) && in_array(gettype($form['caresoft_setting']), ["array","object"])  ? json_encode($form['caresoft_setting']) : null;

            if (!$name) {
                wp_send_json_error('Missing required field', 402);
            }else if($to_caresoft_now == 'on' && (is_null($nguon_phieu) || is_null($chi_tiet_nguon_phieu))){
                wp_send_json_error('Missing required field', 402);
            }else {
                $newForm = array(
                    'name' => $name,
                    'title' => $title,
                    'directional' => $directional,
                    'to_caresoft_now' => $to_caresoft_now,
                    'caresoft_id' => $caresoft_id,
                    'nguon_phieu' => $nguon_phieu,
                    'chi_tiet_nguon_phieu' => $chi_tiet_nguon_phieu,
                    'form_custom_template' => $form_custom_template,
                    'caresoft_setting' => $caresoft_setting,
                    'created_at' => $time,
                    'updated_at' => $time
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
        $form = isset($_REQUEST['form']) && gettype($_REQUEST['form']) == 'array' && count($_REQUEST['form']) > 0 ? $_REQUEST['form'] : false;
        if (!$form) {
            wp_send_json_error('Mising some required field', 401);
        } else {
            $form_id = isset($form['form_id']) && in_array(gettype($form['form_id']), ["string", "number"]) && (int)$form['form_id'] > 0 ? (int)$form['form_id'] : false;
            if (!$form_id) {
                wp_send_json_error('Mising some required field', 401);
            } else {

                $form['title'] = isset($form['title']) && gettype($form['title']) == "string" && strlen($form['title']) > 0 ? $form['title'] : null;
                $form['name'] = isset($form['name']) && gettype($form['name']) == "string" && strlen($form['name']) > 0 ? $form['name'] : null;
                $form['to_caresoft_now'] = isset($form['to_caresoft_now']) && in_array($form['to_caresoft_now'], ['on','off']) ? $form['to_caresoft_now'] : null;
                $form['directional'] = isset($form['directional']) && gettype($form['directional']) == "string" && strlen($form['directional']) > 0 ? $form['directional'] : null;
                $form['caresoft_id'] = isset($form['caresoft_id']) && (int)$form['caresoft_id'] > 0 && strlen((string)$form['caresoft_id']) == 5 ? $form['caresoft_id'] : null;
                $form['nguon_phieu'] = isset($form['nguon_phieu']) && (int)$form['nguon_phieu'] ? $form['nguon_phieu'] : null ;
                $form['chi_tiet_nguon_phieu'] = isset($form['chi_tiet_nguon_phieu']) && (int)$form['chi_tiet_nguon_phieu'] ? $form['chi_tiet_nguon_phieu'] : null ;
                $form['form_custom_template'] = isset($form['form_custom_template']) && gettype($form['form_custom_template']) == 'array' ? json_encode($form['form_custom_template']) : null;
                $form['caresoft_setting'] = isset($form['caresoft_setting']) && in_array(gettype($form['caresoft_setting']), ["array","object"]) ? json_encode($form['caresoft_setting']) : null;

                self::wmUpdateForm($form_id, $form, function ($err) {
                    if (!$err) {
                        wp_send_json_success(true);
                    } else {
                        wp_send_json_error('Cannot update this form', 405);
                    }
                });
            }
        }
        die();
    }

    public static function deleteFormAPI() {
        $form_id = isset($_REQUEST['form_id']) && in_array(gettype($_REQUEST['form_id']), ["string", "number"]) && (int)$_REQUEST['form_id'] > 0 ? (int)$_REQUEST['form_id'] : false;
        if (!$form_id) {
            wp_send_json_error('Mising some required field', 401);
        } else {
            self::wmDeleteForm($form_id, function ($err) {
                if (!$err) {
                    wp_send_json_success(true);
                } else {
                    wp_send_json_error('Cannot delete this form', 402);
                }
            });
        }
        die();
    }




    public static function wmListAgents($callback) {
        global $wpdb;
        $tableName =  $wpdb->prefix . self::CS_ANGENT_TABLE_NAME;
        $result = $wpdb->get_results( "SELECT * FROM {$tableName}", OBJECT );
        $agents = null;
        $err = false;
        if ($result) $agents = $result;
        if (!$agents) $err = "Cannot get agents";
        $callback($err, $agents);
    }



    public static function wmReadTicket($ticket_id, $callback) {
        $ticket_id = isset($ticket_id) && !is_null($ticket_id) && (int)$ticket_id > 0 ? $ticket_id : false;
        if (!$ticket_id) {
            $callback("Mising some required field");
        } else {
            global $wpdb;
            $tableName =  $wpdb->prefix . self::TICKET_TABLE_NAME;
            $result = $wpdb->get_results( "SELECT * FROM {$tableName} WHERE ticket_id = {$ticket_id} LIMIT 1", OBJECT );
            $ticket = null;
            if ($result) {
                $ticket = $result[0];

                $ticket->detail = isset($ticket->detail) && !is_null($ticket->detail) ? json_decode($ticket->detail) : null;
                $ticket->caresoft_ticket = isset($ticket->caresoft_ticket) && !is_null($ticket->caresoft_ticket) ? json_decode($ticket->caresoft_ticket) : null;
                $ticket->ticket_data_custom = isset($ticket->ticket_data_custom) && !is_null($ticket->ticket_data_custom) ? json_decode($ticket->ticket_data_custom) : null;
                $ticket->ticket_data = isset($ticket->ticket_data) && !is_null($ticket->ticket_data) ? json_decode($ticket->ticket_data) : null;

            }
            if (!$ticket) {
                $callback("Cannot get this ticket", null);
            } else {
                $callback(false, $ticket);
            }
        }
    }

    public static function wmUpdateTicket($ticket_id, $update, $callback) {
        $ticket_id = isset($ticket_id) && !is_null($ticket_id) && (int)$ticket_id > 0 ? (int)$ticket_id : false;
        $update = isset($update) && is_array($update) ? $update : false;
        if (!$ticket_id) {
            $callback("Mising some required field");
        } elseif (!$update) {
            $callback("Mising some data to update");
        } else {
            global $wpdb;
            $tableName =  $wpdb->prefix . self::TICKET_TABLE_NAME;
            $result = $wpdb->update($tableName, $update, array('ticket_id' => $ticket_id));
            if (!$result) {
                $callback("Cannot update this ticket");
            } else {
                $callback(false);
            }
        }
    }

    public static function readTicketAPI() {
        $method = strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_SERVER['REQUEST_METHOD'] : false;
        if ($method) {
            $ticket_id = isset($_GET['ticket_id']) && (int)$_GET['ticket_id'] > 0 ? (int)$_GET['ticket_id'] : false;
            self::wmReadTicket($ticket_id, function ($err, $ticket) {
                if (!$err && $ticket) {
                    wp_send_json_success($ticket);
                } else {
                    wp_send_json_error($err, 403);
                }
            });
        } else {
            wp_send_json_error('Unknown this method', 401);
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
            $formCustom = isset($ticket['formCustom']) && $ticket['formCustom'] == true ? true : false;
            $sources = isset($ticket['sources']) && is_string($ticket['sources']) ? $ticket['sources'] : false;
            $referer = isset($ticket['referer']) && is_array($ticket['referer']) ? $ticket['referer'] : false;
            $time =  self::dateTimeNow();

            /**
             * With Ticket data
             */
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
                if ($referer) $newTicket['referer'] = json_encode($referer);
                if ($sources) $newTicket['sources'] = $sources;

                $newTicket["ip_address"] = self::get_client_ip();

                if ($formCustom) {
                    $ticketCustomData = $ticket;
                    unset($ticketCustomData["formCustom"]);
                    unset($ticketCustomData["directional"]);
                    unset($ticketCustomData["form_id"]);

                    $newTicket['ticket_data_custom'] = json_encode($ticket);
                }

                self::wmReadForm($form_id, function ($err, $formData) use (&$newTicket) {
                    if (!$err && $formData) {
                        // Insert new ticket to database
                        global $wpdb;
                        $ticketTable = $wpdb->prefix . self::TICKET_TABLE_NAME;
                        $ticketDetail = isset($newTicket['detail']) && !is_null($newTicket['detail']) ? json_decode($newTicket['detail']) : false;

                        // Get id of page register
                        $pageUrl = $ticketDetail->origin . $ticketDetail->pathname;
                        $post_id = url_to_postid($pageUrl);
                        $postDetail = get_post($post_id);
                        $newTicket["post_id"] = $post_id && $postDetail ? $post_id : null;

                        $email = $newTicket['email'] ? $newTicket['email'] : null;
                        $name = $newTicket['name'];
                        $phone = $newTicket['phone'];
                        $referer = $newTicket['referer'] ? json_decode($newTicket['referer']) : false;

                        $sources = gettype($newTicket['sources']) == "string" && strlen($newTicket['sources']) ? $newTicket['sources'] : false;
                        if (!$sources) {
                            $sources = isset($ticketDetail->search) && gettype($ticketDetail->search) == "string" && strlen($ticketDetail->search) > 0 ? substr($ticketDetail->search, 1, strlen($ticketDetail->search)) : false;
                            $newTicket['sources'] = $sources ? $sources : null;
                        }

                        // Check and Send data to CareSoft
                        $checkToCareSoftNow = $formData->to_caresoft_now == self::TO_CARESOFT_NOW_ON;
                        $result = false;

                        if ($checkToCareSoftNow) {
                            $title = "";
                            $title .= $formData->title . ' - ' . $name;
                            $ticketComment = "";
                            $caresoft_id = isset($formData->caresoft_id) ? $formData->caresoft_id : null;
                            $nguon_phieu = isset($formData->nguon_phieu) ? $formData->nguon_phieu : null;
                            $chi_tiet_nguon_phieu = isset($formData->chi_tiet_nguon_phieu) ? $formData->chi_tiet_nguon_phieu : null;
                            $caresoftSetting = !is_null($formData->caresoft_setting) ? $formData->caresoft_setting : false;
                            $hasKey = array_key_exists("utm_source", self::queryToArray($sources));

                            $utm_source = false;
                            if ($referer && array_key_exists("utm_source", (array)$referer)) {
                                $utm_source = strtolower($referer->utm_source);
                            } elseif ($caresoftSetting && $sources && $hasKey) {
                                $utm_source = strtolower(self::queryToArray($sources)["utm_source"]);
                            }
                            if ($utm_source) {
                                $x = array_filter($caresoftSetting, function($obj) use (&$utm_source) {
                                    $flag = strtolower($obj->utm_source) == $utm_source;
                                    return $flag;
                                });
                                $source = $x[key((array)$x)];
                                $nguon_phieu = $source->nguon_phieu;
                                $chi_tiet_nguon_phieu = $source->chi_tiet_nguon_phieu;
                            }

                            // Optimize api title and comment
                            $ticketComment .= "<br> <b style='color:firebrick'>Chi Tiết Đăng Kí</b>";
                            $ticketComment .= '<br> Link bài viết : '. $ticketDetail->href;
                            $postTitle = $postDetail && isset($postDetail->post_title) ? $postDetail->post_title : false;
                            if ($postTitle) {
                                $title .= ' - ' . $postTitle;
                                $ticketComment .= '<br> Đăng Ký Tại Page : ' . $postTitle;
                            }
                            /*$note = $newTicket['note'] ? $newTicket['note'] : false;
                            if ($note) {
                                $ticketComment .= "<br> Lời nhắn của khách hàng : " . $note;
                            }*/

                            $ticket_data_custom = isset($newTicket['ticket_data_custom']) && !is_null($newTicket['ticket_data_custom']) ? json_decode($newTicket['ticket_data_custom']) : false;
                            $form_custom_template = isset($formData->form_custom_template) && !is_null($formData->form_custom_template) ? $formData->form_custom_template : false;

                            $customTicketField = array_filter($form_custom_template,function($obj) {
                                $hasDefaultName = true /*!in_array($obj->name, ["name","phone"])*/;
                                $isButton = !in_array($obj->type, ["submit","button"]);
                                $flag = $hasDefaultName && $isButton;
                                return $flag;
                            });
                            if (!$ticket_data_custom && !$form_custom_template && !$customTicketField) {

                            } else {
                                foreach ($customTicketField as $o => $field) {
                                    $c = (array)$ticket_data_custom;
                                    if (isset($c[$field->name]) && !is_null($c[$field->name])) {
                                        $extrasVal = $c[$field->name];
                                        $ticketComment .= "<br> " . $field->label . " : " . $extrasVal ;
                                    }
                                }
                            }

                            $options = array($title, $ticketComment, $email, $phone, $name, $caresoft_id,$nguon_phieu,$chi_tiet_nguon_phieu);
                            $ticketCareSoft = self::sendTicketToCareSoft($options);
                            // If can't send to caresoft
                            if (!$ticketCareSoft) {
                                // if send fail ticketCaresoft
                                wp_send_json_error("Cannot create new ticket",405);
                            } else {
                                $newTicket['caresoft_ticket'] = json_encode($ticketCareSoft);
                                $result =  $wpdb->insert( $ticketTable, $newTicket);
                            }
                        } else {
                            $result =  $wpdb->insert( $ticketTable, $newTicket);
                        }

                        if ($result) {
                            wp_send_json_success(true);
                        } else {
                            wp_send_json_error("Cannot create new ticket",403);
                        }
                    } else {
                        wp_send_json_error("Cannot find this form" , 402);
                    }
                });
            } else {
                wp_send_json_error('Missing require field', 401);
            }
        }
        die();
    }

    public static function setUpTicketToCareSoft($ticket) {
        $newTicket = (array)$ticket;
        $options = array();
        $ticketDetail = !is_null($ticket->detail) && gettype($ticket->detail) == 'object' ? $ticket->detail : false;
        $title = "";
        $optionsExstra = array();
        self::wmReadForm($ticket->form_id, function ($err, $form) use (&$title , &$optionsExstra) {
            if (!$err && $form) {
                $title .= $form->title . ' - ';
                $caresoft_id = isset($form->caresoft_id) ? $form->caresoft_id : null;
                $nguon_phieu = isset($form->nguon_phieu) ? $form->nguon_phieu : null ;
                $chi_tiet_nguon_phieu = isset($form->chi_tiet_nguon_phieu) ? $form->chi_tiet_nguon_phieu : null ;
                $optionsExstra = [$caresoft_id,$nguon_phieu,$chi_tiet_nguon_phieu];
            }
        });
        $title .= $ticket->name;
        $ticketComment = "";
        $email = $ticket->email ? $ticket->email : null;
        $name = $ticket->name;
        $phone = $ticket->phone;

        // Optimize api title and comment
        $ticketComment .= "<br> <b style='color:firebrick'>Chi Tiết Đăng Kí</b>";
        $ticketComment .= '<br> Link bài viết : '. $ticketDetail->href;

        $postTitle = $ticketDetail && isset($ticketDetail->postTitle) && !is_null($ticketDetail->postTitle) ? $ticketDetail->postTitle : false;
        if ($postTitle) {
            $title .= ' - ' . $postTitle;
            $ticketComment .= '<br> Tiêu Đề Page : ' . $postTitle;
        }
        $note = $newTicket['note'] ? $newTicket['note'] : false;
        if ($note) {
            $ticketComment .= "<br> Lời nhắn của khách hàng";
        }

        $options = array_merge(array($title, $ticketComment, $email, $phone, $name), $optionsExstra);
        return $options;
    }

    public function sendTicketToCareSoft($options) {
        $options = isset($options) && !is_null($options) && gettype($options) == 'array' ? $options : false;
        if (!$options)
            return false;
        list($title , $ticket_comment, $email, $phone, $username, $caresoft_id,  $nguon_phieu, $chi_tiet_nguon_phieu) = $options;
        $caresoft_id = isset($caresoft_id) && !is_null($caresoft_id) && (int)$caresoft_id > 0 ? (int)$caresoft_id : 42124;
        $nguon_phieu = isset($nguon_phieu) && !is_null($nguon_phieu) && (int)$nguon_phieu > 0 ? (int)$nguon_phieu : 41890;
        $chi_tiet_nguon_phieu = isset($chi_tiet_nguon_phieu) && !is_null($chi_tiet_nguon_phieu) && (int)$chi_tiet_nguon_phieu > 0 ? (int)$chi_tiet_nguon_phieu : 42112;
        $title = isset($title) && gettype($title) == "string" && strlen($title) > 0 ? $title : false;
        $ticket_comment = isset($ticket_comment) && gettype($ticket_comment) == "string" && strlen($ticket_comment) > 0 ? $ticket_comment : false;
        $email = isset($email) && !is_null($email) && strlen($email) > 0 ? $email : "";
        $phone = isset($phone) && !is_null($phone) && strlen($phone) > 9 && strlen($phone) < 15 ? $phone : false;
        $email = isset($email) && !is_null($email) && strlen($email) > 0 ? $email : false;
        $username = isset($username) && !is_null($username) && strlen($username) > 0 ? $username : false;

        if ($title && $username && $phone) {
            $urlSend = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets";
            $agent = self::getAgentsAssignee();
            $custom_field = '{"id": "3406", "value": "41875"},{"id": "1448", "value": "'.$nguon_phieu.'"}';
            if (in_array($chi_tiet_nguon_phieu, [44119,41920, 41923, 41926, 41929, 41932, 41935, 42490, 44119])) {
                $custom_field .= ',{"id": "1700", "value": "'.$chi_tiet_nguon_phieu.'"}';
            } else {
                $custom_field .= ',{"id": "1416", "value": "'.$chi_tiet_nguon_phieu.'"}';
            }
            // $custom_field = '{"id": "3406", "value": "41875"},{"id": "1448", "value": "'.$nguon_phieu.'"},{"id": "1416", "value": "'.$chi_tiet_nguon_phieu.'"}';
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

        $careSoftAgents = null;

        $urlGet = "https://api.caresoft.vn/tmvngocdung/api/v1/agents";
        $resultGet = self::getPostData($urlGet);
        $argsGet = json_decode($resultGet,true);
        $careSoftAgents = isset($argsGet["agents"]) && gettype($argsGet["agents"]) == "array" && count($argsGet["agents"]) > 0 ? $argsGet["agents"] : null;

        /*self::wmListAgents(function ($err, $list) use (&$careSoftAgents) {
            if (!$err && $list) {
                $careSoftAgents = $list;
            } else {
                $urlGet = "https://api.caresoft.vn/tmvngocdung/api/v1/agents";
                $resultGet = self::getPostData($urlGet);
                $argsGet = json_decode($resultGet,true);
                $careSoftAgents = isset($argsGet["agents"]) && gettype($argsGet["agents"]) == "array" && count($argsGet["agents"]) > 0 ? $argsGet["agents"] : null;
            }
        });*/
        $agents = $careSoftAgents ? $careSoftAgents : [];
        $ids = array();
        $ex = array(15413222, 16185696,16595190,15594858,
            16650858,19196733,19831065,19833102,19833687,
            19834329,19834410,22480929,23943516,24881607,
            24882753,24883884);
        foreach ($agents as $val) {
            $val = (array)$val;
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




    public static function getWMFormShortCode($att, $content = null) {
        $form_id = isset($att['form_id']) && !is_null($att['form_id']) && (int)$att['form_id'] > 0 ? (int)$att['form_id'] : false;
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

    public static function getWMPopupShortCode($att, $content = null) {
        $popup_id = isset($att['popup_id']) && !is_null($att['popup_id']) && (int)$att['popup_id'] > 0 ? $att['popup_id'] : false;
        $button = isset($att['type']) && $att['type'] == 'button' ? $att['type'] : false;

        $popupHtml = "";
        if (!$popup_id) {
            $popupHtml = "Pop Oops";
        } else {
            $popupStr = '';
            self::wmReadPopup($popup_id, function ($err, $popupData) use (&$popupStr, &$popupHtml, &$button, &$att) {
                if (!$err && $popupData) {
                    $modalIdHtml = self::stringToSlug($popupData->title);
                    $buttonText = isset($att['button_text']) && gettype($att['button_text']) == "string" ? $att['button_text'] : "Click Vào Đây";
                    if ($button) {
                        $popupStr = '<button type="button" class="wm-whiteframe-2dp btn btn-primary btn-sm" data-toggle="modal" data-target="#'.$modalIdHtml.'">
                                    '.$buttonText.'
                                </button>';
                    } else {
                        $form_id = !is_null($popupData->form_id) && $popupData->form_id > 0 ? $popupData->form_id : false;
                        $popupData->wmForm = null;
                        // If isset $form_id
                        if ($form_id)
                            $wmForm = htmlspecialchars(self::getWMFormShortCode(array("form_id"=>$form_id)));
                        $popupData->wmForm = $wmForm;
                        $popupData->modalIdHtml = $modalIdHtml;

                        $bg_image = self::getImageById($popupData->bg_image_id);
                        $popupData->bg_image = $bg_image ? $bg_image->url : "";
                        $popupData->bg_image_width = $bg_image->width ? $bg_image->width . 'px' : "";
                        $popupData->bg_image_height = $bg_image->height ? $bg_image->height . 'px' : "";

                        $popupStr = self::getFETemplate((array)$popupData, 'popup');
                    }
                }
            });
            $popupHtml = htmlspecialchars_decode($popupStr);
        }

        return $popupHtml;
    }


    public static function arrayToInput($arr) {
        $types = [''];
    }

    public static function htmlVerifyCodeToDb($htmlStr) {
        return str_replace('\"', '"', $htmlStr);
    }

    public static function dateTimeNow() {
        return date('Y-m-d H:i:s');
    }

    public static function dateTimeToYMD($datetime) {
        //Convert it into a timestamp.
        $timestamp = strtotime($datetime);
        //Convert it to DD-MM-YYYY
        $dmy = date("Y-m-d", $timestamp);
        return $dmy;
    }

    public static function dateTimeToDMY($datetime) {
        //Convert it into a timestamp.
        $timestamp = strtotime($datetime);

        //Convert it to DD-MM-YYYY
        $dmy = date("d-m-Y", $timestamp);

        return $dmy;
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

    public static function getImageById($id) {
        $id = isset($id) && (int)$id > 0 ? $id : false;
        if (!$id) {
            return false;
        }

        $a = array("url","width","height");
        $b = wp_get_attachment_image_src( $id, $size = 'full' );
        $x = array();
        foreach ($a as $key => $value) {
            $x[$value] = $b[$key];
        }
        $x = (object)$x;
//        $url = $image[0]; //- image URL
//        $width = $image[1]; //- image width
//        $height = $image[2]; //- image height

        return $x;
    }

    public static function vn_str_filter ($str){
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }

    public static function stringToSlug($str) {
        $a = self::vn_str_filter($str);
        $x = str_replace(" ","_", $a);
        return $x;
    }

    public static function findObjectInArray($search = "",$arr = array()) {
        foreach ($arr as $key => $val) {
            $val = (array)$val;
            $hasSearch = array_key_exists($search, $val) ? true : false;
            if ($hasSearch) {
                $obj = (object)$val;
                return $obj;
            } else {
                continue;
            }
        }
        return null;
    }

    /*
         * @param string $name Name of option or name of post custom field.
         * @param string $value Optional Attachment ID
         * @return string HTML of the Upload Button
         */
    public static function wm_image_uploader_field( $name, $value = '') {
        $image = ' button">Upload image';
        $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
        $display = 'none'; // display state ot the "Remove image" button
        $hasValue = gettype($value) == "string" && strlen(trim($value)) > 0 ? true : false;

        if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {

            // $image_attributes[0] - image URL
            // $image_attributes[1] - image width
            // $image_attributes[2] - image height

            $image = '"><img class="wm-img" src="' . $image_attributes[0] . '" style="margin-bottom:15px;" />';
            $display = 'inline-block';
        }

        return '<div>
                    <a href="#" class="wm_upload_image_button' . $image . '</a>
                    <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
                    <a href="#" class="wm_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
                </div>';
    }


    public static function ytVideoDetailById($video_id) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id=$video_id&key=AIzaSyBF2LUcRgbbcYOk58oYmUdD52mwSDIlN2A");
//        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id=$video_id&key=AIzaSyBeh4tilZ1vbt9biWijEqjE-DXS3LfcFvc");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);
        return $response;
    }

    public static function wmReadYoutubeVideo($video_id) {
        $video_id = gettype($video_id) == "string" && strlen($video_id) && strlen($video_id) < 100 ? $video_id : false;
        if (!$video_id) {
            return "missing video id";
        }

        global $wpdb;
        $youtubeTable = $wpdb->prefix . self::YOUTUBE_TABLE_NAME;
        $video = $wpdb->get_row("select * from $youtubeTable where youtube_id = '$video_id'", OBJECT);
        if (!$video) {
            $createNewVideo = self::wmNewYoutubeVideo($video_id);
            if (!$createNewVideo) return "Cannot create new video";
            $video = $wpdb->get_row("select * from $youtubeTable where youtube_id = '$video_id'", OBJECT);
        }
        if (isset($video->details) && !is_null($video->details)) {
            $video->details = json_decode($video->details);
        }
        return $video;
    }

    public static function wmNewYoutubeVideo($video_id) {
        $video_id = gettype($video_id) == "string" && strlen($video_id) && strlen($video_id) < 100 ? $video_id : false;

        if (!$video_id) {
            return "missing video id";
        }

        global $wpdb;
        $time =  self::dateTimeNow();
        $youtubeTable = $wpdb->prefix . self::YOUTUBE_TABLE_NAME;

        $details = self::ytVideoDetailById($video_id);
        $successAPI = isset($details->items) && count($details->items) ? $details->items : false;
        if ($successAPI) {
            $details = json_encode($details);
        }
        $youtubeVidArr = array(
            "youtube_id" => $video_id,
            "details" => $details,
            "updated_at" => $time,
            "created_at" => $time
        );

        $result = $wpdb->insert($youtubeTable, $youtubeVidArr);
        return $result;
    }


    public static function Generate_Featured_Image( $image_url, $post_id  ){
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        $filename = basename($image_url);
        if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
        else                                    $file = $upload_dir['basedir'] . '/' . $filename;
        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
        $res2= set_post_thumbnail( $post_id, $attach_id );
    }


    // Function to get the client IP address
    public static function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}