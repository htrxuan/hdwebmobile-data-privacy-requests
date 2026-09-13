<?php

namespace htrxuan\hddpr;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The only class that ever files a WordPress core personal-data request on a customer's
 * behalf.
 *
 * Several competing GDPR/data-request plugins for WooCommerce accepted an email address
 * directly from the request body when a logged-in customer submitted a data export or
 * erasure request, and used THAT email -- rather than the requester's own account email -- to
 * create the underlying privacy request (CWE-863, Incorrect Authorization). A logged-in
 * customer could submit someone else's email address and initiate an export or erasure
 * request against a stranger's data.
 *
 * This plugin never has an email field to submit in the first place: create_request() takes a
 * user id as its FIRST parameter -- never an email -- and always derives the email from that
 * user's own WordPress account record (`get_userdata($user_id)->user_email`). The only caller
 * (HDDPR_Frontend's admin-post handler) always passes `get_current_user_id()`. Every request
 * is then created and confirmed through WordPress core's own, already-audited personal-data
 * request system (`wp_create_user_request()` / `wp_send_user_request()`) rather than this
 * plugin reimplementing export or erasure itself -- the confirmation email core sends still
 * goes to the account's own real address, so even a compromised logged-in session can't
 * silently complete a request without that email being seen.
 */
class HDDPR_Repository
{
    const ACTION_EXPORT = 'export_personal_data';
    const ACTION_ERASE  = 'remove_personal_data';

    public static function get_allowed_actions()
    {
        return array(self::ACTION_EXPORT, self::ACTION_ERASE);
    }

    /**
     * The only write path in this plugin. $user_id is always a real WordPress user id -- there
     * is no parameter here, or anywhere else in this class, that accepts an email address from
     * a caller.
     */
    public static function create_request($user_id, $action_name)
    {
        if (!in_array($action_name, self::get_allowed_actions(), true)) {
            return new \WP_Error('hddpr_invalid_action', __('Unknown request type.', 'hdwebmobile-data-privacy-requests'));
        }

        $user = get_userdata(absint($user_id));
        if (!$user) {
            return new \WP_Error('hddpr_invalid_user', __('You must be logged in to request this.', 'hdwebmobile-data-privacy-requests'));
        }

        $request_id = wp_create_user_request($user->user_email, $action_name, array(), 'pending');
        if (is_wp_error($request_id)) {
            return $request_id;
        }

        wp_send_user_request($request_id);

        return $request_id;
    }

    /**
     * Every request this customer has ever filed -- looked up by their OWN account email,
     * never by a value supplied from a request.
     */
    public static function get_requests_for_user($user_id)
    {
        $user = get_userdata(absint($user_id));
        if (!$user) {
            return array();
        }

        $query = new \WP_Query(array(
            'post_type'      => 'user_request',
            'title'          => $user->user_email,
            'post_status'    => 'any',
            'posts_per_page' => 50,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        $requests = array();
        foreach ($query->posts as $post) {
            $requests[] = array(
                'action_name' => get_post_field('post_name', $post),
                'status'      => get_post_status($post),
                'date'        => get_the_date('', $post),
            );
        }
        return $requests;
    }
}
