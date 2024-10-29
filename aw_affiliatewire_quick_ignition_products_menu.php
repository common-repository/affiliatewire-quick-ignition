<?php
require_once 'aw_affiliatewire_quick_ignition_wp_functions.php';

ob_start();

if (isset($_POST['aw_affiliatewire_quick_ignition_create_post'])) {
    aw_createPost($_POST['aw_alias'],$_POST['aw_merchant'],$_POST['aw_productID']);
}
ob_flush();

$affiliateAliases = get_option('aw_affiliatewire_quick_ignition_affiliateAliases');
$aliasCount = count($affiliateAliases);
$productCount = number_format(get_option('aw_affiliatewire_quick_ignition_productsCount'),0);
$productUpdate = get_option('aw_affiliatewire_quick_ignition_productsUpdate');
echo "<div class='wrap'>";
echo '<h1>Product Catalog</h1>';

if ($aliasCount > 0) {
    $firstAlias = reset($affiliateAliases);
    $productCount = number_format(get_option('aw_affiliatewire_quick_ignition_productsCount'),0);
    $productsArray = maybe_unserialize(get_option('aw_affiliatewire_quick_ignition_products'));
    $products = $productsArray[$firstAlias];
} else {
    $productCount = 0;
}
if ($productCount == 0) {
    echo '<br/><br/><table class="tablesorter widefat" id="aw_products" >';
    echo '<tr><td>No Products</td></tr>';
    echo '</table>';
} else {

    ksort($products);

    echo '<br/><br/><table class="tablesorter widefat" id="aw_products" >';
        echo '<thead><tr>';
        echo '<th>Action</th>';
        echo '<th>Product Date</th>';
        echo '<th>Company</th>';
        echo '<th>Product Name</th>';
        echo '<th>Category</th>';
        echo '<th>Prices</th>';
        echo '<th>Language</th>';
        echo '<th>Commission</th>';
        echo '</tr></thead>';
        echo '<tbody>';
    foreach ($products as $merchantName => $merchant) {
        foreach ($merchant as $offerID => $offer) {
            $offerDetails = new SimpleXMLElement($offer);
            if (strtotime($offerDetails->dateAdded) > $productUpdate) {
                echo '<tr style="background: yellow;">';
            } else {
                echo '<tr>';
            }

            echo '<td width="10%" align="left" style="border-width: 1px;border-color: #D0DFE9;">';
            echo '<form name="aw_affiliatewire_quick_ignition_affiliateID_form" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
            echo '<label>Affiliate Alias:</label>'.aw_affiliatewire_quick_ignition_create_select('aw_alias',$affiliateAliases,$firstAlias);
            echo '<input type="hidden" value="'.$merchantName.'" name="aw_merchant">';
            echo '<input type="hidden" value="'.$offerID.'" name="aw_productID">';
            echo '<br/><input class="button-secondary" type="submit" value="Create Post" name="aw_affiliatewire_quick_ignition_create_post">';
            echo '</form>';
            echo '</td>';
            echo '<td width="8%" align="left" style="border-width: 1px;border-color: #D0DFE9;"> ';//.$offerDetails->dateAdded;
                echo '<br/>'.date('Y/m/d',strtotime($offerDetails->dateAdded));
                if (strtotime($offerDetails->dateAdded) > $productUpdate) {
                    echo '<br/>New Product';
                }
            echo '<td width="15%" align="left" style="border-width: 1px;border-color: #D0DFE9;"> ';//.$offerDetails->dateAdded;
                echo $merchantName;
            echo '</td>';
            echo '</td>';
            echo '<td width="30%" align="left" style="border-width: 1px;border-color: #D0DFE9;"> ';
            echo $offerDetails->name;
            echo '<br/> [<span style="font-style: oblique">'.$offerID.'</span>]';
            echo '<br/>';
            echo '<table class="widefat"><thead><th style="font-size: 10%">&nbsp;</th></thead><tbody>';
            echo '<tr><td>'.$offerDetails->details.'</td></tr></tbody></table></td>';

            echo '<td width="8%" align="left" style="border-width: 1px;border-color: #D0DFE9;"> '.$offerDetails->category.'</td>';
            echo '<td width="12%" align="right"  style="border-width: 1px;border-color: #D0DFE9;"><ul>';
            $prices = (array)$offerDetails->prices;
            foreach ($prices as $currency => $price) {
                echo "<li>".$price." ".$currency."</li>";
            }
            echo '</ul></td>';
            echo '<td width="8%" align="left" style="border-width: 1px;border-color: #D0DFE9;"> '.str_replace(',','<br/>',$offerDetails->languages).'</td>';
            echo '<td width="8%"  style="border-width: 1px;border-color: #D0DFE9;"> '.$offerDetails->commission.'</td></tr>';
        }
    }
    echo '</tbody></table>';
    echo '<script type="text/javascript">
            $(document).ready(function()
                {
                    $("#aw_products").tablesorter();
                }
            );
            </script>';

} 

