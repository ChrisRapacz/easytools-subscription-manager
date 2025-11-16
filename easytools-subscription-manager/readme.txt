=== Easytools Subscription Manager ===
Contributors: chrisrapacz
Tags: subscription, membership, easytools, access control, webhooks
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.5.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Complete subscription management system for WordPress integrated with Easytools payment platform. Automate user accounts, protect premium content, and manage subscriptions.

== Description ==

Easytools Subscription Manager is a powerful WordPress plugin that seamlessly integrates your website with the Easytools payment platform. Automatically create user accounts when customers purchase subscriptions, protect premium content, send personalized welcome emails, and manage subscription access - all without manual intervention.

= Key Features =

**🔄 Automatic Subscription Management**
* Automatically create WordPress accounts after subscription purchase
* Real-time subscription status updates via webhooks
* Automatic activation/deactivation of premium content access
* Support for monthly, annual, and custom subscription types

**🔐 Advanced Access Control**
* Two protection modes: Protect entire site (with exceptions) or protect specific pages
* Flexible page/post protection configuration
* Smart redirects for users without access
* Customizable access levels and user roles

**🎨 Beautiful Bouncer Page**
* 1-click bouncer page generator from template
* Full customization: icon color, button color, background color
* Responsive design that works perfectly on all devices
* Option to copy HTML for manual editing

**📧 Personalized Welcome Emails**
* Professional HTML email templates
* Full customization: colors, subject, content, CTA button
* Dynamic variables: {username}, {site_name}, {login_url}
* Test email sending functionality
* Automatic password reset links

**🔗 Secure Webhook Integration**
* Cryptographic webhook signature verification
* Support for subscription.active, subscription.expired, subscription.cancelled events
* Detailed webhook logging
* Built-in webhook testing tool

**👥 Subscriber Management**
* Visual dashboard showing all subscription statuses
* Manual activation/deactivation controls
* Detailed subscription information (type, dates, email)
* User-friendly status indicators

**📊 Monitoring & Logs**
* Detailed webhook event logging
* Export logs to CSV or Markdown
* Time-based filtering
* Real-time success/error notifications

= Perfect For =

* Membership sites
* Online courses and educational platforms
* Premium content websites
* SaaS products with WordPress integration
* Digital product sellers
* Newsletter subscriptions

= Requirements =

* WordPress 5.0 or higher
* PHP 7.4 or higher
* SSL certificate (HTTPS)
* Active Easytools account

= Easytools Platform =

This plugin requires an account with Easytools (easy.tools), a modern payment and subscription management platform. Easytools provides:
* Secure payment processing
* Subscription management
* Webhook notifications
* API access

