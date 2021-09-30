<?php
/**
 * This file contains the code for generating the plugin settings page
 */

require 'db_init.php';

define("sql_file", plugin_dir_path(__FILE__) . "database_structure.sql");

// This function implements the custom action called when the create tables button is clicked
function aah_create_tables_action() {
    $result = "";
    $result = aah_create_tables(sql_file);
    echo $result;
    die( __FUNCTION__ );
}

// This is an ajax request to create the tables
function aah_create_tables_request_ajax() { ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var data = {
                'action': 'aah_ajax_create_tables',
            };
            jQuery.post(ajaxurl, data, function(response) {
                if(response['success'] == 'true') {
                    alert('Tables created successfully');
                } else {
                    var error = (response['error'] ? response['error'] : 'unknown');
                    alert(`Table creation failed with error: ${error}`);
                }
            });
        });
    </script><?php
}

// This is an ajax action to create the tables
function aah_create_tables_action_ajax() {
    $result = aah_create_tables_ajax(sql_file);
    echo $result;
    wp_die();
}
add_action('wp_ajax_aah_ajax_create_tables', 'aah_create_tables_ajax');


// hook to add custom action
add_action( 'admin_post_aah_create_tables', 'aah_create_tables_request_ajax' );

function aah_configure_settings_page() {
    add_menu_page( "Adopt-a-Hemlock Settings", "Adopt-a-Hemlock Settings",
        "manage_options", "adopt-a-hemlock", "aah_render_tables_create_page");
}

// hook to add settings to admin menu
add_action( 'admin_menu', 'aah_configure_settings_page' );

function aah_render_tables_create_page() {
    ?>

    <h2>Create Database Tables</h2>

    <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
        <input type="hidden" name="action" value="aah_create_tables">
        <?php submit_button( 'Create' ); ?>
    </form>
    <?php
}