<?php
/**
 * Plugin Name: Users Birthday Email
 * Plugin URI: //webfydev.com/products/plugins/users-birthday-email
 * Description: Users Birthday Email automatically send an email to WordPress users on their birthday. This is very easy to use with any membership plugins.
 * Version: 1.0.8.0
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
        add_action( 'admin_notices', [ $this, 'unity_cron_requirement_notice' ] );
        add_action( 'wp_ajax_unity_dismiss_cron_notice', [ $this, 'unity_dismiss_cron_notice' ] );
        add_action( 'admin_post_unity_send_birthday_emails', [ $this, 'unity_send_selected_birthday_emails' ] );
    }

    public function unity_admin_scripts($hook) {
        if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON && current_user_can( 'manage_options' ) ) {
            wp_enqueue_script( 'jquery' );
            $notice_script = 'document.addEventListener("click", function(event) {
                if (!event.target.closest(".unity-cron-notice .notice-dismiss")) return;
                fetch(ajaxurl, {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"},
                    body: new URLSearchParams({
                        action: "unity_dismiss_cron_notice",
                        nonce: ' . wp_json_encode( wp_create_nonce( 'unity_dismiss_cron_notice' ) ) . '
                    })
                });
            });';
            wp_add_inline_script( 'jquery', $notice_script );
        }

        if( "users_page_unity-users-birthday-emails" != $hook ) {
            return;
        }
        wp_enqueue_script( 'jquery' );
        wp_add_inline_script( 'jquery', 'jQuery(function($) {
            $(".unity-birthday-select-all").on("change", function() {
                $(".unity-birthday-user").prop("checked", this.checked);
            });
            $(".unity-birthday-user").on("change", function() {
                if (!this.checked) $(".unity-birthday-select-all").prop("checked", false);
            });
        });' );
        wp_enqueue_style( 'unity-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css', [], '1.0.0' );
    }

    public function unity_load_textdomain() {
        load_plugin_textdomain( 'unity-users-birthday-email', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    public function plugin_action_links($actions){
        $actions[] = '<a href="'. esc_url( get_admin_url(null, 'users.php?page=unity-users-birthday-emails') ) .'">' . __('Settings', 'unity-users-birthday-email') . '</a>';
        $actions[] = '<a href="//webfydev.com/products/plugins/users-birthday-email">' . __('Docs', 'unity-users-birthday-email') . '</a>';
        return $actions;
    }

    public function unity_cron_requirement_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
            $screen = get_current_screen();
            $is_birthday_settings_page = $screen && 'users_page_unity-users-birthday-emails' === $screen->id;

            if ( ! $is_birthday_settings_page && (int) get_user_meta( get_current_user_id(), '_unity_cron_notice_dismissed_until', true ) > time() ) {
                return;
            }
            ?>
            <div class="notice notice-warning <?php echo $is_birthday_settings_page ? '' : 'is-dismissible'; ?> unity-cron-notice">
                <p>
                    <strong>Users Birthday Email:</strong>
                    WordPress Cron is disabled (DISABLE_WP_CRON = true).
                    Birthday emails will only work if your hosting server has a real cron job configured to run wp-cron.php regularly.
                </p>
            </div>
            <?php
        }
    }

    public function unity_dismiss_cron_notice() {
        check_ajax_referer( 'unity_dismiss_cron_notice', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [], 403 );
        }

        update_user_meta( get_current_user_id(), '_unity_cron_notice_dismissed_until', time() + DAY_IN_SECONDS );
        wp_send_json_success();
    }

    public function event_trigger_schedule() {
        if ( ! wp_next_scheduled( 'unity_daily_event' ) ) {
            wp_schedule_event( time(), 'hourly', 'unity_daily_event' );
        }
    }

    public function unity_mail_function() {
        $options = get_option( 'unity_birthday_setting' );
        if ( ! $this->is_birthday_email_enabled( $options ) ) {
            return;
        }

        $send_time = is_array( $options ) && isset( $options['unity_set_email_time'] ) ? $options['unity_set_email_time'] : 0;
        if ( (int) gmdate( 'G' ) !== (int) $send_time ) {
            return;
        }

        foreach ( $this->get_birthday_users_today() as $user ) {
            $this->send_birthday_email( $user );
        }
    }

    public function is_birthday_email_enabled( $options = null ) {
        if ( null === $options ) {
            $options = get_option( 'unity_birthday_setting' );
        }

        return ! is_array( $options ) || ! array_key_exists( 'unity_enable_birthday_email', $options ) || '1' === (string) $options['unity_enable_birthday_email'];
    }

    public function get_birthday_users_today() {
        return $this->get_birthday_users_for_date( time() );
    }

    public function get_birthday_users_tomorrow() {
        return $this->get_birthday_users_for_date( time() + DAY_IN_SECONDS );
    }

    private function get_birthday_users_for_date( $timestamp ) {
        $target_day = gmdate( 'd', $timestamp );
        $target_month = gmdate( 'm', $timestamp );
        $birthday_key = apply_filters( 'unity_users_birthday_meta_key', 'unity-birth-date' );
        $args = [
            'meta_query' => [
                [
                    'key' => $birthday_key,
                    'value' => '-' . $target_month . '-' . $target_day,
                    'compare' => 'RLIKE',
                ],
            ],
        ];
        $user_query = new WP_User_Query( apply_filters( 'unity_users_birth_date_query_args', $args ) );
        $birthday_users = [];

        foreach ( $user_query->results as $user ) {
            $birthdate = get_user_meta( $user->ID, $birthday_key, true );
            $date = $birthdate ? date_create( $birthdate ) : false;
            $birthday = $date ? date_format( $date, 'd' ) : '';
            $birthmonth = $date ? date_format( $date, 'm' ) : '';
            $birthday = apply_filters( 'unity_users_birth_day_format', $birthday, $user );
            $birthmonth = apply_filters( 'unity_users_birth_month_format', $birthmonth, $user );

            if ( $target_day == $birthday && $target_month == $birthmonth ) {
                $birthday_users[] = $user;
            }
        }

        return $birthday_users;
    }

    public function send_birthday_email( $user ) {
        $options = get_option( 'unity_birthday_setting' );
        if ( ! $this->is_birthday_email_enabled( $options ) ) {
            return false;
        }

        $user_name = $user->get( 'user_login' );
        $first_name = $user->get( 'first_name' ) ?: $user_name;
        $last_name = $user->get( 'last_name' ) ?: $user_name;
        $nickname = $user->get( 'user_nicename' ) ?: $user_name;
        $display_name = $user->get( 'display_name' ) ?: $user_name;
        $full_name = $user->get( 'first_name' ) ? $first_name . ' ' . $last_name : $user_name;
        $from_name = is_array( $options ) && isset( $options['unity_set_from_name'] ) ? $options['unity_set_from_name'] : get_bloginfo( 'name' );
        $from_email = is_array( $options ) && isset( $options['unity_set_from_email'] ) ? $options['unity_set_from_email'] : get_bloginfo( 'admin_email' );
        $default_subject = is_array( $options ) ? 'Happy Birthday @username' : 'Happy Birthday @firstname@';
        $subject = is_array( $options ) && isset( $options['unity_email_temp_sub'] ) ? $options['unity_email_temp_sub'] : $default_subject;
        $description = is_array( $options ) && isset( $options['unity_email_temp_desc'] ) ? $options['unity_email_temp_desc'] : '<h2>Happy Birthday @firstname@</h2> <img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'images/unity-birthday-email.jpg">';
        $notify = is_array( $options ) && isset( $options['unity_set_notification_too'] );
        $notify_email = is_array( $options ) && isset( $options['unity_set_notify_too_email'] ) ? $options['unity_set_notify_too_email'] : get_bloginfo( 'admin_email' );
        $replacements = [
            '@username@' => $user_name,
            '@fullname@' => $full_name,
            '@firstname@' => $first_name,
            '@lastname@' => $last_name,
            '@nickname@' => $nickname,
            '@displayname@' => $display_name,
        ];
        $from_name = str_replace( array_keys( $replacements ), array_values( $replacements ), $from_name );
        $subject = str_replace( array_keys( $replacements ), array_values( $replacements ), $subject );
        $description = str_replace( array_keys( $replacements ), array_values( $replacements ), $description );
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
        ];
        $head_style = apply_filters( 'unity_users_birth_day_email_head_style', '' );
        $message = '<html><head><title>Happy Birthday</title>' .
            ( ! empty( $head_style ) ? '<style>' . $head_style . '</style>' : '' ) .
            '</head><body class="unity-birthday-email-body"><div class="unity-birthday-email-content">' .
            html_entity_decode( $description ) . '</div></body></html>';
        $sent = wp_mail( $user->get( 'user_email' ), $subject, $message, $headers );

        if ( $sent && $notify ) {
            wp_mail( $notify_email, 'Notification of Birthday Email Sent', 'A birthday email was sent to ' . $full_name . '.', $headers );
        }

        return $sent;
    }

    public function unity_send_selected_birthday_emails() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to send birthday emails.', 'unity-users-birthday-email' ) );
        }
        check_admin_referer( 'unity_send_birthday_emails' );

        if ( ! $this->is_birthday_email_enabled() ) {
            wp_safe_redirect( add_query_arg( 'birthday-emails-disabled', '1', admin_url( 'users.php?page=unity-users-birthday-emails' ) ) );
            exit;
        }

        $selected_ids = isset( $_POST['birthday_user_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['birthday_user_ids'] ) ) : [];
        $sent_count = 0;
        foreach ( $this->get_birthday_users_today() as $user ) {
            if ( in_array( (int) $user->ID, $selected_ids, true ) && $this->send_birthday_email( $user ) ) {
                $sent_count++;
            }
        }

        wp_safe_redirect( add_query_arg( [ 'birthday-emails-sent' => $sent_count ], admin_url( 'users.php?page=unity-users-birthday-emails' ) ) );
        exit;
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