Learn more at [easy.tools](https://easy.tools)

= Documentation & Support =

* [Documentation (English)](https://www.easy.tools/docs/explore)
* [Documentation (Polish)](https://www.easy.tools/pl/docs/odkrywaj)
* Email support: kontakt.rapacz@gmail.com

= Privacy & Data =

This plugin:
* Does not collect or store any user data beyond what WordPress normally stores
* Communicates securely with Easytools API via HTTPS
* Uses cryptographic signature verification for webhook security
* Follows WordPress and GDPR best practices

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins → Add New
3. Search for "Easytools Subscription Manager"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to WordPress admin panel
3. Go to Plugins → Add New → Upload Plugin
4. Choose the downloaded ZIP file
5. Click "Install Now" and then "Activate Plugin"

= Configuration =

**Step 1: Get Your Checkout URL**
1. Create a subscription product in your Easytools account
2. Copy the checkout URL (e.g., https://easl.ink/yourproduct)
3. Paste it in the "Checkout URL" field in plugin settings

**Step 2: Configure Webhook**
1. In Easytools: Navigate to API & Webhooks → Generate Webhook Signing Key
2. Copy the generated key
3. In WordPress: Click the eye icon next to "Webhook Signing Key" field
4. Paste the key and save
5. Copy the webhook URL shown below the field
6. In Easytools: Add a new webhook with this URL
7. Select events: subscription.active, subscription.expired, subscription.cancelled

**Step 3: Set Up Access Control**
1. Choose protection mode (protect all or protect specific pages)
2. Select pages to protect or exclude
3. Configure bouncer page settings
4. Save settings

**Step 4: Customize Welcome Emails (Optional)**
1. Enable welcome email sending
2. Configure sender details
3. Customize email content and colors
4. Send test email
5. Save settings

== Frequently Asked Questions ==

= Do I need an Easytools account? =

Yes, this plugin requires an active Easytools account. You can sign up at [easy.tools](https://easy.tools)

= Is SSL/HTTPS required? =

Yes, HTTPS is required for secure webhook communication between Easytools and your WordPress site.

= Can I customize the welcome emails? =

Absolutely! You can customize email colors, subject, content, and use dynamic variables like {username}, {site_name}, and {login_url}.

= How does the access control work? =

You can either protect your entire site (with exceptions for public pages) or protect only specific pages. Users without active subscriptions are automatically redirected to your bouncer page or checkout.

= What happens when a subscription expires? =

The plugin automatically receives a webhook notification and immediately revokes the user's premium access. They will no longer be able to view protected content.

= Can I manually manage user access? =

Yes, you can manually activate or deactivate access for any user from the Subscribers panel.

= Does it work with existing WordPress users? =

Yes! If an email from Easytools matches an existing WordPress user, the plugin will update that user's subscription status instead of creating a duplicate account.

= Can I see webhook activity? =

Yes, the plugin includes a detailed webhook logging system. You can view all webhook events, export logs to CSV or Markdown, and filter by date range.

= Is the plugin translation ready? =

Yes! The plugin includes Polish translation and is ready for additional translations using .po/.mo files.

= What if emails are not being sent? =

WordPress email delivery can be unreliable. We recommend installing a SMTP plugin like "WP Mail SMTP" and configuring it with a professional email service (Gmail, SendGrid, Mailgun, etc.).

== Screenshots ==

1. Main settings page with checkout URL and webhook configuration
2. Access control settings - protect entire site or specific pages
3. Beautiful bouncer page with customizable colors
4. Personalized welcome email editor with live preview
5. Subscriber management panel showing all users and their subscription status
6. Detailed webhook logs with export options
7. Webhook tester for easy debugging
8. Dashboard widget showing recent subscription activity

== Changelog ==

= 1.5.5 =
* Added automatic synchronization between Checkout URL and Product URL
* Intelligent form field auto-filling (no need to enter URLs twice)
* Visual confirmation of URL synchronization (green highlight)
* Option to manually override URLs if needed

= 1.5.4 =
* Added validation for product URL before creating Bouncer Page
* Required field indicator with visual feedback
* Automatic red highlight for empty required fields
* Improved user experience for bouncer page creation

= 1.5.3 =
* Removed HTML comments from bouncer page (fixes spacing issues)
* Added author footer in settings panel
* Fixed bouncer page formatting

= 1.5.2 =
* Fixed vertical text alignment on buttons
* Removed extra whitespace in bouncer page

= 1.5.1 =
* Added confirmation messages when creating Bouncer Page
* Fixed real-time color update issues
* Changed default page name to "Bouncer Page"

= 1.5.0 =
* Complete Bouncer Page system
* Customizable colors (icon, button, background)
* 1-click page generator
* HTML copy functionality

= 1.4.0 =
* Enhanced webhook logging system
* Export logs to CSV and Markdown
* Date range filtering for logs
* Improved error handling

= 1.3.0 =
* Added webhook tester
* Improved email customization
* Added dynamic variables support
* Better mobile responsiveness

= 1.2.0 =
* Added subscriber management panel
* Visual subscription status indicators
* Manual access control
* Improved admin interface

= 1.1.0 =
* Added access control features
* Two protection modes
* Smart redirects
* Enhanced security

= 1.0.0 =
* Initial release
* Basic subscription management
* Webhook integration
* Welcome email system

== Upgrade Notice ==

= 1.5.5 =
Improved URL synchronization and auto-filling. Upgrade for better user experience when configuring bouncer pages.

= 1.5.0 =
Major update with complete Bouncer Page system. Highly recommended upgrade for all users.

= 1.0.0 =
Initial release of Easytools Subscription Manager.

== Additional Info ==

= Credits =

* Developed by Chris Rapacz (Krzysztof Rapacz)
* Website: [chrisrapacz.com](https://www.chrisrapacz.com)
* LinkedIn: [linkedin.com/in/krzysztofrapacz](https://www.linkedin.com/in/krzysztofrapacz/)

= Support =

For support, please email kontakt.rapacz@gmail.com or visit the plugin support forum.

= Contributing =

This plugin is open source. Contributions are welcome!

= Languages =

* English (default)
* Polish (included)

Additional translations welcome via .po/.mo files.
