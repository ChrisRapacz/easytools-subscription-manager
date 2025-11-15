<?php
/**
 * Webhook Tester - Testing tool for Easytools webhooks
 */

if (!defined('ABSPATH')) {
    exit;
}

class Easytools_Webhook_Tester {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_tester_page'), 20);
        add_action('wp_ajax_easytools_test_webhook', array($this, 'handle_test_webhook'));
        add_action('wp_ajax_easytools_generate_signature', array($this, 'generate_signature'));
        add_action('wp_ajax_easytools_send_test_email', array($this, 'send_test_email'));
    }

    /**
     * Add tester page to menu
     */
    public function add_tester_page() {
        add_submenu_page(
            'easytools-subscription',
            __('Webhook Testing', 'easytools-sub'),
            __('🧪 Webhook Testing', 'easytools-sub'),
            'manage_options',
            'easytools-webhook-tester',
            array($this, 'render_tester_page')
        );
    }

    /**
     * Render tester page
     */
    public function render_tester_page() {
        $webhook_url = rest_url('easytools/v1/webhook');
        $signing_key = get_option('easytools_webhook_signing_key', '');
        $api_token = get_option('easytools_api_token', '');
        $webhook_url_with_token = $api_token ? $webhook_url . '?api_token=' . $api_token : $webhook_url;
        $site_url = get_site_url();

        ?>
        <div class="easytools-premium-wrap">
            <div class="easytools-premium-header">
                <h1>🧪 <?php _e('Easytools Webhook Testing', 'easytools-sub'); ?></h1>
                <p><?php _e('Test and debug webhook integrations with Easytools payment platform', 'easytools-sub'); ?></p>
            </div>

            <div class="easytools-tester-container">

                <!-- Section 1: Basic Information -->
                <div class="easytools-premium-card">
                    <h2>📍 <?php _e('Webhook Endpoint', 'easytools-sub'); ?></h2>
                    <div class="endpoint-info">
                        <label><?php _e('Webhook URL (with API Token):', 'easytools-sub'); ?></label>
                        <div class="endpoint-url">
                            <?php
                            $masked_webhook_url = $webhook_url . '?api_token=' . str_repeat('*', 16);
                            ?>
                            <input type="text" id="webhook-url-tester-display" value="<?php echo esc_attr($masked_webhook_url); ?>" readonly onclick="this.select()" />
                            <input type="hidden" id="webhook-url-tester-real" value="<?php echo esc_attr($webhook_url_with_token); ?>">
                            <button class="button" onclick="toggleWebhookUrlTesterVisibility(this)">
                                👁️ <?php _e('Show', 'easytools-sub'); ?>
                            </button>
                            <button class="button copy-btn" onclick="copyWebhookUrlTester()">
                                📋 <?php _e('Copy', 'easytools-sub'); ?>
                            </button>
                        </div>
                        <p class="description" style="margin-top: 8px;">
                            <?php _e('Use this URL in Easytools → API & Webhooks. The API token provides additional security.', 'easytools-sub'); ?>
                        </p>
                    </div>

                    <div class="endpoint-info">
                        <label><?php _e('Signing Key:', 'easytools-sub'); ?></label>
                        <div class="endpoint-url">
                            <?php if ($signing_key): ?>
                            <input type="password" id="signing-key-field" value="<?php echo esc_attr($signing_key); ?>" readonly onclick="this.select()" />
                            <button class="button" onclick="toggleVisibility('signing-key-field', this)">
                                👁️ <?php _e('Show', 'easytools-sub'); ?>
                            </button>
                            <button class="button copy-btn" onclick="copyToClipboard('<?php echo esc_js($signing_key); ?>')">
                                📋 <?php _e('Copy', 'easytools-sub'); ?>
                            </button>
                            <?php else: ?>
                            <input type="text" value="<?php echo esc_attr(__('Not configured', 'easytools-sub')); ?>" readonly />
                            <?php endif; ?>
                        </div>
                        <?php if (!$signing_key): ?>
                        <p class="description warning">
                            ⚠️ <?php _e('Signing key is not configured. Set it in Settings tab.', 'easytools-sub'); ?>
                        </p>
                        <?php endif; ?>
                    </div>

                    <?php if ($api_token): ?>
                    <div class="endpoint-info">
                        <label><?php _e('API Token:', 'easytools-sub'); ?></label>
                        <div class="endpoint-url">
                            <input type="password" id="api-token-field" value="<?php echo esc_attr($api_token); ?>" readonly onclick="this.select()" />
                            <button class="button" onclick="toggleVisibility('api-token-field', this)">
                                👁️ <?php _e('Show', 'easytools-sub'); ?>
                            </button>
                            <button class="button copy-btn" onclick="copyToClipboard('<?php echo esc_js($api_token); ?>')">
                                📋 <?php _e('Copy', 'easytools-sub'); ?>
                            </button>
                        </div>
                        <p class="description" style="margin-top: 8px;">
                            <?php _e('This token is automatically added to the webhook URL above.', 'easytools-sub'); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Section 2: Postman Instructions -->
                <div class="easytools-premium-card">
                    <h2>📖 <?php _e('How to test in Postman', 'easytools-sub'); ?></h2>
                    <p class="description">
                        <?php _e('Postman is a powerful API testing tool. If you don\'t have it yet:', 'easytools-sub'); ?>
                        <a href="https://www.postman.com/downloads/" target="_blank" rel="noopener noreferrer" style="color: #05c7aa; font-weight: 600; text-decoration: none;">
                            <?php _e('Download Postman here', 'easytools-sub'); ?> →
                        </a>
                    </p>
                    <ol class="postman-instructions">
                        <li>
                            <strong><?php _e('Open Postman and create a new request', 'easytools-sub'); ?></strong>
                            <ul>
                                <li><?php _e('Click "New" → "HTTP Request"', 'easytools-sub'); ?></li>
                                <li><?php _e('Method: POST', 'easytools-sub'); ?></li>
                                <?php if ($api_token): ?>
                                <li>
                                    <span><?php _e('URL (with API token):', 'easytools-sub'); ?></span>
                                    <div style="display: inline-flex; align-items: center; gap: 8px; margin-top: 5px;">
                                        <code id="postman-url-with-token-display"><?php echo esc_html($webhook_url . '?api_token=' . str_repeat('*', 16)); ?></code>
                                        <input type="hidden" id="postman-url-with-token-real" value="<?php echo esc_attr($webhook_url_with_token); ?>">
                                        <button class="button button-small" onclick="togglePostmanUrlVisibility('with-token', this)" style="padding: 2px 8px; height: auto;">
                                            👁️ <?php _e('Show', 'easytools-sub'); ?>
                                        </button>
                                        <button class="button button-small" onclick="copyPostmanUrl('with-token')" style="padding: 2px 8px; height: auto;">
                                            📋 <?php _e('Copy', 'easytools-sub'); ?>
                                        </button>
                                    </div>
                                </li>
                                <li style="color: #666; font-size: 0.95em; margin-top: 10px;">
                                    <span><?php _e('Or without API token:', 'easytools-sub'); ?></span>
                                    <div style="display: inline-flex; align-items: center; gap: 8px; margin-top: 5px;">
                                        <code><?php echo esc_html($webhook_url); ?></code>
                                        <button class="button button-small" onclick="copyPostmanUrl('without-token')" style="padding: 2px 8px; height: auto;">
                                            📋 <?php _e('Copy', 'easytools-sub'); ?>
                                        </button>
                                    </div>
                                    <input type="hidden" id="postman-url-without-token-real" value="<?php echo esc_attr($webhook_url); ?>">
                                </li>
                                <?php else: ?>
                                <li>
                                    <span><?php _e('URL:', 'easytools-sub'); ?></span>
                                    <div style="display: inline-flex; align-items: center; gap: 8px; margin-top: 5px;">
                                        <code><?php echo esc_html($webhook_url); ?></code>
                                        <button class="button button-small" onclick="copyPostmanUrl('without-token')" style="padding: 2px 8px; height: auto;">
                                            📋 <?php _e('Copy', 'easytools-sub'); ?>
                                        </button>
                                    </div>
                                    <input type="hidden" id="postman-url-without-token-real" value="<?php echo esc_attr($webhook_url); ?>">
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li>
                            <strong><?php _e('Set Headers:', 'easytools-sub'); ?></strong>
                            <ul>
                                <li><code>Content-Type: application/json</code></li>
                                <li><code>x-webhook-signature: [generated HMAC signature]</code></li>
                            </ul>
                        </li>
                        <li>
                            <strong><?php _e('Select JSON payload from examples below', 'easytools-sub'); ?></strong>
                        </li>
                        <li>
                            <strong><?php _e('Generate HMAC signature using calculator below', 'easytools-sub'); ?></strong>
                        </li>
                        <li>
                            <strong><?php _e('Send request', 'easytools-sub'); ?></strong>
                        </li>
                        <li>
                            <strong><?php _e('Check response and logs in "Webhook Logs" tab', 'easytools-sub'); ?></strong>
                        </li>
                    </ol>
                    <?php if ($api_token): ?>
                    <div class="easytools-info-box" style="background: rgba(5, 199, 170, 0.1); border-left: 4px solid #05c7aa; padding: 15px; border-radius: 8px; margin-top: 15px;">
                        <p style="margin: 0;">
                            <strong>💡 <?php _e('Note:', 'easytools-sub'); ?></strong>
                            <?php _e('API token provides additional security. You can test with or without it, but production webhooks should include the token in the URL.', 'easytools-sub'); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Section 3: Signature Calculator -->
                <div class="easytools-premium-card">
                    <h2>🔐 <?php _e('HMAC SHA256 Signature Calculator', 'easytools-sub'); ?></h2>
                    <p class="description">
                        <?php _e('Paste JSON payload to generate x-webhook-signature for Postman', 'easytools-sub'); ?>
                    </p>

                    <?php if (!$signing_key): ?>
                    <div class="notice notice-error inline">
                        <p>⚠️ <?php _e('You must first set the Signing Key in Settings tab!', 'easytools-sub'); ?></p>
                    </div>
                    <?php else: ?>

                    <div class="signature-calculator">
                        <label><?php _e('JSON Payload:', 'easytools-sub'); ?></label>
                        <textarea id="payload-input" rows="10" placeholder='{"event":"subscription_created","customer_email":"test@example.com",...}' style="width: 100%; font-family: monospace; padding: 10px; border: 2px solid #e1e8ed; border-radius: 8px;"></textarea>

                        <div style="display: flex; gap: 10px; margin-top: 10px; align-items: stretch;">
                            <button type="button" class="button button-primary" onclick="generateSignature()">
                                🔐 <?php _e('Generate Signature', 'easytools-sub'); ?>
                            </button>
                            <button type="button" class="button signature-calculator-button" onclick="copyPayloadInput()">
                                📋 <?php _e('Copy JSON', 'easytools-sub'); ?>
                            </button>
                        </div>

                        <div id="signature-result" style="display:none;">
                            <label><?php _e('x-webhook-signature:', 'easytools-sub'); ?></label>
                            <div class="endpoint-url">
                                <input type="text" id="signature-output" readonly onclick="this.select()" />
                                <button class="button copy-btn" onclick="copySignature()">
                                    📋 <?php _e('Copy', 'easytools-sub'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>
                </div>

                <!-- Section 4: Example Payloads -->
                <div class="easytools-premium-card">
                    <h2>📋 <?php _e('Example Payloads', 'easytools-sub'); ?></h2>
                    <p class="description">
                        <?php _e('Use these example payloads for testing webhooks. Copy, modify in your editor, or send directly.', 'easytools-sub'); ?>
                    </p>

                    <div style="background: rgba(5, 199, 170, 0.1); border: 2px solid #05c7aa; border-radius: 8px; padding: 15px; margin: 20px 0;">
                        <label for="custom-test-email" style="display: block; font-weight: 600; margin-bottom: 8px; color: #0e534c;">
                            📧 <?php _e('Custom Test Email:', 'easytools-sub'); ?>
                        </label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="email"
                                   id="custom-test-email"
                                   name="custom-test-email"
                                   value=""
                                   placeholder="test@example.com"
                                   autocomplete="off"
                                   style="flex: 1; padding: 8px 12px; border: 2px solid #05c7aa; border-radius: 6px; font-size: 14px; background: white; cursor: text;">
                            <button type="button" class="button button-primary" onclick="updateAllPayloadsWithEmail()">
                                🔄 <?php _e('Update All Payloads', 'easytools-sub'); ?>
                            </button>
                        </div>
                        <p class="description" style="margin: 8px 0 0 0;">
                            <?php _e('Enter your test email and click "Update All Payloads" to apply it to all examples. Your email is remembered between sessions.', 'easytools-sub'); ?>
                        </p>
                    </div>

                    <?php
                    $test_email = 'test@example.com';
                    $payloads = $this->get_example_payloads($test_email);

                    foreach ($payloads as $event_name => $payload_data):
                    ?>

                    <div class="payload-example">
                        <h3>
                            <?php echo esc_html($payload_data['icon']); ?>
                            <?php echo esc_html($payload_data['title']); ?>
                        </h3>
                        <p class="description"><?php echo esc_html($payload_data['description']); ?></p>

                        <div class="payload-actions">
                            <button class="button" onclick="copyPayload('payload-<?php echo esc_attr($event_name); ?>')">
                                📋 <?php _e('Copy JSON', 'easytools-sub'); ?>
                            </button>
                            <button class="button" onclick="useInCalculator('payload-<?php echo esc_attr($event_name); ?>')">
                                🔐 <?php _e('Use in calculator', 'easytools-sub'); ?>
                            </button>
                            <button class="button button-primary" onclick="sendTestWebhook('<?php echo esc_js($event_name); ?>')">
                                🚀 <?php _e('Send test webhook', 'easytools-sub'); ?>
                            </button>
                        </div>

                        <!-- READONLY TEXTAREA -->
                        <textarea class="payload-code" id="payload-<?php echo esc_attr($event_name); ?>" rows="15" readonly><?php echo esc_html(json_encode($payload_data['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                    </div>

                    <?php endforeach; ?>
                </div>

                <!-- Section 5: Developer Mode -->
                <div class="easytools-premium-card">
                    <h2>⚙️ <?php _e('Developer Mode', 'easytools-sub'); ?></h2>
                    <p class="description">
                        <?php _e('In developer mode, webhook signature verification is DISABLED. Use only for testing!', 'easytools-sub'); ?>
                    </p>

                    <label>
                        <input type="checkbox" id="dev-mode" <?php checked(get_option('easytools_dev_mode', 'no'), 'yes'); ?> onchange="toggleDevMode(this.checked)" />
                        <strong><?php _e('Enable developer mode (disable signature verification)', 'easytools-sub'); ?></strong>
                    </label>

                    <div class="notice notice-warning inline" style="margin-top: 15px;">
                        <p>⚠️ <?php _e('WARNING: Disable developer mode in production! This is dangerous.', 'easytools-sub'); ?></p>
                    </div>
                </div>

                <!-- Section 6: Test Email System -->
                <div class="easytools-premium-card">
                    <h2>📧 <?php _e('Test Email System', 'easytools-sub'); ?></h2>
                    <p class="description">
                        <?php _e('Test your email configuration independently of webhooks. This sends a welcome email directly to verify your email settings are working correctly.', 'easytools-sub'); ?>
                    </p>

                    <div style="background: rgba(5, 199, 170, 0.1); border-left: 4px solid #05c7aa; padding: 15px; border-radius: 8px; margin: 20px 0;">
                        <p style="margin: 0 0 10px 0; font-weight: 600; color: #0e534c;">
                            💡 <?php _e('Why separate email testing?', 'easytools-sub'); ?>
                        </p>
                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #666;">
                            <?php _e('Test webhooks sent via the "Send test webhook" button use internal WordPress HTTP requests which can have different behavior than real external webhooks. Use this button to test your email configuration directly without webhook processing.', 'easytools-sub'); ?>
                        </p>
                    </div>

                    <div style="margin-top: 20px;">
                        <label for="test-email-input" style="display: block; font-weight: 600; margin-bottom: 8px;">
                            <?php _e('Email Address:', 'easytools-sub'); ?>
                        </label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="email"
                                   id="test-email-input"
                                   placeholder="your.email@example.com"
                                   style="flex: 1; padding: 10px 12px; border: 2px solid #e1e8ed; border-radius: 8px; font-size: 14px;"
                                   value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>">
                            <button type="button" class="button button-primary" onclick="sendTestEmail()" style="padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                                📧 <?php _e('Send Test Email', 'easytools-sub'); ?>
                            </button>
                        </div>
                        <p class="description" style="margin: 8px 0 0 0;">
                            <?php _e('A welcome email will be sent to this address. Check your inbox (and spam folder) after clicking the button.', 'easytools-sub'); ?>
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <style>
            /* Premium Wrapper Styles */
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

            .easytools-tester-container {
                max-width: 1200px;
            }

            .easytools-premium-card {
                background: white;
                border-radius: 12px;
                padding: 30px;
                margin-bottom: 25px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            }

            .easytools-premium-card h2 {
                margin-top: 0;
                padding-bottom: 15px;
                border-bottom: 2px solid #f0f0f1;
                font-size: 20px;
                line-height: 1.4;
                color: #2c3e50;
            }

            .endpoint-info {
                margin: 20px 0;
            }

            .endpoint-info label {
                display: block;
                font-weight: 600;
                margin-bottom: 8px;
            }

            .endpoint-url {
                display: flex;
                gap: 10px;
            }

            .endpoint-url input {
                flex: 1;
                padding: 8px 12px;
                font-family: monospace;
                font-size: 13px;
                border: 2px solid #e1e8ed;
                border-radius: 8px;
            }

            .endpoint-url input:focus {
                border-color: #05c7aa;
                box-shadow: 0 0 0 3px rgba(5, 199, 170, 0.1);
                outline: none;
            }

            .copy-btn {
                white-space: nowrap;
                background: #05c7aa;
                border: none;
                color: white;
                padding: 8px 16px;
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-weight: 500;
            }

            .copy-btn:hover {
                background: #00a18b;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(5, 199, 170, 0.3);
            }

            .postman-instructions {
                line-height: 1.8;
            }

            .postman-instructions li {
                margin-bottom: 15px;
            }

            .postman-instructions code {
                background: #e0f5f2;
                padding: 3px 8px;
                border-radius: 4px;
                font-size: 13px;
                color: #0e534c;
            }

            .signature-calculator {
                margin-top: 15px;
            }

            .signature-calculator label {
                display: block;
                font-weight: 600;
                margin-bottom: 8px;
            }

            .signature-calculator .button-primary,
            .signature-calculator .signature-calculator-button {
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-weight: 600;
                margin-top: 15px;
            }

            .signature-calculator .button-primary {
                background: #05c7aa;
                border: none;
                color: white;
                box-shadow: 0 4px 15px rgba(5, 199, 170, 0.3);
            }

            .signature-calculator .button-primary:hover {
                background: #00a18b;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(5, 199, 170, 0.4);
            }

            .signature-calculator .signature-calculator-button {
                border: 2px solid #05c7aa;
                background: white;
                color: #05c7aa;
            }

            .signature-calculator .signature-calculator-button:hover {
                background: #05c7aa;
                color: white;
                transform: translateY(-2px);
            }

            #signature-result {
                margin-top: 20px;
                padding: 15px;
                background: rgba(5, 199, 170, 0.1);
                border: 2px solid #05c7aa;
                border-radius: 8px;
            }

            #signature-result label {
                font-weight: 600;
                color: #0e534c;
                margin-bottom: 8px;
            }

            .payload-example {
                background: #f8fffe;
                border: 2px solid #d1f3ee;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
            }

            .payload-example h3 {
                margin-top: 0;
                margin-bottom: 10px;
                color: #0e534c;
                font-size: 18px;
                line-height: 1.4;
            }

            .payload-actions {
                margin: 15px 0;
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .payload-actions .button {
                border: 2px solid #05c7aa;
                color: #05c7aa;
                border-radius: 6px;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .payload-actions .button:hover {
                background: #05c7aa;
                color: white;
                transform: translateY(-2px);
            }

            .payload-actions .button-primary {
                background: #05c7aa;
                border: none;
                color: white;
                box-shadow: 0 4px 15px rgba(5, 199, 170, 0.3);
            }

            .payload-actions .button-primary:hover {
                background: #00a18b;
                box-shadow: 0 6px 20px rgba(5, 199, 170, 0.4);
            }

            .payload-code {
                background: #282c34;
                color: #abb2bf;
                padding: 15px;
                border-radius: 8px;
                overflow-x: auto;
                font-size: 13px;
                line-height: 1.6;
                font-family: 'Courier New', monospace;
                width: 100%;
                border: 1px solid #1e2127;
                cursor: default;
            }

            .description.warning {
                color: #d63638;
                font-weight: 600;
            }

            .notice.inline {
                padding: 12px;
                margin: 15px 0;
                border-radius: 6px;
            }
        </style>

        <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('✅ <?php echo esc_js(__('Copied to clipboard!', 'easytools-sub')); ?>');
            });
        }

        function toggleWebhookUrlTesterVisibility(button) {
            const displayInput = document.getElementById('webhook-url-tester-display');
            const realInput = document.getElementById('webhook-url-tester-real');

            if (displayInput.value.includes('***')) {
                // Show real URL
                displayInput.value = realInput.value;
                button.textContent = '🙈 <?php echo esc_js(__('Hide', 'easytools-sub')); ?>';
            } else {
                // Hide token
                const maskedUrl = realInput.value.replace(/api_token=[^&]+/, 'api_token=****************');
                displayInput.value = maskedUrl;
                button.textContent = '👁️ <?php echo esc_js(__('Show', 'easytools-sub')); ?>';
            }
        }

        function copyWebhookUrlTester() {
            const realInput = document.getElementById('webhook-url-tester-real');
            navigator.clipboard.writeText(realInput.value).then(() => {
                alert('✅ <?php echo esc_js(__('Webhook URL copied to clipboard!', 'easytools-sub')); ?>');
            }).catch(err => {
                alert('❌ <?php echo esc_js(__('Failed to copy to clipboard', 'easytools-sub')); ?>');
            });
        }

        function toggleVisibility(fieldId, button) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                button.textContent = '🙈 <?php echo esc_js(__('Hide', 'easytools-sub')); ?>';
            } else {
                field.type = 'password';
                button.textContent = '👁️ <?php echo esc_js(__('Show', 'easytools-sub')); ?>';
            }
        }

        function togglePostmanUrlVisibility(type, button) {
            const displayCode = document.getElementById('postman-url-' + type + '-display');
            const realInput = document.getElementById('postman-url-' + type + '-real');

            if (displayCode.textContent.includes('***')) {
                // Show real URL
                displayCode.textContent = realInput.value;
                button.textContent = '🙈 <?php echo esc_js(__('Hide', 'easytools-sub')); ?>';
            } else {
                // Hide token
                const maskedUrl = realInput.value.replace(/api_token=[^&]+/, 'api_token=****************');
                displayCode.textContent = maskedUrl;
                button.textContent = '👁️ <?php echo esc_js(__('Show', 'easytools-sub')); ?>';
            }
        }

        function copyPostmanUrl(type) {
            const realInput = document.getElementById('postman-url-' + type + '-real');
            navigator.clipboard.writeText(realInput.value).then(() => {
                alert('✅ <?php echo esc_js(__('URL copied to clipboard!', 'easytools-sub')); ?>');
            }).catch(err => {
                alert('❌ <?php echo esc_js(__('Failed to copy to clipboard', 'easytools-sub')); ?>');
            });
        }

        // Simple email field - just load saved value on page load
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('custom-test-email');
            if (!emailInput) return;

            // Load saved email on page load
            const savedEmail = localStorage.getItem('easytools_test_email');
            if (savedEmail) {
                emailInput.value = savedEmail;
            } else {
                emailInput.value = 'test@example.com';
            }
        });

        function updateAllPayloadsWithEmail() {
            const newEmail = document.getElementById('custom-test-email').value;
            if (!newEmail || !newEmail.includes('@')) {
                alert('❌ <?php echo esc_js(__('Please enter a valid email address', 'easytools-sub')); ?>');
                return;
            }

            // Save to localStorage
            localStorage.setItem('easytools_test_email', newEmail);

            // Find all payload textareas
            const textareas = document.querySelectorAll('.payload-code');
            let updatedCount = 0;

            textareas.forEach(textarea => {
                try {
                    // Parse the JSON
                    const payload = JSON.parse(textarea.value);

                    // Update email fields
                    if (payload.customer_email) {
                        payload.customer_email = newEmail;
                        updatedCount++;
                    }

                    // Update the textarea with formatted JSON
                    textarea.value = JSON.stringify(payload, null, 4);
                } catch (e) {
                    console.error('Failed to parse payload:', e);
                }
            });

            alert('✅ <?php echo esc_js(__('Updated', 'easytools-sub')); ?> ' + updatedCount + ' <?php echo esc_js(__('payloads with new email!', 'easytools-sub')); ?>');
        }


        function copyPayload(elementId) {
            const element = document.getElementById(elementId);
            if (!element) {
                alert('❌ <?php echo esc_js(__('Element not found!', 'easytools-sub')); ?>');
                return;
            }
            const text = element.value || element.textContent;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('✅ <?php echo esc_js(__('Payload copied to clipboard!', 'easytools-sub')); ?>');
                }).catch(err => {
                    // Fallback for older browsers
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.top = "-9999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    alert('✅ <?php echo esc_js(__('Payload copied to clipboard!', 'easytools-sub')); ?>');
                } else {
                    alert('❌ <?php echo esc_js(__('Failed to copy to clipboard', 'easytools-sub')); ?>');
                }
            } catch (err) {
                alert('❌ <?php echo esc_js(__('Failed to copy to clipboard', 'easytools-sub')); ?>');
            }

            document.body.removeChild(textArea);
        }

        function useInCalculator(elementId) {
            const element = document.getElementById(elementId);
            const text = element.value;
            document.getElementById('payload-input').value = text;
            document.getElementById('payload-input').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function copySignature() {
            const signature = document.getElementById('signature-output').value;
            navigator.clipboard.writeText(signature).then(() => {
                alert('✅ <?php echo esc_js(__('Signature copied to clipboard!', 'easytools-sub')); ?>');
            });
        }

        function copyPayloadInput() {
            const payload = document.getElementById('payload-input').value;
            if (!payload.trim()) {
                alert('⚠️ <?php echo esc_js(__('Payload is empty!', 'easytools-sub')); ?>');
                return;
            }
            navigator.clipboard.writeText(payload).then(() => {
                alert('✅ <?php echo esc_js(__('JSON payload copied to clipboard!', 'easytools-sub')); ?>');
            }).catch(err => {
                alert('❌ <?php echo esc_js(__('Failed to copy to clipboard', 'easytools-sub')); ?>');
            });
        }

        function generateSignature() {
            const payload = document.getElementById('payload-input').value;

            if (!payload.trim()) {
                alert('⚠️ <?php echo esc_js(__('Paste JSON payload!', 'easytools-sub')); ?>');
                return;
            }

            // Check if valid JSON
            try {
                JSON.parse(payload);
            } catch (e) {
                alert('❌ <?php echo esc_js(__('Invalid JSON format!', 'easytools-sub')); ?>');
                return;
            }

            // Send AJAX request
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'easytools_generate_signature',
                    payload: payload,
                    nonce: '<?php echo wp_create_nonce('easytools_tester'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        document.getElementById('signature-output').value = response.data.signature;
                        document.getElementById('signature-result').style.display = 'block';
                    } else {
                        alert('❌ ' + response.data.message);
                    }
                },
                error: function() {
                    alert('❌ <?php echo esc_js(__('Error generating signature', 'easytools-sub')); ?>');
                }
            });
        }

        function sendTestWebhook(eventType) {
            if (!confirm('🚀 <?php echo esc_js(__('Send test webhook?', 'easytools-sub')); ?>')) {
                return;
            }

            const payloadElement = document.getElementById('payload-' + eventType);
            const payload = payloadElement.value;

            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'easytools_test_webhook',
                    payload: payload,
                    nonce: '<?php echo wp_create_nonce('easytools_tester'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ ' + response.data.message + '\n\n<?php echo esc_js(__('Check result in "Webhook Logs" tab', 'easytools-sub')); ?>');
                    } else {
                        alert('❌ ' + response.data.message);
                    }
                },
                error: function() {
                    alert('❌ <?php echo esc_js(__('Error sending webhook', 'easytools-sub')); ?>');
                }
            });
        }

        function toggleDevMode(enabled) {
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'easytools_toggle_dev_mode',
                    enabled: enabled ? 'yes' : 'no',
                    nonce: '<?php echo wp_create_nonce('easytools_tester'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        if (enabled) {
                            alert('⚠️ <?php echo esc_js(__('Developer mode ENABLED. Signature verification disabled!', 'easytools-sub')); ?>');
                        } else {
                            alert('✅ <?php echo esc_js(__('Developer mode DISABLED. Signature verification enabled.', 'easytools-sub')); ?>');
                        }
                    }
                }
            });
        }

        function sendTestEmail() {
            const emailInput = document.getElementById('test-email-input');
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

    /**
     * Get example payloads
     */
    private function get_example_payloads($test_email) {
        return array(
            'product_assigned' => array(
                'icon' => '✅',
                'title' => __('Product Assigned', 'easytools-sub'),
                'description' => __('Webhook sent when product is assigned to user (most important event)', 'easytools-sub'),
                'payload' => array(
                    'event' => 'product_assigned',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'product_name' => 'Premium Subscription',
                    'subscription_type' => 'monthly',
                    'price' => '99.00',
                    'currency' => 'PLN',
                    'renewal_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
                    'subscription_one_time' => false,
                    'trial_ends_at' => null
                )
            ),

            'subscription_created' => array(
                'icon' => '🆕',
                'title' => __('Subscription Created', 'easytools-sub'),
                'description' => __('Webhook sent when customer purchases subscription', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_created',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'product_name' => 'Premium Subscription',
                    'subscription_type' => 'monthly',
                    'price' => '99.00',
                    'currency' => 'PLN',
                    'renewal_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
                    'subscription_one_time' => false,
                    'trial_ends_at' => null
                )
            ),

            'subscription_expired' => array(
                'icon' => '❌',
                'title' => __('Subscription Expired', 'easytools-sub'),
                'description' => __('Webhook sent when subscription expires', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_expired',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'subscription_end_date' => date('Y-m-d H:i:s')
                )
            ),

            'subscription_cancelled' => array(
                'icon' => '🚫',
                'title' => __('Subscription Cancelled', 'easytools-sub'),
                'description' => __('Webhook sent when user cancels subscription (access continues until period end)', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_cancelled',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'cancellation_reason' => 'Customer request',
                    'cancelled_at' => date('Y-m-d H:i:s')
                )
            ),

            'subscription_renewed' => array(
                'icon' => '🔄',
                'title' => __('Subscription Renewed', 'easytools-sub'),
                'description' => __('Webhook sent when subscription is renewed', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_renewed',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'subscription_type' => 'monthly',
                    'price' => '99.00',
                    'currency' => 'PLN',
                    'renewal_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
                    'renewed_at' => date('Y-m-d H:i:s')
                )
            ),

            'subscription_paused' => array(
                'icon' => '⏸️',
                'title' => __('Subscription Paused', 'easytools-sub'),
                'description' => __('Webhook sent when subscription is paused', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_paused',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'paused_at' => date('Y-m-d H:i:s')
                )
            ),

            'subscription_plan_changed' => array(
                'icon' => '🔀',
                'title' => __('Subscription Plan Changed', 'easytools-sub'),
                'description' => __('Webhook sent when user changes subscription plan', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_plan_changed',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'old_product_id' => 'prod_67890',
                    'new_product_id' => 'prod_99999',
                    'old_subscription_type' => 'monthly',
                    'new_subscription_type' => 'yearly',
                    'price' => '999.00',
                    'currency' => 'PLN',
                    'changed_at' => date('Y-m-d H:i:s')
                )
            ),

            'subscription_deleted' => array(
                'icon' => '🗑️',
                'title' => __('Subscription Deleted', 'easytools-sub'),
                'description' => __('Webhook sent when subscription is permanently deleted', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_deleted',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'deleted_at' => date('Y-m-d H:i:s')
                )
            ),

            'product_access_expired' => array(
                'icon' => '⛔',
                'title' => __('Product Access Expired', 'easytools-sub'),
                'description' => __('Webhook sent when user product access expires', 'easytools-sub'),
                'payload' => array(
                    'event' => 'product_access_expired',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'product_name' => 'Premium Subscription',
                    'access_expired_at' => date('Y-m-d H:i:s')
                )
            ),

            'one_time_purchase' => array(
                'icon' => '💰',
                'title' => __('One Time Purchase (Lifetime)', 'easytools-sub'),
                'description' => __('Webhook for lifetime purchase (no renewals)', 'easytools-sub'),
                'payload' => array(
                    'event' => 'product_assigned',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_lifetime',
                    'product_name' => 'Lifetime Access',
                    'subscription_type' => 'lifetime',
                    'price' => '999.00',
                    'currency' => 'PLN',
                    'subscription_one_time' => true,
                    'renewal_date' => null,
                    'trial_ends_at' => null
                )
            ),

            'subscription_resumed' => array(
                'icon' => '▶️',
                'title' => __('Subscription Resumed', 'easytools-sub'),
                'description' => __('Webhook sent when canceled subscription is resumed (reactivates access)', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_resumed',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'product_name' => 'Premium Subscription',
                    'subscription_type' => 'monthly',
                    'price' => '99.00',
                    'currency' => 'PLN',
                    'renewal_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
                    'resumed_at' => date('Y-m-d H:i:s')
                )
            ),

            'single_product_bought' => array(
                'icon' => '🛒',
                'title' => __('Single Product Bought', 'easytools-sub'),
                'description' => __('Webhook sent when customer purchases a one-time product', 'easytools-sub'),
                'payload' => array(
                    'event' => 'single_product_bought',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_single',
                    'product_name' => 'One-Time Product',
                    'price' => '49.00',
                    'currency' => 'PLN',
                    'purchase_date' => date('Y-m-d H:i:s')
                )
            ),

            'product_access_expiring' => array(
                'icon' => '⚠️',
                'title' => __('Product Access Expiring', 'easytools-sub'),
                'description' => __('Informational webhook sent before access expires (no action taken by plugin)', 'easytools-sub'),
                'payload' => array(
                    'event' => 'product_access_expiring',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'product_name' => 'Premium Subscription',
                    'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days'))
                )
            ),

            'subscription_renewal_failed' => array(
                'icon' => '💳',
                'title' => __('Subscription Renewal Failed', 'easytools-sub'),
                'description' => __('Informational webhook sent when payment fails (access continues until expired)', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_renewal_failed',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'product_name' => 'Premium Subscription',
                    'failed_at' => date('Y-m-d H:i:s'),
                    'next_retry' => date('Y-m-d H:i:s', strtotime('+3 days'))
                )
            ),

            'subscription_renewal_upcoming' => array(
                'icon' => '📅',
                'title' => __('Subscription Renewal Upcoming', 'easytools-sub'),
                'description' => __('Informational webhook sent before upcoming renewal (no action taken by plugin)', 'easytools-sub'),
                'payload' => array(
                    'event' => 'subscription_renewal_upcoming',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe',
                    'customer_id' => 'cust_12345',
                    'product_id' => 'prod_67890',
                    'product_name' => 'Premium Subscription',
                    'renewal_date' => date('Y-m-d H:i:s', strtotime('+7 days')),
                    'price' => '99.00',
                    'currency' => 'PLN'
                )
            ),

            'customer_data_changed' => array(
                'icon' => '👤',
                'title' => __('Customer Data Changed', 'easytools-sub'),
                'description' => __('Informational webhook sent when customer updates their data (no action taken)', 'easytools-sub'),
                'payload' => array(
                    'event' => 'customer_data_changed',
                    'customer_email' => $test_email,
                    'customer_name' => 'John Doe Updated',
                    'customer_id' => 'cust_12345',
                    'changed_at' => date('Y-m-d H:i:s')
                )
            )
        );
    }

    /**
     * AJAX: Generate HMAC signature
     * FIXED: Normalize JSON before generating HMAC
     */
    public function generate_signature() {
        check_ajax_referer('easytools_tester', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'easytools-sub')));
        }

        $payload = isset($_POST['payload']) ? wp_unslash($_POST['payload']) : '';
        $signing_key = get_option('easytools_webhook_signing_key', '');

        if (empty($signing_key)) {
            wp_send_json_error(array('message' => __('Signing key is not configured', 'easytools-sub')));
        }

        if (empty($payload)) {
            wp_send_json_error(array('message' => __('Payload is empty', 'easytools-sub')));
        }

        // CRITICAL FIX: Normalize JSON before HMAC (same as webhook handler)
        $json_data = json_decode($payload, true);
        if ($json_data !== null) {
            // Re-encode without pretty printing to ensure consistency
            $normalized_payload = json_encode($json_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            wp_send_json_error(array('message' => __('Invalid JSON format', 'easytools-sub')));
            return;
        }

        // Generate HMAC SHA256 signature
        $signature = hash_hmac('sha256', $normalized_payload, $signing_key);

        wp_send_json_success(array(
            'signature' => $signature,
            'message' => __('Signature generated successfully', 'easytools-sub'),
            'normalized_payload' => $normalized_payload
        ));
    }

    /**
     * AJAX: Send test webhook
     */
    public function handle_test_webhook() {
        check_ajax_referer('easytools_tester', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'easytools-sub')));
        }

        $payload = isset($_POST['payload']) ? wp_unslash($_POST['payload']) : '';

        if (empty($payload)) {
            wp_send_json_error(array('message' => __('Payload is empty', 'easytools-sub')));
        }

        // Normalize and generate signature
        $json_data = json_decode($payload, true);
        if ($json_data !== null) {
            $normalized_payload = json_encode($json_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $normalized_payload = $payload;
        }

        $signing_key = get_option('easytools_webhook_signing_key', '');
        $signature = !empty($signing_key) ? hash_hmac('sha256', $normalized_payload, $signing_key) : '';

        // Send webhook to own endpoint
        $webhook_url = rest_url('easytools/v1/webhook');

        // Add API token if configured
        $api_token = get_option('easytools_api_token', '');
        if (!empty($api_token)) {
            $webhook_url = add_query_arg('api_token', $api_token, $webhook_url);
        }

        $response = wp_remote_post($webhook_url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-webhook-signature' => $signature
            ),
            'body' => $normalized_payload,
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        if ($response_code === 200) {
            wp_send_json_success(array(
                'message' => __('Webhook sent successfully!', 'easytools-sub'),
                'response' => json_decode($response_body, true)
            ));
        } else {
            wp_send_json_error(array(
                'message' => sprintf(__('HTTP Error %d', 'easytools-sub'), $response_code),
                'response' => $response_body
            ));
        }
    }

    /**
     * AJAX: Send test email
     */
    public function send_test_email() {
        check_ajax_referer('easytools_tester', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'easytools-sub')));
        }

        $test_email = isset($_POST['test_email']) ? sanitize_email($_POST['test_email']) : '';

        if (empty($test_email) || !is_email($test_email)) {
            wp_send_json_error(array('message' => __('Please provide a valid email address', 'easytools-sub')));
        }

        // Check if email handler is available
        if (!class_exists('Easytools_Email_Handler')) {
            wp_send_json_error(array('message' => __('Email handler not found', 'easytools-sub')));
        }

        // Send test email using the dedicated test function
        $email_handler = Easytools_Email_Handler::get_instance();
        $test_username = 'demo_user';

        $result = $email_handler->send_test_email($test_email, $test_username);

        if ($result) {
            wp_send_json_success(array(
                'message' => sprintf(__('Test email sent successfully to %s! Check your inbox (and spam folder).', 'easytools-sub'), $test_email)
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to send test email. Please check: 1) WordPress email configuration (wp_mail), 2) SMTP plugin if using one, 3) WordPress error logs for details.', 'easytools-sub')
            ));
        }
    }
}

/**
 * AJAX: Toggle dev mode
 */
add_action('wp_ajax_easytools_toggle_dev_mode', function() {
    check_ajax_referer('easytools_tester', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error();
    }

    $enabled = isset($_POST['enabled']) ? sanitize_text_field($_POST['enabled']) : 'no';
    update_option('easytools_dev_mode', $enabled);

    wp_send_json_success();
});
