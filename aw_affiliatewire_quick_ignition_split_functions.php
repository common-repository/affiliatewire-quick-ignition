<?php
require_once 'aw_affiliatewire_quick_ignition_wp_functions.php';

function aw_affiliatewire_quick_ignition_split_test_new($MasterPost, $testName) {
    //create a new split test

    //Make shure post is not already part of a split test;
    $masterPostID = aw_get_metadata('post',$MasterPost,'aw_affiliatewire_quick_ignition_split_Master',true);
    if ($masterPostID != '') {
        return false;
    }
    //the only member is the Master Post
    $splitTestData[] = $MasterPost;
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Children', $splitTestData);
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Master', $MasterPost);
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_TestName', base64_encode($testName));
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Hits', 0);
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Links', array());
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Start', time());
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Total_Hits', 0);
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Running', 'false');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_IPs', array());

    return true;
}


function aw_affiliatewire_quick_ignition_split_test_add($MasterPost, $ChildPost) {
    //Add another post to a split test

    //Make shure post is not already part of a split test;
    $masterPostID = aw_get_metadata('post',$ChildPost,'aw_affiliatewire_quick_ignition_split_Master',true);
    if ($masterPostID != '') {
        return false;
    }

    //get the master values from the Master Post
    $splitTestData = aw_get_metadata('post',$MasterPost, 'aw_affiliatewire_quick_ignition_split_Children',true);
    $testName = aw_get_metadata('post',$MasterPost, 'aw_affiliatewire_quick_ignition_split_TestName',true);

    //Add the child split test
    $splitTestData[] = $ChildPost;
    $splitTestData = array_unique($splitTestData);
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Children', $splitTestData);

    //Set the starting values for the child post
    aw_update_metadata($ChildPost,'aw_affiliatewire_quick_ignition_split_Master', $MasterPost);
    aw_update_metadata($ChildPost,'aw_affiliatewire_quick_ignition_split_TestName', $testName);
    aw_update_metadata($ChildPost,'aw_affiliatewire_quick_ignition_split_Hits', '0');
    aw_update_metadata($ChildPost,'aw_affiliatewire_quick_ignition_split_Links', array());

    return true;
}

function aw_affiliatewire_quick_ignition_split_test_remove($MasterPost, $ChildPost) {
    //Remove a post from a split test
    //get the master values from the Master Post
    $splitTestData = aw_get_metadata('post',$MasterPost, 'aw_affiliatewire_quick_ignition_split_Children',true);

    //remove the Post from the list of children
    $splitTestData = array_diff($splitTestData, array($ChildPost));
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Children', $splitTestData);

    //unset the split test scores
    aw_update_metadata($ChildPost,'aw_affiliatewire_quick_ignition_split_Master', '');
    aw_update_metadata($ChildPost,'aw_affiliatewire_quick_ignition_split_TestName', '');
    aw_update_metadata($ChildPost,'aw_affiliatewire_quick_ignition_split_Hits', '');
    aw_update_metadata($ChildPost,'aw_affiliatewire_quick_ignition_split_Links', '');
    return true;
}

function aw_affiliatewire_quick_ignition_split_test_close($MasterPost, $ChosenPost) {
 
    //Close a split test by setting the closed value for the split test to a single post
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Closed', $ChosenPost);
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_End', time());
    return true;
}

function aw_affiliatewire_quick_ignition_split_test_delete($MasterPost) {
    $splitTestData = aw_get_metadata('post',$MasterPost, 'aw_affiliatewire_quick_ignition_split_Children',true);

    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Children', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Master', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_TestName', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Hits', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Links', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Start', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_End', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Closed', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Total_Hits', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Running', '');
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_IPs', '');

    foreach ($splitTestData as $splitTest) {
        aw_update_metadata($splitTest,'aw_affiliatewire_quick_ignition_split_Master', '');
        aw_update_metadata($splitTest,'aw_affiliatewire_quick_ignition_split_TestName', '');
        aw_update_metadata($splitTest,'aw_affiliatewire_quick_ignition_split_Hits', '');
        aw_update_metadata($splitTest,'aw_affiliatewire_quick_ignition_split_Links', '');
    }

    return true;

}

