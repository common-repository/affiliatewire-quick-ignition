<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function aw_affiliatewire_quick_ignition_run() {

    //if it is running, include functions
    require_once 'aw_affiliatewire_quick_ignition_functions.php';
    require_once 'aw_affiliatewire_quick_ignition_buttons.php';

    //add input boxes to post edit menu
    add_action('admin_menu', 'aw_affiliatewire_quick_ignition_create_meta_box');
    add_action('save_post', 'aw_affiliatewire_quick_ignition_save_postdata');

    //add buttons to editor
    add_action( 'edit_form_advanced', 'aw_affiliatewire_quick_ignition_quicktags');
    add_action( 'edit_page_form','aw_affiliatewire_quick_ignition_quicktags');

    //add buttons to wysiwyg editor
    add_action('init', 'AW_Buttons');
    function AW_Buttons(){
        require_once('aw_affiliatewire_quick_ignition_buttons.php');
        global $AW_Buttons;
        $AW_Buttons = new AW_Buttons();
    }

    add_action('loop_start','aw_affiliatewire_quick_ignition_split_testing_posts');
    add_action('wp', 'aw_affiliatewire_quick_ignition_split_testing_headers');
    add_action('init','aw_affiliatewire_quick_ignition_split_tracking');
    add_action('parse_request','aw_affiliatewire_quick_ignition_split_testing_headers');
}

//For the split testing, modifies the posts array to change what links are displayed
function aw_affiliatewire_quick_ignition_split_testing_posts($variables) {
    //don't run on admin pages
    if (is_admin()) {
        return $variables;
    }
    global $post;
    require_once 'aw_affiliatewire_quick_ignition_functions.php';
    require_once 'aw_affiliatewire_quick_ignition_split_functions.php';
    //if only one post is shown, check it and return it
    if ($variables->query_vars['p'] > 0) {
        $return_post = aw_affiliatewire_quick_ignition_split_test_run($post);
        $variables->posts[0] = $return_post;
        return $variables;
    }

    $args = $variables->query_vars;

    $postLimit = $args['posts_per_page'];
    if ($args['paged'] > 0) {
        $startItem = (($args['paged']-1)*$postLimit);
    } else {
        $startItem = 0;
    }

    $allPostsCount = aw_count_posts();
    $allPostsCounter = $allPostsCount->publish;

    $args['count'] = $allPostsCounter;
    unset($args['posts_per_page']);
    unset($args['paged']);
    $allPosts = aw_get_posts($allPostsCounter,$args);
    foreach ($allPosts as $key => $postItem) {
        //replace the current post with a split test post if there is one
        $return_post = aw_affiliatewire_quick_ignition_split_test_run($postItem);
        $allPosts[$key] = $return_post;
    }

    //make shure that a post is only displayed once
    $allPosts = array_unique($allPosts,SORT_REGULAR);
    //renumber the post array to remove blank spaces and make shure the
    //list starts at 0 (required)
    $allPosts = array_values($allPosts);

    $showPosts = array_slice($allPosts,$startItem,$postLimit);

    $variables->posts = $showPosts;
    $variables->found_posts = count($allPosts);
    $variables->max_num_pages = ceil($variables->found_posts/$variables->query_vars['posts_per_page']);
    $variables->post_count = count($showPosts);
    //add the tracking javascript once

    echo aw_affiliatewire_quick_ignition_get_base_tracking_js();

    return $variables;
}

function aw_affiliatewire_quick_ignition_split_testing_headers($variable) {
    //don't run on admin pages
    if (is_admin()) {
        return $variable;
    }
    require_once 'aw_affiliatewire_quick_ignition_functions.php';
    require_once 'aw_affiliatewire_quick_ignition_split_functions.php';
    global $wp_query;
    global $post;

    $allPostsCount = aw_count_posts();
    $allPostsCounter = $allPostsCount->publish;
    $allPosts = aw_get_posts($allPostsCounter);
    
    foreach ($allPosts as $key => $postItem) {
        //replace the current post with a split test post if there is one
        $return_post = aw_affiliatewire_quick_ignition_split_test_run($postItem);
        $allPosts[$key] = $return_post;
    }

    $postLimit = $wp_query->query_vars['posts_per_page'];
    if ($wp_query->query_vars['paged'] > 0) {
        $startItem = (($wp_query->query_vars['paged']-1)*$postLimit);
    } else {
        $startItem = 0;
    }

    //make shure that a post is only displayed once
    $allPosts = array_unique($allPosts,SORT_REGULAR);
    //renumber the post array to remove blank spaces and make shure the
    //list starts at 0 (required)
    $allPosts = array_values($allPosts);

    $showPosts = array_slice($allPosts,$startItem,$postLimit);

    //if only one post is shown, check it and return it
    if ($wp_query->query_vars['p'] > 0) {
        $post = aw_affiliatewire_quick_ignition_split_test_run($post);
    } elseif ($wp_query->query_vars['paged'] == '') {
        //if only first page is shown, check it and return it
        $post = aw_affiliatewire_quick_ignition_split_test_run($post);
    } else {
        $post = $allPosts[$startItem];
    }

    if (isset($wp_query->query_vars[p]) && $wp_query->query_vars[p] > 0) {
        $wp_query->query_vars[p] = $post->ID;
    }
    if (isset($wp_query->posts)) {
        $wp_query->posts = $showPosts;
    }
    if (isset($wp_query->post)) {
        $wp_query->post = $post;
    }

    return $variable;
}

function aw_affiliatewire_quick_ignition_split_tracking() {
    if (isset($_GET['blog']) && isset($_GET['track'])) {

        require_once 'aw_affiliatewire_quick_ignition_split_functions.php';
        $postMaster = aw_get_metadata('post',(int)$_GET['blog'],'aw_affiliatewire_quick_ignition_split_Master',true);
        $splitClosed = aw_get_metadata('post',$postMaster,'aw_affiliatewire_quick_ignition_split_Closed',true);

        if ($splitClosed != '') {
            die;
        }

        $splitTestLinks = aw_get_metadata('post',(int)$_GET['blog'],'aw_affiliatewire_quick_ignition_split_Links',true);
        $splitTestLinks[(string)$_GET['track']]++;
        aw_update_metadata((int)$_GET['blog'],'aw_affiliatewire_quick_ignition_split_Links', $splitTestLinks);
        die;
    }
}


?>