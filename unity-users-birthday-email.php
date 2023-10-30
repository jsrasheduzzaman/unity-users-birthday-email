<?php
/**
 * Plugin Name: Users Birthday Email
 * Plugin URI: 
 * Description: Users Birthday Email automatically send an email to WordPress users on their birthday. This is very easy to use with any membership plugins.
 * Version: 1.0
 * Requires at least: 5.7
 * Requires PHP: 7.2
 * Author: Unity Active Developers
 * Author URI: 
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: unity-birthday-email
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define('UNITY_PATH', plugin_dir_path(__FILE__));


class Unity_Birthday {

    private static $instances = [];

    protected function __construct() {
        add_action( 'plugins_loaded', [$this, 'unity_load_textdomain'] );
        add_action( 'admin_menu', [$this, 'unity_sub_menu_page'] );
        add_action( 'admin_init', [$this, 'unity_settings_options'] );
        add_action( 'show_user_profile', [$this, 'unity_user_birthday_input'] );
        add_action( 'edit_user_profile', [$this, 'unity_user_birthday_input'] );
        add_action( 'personal_options_update', [$this, 'uniry_user_profile_update'] );
        add_action( 'edit_user_profile_update', [$this, 'uniry_user_profile_update'] );
        add_action( 'admin_enqueue_scripts', [ $this, 'unity_admin_scripts' ] );
        add_action( 'wp', [$this, 'event_trigger_schedule'] );
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [$this, 'plugin_action_links'] );


        // add_action( 'unity_daily_event', [$this, 'unity_mail_function'] );
    }


    public function unity_admin_scripts($hook) {
        if( "users_page_unity-users-birthday-emails" != $hook ) {
            return;
        }
        wp_enqueue_style( 'unity-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css', [], '1.0.0' );
    }


    public function unity_load_textdomain() {
        load_plugin_textdomain( 'unity-birthday-email', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    public function unity_sub_menu_page() {
        require_once( UNITY_PATH . '/inc/unity-settings.php' );
    }

    public function unity_settings_options() {
        require_once( UNITY_PATH . '/inc/unity-settings-options.php' );
    }

    public function plugin_action_links($actions){
        $actions[] = '<a href="'. esc_url( get_admin_url(null, 'users.php?page=unity-users-birthday-emails') ) .'">' . __('Settings', 'unity-birthday-email') . '</a>';
        return $actions;
    }

    public function unity_user_birthday_input($user) {
        require_once( UNITY_PATH . '/inc/user-birthday-input.php' );
    }

    public function uniry_user_profile_update($user_id) {
        if ( current_user_can( 'edit_user', $user_id ) ) {
            update_user_meta( $user_id, 'unity_birth_date', sanitize_text_field( $_POST['unity_birth_date'] ) );
        }
    }

    public function event_trigger_schedule() {
        if ( ! wp_next_scheduled( 'unity_daily_event' ) ) {
            wp_schedule_event( time(), 'hourly', 'unity_daily_event' );
        }
    }

    public function unity_mail_function() {

        // if (gmdate('G') !== "8") {
        //     return;
        // }

        $todayDay = date('j');
        $todayMonth = date('F');

        $args = array(
            'meta_query' => array(
                array(
                    'key' => 'mepr_birth_day',
                    'value' => $todayDay, // 2023-11-13 == 13
                    'compare' => '=',
                    'type' => 'NUMERIC',
                ),
            ),
            'fields' => 'all_with_meta',
        );

        $args = apply_filters( 'unity_users_birth_date_query_arg', $args );

        $user_query = new WP_User_Query($args);
       
        if (!empty($user_query->results)) {
            foreach ($user_query->results as $user) {
                if ($user->exists()) {


                    if ($user->has_prop('mepr_birth_day')) $birthday = $user->get('mepr_birth_day');
                    else $birthday = '';
                    if ($user->has_prop('mepr_birth_month')) $birthmonth = $user->get('mepr_birth_month');
                    else $birthmonth = '';

                    $birthday = apply_filters( 'unity_users_birth_day', $birthday );
                    $birthmonth = apply_filters( 'unity_users_birth_month', $birthmonth );


                    if ($todayDay == $birthday && strtolower($todayMonth) == $birthmonth) {

                        $username = '';
                        if($user->get('first_name')){
                            $username = $user->get('first_name');
                        }elseif($user->get('nickname')){
                            $username = $user->get('nickname');
                        }else{
                            $username = $user->get('display_name');
                        }

                        $to = $user->get('user_email');
                        $subject = 'Happy Birthday ' . $username . '!';
                        $headers = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        $headers .= 'Brussels Women\'s Club <info@brusselswomens.club>' . "\r\n";
                        $message = '
                            <html>
                            <head>
                            <title>Happy Birthday</title>
                            </head>
                            <body>
                            <p><img src="https://brusselswomens.club/wp-content/uploads/2022/02/BWCBday.jpeg" alt="Happy Birthday"></p>
                            </body>
                            </html>
                            ';
                        if(wp_mail( $to,$subject,$message,$headers )){
                            wp_mail('khiron141@gmail.com', 'Notification of Birthday Email Sent', 'A birthday email was sent to ' . $username . '.', $headers);
                        }
                    }
                }
            }
        }else{
            return;
        }
    }


    public static function getInstance() {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }
}

Unity_Birthday::getInstance();