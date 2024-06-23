<?php
/**
 * Plugin Name: Voiceflow
 * Description: Adds Voiceflow chat widget to your site.
 * Version: 1.0.0
 * Author: Umbral.ai
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define constants for file paths.
define( 'VOICEFLOW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'VOICEFLOW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include other files.
require_once VOICEFLOW_PLUGIN_PATH . 'includes/admin/settings.php';
require_once VOICEFLOW_PLUGIN_PATH . 'includes/enqueue-scripts.php';
require_once VOICEFLOW_PLUGIN_PATH . 'includes/wp-voiceflow-view-transcripts.php';








//require_once plugin_dir_path( __FILE__ ) . 'includes/admin/wp-voiceflow-view-transcripts.php';



// ... other includes as needed ...

// Add a new function to create the menu item and the settings page.
function voiceflow_add_admin_menu() {
    // Add a new top-level menu (ill-advised):
    add_menu_page(
        'Voiceflow Settings', // Page title
        'Voiceflow', // Menu title
        'manage_options', // Capability
        'voiceflow', // Menu slug
        'voiceflow_settings_page' // Function that handles the page content
    );

    // Add a submenu page under the Voiceflow menu:
    add_submenu_page(
        'voiceflow', // Parent slug
        'View Transcripts', // Page title
        'View Transcripts', // Menu title
        'manage_options', // Capability
        'voiceflow_transcripts', // Menu slug
        'voiceflow_transcripts_page' // Function that handles the page content
    );

    // Register a setting for our options.
   register_setting( 'voiceflow', 'voiceflow_options' );
   register_setting( 'voiceflow', 'voiceflow_include_content' );

    // Add a section to the settings page.
    add_settings_section(
        'voiceflow_section', // ID
        'Voiceflow Settings', // Title
        'voiceflow_section_callback', // Callback
        'voiceflow' // Page on which to add this section of options
    );

    // Add a field to the 'voiceflow_section' section of our page
    add_settings_field(
        'voiceflow_include_content', // ID
        'Include current page Content', // Title 
        'voiceflow_include_content_callback', // Callback
        'voiceflow', // Page
        'voiceflow_section' // Section           
    );
}

// Hook into the 'admin_menu' action.
add_action( 'admin_menu', 'voiceflow_add_admin_menu' );

// Function to handle the settings page content.
function voiceflow_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Add error/update messages
    // Check if the user have submitted the settings
    // WordPress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // Add settings saved message with the class of "updated"
        add_settings_error('voiceflow_messages', 'voiceflow_message', __('Settings Saved', 'voiceflow'), 'updated');
    }

    // Show error/update messages
    settings_errors('voiceflow_messages');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
        <?php
        // Output security fields for the registered setting "voiceflow"
        settings_fields('voiceflow');
        // Output setting sections and their fields
        do_settings_sections('voiceflow');
        // Output save settings button
        submit_button('Save Settings');
        ?>
        </form>
    </div>
    <?php
}

// Function to handle the section callback.
function voiceflow_section_callback( $arguments ) {
    switch( $arguments['id'] ){
        case 'voiceflow_section':
            echo 'Enter your settings below:';
            break;
    }
}

function voiceflow_include_content_callback() { 
    // Get the value of the setting we've registered with register_setting()
    $setting = get_option('voiceflow_include_content');
    // Output the field
    ?>
    <input type="checkbox" name="voiceflow_include_content" <?php checked( $setting, 'yes' ); ?> value="yes">
    <?php
}


?>