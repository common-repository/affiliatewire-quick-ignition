<?php

class AW_Buttons {

    //check if user is admin, and if so, can they edit posts
    //if so, add the buttons to wysiwyg editor
	function AW_Buttons(){
		if(is_admin()){
			if ( current_user_can('edit_posts') && current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
			{
				add_filter('tiny_mce_version', array(&$this, 'tiny_mce_version') );
				add_filter("mce_external_plugins", array(&$this, "mce_external_plugins"));
				add_filter('mce_buttons_2', array(&$this, 'mce_buttons'));
			}
		}
	}
    //declare order buttons will apear
	function mce_buttons($buttons) {
		array_push($buttons, "separator", "awproductName", "awdescription", "awproductBox", "awproductPrice", "separator", "awlandingPage", "awbuyNow", "awdownload" );
        return $buttons;
	}
    //declair source file for javascript describing buttons
	function mce_external_plugins($plugin_array) {
		$plugin_array['aw_affiliatewire_quick_ignition_buttons']  =  plugins_url('/aw_affiliatewire_quick_ignition_buttons.js', __FILE__);
        return $plugin_array;
	}
	function tiny_mce_version($version) {
		return ++$version;
	}
}


function aw_affiliatewire_quick_ignition_quicktags() {
    //create buttons to be used on HTML plain editor to match ones on wysiwyg editor
    ?>
<script type="text/javascript" charset="utf-8">
    edbuttonlength = edButtons.length;
    edbuttonlength_t = edbuttonlength;
    edButtons[edbuttonlength++] = new edButton('ed_aw_Product_Name','Product Name','[awProductName]','[/awProductName]');
    edButtons[edbuttonlength++] = new edButton('ed_aw_Product_Description','Product Description','[awDescription]','[/awDescription]');
    edButtons[edbuttonlength++] = new edButton('ed_aw_Product_Box','Product Box','[awProductBox]','[/awProductBox]');
    edButtons[edbuttonlength++] = new edButton('ed_aw_Product_Price','Product Price','[awProductPrice]','[/awProductPrice]');
    edButtons[edbuttonlength++] = new edButton('ed_aw_Landing_Page','Landing Page','[awLandingPage]','[/awLandingPage]');
    edButtons[edbuttonlength++] = new edButton('ed_aw_Download','Download Link','[awDownload]','[/awDownload]');
    edButtons[edbuttonlength++] = new edButton('ed_aw_Buy_Now','Buy Now','[awBuyNow]','[/awBuyNow]');
    (function(){

        if (typeof jQuery === 'undefined') {
            return;
        }
        jQuery(document).ready(function(){
            jQuery("#ed_toolbar").append('<br/>');
            jQuery("#ed_toolbar").append('<input type="button" value="Product Name" id="ed_aw_Product_Name" class="ed_button" onclick="edInsertTag(edCanvas, edbuttonlength_t);" title="Product Name Image" />');
            jQuery("#ed_toolbar").append('<input type="button" value="Product Descripion" id="ed_aw_Product_Description" class="ed_button" onclick="edInsertTag(edCanvas, edbuttonlength_t+1);" title="Product Description" />');
            jQuery("#ed_toolbar").append('<input type="button" value="Product Box" id="ed_aw_Product_Box" class="ed_button" onclick="edInsertTag(edCanvas, edbuttonlength_t+2);" title="Product Box" />');
            jQuery("#ed_toolbar").append('<input type="button" value="Product Price" id="ed_aw_Product_Price" class="ed_button" onclick="edInsertTag(edCanvas, edbuttonlength_t+3);" title="Product Price" />');
            jQuery("#ed_toolbar").append('<input type="button" value="Landing Page" id="ed_aw_Landing_Page" class="ed_button" onclick="edInsertTag(edCanvas, edbuttonlength_t+4);" title="Landing Page" />');
            jQuery("#ed_toolbar").append('<input type="button" value="Download Link" id="ed_awDownload" class="ed_button" onclick="edInsertTag(edCanvas, edbuttonlength_t+5);" title="Download Link" />');
            jQuery("#ed_toolbar").append('<input type="button" value="Buy Now" id="ed_aw_Buy_Now" class="ed_button" onclick="edInsertTag(edCanvas, edbuttonlength_t+6);" title="Buy Now" />');
        });
    }());
    // ]]>
</script>
<?php

}


function aw_affiliatewire_quick_ignition_print_scripts() {
    global $pagenow;
    if (is_admin() && ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) ) {
        $js = plugins_url('/quicktag-extender.js', __FILE__ );
        wp_enqueue_script("qtescript", $js, array('quicktags') );
    }
}

?>