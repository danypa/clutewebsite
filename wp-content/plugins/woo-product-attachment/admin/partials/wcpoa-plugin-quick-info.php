<?php
$plugin_name = WCPOA_PLUGIN_NAME;
$plugin_version = WCPOA_PLUGIN_VERSION;
?>

<div class="wcpoa-table-main res-cl">
    <h2><?php esc_html_e('Introduction', 'woocommerce-product-attachment'); ?></h2>
    <table class="wcpoa-tableouter">
        <tbody>
            <tr>
                <td class="fr-1"><?php esc_html_e('Product Type', 'woocommerce-product-attachment'); ?></td>
                <td class="fr-2"><?php esc_html_e('WordPress Plugin', 'woocommerce-product-attachment'); ?></td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('Product Name', 'woocommerce-product-attachment'); ?></td>
                <td class="fr-2"><?php echo esc_html($plugin_name) ?></td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('Installed Version', 'woocommerce-product-attachment'); ?></td>
                <td class="fr-2"><?php echo esc_html($plugin_version); ?></td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('License & Terms of use', 'woocommerce-product-attachment'); ?></td>
                <td class="fr-2"><a href="<?php echo esc_url('https://www.thedotstore.com/terms-and-conditions/'); ?>"><?php esc_html_e('Click here', 'woocommerce-product-attachment'); ?></a> <?php esc_html_e('to view license and terms of use.', 'woocommerce-product-attachment'); ?></td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('Help & Support', 'woocommerce-product-attachment'); ?></td>
                <td class="fr-2">
                    <ul class="help-support">
                        <li><a target="_blank" href="<?php echo esc_url(site_url('wp-admin/admin.php?page=woocommerce_product_attachment&tab=wcpoa-plugin-getting-started')); ?>"><?php esc_html_e('Quick Start Guide', 'woocommerce-product-attachment'); ?></a></li>
                        <li><a target="_blank" href="<?php echo esc_url('http://www.thedotstore.com/docs/plugin/woocommerce-product-attachment/'); ?>"><?php esc_html_e('Documentation', 'woocommerce-product-attachment'); ?></a>
                        </li>
                        <li><a target="_blank" href="<?php echo esc_url('https://www.thedotstore.com/support/'); ?>"><?php esc_html_e('Support Forum', 'woocommerce-product-attachment'); ?></a></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('Localization', 'woocommerce-product-attachment'); ?></td>
                <td class="fr-2"><?php esc_html_e('English, Spanish', 'woocommerce-product-attachment'); ?></td>
            </tr>
        </tbody>
    </table>
</div>
