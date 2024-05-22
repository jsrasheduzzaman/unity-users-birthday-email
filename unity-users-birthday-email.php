<?php
/**
 * Plugin Name: Users Birthday Email
 * Plugin URI: //webfydev.com/our-plugins/users-birthday-email.html
 * Description: Users Birthday Email automatically send an email to WordPress users on their birthday. This is very easy to use with any membership plugins.
 * Version: 1.0.6
 * Requires at least: 5.5.1
 * Requires PHP: 7.2
 * Author: Webfydev
 * Author URI: //webfydev.com
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: unity-users-birthday-email
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define('UNITY_PATH', plugin_dir_path(__FILE__));

require_once( UNITY_PATH . '/inc/unity-settings.php' );
require_once( UNITY_PATH . '/inc/unity-settings-options.php' );
require_once( UNITY_PATH . '/inc/user-birthday-input.php' );


class Unity_Birthday {

    private static $instances = [];

    protected function __construct() {
        add_action( 'plugins_loaded', [$this, 'unity_load_textdomain'] );
        add_action( 'admin_enqueue_scripts', [ $this, 'unity_admin_scripts' ] );
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [$this, 'plugin_action_links'] );
        add_action( 'wp', [$this, 'event_trigger_schedule'] );
        add_action( 'unity_daily_event', [$this, 'unity_mail_function'] );
    }

    public function unity_admin_scripts($hook) {
        if( "users_page_unity-users-birthday-emails" != $hook ) {
            return;
        }
        wp_enqueue_style( 'unity-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css', [], '1.0.0' );
    }

    public function unity_load_textdomain() {
        load_plugin_textdomain( 'unity-users-birthday-email', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    public function plugin_action_links($actions){
        $actions[] = '<a href="'. esc_url( get_admin_url(null, 'users.php?page=unity-users-birthday-emails') ) .'">' . __('Settings', 'unity-users-birthday-email') . '</a>';
        $actions[] = '<a href="//webfydev.com/our-plugins/users-birthday-email.html">' . __('Docs', 'unity-users-birthday-email') . '</a>';
        return $actions;
    }

    public function event_trigger_schedule() {
        if ( ! wp_next_scheduled( 'unity_daily_event' ) ) {
            wp_schedule_event( time(), 'hourly', 'unity_daily_event' );
        }
    }

    public function unity_mail_function() {

        $options = get_option( 'unity_birthday_setting' );
        if (is_array($options)) {
            $sendTime = isset($options['unity_set_email_time']) ? $options['unity_set_email_time'] : 0;
        }else{
            $sendTime = 0;
        }

        if (gmdate('G') !== $sendTime) {
            return;
        }

        $todayDay       = gmdate('d');    // 1-31
        $todayMonth     = gmdate('m');    // 1-12
        $getBirthday = apply_filters( 'unity_users_birthday_meta_key', 'unity_birth_date' );

        $args = array(
            'meta_query' => array(
                array(
                    'key' => $getBirthday,
                    'value' => '-'.$todayMonth.'-'.$todayDay,
                    'compare' => 'RLIKE',
                ),
            ),
        );

        $args = apply_filters( 'unity_users_birth_date_query_args', $args );

        $user_query = new WP_User_Query($args);
       
        if (!empty($user_query->results)) {
            foreach ($user_query->results as $user) {
                if ($user->exists()) {
                    if ($user->has_prop('unity_birth_date')) $birthdate = $user->get('unity_birth_date');
                    else $birthdate = '';

                    $birthday = '';
                    $birthmonth = '';
                    if (!empty($birthdate)) {
                        $date = date_create($birthdate);
                        $birthday = date_format($date,"d");     // 1-31
                        $birthmonth = date_format($date,"m");   // 1-12
                    }

                    $birthday = apply_filters( 'unity_users_birth_day_format', $birthday, $user );
                    $birthmonth = apply_filters( 'unity_users_birth_month_format', $birthmonth, $user );


                    if ($todayDay == $birthday && $todayMonth == $birthmonth) {

                        $userName   = $user->get('user_login');
                        $fName      = $user->get('first_name') ?? $user->get('user_login');
                        $lname      = $user->get('last_name') ?? $user->get('user_login');
                        $niceName   = $user->get('user_nicename') ?? $user->get('user_login');
                        $disName    = $user->get('display_name') ?? $user->get('user_login');
                        $fullName   = $user->get('first_name') && $user->get('first_name') ? $fName .' '. $lname : $user->get('user_login');


                        if (is_array($options)) {
                            $sendTime = isset($options['unity_set_email_time']) ? $options['unity_set_email_time'] : 0;
                            $fromName = isset($options['unity_set_from_name']) ? $options['unity_set_from_name'] : get_bloginfo('name');
                            $fromEmail = isset($options['unity_set_from_email']) ? $options['unity_set_from_email'] : get_bloginfo('admin_email');
                            $mailSub = isset($options['unity_email_temp_sub']) ? $options['unity_email_temp_sub'] : 'Happy Birthday @username';
                            $mailDesc = isset($options['unity_email_temp_desc']) ? $options['unity_email_temp_desc'] : '<h2>Happy Birthday @firstname@</h2> <img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'images/unity-birthday-email.jpg">';
                            $checked = isset($options['unity_set_notification_too']) ? true : false;
                            $notify2Email = isset($options['unity_set_notify_too_email']) ? $options['unity_set_notify_too_email'] : get_bloginfo('admin_email');
                        }else{
                            $sendTime = 0;
                            $fromName = get_bloginfo('name');
                            $fromEmail = get_bloginfo('admin_email');
                            $mailSub = 'Happy Birthday @firstname@';
                            $mailDesc = '<h2>Happy Birthday @firstname@</h2> <img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'images/unity-birthday-email.jpg">';
                            $checked = false;
                            $notify2Email = get_bloginfo('admin_email');
                        }


                        // Replace
                        $fromName   =   str_replace("@username@",$userName,$fromName);
                        $mailSub    =   str_replace("@username@",$userName,$mailSub);
                        $mailDesc   =   str_replace("@username@",$userName,$mailDesc);

                        $fromName   =   str_replace("@fullname@",$fullName,$fromName);
                        $mailSub    =   str_replace("@fullname@",$fullName,$mailSub);
                        $mailDesc   =   str_replace("@fullname@",$fullName,$mailDesc);

                        $fromName   =   str_replace("@firstname@",$fName,$fromName);
                        $mailSub    =   str_replace("@firstname@",$fName,$mailSub);
                        $mailDesc   =   str_replace("@firstname@",$fName,$mailDesc);

                        $fromName   =   str_replace("@lastname@",$lname,$fromName);
                        $mailSub    =   str_replace("@lastname@",$lname,$mailSub);
                        $mailDesc   =   str_replace("@lastname@",$lname,$mailDesc);

                        $fromName   =   str_replace("@nickname@",$niceName,$fromName);
                        $mailSub    =   str_replace("@nickname@",$niceName,$mailSub);
                        $mailDesc   =   str_replace("@nickname@",$niceName,$mailDesc);

                        $fromName   =   str_replace("@displayname@",$disName,$fromName);
                        $mailSub    =   str_replace("@displayname@",$disName,$mailSub);
                        $mailDesc   =   str_replace("@displayname@",$disName,$mailDesc);


                        $to = $user->get('user_email');
                        $subject = $mailSub;
                        $headers = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        $headers .= $fromName . '<' . $fromEmail . '>' . "\r\n";
                        $message = '
                            <html>
                            <head>
                            <title>Happy Birthday</title>
                            </head>
                            <body>
                            <div>' . html_entity_decode($mailDesc) . '</div>
                            </body>
                            </html>
                            ';
                        if( wp_mail( $to,$subject,$message,$headers ) && $checked ){
                            wp_mail($notify2Email, 'Notification of Birthday Email Sent', 'A birthday email was sent to ' . $fullName . '.', $headers);
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


// Load Plugin
Unity_Birthday_SettingsPage::getInstance();
Unity_Birthday_Input::getInstance();
Unity_Birthday_SettingsOption::getInstance();
Unity_Birthday::getInstance();