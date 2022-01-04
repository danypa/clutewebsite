<?php

namespace FcfVendor\WPDesk\PluginBuilder\Plugin;

/**
 * It means that this class is should know about subscription activation
 *
 * @package WPDesk\PluginBuilder\Plugin
 */
interface ActivationAware
{
    /**
     * Set the activation flag to true
     *
     * @return void
     */
    public function set_active();
    /**
     * Is subscription active?
     *
     * @return bool
     */
    public function is_active();
}
