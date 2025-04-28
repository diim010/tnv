<?php
/**
 * Plugin Name: Tonna Vinyla
 * Plugin URI: https://example.com/tonna-vinyla
 * Description: WordPress plugin for managing vinyl records with WooCommerce integration, Discogs data import, QR scanning, and music preview functionality.
 * Version: 1.0.0
 * Author: diim010
 * Author URI: https://diim010.github.io/en
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: tonna-vinyla
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Define plugin constants
 */
define('TNV_VERSION', '1.0.0');
define('TNV_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TNV_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TNV_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('TNV_PLUGIN_NAME', 'tonna-vinyla');

/**
 * The code that runs during plugin activation.
 */
function activate_tonna_vinyla() {
    require_once TNV_PLUGIN_DIR . 'includes/class-tnv-activator.php';
    TNV_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_tonna_vinyla() {
    require_once TNV_PLUGIN_DIR . 'includes/class-tnv-deactivator.php';
    TNV_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_tonna_vinyla');
register_deactivation_hook(__FILE__, 'deactivate_tonna_vinyla');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require TNV_PLUGIN_DIR . 'includes/class-tonna-vinyla.php';

/**
 * Begins execution of the plugin.
 */
function run_tonna_vinyla() {
    // Check if WooCommerce is active
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action('admin_notices', function() {
            ?>
            <div class="error">
                <p><?php _e('Tonna Vinyla requires WooCommerce to be installed and active.', 'tonna-vinyla'); ?></p>
            </div>
            <?php
        });
        return;
    }

    // Load plugin when all plugins are loaded
    $plugin = new Tonna_Vinyla();
    $plugin->run();
}

// Hook to run the plugin when WordPress is loaded
add_action('plugins_loaded', 'run_tonna_vinyla', 11); // Priority 11 to ensure it runs after WooCommerce

/**
 * Add a link to the plugin's settings page in the plugins list
 */
function tnv_add_plugin_settings_link($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=tonna-vinyla') . '">' . __('Settings', 'tonna-vinyla') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . TNV_PLUGIN_BASENAME, 'tnv_add_plugin_settings_link');