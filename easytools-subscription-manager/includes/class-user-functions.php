<?php
/**
 * User Functions - user functions and meta fields
 */

if (!defined('ABSPATH')) {
    exit;
}

class Easytools_User_Functions {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Add fields to user profile
        add_action('show_user_profile', array($this, 'add_subscription_fields'));
        add_action('edit_user_profile', array($this, 'add_subscription_fields'));

        // Save profile fields
        add_action('personal_options_update', array($this, 'save_subscription_fields'));
        add_action('edit_user_profile_update', array($this, 'save_subscription_fields'));

        // Add columns to users list
        add_filter('manage_users_columns', array($this, 'add_user_columns'));
        add_filter('manage_users_custom_column', array($this, 'render_user_columns'), 10, 3);

        // Add filter to users list
        add_action('restrict_manage_users', array($this, 'add_subscription_filter'));
        add_filter('pre_get_users', array($this, 'filter_users_by_subscription'));
    }

    /**
     * Add subscription fields to user profile
     */
    public function add_subscription_fields($user) {
        if (!current_user_can('edit_users')) {
            return;
        }
        
        $subscribed = get_user_meta($user->ID, 'subscribed', true);
        $subscription_type = get_user_meta($user->ID, 'subscription_type', true);
        $subscription_date = get_user_meta($user->ID, 'subscription_date', true);
        $renewal_date = get_user_meta($user->ID, 'renewal_date', true);
        $subscription_one_time = get_user_meta($user->ID, 'subscription_one_time', true);
        $trial_ends_at = get_user_meta($user->ID, 'trial_ends_at', true);
        $easytools_customer_id = get_user_meta($user->ID, 'easytools_customer_id', true);
        $access_expired = get_user_meta($user->ID, 'access_expired', true);
        ?>
        
        <h2><?php _e('Easytools Subscription', 'easytools-sub'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label for="subscribed"><?php _e('Subscription Status', 'easytools-sub'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="subscribed" id="subscribed" value="1" <?php checked($subscribed, '1'); ?> />
                        <?php _e('Active subscription', 'easytools-sub'); ?>
                    </label>
                    <p class="description">
                        <?php if ($subscribed === '1') : ?>
                            <span style="color: green; font-weight: bold;">● <?php _e('User has access', 'easytools-sub'); ?></span>
                        <?php else : ?>
                            <span style="color: red; font-weight: bold;">● <?php _e('User does not have access', 'easytools-sub'); ?></span>
                        <?php endif; ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th><label for="subscription_type"><?php _e('Subscription Type', 'easytools-sub'); ?></label></th>
                <td>
                    <input type="text" name="subscription_type" id="subscription_type" value="<?php echo esc_attr($subscription_type); ?>" class="regular-text" />
                    <p class="description"><?php _e('e.g. monthly, yearly, lifetime', 'easytools-sub'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th><label><?php _e('Start Date', 'easytools-sub'); ?></label></th>
                <td>
                    <input type="text" name="subscription_date" value="<?php echo esc_attr($subscription_date); ?>" class="regular-text" />
                    <p class="description"><?php _e('Format: YYYY-MM-DD HH:MM:SS', 'easytools-sub'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th><label><?php _e('Renewal Date', 'easytools-sub'); ?></label></th>
                <td>
                    <input type="text" name="renewal_date" value="<?php echo esc_attr($renewal_date); ?>" class="regular-text" />
                    <p class="description"><?php _e('Next payment date (for recurring subscriptions)', 'easytools-sub'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th><label><?php _e('One-time Payment', 'easytools-sub'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="subscription_one_time" value="1" <?php checked($subscription_one_time, '1'); ?> />
                        <?php _e('Lifetime access (no renewals)', 'easytools-sub'); ?>
                    </label>
                </td>
            </tr>
            
            <tr>
                <th><label><?php _e('Trial Ends', 'easytools-sub'); ?></label></th>
                <td>
                    <input type="text" name="trial_ends_at" value="<?php echo esc_attr($trial_ends_at); ?>" class="regular-text" />
                </td>
            </tr>
            
            <tr>
                <th><label><?php _e('Easytools Customer ID', 'easytools-sub'); ?></label></th>
                <td>
                    <input type="text" name="easytools_customer_id" value="<?php echo esc_attr($easytools_customer_id); ?>" class="regular-text" readonly />
                    <p class="description"><?php _e('Customer ID in Easytools system (read-only)', 'easytools-sub'); ?></p>
                </td>
            </tr>
            
            <?php if ($access_expired === '1') : ?>
            <tr>
                <th></th>
                <td>
                    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-top: 10px;">
                        <strong><?php _e('⚠️ Subscription Expired', 'easytools-sub'); ?></strong>
                        <p style="margin: 5px 0 0 0;"><?php _e('User no longer has access to protected content.', 'easytools-sub'); ?></p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </table>
        <?php
    }
    
    /**
     * Save subscription fields
     */
    public function save_subscription_fields($user_id) {
        if (!current_user_can('edit_users')) {
            return;
        }
        
        update_user_meta($user_id, 'subscribed', isset($_POST['subscribed']) ? '1' : '0');
        
        if (isset($_POST['subscription_type'])) {
            update_user_meta($user_id, 'subscription_type', sanitize_text_field($_POST['subscription_type']));
        }
        
        if (isset($_POST['subscription_date'])) {
            update_user_meta($user_id, 'subscription_date', sanitize_text_field($_POST['subscription_date']));
        }
        
        if (isset($_POST['renewal_date'])) {
            update_user_meta($user_id, 'renewal_date', sanitize_text_field($_POST['renewal_date']));
        }
        
        update_user_meta($user_id, 'subscription_one_time', isset($_POST['subscription_one_time']) ? '1' : '0');
        
        if (isset($_POST['trial_ends_at'])) {
            update_user_meta($user_id, 'trial_ends_at', sanitize_text_field($_POST['trial_ends_at']));
        }
    }
    
    /**
     * Add columns to users list
     */
    public function add_user_columns($columns) {
        $columns['easytools_subscription'] = __('Subscription', 'easytools-sub');
        return $columns;
    }

    /**
     * Render user columns
     */
    public function render_user_columns($value, $column_name, $user_id) {
        if ($column_name === 'easytools_subscription') {
            $subscribed = get_user_meta($user_id, 'subscribed', true);
            $subscription_type = get_user_meta($user_id, 'subscription_type', true);
            
            if ($subscribed === '1') {
                $type_label = $subscription_type ? ' (' . esc_html($subscription_type) . ')' : '';
                return '<span style="color: green; font-weight: bold;">● Active</span>' . $type_label;
            } else {
                return '<span style="color: #999;">○ None</span>';
            }
        }
        
        return $value;
    }
    
    /**
     * Add subscription filter to users list
     */
    public function add_subscription_filter() {
        $selected = isset($_GET['easytools_subscription']) ? $_GET['easytools_subscription'] : '';
        ?>
        <label class="screen-reader-text" for="easytools-subscription-filter">
            <?php _e('Filter by subscription', 'easytools-sub'); ?>
        </label>
        <select name="easytools_subscription" id="easytools-subscription-filter">
            <option value=""><?php _e('All subscriptions', 'easytools-sub'); ?></option>
            <option value="active" <?php selected($selected, 'active'); ?>><?php _e('Active', 'easytools-sub'); ?></option>
            <option value="inactive" <?php selected($selected, 'inactive'); ?>><?php _e('Inactive', 'easytools-sub'); ?></option>
        </select>
        <?php
    }
    
    /**
     * Filter users by subscription
     */
    public function filter_users_by_subscription($query) {
        global $pagenow;
        
        if ($pagenow !== 'users.php' || !is_admin()) {
            return $query;
        }
        
        if (!isset($_GET['easytools_subscription']) || empty($_GET['easytools_subscription'])) {
            return $query;
        }
        
        $subscription_status = $_GET['easytools_subscription'];
        
        $meta_query = array(
            array(
                'key' => 'subscribed',
                'value' => $subscription_status === 'active' ? '1' : '0',
                'compare' => '='
            )
        );
        
        $query->set('meta_query', $meta_query);
        
        return $query;
    }
}
