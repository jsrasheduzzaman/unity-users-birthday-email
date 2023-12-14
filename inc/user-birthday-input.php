<?php
echo '<h2>' . __('Birthday Information','unity-birthday-email') . '</h2>';
?>
<table class="form-table">
    <tr>
        <th><label for="unity-birth-date"><?php _e( 'Birth Date', 'unity-birthday-email' ); ?></label></th>
        <td>
            <input type="hidden" name="unity-birthdate-validity" value="<?php echo wp_create_nonce( 'unity_birthdate_nonce_action' ); ?>" />
            <input type="date" name="unity-birth-date" id="unity-birth-date" value="<?php echo esc_attr( get_the_author_meta( 'unity-birth-date', $user->ID ) ); ?>" max="<?php echo esc_attr(gmdate("Y-m-d")); ?>" />
        </td>
    </tr>
</table>

<?php