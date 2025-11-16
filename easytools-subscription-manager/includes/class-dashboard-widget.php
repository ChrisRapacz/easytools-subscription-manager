<?php
/**
 * Dashboard Widget - Statistics and overview
 */

if (!defined('ABSPATH')) {
    exit;
}

class Easytools_Dashboard_Widget {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
    }
    
    /**
     * Add dashboard widget
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'easytools_dashboard_widget',
            '📊 ' . __('Easytools Subscriptions', 'easytools-sub'),
            array($this, 'render_dashboard_widget')
        );
    }
    
    /**
     * Render dashboard widget
     */
    public function render_dashboard_widget() {
        $stats = $this->get_subscription_stats();
        ?>
        <div class="easytools-widget-stats">
            <div class="easytools-widget-stat">
                <div class="easytools-widget-stat-number" style="color: #00a32a;">
                    <?php echo esc_html($stats['active']); ?>
                </div>
                <div class="easytools-widget-stat-label">
                    <?php _e('Active Subscriptions', 'easytools-sub'); ?>
                </div>
            </div>
            
            <div class="easytools-widget-stat">
                <div class="easytools-widget-stat-number" style="color: #d63638;">
                    <?php echo esc_html($stats['inactive']); ?>
                </div>
                <div class="easytools-widget-stat-label">
                    <?php _e('Inactive Subscriptions', 'easytools-sub'); ?>
                </div>
            </div>
            
            <div class="easytools-widget-stat">
                <div class="easytools-widget-stat-number" style="color: #2271b1;">
                    <?php echo esc_html($stats['today']); ?>
                </div>
                <div class="easytools-widget-stat-label">
                    <?php _e('New Today', 'easytools-sub'); ?>
                </div>
            </div>
            
            <div class="easytools-widget-stat">
                <div class="easytools-widget-stat-number" style="color: #f0b849;">
                    <?php echo esc_html($stats['recent_webhooks']); ?>
                </div>
                <div class="easytools-widget-stat-label">
                    <?php _e('Webhooks (24h)', 'easytools-sub'); ?>
                </div>
            </div>
        </div>
        
        <div class="easytools-widget-footer">
            <a href="<?php echo admin_url('admin.php?page=easytools-subscription'); ?>">
                <?php _e('View All Subscriptions', 'easytools-sub'); ?> →
            </a>
        </div>
        <?php
    }
    
    /**
     * Get subscription statistics
     */
    private function get_subscription_stats() {
        global $wpdb;
        
        // Active subscriptions
        $active = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->usermeta}
                 WHERE meta_key = %s AND meta_value = %s",
                'subscribed',
                '1'
            )
        );

        // Inactive subscriptions
        $inactive = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->usermeta}
                 WHERE meta_key = %s AND meta_value = %s",
                'subscribed',
                '0'
            )
        );
        
        // New subscriptions today
        $today = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->usermeta} 
                 WHERE meta_key = 'subscription_date' 
                 AND meta_value >= %s",
                date('Y-m-d 00:00:00')
            )
        );
        
        // Recent webhooks (last 24 hours)
        $table_name = $wpdb->prefix . 'easytools_webhook_logs';
        $recent_webhooks = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} 
                 WHERE created_at >= %s",
                date('Y-m-d H:i:s', strtotime('-24 hours'))
            )
        );
        
        return array(
            'active' => intval($active),
            'inactive' => intval($inactive),
            'today' => intval($today),
            'recent_webhooks' => intval($recent_webhooks),
        );
    }
}
