<?php

if (isset($_POST['aw_affiliatewire_quick_ignition_setting'])) {
    if ($_POST['aw_affiliatewire_quick_ignition_setting'] == 'enable') {
        $plugin_setting = 'enable';
    } else {
        $plugin_setting = 'disable';
    }
    update_option('aw_affiliatewire_quick_ignition_setting',$plugin_setting);
}

$affiliateAliases = get_option('aw_affiliatewire_quick_ignition_affiliateAliases');

if (isset($_POST['aw_affiliatewire_quick_ignition_affiliateAlias'])) {
    if (isset($_POST['save'])) {
        $affiliateAlias = (string)$_POST['aw_affiliatewire_quick_ignition_affiliateAlias'];
        $affiliateAlias = substr($affiliateAlias,0,10);
        $affiliateAliases[$affiliateAlias] = $affiliateAlias;
        update_option('aw_affiliatewire_quick_ignition_affiliateAliases',$affiliateAliases);
    }
    if (isset($_POST['remove'])) {
        $affiliateAlias = (string)$_POST['aw_affiliatewire_quick_ignition_affiliateAlias'];
        $affiliateAlias = substr($affiliateAlias,0,10);
        unset($affiliateAliases[$affiliateAlias]);
        update_option('aw_affiliatewire_quick_ignition_affiliateAliases',$affiliateAliases);
    }
    update_product_lists();
}

if (isset($_POST['aw_affiliatewire_quick_ignition_merchant'])) {
    update_option('aw_affiliatewire_quick_ignition_merchant',$_POST['aw_affiliatewire_quick_ignition_merchant']);
}

$affiliateAliases = get_option('aw_affiliatewire_quick_ignition_affiliateAliases');

if (isset($_POST['aw_affiliatewire_quick_ignition_updateproducts'])) {
    update_product_lists();
}


$merchants = maybe_unserialize(get_option('aw_affiliatewire_quick_ignition_merchants'));
$merchant = get_option('aw_affiliatewire_quick_ignition_merchant');
$plugin_setting = get_option('aw_affiliatewire_quick_ignition_setting');
$productCount = number_format(get_option('aw_affiliatewire_quick_ignition_productsCount'),0);
$productUpdate = get_option('aw_affiliatewire_quick_ignition_productsUpdate');

echo "<div class='wrap'>";
$url = plugins_url('/images/', __FILE__);
echo '<img src="'.$url.'affwire-logo.png">';
echo '<h1>AffiliateWire Quick Ignition</h1><br/>';
echo '<table  class="widefat"><tr>';
//echo '<th style="width: 150px;">Plugin Status: </th>';
//echo '<form name="aw_affiliatewire_quick_ignition_setting_form" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
//if ($plugin_setting == 'enable') {
//    echo '<td style="padding: 5px;width: 350px;"">Enabled</td>';
//    echo '<td style="width: 100px;">';
//    echo '<input type="hidden" name="aw_affiliatewire_quick_ignition_setting" value="disable">';
//    echo '<input class="button-primary" type="submit" value="Disable" name="disable">';
//} else {
//    echo '<td style="padding: 5px;;width: 350px;">Disabled</td>';
//    echo '<td >';
//    echo '<input type="hidden" name="aw_affiliatewire_quick_ignition_setting" value="enable">';
//    echo '<input class="button-primary" type="submit" value="Enable" name="enable">';
//}
//echo '</form>';
//echo '</td><td>&nbsp;</td></tr>';

$aliasCount = count($affiliateAliases);
if ($aliasCount == 0) {
    $rowCount = 2;
} else {
    $rowCount = $aliasCount +1;
}
echo '<tr><th valign="top" rowspan="'.($rowCount).'">Affiliate Alias</th>';
if ($aliasCount == 0) {
    echo '<td colspan="2"><span style="color:grey">None</span>';
    echo "<br><strong>Don't have an affiliate account yet? <a href='http://www.revenuewire.com/affiliate/signup/'>Become an Affiliate</a>.</strong>";
    echo '</td><td>&nbsp;</td></tr>';
} else {
    $awCounter = 0;
    foreach ($affiliateAliases as $affiliateAlias) {
        if ($awCounter++ > 0) {
            echo '<tr>';
        }
        echo '<form name="aw_affiliatewire_quick_ignition_affiliateID_form" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
        echo '<td>'.$affiliateAlias.'</td>';
        echo '<input type="hidden" value="'.$affiliateAlias.'" name="aw_affiliatewire_quick_ignition_affiliateAlias">';
        echo '<td><input class="button-secondary" type="submit" value="Remove Alias" name="remove"></td><td>&nbsp;</td></tr>';
        echo '</form>';
    }
}
echo '<tr>';
echo '<form name="aw_affiliatewire_quick_ignition_affiliateID_form" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
echo '<td><input type="text" name="aw_affiliatewire_quick_ignition_affiliateAlias" value=""></td>';
echo '<td><input class="button-primary" type="submit" value="Add Alias" name="save"></td>';
echo '</form>';
echo '<td>&nbsp;</td></tr>';

