<?php
echo '<h2>' . __('Birthday Information','unity-birthday-email') . '</h2>';
?>
<table class="form-table">
    <tr>
        <th><label for="unity_birth_date"><?php _e( 'Birth Date', 'unity-birthday-email' ); ?></label></th>
        <td>
            <input type="date" name="unity_birth_date" id="unity_birth_date" value="<?php echo esc_attr( get_the_author_meta( 'unity_birth_date', $user->ID ) ); ?>" max="<?php echo esc_attr(date("Y-m-d")); ?>" />
        </td>
    </tr>
</table>

<?php