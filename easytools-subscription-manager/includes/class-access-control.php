<?php
/**
 * Access Control - access control for protected pages
 */

if (!defined('ABSPATH')) {
    exit;
}

class Easytools_Access_Control {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('template_redirect', array($this, 'check_page_access'), 1);
        add_filter('the_content', array($this, 'filter_content_access'), 999);
    }
    
    /**
     * Check page access
     */
    public function check_page_access() {
        // Ignore for admins and unauthenticated users on login/registration pages
        if (is_admin() || $this->is_public_page()) {
            return;
        }

        // Check if page should be protected
        if (!$this->should_protect_current_page()) {
            return;
        }

        // If user not logged in OR doesn't have active subscription - redirect to checkout
        if (!is_user_logged_in() || !$this->user_has_active_subscription()) {
            $this->redirect_to_checkout();
            return;
        }
    }
    
    /**
     * Filter content - add message if subscription is missing
     */
    public function filter_content_access($content) {
        if (is_admin() || !is_singular()) {
            return $content;
        }

        if (!$this->should_protect_current_page()) {
            return $content;
        }

        // Show subscription required message for both non-logged users and users without subscription
        if (!is_user_logged_in() || !$this->user_has_active_subscription()) {
            return $this->get_subscription_required_message();
        }

        return $content;
    }
    
    /**
     * Check if current page should be protected
     */
    private function should_protect_current_page() {
        global $post;

        if (!$post) {
            return false;
        }

        // Never protect the bouncer page itself (to avoid redirect loops)
        $bouncer_page_id = get_option('easytools_bouncer_page', '');
        if (!empty($bouncer_page_id) && $post->ID == $bouncer_page_id) {
            return false;
        }

        $protect_all = get_option('easytools_protect_all', 'no') === 'yes';
        $protected_pages = get_option('easytools_protected_pages', array());
        $excluded_pages = get_option('easytools_exclude_pages', array());

        // Mode 1: Protect all pages EXCEPT selected ones
        if ($protect_all) {
            // If page is on exclusion list, DO NOT protect
            if (in_array($post->ID, $excluded_pages)) {
                return false;
            }
            // Otherwise protect
            return true;
        }

        // Mode 2: Protect ONLY selected pages
        return in_array($post->ID, $protected_pages);
    }
    
    /**
     * Check if this is a public page (login, registration, etc.)
     */
    private function is_public_page() {
        // WordPress login/registration pages
        if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
            return true;
        }

        // API endpoints
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return true;
        }

        // AJAX requests
        if (wp_doing_ajax()) {
            return true;
        }

        return false;
    }
    
    /**
     * Check if user has active subscription
     */
    private function user_has_active_subscription() {
        if (!is_user_logged_in()) {
            return false;
        }

        $user_id = get_current_user_id();
        $subscribed = get_user_meta($user_id, 'subscribed', true);

        return $subscribed === '1';
    }

    /**
     * Redirect to login
     */
    private function redirect_to_login() {
        $redirect_to = urlencode($this->get_current_url());
        $login_url = wp_login_url($redirect_to);

        wp_redirect($login_url);
        exit;
    }

    /**
     * Redirect to checkout in Easytools (with user email if available)
     * OR redirect to bouncer page if enabled
     */
    private function redirect_to_checkout() {
        // Check if bouncer page is enabled
        $bouncer_enabled = get_option('easytools_enable_bouncer', 'no') === 'yes';
        $bouncer_page_id = get_option('easytools_bouncer_page', '');

        if ($bouncer_enabled && !empty($bouncer_page_id)) {
            // Redirect to bouncer page
            $bouncer_url = get_permalink($bouncer_page_id);
            if ($bouncer_url) {
                wp_redirect($bouncer_url);
                exit;
            }
            // If bouncer page not found, fall through to checkout redirect
        }

        // Default: redirect to checkout
        $checkout_url = get_option('easytools_checkout_url', '');

        if (empty($checkout_url)) {
            wp_die(
                __('Payment URL not configured. Please contact the administrator.', 'easytools-sub'),
                __('Configuration Error', 'easytools-sub')
            );
        }

        // If user is logged in, add their email to URL
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_email = $current_user->user_email;

            if (!empty($user_email)) {
                $checkout_url = $this->add_email_to_url($checkout_url, $user_email);
            }
        }
        // If user is not logged in, just redirect to checkout without email
        // They will enter their email in Easytools checkout form

        // Redirect
        wp_redirect($checkout_url);
        exit;
    }

    /**
     * Add email to checkout URL
     */
    private function add_email_to_url($url, $email) {
        // Parse URL
        $parsed_url = parse_url($url);

        // Get existing parameters
        $query_params = array();
        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $query_params);
        }

        // Add email - Easytools uses 'email' or 'customer_email' parameter
        $query_params['email'] = $email;

        // Rebuild URL
        $new_query = http_build_query($query_params);
        
        $new_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
        
        if (isset($parsed_url['port'])) {
            $new_url .= ':' . $parsed_url['port'];
        }
        
        if (isset($parsed_url['path'])) {
            $new_url .= $parsed_url['path'];
        }
        
        if (!empty($new_query)) {
            $new_url .= '?' . $new_query;
        }
        
        if (isset($parsed_url['fragment'])) {
            $new_url .= '#' . $parsed_url['fragment'];
        }
        
        return $new_url;
    }
    
    /**
     * Get current URL
     */
    private function get_current_url() {
        global $wp;
        return home_url(add_query_arg(array(), $wp->request));
    }

    /**
     * Message for non-logged-in users
     */
    private function get_login_message() {
        $login_url = wp_login_url($this->get_current_url());
        $register_url = wp_registration_url();
        
        ob_start();
        ?>
        <div class="easytools-access-message" style="background: #f0f0f1; border-left: 4px solid #2271b1; padding: 20px; margin: 20px 0; border-radius: 4px;">
            <h3 style="margin-top: 0;"><?php _e('Login Required', 'easytools-sub'); ?></h3>
            <p><?php _e('This content is available only to logged-in users.', 'easytools-sub'); ?></p>
            <p>
                <a href="<?php echo esc_url($login_url); ?>" class="button button-primary">
                    <?php _e('Log In', 'easytools-sub'); ?>
                </a>
                <?php if (get_option('users_can_register')) : ?>
                    <a href="<?php echo esc_url($register_url); ?>" class="button">
                        <?php _e('Register', 'easytools-sub'); ?>
                    </a>
                <?php endif; ?>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Message about required subscription
     */
    private function get_subscription_required_message() {
        $checkout_url = get_option('easytools_checkout_url', '');

        // If user is logged in, add their email to checkout URL
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            if (!empty($current_user->user_email)) {
                $checkout_url = $this->add_email_to_url($checkout_url, $current_user->user_email);
            }
        }

        ob_start();
        ?>
        <div class="easytools-subscription-message" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 4px;">
            <h3 style="margin-top: 0;"><?php _e('Subscription Required', 'easytools-sub'); ?></h3>
            <p><?php _e('This content is available only to users with an active subscription.', 'easytools-sub'); ?></p>
            <?php if (is_user_logged_in()) : ?>
                <?php $current_user = wp_get_current_user(); ?>
                <p>
                    <strong><?php printf(__('Hello, %s!', 'easytools-sub'), esc_html($current_user->display_name)); ?></strong>
                    <?php _e('To access this content, please purchase a subscription.', 'easytools-sub'); ?>
                </p>
            <?php else : ?>
                <p>
                    <?php _e('To access this content, please purchase a subscription.', 'easytools-sub'); ?>
                </p>
            <?php endif; ?>
            <p>
                <a href="<?php echo esc_url($checkout_url); ?>" class="button button-primary" style="background: #05c7aa; border-color: #05c7aa;">
                    <?php _e('Get Access', 'easytools-sub'); ?>
                </a>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Public method to check subscription (for use in templates)
     */
    public static function has_active_subscription($user_id = null) {
        if (!$user_id) {
            if (!is_user_logged_in()) {
                return false;
            }
            $user_id = get_current_user_id();
        }
        
        $subscribed = get_user_meta($user_id, 'subscribed', true);
        return $subscribed === '1';
    }
    
    /**
     * Public method to get checkout URL with email
     */
    public static function get_checkout_url_with_email($user_email = null) {
        if (!$user_email && is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_email = $current_user->user_email;
        }
        
        $checkout_url = get_option('easytools_checkout_url', '');
        
        if (empty($checkout_url) || empty($user_email)) {
            return $checkout_url;
        }
        
        $instance = self::get_instance();
        return $instance->add_email_to_url($checkout_url, $user_email);
    }
}
