(function(){

    tinymce.create('tinymce.plugins.aw_affiliatewire_quick_ignition_buttons', {

        init : function(ed, url){
            //add a command to tell the editor to add text around selected text
            //then add a button to implement the command
            //repeated for each button used.
            ed.addCommand('mceawProductBox', function(){
                ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
                tinyMCE.activeEditor.selection.setContent('[awProductBox]' + ilc_sel_content + '[/awProductBox]');
            });
            ed.addButton('awproductBox', {
                title: 'Product Box',
                image: url + '/images/PB.png',
                cmd: 'mceawProductBox'
            });


			ed.addCommand('mceawLandingPage', function(){
                ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
                tinyMCE.activeEditor.selection.setContent('[awLandingPage]' + ilc_sel_content + '[/awLandingPage]');
            });
            ed.addButton('awlandingPage', {
                title: 'Landing Page',
                image: url + '/images/LP.png',
                cmd: 'mceawLandingPage'
            });


			ed.addCommand('mceawDownload', function(){
                ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
                tinyMCE.activeEditor.selection.setContent('[awDownload]' + ilc_sel_content + '[/awDownload]');
            });
            ed.addButton('awdownload', {
                title: 'Download Link',
                image: url + '/images/DL.png',
                cmd: 'mceawDownload'
            });


			ed.addCommand('mceawBuyNow', function(){
                ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
                tinyMCE.activeEditor.selection.setContent('[awBuyNow]' + ilc_sel_content + '[/awBuyNow]');
            });
            ed.addButton('awbuyNow', {
                title: 'Buy Now',
                image: url + '/images/BN.png',
                cmd: 'mceawBuyNow'
            });

			ed.addCommand('mceawDescription', function(){
                ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
                tinyMCE.activeEditor.selection.setContent('[awDescription]' + ilc_sel_content + '[/awDescription]');
            });
            ed.addButton('awdescription', {
                title: 'Description',
                image: url + '/images/PD.png',
                cmd: 'mceawDescription'
            });

			ed.addCommand('mceawDetails', function(){
                ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
                tinyMCE.activeEditor.selection.setContent('[awDetails]' + ilc_sel_content + '[/awDetails]');
            });
            ed.addButton('awdetials', {
                title: 'Details',
                image: url + '/images/PS.png',
                cmd: 'mceawDetails'
            });

            ed.addCommand('mceawProductName', function(){
                ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
                tinyMCE.activeEditor.selection.setContent('[awProductName]' + ilc_sel_content + '[/awProductName]');
            });
            ed.addButton('awproductName', {
                title: 'Product Name',
                image: url + '/images/PN.png',
                cmd: 'mceawProductName'
            });

            ed.addCommand('mceawProductPrice', function(){
                ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
                tinyMCE.activeEditor.selection.setContent('[awProductPrice]' + ilc_sel_content + '[/awProductPrice]');
            });
            ed.addButton('awproductPrice', {
                title: 'Product Price',
                image: url + '/images/PP.png',
                cmd: 'mceawProductPrice'
            });

        },
        createControl : function(n, cm){
            return null;
        },
        getInfo : function(){
            return {
                longname: 'AffiliateWire',
                author: 'AffiliateWire',
                authorurl: 'http://affiliate.revenuewire.com/',
                infourl: 'http://affiliate.revenuewire.com',
                version: "1.1"
            };
        }
    });
    tinymce.PluginManager.add('aw_affiliatewire_quick_ignition_buttons', tinymce.plugins.aw_affiliatewire_quick_ignition_buttons);
})();