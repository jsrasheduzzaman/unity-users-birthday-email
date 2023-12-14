<?php

add_submenu_page(
    'users.php',
    __( 'Unity Users Birthday Email Settings', 'unity-birthday-email' ),
    __( 'Birthday Emails Settings', 'unity-birthday-email' ),
    'manage_options',
    'unity-users-birthday-emails',
    'unity_settings_callback'
);


function unity_settings_callback() {
	if( ! current_user_can( 'manage_options' ) ){
		return;
	}

	if ( isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], '-1') && isset( $_GET['settings-updated'] ) ) {
			add_settings_error( 'unity_birthday_messages', 'unity_birthday_message', __( 'Settings Saved', 'unity-birthday-email' ), 'updated' );
	}

	settings_errors( 'unity_birthday_messages' );


	?>

	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<form action="options.php" method="post">
			<?php
			wp_nonce_field( 'unity_bday_submenu_action', 'unity-bday-submenu-name' );
			settings_fields('unity_birthday_settings');
			do_settings_sections('unity_birthday_settings');
			submit_button( 'Save Changes' );
			?>
		</form>
	</div>

	<?php
}