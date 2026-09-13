<?php

namespace htrxuan\hddpr;

if (!defined('ABSPATH')) {
    exit;
}

final class HDDPR_Core
{

    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->includes();
        $this->init_hooks();
    }

    private function __clone()
    {
    }

    private function includes()
    {
        require_once HDDPR_PLUGIN_DIR . 'includes/class-hddpr-repository.php';
        require_once HDDPR_PLUGIN_DIR . 'includes/class-hddpr-admin.php';
        require_once HDDPR_PLUGIN_DIR . 'includes/class-hddpr-frontend.php';
    }

    private function init_hooks()
    {
        add_action('admin_notices', array($this, 'render_missing_woocommerce_notice'));

        if (!class_exists('WooCommerce')) {
            return;
        }

        // HDDPR_Admin owns the hdwebmobile_hub_tabs registration used by the shared hub
        // page, so it must load unconditionally (not only when is_admin()).
        HDDPR_Admin::get_instance();
        HDDPR_Frontend::get_instance();
    }

    public function render_missing_woocommerce_notice()
    {
        $screen = get_current_screen();
        if (!$screen || 'plugins' !== $screen->id) {
            return;
        }

        if (!get_transient('hddpr_wc_missing_notice')) {
            return;
        }
        delete_transient('hddpr_wc_missing_notice');
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php esc_html_e('HDWebmobile Data Privacy Requests requires WooCommerce to be installed and active. The plugin has been deactivated.', 'hdwebmobile-data-privacy-requests'); ?>
            </p>
        </div>
        <?php
    }
}
