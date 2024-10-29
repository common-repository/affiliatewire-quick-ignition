<?php

require_once 'aw_affiliatewire_quick_ignition_wp_functions.php';

class awItems {
    //Class used to store constant values of tags

    const awLandingPage = 'awLandingPage';
    const awBuyNow = 'awBuyNow';
    const awDownload = 'awDownload';
    const awProductBox = 'awProductBox';
    const awDescription = 'awDescription';
    const awDetails = 'awDetails';
    const awProductName = 'awProductName';
    const awProductPrice = 'awProductPrice';

}

add_filter('the_content', 'aw_affiliatewire_quick_ignition_show_links');

function aw_affiliatewire_quick_ignition_show_links($content = '') {
    //filter post bodies before they are displayed
    //using values stored in the post metadata, replace any tags
    //with either text, links or images
    global $post;


    $landing_page = (aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Landing_Page_value', true));
    $download_link = (aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Direct_Download_value', true));
    $purchase_page = (aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Purchase_Page_value', true));
    $product_box = (aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Product_Box_value', true));
    $product_name = (aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Product_Name_value', true));
    $product_description = (aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Product_Description_value', true));
    $product_details = (aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Product_Details_value', true));
    $product_price = (aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Product_Price_value', true));


    $content = aw_affiliatewire_quick_ignition_replace_placeholder_codes_text(awItems::awDescription, '', $product_description, $content);

    $content = aw_affiliatewire_quick_ignition_replace_placeholder_codes_text(awItems::awProductName, '', $product_name, $content);

    $content = aw_affiliatewire_quick_ignition_replace_placeholder_codes_text(awItems::awProductPrice, '', $product_price, $content);

    $content = aw_affiliatewire_quick_ignition_replace_placeholder_codes_image(awItems::awProductBox, $product_name, $product_box, $content);

    $content = aw_affiliatewire_quick_ignition_replace_placeholder_codes_links(awItems::awLandingPage, 'More Information', $landing_page, $content);

    $content = aw_affiliatewire_quick_ignition_replace_placeholder_codes_links(awItems::awBuyNow, 'Buy Now', $purchase_page, $content);

    $content = aw_affiliatewire_quick_ignition_replace_placeholder_codes_links(awItems::awDownload, 'Download', $download_link, $content);

    $content = aw_affiliatewire_quick_ignition_add_class_to_links($content, 'post' . $post->ID, true);

    global $pagenow;
    if ($pagenow == 'index.php') {
        $content .= aw_affiliatewire_quick_ignition_getCSS();
    }

    return $content;
}

function aw_affiliatewire_quick_ignition_replace_placeholder_codes_links($idName, $message, $link, $text) {
    //replace text with link with text based on text found between the tags
    //if no text is found use the default message
    $pattern = '/[[]' . $idName . '([^]]*)[]](.*)[[][\/]' . $idName . '[]]/i';
    $count = preg_match_all($pattern, $text, $matches);
    for ($cnt = 0; $cnt < $count; $cnt++) {
        $matchedText = trim(str_replace('&nbsp;', ' ', htmlentities(utf8_decode(strip_tags($matches[2][$cnt])))));
        $matchedText2 = $matches[2][$cnt];
        if ($matchedText == '') {
            $matchedText = $message;
            $matchedText2 = $message;
        }
        $replace = '<a' . $matches[1][$cnt] . ' target="_blank" href="' .
                ($link) . '" title="' .
                ($matchedText) . '">' .
                ($matchedText2) . '</a>';

        $replace = aw_affiliatewire_quick_ignition_add_class_to_links($replace, $idName, false);
        $text = str_replace($matches[0][$cnt], $replace, $text);
    }
    return $text;
}

function aw_affiliatewire_quick_ignition_replace_placeholder_codes_image($idName, $message, $link, $text) {
    //replace text with link with text based on text found between the tags
    //if no text is found use the default message
    $pattern = '/[[]' . $idName . '([^]]*)[]](.*)[[][\/]' . $idName . '[]]/i';
    $count = preg_match_all($pattern, $text, $matches);
    for ($cnt = 0; $cnt < $count; $cnt++) {
        $matchedText = trim(str_replace('&nbsp;', ' ', htmlentities(utf8_decode(strip_tags($matches[2][$cnt])))));
        if ($matchedText == '') {
            $matchedText = $message;
        }
        $replace = '<img ' . $matches[1][$cnt] . ' src="' .
                ($link) . '" alt="' .
                ($matchedText) . '" />';
        $replace = aw_affiliatewire_quick_ignition_add_class_to_images($replace, $idName, false);
        $text = str_replace($matches[0][$cnt], $replace, $text);
    }
    return $text;
}

function aw_affiliatewire_quick_ignition_replace_placeholder_codes_text($idName, $message, $link, $text) {
    //replace text with link with text based on text found between the tags
    //if no text is found use the default message
    $pattern = '/[[]' . $idName . '([^]]*)[]](.*)[[][\/]' . $idName . '[]]/i';
    $count = preg_match_all($pattern, $text, $matches);
    for ($cnt = 0; $cnt < $count; $cnt++) {
        $matchedText = trim(str_replace('&nbsp;', ' ', htmlentities(utf8_decode(strip_tags($matches[2][$cnt])))));
        if ($matchedText == '') {
            $matchedText = $message;
        }
        $replace = '<span ' . $matches[1][$cnt] . ' title="' .
                ($matchedText) . '">' .
                ($link) . '</span>';
        $replace = aw_affiliatewire_quick_ignition_add_class_to_text($replace, $idName, false);
        $text = str_replace($matches[0][$cnt], $replace, $text);
    }
    return $text;
}

global $aw_affiliatewire_quick_ignition_blank_meta_boxes;
$aw_affiliatewire_quick_ignition_blank_meta_boxes = aw_affiliatewire_quick_ignition_create_meta_boxes();

function aw_affiliatewire_quick_ignition_create_meta_boxes() {
    //define basic metaboxes for storing data used by the plugin
    $offerDetails = aw_affiliatewire_quick_ignition_getOptionArray($post);
    $affiliateAliases = get_option('aw_affiliatewire_quick_ignition_affiliateAliases');

    $toReturn =
            array(
                "Affiliate_Alias" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Affiliate_Alias",
                    "std" => "",
                    "title" => "Affiliate Alias",
                    "type" => 'select',
                    "selectValues" => $affiliateAliases,
                    "description" => "Choose an aliases.",
                    "javascript" => aw_affiliatewire_quick_ignition_get_javascript(5),
                    "button" => ""),
                "Product_Name" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Product_Name",
                    "std" => "",
                    "title" => "<img src='" . plugins_url('images/PN.png', __FILE__) . "' style='margin: 5px;vertical-align: middle;'>Product Name",
                    "description" => "Select a product using the drop-down menu or enter the name manually.<br/>",
                    "chocies" => $offerDetails['products'],
                    "javascript" => aw_affiliatewire_quick_ignition_get_javascript(3),
                    "button" => ""),
                "Landing_Page" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Landing_Page",
                    "std" => "",
                    "title" => "<img src='" . plugins_url('images/LP.png', __FILE__) . "' style='margin: 5px;vertical-align: middle;'>Landing Page",
                    "description" => "Select a pre-made landing page using the drop-down menu or enter your own landing page URL.<br/>",
                    "chocies" => "<select name='aw_landingPages' onchange='aw_loadLandingPages();' ><option>No Items Selected</option></select>",
                    "javascript" => aw_affiliatewire_quick_ignition_get_javascript(1),
                    "button" => '<button name="testPage" value="Test Link" onClick="openLink(\'aw_affiliatewire_quick_ignition_Landing_Page_value\');return false;" style="float:;">Test Link</button>'),
                "Purchase_Page" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Purchase_Page",
                    "std" => "",
                    "title" => "<img src='" . plugins_url('images/BN.png', __FILE__) . "' style='margin: 5px;vertical-align: middle;'>Purchase Page",
                    "description" => "",
                    "button" => '<button name="testPage" value="Test Link" onClick="openLink(\'aw_affiliatewire_quick_ignition_Purchase_Page_value\');return false;" style="float:;">Test Link</button>'),
                "Direct_Download" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Direct_Download",
                    "std" => "",
                    "title" => "<img src='" . plugins_url('images/DL.png', __FILE__) . "' style='margin: 5px;vertical-align: middle;'>Direct Download Link",
                    "description" => "",
                    "button" => '<button name="testPage" value="Test Link" onClick="openLink(\'aw_affiliatewire_quick_ignition_Direct_Download_value\');return false;" style="float:;">Test Link</button>'),
                "Product_Price" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Product_Price",
                    "std" => "",
                    "title" => "<img src='" . plugins_url('images/PP.png', __FILE__) . "' style='margin: 5px;vertical-align: middle;'>Product Price",
                    "description" => "Select the product price or enter your own text.<br/>",
                    "chocies" => "<select name='aw_Product_Price' onchange='aw_loadProductPrices();' ><option>No Items Selected</option></select>",
                    "javascript" => aw_affiliatewire_quick_ignition_get_javascript(2),
                    "button" => ""),
                "Product_Box" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Product_Box",
                    "std" => "",
                    "title" => "<img src='" . plugins_url('images/PB.png', __FILE__) . "' style='margin: 5px;vertical-align: middle;'>Product Box<span style='float: ;padding-right: 15px;'><img id='awProductBoxImage' src='blank'/></span>",
                    "description" => "Use the default box shot shown or enter a custom image URL.",
                    "button" => ""),
                "Product_Description" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Product_Description",
                    "std" => "",
                    "title" => "<img src='" . plugins_url('images/PD.png', __FILE__) . "' style='margin: 5px;vertical-align: middle;'>Product Description",
                    "type" => 'textarea',
                    "description" => "",
                    "button" => ""),
                "Product_Details" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Product_Details",
                    "std" => "",
                    "title" => "Product Details",
                    "type" => 'textarea',
                    "description" => "Add a detailed description of the product here.",
                    "button" => ""),
                "Product_Images" => array(
                    "name" => "aw_affiliatewire_quick_ignition_Product_Images",
                    "std" => "",
                    "title" => "Product Images ",
                    "description" => "Choose from the available images and copy the url to insert into your post.<br/>",
                    "chocies" => "<select name='aw_productImages' class='qtipPopup' onchange='aw_loadProductImages();' ><option>No Items Selected</option></select>",
                    "javascript" => aw_affiliatewire_quick_ignition_get_javascript(4))
    );

    unset($toReturn['Product_Details']);
    return $toReturn;
}

