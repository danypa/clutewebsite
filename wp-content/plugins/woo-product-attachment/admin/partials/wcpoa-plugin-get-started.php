<?php

$plugin_url = esc_url( WCPOA_PLUGIN_URL );
$plugin_txt_domain = WCPOA_PLUGIN_TEXT_DOMAIN;
$setting_url = 'setting.png';
$single_prod_url = 'Getting_Started_02.png';
?>
<div class="wcpoa-table-main res-cl">
    <h2><?php 
esc_html_e( 'Thanks For Installing WooCommerce WooCommerce Product Attachment', $plugin_txt_domain );
?></h2>
    <table class="wcpoa-tableouter">
        <tbody>
            <tr>
                <td class="fr-2">
                    <p class="block gettingstarted"><strong><?php 
esc_html_e( 'Getting Started', $plugin_txt_domain );
?></strong></p>
                    <p class="block textgetting"><?php 
esc_html_e( 'Enhance your customer experience of product pages with downloadable files, such as technical descriptions, certificates, and licenses, user guides, and manuals, etc. A plugin will help you to attach/ upload any kind of files (doc, jpg, videos, pdf) for a customer orders.', $plugin_txt_domain );
?></p>
                    <p class="block textgetting">
                        <strong>Step 1: </strong><?php 
esc_html_e( 'Setting Page', $plugin_txt_domain );
?>
                        <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/setting.png' ;
?>"></span>
                    </p>
                    <p class="block textgetting">
                        <strong>Step 2: </strong><?php 
esc_html_e( 'Single Product Attachment ', $plugin_txt_domain );
?>
                        <span class="gettingstarted"><img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/Getting_Started_02.png' ;
?>"></span>
                    </p>
                    <?php 
?>
                </td>
            </tr>
        </tbody>
    </table>
</div>