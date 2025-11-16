<?php
/**
 * Plugin Name: Easytools Subscription Manager
 * Plugin URI: https://easy.tools
 * Description: Complete subscription management system for Easytools with webhooks, access control, API token security, and premium admin interface
 * Version: 1.5.6
 * Author: Chris Rapacz
 * Author URI: https://www.chrisrapacz.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: easytools-sub
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EASYTOOLS_SUB_VERSION', '1.5.6');
define('EASYTOOLS_SUB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EASYTOOLS_SUB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EASYTOOLS_SUB_PLUGIN_FILE', __FILE__);

/**
 * Main Plugin Class
 */
class Easytools_Subscription_Manager {

    private static $instance = null;

    /**
     * Singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_textdomain();
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load plugin text domain for translations
     */
    private function load_textdomain() {
        load_plugin_textdomain(
            'easytools-sub',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        require_once EASYTOOLS_SUB_PLUGIN_DIR . 'includes/class-admin-settings.php';
        require_once EASYTOOLS_SUB_PLUGIN_DIR . 'includes/class-email-handler.php';
        require_once EASYTOOLS_SUB_PLUGIN_DIR . 'includes/class-webhook-handler.php';
        require_once EASYTOOLS_SUB_PLUGIN_DIR . 'includes/class-webhook-tester.php';
        require_once EASYTOOLS_SUB_PLUGIN_DIR . 'includes/class-access-control.php';
        require_once EASYTOOLS_SUB_PLUGIN_DIR . 'includes/class-user-functions.php';
        require_once EASYTOOLS_SUB_PLUGIN_DIR . 'includes/class-webhook-logger.php';
        require_once EASYTOOLS_SUB_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once EASYTOOLS_SUB_PLUGIN_DIR . 'includes/class-dashboard-widget.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Initialize components
        add_action('plugins_loaded', array($this, 'init_components'));

        // Enqueue admin styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Add settings link in plugins list
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));

        // Add plugin meta links
        add_filter('plugin_row_meta', array($this, 'add_plugin_meta_links'), 10, 2);
    }

    /**
     * Initialize plugin components
     */
    public function init_components() {
        Easytools_Admin_Settings::get_instance();
        Easytools_Email_Handler::get_instance();
        Easytools_Webhook_Handler::get_instance();
        Easytools_Webhook_Tester::get_instance();
        Easytools_Access_Control::get_instance();
        Easytools_User_Functions::get_instance();
        Easytools_Webhook_Logger::get_instance();
        Easytools_Shortcodes::get_instance();
        Easytools_Dashboard_Widget::get_instance();
    }

    /**
     * Enqueue admin assets (CSS/JS)
     */
    public function enqueue_admin_assets($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'easytools') === false && $hook !== 'index.php') {
            return;
        }

        // Enqueue premium CSS
        wp_enqueue_style(
            'easytools-admin-premium',
            EASYTOOLS_SUB_PLUGIN_URL . 'assets/css/admin-premium.css',
            array(),
            EASYTOOLS_SUB_VERSION
        );

        // Enqueue dashicons (for icons)
        wp_enqueue_style('dashicons');
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create webhook logs table
        global $wpdb;
        $table_name = $wpdb->prefix . 'easytools_webhook_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(100) NOT NULL,
            customer_email varchar(255) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            status varchar(50) NOT NULL,
            request_body longtext NOT NULL,
            response_body text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY event_type (event_type),
            KEY customer_email (customer_email),
            KEY created_at (created_at),
            KEY status (status)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Set default options
        $defaults = array(
            'easytools_checkout_url' => '',
            'easytools_webhook_signing_key' => '',
            'easytools_api_token' => '',
            'easytools_protected_pages' => array(),
            'easytools_protect_all' => 'no',
            'easytools_exclude_pages' => array(),
            'easytools_prefer_automation' => 'yes',
            'easytools_automation_wait_time' => 30,
            'easytools_default_role' => 'subscriber',
            'easytools_send_welcome_email' => 'yes',
            'easytools_admin_notifications' => 'yes',
            'easytools_admin_email' => get_option('admin_email'),
            'easytools_dev_mode' => 'no',
            'easytools_enable_bouncer' => 'no',
            'easytools_bouncer_page' => '',
            'easytools_bouncer_product_url' => '',
            'easytools_bouncer_icon_color' => '#71efab',
            'easytools_bouncer_button_color' => '#71efab',
            'easytools_bouncer_bg_color' => '#172532',
        );

        foreach ($defaults as $key => $value) {
            if (false === get_option($key)) {
                add_option($key, $value);
            }
        }

        // Flush rewrite rules for REST API
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Add settings link to plugins page
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="admin.php?page=easytools-subscription">' . __('Settings', 'easytools-sub') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add meta links to plugins page
     */
    public function add_plugin_meta_links($links, $file) {
        if (plugin_basename(__FILE__) === $file) {
            // Detect language (simple check based on WP locale)
            $locale = get_locale();
            $docs_url = (strpos($locale, 'pl') === 0) ? 'https://www.easy.tools/pl/docs/odkrywaj' : 'https://www.easy.tools/docs/explore';

            $row_meta = array(
                'docs' => '<a href="' . esc_url($docs_url) . '" target="_blank">' . __('Documentation', 'easytools-sub') . '</a>',
                'support' => '<a href="mailto:kontakt.rapacz@gmail.com">' . __('Support', 'easytools-sub') . '</a>',
            );
            return array_merge($links, $row_meta);
        }
        return $links;
    }
}

/**
 * Initialize plugin
 */
function easytools_subscription_manager() {
    return Easytools_Subscription_Manager::get_instance();
}

// Run the plugin
easytools_subscription_manager();