function aw_affiliatewire_quick_ignition_new_meta_boxes() {
    //create the meta boxes when the post page is created

    global $post, $aw_affiliatewire_quick_ignition_blank_meta_boxes;
    //populate the select dropdowns with information based on
    //stored data with the post

    $offerDetails = aw_affiliatewire_quick_ignition_getOptionArray($post->ID);
    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Landing_Page']["choices"] .= $offerDetails['landing_pages'];
    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Landing_Page']["javascript"] = aw_affiliatewire_quick_ignition_get_javascript(1);

    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Product_Price']["choices"] = $offerDetails['prices'];
    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Product_Price']['javascript'] = aw_affiliatewire_quick_ignition_get_javascript(2);

    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Product_Name']['choices'] = $offerDetails['products'];

    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Product_Name']['javascript'] = aw_affiliatewire_quick_ignition_get_javascript(3);

    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Product_Images']["choices"] = $offerDetails['product_images'];
    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Product_Images']["javascript"] = aw_affiliatewire_quick_ignition_get_javascript(4);

    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Affiliate_Alias']['defaultValue'] = aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Affiliate_Alias_value', true);

    $meta_box_value = aw_get_metadata('post', $post->ID, 'aw_affiliatewire_quick_ignition_Product_Box' . '_value', true);

    $aw_affiliatewire_quick_ignition_blank_meta_boxes['Product_Box']['title'] = "<img src='" . plugins_url('images/PB.png', __FILE__) . "' style='margin: 5px;vertical-align: middle;'>Product Box<span style='float: right;padding-right: 15px;'><img id='awProductBoxImage' src='$meta_box_value'/></span>";

    //load the data into the meta boxes from the database
    //and create the html to display it
    foreach ($aw_affiliatewire_quick_ignition_blank_meta_boxes as $meta_box_name => $meta_box) {
        $meta_box_value = aw_get_metadata('post', $post->ID, $meta_box['name'] . '_value', true);

        if ($meta_box_value == "")
            $meta_box_value = $meta_box['std'];

        echo'<input type="hidden" name="' . $meta_box['name'] . '_noncename" id="' . $meta_box['name'] . '_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

        echo'<h2>' . $meta_box['title'] . '</h2>';

        echo'<p><label for="' . $meta_box['name'] . '_value">' . $meta_box['description'] . '</label>' . $meta_box['choices'] . '</p>';

        if (isset($meta_box['type']) && $meta_box['type'] == 'textarea') {
            echo '<textarea name="' . $meta_box['name'] . '_value" cols="70" rows="4">' . $meta_box_value . '</textarea><br />' . $meta_box['button'];
        } elseif (isset($meta_box['type']) && $meta_box['type'] == 'select') {
            echo str_replace('No Items', "No Alias's Entered", aw_affiliatewire_quick_ignition_create_select($meta_box['name'] . '_value', $meta_box['selectValues'], $meta_box['defaultValue'], ' onChange="aw_change_alias();"'));
        } else {
            echo'<input type="text" name="' . $meta_box['name'] . '_value" value="' . $meta_box_value . '" size="70" />' . $meta_box['button'] . '<br />';
        }
        echo $meta_box['javascript'];
    }
}

