<?php
register_setting( 'unity_birthday_settings', 'unity_birthday_setting' );

add_settings_section(
	'unity_birthday_inputs',
	'Email Settings',
	'__return_null',
	'unity_birthday_settings'
);
add_settings_section(
	'unity_birthday_notify_too',
	'Notification Too',
	'__return_null',
	'unity_birthday_settings'
);



add_settings_field(
	'unity_sending_email_time',
	__('When to Start Sending Emails', 'unity-birthday-email'),
	'unity_set_sending_email_time',
	'unity_birthday_settings',
	'unity_birthday_inputs'
);

add_settings_field(
	'unity_from_name',
	__('From Name', 'unity-birthday-email'),
	'unity_set_sending_from_name',
	'unity_birthday_settings',
	'unity_birthday_inputs'
);

add_settings_field(
	'unity_from_email',
	__('From Email', 'unity-birthday-email'),
	'unity_set_sending_from_email',
	'unity_birthday_settings',
	'unity_birthday_inputs'
);



add_settings_field(
	'unity_notify_too',
	__('Send Notification Too', 'unity-birthday-email'),
	'unity_set_notification_too',
	'unity_birthday_settings',
	'unity_birthday_notify_too'
);

add_settings_field(
	'unity_notify_too_email',
	__('Notification Too Email', 'unity-birthday-email'),
	'unity_set_notify_too_email',
	'unity_birthday_settings',
	'unity_birthday_notify_too'
);


function unity_set_sending_email_time() {
	$options = get_option( 'unity_birthday_setting' );
	if (is_array($options)) {
		$sendTime = isset($options['unity_set_email_time']) ? $options['unity_set_email_time'] : 0;
	}else{
		$sendTime = 0;
	}
	echo'<input type="number" name="unity_birthday_setting[unity_set_email_time]" value="'. $sendTime .'" min="0" max="23" step="1">';
}

function unity_set_sending_from_name() {
	$options = get_option( 'unity_birthday_setting' );
	if (is_array($options)) {
		$fromName = isset($options['unity_set_from_name']) ? $options['unity_set_from_name'] : 'Test From name';
	}else{
		$fromName = 'Test From name';
	}
	echo'<input type="text" name="unity_birthday_setting[unity_set_from_name]" value="'. $fromName .'">';
}

function unity_set_sending_from_email() {
	$options = get_option( 'unity_birthday_setting' );
	if (is_array($options)) {
		$fromEmail = isset($options['unity_set_from_email']) ? $options['unity_set_from_email'] : 'test@test.test';
	}else{
		$fromEmail = 'test@test.test';
	}
	echo'<input type="email" name="unity_birthday_setting[unity_set_from_email]" value="'. $fromEmail .'">';
}


function unity_set_notification_too() {
	$options = get_option( 'unity_birthday_setting' );
	if (is_array($options)) {
		$checked = isset($options['unity_set_notification_too']) ? true : false;
	} else {
		$checked = true;
	}

	echo'<label class="unity_switch">
	  <input class="unity_input" type="checkbox" name="unity_birthday_setting[unity_set_notification_too]" '. checked( $checked, true, false ) .'>
	  <span class="unity_toggle"></span>
	</label>';
}

function unity_set_notify_too_email() {
	$options = get_option( 'unity_birthday_setting' );
	if (is_array($options)) {
		$notify2Email = isset($options['unity_set_notify_too_email']) ? $options['unity_set_notify_too_email'] : 'admin@test.test';
	}else{
		$notify2Email = 'admin@test.test';
	}
	echo'<input type="email" name="unity_birthday_setting[unity_set_notify_too_email]" value="'. $notify2Email .'">';
}