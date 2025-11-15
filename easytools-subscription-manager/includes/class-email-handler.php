<?php
/**
 * Email Handler - Beautiful HTML emails for user notifications
 */

if (!defined('ABSPATH')) {
    exit;
}

class Easytools_Email_Handler {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Override WordPress default email from address
        add_filter('wp_mail_from', array($this, 'custom_mail_from'));
        add_filter('wp_mail_from_name', array($this, 'custom_mail_from_name'));
        // Note: wp_mail_content_type filter is applied/removed per email to avoid conflicts
    }

    /**
     * Custom email "from" address
     */
    public function custom_mail_from($original_email_address) {
        $custom_email = get_option('easytools_email_from_address', '');

        if (!empty($custom_email) && is_email($custom_email)) {
            return $custom_email;
        }

        return $original_email_address;
    }

    /**
     * Custom email "from" name
     */
    public function custom_mail_from_name($original_email_from) {
        $custom_name = get_option('easytools_email_from_name', '');

        if (!empty($custom_name)) {
            return $custom_name;
        }

        return $original_email_from;
    }

    /**
     * Set email content type to HTML
     */
    public function set_html_content_type() {
        return 'text/html';
    }

    /**
     * Send test email (for testing email configuration without creating users)
     */
    public function send_test_email($test_email, $test_username = 'testuser') {
        // Generate a dummy reset URL for testing purposes
        $reset_url = wp_login_url() . '?action=rp&key=TEST_KEY_12345';
        $login_url = wp_login_url();
        $site_name = get_bloginfo('name');
        $brand_color = get_option('easytools_email_brand_color', '#05c7aa');

        // Get customizable email content (with defaults)
        $subject_template = get_option('easytools_email_subject', '[{site_name}] Welcome! Set Your Password');
        $heading_template = get_option('easytools_email_heading', '🎉 Welcome to {site_name}!');
        $message_template = get_option('easytools_email_message', "Your account has been successfully created. We're excited to have you on board!\n\nTo complete your account setup and access your dashboard, please set your password by clicking the button below.");
        $button_text = get_option('easytools_email_button_text', 'Set Your Password');

        // Replace placeholders
        $replacements = array(
            '{username}' => $test_username,
            '{site_name}' => $site_name,
            '{login_url}' => $login_url
        );

        $subject = str_replace(array_keys($replacements), array_values($replacements), $subject_template);
        $heading = str_replace(array_keys($replacements), array_values($replacements), $heading_template);
        $message = str_replace(array_keys($replacements), array_values($replacements), $message_template);

        // Add test notice to message
        $message .= "\n\n[This is a test email - the password reset link is a placeholder and will not work]";

        $email_body = $this->get_welcome_email_template(array(
            'username' => $test_username,
            'reset_url' => $reset_url,
            'login_url' => $login_url,
            'site_name' => $site_name,
            'brand_color' => $brand_color,
            'heading' => $heading,
            'message' => $message,
            'button_text' => $button_text
        ));

        $headers = array('Content-Type: text/html; charset=UTF-8');

        // Apply HTML content type filter only for this email
        add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));

        error_log("Easytools Email: Sending test email to {$test_email}");
        $sent = wp_mail($test_email, $subject, $email_body, $headers);

        // IMPORTANT: Remove the filter immediately after sending to avoid conflicts
        remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));

        if ($sent) {
            error_log("Easytools Email: Test email sent successfully to {$test_email}");
        } else {
            error_log("Easytools Email: Failed to send test email to {$test_email}");
            // Log additional wp_mail debug info
            global $phpmailer;
            if (isset($phpmailer)) {
                error_log("Easytools Email: PHPMailer error: " . $phpmailer->ErrorInfo);
            }
        }

        return $sent;
    }

    /**
     * Send new user welcome email with password reset link
     */
    public function send_new_user_email($user_id, $user_email, $user_login) {
        error_log("Easytools Email: ========== STARTING send_new_user_email ==========");
        error_log("Easytools Email: Parameters - user_id: {$user_id}, email: {$user_email}, login: {$user_login}");

        // IMPORTANT: Get a fresh user object from database to ensure all data is current
        // This is critical for password reset key generation
        error_log("Easytools Email: Clearing user cache...");
        wp_cache_delete($user_id, 'users');
        wp_cache_delete($user_login, 'userlogins');

        error_log("Easytools Email: Fetching user from database...");
        $user = get_user_by('id', $user_id);

        if (!$user) {
            error_log('Easytools Email: ERROR - User not found for ID ' . $user_id);
            return false;
        }
        error_log("Easytools Email: User found - login: {$user->user_login}, email: {$user->user_email}");

        // Generate password reset key
        error_log("Easytools Email: Generating password reset key...");
        $key = get_password_reset_key($user);

        if (is_wp_error($key)) {
            error_log('Easytools Email: ERROR - Failed to generate password reset key - ' . $key->get_error_message());
            return false;
        }
        error_log("Easytools Email: Password reset key generated: " . substr($key, 0, 10) . "...");

        // Verify the key was saved (WordPress stores it in wp_users table, NOT user meta)
        global $wpdb;
        $stored_key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE ID = %d", $user_id));
        if (empty($stored_key)) {
            error_log('Easytools Email: WARNING - Password reset key was not automatically saved');
            error_log('Easytools Email: Manually saving password reset key to database...');

            // Manually save the key (WordPress should do this but sometimes fails)
            // WordPress stores activation keys as: {timestamp}:{hashed_key}
            $hashed = time() . ':' . wp_hash($key);  // Use wp_hash() NOT wp_hash_password()
            $result = $wpdb->update(
                $wpdb->users,
                array('user_activation_key' => $hashed),
                array('ID' => $user_id)
            );

            if ($result === false) {
                error_log('Easytools Email: ERROR - Failed to manually save password reset key to database');
                error_log('Easytools Email: Database error: ' . $wpdb->last_error);
                return false;
            }

            error_log('Easytools Email: Password reset key manually saved successfully');

            // The key we use in the URL is the unhashed version
            // WordPress will hash it again when checking, so we use the original $key
        } else {
            error_log("Easytools Email: Password reset key verified in database");
        }

        error_log("Easytools Email: Password reset key generated successfully for {$user_login}");

        error_log("Easytools Email: Building email URLs and content...");
        $reset_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
        $login_url = wp_login_url();
        $site_name = get_bloginfo('name');
        $brand_color = get_option('easytools_email_brand_color', '#05c7aa');
        error_log("Easytools Email: Reset URL: {$reset_url}");
        error_log("Easytools Email: Site name: {$site_name}");
        error_log("Easytools Email: Brand color: {$brand_color}");

        // Get customizable email content (with defaults)
        error_log("Easytools Email: Loading email templates from options...");
        $subject_template = get_option('easytools_email_subject', '[{site_name}] Welcome! Set Your Password');
        $heading_template = get_option('easytools_email_heading', '🎉 Welcome to {site_name}!');
        $message_template = get_option('easytools_email_message', "Your account has been successfully created. We're excited to have you on board!\n\nTo complete your account setup and access your dashboard, please set your password by clicking the button below.");
        $button_text = get_option('easytools_email_button_text', 'Set Your Password');
        error_log("Easytools Email: Templates loaded successfully");

        // Replace placeholders
        error_log("Easytools Email: Replacing placeholders in templates...");
        $replacements = array(
            '{username}' => $user_login,
            '{site_name}' => $site_name,
            '{login_url}' => $login_url
        );

        $subject = str_replace(array_keys($replacements), array_values($replacements), $subject_template);
        $heading = str_replace(array_keys($replacements), array_values($replacements), $heading_template);
        $message = str_replace(array_keys($replacements), array_values($replacements), $message_template);
        error_log("Easytools Email: Subject: {$subject}");

        error_log("Easytools Email: Generating HTML email template...");
        $email_body = $this->get_welcome_email_template(array(
            'username' => $user_login,
            'reset_url' => $reset_url,
            'login_url' => $login_url,
            'site_name' => $site_name,
            'brand_color' => $brand_color,
            'heading' => $heading,
            'message' => $message,
            'button_text' => $button_text
        ));
        error_log("Easytools Email: HTML template generated, length: " . strlen($email_body) . " bytes");

        $headers = array('Content-Type: text/html; charset=UTF-8');
        error_log("Easytools Email: Email headers set");

        // Apply HTML content type filter only for this email
        error_log("Easytools Email: Adding HTML content type filter...");
        add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));

        error_log("Easytools Email: Calling wp_mail() - TO: {$user_email}, SUBJECT: {$subject}");
        $sent = wp_mail($user_email, $subject, $email_body, $headers);
        error_log("Easytools Email: wp_mail() returned: " . ($sent ? 'TRUE' : 'FALSE'));

        // IMPORTANT: Remove the filter immediately after sending to avoid conflicts
        error_log("Easytools Email: Removing HTML content type filter...");
        remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));

        if ($sent) {
            error_log("Easytools Email: ========== EMAIL SENT SUCCESSFULLY ==========");
        } else {
            error_log("Easytools Email: ========== EMAIL FAILED ==========");
            error_log("Easytools Email: wp_mail returned FALSE - check WordPress email configuration");

            // Try to get more details about the failure
            global $phpmailer;
            if (isset($phpmailer) && is_object($phpmailer)) {
                error_log("Easytools Email: PHPMailer ErrorInfo: " . $phpmailer->ErrorInfo);
            }
        }

        return $sent;
    }

    /**
     * Get HTML email template for new user welcome
     */
    private function get_welcome_email_template($args) {
        $username = esc_html($args['username']);
        $reset_url = esc_url($args['reset_url']);
        $login_url = esc_url($args['login_url']);
        $site_name = esc_html($args['site_name']);
        $brand_color = esc_attr($args['brand_color']);
        $heading = esc_html($args['heading']);
        $message_text = esc_html($args['message']);
        $button_text = esc_html($args['button_text']);
        $current_year = date('Y');

        // Convert line breaks in message to HTML paragraphs
        $message_paragraphs = array_filter(explode("\n", $message_text));
        $message_html = '';
        foreach ($message_paragraphs as $paragraph) {
            $trimmed = trim($paragraph);
            if (!empty($trimmed)) {
                $message_html .= '<p style="margin: 0 0 20px 0; font-size: 18px; line-height: 28px; color: #686868;">' . $trimmed . '</p>';
            }
        }

        return <<<HTML
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <title>Welcome to {$site_name}</title>
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap');

        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-font-smoothing: antialiased;
            background-color: #f5f5f5;
            font-family: 'Inter', 'Helvetica', 'Arial', sans-serif;
        }

        table {
            border-collapse: collapse;
        }

        img {
            border: 0;
            outline: none;
            text-decoration: none;
        }

        .email-container {
            max-width: 620px;
            margin: 0 auto;
        }

        .button {
            display: inline-block;
            padding: 15px 35px;
            background-color: {$brand_color};
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
        }

        .button:hover {
            opacity: 0.9;
        }

        @media screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
            }

            .content-padding {
                padding: 25px 20px !important;
            }

            h1 {
                font-size: 28px !important;
                line-height: 36px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table class="email-container" width="620" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <!-- Header -->
                    <tr>
                        <td class="content-padding" style="padding: 40px; text-align: center; border-bottom: 1px solid #f0f0f0;">
                            <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: {$brand_color};">{$site_name}</h2>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td class="content-padding" style="padding: 40px;">
                            <h1 style="margin: 0 0 20px 0; font-size: 32px; font-weight: 900; line-height: 40px; color: #151515; letter-spacing: -0.5px;">
                                {$heading}
                            </h1>

                            {$message_html}

                            <div style="background-color: #f8f9fa; border-left: 4px solid {$brand_color}; padding: 20px; margin: 25px 0; border-radius: 8px;">
                                <p style="margin: 0 0 10px 0; font-size: 14px; font-weight: 600; color: #686868; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Your Username
                                </p>
                                <p style="margin: 0; font-size: 20px; font-weight: 700; color: #151515; font-family: 'Courier New', monospace;">
                                    {$username}
                                </p>
                            </div>

                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px 0;">
                                        <a href="{$reset_url}" class="button" style="display: inline-block; padding: 15px 35px; background-color: {$brand_color}; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                                            {$button_text}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 15px 0; font-size: 16px; line-height: 24px; color: #686868;">
                                After setting your password, you can log in at:
                            </p>

                            <p style="margin: 0 0 25px 0;">
                                <a href="{$login_url}" style="color: {$brand_color}; text-decoration: underline; font-size: 16px; word-break: break-all;">
                                    {$login_url}
                                </a>
                            </p>

                            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin: 25px 0;">
                                <p style="margin: 0; font-size: 14px; line-height: 22px; color: #856404;">
                                    <strong>⚠️ Security Note:</strong> This password setup link will expire in 24 hours. If it expires, you can request a new one on the login page.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; text-align: center; border-radius: 0 0 16px 16px;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; line-height: 20px; color: #686868;">
                                This is an automated message from <strong>{$site_name}</strong>
                            </p>
                            <p style="margin: 0; font-size: 13px; line-height: 18px; color: #999999;">
                                If you didn't create an account, please disregard this email.
                            </p>
                            <p style="margin: 15px 0 0 0; font-size: 12px; color: #999999;">
                                © {$current_year} {$site_name}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
