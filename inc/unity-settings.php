<?php
class Unity_Birthday_SettingsPage {

    private static $instances = [];

    protected function __construct() {
        add_action( 'admin_menu', [ $this, 'unity_user_menu_page' ] );
    }

    public function unity_user_menu_page() { 
    	add_submenu_page(
		    'users.php',
		    __( 'Users Birthday Email Settings', 'unity-users-birthday-email' ),
		    __( 'Birthday Emails Settings', 'unity-users-birthday-email' ),
		    'manage_options',
		    'unity-users-birthday-emails',
		    [ $this, 'unity_settings_callback' ]
		);
    }
   
    public function unity_settings_callback() {
		if( ! current_user_can( 'manage_options' ) ){
			return;
		}

		$birthday_users = Unity_Birthday::getInstance()->get_birthday_users_today();
		$tomorrow_birthday_users = Unity_Birthday::getInstance()->get_birthday_users_tomorrow();

		if ( isset($_REQUEST['_wpnonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), '-1') && isset( $_GET['settings-updated'] ) ) {
				add_settings_error( 'unity_birthday_messages', 'unity_birthday_message', __( 'Settings Saved', 'unity-users-birthday-email' ), 'updated' );
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

			<h2><?php esc_html_e( 'Birthdays Today', 'unity-users-birthday-email' ); ?></h2>
			<?php if ( isset( $_GET['birthday-emails-disabled'] ) ) : ?>
				<div class="notice notice-warning"><p><?php esc_html_e( 'Birthday email sending is disabled in the settings.', 'unity-users-birthday-email' ); ?></p></div>
			<?php endif; ?>
			<?php if ( isset( $_GET['birthday-emails-sent'] ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( __( 'Birthday emails sent to %d users.', 'unity-users-birthday-email' ), absint( $_GET['birthday-emails-sent'] ) ) ); ?></p></div>
			<?php endif; ?>

			<?php if ( empty( $birthday_users ) ) : ?>
				<p><?php esc_html_e( 'No users have a birthday today.', 'unity-users-birthday-email' ); ?></p>
			<?php else : ?>
				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="unity_send_birthday_emails">
					<?php wp_nonce_field( 'unity_send_birthday_emails' ); ?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<td class="manage-column column-cb check-column"><input type="checkbox" class="unity-birthday-select-all" aria-label="<?php esc_attr_e( 'Select all birthday users', 'unity-users-birthday-email' ); ?>"></td>
								<th scope="col"><?php esc_html_e( 'Username', 'unity-users-birthday-email' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Name', 'unity-users-birthday-email' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Email', 'unity-users-birthday-email' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $birthday_users as $birthday_user ) : ?>
								<tr>
									<th scope="row" class="check-column"><input type="checkbox" name="birthday_user_ids[]" value="<?php echo esc_attr( $birthday_user->ID ); ?>" class="unity-birthday-user"></th>
									<td><?php echo esc_html( $birthday_user->user_login ); ?></td>
									<td><?php echo esc_html( $birthday_user->display_name ); ?></td>
									<td><?php echo esc_html( $birthday_user->user_email ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php submit_button( __( 'Send Instant Birthday Emails', 'unity-users-birthday-email' ) ); ?>
				</form>
			<?php endif; ?>

			<h2><?php esc_html_e( 'Birthdays Tomorrow', 'unity-users-birthday-email' ); ?></h2>
			<?php if ( empty( $tomorrow_birthday_users ) ) : ?>
				<p><?php esc_html_e( 'No users have a birthday tomorrow.', 'unity-users-birthday-email' ); ?></p>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Username', 'unity-users-birthday-email' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Name', 'unity-users-birthday-email' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Email', 'unity-users-birthday-email' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $tomorrow_birthday_users as $birthday_user ) : ?>
							<tr>
								<td><?php echo esc_html( $birthday_user->user_login ); ?></td>
								<td><?php echo esc_html( $birthday_user->display_name ); ?></td>
								<td><?php echo esc_html( $birthday_user->user_email ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<?php
	}

    public static function getInstance() {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }
}