function aw_affiliatewire_quick_ignition_start_test(&$MasterPost) {
    $splitTestData = aw_get_metadata('post',$MasterPost, 'aw_affiliatewire_quick_ignition_split_Children',true);
    foreach ($splitTestData as $postID) {
        $post = aw_get_post($postID);
        
        $current_post = get_post( $postID, 'ARRAY_A' );
        if ($current_post->post_status != 'publish') {
            
            
            $current_post['post_status'] = 'publish';
            wp_update_post($current_post);
        }
    }
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Running', 'true');
}

function aw_affiliatewire_quick_ignition_publish_all(&$MasterPost) {
    $splitTestData = aw_get_metadata('post',$MasterPost, 'aw_affiliatewire_quick_ignition_split_Children',true);
    foreach ($splitTestData as $postID) {
        $post = aw_get_post($postID);
        
        $current_post = get_post( $postID, 'ARRAY_A' );
        if ($current_post->post_status != 'publish') {
            
            
            $current_post['post_status'] = 'publish';
            wp_update_post($current_post);
        }
    }
    aw_update_metadata($MasterPost,'aw_affiliatewire_quick_ignition_split_Running', 'true');
}

function aw_affiliatewire_quick_ignition_split_test_run(&$post) {
    if (isset($_REQUEST['preview']) && $_REQUEST['preview'] == "true") {
        if (isset($_REQUEST['p']) && $_REQUEST['p'] > 0) {
            return $post;
        }
    }
    //find out if post is part of a split test
    $postMaster = aw_get_metadata('post',$post->ID,'aw_affiliatewire_quick_ignition_split_Master',true);
    $splitTestData = aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_Children',true);
    if ($postMaster > 0) {
        if (count($splitTestData) > 0) {
            //make shure we are using the correct database
            mysql_select_db(DB_NAME);

            //get split test values

            $splitTestName = aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_TestName',true);

            //pick one of the split tests to display
            $choice = (int)aw_affiliatewire_quick_ignition_split_test_choose($splitTestData,$splitTestName);
            $choicePost = get_post($choice);
            //return the split test post
            return $choicePost;
        }
    }
        //return the orginal post
        return $post;
}

function aw_affiliatewire_quick_ignition_split_test_run_by_id(&$postID) {
    if (isset($_REQUEST['preview']) && $_REQUEST['preview'] == "true") {
        if (isset($_REQUEST['p']) && $_REQUEST['p'] > 0) {
            return $postID;
        }
    }
    //find out if post is part of a split test
    $postMaster = aw_get_metadata('post',$postID,'aw_affiliatewire_quick_ignition_split_Master',true);
    
    $running = aw_get_metadata('post',reset($splitTestMembers),'aw_affiliatewire_quick_ignition_split_Running',true);
    
    $splitTestData = aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_Children',true);
    if ($postMaster > 0) {
        if (count($splitTestData) > 0) {
            //make shure we are using the correct database
            mysql_select_db(DB_NAME);

            //get split test values

            $splitTestName = aw_get_metadata('post',$postMaster, 'aw_affiliatewire_quick_ignition_split_TestName',true);

            //pick one of the split tests to display
            $choice = (int)aw_affiliatewire_quick_ignition_split_Test_choose($splitTestData,$splitTestName);
            //return the split test post
            return $choice;
        }
    }
    
    //return the orginal post
    return $postID;
}

