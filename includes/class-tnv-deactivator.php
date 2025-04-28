<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Tonna_Vinyla
 */

class TNV_Deactivator {

    /**
     * Runs during plugin deactivation.
     *
     * Actions performed:
     * - Remove capabilities
     * - Flush rewrite rules
     * 
     * Note: We don't remove database tables or settings to preserve user data
     * in case the plugin is reactivated.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Remove capabilities
        self::remove_capabilities();

        // Flush rewrite rules to ensure permalinks are properly reset
        flush_rewrite_rules();
    }
    
    /**
     * Remove capabilities assigned by the plugin.
     *
     * @since    1.0.0
     */
    private static function remove_capabilities() {
        $admin_role = get_role('administrator');
        
        if ($admin_role) {
            $admin_role->remove_cap('manage_vinyl_products');
            $admin_role->remove_cap('edit_vinyl_products');
            $admin_role->remove_cap('delete_vinyl_products');
        }

        $shop_manager_role = get_role('shop_manager');
        
        if ($shop_manager_role) {
            $shop_manager_role->remove_cap('manage_vinyl_products');
            $shop_manager_role->remove_cap('edit_vinyl_products');
            $shop_manager_role->remove_cap('delete_vinyl_products');
        }
    }
}