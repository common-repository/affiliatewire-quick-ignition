<?php
/*
 Plugin Name: AffiliateWire Quick Ignition Plugin
 Plugin URI: https://affiliate.revenuewire.com/
 Description: Plugin for publishing affiliate advertising for <a href="https://affiliate.revenuewire.com/" target="_blank">https://affiliate.revenuewire.com/</a>
 Version: 1.0.9
 */

require_once('aw_affiliatewire_quick_ignition_startup.php');

//  Add blank settings on install
register_activation_hook(__FILE__,'aw_affiliatewire_quick_ignition_install');

//disable the plugin
register_deactivation_hook( __FILE__, 'aw_affiliatewire_quick_ignition_remove' );

//Remove Stored settings on uninstall
register_uninstall_hook( __FILE__, 'aw_affiliatewire_quick_ignition_unisntall' );

add_action('admin_menu', 'aw_affiliatewire_quick_ignition_menu');

//get if plugin is turned on
$plugin_setting = get_option('aw_affiliatewire_quick_ignition_setting');

//check if plugin is running
if ($plugin_setting == 'enable') {

    require_once('aw_affiliatewire_quick_ignition_runtime.php');

    aw_affiliatewire_quick_ignition_run();

}

add_filter("plugin_action_links", 'aw_affiliatewire_quick_ignition_settings_link', 10, 2);

?>