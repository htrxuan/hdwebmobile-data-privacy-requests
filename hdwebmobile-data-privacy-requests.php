<?php

/**
 * Plugin Name: HDWebmobile Data Privacy Requests
 * Plugin URI: https://hdwebmobile.com/plugins/hdwebmobile-data-privacy-requests/
 * Description: Let customers request their own data be exported or erased from My Account, via WordPress's own core privacy-request system.
 * Version: 1.0.0
 * Author: htrxuan - Han Tran
 * Author URI: https://hdwebmobile.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hdwebmobile-data-privacy-requests
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 * Requires PHP: 7.4
 * Requires at least: 6.9
 */

namespace htrxuan\hddpr;

if (!defined('ABSPATH')) {
    exit;
}

define('HDDPR_VERSION', '1.0.0');
define('HDDPR_PLUGIN_FILE', __FILE__);
define('HDDPR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HDDPR_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once HDDPR_PLUGIN_DIR . 'includes/class-hddpr-activator.php';

register_activation_hook(__FILE__, array(HDDPR_Activator::class, 'activate'));

add_action('before_woocommerce_init', function () {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', HDDPR_PLUGIN_FILE, true);
    }
});

add_action('plugins_loaded', function () {
    require_once HDDPR_PLUGIN_DIR . 'includes/class-hddpr-core.php';
    HDDPR_Core::get_instance();
});

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $donate_link = '<a href="https://paypal.me/htrxuan/20" target="_blank" rel="noopener noreferrer">' . esc_html__('Donate', 'hdwebmobile-data-privacy-requests') . '</a>';
    array_unshift($links, $donate_link);
    return $links;
});
