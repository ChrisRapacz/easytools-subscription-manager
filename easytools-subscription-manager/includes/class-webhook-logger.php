<?php
/**
 * Webhook Logger - logging and displaying webhooks
 */

if (!defined('ABSPATH')) {
    exit;
}

class Easytools_Webhook_Logger {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // AJAX endpoint for fetching log details
        add_action('wp_ajax_get_webhook_log_details', array($this, 'ajax_get_log_details'));

        // AJAX endpoint for deleting log
        add_action('wp_ajax_delete_webhook_log', array($this, 'ajax_delete_log'));

        // Cleanup old logs (30 days)
        add_action('easytools_cleanup_logs', array($this, 'cleanup_old_logs'));

        // Schedule cleanup if it doesn't exist
        if (!wp_next_scheduled('easytools_cleanup_logs')) {
            wp_schedule_event(time(), 'daily', 'easytools_cleanup_logs');
        }
    }

    /**
     * AJAX: Get log details
     */
    public function ajax_get_log_details() {
        check_ajax_referer('easytools_log_details', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $log_id = isset($_POST['log_id']) ? intval($_POST['log_id']) : 0;
        
        if (!$log_id) {
            wp_send_json_error(array('message' => 'Invalid log ID'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'easytools_webhook_logs';
        
        $log = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $log_id
        ));
        
        if (!$log) {
            wp_send_json_error(array('message' => 'Log not found'));
        }
        
        ob_start();
        ?>
        <div class="easytools-log-details">
            <table class="widefat" style="margin-bottom: 20px;">
                <tr>
                    <th style="width: 200px;"><?php _e('ID', 'easytools-sub'); ?></th>
                    <td><?php echo esc_html($log->id); ?></td>
                </tr>
                <tr>
                    <th><?php _e('Date', 'easytools-sub'); ?></th>
                    <td><?php echo esc_html($log->created_at); ?></td>
                </tr>
                <tr>
                    <th><?php _e('Event Type', 'easytools-sub'); ?></th>
                    <td><code><?php echo esc_html($log->event_type); ?></code></td>
                </tr>
                <tr>
                    <th><?php _e('Customer Email', 'easytools-sub'); ?></th>
                    <td><?php echo esc_html($log->customer_email); ?></td>
                </tr>
                <tr>
                    <th><?php _e('User ID', 'easytools-sub'); ?></th>
                    <td>
                        <?php 
                        if ($log->user_id) {
                            echo '<a href="' . get_edit_user_link($log->user_id) . '" target="_blank">' . esc_html($log->user_id) . '</a>';
                        } else {
                            echo 'â€”';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Status', 'easytools-sub'); ?></th>
                    <td>
                        <?php
                        $status_class = $log->status === 'success' ? 'green' : 'red';
                        echo '<span style="color: ' . $status_class . '; font-weight: bold;">' . esc_html($log->status) . '</span>';
                        ?>
                    </td>
                </tr>
            </table>
            
            <h3><?php _e('Request Body (received webhook)', 'easytools-sub'); ?></h3>
            <pre style="background: #f5f5f5; padding: 15px; overflow: auto; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;"><?php 
                $request_data = json_decode($log->request_body, true);
                if ($request_data) {
                    echo esc_html(json_encode($request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                } else {
                    echo esc_html($log->request_body);
                }
            ?></pre>
            
            <h3><?php _e('Response Body (plugin response)', 'easytools-sub'); ?></h3>
            <pre style="background: #f5f5f5; padding: 15px; overflow: auto; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;"><?php 
                $response_data = json_decode($log->response_body, true);
                if ($response_data) {
                    echo esc_html(json_encode($response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                } else {
                    echo esc_html($log->response_body);
                }
            ?></pre>
        </div>
        <?php
        $html = ob_get_clean();
        
        wp_send_json_success(array('html' => $html));
    }

    /**
     * AJAX: Delete log
     */
    public function ajax_delete_log() {
        check_ajax_referer('easytools_delete_log', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $log_id = isset($_POST['log_id']) ? intval($_POST['log_id']) : 0;

        if (!$log_id) {
            wp_send_json_error(array('message' => 'Invalid log ID'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'easytools_webhook_logs';

        $result = $wpdb->delete(
            $table_name,
            array('id' => $log_id),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to delete log'));
        }

        wp_send_json_success(array('message' => 'Log deleted successfully'));
    }

    /**
     * Cleanup old logs (>30 days)
     */
    public function cleanup_old_logs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'easytools_webhook_logs';
        
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            30
        ));
    }
    
    /**
     * Webhook statistics
     */
    public static function get_webhook_stats() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'easytools_webhook_logs';
        
        $stats = array(
            'total' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name"),
            'success' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'success'"),
            'failed' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status LIKE 'error%'"),
            'last_24h' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)")
        );
        
        return $stats;
    }
}