function aw_affiliatewire_quick_ignition_split_test_choose($splitTestMembers, $splitTestName) {
    if(!isset($_SESSION))
    {
        session_start();
    }
    
    $choice = -1;
    $splitMaster = aw_get_metadata('post',reset($splitTestMembers),'aw_affiliatewire_quick_ignition_split_Master',true);
    $splitClosed = aw_get_metadata('post',$splitMaster,'aw_affiliatewire_quick_ignition_split_Closed',true);
    $ipChoice = aw_get_metadata('post', $splitMaster,'aw_affiliatewire_quick_ignition_split_IPs', true);
    
    foreach ($splitTestMembers as $member) {
        if (get_post_status($member) != 'publish') {
            $splitTestMembers = array_diff($splitTestMembers,array($member));
        }
    }
    
    if ($splitClosed != '') {
        return $splitClosed;
    }

    $userIP = ip2long($_SERVER['REMOTE_ADDR']);
    $cookiename = preg_replace('/[^a-zA-Z0-9]/','_',(get_bloginfo('name').$splitTestName));
    
    if (isset($ipChoice[$userIP])) {
        $choice = $ipChoice[$userIP];
        //check if there is already a cookie set
    } elseif (isset($_COOKIE[$cookiename])) {
        $choice = (int)$_COOKIE[$cookiename];  //get the choice from the cookie
        //if no cookie, check if we already set the choice to the session
    } elseif (isset($_SESSION[$cookiename])) {
        $choice = (int)$_SESSION[$cookiename]; //get the choice from the session
    } 
    if ($choice == -1 OR !in_array($choice, $splitTestMembers)) { 
        $count = count($splitTestMembers);
        $choiceNumber = rand(0,$count-1); //randomly choose one of the posts
        $choice = $splitTestMembers[$choiceNumber];
        //update the count for that choice (number of 'unique' hits
        $splitTestHits = aw_get_metadata('post',$choice,'aw_affiliatewire_quick_ignition_split_Hits',true);
        $splitTestHits++;
        aw_update_metadata($choice,'aw_affiliatewire_quick_ignition_split_Hits',$splitTestHits);
        //update total hit counter
        $splitTestTotalHits = aw_get_metadata('post',$splitMaster,'aw_affiliatewire_quick_ignition_split_Total_Hits',true);
        $splitTestTotalHits++;
        aw_update_metadata($splitMaster,'aw_affiliatewire_quick_ignition_split_Total_Hits',$splitTestTotalHits);
    }
    
    $ipChoice[$userIP] = $choice;
    
    $_SESSION[$cookiename] = $choice; //store the choice as a cookie
    //store the choice as a cookie
    $expireDate = time() + (60 * 60 * 24 * 30); // 30 days
    $cookiePath = '/';
    $setcookie = setcookie($cookiename, $choice, $expireDate, $cookiePath);
    aw_update_metadata($splitMaster,'aw_affiliatewire_quick_ignition_split_IPs', $ipChoice);

    return $choice;

}

function aw_affiliatewire_quick_ignition_get_base_tracking_js() {
    $callbackUrl = aw_get_site_url();

    $toReturn = '
<script type="text/javascript" charset="utf-8">
// tracking of external URLs
// unobstrusive DHTML/JS
// Orginal:
// May 2005 Martin Spernau (martin AT traumwind DOT de)
// Updated:
// Dec 2011 for use in affiliate quick ignition plugin


// Add an eventListener to browsers that can do it somehow.
function addEvent(obj, evType, fn){
        if (obj.addEventListener){
                obj.addEventListener(evType, fn, true);
                return true;
        } else if (obj.attachEvent){
                var r = obj.attachEvent("on"+evType, fn);
                return r;
        } else {
                return false;
        }
}

function trackInit() {
	var links = document.getElementsByTagName("a");
	var externalRex = /^http[s]*:/i;
    var placeholder = "placeholder";
	for (var i = 0; i < links.length; i++) {
		var link = links[i];
        var classList = link.className;
        if (classList != "") {
            var blog = (classList.match(/post(\d+)/));
            if (blog != null) {
                if (blog.length >= 2) {
                    var postID = blog[1];
                    if (externalRex.exec(link.href)) {
                        eval(
                        "var fn = function () {"
                        + "track(this,\'"+postID+"\');"
                        + "return false;"
                        + "}"
                        );
                        addEvent(link,"click", fn );
                    }
                    placeholder = "placeholder";
                }
            }
        }
	}
}

function track(link, postID) {
	var url = link.href;
	var trImg = new Image();
	trImg.src = "'.$callbackUrl.'/?blog="+postID+"&track="+url;
}


addEvent(window,"load", trackInit);
</script>
';

    return $toReturn;
}

function aw_get_post_status($post_status) {
    switch ($post_status) {
        case "publish":
            $toReturn = "(Published)";
            break;
        case "draft":
            $toReturn = "(Draft)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            break;
        default:
            $toReturn = "(" . $post_status . ")";
    }
    return $toReturn;
}

?>