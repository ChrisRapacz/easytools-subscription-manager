<?php
/**
 * Admin Panel Settings
 *
 * Handles the admin interface for Easytools Subscription plugin
 * with modern premium visual design
 */

if (!defined('ABSPATH')) {
    exit;
}

class Easytools_Admin_Settings {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_head', array($this, 'add_premium_styles'));
        add_action('wp_ajax_easytools_export_logs', array($this, 'handle_export_logs'));
        add_action('wp_ajax_easytools_toggle_user_access', array($this, 'handle_toggle_user_access'));
        add_action('wp_ajax_easytools_create_bouncer_page', array($this, 'handle_create_bouncer_page'));
        add_action('wp_ajax_easytools_get_bouncer_html', array($this, 'handle_get_bouncer_html'));
    }

    /**
     * Add premium inline styles
     */
    public function add_premium_styles() {
        $screen = get_current_screen();
        if (strpos($screen->id, 'easytools') === false) {
            return;
        }
        ?>
        <style>
            /* Premium Container Styles */
            .easytools-premium-wrap {
                background: #05c7aa;
                padding: 40px;
                margin: 20px 0 20px -20px;
                min-height: 100vh;
            }

            .easytools-premium-header {
                background: rgba(255, 255, 255, 0.98);
                padding: 30px;
                border-radius: 16px;
                margin-bottom: 30px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            }

            .easytools-premium-header h1 {
                margin: 0 0 10px 0;
                font-size: 28px;
                font-weight: 700;
                line-height: 1.5;
                color: #05c7aa;
                padding: 8px 0;
            }

            .easytools-premium-header p {
                margin: 0;
                color: #666;
                font-size: 16px;
                line-height: 1.5;
            }

            /* Settings Grid Layout */
            .easytools-settings-grid {
                display: grid;
                grid-template-columns: 1fr 350px;
                gap: 30px;
                margin-bottom: 30px;
            }

            @media (max-width: 1200px) {
                .easytools-settings-grid {
                    grid-template-columns: 1fr;
                }
            }

            /* Premium Card Styles */
            .easytools-premium-card {
                background: rgba(255, 255, 255, 0.98);
                border-radius: 16px;
                padding: 30px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .easytools-premium-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            }

            /* Section Headers with Icons */
            .easytools-section-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin: 0 0 20px 0;
                padding-bottom: 15px;
                border-bottom: 2px solid #f0f0f0;
            }

            .easytools-section-header .dashicons {
                font-size: 28px;
                width: 28px;
                height: 28px;
                color: #05c7aa;
            }

            .easytools-section-header h2 {
                margin: 0;
                font-size: 22px;
                font-weight: 600;
                line-height: 1.4;
                color: #2c3e50;
            }

            /* Form Field Styling */
            .easytools-premium-card .form-table {
                margin-top: 0;
            }

            .easytools-premium-card .form-table th {
                padding: 20px 0 20px 0;
                font-weight: 600;
                color: #2c3e50;
                width: 250px;
            }

            .easytools-premium-card .form-table td {
                padding: 20px 0;
            }

            .easytools-premium-card input[type="text"],
            .easytools-premium-card input[type="url"],
            .easytools-premium-card input[type="email"],
            .easytools-premium-card input[type="password"],
            .easytools-premium-card input[type="number"],
            .easytools-premium-card select {
                border: 2px solid #e1e8ed;
                border-radius: 8px;
                padding: 10px 15px;
                font-size: 14px;
                transition: all 0.3s ease;
            }

            .easytools-premium-card input[type="text"]:focus,
            .easytools-premium-card input[type="url"]:focus,
            .easytools-premium-card input[type="email"]:focus,
            .easytools-premium-card input[type="password"]:focus,
            .easytools-premium-card input[type="number"]:focus,
            .easytools-premium-card select:focus {
                border-color: #05c7aa;
                box-shadow: 0 0 0 3px rgba(5, 199, 170, 0.1);
                outline: none;
            }

            /* Checkbox Styling */
            .easytools-premium-card input[type="checkbox"] {
                width: 18px;
                height: 18px;
                margin-right: 8px;
                cursor: pointer;
            }

            /* Description Text */
            .easytools-premium-card .description {
                color: #666;
                font-size: 13px;
                line-height: 1.6;
                margin-top: 8px;
            }

            .easytools-premium-card .description code {
                background: #e0f5f2;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 12px;
                color: #0e534c;
            }

            /* Submit Button */
            .easytools-premium-card .submit input[type="submit"] {
                background: #05c7aa;
                border: none;
                border-radius: 8px;
                color: white;
                padding: 12px 30px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(5, 199, 170, 0.3);
            }

            .easytools-premium-card .submit input[type="submit"]:hover {
                background: #00a18b;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(5, 199, 170, 0.4);
            }

            /* Sidebar Cards */
            .easytools-sidebar-card {
                background: rgba(255, 255, 255, 0.98);
                border-radius: 16px;
                padding: 25px;
                margin-bottom: 20px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            }

            .easytools-sidebar-card h3 {
                margin: 0 0 15px 0;
                font-size: 18px;
                font-weight: 600;
                color: #2c3e50;
            }

            .easytools-sidebar-card ol {
                margin: 0;
                padding-left: 20px;
            }

            .easytools-sidebar-card ol li {
                margin-bottom: 10px;
                color: #555;
                line-height: 1.6;
            }

            .easytools-sidebar-card a {
                color: #05c7aa;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }

            .easytools-sidebar-card a:hover {
                color: #058071;
            }

            /* Stats Display */
            .easytools-stat-number {
                font-size: 36px;
                font-weight: 700;
                line-height: 1.4;
                padding: 6px 0;
                color: #05c7aa;
                margin: 15px 0;
                text-align: center;
                display: inline-block;
                padding: 5px 0;
                max-width: 100%;
                word-break: break-word;
            }

            /* Status Indicators */
            .easytools-status-active {
                color: #10b981;
                font-weight: 600;
            }

            .easytools-status-inactive {
                color: #ef4444;
                font-weight: 600;
            }

            .easytools-status-dot {
                display: inline-block;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                margin-right: 6px;
            }

            .easytools-status-dot.active {
                background: #10b981;
                box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
            }

            .easytools-status-dot.inactive {
                background: #ef4444;
                box-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
            }

            /* Table Styling */
            .easytools-premium-wrap .wp-list-table {
                background: white;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            }

            .easytools-premium-wrap .wp-list-table th {
                background: #05c7aa;
                color: white;
                font-weight: 600;
                padding: 15px;
                line-height: 1.4;
            }

            .easytools-premium-wrap .wp-list-table td {
                padding: 15px;
                border-bottom: 1px solid #f0f0f0;
            }

            .easytools-premium-wrap .wp-list-table tr:hover td {
                background: #f8f9fa;
            }

            /* Button Styling */
            .easytools-premium-wrap .button {
                border-radius: 6px;
                border: 2px solid #05c7aa;
                color: #05c7aa;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .easytools-premium-wrap .button:hover {
                background: #05c7aa;
                color: white;
            }

            /* Modal Styling */
            #log-details-modal {
                backdrop-filter: blur(5px);
            }

            #log-details-modal > div {
                background: white;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }

            /* Multi-select Styling */
            .easytools-page-select {
                border: 2px solid #e1e8ed !important;
                border-radius: 8px !important;
                padding: 10px !important;
            }

            .easytools-page-select:focus {
                border-color: #05c7aa !important;
                box-shadow: 0 0 0 3px rgba(5, 199, 170, 0.1) !important;
                outline: none !important;
            }

            /* Settings Success Message */
            .settings-error.updated {
                background: #d1fae5;
                border-left: 4px solid #10b981;
                border-radius: 8px;
                margin: 20px 0;
            }

            /* Info Box */
            .easytools-info-box {
                background: rgba(5, 199, 170, 0.1);
                border-left: 4px solid #05c7aa;
                padding: 15px;
                border-radius: 8px;
                margin: 15px 0;
            }

            .easytools-info-box p {
                margin: 0;
                color: #2c3e50;
                line-height: 1.6;
            }
        </style>
        <?php
    }

    /**
     * Add menu in admin panel
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Easytools Subscription', 'easytools-sub'),
            __('Easytools', 'easytools-sub'),
            'manage_options',
            'easytools-subscription',
            array($this, 'render_settings_page'),
            'dashicons-cart',
            30
        );

        add_submenu_page(
            'easytools-subscription',
            __('Settings', 'easytools-sub'),
            __('Settings', 'easytools-sub'),
            'manage_options',
            'easytools-subscription',
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            'easytools-subscription',
            __('Webhook Logs', 'easytools-sub'),
            __('Webhook Logs', 'easytools-sub'),
            'manage_options',
            'easytools-webhook-logs',
            array($this, 'render_logs_page')
        );

        add_submenu_page(
            'easytools-subscription',
            __('Subscribers', 'easytools-sub'),
            __('Subscribers', 'easytools-sub'),
            'manage_options',
            'easytools-subscribers',
            array($this, 'render_subscribers_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        // Section: Basic Settings
        add_settings_section(
            'easytools_basic_settings',
            '<span class="dashicons dashicons-admin-settings"></span>' . __('Basic Settings', 'easytools-sub'),
            array($this, 'basic_settings_callback'),
            'easytools-subscription'
        );

        register_setting('easytools_settings', 'easytools_checkout_url');
        add_settings_field(
            'easytools_checkout_url',
            __('Easytools Checkout URL', 'easytools-sub'),
            array($this, 'checkout_url_callback'),
            'easytools-subscription',
            'easytools_basic_settings'
        );

        register_setting('easytools_settings', 'easytools_webhook_signing_key');
        add_settings_field(
            'easytools_webhook_signing_key',
            __('Webhook Signing Key', 'easytools-sub'),
            array($this, 'signing_key_callback'),
            'easytools-subscription',
            'easytools_basic_settings'
        );

        register_setting('easytools_settings', 'easytools_api_token');
        add_settings_field(
            'easytools_api_token',
            __('API Token (Optional)', 'easytools-sub'),
            array($this, 'api_token_callback'),
            'easytools-subscription',
            'easytools_basic_settings'
        );

        // Section: Access Control
        add_settings_section(
            'easytools_access_settings',
            '<span class="dashicons dashicons-lock"></span>' . __('Access Control', 'easytools-sub'),
            array($this, 'access_settings_callback'),
            'easytools-subscription'
        );

        register_setting('easytools_settings', 'easytools_protect_all');
        add_settings_field(
            'easytools_protect_all',
            __('Protect Entire Site', 'easytools-sub'),
            array($this, 'protect_all_callback'),
            'easytools-subscription',
            'easytools_access_settings'
        );

        register_setting('easytools_settings', 'easytools_exclude_pages', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_page_array')
        ));
        add_settings_field(
            'easytools_exclude_pages',
            __('Exclude Pages (if protecting entire site)', 'easytools-sub'),
            array($this, 'exclude_pages_callback'),
            'easytools-subscription',
            'easytools_access_settings'
        );

        register_setting('easytools_settings', 'easytools_protected_pages', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_page_array')
        ));
        add_settings_field(
            'easytools_protected_pages',
            __('Protected Pages (if NOT protecting entire site)', 'easytools-sub'),
            array($this, 'protected_pages_callback'),
            'easytools-subscription',
            'easytools_access_settings'
        );

        // Section: Bouncer Page
        add_settings_section(
            'easytools_bouncer_settings',
            '<span class="dashicons dashicons-visibility"></span>' . __('Bouncer Page', 'easytools-sub'),
            array($this, 'bouncer_settings_callback'),
            'easytools-subscription'
        );

        register_setting('easytools_settings', 'easytools_enable_bouncer');
        add_settings_field(
            'easytools_enable_bouncer',
            __('Enable Bouncer Page', 'easytools-sub'),
            array($this, 'enable_bouncer_callback'),
            'easytools-subscription',
            'easytools_bouncer_settings'
        );

        register_setting('easytools_settings', 'easytools_bouncer_page');
        add_settings_field(
            'easytools_bouncer_page',
            __('Bouncer Page', 'easytools-sub'),
            array($this, 'bouncer_page_callback'),
            'easytools-subscription',
            'easytools_bouncer_settings'
        );

        register_setting('easytools_settings', 'easytools_bouncer_product_url');
        add_settings_field(
            'easytools_bouncer_product_url',
            __('Product URL', 'easytools-sub'),
            array($this, 'bouncer_product_url_callback'),
            'easytools-subscription',
            'easytools_bouncer_settings'
        );

        register_setting('easytools_settings', 'easytools_bouncer_icon_color');
        add_settings_field(
            'easytools_bouncer_icon_color',
            __('Icon Color', 'easytools-sub'),
            array($this, 'bouncer_icon_color_callback'),
            'easytools-subscription',
            'easytools_bouncer_settings'
        );

        register_setting('easytools_settings', 'easytools_bouncer_button_color');
        add_settings_field(
            'easytools_bouncer_button_color',
            __('Button Color', 'easytools-sub'),
            array($this, 'bouncer_button_color_callback'),
            'easytools-subscription',
            'easytools_bouncer_settings'
        );

        register_setting('easytools_settings', 'easytools_bouncer_bg_color');
        add_settings_field(
            'easytools_bouncer_bg_color',
            __('Background Color', 'easytools-sub'),
            array($this, 'bouncer_bg_color_callback'),
            'easytools-subscription',
            'easytools_bouncer_settings'
        );

        // Section: User Registration
        add_settings_section(
            'easytools_user_settings',
            '<span class="dashicons dashicons-admin-users"></span>' . __('User Registration', 'easytools-sub'),
            array($this, 'user_settings_callback'),
            'easytools-subscription'
        );

        register_setting('easytools_settings', 'easytools_account_creation_mode');
        add_settings_field(
            'easytools_account_creation_mode',
            __('Account Creation Mode', 'easytools-sub'),
            array($this, 'account_creation_mode_callback'),
            'easytools-subscription',
            'easytools_user_settings'
        );

        register_setting('easytools_settings', 'easytools_default_role');
        add_settings_field(
            'easytools_default_role',
            __('Default User Role', 'easytools-sub'),
            array($this, 'default_role_callback'),
            'easytools-subscription',
            'easytools_user_settings'
        );

        register_setting('easytools_settings', 'easytools_send_welcome_email');
        add_settings_field(
            'easytools_send_welcome_email',
            __('Send Welcome Email', 'easytools-sub'),
            array($this, 'welcome_email_callback'),
            'easytools-subscription',
            'easytools_user_settings'
        );

        register_setting('easytools_settings', 'easytools_email_from_address');
        add_settings_field(
            'easytools_email_from_address',
            __('Email From Address', 'easytools-sub'),
            array($this, 'email_from_address_callback'),
            'easytools-subscription',
            'easytools_user_settings'
        );

        register_setting('easytools_settings', 'easytools_email_from_name');
        add_settings_field(
            'easytools_email_from_name',
            __('Email From Name', 'easytools-sub'),
            array($this, 'email_from_name_callback'),
            'easytools-subscription',
            'easytools_user_settings'
        );

        register_setting('easytools_settings', 'easytools_email_brand_color');
        add_settings_field(
            'easytools_email_brand_color',
            __('Email Brand Color', 'easytools-sub'),
            array($this, 'email_brand_color_callback'),
            'easytools-subscription',
            'easytools_user_settings'
        );

        // Section: Email Content
        add_settings_section(
            'easytools_email_content_settings',
            '<span class="dashicons dashicons-email"></span>' . __('Email Content Customization', 'easytools-sub'),
            array($this, 'email_content_settings_callback'),
            'easytools-subscription'
        );

        register_setting('easytools_settings', 'easytools_email_subject');
        add_settings_field(
            'easytools_email_subject',
            __('Email Subject', 'easytools-sub'),
            array($this, 'email_subject_callback'),
            'easytools-subscription',
            'easytools_email_content_settings'
        );

        register_setting('easytools_settings', 'easytools_email_heading');
        add_settings_field(
            'easytools_email_heading',
            __('Email Heading', 'easytools-sub'),
            array($this, 'email_heading_callback'),
            'easytools-subscription',
            'easytools_email_content_settings'
        );

        register_setting('easytools_settings', 'easytools_email_message');
        add_settings_field(
            'easytools_email_message',
            __('Email Message', 'easytools-sub'),
            array($this, 'email_message_callback'),
            'easytools-subscription',
            'easytools_email_content_settings'
        );

        register_setting('easytools_settings', 'easytools_email_button_text');
        add_settings_field(
            'easytools_email_button_text',
            __('Button Text', 'easytools-sub'),
            array($this, 'email_button_text_callback'),
            'easytools-subscription',
            'easytools_email_content_settings'
        );

        // Test Email field (no registration needed, it's an action not a setting)
        add_settings_field(
            'easytools_test_email_button',
            __('Test Email', 'easytools-sub'),
            array($this, 'test_email_button_callback'),
            'easytools-subscription',
            'easytools_email_content_settings'
        );

        // Section: Notifications
        add_settings_section(
            'easytools_notification_settings',
            '<span class="dashicons dashicons-email-alt"></span>' . __('Notifications', 'easytools-sub'),
            array($this, 'notification_settings_callback'),
            'easytools-subscription'
        );

        register_setting('easytools_settings', 'easytools_admin_notifications');
        add_settings_field(
            'easytools_admin_notifications',
            __('Admin Notifications', 'easytools-sub'),
            array($this, 'admin_notifications_callback'),
            'easytools-subscription',
            'easytools_notification_settings'
        );

        register_setting('easytools_settings', 'easytools_admin_email');
        add_settings_field(
            'easytools_admin_email',
            __('Admin Email', 'easytools-sub'),
            array($this, 'admin_email_callback'),
            'easytools-subscription',
            'easytools_notification_settings'
        );
    }

    /**
     * Callbacks for sections
     */
    public function basic_settings_callback() {
        echo '<div class="easytools-info-box">';
        echo '<p>' . __('Configure basic integration settings with Easytools payment platform.', 'easytools-sub') . '</p>';
        echo '</div>';
    }

    public function access_settings_callback() {
        echo '<div class="easytools-info-box">';
        echo '<p>' . __('Define which pages require an active subscription to access.', 'easytools-sub') . '</p>';
        echo '</div>';
    }

    public function bouncer_settings_callback() {
        echo '<div class="easytools-info-box">';
        echo '<p>' . __('Configure a custom page that non-subscribers see when trying to access protected content. This provides a better user experience than redirecting to checkout directly.', 'easytools-sub') . '</p>';
        echo '</div>';
    }

    public function user_settings_callback() {
        echo '<div class="easytools-info-box">';
        echo '<p>' . __('Configure how user accounts are created when subscriptions are purchased.', 'easytools-sub') . '</p>';
        echo '</div>';
    }

    public function email_content_settings_callback() {
        echo '<div class="easytools-info-box">';
        echo '<p>' . __('Customize the content of welcome emails sent to new users.', 'easytools-sub') . ' ';
        echo __('Available placeholders:', 'easytools-sub') . ' <code>{username}</code>, <code>{site_name}</code>, <code>{login_url}</code></p>';
        echo '</div>';
    }

    public function notification_settings_callback() {
        echo '<div class="easytools-info-box">';
        echo '<p>' . __('Manage email notifications for subscription events.', 'easytools-sub') . '</p>';
        echo '</div>';
    }

    /**
     * Callbacks for fields
     */
    public function checkout_url_callback() {
        $value = get_option('easytools_checkout_url', '');
        ?>
        <input type="url" name="easytools_checkout_url" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://easycart.pl/checkout/your-link">
        <p class="description"><?php _e('The Easytools checkout URL where users without subscription will be redirected. User email will be automatically appended to the URL.', 'easytools-sub'); ?></p>
        <?php
    }

    public function signing_key_callback() {
        $value = get_option('easytools_webhook_signing_key', '');
        $masked_value = $value ? str_repeat('*', 20) : '';
        ?>
        <div style="display: flex; gap: 10px; align-items: center; max-width: 600px;">
            <input type="text"
                   id="easytools_webhook_signing_key_display"
                   value="<?php echo esc_attr($masked_value); ?>"
                   class="regular-text"
                   readonly
                   style="flex: 1; background: #f0f0f1; cursor: not-allowed;">
            <input type="hidden"
                   name="easytools_webhook_signing_key"
                   id="easytools_webhook_signing_key_real"
                   value="<?php echo esc_attr($value); ?>">
            <button type="button"
                    class="button"
                    onclick="toggleSigningKeyVisibility()"
                    style="padding: 6px 12px; display: inline-flex; align-items: center; justify-content: center;">
                <span class="dashicons dashicons-visibility" id="signing-key-icon" style="margin: 0; width: 18px; height: 18px; font-size: 18px; line-height: 1;"></span>
            </button>
        </div>
        <p class="description"><?php _e('Generate this in Easytools → API & Webhooks. Used to verify webhook authenticity.', 'easytools-sub'); ?></p>
        <p><strong><?php _e('Webhook URL:', 'easytools-sub'); ?></strong> <code><?php echo esc_url(rest_url('easytools/v1/webhook')); ?></code></p>

        <script>
        function toggleSigningKeyVisibility() {
            var displayInput = document.getElementById('easytools_webhook_signing_key_display');
            var realInput = document.getElementById('easytools_webhook_signing_key_real');
            var icon = document.getElementById('signing-key-icon');

            if (displayInput.value.includes('*')) {
                // Show real value
                displayInput.value = realInput.value;
                displayInput.style.background = '#fff';
                displayInput.style.cursor = 'text';
                displayInput.removeAttribute('readonly');
                icon.classList.remove('dashicons-visibility');
                icon.classList.add('dashicons-hidden');

                // Update real input when editing
                displayInput.addEventListener('input', function() {
                    realInput.value = this.value;
                });
            } else {
                // Hide value
                displayInput.value = realInput.value ? '<?php echo str_repeat('*', 20); ?>' : '';
                displayInput.style.background = '#f0f0f1';
                displayInput.style.cursor = 'not-allowed';
                displayInput.setAttribute('readonly', 'readonly');
                icon.classList.remove('dashicons-hidden');
                icon.classList.add('dashicons-visibility');
            }
        }
        </script>
        <?php
    }

    public function api_token_callback() {
        $value = get_option('easytools_api_token', '');
        $api_token = $value ? $value : wp_generate_password(32, false);

        // Save generated token if empty
        if (empty($value)) {
            update_option('easytools_api_token', $api_token);
        }

        $webhook_url_with_token = rest_url('easytools/v1/webhook') . '?api_token=' . $api_token;
        ?>
        <div style="display: flex; gap: 10px; align-items: center; max-width: 600px;">
            <input type="password"
                   id="easytools_api_token_display"
                   value="<?php echo esc_attr($api_token); ?>"
                   class="regular-text"
                   readonly
                   style="flex: 1; background: #f0f0f1; cursor: not-allowed;">
            <input type="hidden"
                   name="easytools_api_token"
                   id="easytools_api_token"
                   value="<?php echo esc_attr($api_token); ?>">
            <button type="button"
                    class="button"
                    onclick="toggleApiTokenVisibility()"
                    style="padding: 6px 12px; display: inline-flex; align-items: center; justify-content: center;">
                <span class="dashicons dashicons-visibility" id="api-token-icon" style="margin: 0; width: 18px; height: 18px; font-size: 18px; line-height: 1;"></span>
            </button>
            <button type="button"
                    class="button"
                    onclick="regenerateApiToken()"
                    style="padding: 6px 12px; display: inline-flex; align-items: center; justify-content: center;">
                <span class="dashicons dashicons-update" style="margin: 0; width: 18px; height: 18px; font-size: 18px; line-height: 1;"></span>
            </button>
        </div>
        <p class="description"><?php _e('API token for additional webhook security. This token is automatically generated. Click the refresh button to generate a new token if needed.', 'easytools-sub'); ?></p>
        <div style="margin-top: 15px;">
            <strong><?php _e('Webhook URL with API Token:', 'easytools-sub'); ?></strong>
            <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                <code id="webhook-url-display" style="flex: 1; padding: 8px; background: #f0f0f1; border-radius: 4px; display: block;"><?php
                    $webhook_base = rest_url('easytools/v1/webhook');
                    echo esc_html($webhook_base . '?api_token=' . str_repeat('*', 16));
                ?></code>
                <input type="hidden" id="webhook-url-real" value="<?php echo esc_attr($webhook_url_with_token); ?>">
                <button type="button"
                        class="button button-small"
                        onclick="toggleWebhookUrlVisibility()">
                    👁️ <?php _e('Show', 'easytools-sub'); ?>
                </button>
                <button type="button"
                        class="button button-small"
                        onclick="copyWebhookUrl()">
                    📋 <?php _e('Copy URL', 'easytools-sub'); ?>
                </button>
            </div>
        </div>

        <script>
        function toggleApiTokenVisibility() {
            var displayInput = document.getElementById('easytools_api_token_display');
            var realInput = document.getElementById('easytools_api_token');
            var icon = document.getElementById('api-token-icon');

            if (displayInput.type === 'password') {
                // Show real value
                displayInput.type = 'text';
                displayInput.style.background = '#fff';
                icon.classList.remove('dashicons-visibility');
                icon.classList.add('dashicons-hidden');
            } else {
                // Hide value
                displayInput.type = 'password';
                displayInput.style.background = '#f0f0f1';
                icon.classList.remove('dashicons-hidden');
                icon.classList.add('dashicons-visibility');
            }
        }

        function toggleWebhookUrlVisibility() {
            var displayCode = document.getElementById('webhook-url-display');
            var realInput = document.getElementById('webhook-url-real');
            var button = event.target.closest('button');

            if (displayCode.textContent.includes('***')) {
                // Show real URL
                displayCode.textContent = realInput.value;
                button.textContent = '🙈 <?php echo esc_js(__('Hide', 'easytools-sub')); ?>';
            } else {
                // Hide token
                var webhookBase = '<?php echo esc_js(rest_url('easytools/v1/webhook')); ?>';
                displayCode.textContent = webhookBase + '?api_token=' + '****************';
                button.textContent = '👁️ <?php echo esc_js(__('Show', 'easytools-sub')); ?>';
            }
        }

        function regenerateApiToken() {
            if (!confirm('<?php echo esc_js(__('Generate a new API token? You will need to update the webhook URL in Easytools.', 'easytools-sub')); ?>')) {
                return;
            }

            // Generate new token
            var newToken = '';
            var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            for (var i = 0; i < 32; i++) {
                newToken += chars.charAt(Math.floor(Math.random() * chars.length));
            }

            // Update both inputs
            document.getElementById('easytools_api_token').value = newToken;
            document.getElementById('easytools_api_token_display').value = newToken;

            // Update webhook URL
            var webhookBase = '<?php echo esc_js(rest_url('easytools/v1/webhook')); ?>';
            var newWebhookUrl = webhookBase + '?api_token=' + newToken;
            document.getElementById('webhook-url-real').value = newWebhookUrl;

            // Update display (masked)
            document.getElementById('webhook-url-display').textContent = webhookBase + '?api_token=' + '****************';

            alert('<?php echo esc_js(__('New API token generated! Remember to save settings.', 'easytools-sub')); ?>');
        }

        function copyWebhookUrl() {
            var url = document.getElementById('webhook-url-real').value;
            navigator.clipboard.writeText(url).then(function() {
                alert('✅ <?php echo esc_js(__('Webhook URL copied to clipboard!', 'easytools-sub')); ?>');
            }).catch(function(err) {
                alert('❌ <?php echo esc_js(__('Failed to copy to clipboard', 'easytools-sub')); ?>');
            });
        }
        </script>
        <?php
    }

    public function protect_all_callback() {
        $value = get_option('easytools_protect_all', 'no');
        ?>
        <label>
            <input type="checkbox" name="easytools_protect_all" value="yes" <?php checked($value, 'yes'); ?>>
            <?php _e('Protect all pages except those selected below', 'easytools-sub'); ?>
        </label>
        <p class="description"><?php _e('If checked, the entire site will be protected except for pages selected in "Exclude Pages".', 'easytools-sub'); ?></p>
        <?php
    }

    public function exclude_pages_callback() {
        $selected = get_option('easytools_exclude_pages', array());
        $pages = get_pages();
        ?>
        <select name="easytools_exclude_pages[]" multiple class="easytools-page-select" style="width: 100%; max-width: 500px; height: 200px;">
            <?php foreach ($pages as $page) : ?>
                <option value="<?php echo esc_attr($page->ID); ?>" <?php echo in_array($page->ID, $selected) ? 'selected' : ''; ?>>
                    <?php echo esc_html($page->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Pages that will be accessible without subscription (e.g., homepage, login, registration).', 'easytools-sub'); ?></p>
        <p class="description"><em><?php _e('Hold Ctrl (Cmd on Mac) to select multiple pages.', 'easytools-sub'); ?></em></p>
        <?php
    }

    public function protected_pages_callback() {
        $selected = get_option('easytools_protected_pages', array());
        $pages = get_pages();
        ?>
        <select name="easytools_protected_pages[]" multiple class="easytools-page-select" style="width: 100%; max-width: 500px; height: 200px;">
            <?php foreach ($pages as $page) : ?>
                <option value="<?php echo esc_attr($page->ID); ?>" <?php echo in_array($page->ID, $selected) ? 'selected' : ''; ?>>
                    <?php echo esc_html($page->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Pages that require an active subscription to access.', 'easytools-sub'); ?></p>
        <p class="description"><em><?php _e('Hold Ctrl (Cmd on Mac) to select multiple pages.', 'easytools-sub'); ?></em></p>
        <?php
    }

    public function enable_bouncer_callback() {
        $enabled = get_option('easytools_enable_bouncer', 'no');
        ?>
        <label>
            <input type="checkbox" name="easytools_enable_bouncer" value="yes" <?php checked($enabled, 'yes'); ?>>
            <?php _e('Enable custom bouncer page for non-subscribers', 'easytools-sub'); ?>
        </label>
        <p class="description"><?php _e('When enabled, non-subscribers will see your custom bouncer page instead of being redirected to checkout.', 'easytools-sub'); ?></p>
        <?php
    }

    public function bouncer_page_callback() {
        $selected = get_option('easytools_bouncer_page', '');
        $pages = get_pages();
        ?>
        <select name="easytools_bouncer_page" class="regular-text">
            <option value=""><?php _e('— Select Page —', 'easytools-sub'); ?></option>
            <?php foreach ($pages as $page) : ?>
                <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($selected, $page->ID); ?>>
                    <?php echo esc_html($page->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Select an existing page or create a new one from template below.', 'easytools-sub'); ?></p>

        <div style="margin-top: 15px;">
            <button type="button" class="button" id="create-bouncer-page">
                <span class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></span>
                <?php _e('Create New Bouncer Page from Template', 'easytools-sub'); ?>
            </button>
            <button type="button" class="button" id="copy-bouncer-html">
                <span class="dashicons dashicons-clipboard" style="vertical-align: middle;"></span>
                <?php _e('Copy Bouncer HTML Template', 'easytools-sub'); ?>
            </button>
        </div>

        <div id="bouncer-preview" style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px; display: none;">
            <h4><?php _e('Bouncer Template Preview:', 'easytools-sub'); ?></h4>
            <div id="bouncer-preview-content"></div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Helper function to get current color values
            function getCurrentColors() {
                return {
                    product_url: $('input[name="easytools_bouncer_product_url"]').val(),
                    icon_color: $('input[name="easytools_bouncer_icon_color"]').val(),
                    button_color: $('input[name="easytools_bouncer_button_color"]').val(),
                    bg_color: $('input[name="easytools_bouncer_bg_color"]').val()
                };
            }

            // Create new bouncer page
            $('#create-bouncer-page').on('click', function() {
                var button = $(this);
                var originalHtml = button.html();
                var colors = getCurrentColors();

                // Validate product URL is not empty
                if (!colors.product_url || colors.product_url.trim() === '') {
                    alert('<?php _e('Please enter a Product URL before creating the bouncer page. This is where the "Start Subscription" button will link to.', 'easytools-sub'); ?>');
                    // Scroll to and highlight the product URL field
                    $('input[name="easytools_bouncer_product_url"]').focus().css('border-color', '#dc3232');
                    setTimeout(function() {
                        $('input[name="easytools_bouncer_product_url"]').css('border-color', '');
                    }, 3000);
                    return;
                }

                button.prop('disabled', true).html('<span class="dashicons dashicons-update" style="vertical-align: middle; animation: rotation 2s infinite linear;"></span> <?php _e('Creating...', 'easytools-sub'); ?>');

                $.post(ajaxurl, {
                    action: 'easytools_create_bouncer_page',
                    nonce: '<?php echo wp_create_nonce('easytools_create_bouncer'); ?>',
                    product_url: colors.product_url,
                    icon_color: colors.icon_color,
                    button_color: colors.button_color,
                    bg_color: colors.bg_color
                }, function(response) {
                    if (response.success) {
                        // Show success message
                        var successMsg = $('<div style="background: #46b450; color: white; padding: 12px 15px; border-radius: 4px; margin-top: 15px; display: flex; align-items: center; gap: 10px;"><span class="dashicons dashicons-yes" style="font-size: 20px;"></span><span>' + response.data.message + '</span></div>');
                        button.after(successMsg);

                        // Reload page after 1.5 seconds to show the new page in dropdown
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert(response.data.message);
                        button.prop('disabled', false).html(originalHtml);
                    }
                }).fail(function() {
                    alert('<?php _e('Error creating bouncer page. Please try again.', 'easytools-sub'); ?>');
                    button.prop('disabled', false).html(originalHtml);
                });
            });

            // Copy HTML template
            $('#copy-bouncer-html').on('click', function() {
                var colors = getCurrentColors();

                // Validate product URL is not empty
                if (!colors.product_url || colors.product_url.trim() === '') {
                    alert('<?php _e('Please enter a Product URL before copying the HTML template. This is where the "Start Subscription" button will link to.', 'easytools-sub'); ?>');
                    // Scroll to and highlight the product URL field
                    $('input[name="easytools_bouncer_product_url"]').focus().css('border-color', '#dc3232');
                    setTimeout(function() {
                        $('input[name="easytools_bouncer_product_url"]').css('border-color', '');
                    }, 3000);
                    return;
                }

                $.post(ajaxurl, {
                    action: 'easytools_get_bouncer_html',
                    nonce: '<?php echo wp_create_nonce('easytools_get_bouncer_html'); ?>',
                    product_url: colors.product_url,
                    icon_color: colors.icon_color,
                    button_color: colors.button_color,
                    bg_color: colors.bg_color
                }, function(response) {
                    if (response.success) {
                        // Create temporary textarea
                        var $temp = $('<textarea>');
                        $('body').append($temp);
                        $temp.val(response.data.html).select();
                        document.execCommand('copy');
                        $temp.remove();

                        // Show preview
                        $('#bouncer-preview').fadeIn();
                        $('#bouncer-preview-content').html('<pre style="overflow-x: auto; background: white; padding: 10px; border-radius: 4px; max-height: 300px;">' + $('<div>').text(response.data.html).html() + '</pre>');

                        // Show success message
                        var successMsg = $('<div style="background: #46b450; color: white; padding: 10px 15px; border-radius: 4px; margin-top: 10px;"><span class="dashicons dashicons-yes" style="vertical-align: middle;"></span> <?php _e('Bouncer HTML copied to clipboard!', 'easytools-sub'); ?></div>');
                        $(this).after(successMsg);
                        setTimeout(function() {
                            successMsg.fadeOut(300, function() { $(this).remove(); });
                        }, 3000);
                    }
                }.bind(this));
            });
        });
        </script>
        <style>
        @keyframes rotation {
            from { transform: rotate(0deg); }
            to { transform: rotate(359deg); }
        }
        </style>
        <?php
    }

    public function bouncer_product_url_callback() {
        $checkout_url = get_option('easytools_checkout_url', '');
        $bouncer_url = get_option('easytools_bouncer_product_url', '');

        // If bouncer URL is empty, use checkout URL as default
        $value = !empty($bouncer_url) ? $bouncer_url : $checkout_url;
        ?>
        <input type="url"
               name="easytools_bouncer_product_url"
               id="easytools_bouncer_product_url"
               value="<?php echo esc_url($value); ?>"
               class="regular-text"
               placeholder="https://easl.ink/yourproduct"
               required>
        <p class="description">
            <strong style="color: #dc3232;">* <?php _e('Required', 'easytools-sub'); ?></strong> -
            <?php _e('The Easytools product URL that appears on the bouncer page "Start Subscription" button.', 'easytools-sub'); ?>
            <br>
            <em style="color: #2271b1;">
                💡 <?php _e('Tip: This will automatically sync with the Checkout URL above unless you manually change it.', 'easytools-sub'); ?>
            </em>
        </p>

        <script>
        jQuery(document).ready(function($) {
            var $checkoutUrl = $('input[name="easytools_checkout_url"]');
            var $bouncerUrl = $('#easytools_bouncer_product_url');
            var userModified = false;

            // Track if user manually modifies the bouncer URL
            $bouncerUrl.on('input', function() {
                userModified = true;
            });

            // Auto-sync checkout URL to bouncer URL (only if user hasn't manually changed it)
            $checkoutUrl.on('input change', function() {
                if (!userModified) {
                    var newUrl = $(this).val().trim();
                    if (newUrl) {
                        $bouncerUrl.val(newUrl);
                        // Flash the field to show it updated
                        $bouncerUrl.css('background-color', '#d4edda');
                        setTimeout(function() {
                            $bouncerUrl.css('background-color', '');
                        }, 500);
                    }
                }
            });
        });
        </script>
        <?php
    }

    public function bouncer_icon_color_callback() {
        $value = get_option('easytools_bouncer_icon_color', '#71efab');
        ?>
        <input type="text" name="easytools_bouncer_icon_color" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#71efab">
        <p class="description"><?php _e('Color of the lock icon on the bouncer page.', 'easytools-sub'); ?></p>
        <?php
    }

    public function bouncer_button_color_callback() {
        $value = get_option('easytools_bouncer_button_color', '#71efab');
        ?>
        <input type="text" name="easytools_bouncer_button_color" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#71efab">
        <p class="description"><?php _e('Color of the primary button on the bouncer page.', 'easytools-sub'); ?></p>
        <?php
    }

    public function bouncer_bg_color_callback() {
        $value = get_option('easytools_bouncer_bg_color', '#172532');
        ?>
        <input type="text" name="easytools_bouncer_bg_color" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#172532">
        <p class="description"><?php _e('Background color of the bouncer page container.', 'easytools-sub'); ?></p>
        <?php
    }

    public function account_creation_mode_callback() {
        $mode = get_option('easytools_account_creation_mode', 'webhook_only');
        ?>
        <fieldset>
            <label style="display: block; margin-bottom: 15px;">
                <input type="radio" name="easytools_account_creation_mode" value="webhook_only" <?php checked($mode, 'webhook_only'); ?>>
                <strong><?php _e('Webhook Only (Instant)', 'easytools-sub'); ?></strong>
                <p class="description" style="margin-left: 25px;">
                    <?php _e('Not using Easytools Automation. Accounts are created instantly by webhook when subscription is purchased. ⚡ Fastest option.', 'easytools-sub'); ?>
                </p>
            </label>

            <label style="display: block; margin-bottom: 15px;">
                <input type="radio" name="easytools_account_creation_mode" value="automation_with_fallback" <?php checked($mode, 'automation_with_fallback'); ?>>
                <strong><?php _e('Easytools Automation with Webhook Fallback', 'easytools-sub'); ?></strong>
                <p class="description" style="margin-left: 25px;">
                    <?php _e('Using Easytools Automation for account creation. Webhook will wait 5 minutes for automation, then create account if automation hasn\'t completed. ✅ Recommended if using automation.', 'easytools-sub'); ?>
                </p>
            </label>

            <label style="display: block; margin-bottom: 15px;">
                <input type="radio" name="easytools_account_creation_mode" value="automation_only" <?php checked($mode, 'automation_only'); ?>>
                <strong><?php _e('Easytools Automation Only (No Fallback)', 'easytools-sub'); ?></strong>
                <p class="description" style="margin-left: 25px;">
                    <?php _e('Trust Easytools Automation completely. Webhook will NOT create accounts at all. ⚠️ Use only if you\'re confident automation always works.', 'easytools-sub'); ?>
                </p>
            </label>
        </fieldset>

        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-top: 15px; border-radius: 4px;">
            <p style="margin: 0; font-size: 14px;">
                <strong>💡 <?php _e('Tip:', 'easytools-sub'); ?></strong>
                <?php _e('If you\'re using Easytools Automation to create accounts, choose "Automation with Fallback" for maximum reliability. The 5-minute wait ensures automation has time to complete.', 'easytools-sub'); ?>
            </p>
        </div>
        <?php
    }

    public function default_role_callback() {
        $selected = get_option('easytools_default_role', 'subscriber');
        $roles = wp_roles()->get_names();
        ?>
        <select name="easytools_default_role">
            <?php foreach ($roles as $role_slug => $role_name) : ?>
                <option value="<?php echo esc_attr($role_slug); ?>" <?php selected($selected, $role_slug); ?>>
                    <?php echo esc_html($role_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Role assigned to new users created by webhook.', 'easytools-sub'); ?></p>
        <?php
    }

    public function welcome_email_callback() {
        $value = get_option('easytools_send_welcome_email', 'yes');
        ?>
        <label>
            <input type="checkbox" name="easytools_send_welcome_email" value="yes" <?php checked($value, 'yes'); ?>>
            <?php _e('Send welcome email with login credentials', 'easytools-sub'); ?>
        </label>
        <?php
    }

    public function email_from_address_callback() {
        $value = get_option('easytools_email_from_address', '');
        $default = 'wordpress@' . str_replace('www.', '', $_SERVER['SERVER_NAME']);
        ?>
        <input type="email" name="easytools_email_from_address" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="<?php echo esc_attr($default); ?>">
        <p class="description">
            <?php _e('Custom "From" email address for welcome emails. Leave empty to use default WordPress address.', 'easytools-sub'); ?>
            <br><strong><?php _e('Default:', 'easytools-sub'); ?></strong> <?php echo esc_html($default); ?>
        </p>
        <?php
    }

    public function email_from_name_callback() {
        $value = get_option('easytools_email_from_name', '');
        $default = get_bloginfo('name');
        ?>
        <input type="text" name="easytools_email_from_name" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="<?php echo esc_attr($default); ?>">
        <p class="description">
            <?php _e('Custom "From" name for welcome emails. Leave empty to use site name.', 'easytools-sub'); ?>
            <br><strong><?php _e('Default:', 'easytools-sub'); ?></strong> <?php echo esc_html($default); ?>
        </p>
        <?php
    }

    public function email_brand_color_callback() {
        $value = get_option('easytools_email_brand_color', '#05c7aa');
        ?>
        <input type="color" name="easytools_email_brand_color" value="<?php echo esc_attr($value); ?>" class="color-picker">
        <input type="text" value="<?php echo esc_attr($value); ?>" readonly class="regular-text" style="width: 100px; margin-left: 10px;">
        <p class="description"><?php _e('Brand color for email buttons and accents. Used in HTML welcome emails.', 'easytools-sub'); ?></p>
        <script>
        jQuery(document).ready(function($) {
            $('input[name="easytools_email_brand_color"]').on('change', function() {
                $(this).next('input').val($(this).val());
            });
        });
        </script>
        <?php
    }

    public function email_subject_callback() {
        $default = '[{site_name}] Welcome! Set Your Password';
        $value = get_option('easytools_email_subject', $default);
        ?>
        <input type="text" name="easytools_email_subject" value="<?php echo esc_attr($value); ?>" class="large-text">
        <p class="description">
            <?php _e('Subject line for welcome email. Use placeholders: {username}, {site_name}, {login_url}', 'easytools-sub'); ?>
        </p>
        <?php
    }

    public function email_heading_callback() {
        $default = '🎉 Welcome to {site_name}!';
        $value = get_option('easytools_email_heading', $default);
        ?>
        <input type="text" name="easytools_email_heading" value="<?php echo esc_attr($value); ?>" class="large-text">
        <p class="description">
            <?php _e('Main heading displayed in the email. Use placeholders: {username}, {site_name}, {login_url}', 'easytools-sub'); ?>
        </p>
        <?php
    }

    public function email_message_callback() {
        $default = "Your account has been successfully created. We're excited to have you on board!\n\nTo complete your account setup and access your dashboard, please set your password by clicking the button below.";
        $value = get_option('easytools_email_message', $default);
        ?>
        <textarea name="easytools_email_message" rows="6" class="large-text" style="font-family: monospace;"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">
            <?php _e('Main message content. Press Enter for new paragraphs. Use placeholders: {username}, {site_name}, {login_url}', 'easytools-sub'); ?>
        </p>
        <?php
    }

    public function email_button_text_callback() {
        $default = 'Set Your Password';
        $value = get_option('easytools_email_button_text', $default);
        ?>
        <input type="text" name="easytools_email_button_text" value="<?php echo esc_attr($value); ?>" class="regular-text">
        <p class="description">
            <?php _e('Text displayed on the password setup button.', 'easytools-sub'); ?>
        </p>
        <?php
    }

    public function test_email_button_callback() {
        $current_user = wp_get_current_user();
        ?>
        <div style="background: rgba(5, 199, 170, 0.1); border-left: 4px solid #05c7aa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
            <p style="margin: 0 0 10px 0; font-weight: 600; color: #0e534c;">
                <?php _e('Test your email configuration', 'easytools-sub'); ?>
            </p>
            <p style="margin: 0 0 15px 0; font-size: 13px; color: #666;">
                <?php _e('Send a test welcome email to verify your email settings, sender information, and template customization are working correctly.', 'easytools-sub'); ?>
            </p>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="email"
                       id="test-email-settings-input"
                       placeholder="your.email@example.com"
                       style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;"
                       value="<?php echo esc_attr($current_user->user_email); ?>">
                <button type="button" class="button button-secondary" onclick="sendTestEmailFromSettings()" style="white-space: nowrap;">
                    📧 <?php _e('Send Test Email', 'easytools-sub'); ?>
                </button>
            </div>
        </div>
        <script>
        function sendTestEmailFromSettings() {
            const emailInput = document.getElementById('test-email-settings-input');
            const email = emailInput.value.trim();

            if (!email) {
                alert('⚠️ <?php echo esc_js(__('Please enter an email address', 'easytools-sub')); ?>');
                return;
            }

            if (!email.includes('@')) {
                alert('⚠️ <?php echo esc_js(__('Please enter a valid email address', 'easytools-sub')); ?>');
                return;
            }

            if (!confirm('📧 <?php echo esc_js(__('Send test email to', 'easytools-sub')); ?> ' + email + '?')) {
                return;
            }

            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'easytools_send_test_email',
                    test_email: email,
                    nonce: '<?php echo wp_create_nonce('easytools_tester'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ ' + response.data.message);
                    } else {
                        alert('❌ ' + response.data.message);
                    }
                },
                error: function() {
                    alert('❌ <?php echo esc_js(__('Error sending test email', 'easytools-sub')); ?>');
                }
            });
        }
        </script>
        <?php
    }

    public function admin_notifications_callback() {
        $value = get_option('easytools_admin_notifications', 'yes');
        ?>
        <label>
            <input type="checkbox" name="easytools_admin_notifications" value="yes" <?php checked($value, 'yes'); ?>>
            <?php _e('Send notifications about new/expired subscriptions', 'easytools-sub'); ?>
        </label>
        <?php
    }

    public function admin_email_callback() {
        $value = get_option('easytools_admin_email', get_option('admin_email'));
        ?>
        <input type="email" name="easytools_admin_email" value="<?php echo esc_attr($value); ?>" class="regular-text">
        <?php
    }

    /**
     * Sanitize array of page IDs
     */
    public function sanitize_page_array($input) {
        if (!is_array($input)) {
            return array();
        }
        return array_map('intval', $input);
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="easytools-premium-wrap">
            <div class="easytools-premium-header">
                <h1><?php _e('Easytools Subscription Manager', 'easytools-sub'); ?></h1>
                <p><?php _e('Manage your subscription settings and protect your content with ease', 'easytools-sub'); ?></p>
            </div>

            <?php settings_errors(); ?>

            <div class="easytools-settings-grid">
                <div>
                    <div class="easytools-premium-card">
                        <form method="post" action="options.php">
                            <?php
                            settings_fields('easytools_settings');
                            do_settings_sections('easytools-subscription');
                            submit_button(__('Save Settings', 'easytools-sub'));
                            ?>
                        </form>
                    </div>
                </div>

                <div>
                    <div class="easytools-sidebar-card">
                        <h3><?php _e('Setup Instructions', 'easytools-sub'); ?></h3>
                        <ol>
                            <li><?php _e('Generate Webhook Signing Key in Easytools', 'easytools-sub'); ?></li>
                            <li><?php _e('Add webhook URL in Easytools → API & Webhooks', 'easytools-sub'); ?></li>
                            <li><?php _e('Configure checkout URL', 'easytools-sub'); ?></li>
                            <li><?php _e('Select protected pages', 'easytools-sub'); ?></li>
                            <li><?php _e('Optional: Configure Automation in Easytools', 'easytools-sub'); ?></li>
                        </ol>

                        <h3 style="margin-top: 25px;"><?php _e('Need Help?', 'easytools-sub'); ?></h3>
                        <p>
                            <?php
                            $locale = get_locale();
                            $docs_url = (strpos($locale, 'pl') === 0) ? 'https://www.easy.tools/pl/docs/odkrywaj' : 'https://www.easy.tools/docs/explore';
                            ?>
                            <a href="<?php echo esc_url($docs_url); ?>" target="_blank"><?php _e('Easytools Documentation', 'easytools-sub'); ?></a><br>
                            <a href="mailto:kontakt.rapacz@gmail.com"><?php _e('Contact Support', 'easytools-sub'); ?></a>
                        </p>
                    </div>

                    <div class="easytools-sidebar-card">
                        <h3><?php _e('Active Subscriptions', 'easytools-sub'); ?></h3>
                        <?php
                        $active_count = $this->get_active_subscriptions_count();
                        echo '<div class="easytools-stat-number">' . esc_html($active_count) . '</div>';
                        ?>
                        <p style="text-align: center;"><a href="<?php echo admin_url('admin.php?page=easytools-subscribers'); ?>"><?php _e('View All Subscribers', 'easytools-sub'); ?></a></p>
                    </div>
                </div>
            </div>

            <!-- Author Footer -->
            <div style="margin-top: 40px; padding: 20px; text-align: center; background: rgba(255, 255, 255, 0.6); border-radius: 12px; border-top: 3px solid #05c7aa;">
                <p style="margin: 0; color: #666; font-size: 14px;">
                    <?php _e('Plugin created by', 'easytools-sub'); ?>
                    <strong style="color: #05c7aa;">Chris Rapacz</strong>
                </p>
                <p style="margin: 8px 0 0 0;">
                    <a href="https://www.linkedin.com/in/krzysztofrapacz/" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; color: #0077b5; text-decoration: none; font-weight: 500; font-size: 14px;">
                        <svg style="width: 18px; height: 18px; fill: #0077b5;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                        <?php _e('Connect on LinkedIn', 'easytools-sub'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Render logs page
     */
    public function render_logs_page() {
        ?>
        <div class="easytools-premium-wrap">
            <div class="easytools-premium-header">
                <h1><?php _e('Easytools Webhook Logs', 'easytools-sub'); ?></h1>
                <p><?php _e('Monitor and debug webhook communications from Easytools', 'easytools-sub'); ?></p>
            </div>

            <div class="easytools-premium-card">
                <!-- Export Controls -->
                <div style="display: flex; gap: 15px; align-items: flex-end; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid #f0f0f1;">
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;"><?php _e('Date Range Filter:', 'easytools-sub'); ?></label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="date" id="filter-date-from" style="padding: 8px 12px; border: 2px solid #e1e8ed; border-radius: 8px;">
                            <span><?php _e('to', 'easytools-sub'); ?></span>
                            <input type="date" id="filter-date-to" style="padding: 8px 12px; border: 2px solid #e1e8ed; border-radius: 8px;">
                            <button type="button" class="button" onclick="filterLogs()" style="padding: 8px 16px;">
                                🔍 <?php _e('Filter', 'easytools-sub'); ?>
                            </button>
                            <button type="button" class="button" onclick="clearFilter()" style="padding: 8px 16px;">
                                ✖️ <?php _e('Clear', 'easytools-sub'); ?>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;"><?php _e('Export:', 'easytools-sub'); ?></label>
                        <div style="display: flex; gap: 10px;">
                            <button type="button" class="button button-primary" onclick="exportLogs('csv')" style="background: #05c7aa; border: none; padding: 8px 16px; color: white; border-radius: 6px; font-weight: 600;">
                                📊 <?php _e('Export to CSV', 'easytools-sub'); ?>
                            </button>
                            <button type="button" class="button button-primary" onclick="exportLogs('md')" style="background: #05c7aa; border: none; padding: 8px 16px; color: white; border-radius: 6px; font-weight: 600;">
                                📝 <?php _e('Export to MD', 'easytools-sub'); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'easytools_webhook_logs';

                // Date range filter
                $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
                $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';

                // Pagination
                $per_page = 20;
                $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
                $offset = ($page - 1) * $per_page;

                // Build WHERE clause for date filter
                $where_clause = '';
                $query_params = array();

                if (!empty($date_from) && !empty($date_to)) {
                    $where_clause = "WHERE DATE(created_at) BETWEEN %s AND %s";
                    $query_params[] = $date_from;
                    $query_params[] = $date_to;
                } elseif (!empty($date_from)) {
                    $where_clause = "WHERE DATE(created_at) >= %s";
                    $query_params[] = $date_from;
                } elseif (!empty($date_to)) {
                    $where_clause = "WHERE DATE(created_at) <= %s";
                    $query_params[] = $date_to;
                }

                // Get total count
                if (!empty($query_params)) {
                    $total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name $where_clause", $query_params));
                } else {
                    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                }

                // Get logs with pagination
                $query_params[] = $per_page;
                $query_params[] = $offset;

                if (!empty($where_clause)) {
                    $logs = $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d",
                        $query_params
                    ));
                } else {
                    $logs = $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
                        $per_page,
                        $offset
                    ));
                }

                $total_pages = ceil($total / $per_page);
                ?>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'easytools-sub'); ?></th>
                            <th><?php _e('Event Type', 'easytools-sub'); ?></th>
                            <th><?php _e('Customer Email', 'easytools-sub'); ?></th>
                            <th><?php _e('User ID', 'easytools-sub'); ?></th>
                            <th><?php _e('Status', 'easytools-sub'); ?></th>
                            <th><?php _e('Actions', 'easytools-sub'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)) : ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px;">
                                    <?php _e('No webhook logs found.', 'easytools-sub'); ?>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($logs as $log) : ?>
                                <tr>
                                    <td><?php echo esc_html($log->created_at); ?></td>
                                    <td><code><?php echo esc_html($log->event_type); ?></code></td>
                                    <td><?php echo esc_html($log->customer_email); ?></td>
                                    <td>
                                        <?php
                                        if ($log->user_id) {
                                            echo '<a href="' . get_edit_user_link($log->user_id) . '">' . esc_html($log->user_id) . '</a>';
                                        } else {
                                            echo '—';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($log->status === 'success') {
                                            echo '<span class="easytools-status-active"><span class="easytools-status-dot active"></span>' . esc_html($log->status) . '</span>';
                                        } else {
                                            echo '<span class="easytools-status-inactive"><span class="easytools-status-dot inactive"></span>' . esc_html($log->status) . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button type="button" class="button button-small view-log-details" data-log-id="<?php echo esc_attr($log->id); ?>">
                                            👁️ <?php _e('Details', 'easytools-sub'); ?>
                                        </button>
                                        <button type="button" class="button button-small delete-log" data-log-id="<?php echo esc_attr($log->id); ?>" style="color: #d63638; border-color: #d63638; margin-left: 5px;">
                                            🗑️ <?php _e('Delete', 'easytools-sub'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1) : ?>
                    <div class="tablenav bottom">
                        <div class="tablenav-pages">
                            <?php
                            echo paginate_links(array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'prev_text' => __('&laquo;'),
                                'next_text' => __('&raquo;'),
                                'total' => $total_pages,
                                'current' => $page
                            ));
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Modal for details -->
            <div id="log-details-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 100000;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 16px; max-width: 800px; max-height: 80vh; overflow: auto;">
                    <h2><?php _e('Webhook Details', 'easytools-sub'); ?></h2>
                    <div id="log-details-content"></div>
                    <button type="button" class="button" onclick="document.getElementById('log-details-modal').style.display='none'">
                        <?php _e('Close', 'easytools-sub'); ?>
                    </button>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Set current date filter values from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const dateFrom = urlParams.get('date_from');
            const dateTo = urlParams.get('date_to');

            if (dateFrom) {
                $('#filter-date-from').val(dateFrom);
            }
            if (dateTo) {
                $('#filter-date-to').val(dateTo);
            }

            $('.view-log-details').on('click', function() {
                var logId = $(this).data('log-id');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_webhook_log_details',
                        log_id: logId,
                        nonce: '<?php echo wp_create_nonce('easytools_log_details'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#log-details-content').html(response.data.html);
                            $('#log-details-modal').show();
                        }
                    }
                });
            });

            $('.delete-log').on('click', function() {
                var logId = $(this).data('log-id');
                var $button = $(this);
                var $row = $button.closest('tr');

                if (!confirm('<?php echo esc_js(__('Are you sure you want to delete this log? This action cannot be undone.', 'easytools-sub')); ?>')) {
                    return;
                }

                $button.prop('disabled', true).text('<?php echo esc_js(__('Deleting...', 'easytools-sub')); ?>');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'delete_webhook_log',
                        log_id: logId,
                        nonce: '<?php echo wp_create_nonce('easytools_delete_log'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                                // Check if table is now empty
                                if ($('.wp-list-table tbody tr').length === 0) {
                                    $('.wp-list-table tbody').html('<tr><td colspan="6" style="text-align: center; padding: 30px;"><?php echo esc_js(__('No webhook logs found.', 'easytools-sub')); ?></td></tr>');
                                }
                            });
                        } else {
                            alert('<?php echo esc_js(__('Error:', 'easytools-sub')); ?> ' + response.data.message);
                            $button.prop('disabled', false).html('🗑️ <?php echo esc_js(__('Delete', 'easytools-sub')); ?>');
                        }
                    },
                    error: function() {
                        alert('<?php echo esc_js(__('An error occurred while deleting the log.', 'easytools-sub')); ?>');
                        $button.prop('disabled', false).html('🗑️ <?php echo esc_js(__('Delete', 'easytools-sub')); ?>');
                    }
                });
            });
        });

        function filterLogs() {
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;

            const currentUrl = new URL(window.location.href);

            if (dateFrom) {
                currentUrl.searchParams.set('date_from', dateFrom);
            } else {
                currentUrl.searchParams.delete('date_from');
            }

            if (dateTo) {
                currentUrl.searchParams.set('date_to', dateTo);
            } else {
                currentUrl.searchParams.delete('date_to');
            }

            // Reset to first page when filtering
            currentUrl.searchParams.delete('paged');

            window.location.href = currentUrl.toString();
        }

        function clearFilter() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('date_from');
            currentUrl.searchParams.delete('date_to');
            currentUrl.searchParams.delete('paged');
            window.location.href = currentUrl.toString();
        }

        function exportLogs(format) {
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;

            const data = {
                action: 'easytools_export_logs',
                format: format,
                date_from: dateFrom,
                date_to: dateTo,
                nonce: '<?php echo wp_create_nonce('easytools_export_logs'); ?>'
            };

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = ajaxurl;

            for (const key in data) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = data[key];
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
        </script>
        <?php
    }

    /**
     * Render subscribers page
     */
    public function render_subscribers_page() {
        // Get active subscribers
        $active_args = array(
            'meta_query' => array(
                array(
                    'key' => 'subscribed',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );
        $active_query = new WP_User_Query($active_args);
        $active_users = $active_query->get_results();

        // Get all users who ever had subscription metadata
        $all_users_args = array(
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'subscription_type',
                    'compare' => 'EXISTS'
                ),
                array(
                    'key' => 'subscribed',
                    'compare' => 'EXISTS'
                )
            )
        );
        $all_users_query = new WP_User_Query($all_users_args);
        $all_users = $all_users_query->get_results();

        // Filter inactive users (those who are not in active list)
        $active_ids = array_map(function($user) { return $user->ID; }, $active_users);
        $inactive_users = array_filter($all_users, function($user) use ($active_ids) {
            return !in_array($user->ID, $active_ids);
        });
        ?>
        <div class="easytools-premium-wrap">
            <div class="easytools-premium-header">
                <h1><?php _e('Subscription Members', 'easytools-sub'); ?></h1>
                <p><?php _e('View and manage all users with active and inactive subscriptions', 'easytools-sub'); ?></p>
            </div>

            <!-- Active Subscriptions -->
            <div class="easytools-premium-card">
                <h2 style="margin-top: 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f1; color: #2c3e50;">
                    ✅ <?php _e('Active Subscriptions', 'easytools-sub'); ?> (<?php echo count($active_users); ?>)
                </h2>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('User', 'easytools-sub'); ?></th>
                            <th><?php _e('Email', 'easytools-sub'); ?></th>
                            <th><?php _e('Subscription Type', 'easytools-sub'); ?></th>
                            <th><?php _e('Renewal Date', 'easytools-sub'); ?></th>
                            <th><?php _e('Status', 'easytools-sub'); ?></th>
                            <th><?php _e('Actions', 'easytools-sub'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($active_users)) : ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px;">
                                    <?php _e('No active subscriptions found.', 'easytools-sub'); ?>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($active_users as $user) : ?>
                                <tr id="user-row-<?php echo $user->ID; ?>">
                                    <td>
                                        <a href="<?php echo get_edit_user_link($user->ID); ?>">
                                            <?php echo esc_html($user->display_name); ?>
                                        </a>
                                    </td>
                                    <td><?php echo esc_html($user->user_email); ?></td>
                                    <td>
                                        <?php
                                        $sub_type = get_user_meta($user->ID, 'subscription_type', true);
                                        echo esc_html($sub_type ?: '—');
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $renewal = get_user_meta($user->ID, 'renewal_date', true);
                                        echo esc_html($renewal ?: '—');
                                        ?>
                                    </td>
                                    <td>
                                        <span class="easytools-status-active">
                                            <span class="easytools-status-dot active"></span>
                                            <?php _e('Active', 'easytools-sub'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="button button-small" onclick="toggleUserAccess(<?php echo $user->ID; ?>, 'deactivate')">
                                            ❌ <?php _e('Deactivate', 'easytools-sub'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Inactive Subscriptions -->
            <div class="easytools-premium-card" style="margin-top: 25px;">
                <h2 style="margin-top: 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f1; color: #2c3e50;">
                    ❌ <?php _e('Inactive Accounts', 'easytools-sub'); ?> (<?php echo count($inactive_users); ?>)
                </h2>
                <p class="description" style="margin-bottom: 20px;">
                    <?php _e('Users who previously had a subscription that was cancelled, expired, or is no longer active.', 'easytools-sub'); ?>
                </p>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('User', 'easytools-sub'); ?></th>
                            <th><?php _e('Email', 'easytools-sub'); ?></th>
                            <th><?php _e('Last Subscription Type', 'easytools-sub'); ?></th>
                            <th><?php _e('Last Renewal Date', 'easytools-sub'); ?></th>
                            <th><?php _e('Status', 'easytools-sub'); ?></th>
                            <th><?php _e('Actions', 'easytools-sub'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inactive_users)) : ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px;">
                                    <?php _e('No inactive accounts found.', 'easytools-sub'); ?>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($inactive_users as $user) : ?>
                                <tr id="user-row-<?php echo $user->ID; ?>">
                                    <td>
                                        <a href="<?php echo get_edit_user_link($user->ID); ?>">
                                            <?php echo esc_html($user->display_name); ?>
                                        </a>
                                    </td>
                                    <td><?php echo esc_html($user->user_email); ?></td>
                                    <td>
                                        <?php
                                        $sub_type = get_user_meta($user->ID, 'subscription_type', true);
                                        echo esc_html($sub_type ?: '—');
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $renewal = get_user_meta($user->ID, 'renewal_date', true);
                                        echo esc_html($renewal ?: '—');
                                        ?>
                                    </td>
                                    <td>
                                        <span class="easytools-status-inactive">
                                            <span class="easytools-status-dot inactive"></span>
                                            <?php _e('Inactive', 'easytools-sub'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="button button-small" onclick="toggleUserAccess(<?php echo $user->ID; ?>, 'activate')">
                                            ✅ <?php _e('Activate', 'easytools-sub'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
        function toggleUserAccess(userId, action) {
            if (!confirm('<?php echo esc_js(__('Are you sure you want to change this user\'s access?', 'easytools-sub')); ?>')) {
                return;
            }

            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'easytools_toggle_user_access',
                    user_id: userId,
                    action_type: action,
                    nonce: '<?php echo wp_create_nonce('easytools_toggle_access'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert('<?php echo esc_js(__('Error:', 'easytools-sub')); ?> ' + response.data.message);
                    }
                },
                error: function() {
                    alert('<?php echo esc_js(__('An error occurred. Please try again.', 'easytools-sub')); ?>');
                }
            });
        }
        </script>
        <?php
    }

    /**
     * Get active subscriptions count
     */
    private function get_active_subscriptions_count() {
        $args = array(
            'meta_query' => array(
                array(
                    'key' => 'subscribed',
                    'value' => '1',
                    'compare' => '='
                )
            ),
            'count_total' => true
        );

        $user_query = new WP_User_Query($args);
        return $user_query->get_total();
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'easytools') === false) {
            return;
        }

        wp_enqueue_script('jquery');

        // Enqueue color picker for bouncer settings
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Initialize color pickers
        wp_add_inline_script('wp-color-picker', 'jQuery(document).ready(function($) { $(".color-picker").wpColorPicker(); });');
    }

    /**
     * Handle export logs AJAX request
     */
    public function handle_export_logs() {
        check_ajax_referer('easytools_export_logs', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'easytools-sub'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'easytools_webhook_logs';

        $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : 'csv';
        $date_from = isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : '';
        $date_to = isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : '';

        // Build WHERE clause for date filter
        $where_clause = '';
        $query_params = array();

        if (!empty($date_from) && !empty($date_to)) {
            $where_clause = "WHERE DATE(created_at) BETWEEN %s AND %s";
            $query_params[] = $date_from;
            $query_params[] = $date_to;
        } elseif (!empty($date_from)) {
            $where_clause = "WHERE DATE(created_at) >= %s";
            $query_params[] = $date_from;
        } elseif (!empty($date_to)) {
            $where_clause = "WHERE DATE(created_at) <= %s";
            $query_params[] = $date_to;
        }

        // Get logs
        if (!empty($query_params)) {
            $logs = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC",
                $query_params
            ));
        } else {
            $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        }

        // Generate filename
        $date_suffix = !empty($date_from) || !empty($date_to) ? '_' . ($date_from ?: 'all') . '_to_' . ($date_to ?: 'all') : '';
        $filename = 'easytools-webhook-logs' . $date_suffix . '.' . $format;

        if ($format === 'csv') {
            $this->export_to_csv($logs, $filename);
        } else {
            $this->export_to_md($logs, $filename);
        }
    }

    /**
     * Export logs to CSV
     */
    private function export_to_csv($logs, $filename) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // CSV headers
        fputcsv($output, array(
            'ID',
            'Date',
            'Event Type',
            'Customer Email',
            'User ID',
            'Status',
            'Request Body',
            'Response Body'
        ));

        // CSV rows
        foreach ($logs as $log) {
            fputcsv($output, array(
                $log->id,
                $log->created_at,
                $log->event_type,
                $log->customer_email,
                $log->user_id ?: 'N/A',
                $log->status,
                $log->request_body,
                $log->response_body
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * Export logs to Markdown
     */
    private function export_to_md($logs, $filename) {
        header('Content-Type: text/markdown; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Markdown content
        echo "# Easytools Webhook Logs Export\n\n";
        echo "**Generated:** " . current_time('Y-m-d H:i:s') . "\n\n";
        echo "**Total logs:** " . count($logs) . "\n\n";
        echo "---\n\n";

        foreach ($logs as $log) {
            echo "## Log #" . $log->id . "\n\n";
            echo "- **Date:** " . $log->created_at . "\n";
            echo "- **Event Type:** `" . $log->event_type . "`\n";
            echo "- **Customer Email:** " . $log->customer_email . "\n";
            echo "- **User ID:** " . ($log->user_id ?: 'N/A') . "\n";
            echo "- **Status:** " . ($log->status === 'success' ? '✅' : '❌') . " " . $log->status . "\n\n";

            echo "### Request Body\n\n";
            echo "```json\n";
            $request_json = json_decode($log->request_body, true);
            if ($request_json) {
                echo json_encode($request_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo $log->request_body;
            }
            echo "\n```\n\n";

            echo "### Response Body\n\n";
            echo "```json\n";
            $response_json = json_decode($log->response_body, true);
            if ($response_json) {
                echo json_encode($response_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo $log->response_body;
            }
            echo "\n```\n\n";

            echo "---\n\n";
        }

        exit;
    }

    /**
     * Handle AJAX toggle user access
     */
    public function handle_toggle_user_access() {
        check_ajax_referer('easytools_toggle_access', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'easytools-sub')));
        }

        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';

        if (!$user_id || !in_array($action, array('activate', 'deactivate'))) {
            wp_send_json_error(array('message' => __('Invalid request', 'easytools-sub')));
        }

        $user = get_user_by('ID', $user_id);
        if (!$user) {
            wp_send_json_error(array('message' => __('User not found', 'easytools-sub')));
        }

        if ($action === 'activate') {
            update_user_meta($user_id, 'subscribed', '1');
            update_user_meta($user_id, 'access_expired', '0');
            update_user_meta($user_id, 'subscription_date', current_time('mysql'));
            $message = sprintf(__('Access activated for %s', 'easytools-sub'), $user->user_email);
        } else {
            update_user_meta($user_id, 'subscribed', '0');
            update_user_meta($user_id, 'access_expired', '1');
            update_user_meta($user_id, 'subscription_end_date', current_time('mysql'));
            $message = sprintf(__('Access deactivated for %s', 'easytools-sub'), $user->user_email);
        }

        wp_send_json_success(array('message' => $message));
    }

    /**
     * Handle AJAX create bouncer page
     */
    public function handle_create_bouncer_page() {
        check_ajax_referer('easytools_create_bouncer', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'easytools-sub')));
        }

        // Get color values from POST (use current form values)
        $product_url = isset($_POST['product_url']) ? esc_url_raw($_POST['product_url']) : '';
        $icon_color = isset($_POST['icon_color']) ? sanitize_hex_color($_POST['icon_color']) : '#71efab';
        $button_color = isset($_POST['button_color']) ? sanitize_hex_color($_POST['button_color']) : '#71efab';
        $bg_color = isset($_POST['bg_color']) ? sanitize_hex_color($_POST['bg_color']) : '#172532';

        // Get bouncer HTML with current colors
        $html = $this->get_bouncer_html_template($product_url, $icon_color, $button_color, $bg_color);

        // Create new page
        $page_data = array(
            'post_title'   => __('Bouncer Page', 'easytools-sub'),
            'post_content' => $html,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => get_current_user_id()
        );

        $page_id = wp_insert_post($page_data);

        if (is_wp_error($page_id)) {
            wp_send_json_error(array('message' => __('Failed to create page', 'easytools-sub')));
        }

        // Save as bouncer page option
        update_option('easytools_bouncer_page', $page_id);

        wp_send_json_success(array(
            'message' => __('Bouncer page created successfully!', 'easytools-sub'),
            'page_id' => $page_id
        ));
    }

    /**
     * Handle AJAX get bouncer HTML
     */
    public function handle_get_bouncer_html() {
        check_ajax_referer('easytools_get_bouncer_html', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'easytools-sub')));
        }

        // Get color values from POST (use current form values)
        $product_url = isset($_POST['product_url']) ? esc_url_raw($_POST['product_url']) : '';
        $icon_color = isset($_POST['icon_color']) ? sanitize_hex_color($_POST['icon_color']) : '#71efab';
        $button_color = isset($_POST['button_color']) ? sanitize_hex_color($_POST['button_color']) : '#71efab';
        $bg_color = isset($_POST['bg_color']) ? sanitize_hex_color($_POST['bg_color']) : '#172532';

        $html = $this->get_bouncer_html_template($product_url, $icon_color, $button_color, $bg_color);

        wp_send_json_success(array('html' => $html));
    }

    /**
     * Generate bouncer page HTML template
     */
    private function get_bouncer_html_template($product_url = null, $icon_color = null, $button_color = null, $bg_color = null) {
        // Use provided parameters or fall back to saved options
        if ($product_url === null) {
            $product_url = get_option('easytools_bouncer_product_url', get_option('easytools_checkout_url', ''));
        }
        if ($icon_color === null) {
            $icon_color = get_option('easytools_bouncer_icon_color', '#71efab');
        }
        if ($button_color === null) {
            $button_color = get_option('easytools_bouncer_button_color', '#71efab');
        }
        if ($bg_color === null) {
            $bg_color = get_option('easytools_bouncer_bg_color', '#172532');
        }

        $login_url = wp_login_url();

        // NO HTML comments to prevent WordPress from wrapping them in <p> tags
        $html = '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 3rem 1rem;"><div style="max-width: 28rem; width: 100%; background-color: #ffffff; padding: 2.5rem; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); text-align: center; box-sizing: border-box;"><div><svg style="margin-left: auto; margin-right: auto; height: 3rem; width: 3rem; color: ' . esc_attr($icon_color) . ';" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 0 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg></div><h2 style="margin-top: 1.5rem; font-size: 1.5rem; line-height: 2rem; font-weight: 700; letter-spacing: -0.025em; color: #111827;">This Content is for Subscribers Only</h2><p style="margin-top: 1rem; font-size: 1rem; line-height: 1.5rem; color: #4B5563;">To access this page, you must be a subscriber. If you have an active account, please log in. Otherwise, start your subscription</p><div style="margin-top: 2rem;"><div style="margin-bottom: 1rem;"><a href="' . esc_url($product_url) . '" style="display: block; width: 100%; border-radius: 0.5rem; background-color: ' . esc_attr($button_color) . '; padding: 0.875rem 1.25rem; font-size: 1rem; line-height: 1.5rem; font-weight: 600; color: #111827; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); box-sizing: border-box;">Start Your Free Trial or Renew Subscription</a></div><div style="margin-top: 1rem;"><a href="' . esc_url($login_url) . '" style="font-weight: 500; color: #2a8c5a; text-decoration: none;">Already a member? Log in here</a></div></div></div></div>
<script type="text/javascript">
document.addEventListener(\'DOMContentLoaded\', function() {
    var container = document.querySelector(\'.container-wrap\');
    if (container) {
        container.style.backgroundColor = \'' . esc_js($bg_color) . '\';
    }
});
</script>';

        return $html;
    }
}
