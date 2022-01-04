<?php

namespace FcfVendor\WPDesk\PluginBuilder\Plugin;

interface Hookable
{
    /**
     * Init hooks (actions and filters).
     *
     * @return null
     */
    public function hooks();
}
