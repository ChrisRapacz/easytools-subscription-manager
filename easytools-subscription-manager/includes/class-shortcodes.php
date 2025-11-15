<?php
/**
 * Shortcodes - shortcodes for use in content
 */

if (!defined('ABSPATH')) {
    exit;
}

class Easytools_Shortcodes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Register shortcodes
        add_shortcode('easytools_subscribe_button', array($this, 'subscribe_button_shortcode'));
        add_shortcode('easytools_subscription_status', array($this, 'subscription_status_shortcode'));
        add_shortcode('easytools_protected_content', array($this, 'protected_content_shortcode'));
        add_shortcode('easytools_member_only', array($this, 'member_only_shortcode'));
    }

    /**
     * Shortcode: Subscribe button
     * Usage: [easytools_subscribe_button text="Get Access" class="custom-class"]
     */
    public function subscribe_button_shortcode($atts) {
        $atts = shortcode_atts(array(
            'text' => __('Get Access', 'easytools-sub'),
            'class' => '',
            'style' => ''
        ), $atts);

        $checkout_url = get_option('easytools_checkout_url', '');

        if (empty($checkout_url)) {
            return '<p style="color: red;">' . __('[Error: Payment URL not configured]', 'easytools-sub') . '</p>';
        }

        // If user is logged in, add email
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $checkout_url = Easytools_Access_Control::get_checkout_url_with_email($current_user->user_email);
        }
        
        $button_class = 'easytools-subscribe-button button ' . esc_attr($atts['class']);
        $button_style = 'background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 4px; text-decoration: none; display: inline-block; font-weight: bold; ' . esc_attr($atts['style']);
        
        return sprintf(
            '<a href="%s" class="%s" style="%s">%s</a>',
            esc_url($checkout_url),
            $button_class,
            $button_style,
            esc_html($atts['text'])
        );
    }
    
    /**
     * Shortcode: Subscription status
     * Usage: [easytools_subscription_status]
     */
    public function subscription_status_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Log in to see your subscription status.', 'easytools-sub') . '</p>';
        }
        
        $user_id = get_current_user_id();
        $subscribed = get_user_meta($user_id, 'subscribed', true);
        $subscription_type = get_user_meta($user_id, 'subscription_type', true);
        $renewal_date = get_user_meta($user_id, 'renewal_date', true);
        $subscription_one_time = get_user_meta($user_id, 'subscription_one_time', true);
        
        ob_start();
        ?>
        <div class="easytools-subscription-status" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="margin-top: 0;"><?php _e('Your Subscription Status', 'easytools-sub'); ?></h3>

            <?php if ($subscribed === '1') : ?>
                <p style="color: green; font-weight: bold; font-size: 18px;">
                    ✓ <?php _e('Active', 'easytools-sub'); ?>
                </p>

                <?php if ($subscription_type) : ?>
                    <p><strong><?php _e('Type:', 'easytools-sub'); ?></strong> <?php echo esc_html($subscription_type); ?></p>
                <?php endif; ?>

                <?php if ($subscription_one_time === '1') : ?>
                    <p><?php _e('🎉 Lifetime access - no renewals', 'easytools-sub'); ?></p>
                <?php elseif ($renewal_date) : ?>
                    <p><strong><?php _e('Renewal:', 'easytools-sub'); ?></strong> <?php echo esc_html($renewal_date); ?></p>
                <?php endif; ?>

            <?php else : ?>
                <p style="color: #d63638; font-weight: bold; font-size: 18px;">
                    ✗ <?php _e('Inactive', 'easytools-sub'); ?>
                </p>
                <p><?php _e('You do not have an active subscription.', 'easytools-sub'); ?></p>
                <?php echo do_shortcode('[easytools_subscribe_button]'); ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Shortcode: Protected content
     * Usage: [easytools_protected_content]Content for subscribers only[/easytools_protected_content]
     */
    public function protected_content_shortcode($atts, $content = null) {
        if (!is_user_logged_in()) {
            return '<div class="easytools-login-required" style="background: #f0f0f1; padding: 20px; border-left: 4px solid #2271b1; margin: 20px 0;">' .
                   '<p>' . __('Log in to view this content.', 'easytools-sub') . '</p>' .
                   '<a href="' . wp_login_url(get_permalink()) . '" class="button">' . __('Log In', 'easytools-sub') . '</a>' .
                   '</div>';
        }

        if (!Easytools_Access_Control::has_active_subscription()) {
            return '<div class="easytools-subscription-required" style="background: #fff3cd; padding: 20px; border-left: 4px solid #ffc107; margin: 20px 0;">' .
                   '<p>' . __('This content is available only to users with an active subscription.', 'easytools-sub') . '</p>' .
                   do_shortcode('[easytools_subscribe_button]') .
                   '</div>';
        }

        return '<div class="easytools-protected-content">' . do_shortcode($content) . '</div>';
    }

    /**
     * Shortcode: Member-only content (alias)
     * Usage: [easytools_member_only]Content[/easytools_member_only]
     */
    public function member_only_shortcode($atts, $content = null) {
        return $this->protected_content_shortcode($atts, $content);
    }
}
