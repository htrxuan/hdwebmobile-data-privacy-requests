<?php

namespace htrxuan\hddpr;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Renders the "Export my data" / "Erase my account" request buttons on the My Account
 * dashboard, and handles the resulting (logged-in-only) submission. See
 * HDDPR_Repository's docblock for the security design.
 */
final class HDDPR_Frontend
{

    const NONCE_ACTION = 'hddpr_request';

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
        add_action('woocommerce_account_dashboard', array($this, 'render_panel'));
        add_action('admin_post_hddpr_request', array($this, 'handle_request'));
        // Deliberately no admin_post_nopriv_hddpr_request -- this plugin only ever acts on the
        // CURRENTLY logged-in account, so there is nothing for a guest request to do here at
        // all.
    }

    public function render_panel()
    {
        $user_id = get_current_user_id();
        if (!$user_id) {
            return;
        }

        if (!empty($_GET['hddpr_requested'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a read-only success flag on the redirect back from a nonce-checked submission, not a state change.
            echo '<div class="woocommerce-message">' . esc_html__('Your request has been submitted. Check your account email for a confirmation link.', 'hdwebmobile-data-privacy-requests') . '</div>';
        }

        $requests = HDDPR_Repository::get_requests_for_user($user_id);
        ?>
        <div class="hddpr-panel" style="margin-top:2em;padding:1em;border:1px solid #ddd;">
            <h2><?php esc_html_e('Your data', 'hdwebmobile-data-privacy-requests'); ?></h2>
            <p><?php esc_html_e('Request a copy of your personal data, or request that your account be erased. A confirmation link will be emailed to your own account email before anything happens.', 'hdwebmobile-data-privacy-requests'); ?></p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;">
                <input type="hidden" name="action" value="hddpr_request" />
                <input type="hidden" name="hddpr_action" value="<?php echo esc_attr(HDDPR_Repository::ACTION_EXPORT); ?>" />
                <?php wp_nonce_field(self::NONCE_ACTION, 'hddpr_nonce'); ?>
                <button type="submit" class="button"><?php esc_html_e('Request data export', 'hdwebmobile-data-privacy-requests'); ?></button>
            </form>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin-left:0.5em;">
                <input type="hidden" name="action" value="hddpr_request" />
                <input type="hidden" name="hddpr_action" value="<?php echo esc_attr(HDDPR_Repository::ACTION_ERASE); ?>" />
                <?php wp_nonce_field(self::NONCE_ACTION, 'hddpr_nonce'); ?>
                <button type="submit" class="button"><?php esc_html_e('Request account erasure', 'hdwebmobile-data-privacy-requests'); ?></button>
            </form>

            <?php if (!empty($requests)) : ?>
                <h3><?php esc_html_e('Your requests', 'hdwebmobile-data-privacy-requests'); ?></h3>
                <ul>
                    <?php foreach ($requests as $request) : ?>
                        <li>
                            <?php
                            echo esc_html(wp_user_request_action_description($request['action_name']))
                                . ' -- ' . esc_html($request['status'])
                                . ' (' . esc_html($request['date']) . ')';
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php
    }

    public function handle_request()
    {
        if (!is_user_logged_in()) {
            wp_die(esc_html__('You must be logged in to do this.', 'hdwebmobile-data-privacy-requests'));
        }
        check_admin_referer(self::NONCE_ACTION, 'hddpr_nonce');

        $action_name = isset($_POST['hddpr_action']) ? sanitize_key(wp_unslash($_POST['hddpr_action'])) : '';
        $result      = HDDPR_Repository::create_request(get_current_user_id(), $action_name);

        $redirect = wc_get_account_endpoint_url('dashboard');
        if (!is_wp_error($result)) {
            $redirect = add_query_arg('hddpr_requested', '1', $redirect);
        }
        wp_safe_redirect($redirect);
        exit;
    }
}
