<?php

namespace ContentEgg\application\components;

/**
 * ParserModuleConfig abstract class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2019 keywordrush.com
 */
abstract class AffiliateFeedParserModuleConfig extends AffiliateParserModuleConfig {

    public function options()
    {        
        $options = parent::options();
        
        $options['update_mode']['dropdown_options'] = array('cron' => __('By schedule (WP cron)', 'content-egg'));
        $options['update_mode']['default'] = 'cron';
        $options['update_mode']['validator'][] = array(
            'call' => array($this, 'emptyLastImportDate'),
        );

        return $options;
    }

    public function emptyLastImportDate()
    {
        $this->getModuleInstance()->setLastImportDate(0);
        return true;
    }

}
