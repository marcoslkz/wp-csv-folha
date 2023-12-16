<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://fsylum.net
 * @since      1.0.0
 *
 * @package    csv_contracheque
 * @subpackage csv_contracheque/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <form action='<?= $_SERVER['REQUEST_URI']; ?>'  method="post" enctype="multipart/form-data">
        <?php
            settings_fields( $this->plugin_name );
            do_settings_sections( $this->plugin_name );
            //submit_button();
        ?>
        <p class="submit">
            <input type="submit" name="upload" id="upload" class="button button-primary" value="Enviar Arquivo">
        </p>
    </form>
</div>
