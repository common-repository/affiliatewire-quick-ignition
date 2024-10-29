<?php

require_once 'aw_affiliatewire_quick_ignition_split_functions.php';

if (isset($_POST['split_test_name'])) {
    if ($_POST['split_test_name'] == '') {
        $feedback = 'Invalid Test Name';
    } else {
        $splitTestName = preg_replace('/[^a-z0-9\-\_\.\!]/i',' ',$_POST['split_test_name']);
        aw_affiliatewire_quick_ignition_split_test_new((int)$_POST['aw_base_page'], $splitTestName);
    }
}
    
if (isset($_POST['add_post'])) {
    $masterPost = (int)$_POST['master_post'];
    $childPost = (int)$_POST['child_post'];
    aw_affiliatewire_quick_ignition_split_test_add($masterPost, $childPost);
}
    
if (isset($_POST['publish_all'])) {
    $masterPost = (int)$_POST['master_post'];
    aw_affiliatewire_quick_ignition_publish_all($masterPost);
}

if (isset($_POST['remove_post'])) {
    if (isset($_POST['confirm'])) {
        if ($_POST['confirm']=='Yes') {
            $masterPost = (int)$_POST['master_post'];
            $childPost = (int)$_POST['child_post'];
            aw_affiliatewire_quick_ignition_split_test_remove($masterPost, $childPost);
        }
    } else {
        $requiresConfirm = true;
        $action = 'remove_post';
        $masterPost = (int)$_POST['master_post'];
        $childPost = (int)$_POST['child_post'];
        $confirmMessage = 'Remove Post?  Doing so will remove this post from the split test.';
    }
}

if (isset($_POST['close_split'])) {
    if (isset($_POST['confirm'])) {
        if ($_POST['confirm']=='Yes') {
            $masterPost = (int)$_POST['master_post'];
            $childPost = (int)$_POST['child_post'];
            aw_affiliatewire_quick_ignition_split_test_close($masterPost, $childPost);
        }
    } else {
        $requiresConfirm = true;
        $action = 'close_split';
        $masterPost = (int)$_POST['master_post'];
        $childPost = (int)$_POST['child_post'];
        $confirmMessage = 'End Split Test? By ending the test, only your preferred post will be shown in the future.';
    }
}

if (isset($_POST['delete_split'])) {
    if (isset($_POST['confirm'])) {
        if ($_POST['confirm']=='Yes') {
            $masterPost = (int)$_POST['master_post'];
            aw_affiliatewire_quick_ignition_split_test_delete($masterPost);
        }
    } else {
        $requiresConfirm = true;
        $action = 'delete_split';
        $masterPost = (int)$_POST['master_post'];
        $confirmMessage = 'Confirm deletion of test.';
    }
}

echo '<div class="wrap">';
echo '<h1>Split Testing</h1>';

if ($requiresConfirm == true) {
    echo '<br/>';
    echo '<form name="aw_affiliatewire_quick_ignition_split_testing_close_confirm" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
    echo "<h2>Confirmation Required</h2>";
    echo "$confirmMessage<br/>";
    echo '<input type="hidden" name="master_post" value="'.$masterPost.'">';
    echo '<input type="hidden" name="child_post" value="'.$childPost.'">';
    echo '<input type="hidden" name="'.$action.'" value="'.$action.'">';
    echo '<input class="button-secondary" type="submit" name="confirm" value="Yes">';
    echo '<input class="button-secondary" type="submit" name="confirm" value="No">';
    echo '</form><br/>';
}
if (isset($feedback)) {
    echo '<br/>';
    echo '<h3>'.$feedback.'</h3>';
}

$posts = aw_get_all_posts(-1);

echo '<button class="button-primary" name="createnew" value="Create New Split Test" onclick="$(\'#create_new_split_test\').toggle();" >';

echo 'Create New Split Test</button><br/>';

echo '<br/>';
echo '<div id="create_new_split_test" style="display: none;" >';
echo '<table class="widefat" style="width: 60%;">';
echo '<form name="aw_affiliatewire_quick_ignition_split_testing_add_new" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
echo '<tr><th colspan="2">Create New Split Test</th></tr>';
echo '<tr><td style="width: 85px;">Test Name</td>';
echo '<td><input name="split_test_name" type="text" size="40"></td></tr>';
echo '<tr><td>Master Post</td>';
echo '<td><select name="aw_base_page">';

foreach ($posts as $post) {
    $masterPostID = aw_get_metadata('post',$post->ID,'aw_affiliatewire_quick_ignition_split_Master',true);
    if ($masterPostID == '') {
        $show = false;
        $post_status = str_pad($post->ID,5," ",STR_PAD_LEFT);
        $post_status = str_replace(' ', '&nbsp;', $post_status)."&nbsp;&nbsp;&nbsp;";
        switch ($post->post_status) {
            case "publish":
                $post_status .= "(Published)";
                $show = true;
                break;
            case "draft":
                $post_status .= "(Draft)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                $show = true;
                break;
            default:
                $post_status = "(".$post->post_status.")";    
                break;
        }
        if ($show == true) {
            echo '<option value="'.$post->ID.'">'.$post_status." ".aw_truncate_string($post->post_title,50).'</option>';
        }
    }
}