function aw_affiliatewire_quick_ignition_create_meta_box() {
    //add the metaboxes to the edit post page
    if (function_exists('add_meta_box')) {
        add_meta_box('new-meta-boxes', 'AffiliateWire Quick Ignition<br/>
    <table><tr>
    <td><img src="' . plugins_url('images/PN.png', __FILE__) . '" style="margin: 5px;vertical-align: middle;">Product Name</td>
    <td><img src="' . plugins_url('images/PD.png', __FILE__) . '" style="margin: 5px;vertical-align: middle;">Product Description</td>
    <td><img src="' . plugins_url('images/PB.png', __FILE__) . '" style="margin: 5px;vertical-align: middle;">Product Box Image</td>
    <td><img src="' . plugins_url('images/PP.png', __FILE__) . '" style="margin: 5px;vertical-align: middle;">Product Price</td>
</tr><tr>
    <td><img src="' . plugins_url('images/LP.png', __FILE__) . '" style="margin: 5px;vertical-align: middle;">Product Landing Page</td>
    <td><img src="' . plugins_url('images/BN.png', __FILE__) . '" style="margin: 5px;vertical-align: middle;">Product Buy Now</td>
    <td><img src="' . plugins_url('images/DL.png', __FILE__) . '" style="margin: 5px;vertical-align: middle;">Product Download Link</td>
</tr></table>


', 'aw_affiliatewire_quick_ignition_new_meta_boxes', 'post', 'normal', 'high');
    }
}

function aw_affiliatewire_quick_ignition_save_postdata($post_id) {
    //save data stored in meta boxes on the edit page
    global $post, $aw_affiliatewire_quick_ignition_blank_meta_boxes;
    foreach ($aw_affiliatewire_quick_ignition_blank_meta_boxes as $meta_box) {
        // Verify
        if (!wp_verify_nonce($_POST[$meta_box['name'] . '_noncename'], plugin_basename(__FILE__))) {
            return $post_id;
        }

        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        $data = $_POST[$meta_box['name'] . '_value'];

        if (aw_get_metadata('post', $post_id, $meta_box['name'] . '_value', true) == "")
            add_post_meta($post_id, $meta_box['name'] . '_value', $data, true);
        elseif ($data != aw_get_metadata('post', $post_id, $meta_box['name'] . '_value', true))
            update_post_meta($post_id, $meta_box['name'] . '_value', $data);
        elseif ($data == "")
            delete_post_meta($post_id, $meta_box['name'] . '_value', aw_get_metadata('post', $post_id, $meta_box['name'] . '_value', true));
    }
}

function aw_affiliatewire_quick_ignition_getCSS() {
    //create and return the CSS to make the buttons/text that is replaced in each post display
    //uniformly
    $text = '
<style type="text/css">
.' . awItems::awBuyNow . ', .' . awItems::awDownload . ', .' . awItems::awLandingPage . '  {
-moz-border-radius: 3px 3px 3px 3px;
background: url("https://www.safecart.com//images/icon-btn-arrow.png") no-repeat scroll 6px 5px #AEE268;
color: #00549B;
cursor: pointer;
display: inline-block;
font-size: 13px;
font-weight: bold;
padding: 10px 10px 10px 40px;
text-decoration: none;
width: auto;
margin: 10px;
}
.' . awItems::awProductName . ', .' . awItems::awDescription . ', .' . awItems::awDetails . ', .' . awItems::awProductBox . '  {
margin: 2px;
}
</style>';

    return $text;
}

function aw_affiliatewire_quick_ignition_getOptionArray($postID) {
    //set defualt values for select options incase there are no products, or no product is selected
    $toReturn['products'] = '<select name="aw_products"><option>No Valid Products</option></select>';
    $toReturn['landing_pages'] = '<select name="aw_landingPages" onchange="aw_loadLandingPages();" ><option>No Product Selected</option></select>';
    $toReturn['prices'] = '<select name="aw_Product_Price"onchange="aw_loadProductPrices();" ><option>No Product Selected</option></select>';
    $toReturn['product_images'] = '<select name="aw_productImages" class="qtipPopup" onchange="aw_loadProductImages();" ><option>No Product Selected</option></select>';

    //take the product lists and create the drop down select menu's used
    //on the editor page
    $productsList = maybe_unserialize(get_option('aw_affiliatewire_quick_ignition_products'));
    foreach ($productsList as $alias => $products) {
        $productCount = number_format(get_option('aw_affiliatewire_quick_ignition_productsCount'), 0);
        if ($productCount > 0) {

            $affiliateAliases = get_option('aw_affiliatewire_quick_ignition_affiliateAliases');
            $currentAlias = aw_get_metadata('post', $postID, 'aw_affiliatewire_quick_ignition_Affiliate_Alias_value', true);
            if (in_array($currentAlias, $affiliateAliases)) {
                $returnAlias = $currentAlias;
            } else {
                $returnAlias = array_shift($affiliateAliases);
            }

            ksort($products);
            if ($alias == $returnAlias) {
                $visable = 'inherit';
            } else {
                $visable = 'none';
            }
            $offers .= '<select name="aw_products" id="aw_offers_' . $alias . '" style="display: ' . $visable . ';" onchange="aw_loadProducts();" >';
            foreach ($products as $merchantName => $merchant) {

                $offers .= '<optgroup label="' . $merchantName . '">';

                foreach ($merchant as $offerID => $offer) {
                    $offerDetails = new SimpleXMLElement($offer);
                    $landingPages = array();
                    $productImages = array();
                    $assets = $offerDetails->assets;
                    foreach ($assets as $asset) {
                        foreach ($asset as $key => $item) {
                            //create and array of landing pages
                            if ($key == 'landingPage') {
                                $landingPages[] = aw_getCleanString((string) $item->url);
                            }
                            //create an array of graphics from the sales page
                            if ($key == 'salesGraphic') {
                                $productImages[] = aw_getCleanString((string) $item->url);
                            }
                        }
                    }
                    $prices = array();
                    $priceArray = (array) $offerDetails->prices;
                    //create an array of prices
                    foreach ($priceArray as $currency => $price) {
                        $prices[] = $price . " " . $currency;
                    }

                    $selected = '';
                    $product_name = aw_get_metadata('post', $postID, 'aw_affiliatewire_quick_ignition_Product_Name_value', true);
                    $product_name_compare = (string) $offerDetails->name;
                    //echo "<br/>$postID == $product_name == $product_name_compare";
                    if ($product_name == $product_name_compare) {
                        $selected = 'selected';
                        //create select options for landing pages, prices and product images
                        //for when a product is selected
                        if ($alias == $returnAlias) {
                            $toReturn['landing_pages'] = aw_affiliatewire_quick_ignition_create_select('aw_landingPages', $landingPages, '', ' onchange="aw_loadLandingPages();" ');

                            $toReturn['prices'] = aw_affiliatewire_quick_ignition_create_select('aw_Product_Price', $prices, '', ' onchange="aw_loadProductPrices();" ');

                            $toReturn['product_images'] = aw_affiliatewire_quick_ignition_create_select_images('aw_productImages', $productImages, '', ' onchange="aw_loadProductImages();" ');
                        }
                    }
                    //create an array containing all the subvalues for each product
                    $offerArray = array();
                    $offerArray[0] = aw_getCleanString((string) $offerDetails->name);
                    $offerArray[1] = aw_getCleanString((string) $offerDetails->registrationUrl);
                    $offerArray[2] = implode('|', $landingPages);
                    $offerArray[3] = aw_getCleanString((string) $offerDetails->downloadUrl);
                    $offerArray[4] = aw_getCleanString((string) $offerDetails->assets->boxShot->url);
                    $offerArray[5] = aw_getCleanString((string) $offerDetails->customerDescription);
                    $offerArray[6] = aw_getCleanString((string) $offerDetails->details);
                    $offerArray[7] = implode('|', $productImages);
                    $offerArray[8] = implode('|', $prices);
                    //convert the array into a single string to be inputed into select option value
                    $offers .= '<option ' . $selected . ' value="' . implode(';', $offerArray) . '">' . $offerDetails->name . '</option>';
                }
                $offers .= '</optgroup>';
            }
            $offers .= '</select>';
        }
        $toReturn['products'] = $offers;
    }

    return $toReturn;
}

function aw_affiliatewire_quick_ignition_create_select($selectName, $options, $selectedValue = '', $extras = '') {
    //create a standard select option pair based on a array using a given name
    $toReturn = '<select name="' . $selectName . '" id="' . $selectName . '" ' . $extras . ' >';
    if ($selectedValue == '') {
        $toReturn .= '<option>No Items Selected</option>';
    } elseif (count($options) == 0) {
        $toReturn .= '<option>No Items</option>';
    }
    foreach ($options as $option) {
        $toReturn .= '<option value="' . $option . '"';
        if ($option == $selectedValue) {
            $toReturn .= " selected ";
        } else {
            
        }
        $toReturn .= '>' . $option . '</option>';
    }
    $toReturn .= '</select>';
    return $toReturn;
}

function aw_affiliatewire_quick_ignition_create_select_images($selectName, $options, $selectedValue = '', $extras = '') {
    //create a select option pair for images
    //to add a class to enable the popup to display thumbnails
    $toReturn = '<select name="' . $selectName . '" id="' . $selectName . '" class="qtipPopup"  ' . $extras . '  ><option>No Image Selected</option>';
    foreach ($options as $option) {
        $strLoc = strrpos($option, "/") + 1;
        $thumb = substr($option, 0, $strLoc) . "t" . substr($option, $strLoc);
        $toReturn .= '<option value="' . $option . '">' . $option . '</option>';
    }
    $toReturn .= '</select>';
    return $toReturn;
}

function aw_getCleanString($string) {
    //replace double quotes with single quoutes in strings passed
    //to prevent errors
    $toReturn = str_replace('"', "'", $string);

    return $toReturn;
}

function aw_affiliatewire_quick_ignition_get_javascript($scriptID) {
    //function to retreive and return a javascript used to populate the textboxes for
    //the editor
    switch ($scriptID) {
        case 1:
            return '<script type="text/javascript">

    function aw_loadLandingPages() {
    var fred = document.getElementsByName("aw_landingPages")[0];
    var dead = fred.selectedIndex;
    var option = fred.options[dead].value;

    var aw_landing_page = document.getElementsByName("aw_affiliatewire_quick_ignition_Landing_Page_value")[0];

    aw_landing_page.value = option;

    }
    </script>';
            break;
        case 2:
            return '<script type="text/javascript">

    function aw_loadProductPrices() {
    var fred = document.getElementsByName("aw_Product_Price")[0];
    var dead = fred.selectedIndex;
    var option = fred.options[dead].value;

    var aw_product_price = document.getElementsByName("aw_affiliatewire_quick_ignition_Product_Price_value")[0];

    aw_product_price.value = option;

    }
    </script>';
            break;
        case 3:
            return '<script type="text/javascript">
    function aw_loadProducts() {
    var list = document.getElementsByName("aw_affiliatewire_quick_ignition_Affiliate_Alias_value")[0];
    var selected = list.selectedIndex;
    var item = list.options[selected].value;
    var item2 = "aw_offers_" + item;
    var fred = document.getElementById(item2);
//    var fred = document.getElementsByName("aw_products")[0];
    var dead = fred.selectedIndex;
    var option_array = fred.options[dead].value.split(";");


    var aw_product_name = document.getElementsByName("aw_affiliatewire_quick_ignition_Product_Name_value")[0];
    var aw_purchase_page = document.getElementsByName("aw_affiliatewire_quick_ignition_Purchase_Page_value")[0];
    var aw_landing_page = document.getElementsByName("aw_affiliatewire_quick_ignition_Landing_Page_value")[0];
    var aw_download = document.getElementsByName("aw_affiliatewire_quick_ignition_Direct_Download_value")[0];
    var aw_product_box = document.getElementsByName("aw_affiliatewire_quick_ignition_Product_Box_value")[0];
    var aw_product_description = document.getElementsByName("aw_affiliatewire_quick_ignition_Product_Description_value")[0];
    //var aw_product_details = document.getElementsByName("aw_affiliatewire_quick_ignition_Product_Details_value")[0];
    var aw_product_price = document.getElementsByName("aw_affiliatewire_quick_ignition_Product_Price_value")[0];
    var aw_product_image = document.getElementsByName("aw_affiliatewire_quick_ignition_Product_Images_value")[0];

    var aw_product_box_image = document.getElementById("awProductBoxImage");

    aw_product_name.value = option_array[0];
    aw_purchase_page.value = option_array[1];
    var aw_landing_pages = option_array[2].split("|");
    aw_landing_page.value = "";
    aw_download.value = option_array[3];
    aw_product_box.value = option_array[4];
    aw_product_box_image.src = option_array[4];
    aw_product_description.value = option_array[5];
    //aw_product_details.value = option_array[6];
    var aw_product_images = option_array[7].split("|");
    aw_product_image.value = "";
    var aw_product_prices = option_array[8].split("|");
    aw_product_price.value = "";


    var aw_landing_page_option = document.getElementsByName("aw_landingPages")[0];

    for ($cnt = aw_landing_page_option.length;$cnt > 0 ;$cnt--) {
    aw_landing_page_option.remove($cnt);
    }
    for (link in aw_landing_pages) {
    var elOptNew = document.createElement("option");
    elOptNew.text = aw_landing_pages[link];
    elOptNew.value = aw_landing_pages[link];
    try {
    aw_landing_page_option.add(elOptNew, null); // standards compliant; doesnt work in IE
    }
    catch(ex) {
    aw_landing_page_option.add(elOptNew); // IE only
    }
    }

    var aw_productImages_option = document.getElementsByName("aw_productImages")[0];
    for ($cnt = aw_productImages_option.length;$cnt > 0 ;$cnt--) {
    aw_productImages_option.remove($cnt);
    }
    for (link in aw_product_images) {
    var elOptNew = document.createElement("option");
    elOptNew.text = aw_product_images[link];
    elOptNew.value = aw_product_images[link];


    //elOptNew.style.backgroundImage = "url(\'"+linkText+"\')";
    //elOptNew.style.backgroundRepeat = "no-repeat";
    //elOptNew.style.backgroundPosition = "0px 20px";
    //elOptNew.style.height = "125px";

    //elOptNew.text = " ";
    try {
    aw_productImages_option.add(elOptNew, null); // standards compliant; doesnt work in IE
    }
    catch(ex) {
    aw_productImages_option.add(elOptNew); // IE only
    }
    }
    var aw_productPrice_option = document.getElementsByName("aw_Product_Price")[0];
    for ($cnt = aw_productPrice_option.length;$cnt > 0 ;$cnt--) {
    aw_productPrice_option.remove($cnt);
    }
    for (link in aw_product_prices) {
    var elOptNew = document.createElement("option");
    elOptNew.text = aw_product_prices[link];
    elOptNew.value = aw_product_prices[link];
    try {
    aw_productPrice_option.add(elOptNew, null); // standards compliant; doesnt work in IE
    }
    catch(ex) {
    aw_productPrice_option.add(elOptNew); // IE only
    }
    }
    return false;
    }
    </script>
    <script type="text/javascript">
    function openLink(itemName) {
    linkName = document.getElementsByName(itemName)[0];
    link = linkName.value;
    window.open(link); //,\'\',\'scrollbars=yes,menubar=yes,height=600,width=800,resizable=yes,toolbar=yes,location=yes,status=yes\');
    return false;
    }

    </script>';
            break;
        case 4:
            return '
    <link type="text/css" rel="stylesheet" href="' . plugins_url('/js/jquery.qtip.min.css', __FILE__) . '" />
    <script type="text/javascript" src="' . plugins_url('/js/jquery-1.7.min.js', __FILE__) . '" ></script>
    <script type="text/javascript" src="' . plugins_url('/js/jquery.qtip.min.js', __FILE__) . '" ></script>
    <script type="text/javascript">
    function aw_loadProductImages() {
    var fred = document.getElementsByName("aw_productImages")[0];
    var dead = fred.selectedIndex;
    var option = fred.options[dead].value;

    var awProduct_Images = document.getElementsByName("aw_affiliatewire_quick_ignition_Product_Images_value")[0];

    awProduct_Images.value = option;

    }
    $(\'.qtipPopup\').qtip({
        show: {
            event: "click mouseenter mouseover"
        },
        hide: {
            event: "mouseleave"
        },
       style: {
          classes: "ui-tooltip-blue ui-tooltip-shadow",
          width: "200px"
       },
       content: {
            text: "&nbsp;"
        },
   events: {
      hide: function(event, api) {
         setPopupContentHandler("stop");
      },
      show: function(event, api) {
          getPopupContent();
          setPopupContentHandler("run");
      }
}
    });
    
    var counter = 0;
    
function setPopupContentHandler(option)
{
  if ( option == "run" )
  {
    // Start the timer
    setPopupContent = setInterval ( "getPopupContent()", 1000 );
  }
  else
  {
    clearInterval ( setPopupContent );
    //alert("hide breaks");

  }
}

    function getPopupContent() {
        var awProduct_Images = document.getElementsByName("aw_affiliatewire_quick_ignition_Product_Images_value")[0].value;
        var fred = document.getElementsByName("aw_productImages")[0];
        var dead = fred.selectedIndex;
        var option = fred.options[dead].value;
        var breakpos = option.lastIndexOf("/");
        var image = "";
        if (breakpos>0) {
            counter++;
            linkText1 = option.substring(0,breakpos+1);
            linkText2 = option.substring(breakpos+1);
            linkText = linkText1 + "t" + linkText2;
            image = \'<img src="\'+linkText+\'" alt="\'+linkText+\'" style="width: 180px;max-width:400px;max-height:200px;"/>\';
        }
        var old_image = $(\'.qtipPopup\').qtip("option", "content.text");
        if (old_image != image) {
            $(\'.qtipPopup\').qtip("option", "content.text", image);
        }
    }
    </script>';
            break;
        case 5:
            return '
    <script type="text/javascript">
    function aw_change_alias() {

    var list = document.getElementsByName("aw_affiliatewire_quick_ignition_Affiliate_Alias_value")[0];
    var selected = list.selectedIndex;
    var length = list.length;
    var option = list.options[selected].value;

    for (var x = 0;x < length;x++) {
        var item = list.options[x].value;
        var item2 = "aw_offers_" + item;
        var select = document.getElementById(item2);
        if (item2 != "aw_offers_No Alias\'s Entered Selected") {
            if (item == option) {
                select.style.display = "inherit";
            } else {
                select.style.display = "none";
            }
        }
    }

    return false;
    }
    </script>';
            break;
    }
}

if (!function_exists('aw_print_debug')) {

    function aw_print_debug($cat) {
        return '<pre style="text-align:left;background:#ffffff">' . wordwrap(print_r($cat, true), 150, "\n") . '</pre>';
    }

}

if (!function_exists('get_site_url')) {

    /**
     * Retrieve the site url for a given site.
     *
     * Returns the 'site_url' option with the appropriate protocol,  'https' if
     * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
     * overridden.
     *
     * @package WordPress
     * @since 3.0.0
     *
     * @param int $blog_id (optional) Blog ID. Defaults to current blog.
     * @param string $path Optional. Path relative to the site url.
     * @param string $scheme Optional. Scheme to give the site url context. Currently 'http','https', 'login', 'login_post', or 'admin'.
     * @return string Site url link with optional path appended.
     */
    function get_site_url($blog_id = null, $path = '', $scheme = null) {
        // should the list of allowed schemes be maintained elsewhere?
        $orig_scheme = $scheme;
        if (!in_array($scheme, array('http', 'https'))) {
            if (( 'login_post' == $scheme || 'rpc' == $scheme ) && ( force_ssl_login() || force_ssl_admin() ))
                $scheme = 'https';
            elseif (( 'login' == $scheme ) && force_ssl_admin())
                $scheme = 'https';
            elseif (( 'admin' == $scheme ) && force_ssl_admin())
                $scheme = 'https';
            else
                $scheme = ( is_ssl() ? 'https' : 'http' );
        }

        if (empty($blog_id) || !is_multisite())
            $url = get_option('siteurl');
        else
            $url = get_blog_option($blog_id, 'siteurl');

        if ('http' != $scheme)
            $url = str_replace('http://', "{$scheme}://", $url);

        if (!empty($path) && is_string($path) && strpos($path, '..') === false)
            $url .= '/' . ltrim($path, '/');

        return apply_filters('site_url', $url, $path, $orig_scheme, $blog_id);
    }

}

function aw_affiliatewire_quick_ignition_add_class_to_links($string, $class_name = 'awpost', $alwaysAdd = true) {
    $toReturn = $string;
    $matchString = '/([<][a][^>]*[>])/i';

    $matchCount = preg_match_all($matchString, $toReturn, $matches);
    if (is_array($matches)) {
        foreach ($matches[0] as $match) {
            if (substr_count($match, 'class')) {
                if ($alwaysAdd == true) {
                    $pos = strpos($match, 'class') + 7;
                    $replacement = substr($match, 0, $pos) . $class_name . ' ' . substr($match, $pos);
                    $toReturn = str_replace($match, $replacement, $toReturn);
                }
            } else {
                $replacement = str_replace('<a ', '<a class="' . $class_name . '" ', $match);
                $toReturn = str_replace($match, $replacement, $toReturn);
            }
        }
    }
    return $toReturn;
}

function aw_affiliatewire_quick_ignition_add_class_to_images($string, $class_name = 'awpost', $alwaysAdd = true) {
    $toReturn = $string;

    $matchString = '/([<][i][m][g][^>]*[>])/i';

    $matchCount = preg_match_all($matchString, $toReturn, $matches);
    if (is_array($matches)) {
        foreach ($matches[0] as $match) {
            if (substr_count($match, 'class')) {
                if ($alwaysAdd == true) {
                    $pos = strpos($match, 'class') + 7;
                    $replacement = substr($match, 0, $pos) . $class_name . ' ' . substr($match, $pos);
                    $toReturn = str_replace($match, $replacement, $toReturn);
                }
            } else {
                $replacement = str_replace('<img ', '<img class="' . $class_name . '" ', $match);
                $toReturn = str_replace($match, $replacement, $toReturn);
            }
        }
    }
    return $toReturn;
}

function aw_affiliatewire_quick_ignition_add_class_to_text($string, $class_name = 'awpost', $alwaysAdd = true) {
    $toReturn = $string;

    $matchString = '/([<][s][p][a][n][^>]*[>])/i';

    $matchCount = preg_match_all($matchString, $toReturn, $matches);

    if (is_array($matches)) {
        foreach ($matches[0] as $match) {
            if (substr_count($match, 'class')) {
                if ($alwaysAdd == true) {
                    $pos = strpos($match, 'class') + 7;
                    $replacement = substr($match, 0, $pos) . $class_name . ' ' . substr($match, $pos);
                    $toReturn = str_replace($match, $replacement, $toReturn);
                }
            } else {
                $replacement = str_replace('<span', '<span class="' . $class_name . '" ', $match);
                $toReturn = str_replace($match, $replacement, $toReturn);
            }
        }
    }
    return $toReturn;
}

/*
 * truncate_string
 */

function aw_truncate_string($string, $limit) {

    if (strlen($string) <= $limit) {
        return $string;
    } else {

        return (substr($string, 0, $limit) . '...');
    }
}

?>