echo '</div>';
$baseurl = plugin_dir_url( __FILE__ ).'images/';
echo '<style type="text/css">
table.tablesorter thead tr .header {
	background-image: url("'.$baseurl.'aw_sort_both.gif");
	background-repeat: no-repeat;
	background-position: center right;
	cursor: pointer;
}
table.tablesorter thead tr .headerSortUp {
	background-image: url("'.$baseurl.'aw_sort_asc.gif");
}
table.tablesorter thead tr .headerSortDown {
	background-image: url("'.$baseurl.'aw_sort_desc.gif");
}
</style>
';
function aw_createPost($affiliateAlias, $merchantID, $productID) {
    global $wpdb;

    $products = maybe_unserialize(get_option('aw_affiliatewire_quick_ignition_products'));
    $xmlString = $products[$affiliateAlias][$merchantID][$productID];

    $item = new SimpleXMLElement($xmlString);

    $postType = 'draft';
    $product_name = (string)$item->name;
    $product_description = (string)$item->customerDescription;
    $download_link = (string)$item->downloadUrl;
    $purchase_page = (string)$item->registrationUrl;
    $product_box = (string)$item->assets->boxShot->url;

    $postDetails = $post = array(
            'comment_status' => 'open', //[ 'closed' | 'open' ] // 'closed' means no comments.
            'ping_status' => 'open', //[ 'closed' |  ] // 'closed' means pingbacks or trackbacks turned off
            'post_status' => $postType, //'future', //[ 'draft' | 'publish' | 'pending'| 'future' | 'private' ] //Set the status of the new post.
            'post_type' => 'post', //[ 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type ] //You may want to insert a regular post, page, link, a menu item or some custom post type
            'post_title' => $product_name, //[ <the title> ] //The title of your post.
            'post_content' => $body, // [ <the text of the post> ] //The full text of the post.
    );

    $postID = wp_insert_post( $postDetails );
    if ($postID > 0) {
        update_post_meta($postID, 'aw_affiliatewire_quick_ignition_Product_Name_value', $product_name);
        update_post_meta($postID, 'aw_affiliatewire_quick_ignition_Product_Description_value', $product_description);
        update_post_meta($postID, 'aw_affiliatewire_quick_ignition_Direct_Download_value', $download_link);
        update_post_meta($postID, 'aw_affiliatewire_quick_ignition_Purchase_Page_value', $purchase_page);
        update_post_meta($postID, 'aw_affiliatewire_quick_ignition_Product_Box_value', $product_box);
        update_post_meta($postID, 'aw_affiliatewire_quick_ignition_Affiliate_Alias_value', $affiliateAlias);
        if (!headers_sent()) {
            $editurl = site_url().'/wp-admin/post.php?post='.$postID.'&action=edit';
            header("Location:".$editurl);
            die;
        }
        echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.$editurl.'">';
        echo 'In about 30 seconds you will be redirected to the NEW George Warner Home Page.
If redirection does not work please click on the following link:';
        echo '<a href="'.$editurl.'">'.$editurl.'</a>';
        exit;
    }
}
?>
