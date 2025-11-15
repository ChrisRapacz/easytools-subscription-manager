<?php
/**
 * Webhook Handler - Easytools webhooks processing
 */

if (!defined('ABSPATH')) {
    exit;
}

class Easytools_Webhook_Handler {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', array($this, 'register_webhook_endpoint'));
    }

    /**
     * Register REST API endpoint for webhooks
     */
    public function register_webhook_endpoint() {
        register_rest_route('easytools/v1', '/webhook', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_webhook'),
            'permission_callback' => '__return_true' // Verification via signing key
        ));
    }

    /**
     * Main webhook handling function
     */
    public function handle_webhook($request) {
        $start_time = microtime(true);

        // Get request data
        $body = $request->get_body();
        $signature = $request->get_header('x-webhook-signature');
        $json_data = json_decode($body, true);

        // Prepare response
        $response = array(
            'success' => false,
            'message' => '',
            'timestamp' => current_time('mysql')
        );

        // Validate JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $response['message'] = 'Invalid JSON payload';
            $this->log_webhook('invalid', '', null, 'error', $body, json_encode($response));
            return new WP_REST_Response($response, 400);
        }

        $event_type = isset($json_data['event']) ? $json_data['event'] : '';
        $customer_email = isset($json_data['customer_email']) ? sanitize_email($json_data['customer_email']) : '';

        // Verify API token (if configured)
        if (!$this->verify_api_token($request)) {
            $response['message'] = 'Invalid or missing API token';
            $this->log_webhook($event_type, $customer_email, null, 'error_api_token', $body, json_encode($response));
            return new WP_REST_Response($response, 403);
        }

        // Verify webhook signature
        if (!$this->verify_webhook_signature($body, $signature)) {
            $response['message'] = 'Invalid webhook signature';
            $this->log_webhook($event_type, $customer_email, null, 'error_signature', $body, json_encode($response));
            return new WP_REST_Response($response, 403);
        }

        // Validate required fields
        if (empty($event_type) || empty($customer_email)) {
            $response['message'] = 'Missing required fields: event or customer_email';
            $this->log_webhook($event_type, $customer_email, null, 'error_validation', $body, json_encode($response));
            return new WP_REST_Response($response, 400);
        }

        // Check if email is valid
        if (!is_email($customer_email)) {
            $response['message'] = 'Invalid email format';
            $this->log_webhook($event_type, $customer_email, null, 'error_email', $body, json_encode($response));
            return new WP_REST_Response($response, 400);
        }

        // Process webhook based on event type
        try {
            $result = $this->process_webhook_event($event_type, $json_data);

            $response['success'] = true;
            $response['message'] = $result['message'];
            $response['user_id'] = $result['user_id'];
            $response['processing_time'] = round(microtime(true) - $start_time, 3) . 's';

            $this->log_webhook($event_type, $customer_email, $result['user_id'], 'success', $body, json_encode($response));

            return new WP_REST_Response($response, 200);

        } catch (Exception $e) {
            $response['message'] = 'Error processing webhook: ' . $e->getMessage();
            $this->log_webhook($event_type, $customer_email, null, 'error_processing', $body, json_encode($response));

            return new WP_REST_Response($response, 500);
        }
    }

    /**
     * Verify API token from URL parameter
     */
    private function verify_api_token($request) {
        $configured_token = get_option('easytools_api_token', '');

        // If API token is not configured, skip verification
        if (empty($configured_token)) {
            return true;
        }

        // Get api_token from query parameter
        $provided_token = $request->get_param('api_token');

        // If no token provided in request
        if (empty($provided_token)) {
            error_log('Easytools Webhook: API token required but not provided');
            return false;
        }

        // Compare tokens in a timing attack resistant way
        $is_valid = hash_equals($configured_token, $provided_token);

        if (!$is_valid) {
            error_log('Easytools Webhook: Invalid API token provided');
        }

        return $is_valid;
    }

    /**
     * Verify webhook signature
     * FIXED: JSON normalization before HMAC calculation
     */
    private function verify_webhook_signature($payload, $signature) {
        $signing_key = get_option('easytools_webhook_signing_key', '');
        $dev_mode = get_option('easytools_dev_mode', 'no') === 'yes';

        // DEVELOPER MODE - skip verification
        if ($dev_mode) {
            error_log('Easytools Webhook: DEV MODE - Signature verification DISABLED');
            return true;
        }

        // If signing key is not set, skip verification (not recommended!)
        if (empty($signing_key)) {
            error_log('Easytools Webhook: Signing key not configured. Skipping signature verification.');
            return true;
        }

        // If no signature in header
        if (empty($signature)) {
            return false;
        }

        // CRITICAL FIX: Normalize JSON before HMAC calculation
        // This ensures consistent formatting regardless of whitespace/formatting
        $json_data = json_decode($payload, true);
        if ($json_data !== null) {
            // Re-encode without pretty printing to ensure consistency
            $normalized_payload = json_encode($json_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            // If JSON parsing fails, use original payload
            $normalized_payload = $payload;
        }

        // Calculate expected signature (HMAC SHA256)
        $expected_signature = hash_hmac('sha256', $normalized_payload, $signing_key);

        // Compare signatures in a timing attack resistant way
        $is_valid = hash_equals($expected_signature, $signature);

        // Debug logging
        if (!$is_valid) {
            error_log('Easytools Webhook: Signature mismatch');
            error_log('Expected: ' . $expected_signature);
            error_log('Received: ' . $signature);
            error_log('Normalized payload: ' . $normalized_payload);
        }

        return $is_valid;
    }

    /**
     * Process webhook event
     * FIXED: Added all Easytools webhook events based on official documentation
     * - subscription_deleted, product_access_expired (deactivation)
     * - subscription_resumed (reactivation after cancellation)
     * - single_product_bought (one-time purchase)
     * - All informational events handled (no access changes)
     */
    private function process_webhook_event($event_type, $data) {
        $customer_email = sanitize_email($data['customer_email']);
        $user = get_user_by('email', $customer_email);

        switch ($event_type) {
            // Activation events (grant access)
            case 'product_assigned':
            case 'subscription_created':
            case 'subscription_resumed':        // Reactivates canceled subscription
            case 'single_product_bought':       // One-time product purchase
                return $this->handle_subscription_active($data, $user);

            // Deactivation events (revoke access)
            case 'subscription_expired':
            case 'subscription_cancelled':      // UK spelling used by Easytools API
            case 'subscription_deleted':
            case 'product_access_expired':
                return $this->handle_subscription_inactive($data, $user);

            // Renewal event (keep access active)
            case 'subscription_renewed':
                return $this->handle_subscription_renewed($data, $user);

            // Plan change event (update metadata)
            case 'subscription_plan_changed':
                return $this->handle_subscription_plan_changed($data, $user);

            // Informational events (no action needed)
            case 'product_access_expiring':
            case 'subscription_renewal_failed':
            case 'subscription_renewal_upcoming':
            case 'customer_data_changed':
                return array(
                    'user_id' => $user ? $user->ID : null,
                    'message' => 'Informational event received: ' . $event_type
                );

            default:
                return array(
                    'user_id' => $user ? $user->ID : null,
                    'message' => 'Event type not handled: ' . $event_type
                );
        }
    }

    /**
     * Handle subscription activation
     */
    private function handle_subscription_active($data, $user) {
        $customer_email = sanitize_email($data['customer_email']);
        $is_new_user = false;

        // If user doesn't exist, create account
        if (!$user) {
            $user = $this->create_user_account($data);
            $is_new_user = true;

            if (is_wp_error($user)) {
                // Check if this is automation_only mode (not a real error)
                if ($user->get_error_code() === 'automation_only') {
                    return array(
                        'user_id' => null,
                        'message' => 'Automation-only mode: ' . $user->get_error_message()
                    );
                }
                // Real error - throw exception
                throw new Exception('Failed to create user: ' . $user->get_error_message());
            }
        }

        // Check if this is the first time activating subscription (even if account existed)
        $was_never_subscribed = !get_user_meta($user->ID, 'easytools_welcome_email_sent', true);
        $send_email_setting = get_option('easytools_send_welcome_email', 'yes');

        error_log("Easytools: Email check - is_new_user: " . ($is_new_user ? 'TRUE' : 'FALSE'));
        error_log("Easytools: Email check - was_never_subscribed: " . ($was_never_subscribed ? 'TRUE' : 'FALSE'));
        error_log("Easytools: Email check - send_email_setting: " . $send_email_setting);
        error_log("Easytools: Email check - user_id: " . $user->ID);
        error_log("Easytools: Email check - user_login: " . $user->user_login);
        error_log("Easytools: Email check - user_email: " . $user->user_email);

        // Activate subscription
        update_user_meta($user->ID, 'subscribed', '1');
        update_user_meta($user->ID, 'subscription_date', current_time('mysql'));

        // Save additional subscription data
        $this->save_subscription_meta($user->ID, $data);

        // Send welcome email for new users OR users who never received welcome email
        $should_send = ($is_new_user || $was_never_subscribed) && $send_email_setting === 'yes';
        error_log("Easytools: Should send email: " . ($should_send ? 'YES' : 'NO'));

        if ($should_send) {
            error_log("Easytools: ====== ATTEMPTING TO SEND WELCOME EMAIL ======");
            error_log("Easytools: Email target: {$customer_email}");
            error_log("Easytools: Email handler class exists: " . (class_exists('Easytools_Email_Handler') ? 'YES' : 'NO'));

            $email_handler = Easytools_Email_Handler::get_instance();
            error_log("Easytools: Email handler instance created");

            $username = $user->user_login;
            error_log("Easytools: Calling send_new_user_email with user_id={$user->ID}, email={$customer_email}, username={$username}");

            $result = $email_handler->send_new_user_email($user->ID, $customer_email, $username);

            error_log("Easytools: Email send result: " . ($result ? 'SUCCESS' : 'FAILED'));

            if ($result) {
                error_log("Easytools: ====== WELCOME EMAIL SENT SUCCESSFULLY ======");
                update_user_meta($user->ID, 'easytools_welcome_email_sent', '1');
            } else {
                error_log("Easytools: ====== WELCOME EMAIL FAILED TO SEND ======");
                error_log("Easytools: Check wp_mail configuration and SMTP settings");
            }
        } else {
            error_log("Easytools: Email NOT sent - conditions not met or setting disabled");
        }

        // Send notifications
        $this->send_notifications($user, 'activated', $data);

        return array(
            'user_id' => $user->ID,
            'message' => 'Subscription activated for user ' . $customer_email
        );
    }

    /**
     * Handle subscription deactivation
     */
    private function handle_subscription_inactive($data, $user) {
        $customer_email = sanitize_email($data['customer_email']);

        if (!$user) {
            return array(
                'user_id' => null,
                'message' => 'User not found: ' . $customer_email
            );
        }

        // Deactivate subscription
        update_user_meta($user->ID, 'subscribed', '0');
        update_user_meta($user->ID, 'access_expired', '1');
        update_user_meta($user->ID, 'subscription_end_date', current_time('mysql'));

        // Send notifications
        $this->send_notifications($user, 'expired', $data);

        return array(
            'user_id' => $user->ID,
            'message' => 'Subscription deactivated for user ' . $customer_email
        );
    }

    /**
     * Handle subscription renewal
     */
    private function handle_subscription_renewed($data, $user) {
        $customer_email = sanitize_email($data['customer_email']);

        if (!$user) {
            return array(
                'user_id' => null,
                'message' => 'User not found: ' . $customer_email
            );
        }

        // Renew subscription
        update_user_meta($user->ID, 'subscribed', '1');
        update_user_meta($user->ID, 'access_expired', '0');

        // Update renewal date
        $this->save_subscription_meta($user->ID, $data);

        // Send notifications
        $this->send_notifications($user, 'renewed', $data);

        return array(
            'user_id' => $user->ID,
            'message' => 'Subscription renewed for user ' . $customer_email
        );
    }

    /**
     * Handle subscription plan change
     */
    private function handle_subscription_plan_changed($data, $user) {
        $customer_email = sanitize_email($data['customer_email']);

        if (!$user) {
            return array(
                'user_id' => null,
                'message' => 'User not found: ' . $customer_email
            );
        }

        // Update subscription data
        $this->save_subscription_meta($user->ID, $data);

        return array(
            'user_id' => $user->ID,
            'message' => 'Subscription plan changed for user ' . $customer_email
        );
    }

    /**
     * Create user account (mode-based approach)
     */
    private function create_user_account($data) {
        $customer_email = sanitize_email($data['customer_email']);
        $mode = get_option('easytools_account_creation_mode', 'webhook_only');

        error_log("Easytools: create_user_account called for {$customer_email}, mode: {$mode}");

        // Handle different account creation modes
        if ($mode === 'automation_only') {
            // Mode 3: Automation Only - Don't create account, trust automation will handle it
            error_log("Easytools: Automation-only mode. Skipping webhook account creation for {$customer_email}");
            return new WP_Error('automation_only', 'Account creation delegated to Easytools Automation');
        }

        if ($mode === 'automation_with_fallback') {
            // Mode 2: Wait 5 minutes for automation, then create if needed
            $wait_time = 300; // 5 minutes
            error_log("Easytools: Waiting {$wait_time}s for Easytools automation to create account for {$customer_email}");
            sleep($wait_time);

            // Check if account was created by automation
            $user = get_user_by('email', $customer_email);
            if ($user) {
                error_log("Easytools: Account created by automation for {$customer_email}");
                return $user;
            }

            error_log("Easytools: Automation timeout. Creating account via webhook fallback for {$customer_email}");
        }

        // Mode 1 (webhook_only) or Mode 2 fallback: Create account via webhook
        $username = $this->generate_username($customer_email);
        $password = wp_generate_password(12, true, true);
        $default_role = get_option('easytools_default_role', 'subscriber');

        error_log("Easytools: Creating user account for {$customer_email}");

        $user_id = wp_create_user($username, $password, $customer_email);

        if (is_wp_error($user_id)) {
            error_log("Easytools: Failed to create user account: " . $user_id->get_error_message());
            return $user_id;
        }

        error_log("Easytools: User account created successfully, user_id: {$user_id}");

        // Set role
        $user = new WP_User($user_id);
        $user->set_role($default_role);

        // Set display name
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $username,
            'first_name' => isset($data['customer_name']) ? sanitize_text_field($data['customer_name']) : ''
        ));

        // Note: Welcome email is now sent in handle_subscription_active() to ensure it's sent
        // for all new subscriptions, regardless of how the account was created
        error_log("Easytools: Account created via webhook for {$customer_email}");

        return $user;
    }

    /**
     * Generate unique username
     */
    private function generate_username($email) {
        $username = sanitize_user(current(explode('@', $email)));

        // If username exists, add number
        if (username_exists($username)) {
            $i = 1;
            while (username_exists($username . $i)) {
                $i++;
            }
            $username = $username . $i;
        }

        return $username;
    }

    /**
     * Save subscription metadata
     * FIXED: Now uses actual Easytools webhook field names
     */
    private function save_subscription_meta($user_id, $data) {
        // Subscription type - try multiple fields to get the subscription name
        // Priority: subscription_type > subscription_price_name > price_name > price_custom_id > subscription_plan_custom_id
        $subscription_type = null;
        if (!empty($data['subscription_type'])) {
            // Direct subscription_type field (e.g., "monthly", "yearly", "lifetime")
            $subscription_type = sanitize_text_field($data['subscription_type']);
        } elseif (!empty($data['subscription_price_name'])) {
            $subscription_type = sanitize_text_field($data['subscription_price_name']);
        } elseif (!empty($data['price_name'])) {
            $subscription_type = sanitize_text_field($data['price_name']);
        } elseif (!empty($data['price_custom_id'])) {
            $subscription_type = sanitize_text_field($data['price_custom_id']);
        } elseif (!empty($data['subscription_plan_custom_id'])) {
            $subscription_type = sanitize_text_field($data['subscription_plan_custom_id']);
        }

        if ($subscription_type) {
            update_user_meta($user_id, 'subscription_type', $subscription_type);
            error_log("Easytools: Saved subscription_type: {$subscription_type}");
        } else {
            error_log("Easytools: WARNING - No subscription_type found in webhook data");
        }

        // One-time payment (from subscription_onetime field)
        if (isset($data['subscription_onetime'])) {
            update_user_meta($user_id, 'subscription_one_time', $data['subscription_onetime'] ? '1' : '0');
        } elseif (isset($data['subscription_one_time'])) {
            // Fallback to old field name
            update_user_meta($user_id, 'subscription_one_time', $data['subscription_one_time'] ? '1' : '0');
        }

        // Renewal date - use subscription_current_period_end from Easytools
        // Format: "2025-11-28T23:33:20+01:00"
        if (!empty($data['subscription_current_period_end'])) {
            $renewal_date = sanitize_text_field($data['subscription_current_period_end']);
            update_user_meta($user_id, 'renewal_date', $renewal_date);
        } elseif (!empty($data['renewal_date'])) {
            // Fallback to old field name
            update_user_meta($user_id, 'renewal_date', sanitize_text_field($data['renewal_date']));
        }

        // Trial
        if (!empty($data['trial_ends_at'])) {
            update_user_meta($user_id, 'trial_ends_at', sanitize_text_field($data['trial_ends_at']));
        }

        // Easytools Customer ID
        if (!empty($data['customer_id'])) {
            update_user_meta($user_id, 'easytools_customer_id', sanitize_text_field($data['customer_id']));
        }

        // Stripe Customer ID
        if (!empty($data['customer_stripe_id'])) {
            update_user_meta($user_id, 'easytools_stripe_customer_id', sanitize_text_field($data['customer_stripe_id']));
        }

        // Product ID
        if (!empty($data['product_id'])) {
            update_user_meta($user_id, 'easytools_product_id', sanitize_text_field($data['product_id']));
        }

        // Product name
        if (!empty($data['product_name'])) {
            update_user_meta($user_id, 'easytools_product_name', sanitize_text_field($data['product_name']));
        }

        // Subscription ID
        if (!empty($data['subscription_id'])) {
            update_user_meta($user_id, 'easytools_subscription_id', sanitize_text_field($data['subscription_id']));
        }

        // Stripe Subscription ID
        if (!empty($data['subscription_stripe_id'])) {
            update_user_meta($user_id, 'easytools_stripe_subscription_id', sanitize_text_field($data['subscription_stripe_id']));
        }

        // Price - use subscription_plan_price or order_amount
        if (isset($data['subscription_plan_price'])) {
            update_user_meta($user_id, 'subscription_price', sanitize_text_field($data['subscription_plan_price']));
        } elseif (isset($data['order_amount'])) {
            update_user_meta($user_id, 'subscription_price', sanitize_text_field($data['order_amount']));
        } elseif (isset($data['price'])) {
            // Fallback to old field name
            update_user_meta($user_id, 'subscription_price', sanitize_text_field($data['price']));
        }

        // Currency
        if (!empty($data['currency'])) {
            update_user_meta($user_id, 'subscription_currency', sanitize_text_field($data['currency']));
        }

        // Order ID
        if (!empty($data['order_id'])) {
            update_user_meta($user_id, 'easytools_order_id', sanitize_text_field($data['order_id']));
        }
    }

    /**
     * Send notifications
     */
    private function send_notifications($user, $action, $data) {
        // Admin notification
        if (get_option('easytools_admin_notifications', 'yes') === 'yes') {
            $admin_email = get_option('easytools_admin_email', get_option('admin_email'));

            $subject = sprintf('[%s] Subscription %s', get_bloginfo('name'), $action);
            $message = sprintf(
                "User: %s (%s)\nAction: %s\nDate: %s",
                $user->display_name,
                $user->user_email,
                $action,
                current_time('mysql')
            );

            wp_mail($admin_email, $subject, $message);
        }
    }

    /**
     * Log webhook to database
     */
    private function log_webhook($event_type, $customer_email, $user_id, $status, $request_body, $response_body) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'easytools_webhook_logs';

        $wpdb->insert(
            $table_name,
            array(
                'event_type' => $event_type,
                'customer_email' => $customer_email,
                'user_id' => $user_id,
                'status' => $status,
                'request_body' => $request_body,
                'response_body' => $response_body,
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%d', '%s', '%s', '%s', '%s')
        );
    }
}
