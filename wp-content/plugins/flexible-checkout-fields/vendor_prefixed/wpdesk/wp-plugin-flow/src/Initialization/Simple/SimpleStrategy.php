<?php

namespace FcfVendor\WPDesk\Plugin\Flow\Initialization\Simple;

use FcfVendor\WPDesk\Helper\HelperRemover;
use FcfVendor\WPDesk\Helper\PrefixedHelperAsLibrary;
use FcfVendor\WPDesk\License\PluginRegistrator;
use FcfVendor\WPDesk\Plugin\Flow\Initialization\PluginDisablerByFile;
use FcfVendor\WPDesk\Plugin\Flow\Initialization\InitializationStrategy;
use FcfVendor\WPDesk\PluginBuilder\BuildDirector\LegacyBuildDirector;
use FcfVendor\WPDesk\PluginBuilder\Builder\InfoActivationBuilder;
use FcfVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
/**
 * Initialize standard paid plugin
 * - register to helper
 * - initialize helper
 * - build with info about plugin active flag
 */
class SimpleStrategy implements \FcfVendor\WPDesk\Plugin\Flow\Initialization\InitializationStrategy
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
        $this->prepare_tracker_action();
        $registrator = $this->register_plugin();
        $this->init_helper();
        $is_plugin_subscription_active = $registrator instanceof \FcfVendor\WPDesk\License\PluginRegistrator && $registrator->is_active();
        $builder = new \FcfVendor\WPDesk\PluginBuilder\Builder\InfoActivationBuilder($this->plugin_info, $is_plugin_subscription_active);
        $build_director = new \FcfVendor\WPDesk\PluginBuilder\BuildDirector\LegacyBuildDirector($builder);
        $build_director->build_plugin();
        return $build_director->get_plugin();
    }
    /**
     * Register plugin for subscriptions and updates
     *
     * @return PluginRegistrator
     *
     * @see init_helper note
     *
     */
    private function register_plugin()
    {
        if (\apply_filters('wpdesk_can_register_plugin', \true, $this->plugin_info)) {
            $registrator = new \FcfVendor\WPDesk\License\PluginRegistrator($this->plugin_info);
            $registrator->add_plugin_to_installed_plugins();
            return $registrator;
        }
    }
    /**
     * Helper is a component that gives:
     * - activation interface
     * - automatic updates
     * - logs
     * - some other feats
     *
     * NOTE:
     *
     * It's possible for this method to not found classes embedded here.
     * OTHER plugin in unlikely scenario that THIS plugin is disabled
     * can use this class and do not have this library dependencies as
     * these are loaded using composer.
     *
     * @return PrefixedHelperAsLibrary|null
     */
    private function init_helper()
    {
        $this->prevent_older_helpers();
        $this->prepare_helper_action();
        return $this->get_helper_instance();
    }
    /**
     * Try to disable all other types of helpers
     */
    private function prevent_older_helpers()
    {
        if (\apply_filters('wpdesk_can_hack_shared_helper', \true, $this->plugin_info)) {
            // hack to ensure that the class is loaded so other helpers are disabled
            \class_exists(\WPDesk\Helper\HelperAsLibrary::class, \true);
        }
        if (\apply_filters('wpdesk_can_supress_original_helper', \true, $this->plugin_info)) {
            $this->try_suppress_original_helper_load();
            // start supression only once. Prevent doing it again
            \add_filter('wpdesk_can_supress_original_helper', function () {
                return \false;
            });
        }
        if (\apply_filters('wpdesk_can_remove_old_helper_hooks', \true, $this->plugin_info)) {
            (new \FcfVendor\WPDesk\Helper\HelperRemover())->hooks();
        }
    }
    /**
     * Tries to prevent original Helper from loading
     */
    private function try_suppress_original_helper_load()
    {
        (new \FcfVendor\WPDesk\Plugin\Flow\Initialization\PluginDisablerByFile('wpdesk-helper/wpdesk-helper.php'))->disable();
    }
}