echo '</select></td></tr>';
echo '<tr><td>&nbsp;</td><td><input  class="button-primary"  type="submit" name="create_split_test" value="Create Split Test"></td></tr>';
echo '</form></table><br/>';
echo '</div>';



foreach ($posts as $post) {
    $postMaster = aw_get_metadata('post',$post->ID,'aw_affiliatewire_quick_ignition_split_Master',true);
    if ($postMaster == $post->ID) {
        $splitTestData = aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_Children',true);
        $allpublished = true;
        foreach ($splitTestData as $splitItem) {
            if (get_post_status($splitItem) != 'publish') {
                $allpublished = false;
            }
        }
        $splitTestData = array_diff($splitTestData, array($postMaster));
        $splitTestName = aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_TestName',true);
        $splitTestHits = (int)aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_Hits',true);
        $splitStart = (int)aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_Start',true);
        $splitEnd = (int)aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_End',true);
        $splitClosed = (int)aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_Closed',true);
        $splitTestTotalHits = (int)aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_Total_Hits',true);
        $running = $splitMaster = aw_get_metadata('post',$postMaster,'aw_affiliatewire_quick_ignition_split_Running',true);
            

        
        
        echo '<table class="widefat"><thead>';
        echo '<tr><td colspan="7"><span style="font-size: 150%;">'.base64_decode($splitTestName).'</span>';
//        if ($running == 'true') {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Start Date: '.date('M d, Y', $splitStart);
            if ($splitEnd > 0) {
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;End Date:'.date('m d, Y', $splitEnd);
            }
//        } else {
//            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Start Date: Not Running';
//            if ($splitEnd == 0) {
//                echo '&nbsp;&nbsp;&nbsp;<form style="display: inline" name="aw_affiliatewire_quick_ignition_split_testing_remove_post" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
//                echo '<input type="hidden" name="master_post" value="'.$post->ID.'">';
//                echo '<input class="button-secondary" type="submit" name="start_test" value="Start Test">';
//                echo '</form>';
//            }
//        }
        echo '<br/>';
        if ($allpublished != true) {
            echo '<form style="display: inline" name="aw_affiliatewire_quick_ignition_split_testing_remove_post" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
            echo '<input type="hidden" name="master_post" value="'.$post->ID.'">';
            echo '<input class="button-primary" type="submit" name="publish_all" value="Publish All">';
            echo '</form>';
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        
        echo '<form style="display: inline"  name="aw_affiliatewire_quick_ignition_split_testing_delete_split" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
        echo '<input type="hidden" name="master_post" value="'.$post->ID.'">';
        echo '<input class="button-secondary" type="submit" name="delete_split" value="Delete Test">';
        echo '</form>';
        
        echo '</td></tr>';
        
        echo '<tr>';
        echo '<th width="17%" colspan="2">Post Type</th>';
        echo '<th width="53%"  >Post Title</th>';
        echo '<th width="10%" >Status</th>';
        echo '<th width="10%"  ># of Hits</th>';
        echo '<th width="10%"  >Actions</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        echo '<tr>';
        echo '<th colspan="1">Master Post';
        echo '</th>';
        echo '<td>';
        echo '<a style="float: right" href="'.get_site_url().'/?p='.$post->ID.'&preview=true" target="_blank">';
        echo '<buttom class="button-secondary" style="display: block;">Preview</buttom></a>';
        echo '</td>';
        echo '<td><pre style="display: inline">['.str_pad($post->ID,3," ",STR_PAD_LEFT)."]  </pre>".$post->post_title;
        echo '</td>';
        echo '<td>'.aw_get_post_status($post->post_status);
        echo '</td>';
        echo '<td>'.$splitTestHits.' ('.@number_format(($splitTestHits/$splitTestTotalHits*100),0).'%) </td>';
        echo '<td>';
        echo '</td>';
        echo '</tr>';
        
        $splitTestLinks = aw_get_metadata('post',$post->ID,'aw_affiliatewire_quick_ignition_split_Links',true);
        if (is_array($splitTestLinks) && count($splitTestLinks)>0) {
                echo '<tr><th style="text-align: right" colspan="2" rowspan="'.count($splitTestLinks).'" align="right" >Outbound Links</th>';
                foreach ($splitTestLinks as $link => $linkCount) {
                    echo '<td colspan="2">'.$link.'</span></td>';
                    echo '<td>'.$linkCount.'</td>';
                    echo '<td>&nbsp;</td>';
                    echo '</tr><tr>';
                }
        }
        
        foreach ($splitTestData as $splitID) {
            $splitPost = get_post($splitID);
            $splitTestHits = aw_get_metadata('post',$splitPost->ID, 'aw_affiliatewire_quick_ignition_split_Hits',true);
            echo '<th colspan="1">Child Post';
            if ($splitID == $splitClosed) {
            echo ' <span style="color: green;float: right;">Chosen';
           }
           echo '</th>';
        echo '<td>';
        echo '<a style="float: right" href="'.get_site_url().'/?p='.$splitPost->ID.'&preview=true" target="_blank">';
        echo '<buttom class="button-secondary" style="display: block;">Preview</buttom></a>';
        echo '</td>';
            echo '<td><pre style="display: inline">['.str_pad($splitPost->ID,3," ",STR_PAD_LEFT)."]  </pre>".$splitPost->post_title;
            echo '</td>';
            echo '<td>'.aw_get_post_status($splitPost->post_status);
            echo '</td>';
            echo '<td>'.$splitTestHits.' ('.@number_format(($splitTestHits/$splitTestTotalHits*100),0).'%) </td>';
            echo '<td>';
            if (!$splitClosed) {
                echo '<form name="aw_affiliatewire_quick_ignition_split_testing_remove_post" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
                echo '<input type="hidden" name="master_post" value="'.$post->ID.'">';
                echo '<input type="hidden"  name="child_post" value="'.$splitID.'">';
                echo '<input class="button-secondary" type="submit" name="remove_post" value="Remove Post">';
                echo '</form>';
            }
            echo '</td>';
            echo '</tr>';
            $splitTestLinks = aw_get_metadata('post',$splitPost->ID,'aw_affiliatewire_quick_ignition_split_Links',true);
            if (is_array($splitTestLinks) && count($splitTestLinks)>0) {
                echo '<tr><th style="text-align: right" colspan="2" rowspan="'.count($splitTestLinks).'" align="right" >Outbound Links</th>';
                foreach ($splitTestLinks as $link => $linkCount) {
                    echo '<td colspan="2">'.$link.'</span></td>';
                    echo '<td>'.$linkCount.'</td>';
                    echo '<td>&nbsp;</td>';
                    echo '</tr><tr>';
                }
            }
        }
        if (!$splitClosed) {
            echo '<form name="aw_affiliatewire_quick_ignition_split_testing_add_post" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
            echo '<tr><th style="text-align: right" colspan="2">Add Post</th>';
            echo '<td colspan="3"><select name="child_post">';

            foreach ($posts as $postAdd) {
                $masterPostID = aw_get_metadata('post',$postAdd->ID,'aw_affiliatewire_quick_ignition_split_Master',true);
                if ($masterPostID == '') {
                    $show = false;
                    $post_status = str_pad($postAdd->ID,5," ",STR_PAD_LEFT);
                    $post_status = str_replace(' ', '&nbsp;', $post_status)."&nbsp;&nbsp;&nbsp;";
                    switch ($postAdd->post_status) {
                        case "publish":
                            $post_status .= "(Published)";
                            $show = true;
                            break;
                        case "draft":
                            $post_status .= "(Draft)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                            $show = true;
                            break;
                        default:
                            $post_status = "(".$postAdd->post_status.")";    
                    }
                    if ($show == true) {
                        echo '<option value="'.$postAdd->ID.'">'.$post_status." ".aw_truncate_string($postAdd->post_title,100).'</option>';
                    }
                }
            }
            echo '</td><td>';
            echo '</select>';
            echo '<input type="hidden" name="master_post" value="'.$post->ID.'">';
            echo '<input class="button-secondary" type="submit" name="add_post" value="Add Post" style="float: none"></td></tr>';
            echo '</form>';
        }
        if (!$splitClosed) {
            echo '<tr><td colspan="7" align="right"><b>End Split Test</b>&nbsp;&nbsp;&nbsp;';
            echo '<form style="display: inline" name="aw_affiliatewire_quick_ignition_split_testing_close_split" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
            echo '<select name="child_post">';
            echo '<option value="'.$post->ID.'">'.$post->post_title.'</option>';
            foreach ($splitTestData as $splitID) {
                $splitPost = get_post($splitID);
                echo '<option value="'.$splitPost->ID.'">'.$splitPost->post_title.'</option>';
            }
            echo '</select>';
            echo '<input type="hidden" name="master_post" value="'.$post->ID.'">';
            echo '<input class="button-primary" type="submit" name="close_split" value="Choose Preferred Post">';
            echo '</form>';
            echo '</td></tr>';
        } else {
            echo '<tr><td colspan="6" align="right">Split Test Ended</td></tr>';
        }   
        echo '</tbody></table>';
        echo '<br/>';
    }
}
?>