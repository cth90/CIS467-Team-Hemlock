<?php
/**
 * This file contains the code for generating the plugin settings page
 */

require 'db_init.php';

define("sql_file", plugin_dir_path(__FILE__) . "database_structure.sql");
define("csv_files", array(plugin_dir_path(__FILE__) . "cemetery_and_mulligan.csv",
    plugin_dir_path(__FILE__) . "duncan.csv"));

// This is an ajax action to create the tables
function aah_create_tables_action_ajax()
{
    $final_result['success'] = 'false';

    $result = aah_create_tables(sql_file);

    if ($result['success'] == 'true') {
        $populate_result = aah_read_csv(csv_files[0]);
        if ($populate_result <= 0) {
            $final_result['error'] = error_get_last();
            $final_result['success'] = 'false';
        } else {
            $populate_result = aah_read_csv(csv_files[1]);
            if ($populate_result <= 0) {
                $final_result['error'] = error_get_last();
                $final_result['success'] = 'false';
            } else {
                $final_result['success'] = 'true';
            }
        }
    }

    wp_send_json($final_result);
}

// Add the hook for the ajax action
add_action('wp_ajax_aah_ajax_create_tables', 'aah_create_tables_action_ajax');

// This adds the menu page for our plugin
function aah_configure_settings_page()
{
    add_menu_page("Adopt-a-Hemlock Settings", "Adopt-a-Hemlock Settings",
        "manage_options", "adopt-a-hemlock", "aah_render_tables_create_page");
}

// hook to add settings to admin menu
add_action('admin_menu', 'aah_configure_settings_page');

// This renders the settings page
function aah_render_tables_create_page()
{
    ?>
    <div class="aah-create-tables">
        <h2>Create Database Tables and Populate Tree Database</h2>
        <button class="aah-create-tables-button">Do it!</button>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.aah-create-tables-button').click(function (event) {
                    var data = {
                        'action': 'aah_ajax_create_tables',
                    };
                    $.post(ajaxurl, data, function (response) {
                        if (response['success'] == 'true') {
                            alert('Tables created and populated successfully');
                        } else {
                            var error = (response['error'] ? response['error'] : 'unknown');
                            alert(`Table creation failed with error: ${error}`);
                        }
                    });
                });
            });
        </script>
    </div>
    <?php
}