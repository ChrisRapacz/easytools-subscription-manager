# Easytools Subscription Manager - Complete Setup Guide

## Table of Contents
1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Initial Setup](#initial-setup)
4. [Configuring Webhooks](#configuring-webhooks)
5. [Setting Up Access Control](#setting-up-access-control)
6. [Creating a Bouncer Page](#creating-a-bouncer-page)
7. [Customizing Email Templates](#customizing-email-templates)
8. [Managing Subscribers](#managing-subscribers)
9. [Testing Your Setup](#testing-your-setup)
10. [Monitoring Webhook Activity](#monitoring-webhook-activity)
11. [Troubleshooting](#troubleshooting)

---

## Introduction

**Easytools Subscription Manager** is a complete WordPress plugin that integrates your WordPress site with the Easytools payment platform. It automatically:
- Creates user accounts when someone purchases a subscription
- Sends beautiful branded welcome emails with login credentials
- Protects your premium content from non-subscribers
- Shows a custom bouncer page to visitors without access
- Manages subscription status changes via webhooks

---

## Installation

### Step 1: Upload the Plugin
1. Log in to your WordPress admin panel
2. Navigate to **Plugins → Add New → Upload Plugin**
3. Click **Choose File** and select `easytools-subscription-manager-v1.5.3.zip`
4. Click **Install Now**

### Step 2: Activate the Plugin
1. After installation completes, click **Activate Plugin**
2. You'll see a new menu item: **Easytools Subscription** in your WordPress admin sidebar

---

## Initial Setup

### Step 1: Access Plugin Settings
1. Click **Easytools Subscription** in the WordPress admin menu
2. You'll see the main settings page with a beautiful teal interface

### Step 2: Configure Basic Settings

#### Checkout URL
1. In Easytools, create your subscription product
2. Copy the product checkout URL (e.g., `https://easl.ink/yourproduct`)
3. Paste it into the **Checkout URL** field
4. This is where non-subscribers will be redirected to purchase access

#### Webhook Signing Key
1. Log in to your Easytools account
2. Go to **API & Webhooks**
3. Generate a new **Webhook Signing Key**
4. Copy the key
5. Back in WordPress, click the **eye icon** next to the Webhook Signing Key field
6. Paste your key
7. **Important:** Copy the Webhook URL shown below the field (e.g., `https://yoursite.com/wp-json/easytools/v1/webhook`)

#### API Token
1. An API token is automatically generated for you
2. This secures your webhook endpoint
3. You can reveal it by clicking the eye icon
4. Keep this secure - never share it publicly

### Step 3: Save Your Settings
Click **Save Settings** at the bottom of the form.

---

## Configuring Webhooks

### Step 1: Add Webhook URL to Easytools
1. In your Easytools account, go to **API & Webhooks**
2. Click **Add New Webhook**
3. Paste your WordPress webhook URL: `https://yoursite.com/wp-json/easytools/v1/webhook`
4. Select the events you want to receive:
   - ✅ **subscription.active** (when subscription starts)
   - ✅ **subscription.expired** (when subscription ends)
   - ✅ **subscription.cancelled** (when subscription is cancelled)
5. Save the webhook

### Step 2: Test Webhook Connection
1. In WordPress, go to **Easytools Subscription → Webhook Tester**
2. Enter a test email address
3. Click **Send Test Webhook**
4. You should see: ✅ "Test webhook sent successfully"
5. Check the **Logs** tab to verify the webhook was received

---

## Setting Up Access Control

### Mode 1: Protect Specific Pages

**Use this if you only want to protect certain pages:**

1. Go to **Easytools Subscription → Settings**
2. Find the **Access Control** section
3. Leave **Protect Entire Site** unchecked
4. In the **Protected Pages** dropdown, hold Ctrl (Cmd on Mac) and select the pages you want to protect
5. Click **Save Settings**

### Mode 2: Protect Entire Site (Except Specific Pages)

**Use this if you want to protect everything except a few public pages:**

1. Go to **Easytools Subscription → Settings**
2. Find the **Access Control** section
3. Check **Protect Entire Site**
4. In the **Excluded Pages** dropdown, select pages that should remain public (e.g., Home, About, Contact)
5. Click **Save Settings**

### How It Works
- **Non-subscribers** trying to access protected pages are redirected to your bouncer page (or checkout if no bouncer page is set)
- **Active subscribers** can access all protected content
- **Logged-out users** are also redirected

---

## Creating a Bouncer Page

A bouncer page is a beautiful, customized page that non-subscribers see when they try to access premium content. It's much better than directly redirecting to checkout!

### Step 1: Enable Bouncer Page
1. Scroll down to the **Bouncer Page** section
2. Check **Enable custom bouncer page for non-subscribers**

### Step 2: Customize Colors and URL
1. **Product URL**: Enter your Easytools product URL (this is where the "Start Subscription" button will link)
2. **Icon Color**: Click the color picker to choose the lock icon color (default: #71efab - teal)
3. **Button Color**: Choose the primary button color (default: #71efab)
4. **Background Color**: Choose the page background color (default: #172532 - dark blue)

### Step 3: Create the Bouncer Page
**Option A: Create Automatically**
1. Click **Create New Bouncer Page from Template**
2. Wait for the success message: "Bouncer page created successfully!"
3. The page refreshes and your new bouncer page is automatically selected

**Option B: Copy HTML for Manual Creation**
1. Click **Copy Bouncer HTML Template**
2. The HTML is copied to your clipboard
3. Go to **Pages → Add New**
4. Title it "Subscriber Access Required" (or any name you prefer)
5. Switch to **Text/HTML editor** mode
6. Paste the copied HTML
7. Publish the page
8. Go back to **Easytools Subscription → Settings**
9. Select your page from the **Bouncer Page** dropdown
10. Save settings

### Step 4: Save Settings
Click **Save Settings** at the bottom.

### What Visitors See
When non-subscribers try to access protected content, they'll see:
- A lock icon (in your chosen color)
- "This Content is for Subscribers Only" heading
- Explanatory text
- A prominent button: "Start Your Free Trial or Renew Subscription"
- A link: "Already a member? Log in here"

---

## Customizing Email Templates

Your plugin sends beautiful HTML emails to new subscribers with their login credentials. Let's customize them!

### Step 1: Configure Email Settings
1. Scroll to the **User Registration** section
2. **Send Welcome Email**: Make sure this is checked ✅
3. **Email From Address**: Enter your preferred sender email (e.g., `hello@yoursite.com`)
4. **Email From Name**: Enter your sender name (e.g., "Your Company Name")

### Step 2: Customize Email Content
Scroll to the **Email Content Customization** section:

#### Brand Color
- Choose a color that matches your brand (default: #05c7aa - teal)
- This affects the email header and button colors

#### Email Subject
- Default: `[{site_name}] Welcome! Set Your Password`
- Available placeholders:
  - `{username}` - User's login name
  - `{site_name}` - Your WordPress site name
  - `{login_url}` - Login page URL

#### Email Heading
- Default: `🎉 Welcome to {site_name}!`
- This appears at the top of the email

#### Email Message
- Default: "Your account has been successfully created. We're excited to have you on board! To complete your account setup and access your dashboard, please set your password by clicking the button below."
- Customize this to match your brand voice

#### Button Text
- Default: `Set Your Password`
- This is the main call-to-action button

### Step 3: Test Your Email
1. Scroll down to find the **Test Email** section
2. Enter your email address
3. Click **Send Test Email**
4. Check your inbox to see how the email looks
5. Adjust settings as needed and test again

### Step 4: Save Settings
Click **Save Settings** when you're happy with your email design.

---

## Managing Subscribers

### Viewing All Subscribers
1. Go to **Easytools Subscription → Subscribers**
2. You'll see a table with all users who have (or had) subscriptions

### Subscriber Information Displayed
- **Email** - User's email address
- **Username** - WordPress login name
- **Subscription Status** - Active (green) or Expired (red)
- **Subscription Type** - Plan name (e.g., "monthly", "annual")
- **Subscription Date** - When they subscribed
- **Actions** - Quick action buttons

### Manual Subscription Management
You can manually activate or deactivate user access:

**To Activate Access:**
1. Find the user in the list
2. Click the **Activate** button (green checkmark)
3. The user immediately gets access to protected content

**To Deactivate Access:**
1. Find the user in the list
2. Click the **Deactivate** button (red X)
3. The user loses access to protected content

**Use Cases:**
- Manually grant access to someone without going through payment
- Trial subscriptions
- Staff/partner access
- Fixing subscription issues

### Filtering Subscribers
- **Active Subscribers** - Users who currently have access
- **Expired Subscribers** - Users who lost access
- **All Subscribers** - Complete list

---

## Testing Your Setup

### Test 1: Webhook Connection
1. Go to **Easytools Subscription → Webhook Tester**
2. Enter a test email: `test@example.com`
3. Click **Send Test Webhook**
4. Expected result: ✅ Success message
5. Go to **Logs** tab to verify webhook was received

### Test 2: User Account Creation
1. In Easytools, create a test purchase using a real email you can access
2. Complete the checkout process
3. Wait 10-30 seconds for the webhook to process
4. In WordPress, go to **Easytools Subscription → Subscribers**
5. Verify your test user appears in the list with "Active" status
6. Check your email inbox for the welcome email

### Test 3: Access Control
**Log out of WordPress:**
1. Open an incognito/private browser window
2. Try to access a protected page
3. You should be redirected to your bouncer page (if enabled) or checkout
4. Verify the bouncer page displays correctly with your custom colors

**Log in as subscriber:**
1. Use the welcome email you received to set your password
2. Log in to WordPress
3. Try to access a protected page
4. You should have full access ✅

### Test 4: Email Delivery
1. Go to **Easytools Subscription → Settings**
2. Scroll to the **Test Email** section
3. Enter your email address
4. Click **Send Test Email**
5. Check your inbox (and spam folder)
6. Verify the email looks correct with your branding

---

## Monitoring Webhook Activity

### Accessing Webhook Logs
1. Go to **Easytools Subscription → Logs**
2. You'll see a detailed list of all webhook events received

### Understanding Log Entries
Each log entry shows:
- **Date/Time** - When the webhook was received
- **Event Type** - What happened (subscription.active, subscription.expired, etc.)
- **Customer Email** - Who the event is about
- **Status** - Success ✅ or Error ❌
- **Details** - Click to expand and see full webhook data

### Log Status Meanings
- **✅ Success** - Webhook processed correctly, user account created/updated
- **❌ Error** - Something went wrong (check details for error message)

### Common Success Messages
- "User account created and activated"
- "Subscription reactivated successfully"
- "Subscription deactivated"

### Exporting Logs
**For detailed analysis:**
1. Click **Export to CSV** for spreadsheet format
2. Click **Export to MD** for markdown documentation format

**Filter by date:**
1. Use the date range picker
2. Select "From" and "To" dates
3. Click **Filter**
4. Export will only include filtered results

---

## Troubleshooting

### Issue 1: Emails Not Being Sent

**Symptoms:**
- Webhook shows success but no email received
- Test email fails

**Solutions:**

1. **Check WordPress Email Configuration:**
   - Install "WP Mail SMTP" plugin
   - Configure SMTP settings (Gmail, SendGrid, etc.)
   - WordPress's default `wp_mail()` may be blocked by your host

2. **Check Spam Folder:**
   - Welcome emails might be filtered as spam
   - Add your "From Address" to your contacts

3. **Check Debug Logs:**
   - Enable WordPress debug mode
   - Check `wp-content/debug.log` for email errors

4. **Verify Email Settings:**
   - Go to **Easytools Subscription → Settings**
   - Scroll to **User Registration**
   - Ensure "Send Welcome Email" is checked ✅

### Issue 2: User Account Not Created

**Symptoms:**
- Webhook received but user doesn't appear in Subscribers list
- Logs show error

**Solutions:**

1. **Check Webhook Signing Key:**
   - Verify the key in WordPress matches Easytools exactly
   - Re-copy from Easytools if needed

2. **Check Webhook Logs:**
   - Go to **Easytools Subscription → Logs**
   - Look for error messages
   - Common errors:
     - "Invalid signature" - Wrong signing key
     - "Missing email" - Webhook data incomplete

3. **Verify Webhook URL:**
   - In Easytools, verify webhook URL is correct: `https://yoursite.com/wp-json/easytools/v1/webhook`
   - Must use HTTPS (not HTTP)
   - Must not have typos

4. **Check WordPress Permalinks:**
   - Go to **Settings → Permalinks**
   - Click **Save Changes** (even if nothing changed)
   - This refreshes rewrite rules

### Issue 3: Protected Pages Still Accessible

**Symptoms:**
- Non-subscribers can access protected content
- No redirect happens

**Solutions:**

1. **Verify Access Control Settings:**
   - Go to **Easytools Subscription → Settings**
   - Check that pages are listed in "Protected Pages" OR "Protect Entire Site" is enabled

2. **Check Bouncer Page Isn't Protected:**
   - If bouncer page is also protected, you get redirect loop
   - Exclude bouncer page from protection

3. **Clear Cache:**
   - If using caching plugin, clear cache
   - Some caching plugins serve cached versions to logged-out users

4. **Test in Incognito Mode:**
   - Always test access control in incognito/private window
   - Regular window might have cached admin session

### Issue 4: Bouncer Page Looks Wrong

**Symptoms:**
- Colors don't match settings
- Extra spacing or formatting issues
- HTML shows instead of styled page

**Solutions:**

1. **Recreate Bouncer Page:**
   - Delete old bouncer page
   - Create new one with current colors
   - Older versions had formatting issues that are now fixed

2. **Disable Other Plugins Temporarily:**
   - Page builder plugins might interfere
   - Deactivate page builders, test, then reactivate

3. **Check WordPress Theme:**
   - Some themes add extra CSS to pages
   - Try switching to default WordPress theme temporarily

4. **Use Text/HTML Editor:**
   - If manually creating bouncer page, use Text/HTML editor (not Visual)
   - Visual editor might add unwanted formatting

### Issue 5: Password Reset Link Shows "Invalid Key"

**Symptoms:**
- User clicks "Set Your Password" in email
- Gets error: "Your password reset link appears to be invalid"

**Solution:**
- This was fixed in version 1.5.0+
- Make sure you're running version 1.5.3 or higher
- If issue persists, manually reset user password in WordPress admin

### Issue 6: Subscription Type Not Saving

**Symptoms:**
- Subscription Type column empty in Subscribers list
- Webhook logs show subscription type in payload but not saved

**Solution:**
- This was fixed in version 1.4.4+
- Update to version 1.5.3 or higher
- Plugin now checks multiple webhook fields for subscription type

---

## Best Practices

### 1. Regular Monitoring
- Check **Webhook Logs** weekly
- Monitor for errors or failed webhooks
- Export logs monthly for records

### 2. Email Deliverability
- Use professional SMTP service (SendGrid, Mailgun, etc.)
- Set up SPF/DKIM records for your domain
- Test emails regularly to different providers (Gmail, Outlook, etc.)

### 3. Access Control Strategy
- **Protect Entire Site** for membership sites where most content is premium
- **Protect Specific Pages** for mixed content sites with both free and premium content
- Always leave legal pages (Terms, Privacy Policy) unprotected

### 4. Bouncer Page Design
- Match colors to your brand
- Keep messaging clear and concise
- Test on mobile devices
- A/B test different calls-to-action

### 5. Backup Strategy
- Export webhook logs regularly
- Export subscriber list monthly
- Keep backups of customized email templates

### 6. Testing Workflow
Before launching:
1. ✅ Test webhook with test purchase
2. ✅ Verify email delivery
3. ✅ Test access control as non-subscriber
4. ✅ Test access control as subscriber
5. ✅ Test bouncer page on mobile and desktop
6. ✅ Verify subscription status changes (active → expired)

---

## Video Script Outline

### Introduction (30 seconds)
- "Welcome to Easytools Subscription Manager"
- "Seamlessly integrate WordPress with Easytools payment platform"
- "Automatically create accounts, protect content, and manage subscriptions"

### Installation (1 minute)
- Show WordPress admin
- Upload plugin
- Activate
- Show new menu item

### Basic Setup (2 minutes)
- Enter Checkout URL
- Copy webhook signing key from Easytools
- Paste into WordPress
- Copy webhook URL
- Add to Easytools webhooks
- Save settings

### Access Control (2 minutes)
- Show two modes (protect all vs. protect specific)
- Select pages to protect
- Test as logged-out user
- Show redirect

### Bouncer Page (2 minutes)
- Enable bouncer page
- Customize colors with color pickers
- Click "Create New Bouncer Page"
- Show success message
- Visit protected page as non-subscriber
- Show beautiful bouncer page

### Email Customization (1.5 minutes)
- Customize email colors
- Edit subject, heading, message
- Send test email
- Show received email in inbox

### Subscriber Management (1.5 minutes)
- Show Subscribers tab
- Explain columns
- Manually activate/deactivate user
- Show status change

### Webhook Testing & Logs (1.5 minutes)
- Go to Webhook Tester
- Send test webhook
- Go to Logs
- Show successful webhook
- Expand details

### Real Purchase Flow (2 minutes)
- Make test purchase in Easytools
- Wait for webhook
- Show user created in WordPress
- Show email received
- Log in with credentials
- Access protected content

### Conclusion (30 seconds)
- "That's it! Your membership site is ready"
- "Questions? Contact support"
- "Plugin created by Chris Rapacz"
- Show LinkedIn link

**Total Runtime: ~15 minutes**

---

## FAQ

**Q: Can I use this with multiple Easytools products?**
A: Yes! All products from your Easytools account can use the same webhook endpoint. Each purchase creates or updates the user's subscription.

**Q: What happens when a subscription expires?**
A: The plugin receives a `subscription.expired` webhook, automatically deactivates the user's access, and they can no longer view protected content.

**Q: Can I customize the subscriber role?**
A: Yes, in User Registration settings you can set the default WordPress role (subscriber, contributor, etc.).

**Q: Does this work with WooCommerce?**
A: No, this plugin is specifically for Easytools payment platform integration. For WooCommerce, you'd need a different solution.

**Q: Can I import existing subscribers?**
A: You can manually activate users using the Subscribers page, but bulk import isn't currently supported. Consider using Easytools API to trigger webhooks for existing customers.

**Q: Is the plugin translation-ready?**
A: Yes! The plugin includes Polish translations and is ready for additional languages using WordPress translation tools.

**Q: How secure is the webhook endpoint?**
A: Very secure. It uses:
- Webhook signing key verification (cryptographic signature)
- API token authentication
- WordPress nonces for AJAX requests
- All inputs are sanitized and validated

**Q: Can one user have multiple subscriptions?**
A: The plugin tracks one subscription status per user. If a user has multiple Easytools products, the most recent webhook determines their status.

**Q: What if someone buys with different email?**
A: The plugin creates a new WordPress account for each unique email address. Users should use the same email consistently.

**Q: Can I prevent the plugin from creating new accounts?**
A: Currently, the plugin automatically creates accounts for all successful subscription purchases. Manual account management is available in the Subscribers tab.

---

## Support & Resources

**Plugin Documentation:**
- English: https://www.easy.tools/docs/explore
- Polish: https://www.easy.tools/pl/docs/odkrywaj

**Support Email:** kontakt.rapacz@gmail.com

**Plugin Author:** Chris Rapacz
**LinkedIn:** https://www.linkedin.com/in/krzysztofrapacz/

**Easytools Platform:** https://easy.tools

---

## Version History

**v1.5.3** (Current)
- Fixed bouncer page spacing issues
- Removed HTML comments to prevent WordPress auto-formatting
- Added author footer to settings page

**v1.5.2**
- Fixed button text vertical alignment on bouncer page

**v1.5.1**
- Added confirmation messages for bouncer page creation
- Fixed color customization for bouncer page
- Changed default bouncer page title to "Bouncer Page"

**v1.5.0**
- Added complete bouncer page system
- Customizable colors (icon, button, background)
- One-click bouncer page creation
- HTML template copy functionality

**v1.4.5**
- Fixed password reset key storage location
- Keys now correctly stored in wp_users table

**v1.4.4**
- Fixed password reset key hashing (wp_hash vs wp_hash_password)
- Added subscription_type field support

**v1.4.3**
- Implemented manual password reset key saving as fallback
- Added extensive email delivery logging

**v1.4.2**
- Added comprehensive diagnostic logging for email system

**v1.4.1**
- Fixed email delivery timing issues
- Improved webhook processing reliability

**v1.4.0**
- Added test email functionality
- Improved email customization options

---

## Credits

**Plugin Development:** Chris Rapacz
**Payment Platform:** Easytools
**WordPress:** Open source CMS

This plugin is provided as-is under GPL v2 license.

---

**Last Updated:** November 2025
**Plugin Version:** 1.5.3
**WordPress Compatibility:** 5.0+
**PHP Compatibility:** 7.4+
