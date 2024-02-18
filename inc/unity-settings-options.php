<?php
class Unity_Birthday_SettingsOption {

    private static $instances = [];

    protected function __construct() {
        add_action( 'admin_init', [ $this, 'unity_settings_options' ] );
    }

    public function unity_settings_options() {
    	register_setting( 'unity_birthday_settings', 'unity_birthday_setting' );

		add_settings_section(
			'unity_birthday_inputs',
			__('Email Settings' , 'unity-users-birthday-email'),
			'__return_null',
			'unity_birthday_settings'
		);
		add_settings_section(
			'unity_birthday_notify_too',
			__('Notification Too', 'unity-users-birthday-email'),
			'__return_null',
			'unity_birthday_settings'
		);
		add_settings_section(
			'unity_birthday_mail_temp',
			__('Email Template', 'unity-users-birthday-email'),
			'__return_null',
			'unity_birthday_settings'
		);



		add_settings_field(
			'unity_sending_email_time',
			__('When to Start Sending Emails', 'unity-users-birthday-email'),
			[ $this, 'unity_set_sending_email_time' ],
			'unity_birthday_settings',
			'unity_birthday_inputs'
		);

		add_settings_field(
			'unity_from_name',
			__('From Name', 'unity-users-birthday-email'),
			[ $this, 'unity_set_sending_from_name' ],
			'unity_birthday_settings',
			'unity_birthday_inputs'
		);

		add_settings_field(
			'unity_from_email',
			__('From Email', 'unity-users-birthday-email'),
			[ $this, 'unity_set_sending_from_email' ],
			'unity_birthday_settings',
			'unity_birthday_inputs'
		);



		add_settings_field(
			'unity_notify_too',
			__('Send Notification Too', 'unity-users-birthday-email'),
			[ $this, 'unity_set_notification_too' ],
			'unity_birthday_settings',
			'unity_birthday_notify_too'
		);

		add_settings_field(
			'unity_notify_too_email',
			__('Notification Too Email', 'unity-users-birthday-email'),
			[ $this, 'unity_set_notify_too_email' ],
			'unity_birthday_settings',
			'unity_birthday_notify_too'
		);



		add_settings_field(
			'unity_email_tamp_sub',
			__('Email Subject', 'unity-users-birthday-email'),
			[ $this, 'unity_email_tamp_subject' ],
			'unity_birthday_settings',
			'unity_birthday_mail_temp'
		);

		add_settings_field(
			'unity_email_temp_desc',
			__('Email Description', 'unity-users-birthday-email'),
			[ $this, 'unity_email_temp_description' ],
			'unity_birthday_settings',
			'unity_birthday_mail_temp'
		);
    }


    public function unity_set_sending_email_time() {
		$options = get_option( 'unity_birthday_setting' );
		if (is_array($options)) {
			$sendTime = isset($options['unity_set_email_time']) ? $options['unity_set_email_time'] : 0;
		}else{
			$sendTime = 0;
		}
		echo'<input type="number" name="unity_birthday_setting[unity_set_email_time]" value="'. esc_attr( $sendTime ) .'" min="0" max="23" step="1">';
	}

	public function unity_set_sending_from_name() {
		$options = get_option( 'unity_birthday_setting' );
		if (is_array($options)) {
			$fromName = isset($options['unity_set_from_name']) ? $options['unity_set_from_name'] : get_bloginfo('name');
		}else{
			$fromName = get_bloginfo('name');
		}
		echo'<input type="text" name="unity_birthday_setting[unity_set_from_name]" value="'. esc_attr( $fromName ) .'">';
	}

	public function unity_set_sending_from_email() {
		$options = get_option( 'unity_birthday_setting' );
		if (is_array($options)) {
			$fromEmail = isset($options['unity_set_from_email']) ? $options['unity_set_from_email'] : get_bloginfo('admin_email');
		}else{
			$fromEmail = get_bloginfo('admin_email');
		}
		echo'<input type="email" name="unity_birthday_setting[unity_set_from_email]" value="'. esc_attr( $fromEmail ) .'">';
	}


	public function unity_set_notification_too() {
		$options = get_option( 'unity_birthday_setting' );
		if (is_array($options)) {
			$checked = isset($options['unity_set_notification_too']) ? true : false;
		} else {
			$checked = false;
		}

		echo'<label class="unity_switch">
		  <input class="unity_input" type="checkbox" name="unity_birthday_setting[unity_set_notification_too]" '. checked( $checked, true, false ) .'>
		  <span class="unity_toggle"></span>
		</label>';
	}

	public function unity_set_notify_too_email() {
		$options = get_option( 'unity_birthday_setting' );
		if (is_array($options)) {
			$notify2Email = isset($options['unity_set_notify_too_email']) ? $options['unity_set_notify_too_email'] : get_bloginfo('admin_email');
		}else{
			$notify2Email = get_bloginfo('admin_email');
		}
		echo'<input type="email" name="unity_birthday_setting[unity_set_notify_too_email]" value="'. esc_attr( $notify2Email ) .'">';
	}


	public function unity_email_tamp_subject() {
		$options = get_option( 'unity_birthday_setting' );
		if (is_array($options)) {
			$mailSub = isset($options['unity_email_temp_sub']) ? $options['unity_email_temp_sub'] : 'Happy Birthday @username';
		}else{
			$mailSub = 'Happy Birthday @firstname@';
		}
		echo'<input type="text" name="unity_birthday_setting[unity_email_temp_sub]" value="'. esc_attr( $mailSub ) .'">';
	}

	public function unity_email_temp_description() {
		$options = get_option( 'unity_birthday_setting' );
		if (is_array($options)) {
			$mailDesc = isset($options['unity_email_temp_desc']) ? $options['unity_email_temp_desc'] : '<h2>Happy Birthday @firstname@</h2> <img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'images/unity-birthday-email.jpg">';
		}else{
			$mailDesc = '<h2>Happy Birthday @firstname@</h2> <img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'images/unity-birthday-email.jpg">';
		}

		$editor_id = "unity_email_temp_desc";
		$editor_name = "unity_birthday_setting[unity_email_temp_desc]";
		$args = array(
				'media_buttons' => true,
				'textarea_name' => $editor_name,
				'textarea_rows' => get_option('default_post_edit_rows', 30),
				'quicktags' => true,
			);
		wp_editor( html_entity_decode($mailDesc), $editor_id, $args );

		echo '<h4>Please Note:</h4><p>"@username@" will be replaced with the user\'s Username whose birthday it is.</p>
		<p>"@fullname@" will be replaced with the user\'s Full Name whose birthday it is.</p>
		<p>"@firstname@" will be replaced with the user\'s First Name whose birthday it is.</p>
		<p>"@lastname@" will be replaced with the user\'s Last Name whose birthday it is.</p>
		<p>"@nickname@" will be replaced with the user\'s Nick Name whose birthday it is.</p>
		<p>"@displayname@" will be replaced with the user\'s Display Name whose birthday it is.</p>
		<p>If the user doesn\'t have a special name, it will be replaced by the Username.</p>';
	}


    public static function getInstance() {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }
}