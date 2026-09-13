<?php

namespace htrxuan\hddpr;

if (!defined('ABSPATH')) {
    exit;
}

class HDDPR_Admin
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
        require_once HDDPR_PLUGIN_DIR . 'includes/class-hddpr-hub.php';
        add_filter('hdwebmobile_hub_tabs', array($this, 'register_hub_tabs'));
    }

    public function register_hub_tabs($tabs)
    {
        $tabs['data-privacy-requests'] = array(
            'label'  => __('Data Privacy Requests', 'hdwebmobile-data-privacy-requests'),
            'order'  => 163,
            'render' => array($this, 'render_page'),
        );
        return $tabs;
    }

    public function render_page()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('You do not have permission to do this.', 'hdwebmobile-data-privacy-requests'));
        }
        ?>
        <p><?php esc_html_e('Adds "Request data export" and "Request account erasure" buttons to My Account. Every request is filed through WordPress\'s own core personal-data request system -- nothing here is a separate, custom export or erasure engine.', 'hdwebmobile-data-privacy-requests'); ?></p>
        <p>
            <?php
            printf(
                /* translators: 1: link to the Export Personal Data screen, 2: link to the Erase Personal Data screen */
                wp_kses_post(__('Review and action pending requests from the standard %1$s and %2$s screens, exactly as you would for a request made through WordPress\'s own admin-initiated flow.', 'hdwebmobile-data-privacy-requests')),
                '<a href="' . esc_url(admin_url('export-personal-data.php')) . '">' . esc_html__('Export Personal Data', 'hdwebmobile-data-privacy-requests') . '</a>',
                '<a href="' . esc_url(admin_url('erase-personal-data.php')) . '">' . esc_html__('Erase Personal Data', 'hdwebmobile-data-privacy-requests') . '</a>'
            );
            ?>
        </p>
        <?php
    }
}
