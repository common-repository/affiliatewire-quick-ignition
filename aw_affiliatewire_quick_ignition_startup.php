<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//setup functions
function aw_affiliatewire_quick_ignition_install() {
    add_option("aw_affiliatewire_quick_ignition_setting", 'enable');
    add_option("aw_affiliatewire_quick_ignition_productsCount", '0');
    add_option("aw_affiliatewire_quick_ignition_products", array());
    add_option("aw_affiliatewire_quick_ignition_affiliateAliases", array());
    add_option("aw_affiliatewire_quick_ignition_merchants", array());
    add_option("aw_affiliatewire_quick_ignition_productsUpdate", 'Never');
}

function aw_affiliatewire_quick_ignition_remove() {
    delete_option('aw_affiliatewire_quick_ignition_setting');
    delete_option('aw_affiliatewire_quick_ignition_affiliateAliases');
    delete_option('aw_affiliatewire_quick_ignition_products');
    delete_option('aw_affiliatewire_quick_ignition_productsCount');
    delete_option('aw_affiliatewire_quick_ignition_productsUpdate');
    delete_option('aw_affiliatewire_quick_ignition_merchants');
}

function aw_affiliatewire_quick_ignition_unisntall() {
    delete_option('aw_affiliatewire_quick_ignition_setting');
    delete_option('aw_affiliatewire_quick_ignition_products');
    delete_option('aw_affiliatewire_quick_ignition_productsCount');
    delete_option('aw_affiliatewire_quick_ignition_productsUpdate');
    delete_option('aw_affiliatewire_quick_ignition_affiliateAliases');
    delete_option('aw_affiliatewire_quick_ignition_merchants');
}


//menu for settings page
function aw_affiliatewire_quick_ignition_menu_loader() {
    include('aw_affiliatewire_quick_ignition_menu.php');
}

//menu for products page
function aw_affiliatewire_quick_ignition_products_menu_loader() {
    include('aw_affiliatewire_quick_ignition_products_menu.php');
}

//menu for split test page
function aw_affiliatewire_quick_ignition_split_testing_menu_loader() {
    include('aw_affiliatewire_quick_ignition_split_menu.php');
}


//creates the links on the left hand sidebar for the plugin
function aw_affiliatewire_quick_ignition_menu() {
    //set the basic variables
    $icon = plugin_dir_url( __FILE__ ).'images/rw.ico';
    $page_title = 'AffiliateWire Quick Ignition: Settings';
    $menu_title = 'AffiliateWire Quick Ignition';
    $capability = 'manage_options';
    $menu_slug = 'aw_affiliatewire_quick_ignition_menu_loader';
    $function = 'aw_affiliatewire_quick_ignition_menu_loader';

    //create the admin settings page and overall leftbar link
    $page = add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon);
    add_action( "admin_print_scripts-$page", 'aw_affiliatewire_quick_ignition_admin_scripts' );

    // We add this submenu page with the same slug as the parent to ensure we don't get duplicates
    // This sets clicking on the main link to aslo click on a sublink with a different name
    $sub_menu_title = 'Settings';
    $page = add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function);
    add_action( "admin_print_scripts-$page", 'aw_affiliatewire_quick_ignition_admin_scripts' );


    // Now add the submenu page for the Products page
    $submenu_page_title = 'AffiliateWire Quick Ignition: Products';
    $submenu_title = 'Products';
    $submenu_slug = 'aw_affiliatewire_quick_ignition_products_menu_loader';
    $submenu_function = 'aw_affiliatewire_quick_ignition_products_menu_loader';
    $page = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
    add_action( "admin_print_scripts-$page", 'aw_affiliatewire_quick_ignition_admin_scripts' );

    // Now add the submenu page for the Products page
    $submenu_page_title = 'AffiliateWire Quick Ignition: Split Testing';
    $submenu_title = 'Split Testing';
    $submenu_slug = 'aw_affiliatewire_quick_ignition_split_testing_menu_loader';
    $submenu_function = 'aw_affiliatewire_quick_ignition_split_testing_menu_loader';
    $page = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
    add_action( "admin_print_scripts-$page", 'aw_affiliatewire_quick_ignition_admin_scripts' );
}

//add sorttable script to admin settings page
function aw_affiliatewire_quick_ignition_admin_scripts()
{
    //We can include as many Javascript files as we want here.
    wp_enqueue_script('aw_jquery_install', plugins_url('/js/jquery-1.7.min.js', __FILE__), array('jquery'));
    wp_enqueue_script('aw_tablesorder_install', plugins_url('/js/jquery.tablesorter.min.js', __FILE__), array('jquery'));
}


// add a settings link to the plugin in the plugin list
function aw_affiliatewire_quick_ignition_settings_link($links, $file) {
    //echo "<br/>".basename(dirname(__FILE__));
    if ($file == plugin_basename(basename(dirname(__FILE__)) . '/aw_affiliatewire_quick_ignition.php')) {

        $title = __('Settings for this Plugin', 'AffiliateWire Quick Ignition Program');
        $message = __('Settings', 'AffiliateWire Quick Ignition');
        $settings_link = '<a href="admin.php?page=aw_affiliatewire_quick_ignition_menu_loader" title="' . $title . '">' . $message . '</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

?>
