<?php

namespace FcfVendor\WPDesk\Plugin\Flow\Initialization\Simple;

use FcfVendor\WPDesk\Plugin\Flow\Initialization\InitializationStrategy;
use FcfVendor\WPDesk\PluginBuilder\BuildDirector\LegacyBuildDirector;
use FcfVendor\WPDesk\PluginBuilder\Builder\InfoBuilder;
use FcfVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
/**
 * Initialize free plugin
 * - just build it already
 */
class SimpleFreeStrategy implements \FcfVendor\WPDesk\Plugin\Flow\Initialization\InitializationStrategy
{
    use HelperInstanceAsFilter;
    use TrackerInstanceAsFilter;
    /** @var \WPDesk_Plugin_Info */
    protected $plugin_info;
    public function __construct(\FcfVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $this->plugin_info = $plugin_info;
    }
    /**
     * Initializes and builds plugin
     *
     * @return AbstractPlugin
     */
    public function run()
    {
        $this->prepare_helper_action();
        $this->prepare_tracker_action();
        $builder = new \FcfVendor\WPDesk\PluginBuilder\Builder\InfoBuilder($this->plugin_info);
        $build_director = new \FcfVendor\WPDesk\PluginBuilder\BuildDirector\LegacyBuildDirector($builder);
        $build_director->build_plugin();
        return $build_director->get_plugin();
    }
}
