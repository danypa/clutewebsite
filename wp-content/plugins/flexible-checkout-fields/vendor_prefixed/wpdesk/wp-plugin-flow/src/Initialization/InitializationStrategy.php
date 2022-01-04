<?php

namespace FcfVendor\WPDesk\Plugin\Flow\Initialization;

use FcfVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
/**
 * Interface for initialization strategy for plugin. How to initialize it?
 */
interface InitializationStrategy
{
    /**
     * @return AbstractPlugin
     */
    public function run();
}
