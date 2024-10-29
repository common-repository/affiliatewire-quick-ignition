<?php

function aw_update_metadata($post_id, $metadataName, $metadataValue) {
    mysql_select_db(DB_NAME);
    if($metadataValue == "") {
        delete_post_meta($post_id, $metadataName, get_post_meta($post_id, $metadataName, true));
    } elseif (get_post_meta($post_id, $metadataName, true) == "") {
        add_post_meta($post_id, $metadataName, $metadataValue, true);
    } elseif ($metadataValue != get_post_meta($post_id, $metadataName, true)) {
        update_post_meta($post_id, $metadataName, $metadataValue);
    }
}

function aw_get_metadata($post_type, $post_id, $meta_key = '', $single = false) {
    mysql_select_db(DB_NAME);
    return get_metadata($post_type, $post_id, $meta_key, $single );
}


if (!function_exists('aw_print_debug')) {
    function aw_print_debug($cat) {
        return '<pre style="text-align:left;background:#ffffff">'.wordwrap(print_r($cat,true),150,"\n").'</pre>';
    }
}

function aw_print_debug2($cat) {
    $toReturn = '<pre style="text-align:left;background:#ffffff">';
    if (is_array($cat)) {
        echo '<table style="border: 1px solid blue;padding: 0px;">';
        foreach ($cat as $key => $item) {
            echo '<tr><th style="border: 1px solid blue;padding: 4px;">'.$key.'</th><td style="border: 1px solid blue;padding: 4px;">';
            if (is_array($item)) {
                aw_print_debug2($item);
            } else {
                echo $item;
            }
            echo '</td></tr>';
        }
        echo '</table>';
    } else {
        echo '<table style="border: 1px solid blue;padding: 0px;">';
        echo '<tr><td style="border: 1px solid blue;padding: 4px;">';
        echo $cat;
        echo '</td></tr>';
        echo '</table>';
    }


    $toReturn .= '</pre>';

    return $toReturn;
}

function aw_get_posts($count = 10,$input_args = array()) {
    mysql_select_db(DB_NAME);
    $default_args = array(
    'numberposts'     => $count,
    'offset'          => 0,
    'category'        => '',
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'include'         => '',
    'exclude'         => '',
    'meta_key'        => '',
    'meta_value'      => '',
    'post_type'       => 'post',
    'post_mime_type'  => '',
    'post_parent'     => '',
    'post_status'     => 'publish' );
    $args = array_merge($input_args, $default_args);
    return get_posts($args);
}

function aw_get_all_posts($count = 10,$input_args = array()) {
    mysql_select_db(DB_NAME);
    $default_args = array(
    'numberposts'     => $count,
    'offset'          => 0,
    'category'        => '',
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'include'         => '',
    'exclude'         => '',
    'meta_key'        => '',
    'meta_value'      => '',
    'post_type'       => 'post',
    'post_mime_type'  => '',
    'post_parent'     => '',
    'post_status'     => array('draft', 'publish') );
    $args = array_merge($input_args, $default_args);
    return get_posts($args);
}

function aw_get_drafts($count = 10,$input_args = array()) {
    mysql_select_db(DB_NAME);
    $default_args = array(
    'numberposts'     => $count,
    'offset'          => 0,
    'category'        => '',
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'include'         => '',
    'exclude'         => '',
    'meta_key'        => '',
    'meta_value'      => '',
    'post_type'       => 'post',
    'post_mime_type'  => '',
    'post_parent'     => '',
    'post_status'     => array('draft') );
    $args = array_merge($input_args, $default_args);
    return get_posts($args);
}

function aw_get_post($postID) {
    $args = array(
    'post_status'     => array('post') );
    return get_posts($args);
}

function aw_get_site_url() {
    mysql_select_db(DB_NAME);
    return get_bloginfo('url');
}

function aw_count_posts($post = 'post') {
    mysql_select_db(DB_NAME);
    return wp_count_posts($post);
}

?>