echo '<form name="aw_affiliatewire_quick_ignition_affiliateID_form" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
echo '<tr><th>Merchant</th>';
echo '<td>';
if (count($merchants) > 1) {
    echo '<select name="aw_affiliatewire_quick_ignition_merchant">';
    echo '<option value="all">All Merchants</option>';
    foreach ($merchants as $merchantID => $merchantName) {

        echo '<option value="'.$merchantID.'"';
        if ($merchantID == $merchant) {
            echo ' selected ';
        }
        echo'>'.$merchantName.'</option>';
    }
    echo '</select>';
}
echo '<input type="hidden" name="aw_affiliatewire_quick_ignition_updateproducts" value="true">';
echo '</td><td>&nbsp;</td><td>&nbsp;</td></tr>';

echo '<tr><th>Last Update</th>';
if ($productUpdate > 0) {
    echo '<td>'.date('F j, Y',$productUpdate).'</td>';
} else {
    echo '<td>'.$productUpdate.'</td>';
}
echo '<td>&nbsp</td><td>&nbsp;</td></tr>';

echo '<tr><th>Product Count</th>';
echo '<td>'.$productCount.'</td>';
echo '<td>';
echo '<input type="hidden" value="aw_affiliatewire_quick_ignition_updateproducts" name="aw_affiliatewire_quick_ignition_updateproducts">';
echo '<input class="button-primary" type="submit" value="Update" name="Update"></td><td>&nbsp;</td></tr>';
echo '</form>';
echo '</table>';

echo '</div>';

function update_product_lists() {
    update_option('aw_affiliatewire_quick_ignition_merchants',array());
    update_option('aw_affiliatewire_quick_ignition_products',array());
    update_option('aw_affiliatewire_quick_ignition_productsCount',0);

    $affiliateAliases = get_option('aw_affiliatewire_quick_ignition_affiliateAliases');
    //base url
    foreach ($affiliateAliases as $affiliateAlias) {
        $url = 'https://affiliate.revenuewire.com/products/xmlfeed/getFeed.php?affiliate='.$affiliateAlias;
        $offerXML = file_get_contents($url);
        try {
            //force all xml warnings into errors
            libxml_use_internal_errors(true);

            $catalog = new SimpleXMLElement(trim($offerXML));
            //get list of all possible merchants
            $merchants = array();
            foreach ($catalog as $merchantKey =>  $merchantXML) {
                $merchants[(string)$merchantXML->id] = (string)$merchantXML->name;
            }
            asort($merchants);
            update_option('aw_affiliatewire_quick_ignition_merchants',serialize($merchants));

            //check if a single merchant is selected
            $merchant = get_option('aw_affiliatewire_quick_ignition_merchant');
            if ($merchant != 'all') {
                $url .= '&merchant='.$merchant;
            }

            //make a list of all products (possibly restricted based on merchant)
            $offerXML = file_get_contents($url);
            $catalog = new SimpleXMLElement(trim($offerXML));
            $products[$affiliateAlias] = array();
            $productCount[$affiliateAlias] = 0;
            foreach ($catalog as $merchantKey =>  $merchantXML) {
                foreach ($merchantXML as $key => $offer) {
                    if ($key == 'product') {
                        $products[$affiliateAlias][(string)$merchantXML->name][(string)$offer->id] = $offer->asXML();;
                        $productCount[$affiliateAlias]++;
                    }
                }
            }
            //reset default xml warning state
            libxml_use_internal_errors(false);

            update_option('aw_affiliatewire_quick_ignition_products',serialize($products));
            update_option('aw_affiliatewire_quick_ignition_productsCount',array_shift($productCount));
            update_option('aw_affiliatewire_quick_ignition_productsUpdate',time());
        } catch (Exception $ex) {
            if ($ex->getMessage() == "String could not be parsed as XML") {
                echo '<h2>Error</h2><p>Invalid Affiliate Alias: '.$affiliateAlias.'</p>';
                echo '<form name="aw_affiliatewire_quick_ignition_affiliateID_form" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
                echo 'To Remove '.$affiliateAlias.'';
                echo ' <input type="hidden" value="'.$affiliateAlias.'" name="aw_affiliatewire_quick_ignition_affiliateAlias">';
                echo '<input class="button-secondary" type="submit" value="Click Here" name="remove">';
                echo '</form>';
            }
        }
    }
}
?>