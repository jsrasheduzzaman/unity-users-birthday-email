<?php
class Unity_Birthday_Input {

    private static $instances = [];

    protected function __construct() {
        add_action( 'show_user_profile', [ $this, 'unity_user_birthday_input' ] );
        add_action( 'edit_user_profile', [ $this, 'unity_user_birthday_input' ] );
        add_action( 'personal_options_update', [ $this, 'uniry_user_profile_update' ] );
        add_action( 'edit_user_profile_update', [ $this, 'uniry_user_profile_update' ] );
    }

    public function unity_user_birthday_input($user) { ?>
        <h2> <?php esc_html_e('Birthday Information','unity-users-birthday-email'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label for="unity-birth-date"><?php esc_html_e( 'Birth Date', 'unity-users-birthday-email' ); ?></label></th>
                <td>
                    <input type="hidden" name="unity-birthdate-validity" value="<?php echo esc_attr( wp_create_nonce( 'unity_birthdate_nonce_action' ) ); ?>" />
                    <input type="date" name="unity-birth-date" id="unity-birth-date" value="<?php echo esc_attr( get_the_author_meta( 'unity-birth-date', $user->ID ) ); ?>" max="<?php echo esc_attr(gmdate("Y-m-d")); ?>" />
                </td>
            </tr>
        </table>
        <?php
    }
   
    public function uniry_user_profile_update($user_id) {
        if ( isset( $_REQUEST['unity-birthdate-validity'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['unity-birthdate-validity'] ) ), 'unity_birthdate_nonce_action' ) && current_user_can( 'edit_user', $user_id ) ) {
            update_user_meta( $user_id, 'unity-birth-date', sanitize_text_field( $_POST['unity-birth-date'] ) );
        }else{
            die( __( 'Security check faild', 'unity-users-birthday-email' ) );
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