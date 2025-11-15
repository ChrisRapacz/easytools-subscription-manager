# How to Connect WordPress with Easytools – A Step-by-Step Guide

Watch the tutorial on connecting WordPress with Easytools. In this comprehensive guide, you'll learn how to integrate your WordPress site with Easytools to collect payments, manage subscriptions, and control user access to premium content.

---

## Introduction

### What you'll learn in this tutorial

In this guide, you'll discover how to:
- Set up webhook integration between Easytools and WordPress
- Install and configure the Easytools Subscription Manager plugin
- Protect your premium content and manage subscriber access
- Use native Easytools automations to automatically create user accounts
- Test and troubleshoot your integration
- Implement best security practices

### Why integrate WordPress with Easytools?

Easytools provides powerful subscription and payment processing capabilities, while WordPress is the world's most popular content management system. By connecting them, you can:
- Sell subscriptions directly from your WordPress site
- Automatically manage user access based on subscription status
- Protect premium content behind a paywall
- Handle recurring payments and subscription renewals
- Track subscription events in real-time

### Two integration methods explained

This tutorial covers **two complementary methods** to integrate Easytools with WordPress:

#### Method 1: Webhooks with Easytools Subscription Manager plugin

**Best for:** Full control over subscription logic, page protection, and custom workflows

This method uses real-time webhooks to sync subscription events from Easytools to your WordPress site. The Easytools Subscription Manager plugin:
- Receives webhook notifications when subscriptions are created, renewed, expired, or cancelled
- Automatically updates user access permissions
- Protects specific pages or your entire site
- Provides built-in testing tools and webhook logs
- Verifies webhook authenticity using HMAC signatures

**Use this method when you need:**
- Content protection (restricting access to pages/posts)
- Custom subscription logic
- Real-time access control
- Detailed webhook logging and monitoring

#### Method 2: Native Easytools Automations

**Best for:** Quick setup, automatic user account creation

This method uses Easytools' built-in WordPress automation:
- Automatically creates WordPress user accounts after purchase
- Assigns specific WordPress roles to new subscribers
- Works without additional plugins
- Quick to set up with minimal configuration

**Use this method when you need:**
- Simple user account creation
- Automatic role assignment
- Integration with other Easytools automations

#### Can you use both methods together?

**Yes!** In fact, combining both methods provides the best experience:
- Use **Automations** to automatically create user accounts after purchase
- Use **Webhooks** (plugin) to manage ongoing access, handle renewals/cancellations, and protect content

This tutorial covers both approaches so you can choose the best fit for your needs.

### Prerequisites and what you'll need

Before starting, make sure you have:

**WordPress requirements:**
- WordPress 5.0 or higher
- WordPress site with HTTPS/SSL certificate (required for webhooks)
- Admin access to your WordPress dashboard
- Ability to install plugins

**Easytools requirements:**
- Active Easytools account
- At least one product/subscription plan created
- Access to your Easytools dashboard

**Technical knowledge:**
- Basic WordPress administration
- Basic understanding of how subscriptions work
- No coding required!

**Time needed:**
- Initial setup: 20-30 minutes
- Testing and verification: 10-15 minutes

---

## Part 1: Understanding Webhooks in Easytools

### 1.1 What are webhooks?

**Webhooks** are automated messages sent from one application to another when specific events occur. Think of them as real-time notifications that allow different systems to communicate instantly.

**How webhooks work in Easytools:**

1. An event happens in Easytools (e.g., someone buys a subscription)
2. Easytools immediately sends an HTTP POST request to your WordPress site
3. The request contains data about the event (JSON payload)
4. Your WordPress site receives and processes this data
5. User access is automatically updated

**Why this matters:**
- **Real-time updates:** Access changes happen instantly (5-10 seconds)
- **Automation:** No manual user management needed
- **Reliability:** Events are delivered even when users close their browser
- **Flexibility:** Handle complex subscription logic

**Example scenario:**
> User purchases a monthly subscription → Easytools sends webhook → WordPress receives notification → User account is updated → User can access premium content

### 1.2 Easytools webhook events

Easytools can send notifications for various subscription-related events. Here's the complete list:

#### Primary Events (Most Important)

**1. `product_assigned`** (RECOMMENDED)
- Triggered when a product is assigned to a customer
- Works for both new purchases and renewals
- Most reliable event for granting access
- **Use this as your primary event**

**JSON example:**
```json
{
  "event": "product_assigned",
  "customer_email": "user@example.com",
  "customer_id": "cus_123456",
  "product_id": "prod_789",
  "product_name": "Premium Monthly Subscription",
  "subscription_type": "monthly",
  "is_subscription": true,
  "subscription_one_time": false,
  "renewal_date": "2025-12-03T10:00:00Z",
  "price": 29.99,
  "currency": "USD"
}
```

**2. `subscription_created`**
- Triggered when a new subscription is purchased
- First event in subscription lifecycle
- Contains initial subscription details

**3. `subscription_expired`**
- Triggered when a subscription expires (payment failed or period ended)
- **Critical:** Use this to revoke access
- Update user status immediately

**4. `subscription_cancelled`**
- Triggered when user or admin cancels subscription
- May still have access until period ends (depending on settings)

#### Additional Events

**5. `subscription_renewed`**
- Successful renewal payment processed
- Subscription period extended

**6. `subscription_paused`**
- Subscription temporarily paused
- User may or may not retain access (depends on your business logic)

**7. `subscription_plan_changed`**
- User upgraded or downgraded their plan
- Contains old and new plan details

**8. One-time purchases (lifetime access)**
- For non-recurring products
- `subscription_one_time: true` in payload
- Grant permanent access

### Which event should you use?

**Primary recommendation: `product_assigned`**

Why?
- Covers both new purchases AND renewals
- Most reliable for access control
- Works with all subscription types
- Reduces duplicate code

**Also recommended: `subscription_expired`**
- Essential for revoking access
- Ensures users without valid subscriptions can't access content

**Optional (for advanced tracking):**
- `subscription_cancelled` - Send cancellation emails, offer win-back deals
- `subscription_plan_changed` - Track upgrades/downgrades
- `subscription_paused` - Handle paused states

### 1.3 Understanding the JSON payload structure

Every webhook sends data in JSON format. Here's what a typical payload looks like:

```json
{
  "event": "product_assigned",
  "customer_email": "john@example.com",
  "customer_id": "cus_abc123",
  "customer_name": "John Smith",
  "product_id": "prod_xyz789",
  "product_name": "Monthly Premium Plan",
  "variant_id": "var_456",
  "variant_name": "Standard Tier",
  "subscription_type": "monthly",
  "is_subscription": true,
  "subscription_one_time": false,
  "subscription_date": "2025-01-03T14:30:00Z",
  "renewal_date": "2025-02-03T14:30:00Z",
  "trial_ends_at": null,
  "price": 29.99,
  "currency": "USD",
  "custom_id": "custom_field_value"
}
```

**Key fields explained:**

| Field | Description | How to use it |
|-------|-------------|--------------|
| `event` | Type of webhook event | Route different events to different handlers |
| `customer_email` | Subscriber's email address | **CRITICAL:** Match WordPress user by email |
| `customer_id` | Easytools customer ID | Store for future reference |
| `product_id` | Which product was purchased | Map to WordPress content/roles |
| `subscription_type` | "monthly", "yearly", "lifetime", etc. | Determine access duration |
| `is_subscription` | Boolean: Is this recurring? | Handle differently than one-time |
| `subscription_one_time` | Boolean: Is this lifetime? | Grant permanent access |
| `renewal_date` | When subscription renews | Store for access management |
| `trial_ends_at` | When trial period ends | Handle trial users differently |

**Important notes:**
- The `customer_email` field is **critical** - this is how you match Easytools customers to WordPress users
- Always validate this email matches a WordPress user before granting access
- If emails don't match, the webhook cannot assign access automatically

### 1.4 Webhook security with HMAC

**Why webhook security matters:**

Anyone could potentially send fake webhook requests to your WordPress site pretending to be Easytools. Without verification, malicious actors could:
- Grant themselves premium access
- Create fake subscription events
- Compromise your subscription system

**Solution: HMAC Signatures**

Easytools uses **HMAC SHA-256 signatures** to verify webhook authenticity.

**How it works:**

1. **Easytools generates a signature:**
   - Takes the webhook payload (JSON)
   - Uses your secret signing key
   - Creates a unique signature using HMAC SHA-256
   - Sends signature in the `x-webhook-signature` header

2. **Your WordPress plugin verifies:**
   - Receives the webhook and signature
   - Uses the same secret signing key
   - Generates its own signature from the payload
   - Compares: if signatures match → authentic, if not → reject

**Example process:**
```
Payload: {"event":"product_assigned",...}
Secret Key: "your_secret_key_here"
→ HMAC SHA-256 → Signature: "a3b2c1d4e5f6..."
```

**This ensures:**
- Only Easytools can send valid webhooks (they have the key)
- Payload hasn't been tampered with (signature would change)
- Your site is protected from fake requests

### 1.5 Generating your Webhook Signing Key in Easytools

**Step-by-step instructions:**

1. **Log in to your Easytools dashboard**

2. **Navigate to Settings**
   - Click on your profile/store name (top right)
   - Select "Settings" from dropdown

3. **Go to API & Webhooks section**
   - Find "API & Webhooks" in the settings menu
   - Click to open

4. **Generate Signing Key**
   - Look for "Webhook Signing Key" section
   - Click "Generate Key" button
   - A long string will appear (e.g., `sk_live_abc123def456...`)

5. **IMPORTANT: Copy and save this key immediately**
   - You will only see this key ONCE
   - If you lose it, you'll need to generate a new one
   - Store it securely in your password manager
   - You'll need this key when configuring the WordPress plugin

**Security best practices:**
- Never share your signing key publicly
- Don't commit it to version control (GitHub, etc.)
- Store it in a secure password manager
- If compromised, regenerate immediately
- Use different keys for test and production environments (if available)

**Example signing key format:**
```
sk_live_[YOUR_ACTUAL_SIGNING_KEY_WILL_APPEAR_HERE]
```
Note: The actual key will be a long alphanumeric string (approximately 64-70 characters)

---

## Part 2: Setting Up WordPress

### 2.1 WordPress requirements

Before installing the plugin, verify your WordPress meets these requirements:

**Minimum specifications:**
- WordPress version: 5.0 or higher (recommended: latest version)
- PHP version: 7.4 or higher
- MySQL version: 5.6 or higher
- HTTPS/SSL certificate: **REQUIRED** (webhooks won't work on HTTP)

**How to check your WordPress version:**
1. Log in to WordPress admin
2. Go to Dashboard → Updates
3. Your version is shown at the top

**How to check if you have SSL:**
- Your site URL should start with `https://` (not `http://`)
- You should see a padlock icon in the browser address bar
- If you don't have SSL, contact your hosting provider to enable it

**Required server permissions:**
- Ability to install plugins
- WordPress REST API enabled (usually enabled by default)
- Ability to receive incoming HTTP POST requests

**Recommended plugins (optional, not required):**
- A security plugin (e.g., Wordfence, Sucuri) - but configure it to allow webhook requests
- A caching plugin - but ensure webhooks bypass cache

### 2.2 Installing Easytools Subscription Manager plugin

**Step 1: Download the plugin**

The plugin should be provided as a .zip file named something like:
- `easytools-subscription-manager-v3.0.zip`
- Or similar

If you don't have the plugin file yet, contact your Easytools representative or download it from the provided link.

**Step 2: Install via WordPress Admin Panel**

1. **Log in to your WordPress admin dashboard**
   - Go to `https://yoursite.com/wp-admin`
   - Enter your admin credentials

2. **Navigate to Plugins section**
   - In the left sidebar, click "Plugins"
   - Click "Add New"

3. **Upload the plugin**
   - Click "Upload Plugin" button at the top
   - Click "Choose File"
   - Select the downloaded .zip file
   - Click "Install Now"

4. **Wait for installation**
   - WordPress will upload and extract the plugin
   - This usually takes 5-15 seconds

**Step 3: Activate the plugin**

1. **After installation completes:**
   - You'll see a success message
   - Click "Activate Plugin" button

2. **Verify activation:**
   - You should see "Plugin activated" message
   - New menu item "Easytools Sub" appears in left sidebar

**Troubleshooting installation:**
- **"The uploaded file exceeds the upload_max_filesize directive"**
  - Contact your hosting provider to increase upload limit
  - Or install via FTP (upload to `/wp-content/plugins/`)

- **"Missing Dependencies" error**
  - Update WordPress to latest version
  - Ensure PHP version is 7.4 or higher

### 2.3 First look at the plugin interface

Once activated, explore the plugin:

1. **Click "Easytools Sub" in the WordPress sidebar**

You'll see several tabs:

**Dashboard Widget**
- Overview of subscription statistics
- Active subscriptions count
- Recent webhook activity

**Settings** (we'll configure this in Part 3)
- Webhook Signing Key
- Checkout URL
- Access Control options

**Webhook Testing** (we'll use this in Part 5)
- Built-in testing tool
- HMAC signature calculator
- Example payloads

**Webhook Logs** (essential for debugging)
- History of all received webhooks
- Success/failure status
- Detailed payload inspection

### 2.4 Understanding WordPress user roles

WordPress has a built-in role-based permission system. When integrating with Easytools, you need to decide which role to assign to your paid subscribers.

**Default WordPress roles (from least to most permissions):**

| Role | Capabilities | Best for |
|------|-------------|----------|
| **Subscriber** | Can only read content and manage their own profile | **Recommended for paid members** - limits what they can do |
| **Contributor** | Can write and edit their own posts (but not publish) | Community members who can submit content |
| **Author** | Can publish and manage their own posts | Content creators in your membership |
| **Editor** | Can publish and manage all posts | Staff members, moderators |
| **Administrator** | Full site control | You and your team only |

**Recommendation for paid subscriptions:**
→ Use **Subscriber** role for most members

**Why Subscriber role?**
- Limited permissions (safe)
- Can access member-only content
- Can't accidentally break your site
- Can't publish spam or unwanted content
- Can manage their own profile

**When to use other roles:**
- **Contributor/Author:** If you're running a membership where users create content
- **Never use Administrator:** For security reasons

**Custom roles:**
If you need more granular control, you can use plugins like:
- Members by MemberPress
- User Role Editor
- PublishPress Capabilities

These allow you to create custom roles like "Silver Member", "Gold Member", "Premium Member" with specific permissions.

**For this tutorial:**
We'll use the **Subscriber** role as the default for paid members.

### 2.5 Generating Application Password in WordPress

Application passwords allow external services (like Easytools) to authenticate with your WordPress site without using your actual admin password. This is more secure and can be easily revoked.

**Why application passwords are needed:**
- Required for Easytools Automations (Method 2)
- More secure than using your real password
- Can be revoked instantly without changing your login
- Follows WordPress security best practices
- Each application can have its own password

**Step-by-step: How to generate an application password**

1. **Log in to WordPress admin**
   - Go to your WordPress dashboard

2. **Navigate to your user profile**
   - Click your username in the top right corner
   - Select "Edit Profile" from dropdown
   - Or go to: Users → Profile

3. **Scroll down to "Application Passwords" section**
   - This section is at the bottom of the profile page
   - If you don't see it, your WordPress version might be too old (update to 5.6+)

4. **Create new application password**
   - In the "New Application Password Name" field, enter: `Easytools Integration`
   - Click "Add New Application Password" button

5. **Copy the generated password**
   - WordPress will display a password like: `abcd 1234 efgh 5678 ijkl 9012`
   - **IMPORTANT:** Copy this password immediately
   - Click the password to select all
   - Copy to clipboard (Ctrl+C or Cmd+C)
   - Paste into a secure location (password manager)

6. **You won't see this password again**
   - Once you navigate away, the password is hidden
   - If you lose it, you'll need to revoke and create a new one

**Security best practices:**

- **Store securely:** Save in a password manager like 1Password, LastPass, or Bitwarden
- **Don't share:** Never share this password publicly
- **One per integration:** Create separate passwords for different services
- **Regular review:** Periodically review and revoke unused application passwords

**How to revoke application password if needed:**

1. Go back to Users → Profile
2. Scroll to "Application Passwords" section
3. Find "Easytools Integration" in the list
4. Click "Revoke" button next to it
5. Confirm revocation

This immediately blocks access - useful if:
- Password is compromised
- You no longer use the integration
- You want to reset and create a new one

**What you'll need this password for:**
- Configuring Easytools Automations (Part 6)
- Allowing Easytools to create WordPress user accounts
- Syncing data between systems

**Troubleshooting:**

- **"Application Passwords section not visible"**
  - Update WordPress to version 5.6 or higher
  - Ensure your site uses HTTPS (required for application passwords)

- **"Cannot generate password"**
  - Check your site has SSL/HTTPS enabled
  - Try a different browser
  - Disable security plugins temporarily

---

## Part 3: Configuring Easytools Subscription Manager Plugin

Now that the plugin is installed, let's configure it to work with your Easytools store.

### 3.1 Accessing plugin settings

1. **In WordPress admin, click "Easytools Sub" in the left sidebar**

2. **Click the "Settings" tab**
   - This is where all main configuration happens
   - You'll see several sections

### 3.2 Basic Settings configuration

**Setting 1: Webhook Signing Key**

This is the most important security setting.

1. **Locate "Webhook Signing Key" field**
   - First field in the settings

2. **Paste your signing key**
   - Remember the key you generated in Easytools (Part 1.5)?
   - Paste it here exactly as shown
   - No extra spaces before or after
   - Format: `sk_live_abc123...`

3. **Why this matters:**
   - Without correct key, webhooks will be rejected
   - Protects your site from fake subscription requests
   - Must match exactly with key in Easytools

**Setting 2: Checkout URL**

This is where non-subscribers will be redirected to purchase.

1. **Locate "Checkout URL" field**

2. **Enter your Easytools store URL**
   - Example: `https://yourstorename.easytools.app`
   - Or your custom domain: `https://store.yoursite.com`
   - Must include `https://`
   - Don't include trailing slash

3. **What happens:**
   - When non-subscribers try to access protected content
   - They're automatically redirected to this URL
   - After purchase, webhooks handle the rest

**Setting 3: Developer Mode (Optional)**

**⚠️ IMPORTANT: Only use this for testing!**

1. **Locate "Developer Mode" checkbox**

2. **What it does:**
   - Disables HMAC signature verification
   - Allows webhooks without valid signatures
   - Useful for testing initial setup

3. **When to enable:**
   - First time testing webhooks
   - Debugging connection issues
   - Learning how the plugin works

4. **When to disable:**
   - **ALWAYS disable before going live**
   - After testing is complete
   - On production sites with real subscribers

5. **Security warning:**
   - Leaving this enabled allows anyone to send fake webhooks
   - Could grant unauthorized access
   - Could compromise your subscription system
   - **Never use on live sites!**

### 3.3 Access Control settings

Now you'll configure which content requires a subscription.

**Access Control Mode: Choose your approach**

**Option A: Protect Entire Site**

1. **Select "Protect Entire Site" option**

2. **What this does:**
   - All pages and posts require subscription
   - Only logged-in subscribers can view content
   - Best for full membership sites

3. **Exceptions:**
   - WordPress login page always accessible
   - You can specify pages to exclude (see below)

**Option B: Protect Specific Pages**

1. **Select "Protect Specific Pages" option**

2. **What this does:**
   - Only selected pages/posts require subscription
   - Public content remains accessible
   - Best for mixed free/premium content

**Selecting pages to protect:**

1. **Scroll to "Protected Pages" section**

2. **You'll see a list of all your pages:**
   - Checkboxes next to each page title
   - Current protection status shown

3. **Select pages to protect:**
   - Check boxes for premium pages
   - Examples: "Premium Content", "Members Area", "Exclusive Articles"

4. **Pages to typically protect:**
   - Member-only blog posts
   - Premium resources/downloads
   - Exclusive courses or tutorials
   - Community forum pages

**Selecting pages to always allow (exceptions):**

Even if protecting entire site, some pages should remain public:

1. **Scroll to "Always Accessible Pages" section**

2. **Select these pages:**
   - Landing page / homepage (so visitors can see what you offer)
   - Sales page describing your subscription
   - Contact page
   - About page
   - Terms of Service / Privacy Policy (legal requirement)

3. **Why this matters:**
   - Visitors need to learn about your offering before subscribing
   - Legal pages must be publicly accessible
   - SEO benefits from having some public content

**Redirect Behavior:**

1. **Locate "Redirect non-subscribers to" setting**

2. **Options:**
   - **Checkout URL:** Send directly to payment (aggressive)
   - **Custom Page:** Send to a sales/info page first (recommended)

3. **Recommended approach:**
   - Create a "Join Now" page explaining your membership
   - List benefits, pricing, testimonials
   - Include prominent "Subscribe Now" button linking to checkout
   - Set this as your redirect page

### 3.4 User Management settings

Control how user accounts are created and managed.

**Setting 1: Automatic User Account Creation**

1. **Locate "Auto-Create Users" checkbox**

2. **Enable this option:**
   - Automatically creates WordPress account when webhook received
   - If user with email doesn't exist, creates new one
   - If user exists, updates their subscription status

3. **How it works:**
   - Easytools sends webhook with customer_email
   - Plugin searches for WordPress user with that email
   - If not found → creates new user
   - If found → updates existing user

**Setting 2: Default User Role**

1. **Locate "Default User Role" dropdown**

2. **Select role for new subscribers:**
   - Recommended: **Subscriber**
   - Other options: Contributor, Author (if appropriate)
   - Never select Administrator!

3. **This role is assigned when:**
   - New user account is auto-created
   - User receives valid subscription webhook

**Setting 3: Email Notifications**

1. **Locate "Send welcome email" checkbox**

2. **Options:**
   - **Enabled:** User receives WordPress welcome email
   - **Disabled:** Silent account creation

3. **Considerations:**
   - Enable if you want users to set their password
   - Disable if using Easytools for all access (no WordPress login needed)
   - Email includes WordPress login URL and instructions

**Setting 4: Matching Strategy (Hybrid Approach)**

The plugin uses email matching to connect Easytools customers with WordPress users.

**How it works:**

1. Webhook arrives with `customer_email: "john@example.com"`
2. Plugin searches WordPress users for matching email
3. If found → updates that user's subscription status
4. If not found → creates new user (if auto-create enabled)

**CRITICAL: Ensure email consistency**

For this to work, the email must match exactly:
- User creates WordPress account with: john@example.com
- User purchases in Easytools using: john@example.com
- Webhook arrives → Plugin finds match → Access granted ✓

**If emails don't match:**
- User has WordPress account with: john@example.com
- User purchases in Easytools using: j.smith@example.com
- Webhook arrives → Plugin can't find match → No access granted ✗

**Best practices:**
- Pre-fill Easytools checkout with WordPress user's email
- Require account creation before purchase
- Validate email during registration

**Example user flow (recommended):**
1. User registers WordPress account (john@example.com)
2. User clicks "Subscribe" button
3. Redirect to Easytools with email pre-filled
4. User completes purchase
5. Webhook updates existing WordPress account

### 3.5 Save your settings

**Important final steps:**

1. **Review all settings carefully**
   - Webhook Signing Key entered correctly
   - Checkout URL is valid
   - Access control configured as desired
   - User management settings appropriate

2. **Click "Save Changes" button**
   - Usually at bottom of the page
   - Look for success message

3. **Verify settings saved:**
   - Refresh the page
   - Check all fields still show your values

**What's configured now:**
✓ WordPress ready to receive webhooks
✓ Security with HMAC signatures enabled
✓ Access control rules defined
✓ User management automated

**Next steps:**
→ Configure webhooks in Easytools (Part 4)
→ Test the integration (Part 5)

---

## Part 4: Setting Up Webhooks in Easytools

Now let's configure Easytools to send webhooks to your WordPress site.

### 4.1 Accessing Easytools API & Webhook settings

1. **Log in to your Easytools dashboard**
   - Go to your Easytools admin panel

2. **Navigate to Settings**
   - Click your store name or profile icon (top right)
   - Select "Settings" from the dropdown menu

3. **Find "API & Webhooks" section**
   - Look in the settings sidebar
   - Click "API & Webhooks" or "Webhooks"
   - This is where you'll configure webhook delivery

### 4.2 Your WordPress Webhook URL

Before configuring in Easytools, you need your WordPress webhook endpoint URL.

**URL Format:**
```
https://yoursite.com/wp-json/easytools/v1/webhook
```

**How to construct your URL:**

1. **Start with your WordPress site URL:**
   - Example: `https://yoursite.com`
   - Must be HTTPS (not HTTP)
   - Don't include trailing slash

2. **Add the REST API path:**
   - `/wp-json/easytools/v1/webhook`
   - This is the plugin's webhook endpoint
   - Always the same for all installations

3. **Complete example:**
   - If your site is: `https://myawesomesite.com`
   - Your webhook URL is: `https://myawesomesite.com/wp-json/easytools/v1/webhook`

**Common variations:**

| Your WordPress URL | Your Webhook URL |
|-------------------|------------------|
| https://example.com | https://example.com/wp-json/easytools/v1/webhook |
| https://blog.example.com | https://blog.example.com/wp-json/easytools/v1/webhook |
| https://example.com/blog | https://example.com/blog/wp-json/easytools/v1/webhook |

**Testing your URL:**

You can test if the endpoint is reachable:
1. Open your browser
2. Visit: `https://yoursite.com/wp-json/`
3. You should see JSON output listing available endpoints
4. Look for `easytools/v1` in the list

### 4.3 Configuring Webhook URL in Easytools

**Step 1: Paste your webhook URL**

1. In Easytools API & Webhooks settings, find the "Webhook URL" field
2. Paste your complete webhook URL
3. Example: `https://yoursite.com/wp-json/easytools/v1/webhook`
4. Verify HTTPS is included
5. Don't add any extra parameters or slashes

**Step 2: Verify Signing Key**

Remember the signing key you generated in Part 1.5? Make sure it's still visible in Easytools.

1. **Locate "Webhook Signing Key" section**
2. **If you can still see the key:**
   - Verify it matches what you entered in WordPress
   - If they don't match, copy the Easytools key and update WordPress
3. **If you can't see the key (it's hidden):**
   - It's already configured correctly
   - As long as you copied it to WordPress earlier, you're good
   - If unsure, generate a new key and update both sides

**Step 3: Test the connection (if available)**

Some versions of Easytools have a "Test Webhook" button:

1. **Click "Test Webhook" or "Send Test"**
2. **Easytools sends a test payload to your WordPress**
3. **Check the response:**
   - ✓ Success: "Webhook received successfully"
   - ✗ Error: See troubleshooting below

**Common test errors:**

| Error | Cause | Solution |
|-------|-------|----------|
| "Connection timeout" | URL unreachable | Verify URL is correct, site is online |
| "SSL certificate error" | Invalid HTTPS | Check SSL certificate is valid |
| "403 Forbidden" | Security plugin blocking | Whitelist Easytools IPs in security settings |
| "Signature mismatch" | Keys don't match | Verify signing keys match exactly |

### 4.4 Selecting webhook events

Now choose which events should trigger webhooks.

**Event selection interface:**

In Easytools, you'll see a list of available events with checkboxes:

```
☑ product_assigned
☑ subscription_created
☑ subscription_expired
☑ subscription_cancelled
☐ subscription_renewed
☐ subscription_paused
☐ subscription_plan_changed
```

**Recommended configuration:**

**Essential (check these):**
- ☑ `product_assigned` - **CRITICAL** for granting access
- ☑ `subscription_expired` - **CRITICAL** for revoking access

**Recommended (check these):**
- ☑ `subscription_created` - Track new subscriptions
- ☑ `subscription_cancelled` - Handle cancellations

**Optional (check if you need them):**
- ☐ `subscription_renewed` - Track renewal events (product_assigned usually covers this)
- ☐ `subscription_paused` - If you offer pause functionality
- ☐ `subscription_plan_changed` - If you offer plan upgrades/downgrades

**Minimal configuration (if unsure):**
At minimum, enable these two:
- `product_assigned`
- `subscription_expired`

These two events handle the most critical scenarios:
- Granting access when someone subscribes
- Revoking access when subscription expires

### 4.5 Save webhook configuration

1. **Review all settings:**
   - Webhook URL correct
   - Signing key configured (even if hidden)
   - Events selected appropriately

2. **Click "Save" or "Update Webhook Settings"**
   - Look for confirmation message
   - "Webhook configuration saved successfully"

3. **Verify configuration:**
   - Refresh the page
   - Ensure URL and events are still set

**Configuration complete!**
✓ Easytools knows where to send webhooks (your WordPress URL)
✓ Webhooks are secured with HMAC signatures
✓ Relevant events will trigger notifications
✓ WordPress is ready to receive and process them

**Next: Test everything! (Part 5)**

---

## Part 5: Testing Your Webhook Integration

Now comes the fun part - testing that everything works!

### 5.1 Using the built-in Webhook Tester

The Easytools Subscription Manager plugin includes a powerful testing tool.

**Step 1: Access the Webhook Testing tab**

1. In WordPress admin, go to **Easytools Sub → 🧪 Webhook Testing**
2. You'll see a premium interface with example payloads

**Step 2: Understanding the interface**

The testing page has three main sections:

**A. Example Payloads**
- Pre-configured JSON examples for each event type
- Quick buttons to select common scenarios
- Examples: "Product Assigned", "Subscription Expired", etc.

**B. Payload Editor**
- Textarea showing the selected JSON
- Initially read-only
- Click "Edit" button to modify
- Useful for testing specific scenarios

**C. Test Controls**
- "Send Test Webhook" button
- Response display area
- Success/error messages

**Step 3: Select a test event**

1. **Choose "Product Assigned" example**
   - Click the "Product Assigned" button
   - The payload textarea populates with example JSON

2. **Review the payload:**
```json
{
  "event": "product_assigned",
  "customer_email": "test@example.com",
  "customer_id": "cus_test123",
  "product_name": "Premium Monthly Subscription",
  "subscription_type": "monthly",
  "is_subscription": true,
  "renewal_date": "2025-12-03T10:00:00Z"
}
```

**Step 4: Edit the payload (optional)**

To test with your actual user:

1. **Click the "Edit" button**
   - Textarea becomes editable
   - Cursor appears in the field

2. **Modify the customer_email:**
   - Change `"customer_email": "test@example.com"`
   - To your actual test user: `"customer_email": "yourtestemail@example.com"`

3. **Why this matters:**
   - The plugin will search for a WordPress user with this email
   - Use an email that exists in your WordPress users
   - Or use a fake email to test user creation

**Step 5: Send the test webhook**

1. **Click "Send Test Webhook" button**

2. **Wait for response (usually 1-2 seconds)**

3. **Check the response:**

**Success response:**
```
✓ Webhook processed successfully
User updated: test@example.com
Subscription status: active
```

**Error responses:**

```
✗ Signature verification failed
```
→ Signing key mismatch - check Part 3.2

```
✗ User not found: test@example.com
```
→ No WordPress user with that email - check email or enable auto-create

```
✗ Invalid JSON payload
```
→ Syntax error in JSON - check for missing commas, quotes

### 5.2 Checking Webhook Logs

After sending test webhooks, verify they were received and processed correctly.

**Step 1: Access Webhook Logs**

1. Go to **Easytools Sub → Webhook Logs**
2. You'll see a table of all received webhooks

**Step 2: Understanding the log table**

| Column | What it shows |
|--------|---------------|
| **Time** | When webhook was received |
| **Event** | Event type (product_assigned, etc.) |
| **Email** | Customer email from payload |
| **Status** | ✓ Success or ✗ Failed |
| **Actions** | View details button |

**Step 3: Inspecting a webhook**

1. **Click "View Details" on a log entry**

2. **Detailed view shows:**
   - Complete JSON payload received
   - Headers (including signature)
   - Processing result
   - User that was updated
   - Any error messages

3. **Example success log:**
```
Event: product_assigned
Email: test@example.com
Status: ✓ Processed successfully
Details:
- User found: test@example.com (ID: 42)
- Subscription status updated: active
- Renewal date set: 2025-12-03
- Role verified: Subscriber
```

4. **Example error log:**
```
Event: product_assigned
Email: test@example.com
Status: ✗ Failed
Error: User not found with email test@example.com
Suggestion: Enable auto-create users or create user manually
```

**Step 4: Using logs for debugging**

The logs are your best friend when troubleshooting:

**Scenario 1: No logs at all**
- Webhooks aren't reaching your site
- Check Easytools webhook URL configuration
- Verify firewall/security settings

**Scenario 2: Logs show "signature failed"**
- HMAC verification failing
- Check signing keys match exactly
- Look for extra spaces in key

**Scenario 3: Logs show "user not found"**
- Email mismatch issue
- Enable auto-create users
- Or create WordPress user manually first

**Scenario 4: Logs show success but access not working**
- User was updated successfully
- Check access control settings
- Verify user role is correct
- Clear WordPress cache

### 5.3 HMAC Calculator

The built-in HMAC calculator helps you test webhooks with external tools like Postman.

**When to use the HMAC Calculator:**

- Testing webhooks from Postman
- Verifying signature generation
- Debugging signature mismatches
- Learning how HMAC works

**Step 1: Access the calculator**

1. On the **Webhook Testing** page, scroll to "HMAC Signature Calculator"

**Step 2: Using the calculator**

**A. Prepare your payload**

1. **Copy a JSON payload:**
   - Use one of the examples
   - Or write your own custom payload

2. **Paste into "Payload" textarea**

**B. Enter your signing key**

1. **Paste your Webhook Signing Key**
   - Same key from Part 1.5
   - Same key configured in settings

2. **Verify no extra spaces:**
   - Should start with `sk_live_` or similar
   - No spaces before or after

**C. Generate signature**

1. **Click "Generate Signature" button**

2. **Signature appears below:**
   - Long hexadecimal string
   - Example: `a7b3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6z7a8b9c0d1e2f3`

3. **Click "Copy Signature" button**
   - Signature copied to clipboard
   - Ready to use in Postman

**How the calculator works:**

```
Input:
- Payload (JSON): {"event":"product_assigned",...}
- Signing Key: sk_live_abc123...

Process:
1. Normalize JSON (remove whitespace)
2. Calculate HMAC SHA-256
3. Output signature

Output:
- Signature: a7b3c4d5e6f7...
```

### 5.4 Testing with Postman (Advanced)

For advanced users, Postman allows you to send webhooks manually with full control.

**Step 1: Install Postman**

1. Download from: https://www.postman.com/downloads/
2. Install and open Postman
3. Create a free account (optional but recommended)

**Step 2: Create a new request**

1. **Click "New" → "HTTP Request"**

2. **Set request method:**
   - Select **POST** from dropdown

3. **Enter URL:**
   - Paste your webhook URL
   - Example: `https://yoursite.com/wp-json/easytools/v1/webhook`

**Step 3: Configure headers**

1. **Click "Headers" tab**

2. **Add two headers:**

| Header | Value |
|--------|-------|
| `Content-Type` | `application/json` |
| `x-webhook-signature` | [paste generated signature] |

**For the signature:**
- Use the HMAC Calculator (Part 5.3) to generate
- Must match the payload exactly
- Include no extra spaces

**Step 4: Add request body**

1. **Click "Body" tab**

2. **Select "raw" radio button**

3. **Select "JSON" from the dropdown** (next to GraphQL)

4. **Paste your JSON payload:**
```json
{
  "event": "product_assigned",
  "customer_email": "test@example.com",
  "customer_id": "cus_test123",
  "product_name": "Premium Monthly",
  "subscription_type": "monthly",
  "is_subscription": true,
  "renewal_date": "2025-12-03T10:00:00Z"
}
```

**IMPORTANT:**
- The payload here must EXACTLY match what you used in HMAC calculator
- If you change the payload, you must regenerate the signature

**Step 5: Send the request**

1. **Click "Send" button**

2. **Check the response:**

**Success (200 OK):**
```json
{
  "success": true,
  "message": "Webhook processed successfully",
  "user_email": "test@example.com",
  "user_id": 42
}
```

**Error (400 Bad Request):**
```json
{
  "success": false,
  "message": "Invalid webhook signature"
}
```

**Common Postman errors:**

| Response Code | Meaning | Solution |
|---------------|---------|----------|
| 200 OK | Success! | Webhook processed correctly |
| 400 Bad Request | Signature invalid | Regenerate signature, check key |
| 403 Forbidden | Access denied | Check security plugins, firewall |
| 404 Not Found | Wrong URL | Verify webhook URL is correct |
| 500 Server Error | WordPress error | Check WordPress error logs |

**Step 6: Verify in WordPress**

After successful Postman request:
1. Go to Webhook Logs in WordPress
2. Find the most recent entry
3. Verify it processed correctly
4. Check the user was updated

**Why signatures might fail:**

Even tiny differences break HMAC signatures:

**These are DIFFERENT:**
```json
{"event":"product_assigned"}
```
```json
{ "event": "product_assigned" }
```
```json
{
  "event": "product_assigned"
}
```

**Solution:**
1. Copy exact payload to HMAC calculator
2. Generate signature
3. Copy exact same payload to Postman body
4. Don't modify anything!

---

## Part 6: Setting Up Native Easytools Automations

In addition to webhooks, Easytools offers native WordPress integration for automatic user creation.

### 6.1 When to use Automations vs. Webhooks

**Understanding the difference:**

| Feature | Webhooks (Plugin) | Automations (Native) |
|---------|-------------------|---------------------|
| **Purpose** | Full subscription management | Simple user creation |
| **Content Protection** | Yes - page/site protection | No |
| **Access Control** | Yes - manage who sees what | No |
| **User Creation** | Yes (optional) | Yes (primary feature) |
| **Subscription Tracking** | Yes - all events | Basic |
| **Setup Complexity** | Moderate (plugin required) | Simple (no plugin) |
| **Customization** | High | Limited |
| **Best For** | Complete membership sites | Quick user account setup |

**Can you use both together?**

**YES - and this is often the best approach!**

**Recommended combination:**

1. **Use Automations for:**
   - Initial user account creation after purchase
   - Assigning WordPress role
   - Sending welcome email

2. **Use Webhooks for:**
   - Content protection
   - Managing ongoing subscription status
   - Handling renewals/expirations/cancellations
   - Detailed logging and monitoring

**Example workflow using both:**

```
User purchases subscription
    ↓
Automation: Creates WordPress account → User receives email
    ↓
Webhook: Updates subscription status → Grants access to protected pages
    ↓
User logs in and accesses premium content
```

**When to use only Automations:**
- You don't need content protection
- You just want user accounts created automatically
- You're using another plugin for access control
- Simple use case with minimal requirements

**When to use only Webhooks:**
- You already have a user registration system
- Users create accounts before purchasing
- You need full control over subscription logic
- You want detailed webhook logging

**For this tutorial:**
We'll set up Automations to complement the webhooks we configured earlier.

### 6.2 Adding WordPress application in Easytools

Let's connect your WordPress site to Easytools.

**Step 1: Access Automations**

1. **Log in to your Easytools dashboard**

2. **Navigate to Automations section**
   - Look for "Automations" in the main navigation
   - Or find it in Settings

3. **Click on "Applications" tab**
   - This shows all connected apps
   - You might see other integrations here (Mailchimp, etc.)

**Step 2: Add new application**

1. **Click "+ New application" button**

2. **A modal or new page opens showing available apps**

3. **Find and select "WordPress"**
   - Scroll through the list
   - Or use search: type "WordPress"
   - Click on the WordPress integration

**Step 3: Name your integration**

1. **Enter a descriptive name**
   - Example: "My WordPress Site"
   - Or: "Main Website"
   - Helps if you have multiple WordPress sites

2. **This name is for your reference only**
   - Makes it easier to select in scenarios later

### 6.3 Connecting WordPress to Easytools

Now you'll authenticate Easytools with your WordPress site.

**Step 1: Enter WordPress site URL**

1. **Find "WordPress URL" field**

2. **Enter your complete site URL:**
   - Format: `https://yoursite.com`
   - Include `https://` (required)
   - Don't include trailing slash
   - Don't include `/wp-admin` or any path

3. **Examples:**
   - ✓ `https://mysite.com`
   - ✓ `https://blog.mysite.com`
   - ✗ `mysite.com` (missing https)
   - ✗ `https://mysite.com/` (has trailing slash)
   - ✗ `https://mysite.com/wp-admin` (includes path)

**Step 2: Enter username**

1. **Find "Username" field**

2. **Enter your WordPress admin username:**
   - This is your WordPress login username
   - NOT your email (unless you use email to log in)
   - Case-sensitive
   - Example: `admin`, `john_smith`, etc.

**Step 3: Enter application password**

Remember the application password you created in Part 2.5?

1. **Find "Password" or "Application Password" field**

2. **Paste the application password:**
   - Format: `abcd 1234 efgh 5678 ijkl 9012`
   - Spaces are OK (Easytools usually handles them)
   - Or without spaces: `abcd1234efgh5678ijkl9012`

3. **Important reminders:**
   - This is NOT your regular WordPress password
   - This is the application password you generated
   - If you lost it, generate a new one (Part 2.5)

**Step 4: Test the connection**

1. **Click "Check Connection" or "Test Connection" button**

2. **Easytools attempts to connect to WordPress:**
   - Sends authentication request
   - Verifies credentials
   - Checks WordPress REST API availability

3. **Wait for response (5-10 seconds)**

**Success response:**
```
✓ Connection successful
WordPress version: 6.4
REST API: Available
```

**Error responses and solutions:**

| Error | Cause | Solution |
|-------|-------|----------|
| "Could not connect to WordPress" | URL unreachable | Verify URL is correct and site is online |
| "Authentication failed" | Wrong credentials | Check username and app password |
| "REST API not available" | REST API disabled | Enable WordPress REST API |
| "SSL certificate error" | Invalid HTTPS | Fix SSL certificate on your site |
| "Permission denied" | User role insufficient | Use Administrator account |

**Step 5: Save the application**

1. **Once connection test succeeds:**
   - Click "Save" or "Save Application" button

2. **Verify it's saved:**
   - You should see it in the Applications list
   - Status shows as "Connected" or green indicator

**Configuration complete!**
✓ WordPress connected to Easytools
✓ Authentication working
✓ Ready to create scenarios

### 6.4 Creating automation scenarios

Now let's create an automation to automatically create WordPress accounts when someone purchases.

**Step 1: Access Scenarios**

1. **In Easytools Automations, click "Scenarios" tab**

2. **You'll see a list of existing scenarios** (if any)

**Step 2: Create new scenario**

1. **Click "+ New scenario" button**

2. **Scenario configuration form opens**

**Step 3: Configure scenario basics**

**A. Scenario Name**
1. Enter a descriptive name
2. Example: "Create WP account on purchase"
3. Helps you identify it later

**B. Select Trigger Event**

1. **Find "Trigger" or "When" dropdown**

2. **Select event that should trigger this automation:**

| Event | When to use |
|-------|-------------|
| **Order Completed** | Most common - triggers immediately after successful payment |
| **Product Assigned** | When product is assigned (might be delayed) |
| **Subscription Created** | Only for new subscriptions (not one-time) |

3. **Recommended: "Order Completed"**
   - Triggers right after purchase
   - Works for both subscriptions and one-time purchases
   - Most reliable option

**C. Select Application**

1. **Find "Application" or "Action" dropdown**

2. **Select your WordPress application:**
   - The one you created in Step 6.3
   - Example: "My WordPress Site"

**D. Select Action**

1. **Find "Action" dropdown**

2. **Select: "Create User"**
   - This creates a new WordPress user account
   - Other actions might be available (update user, etc.)

**Step 4: Configure user creation settings**

**A. User Role**

1. **Find "Role" dropdown**

2. **Select role for new users:**
   - **Subscriber** (recommended for regular members)
   - Contributor (if members can submit content)
   - Author (if members can publish)
   - Editor (only for trusted members)
   - Administrator (NEVER select this!)

3. **What happens:**
   - New WordPress accounts get this role automatically
   - Determines what they can do in WordPress

**B. Field Mapping**

Some Easytools versions allow you to map fields:

| Easytools Field | WordPress Field | Notes |
|-----------------|-----------------|-------|
| customer_email | user_email | **Required** - used for login |
| customer_name | display_name | User's display name |
| first_name | first_name | Optional |
| last_name | last_name | Optional |

Usually this is automatic, but verify:
- Email is mapped correctly (critical!)
- Name fields mapped as desired

**C. Additional Options**

Depending on your Easytools version:

- **Send welcome email:** Enable to notify user
- **Set random password:** Let WordPress generate password
- **Require password reset:** Force user to set password on first login

**Step 5: Assign to products**

Choose which products trigger this automation.

1. **Find "Products" or "Apply to" section**

2. **Select products:**
   - Click checkboxes for relevant products
   - You can select multiple
   - Or select "All products" if available

3. **Optional: Select specific variants**
   - If you have product variants (Basic, Pro, Premium tiers)
   - Select which variants trigger automation
   - Or leave all selected

**Example configuration:**
```
Products:
☑ Monthly Subscription
☑ Yearly Subscription
☑ Lifetime Access

Variants (Monthly Subscription):
☑ Basic Tier
☑ Pro Tier
☑ Enterprise Tier
```

**Step 6: Review and save**

1. **Review all settings:**
   - Trigger event: Order Completed
   - Application: Your WordPress site
   - Action: Create User
   - Role: Subscriber
   - Products: Selected

2. **Click "Save" or "Create Scenario"**

3. **Verify scenario is active:**
   - Look for "Enabled" or "Active" status
   - Some scenarios need to be manually activated

### 6.5 Scenario execution and monitoring

After creating scenarios, you can monitor their execution.

**Accessing execution logs:**

1. **In Easytools Automations, click "Executions" tab**

2. **You'll see a table of automation runs:**

| Column | Shows |
|--------|-------|
| **Time** | When automation ran |
| **Scenario** | Which scenario executed |
| **Trigger** | What caused it (order ID, email) |
| **Status** | Success, Failed, Pending |
| **Actions** | View, Retry, Cancel |

**Understanding execution status:**

**✓ Success (Green)**
- Automation completed successfully
- User account created in WordPress
- Everything worked as expected

**✗ Failed (Red)**
- Automation encountered an error
- User account NOT created
- Requires investigation

**⏳ Pending (Yellow)**
- Automation queued but not yet executed
- Usually executes within 1-2 minutes
- May be waiting for other conditions

**⟲ Retrying (Blue)**
- Previous attempt failed
- System is automatically retrying
- Common for temporary issues (network, etc.)

**Inspecting execution details:**

1. **Click "View" or "Details" on an execution**

2. **Detailed view shows:**
   - Trigger data (customer email, order ID)
   - Action taken (create user)
   - Result (success/error message)
   - Timestamp and duration

**Example success log:**
```
Scenario: Create WP account on purchase
Trigger: Order #12345
Customer: john@example.com
Action: Create WordPress User
Result: ✓ Success
Details:
- User created: john@example.com
- User ID: 789
- Role assigned: Subscriber
- Welcome email sent
```

**Example error log:**
```
Scenario: Create WP account on purchase
Trigger: Order #12346
Customer: jane@example.com
Action: Create WordPress User
Result: ✗ Failed
Error: User already exists with email jane@example.com
Suggestion: User may have registered manually first
```

**Retrying failed executions:**

If an execution fails, you can retry it manually:

1. **Find the failed execution in the list**
2. **Click "Retry" button**
3. **Confirm retry**
4. **System attempts automation again**

**When to retry:**
- Temporary network issues
- WordPress was temporarily down
- Password credential was recently updated

**When NOT to retry:**
- "User already exists" error (not actually an error!)
- Invalid email address
- Insufficient permissions (fix configuration first)

**Canceling pending executions:**

If an execution is stuck in "Pending":

1. **Click "Cancel" button**
2. **Confirm cancellation**
3. **Execution is aborted**

**When to cancel:**
- Realized you made a configuration error
- Customer requested refund (account not needed)
- Duplicate execution (already processed by webhook)

**Monitoring best practices:**

1. **Check executions regularly**
   - Daily for first week
   - Weekly thereafter
   - After any configuration changes

2. **Investigate failures quickly**
   - Failed automations mean users can't access content
   - Poor customer experience
   - May require manual user creation

3. **Set up notifications (if available)**
   - Get email when automation fails
   - Respond proactively

4. **Keep logs for reference**
   - Useful for debugging
   - Track automation performance
   - Audit trail

---

## Part 7: Testing the Complete Flow

Now let's test the entire user journey from purchase to access.

### 7.1 Creating a test subscriber

**Step 1: Set up a test product**

1. **In Easytools, navigate to Products**

2. **Create a test product (if you don't have one):**
   - Name: "Test Subscription - DO NOT BUY"
   - Price: $1.00 (or use test mode)
   - Type: Subscription (Monthly)

3. **Enable Easytools test/sandbox mode:**
   - Check Easytools documentation for your payment processor
   - Stripe test mode: Use test API keys
   - PayPal sandbox: Enable sandbox mode
   - This avoids charging real money!

**Step 2: Create a test WordPress user**

To test email matching, create the user first:

1. **In WordPress admin, go to Users → Add New**

2. **Fill in user details:**
   - Username: `testuser`
   - Email: `testuser@yourdomain.com` (use a real email you can check)
   - First Name: `Test`
   - Last Name: `User`
   - Role: `Subscriber`
   - Set password or let WordPress generate one

3. **Click "Add New User"**

4. **Important: Remember this email address!**
   - You'll use it when making test purchase
   - Email must match exactly for automation to work

**Step 3: Verify user has no access**

Before testing, confirm the user starts without premium access:

1. **Check user's subscription status:**
   - Go to Users → All Users
   - Hover over testuser → click Edit
   - Scroll to "Easytools Subscription" section
   - Should show: No active subscription

2. **Test access to protected page:**
   - Log out of WordPress admin (or use incognito window)
   - Visit a protected page
   - Should be redirected to checkout URL

### 7.2 Protecting content in WordPress

Create a test page to verify access control works.

**Step 1: Create premium content page**

1. **Go to Pages → Add New**

2. **Create a test page:**
   - Title: "Premium Test Content"
   - Content: "This is exclusive content for subscribers only. If you can see this, your subscription is working!"

3. **Publish the page**

**Step 2: Protect the page**

1. **Go to Easytools Sub → Settings**

2. **In Access Control section:**
   - Find "Protected Pages" list
   - Check the box next to "Premium Test Content"
   - Click "Save Changes"

**Step 3: Verify protection works**

1. **Log out of WordPress (or open incognito window)**

2. **Visit the premium page URL:**
   - Example: `https://yoursite.com/premium-test-content`

3. **You should be redirected:**
   - Either to your Easytools checkout URL
   - Or to your custom "Join Now" page
   - You should NOT see the page content

4. **If you can still see the content:**
   - Clear WordPress cache
   - Check access control settings saved
   - Verify you're truly logged out
   - Try a different browser/incognito

### 7.3 Testing the redirect flow

Document the user experience before purchase.

**Step 1: User arrives at site**

1. **Visit your site's homepage (logged out)**

2. **Navigate to premium content:**
   - Click a link to protected page
   - Or directly visit protected page URL

**Step 2: Protection triggers**

1. **What the user sees:**
   - Page starts to load
   - Then redirects (usually instant)
   - Arrives at checkout or sales page

2. **Verify redirect URL is correct:**
   - Should match your configured Checkout URL
   - If using sales page, verify it has "Subscribe" button

**Step 3: Sales page experience (if using one)**

If you set up a custom sales page:

1. **User sees benefits:**
   - Membership benefits listed
   - Pricing displayed clearly
   - Testimonials/social proof

2. **"Subscribe Now" button:**
   - Prominent call-to-action
   - Links to Easytools checkout
   - May pre-fill email (covered next)

**Step 4: Arriving at Easytools checkout**

1. **User lands on Easytools payment page**

2. **Checkout form shows:**
   - Product name and price
   - Email field
   - Payment method fields
   - Subscribe/Pay button

### 7.4 Completing a test purchase

Now let's make a test purchase.

**Step 1: Pre-fill email (important!)**

For the automation and webhook to work, the email must match your WordPress user.

**Method A: Pre-fill via URL parameter**

Add email to Easytools checkout URL:

```
https://yourstorename.easytools.app/checkout?email=testuser@yourdomain.com
```

**Method B: Have user enter matching email**

If URL pre-fill isn't available:
1. Manually type the exact email
2. `testuser@yourdomain.com`
3. Double-check for typos!

**Why this is critical:**
- Automation creates account using this email
- Webhook finds user by this email
- If emails don't match → access won't work

**Step 2: Fill out checkout form**

1. **Email:** testuser@yourdomain.com (matching WordPress user)

2. **Name:** Test User

3. **Payment method:**
   - If test mode: Use test card
   - Stripe test card: `4242 4242 4242 4242`
   - Expiry: Any future date
   - CVC: Any 3 digits

4. **Review details:**
   - Product: Your test subscription
   - Price: $1.00 (or test amount)
   - Recurring: Monthly

**Step 3: Complete payment**

1. **Click "Subscribe" or "Pay Now" button**

2. **Wait for processing:**
   - Usually 2-5 seconds
   - Progress indicator shows

3. **Success page appears:**
   - "Thank you for your purchase!"
   - Order confirmation
   - May show order number

**Step 4: Note the timestamp**

- Check the time: [current time]
- Webhooks usually arrive within 5-10 seconds
- Automations may take 1-2 minutes

### 7.5 Verifying webhook delivery

Now check if the webhook was received.

**Step 1: Check webhook logs**

1. **In WordPress admin, go to Easytools Sub → Webhook Logs**

2. **Look for recent entry (within last minute):**
   - Timestamp should match your purchase time
   - Event: `product_assigned` or `subscription_created`
   - Email: testuser@yourdomain.com

3. **Check status:**
   - ✓ Success: Great! Webhook processed
   - ✗ Failed: Click to view error details

**Step 2: Inspect webhook details**

1. **Click "View Details" on the webhook log**

2. **Verify payload contents:**
```json
{
  "event": "product_assigned",
  "customer_email": "testuser@yourdomain.com",
  "customer_id": "cus_real_id_from_easytools",
  "product_name": "Test Subscription",
  "subscription_type": "monthly",
  "renewal_date": "2025-02-03T14:30:00Z"
}
```

3. **Check processing result:**
   - User found: testuser@yourdomain.com
   - Subscription status updated: active
   - Renewal date set: [date]

**Step 3: Verify user was updated**

1. **Go to Users → All Users in WordPress**

2. **Find and edit your test user**

3. **Scroll to "Easytools Subscription" section:**
   - Subscription Status: ✓ Active
   - Customer ID: [from Easytools]
   - Renewal Date: [from webhook]
   - Subscription Type: monthly

4. **User Role:**
   - Should still be "Subscriber" (or configured role)

**If webhook shows success:**
✓ Webhook received and verified
✓ User found by email
✓ Subscription data saved
→ Ready for next step!

**If webhook failed:**
See troubleshooting in Part 9

### 7.6 Verifying automation execution (if using)

If you set up Easytools Automations, verify they ran.

**Step 1: Check Easytools execution logs**

1. **In Easytools, go to Automations → Executions**

2. **Find recent execution:**
   - Scenario: "Create WP account on purchase"
   - Trigger: Your order number
   - Time: Right after purchase

3. **Check status:**
   - ✓ Success: User account created/updated
   - ✗ Failed: May show "User already exists" (this is OK if user existed)

**Step 2: Understand "User already exists" result**

If you created the WordPress user beforehand (Step 7.1):
- Automation will show: "Failed: User already exists"
- **This is not actually an error!**
- User already had account, so couldn't create duplicate
- Webhook handles the subscription status instead

**When automation is really needed:**
- User doesn't have WordPress account yet
- Easytools automation creates it
- Then webhook updates subscription status

**Best practice: Combine both**
1. Automation: Creates account if doesn't exist
2. Webhook: Always updates subscription status
3. One or both will handle each purchase correctly

### 7.7 Testing access unlock

Now the moment of truth - can the user access premium content?

**Step 1: Return to WordPress site**

Simulate the user's experience:

1. **Close or refresh your browser**

2. **Visit your site homepage**
   - You should still be logged out
   - (If you're logged in as admin, log out first)

**Step 2: Log in as test user**

1. **Go to: `https://yoursite.com/wp-login.php`**

2. **Log in with test user credentials:**
   - Username: `testuser`
   - Password: [the password you set or was emailed]

3. **Login should succeed**
   - You're now logged in as testuser

**Step 3: Try accessing protected page**

1. **Navigate to the premium test page:**
   - Example: `https://yoursite.com/premium-test-content`

2. **What should happen:**
   - Page loads normally (no redirect!)
   - You can see the content
   - "This is exclusive content for subscribers only..."

3. **Success! Access is working!** ✓

**If you're still redirected:**
- Wait 30 seconds and try again (caching)
- Clear WordPress cache
- Verify webhook processed successfully (Step 7.5)
- Check user's subscription status (should be active)
- See troubleshooting (Part 9.4)

**Step 4: Test shortcode (if using)**

If you added the `[easytools_sub_status]` shortcode:

1. **Add shortcode to a page or post:**
   - Edit any page
   - Add shortcode: `[easytools_sub_status]`
   - Save and view page

2. **What you should see:**
```
Subscription Status: Active
Renewal Date: February 3, 2025
Subscription Type: monthly
```

3. **This confirms:**
   - Plugin recognizes user's subscription
   - Data from webhook was saved correctly

### 7.8 Testing subscription expiration

Finally, test that access is revoked when subscription expires.

**Step 1: Manually trigger expiration**

Since you don't want to wait a month:

**Method A: Use webhook tester**

1. **Go to Easytools Sub → Webhook Testing**

2. **Select "Subscription Expired" example**

3. **Click "Edit" and modify email:**
   - Change to: `testuser@yourdomain.com`

4. **Example payload:**
```json
{
  "event": "subscription_expired",
  "customer_email": "testuser@yourdomain.com",
  "customer_id": "cus_test123",
  "product_name": "Test Subscription",
  "expiration_date": "2025-01-03T14:30:00Z"
}
```

5. **Click "Send Test Webhook"**

**Method B: Cancel in Easytools dashboard**

1. In Easytools, find the test subscription
2. Cancel or expire it manually
3. Real webhook will be sent

**Step 2: Verify webhook received**

1. **Check Webhook Logs**

2. **Look for:**
   - Event: `subscription_expired`
   - Email: testuser@yourdomain.com
   - Status: ✓ Success

**Step 3: Verify user status updated**

1. **Go to Users → All Users**

2. **Edit test user**

3. **Easytools Subscription section:**
   - Status: ✗ Expired (or Inactive)
   - Access Expired: Yes

**Step 4: Test access is revoked**

1. **Still logged in as testuser, visit premium page**

2. **What should happen:**
   - Redirected to checkout URL
   - Cannot see premium content anymore
   - Access successfully revoked! ✓

**If still has access:**
- Clear WordPress cache
- Log out and back in
- Check webhook processed (logs)
- See troubleshooting (Part 9.4)

### 7.9 Complete flow summary

**Congratulations!** You've tested the entire subscription lifecycle:

1. ✓ User without subscription redirected to checkout
2. ✓ User completes purchase in Easytools
3. ✓ Webhook delivered to WordPress (5-10 seconds)
4. ✓ User account created/updated automatically
5. ✓ Subscription status marked as active
6. ✓ User can access protected content
7. ✓ Subscription expiration revokes access
8. ✓ User redirected to checkout to renew

**Your integration is working!**

**Next steps:**
- Test with real (non-test) subscription (when ready)
- Set up additional protected pages
- Customize redirect pages
- Configure email notifications
- Review security settings (disable Developer Mode if enabled!)

---

## Part 8: Advanced Features

### 8.1 Shortcodes for subscription status

The plugin provides shortcodes to display subscription information.

**Available shortcodes:**

**1. `[easytools_sub_status]`**

Displays current user's subscription status.

**Usage:**
```
[easytools_sub_status]
```

**Output (for active subscriber):**
```
Subscription Status: Active
Renewal Date: February 3, 2025
Subscription Type: monthly
Customer ID: cus_abc123
```

**Output (for non-subscriber):**
```
No active subscription
```

**Where to use:**
- Account/profile pages
- Dashboard widgets
- Sidebar information
- Footer of members area

**2. Custom shortcode parameters (if available):**

Some versions allow parameters:
```
[easytools_sub_status field="renewal_date"]
```
Outputs: `February 3, 2025`

### 8.2 Conditional content display

Show different content based on subscription status.

**Method 1: Using shortcodes**

```
[easytools_if_subscribed]
  This content only subscribers see.
[/easytools_if_subscribed]

[easytools_if_not_subscribed]
  <a href="https://yourstore.easytools.app">Subscribe now!</a>
[/easytools_if_not_subscribed]
```

**Method 2: Using WordPress conditional tags (theme)**

If you're editing theme files:

```php
<?php if (get_user_meta(get_current_user_id(), 'easytools_subscribed', true) === 'yes'): ?>
  <p>Welcome, premium member!</p>
<?php else: ?>
  <p><a href="<?php echo get_option('easytools_checkout_url'); ?>">Subscribe for access</a></p>
<?php endif; ?>
```

**Use cases:**
- Show "Subscribe" button to non-subscribers
- Show "Manage Subscription" button to subscribers
- Display subscriber-only navigation menu items
- Unlock download buttons for subscribers

### 8.3 Multiple subscription tiers

Handle different subscription levels with distinct access.

**Scenario:**
- Basic Plan ($10/month) - Access to basic content
- Pro Plan ($20/month) - Access to basic + pro content
- Enterprise Plan ($50/month) - Access to everything

**Implementation strategy:**

**Option A: Use WordPress roles**

1. **Create custom roles:**
   - Basic Subscriber
   - Pro Subscriber
   - Enterprise Subscriber

2. **Map Easytools products to roles:**
   - Configure plugin to assign different roles per product
   - Or use code/filter to customize

3. **Protect pages by role:**
   - Basic pages: Require Basic, Pro, or Enterprise role
   - Pro pages: Require Pro or Enterprise role
   - Enterprise pages: Require Enterprise role only

**Option B: Use product IDs in custom logic**

Store product_id from webhook and check it programmatically.

**Option C: Use a membership plugin**

Combine with plugins like:
- MemberPress
- Restrict Content Pro
- Paid Memberships Pro

These provide more granular tier management.

### 8.4 Handling lifetime purchases

One-time purchases grant permanent access.

**How the plugin handles this:**

When webhook has `subscription_one_time: true`:
- User marked as subscribed
- No renewal date set (or set far in future)
- Access never expires (unless manually revoked)

**Differentiating in your site:**

Check subscription type:
```php
$sub_type = get_user_meta($user_id, 'easytools_subscription_type', true);

if ($sub_type === 'lifetime') {
  echo "Lifetime Access";
} else {
  echo "Subscription renews: " . $renewal_date;
}
```

**Display example:**
```
[For lifetime members]
You have lifetime access - no renewal needed!

[For monthly subscribers]
Your subscription renews on February 3, 2025
```

### 8.5 Monitoring subscription health

**Dashboard Widget:**

The plugin adds a widget to your WordPress Dashboard showing:
- Total active subscriptions
- Recent webhook activity
- Failed webhooks (if any)
- Quick stats

**Accessing detailed reports:**

1. **Go to Easytools Sub → Dashboard**

2. **View statistics:**
   - Active subscribers count
   - New subscriptions this month
   - Expired subscriptions
   - Webhook success rate

**Monitoring webhook health:**

1. **Go to Webhook Logs regularly**

2. **Look for patterns:**
   - All recent webhooks successful? ✓ Good!
   - Multiple failures? ✗ Investigate

3. **Set up monitoring (advanced):**
   - Use WordPress plugins like "WP Crontrol" to schedule checks
   - Get email alerts for failed webhooks
   - Monitor uptime with external service

**Key metrics to track:**

| Metric | Target | How to check |
|--------|--------|--------------|
| Webhook success rate | >99% | Webhook Logs |
| Webhook delivery time | <10 sec | Webhook Logs (timestamp) |
| Active subscriptions | Growing | Dashboard Widget |
| Failed subscriptions | <1% | Webhook Logs (errors) |

---

## Part 9: Troubleshooting Common Issues

### 9.1 Webhook not being received

**Symptoms:**
- Purchase completed in Easytools
- No entry in WordPress Webhook Logs
- User not updated

**Diagnosis steps:**

**Step 1: Verify webhook URL in Easytools**

1. Go to Easytools → Settings → API & Webhooks
2. Check the webhook URL
3. Should be: `https://yoursite.com/wp-json/easytools/v1/webhook`
4. Common mistakes:
   - `http://` instead of `https://`
   - Extra trailing slash: `/webhook/`
   - Wrong domain
   - Missing `/wp-json/`

**Step 2: Test REST API accessibility**

1. Open browser
2. Visit: `https://yoursite.com/wp-json/`
3. Should see JSON output
4. If you see "REST API disabled" or error:
   - Check if REST API is blocked
   - Look in security plugins
   - Check .htaccess rules

**Step 3: Check SSL certificate**

1. Visit your site in browser
2. Check for padlock icon in address bar
3. Click padlock → Certificate should be valid
4. If certificate invalid:
   - Contact hosting provider
   - Install/renew SSL certificate
   - Webhooks won't work without valid SSL

**Step 4: Check firewall/security plugins**

Security plugins might block webhook requests:

1. **Check these plugins:**
   - Wordfence
   - Sucuri Security
   - iThemes Security
   - All In One WP Security

2. **Look for:**
   - Blocked IP addresses
   - Rate limiting rules
   - Whitelist Easytools IPs (ask Easytools support for IP ranges)

3. **Temporarily disable security plugin:**
   - Make test purchase
   - If webhook now arrives → security plugin was blocking
   - Re-enable and configure whitelist

**Step 5: Check server firewall**

Some hosts have server-level firewalls:

1. Contact your hosting provider
2. Ask if they're blocking POST requests to `/wp-json/`
3. Request they whitelist Easytools webhook IPs

**Step 6: Check PHP errors**

Webhook might be arriving but PHP error prevents processing:

1. Enable WordPress debug mode:
```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

2. Make test purchase

3. Check debug log:
   - Location: `/wp-content/debug.log`
   - Look for errors around webhook time

### 9.2 HMAC signature verification failures

**Symptoms:**
- Webhook appears in logs
- Status: ✗ Failed
- Error: "Invalid webhook signature" or "Signature verification failed"

**Diagnosis steps:**

**Step 1: Verify signing keys match**

1. **In WordPress:**
   - Go to Easytools Sub → Settings
   - Note the Webhook Signing Key

2. **In Easytools:**
   - Go to Settings → API & Webhooks
   - Check the Webhook Signing Key section
   - If visible, compare with WordPress

3. **Keys must match EXACTLY:**
   - No extra spaces
   - Same capitalization
   - Same format (sk_live_xxx)

**Common mismatches:**

| Issue | Example | Fix |
|-------|---------|-----|
| Extra space | `sk_live_abc ` | Remove trailing space |
| Missing characters | `sk_live_ab` (truncated) | Copy complete key |
| Different keys | Different on each side | Regenerate and update both |

**Step 2: Test with HMAC Calculator**

1. **Go to Webhook Testing tab**
2. **Select example payload**
3. **Enter your signing key**
4. **Click "Generate Signature"**
5. **If error occurs:**
   - Signing key format is wrong
   - Regenerate key in Easytools

**Step 3: Regenerate signing key (if necessary)**

1. **In Easytools:**
   - Go to API & Webhooks
   - Find "Regenerate Signing Key" button
   - Click and confirm

2. **Copy new key immediately**

3. **Update in WordPress:**
   - Easytools Sub → Settings
   - Paste new key
   - Save Changes

4. **Test again with webhook tester**

**Step 4: Temporarily enable Developer Mode**

**⚠️ Only for testing! Disable after!**

1. **In WordPress:**
   - Easytools Sub → Settings
   - Enable "Developer Mode"
   - Save Changes

2. **Make test purchase**

3. **Check if webhook processes:**
   - If YES → problem is signature/key
   - If NO → different issue

4. **DISABLE Developer Mode after testing!**

**Step 5: Check for JSON formatting issues**

Signature calculated on exact payload:

1. **View failed webhook in logs**
2. **Check JSON formatting:**
   - Any strange characters?
   - Encoding issues (é, ñ, etc.)?
   - Extra whitespace?

3. **Plugin should handle this automatically**
   - It normalizes JSON before verification
   - But some edge cases might fail

### 9.3 User not being updated

**Symptoms:**
- Webhook received successfully (✓ in logs)
- But user's subscription status not updated
- User still can't access content

**Diagnosis steps:**

**Step 1: Verify email matches**

1. **Check webhook payload in logs:**
   - Find `customer_email` field
   - Note the exact email: `john@example.com`

2. **Check WordPress users:**
   - Go to Users → All Users
   - Search for that exact email
   - Case-sensitive! john@example.com ≠ John@example.com

3. **If no user found:**
   - Enable "Auto-Create Users" in settings
   - Or manually create user with matching email

**Step 2: Verify webhook payload has required fields**

Check webhook payload includes:
- `customer_email` (required)
- `event` (required)
- `subscription_type` or `is_subscription`

If missing, webhook can't update user.

**Step 3: Check user meta directly**

1. **Install plugin: "User Meta Manager" or similar**

2. **View user meta for test user:**
   - Should have keys like:
     - `easytools_subscribed`
     - `easytools_customer_id`
     - `easytools_renewal_date`

3. **If meta fields missing:**
   - Webhook not actually updating database
   - Check PHP error logs

**Step 4: Verify user role**

1. **Edit the user in WordPress**
2. **Check their role:**
   - Should be Subscriber (or configured role)
   - If "No role" → assign role manually

3. **Some access control plugins require specific role**

### 9.4 Access not being granted after purchase

**Symptoms:**
- Webhook processed successfully
- User status shows as "subscribed"
- But user still can't access protected pages (redirected to checkout)

**Diagnosis steps:**

**Step 1: Clear all caches**

Caching is the #1 cause:

1. **Clear WordPress cache:**
   - If using caching plugin (WP Super Cache, W3 Total Cache, etc.):
     - Find "Clear Cache" button
     - Clear all cache

2. **Clear browser cache:**
   - Or use incognito/private window

3. **Clear server cache:**
   - Some hosts have server-level cache (Cloudflare, Varnish)
   - Clear via hosting control panel

4. **Test again after clearing all caches**

**Step 2: Verify page is actually protected**

1. **Go to Easytools Sub → Settings**
2. **Check Protected Pages list**
3. **Verify the page is checked**
4. **Save changes again**

**Step 3: Check user is logged in**

Seems obvious, but:

1. **Verify you're logged in as the test user**
   - Check username in admin bar
   - Log out and log back in

2. **Cookies might be cleared**

**Step 4: Check access control logic**

1. **Review access control settings**
2. **Are you using:**
   - "Protect entire site" mode?
   - "Protect specific pages" mode?

3. **Is the page in "Always Accessible" exceptions?**
   - If yes, it won't be protected!

**Step 5: Check for plugin conflicts**

Other plugins might interfere:

1. **Deactivate other membership/access plugins temporarily:**
   - MemberPress
   - Restrict Content
   - Any other access control plugins

2. **Test again**

3. **If now works:**
   - Conflict with other plugin
   - Choose one system or configure to work together

**Step 6: Check user meta values**

Use plugin to inspect user meta:

1. **Look for:**
   - `easytools_subscribed` should be `yes` or `true`

2. **If value is wrong:**
   - Manually set to correct value
   - Check why webhook isn't setting correctly

### 9.5 Automations not executing

**Symptoms:**
- Purchase completed
- No execution in Easytools Automations → Executions log
- WordPress user not created

**Diagnosis steps:**

**Step 1: Verify connection still active**

1. **In Easytools → Automations → Applications**
2. **Find your WordPress application**
3. **Check status:**
   - ✓ Connected (green) = OK
   - ✗ Disconnected (red) = Problem

4. **If disconnected:**
   - Click "Test Connection"
   - If fails, check credentials

**Step 2: Verify scenario is enabled**

1. **Go to Automations → Scenarios**
2. **Find your scenario**
3. **Check status:**
   - Should be "Active" or "Enabled"
   - If "Disabled" → Enable it

**Step 3: Verify scenario assigned to product**

1. **Edit the scenario**
2. **Check "Products" section:**
   - Is the purchased product selected?
   - If not, check the appropriate product

**Step 4: Check for failed executions**

1. **Go to Executions tab**
2. **Filter by Failed status**
3. **Look for error messages:**

Common errors:
- "User already exists" - Not actually an error, user existed already
- "Authentication failed" - Application password expired or changed
- "Connection timeout" - WordPress site unreachable

**Step 5: Regenerate application password**

If authentication failing:

1. **In WordPress:**
   - Go to Users → Profile
   - Revoke old Easytools application password
   - Generate new one
   - Copy it

2. **In Easytools:**
   - Go to Automations → Applications
   - Edit WordPress application
   - Paste new password
   - Test connection
   - Save

### 9.6 Redirect loop issues

**Symptoms:**
- Browser shows "Too many redirects" error
- Page keeps redirecting endlessly
- Can't access any pages

**Diagnosis steps:**

**Step 1: Verify checkout URL**

1. **Check Easytools Sub → Settings**
2. **Checkout URL field**
3. **Must NOT be a page on your own site!**

**Bad example (causes loop):**
```
Checkout URL: https://yoursite.com/subscribe
Protected Page: https://yoursite.com/subscribe
→ Redirects to itself = LOOP
```

**Good example:**
```
Checkout URL: https://yourstorename.easytools.app
Protected Page: https://yoursite.com/premium-content
→ Redirects to external Easytools site = OK
```

**Step 2: Check "Always Accessible" pages**

Important pages must be in exceptions:
- Homepage (if protecting entire site)
- Login page (automatically handled, but verify)
- Any landing/sales pages

**Step 3: Clear cookies**

1. Clear browser cookies for your site
2. Or use incognito mode
3. Test again

**Step 4: Disable protect entire site temporarily**

1. **Change to "Protect Specific Pages" mode**
2. **Uncheck all pages**
3. **Save**
4. **Test if loop stops**
5. **If stops → configuration issue with page protection**

**Step 5: Check .htaccess rules**

Some redirect rules in .htaccess conflict:

1. Access your site via FTP/file manager
2. Open `.htaccess` file (in WordPress root)
3. Look for redirect rules
4. Temporarily rename .htaccess to .htaccess-backup
5. Test again
6. If loop stops → .htaccess conflict

---

## Part 10: Best Practices & Security

### 10.1 Security recommendations

**Critical security practices:**

**1. Always use HMAC verification in production**
- ✗ Developer Mode must be DISABLED on live sites
- ✓ Webhook Signing Key must be configured
- ✓ Signatures verified on every webhook

**2. Keep signing key secure**
- Store in password manager
- Never commit to Git/version control
- Don't share publicly
- Regenerate if compromised

**3. Use strong application passwords**
- Long, random passwords
- Different password per integration
- Revoke unused passwords regularly
- Review authorized applications monthly

**4. SSL/HTTPS is mandatory**
- All data encrypted in transit
- Webhooks won't work without SSL
- Get free SSL from Let's Encrypt

**5. Keep WordPress updated**
- Update WordPress core regularly
- Update plugins (including Easytools plugin)
- Update themes
- Outdated software = security vulnerabilities

**6. Limit user permissions**
- Subscribers should have minimal permissions
- Never assign Administrator role automatically
- Review user roles periodically

**7. Monitor webhook logs**
- Check for suspicious activity
- Unusual IP addresses
- Failed signature verifications (attack attempts)
- Report attacks to Easytools

**8. Backup regularly**
- Backup WordPress database daily
- Backup includes user subscription data
- Test restore process
- Store backups securely offsite

### 10.2 Performance optimization

**Optimize webhook handling:**

**1. Webhook response time**
- Target: <2 seconds
- Plugin processes webhooks quickly
- But slow server = slow response

**2. Reduce server load**
- Don't trigger heavy operations in webhook handler
- Send emails async (queue)
- Defer non-critical updates

**3. Caching strategies**
- Cache public pages aggressively
- EXCLUDE protected pages from cache
- Bypass cache for logged-in users

**4. Database optimization**
- Regular database cleanup
- Optimize tables monthly
- Remove old webhook logs (keep 90 days)

### 10.3 User experience tips

**Improve subscriber experience:**

**1. Set appropriate redirect delays**
- In Easytools, configure post-purchase redirect
- Wait 10-15 seconds before redirecting (allows webhooks to process)
- Show "Processing..." message

**2. Show processing messages**
After purchase redirect:
```
"Thank you for subscribing!
Your account is being activated...
This may take up to 30 seconds."
```

**3. Clear communication about subscription status**
- Show renewal date prominently
- Send reminder before renewal
- Explain what happens when subscription expires

**4. Graceful error handling**
If webhook fails:
- User experience shouldn't break
- Show: "Activation in progress, please wait"
- Manual fallback: Support can activate manually

**5. Easy cancellation**
- Provide clear cancellation link
- Link to Easytools customer portal
- Don't make cancellation difficult

### 10.4 Maintenance and monitoring

**Regular maintenance tasks:**

**Daily:**
- Check Webhook Logs for failures
- Respond to failed webhooks within 24 hours

**Weekly:**
- Review automation executions
- Check for pattern of failures
- Monitor active subscription count

**Monthly:**
- Review and revoke unused application passwords
- Update WordPress and plugins
- Review access control settings
- Clean up old webhook logs

**Quarterly:**
- Full security audit
- Review user roles and permissions
- Test full purchase flow
- Update documentation

**After WordPress updates:**
- Test webhook still works
- Test protected pages still work
- Test user login and access
- Check for plugin conflicts

---

## Part 11: Summary & Next Steps

### 11.1 What you've accomplished

Congratulations! You've successfully:

✓ **Understood webhooks**
- How they work
- Why they're important
- Security with HMAC

✓ **Installed and configured plugin**
- Easytools Subscription Manager installed
- Settings configured correctly
- Access control rules defined

✓ **Set up webhook integration**
- Easytools sends webhooks to WordPress
- Signatures verified automatically
- Users updated in real-time

✓ **Configured automations (optional)**
- WordPress connected to Easytools
- User accounts created automatically
- Roles assigned correctly

✓ **Protected content**
- Premium pages protected
- Non-subscribers redirected
- Subscribers can access content

✓ **Tested thoroughly**
- Complete purchase flow tested
- Webhook delivery verified
- Access control confirmed
- Expiration tested

✓ **Learned troubleshooting**
- Common issues and solutions
- How to use logs effectively
- When to check what

### 11.2 Your integration is now:**

**Receiving real-time webhooks:**
- Subscription created/renewed → Grant access
- Subscription expired/cancelled → Revoke access
- Updates happen within seconds

**Automating user management:**
- Users created automatically (if using automations)
- Roles assigned correctly
- Email notifications sent

**Protecting premium content:**
- Selected pages require subscription
- Non-subscribers redirected to checkout
- Subscribers enjoy seamless access

**Providing great user experience:**
- Fast, automatic activation
- Clear subscription status
- Secure and reliable

### 11.3 Going further

**Next steps to enhance your integration:**

**1. Add email marketing integration**
- Connect Easytools to Mailchimp/ConvertKit
- Add subscribers to email list
- Send welcome sequence

**2. Create members-only community**
- Add forum (bbPress, BuddyPress)
- Protect forum pages
- Build engaged community

**3. Add advanced analytics**
- Track subscription metrics
- Monitor churn rate
- Identify popular content

**4. Implement content dripping**
- Release content gradually
- Based on subscription date
- Keep subscribers engaged

**5. Add multiple tiers**
- Basic, Pro, Enterprise plans
- Different access levels
- Upgrade paths

**6. Custom webhook handlers**
- Hook into plugin events
- Add custom logic
- Integrate with other systems

### 11.4 Resources and support

**Plugin documentation:**
- README file in plugin folder
- Inline help in WordPress admin

**Easytools documentation:**
- https://easytools.app/docs
- Webhook events reference
- API documentation

**WordPress resources:**
- WordPress Codex: https://codex.wordpress.org
- WordPress REST API docs
- User role documentation

**Easytools support:**
- Contact via Easytools dashboard
- Ask about webhook issues
- Request features

**Community resources:**
- WordPress forums
- Easytools community
- Facebook groups

### 11.5 Troubleshooting checklist

Keep this handy for quick reference:

**Webhook not arriving?**
□ Check webhook URL in Easytools
□ Verify SSL certificate valid
□ Check firewall/security plugins
□ Test REST API accessible

**Signature verification failing?**
□ Check signing keys match exactly
□ No extra spaces in key
□ Regenerate key if needed
□ Temporarily enable Developer Mode to test

**User not updated?**
□ Check email addresses match
□ Enable auto-create users
□ Verify webhook processed successfully
□ Check user meta values

**Access not working?**
□ Clear all caches
□ Verify page is protected
□ Check user is logged in
□ Check user role correct

**Automation not running?**
□ Verify connection active
□ Check scenario enabled
□ Verify assigned to correct products
□ Check execution logs for errors

---

## Appendix

### A. Quick Reference: Webhook Events

| Event | When Triggered | Use For |
|-------|---------------|---------|
| `product_assigned` | Product assigned to user | **PRIMARY** - Grant access |
| `subscription_created` | New subscription purchased | Track new subscribers |
| `subscription_expired` | Subscription expired | **CRITICAL** - Revoke access |
| `subscription_cancelled` | User cancelled | Handle cancellations |
| `subscription_renewed` | Payment renewed subscription | Track renewals |
| `subscription_paused` | Subscription paused | Handle paused state |
| `subscription_plan_changed` | User changed plan | Track upgrades/downgrades |

**Minimal setup:** Enable `product_assigned` and `subscription_expired`

### B. Quick Reference: Plugin Settings

| Setting | Purpose | Recommended Value |
|---------|---------|------------------|
| **Webhook Signing Key** | Security - verify webhooks | From Easytools (sk_live_...) |
| **Checkout URL** | Where to redirect non-subscribers | Your Easytools store URL |
| **Developer Mode** | Disable signature check | ✗ OFF (only enable for testing) |
| **Auto-Create Users** | Create WP accounts automatically | ✓ ON |
| **Default User Role** | Role for new subscribers | Subscriber |
| **Send Welcome Email** | Email new users | Your preference |
| **Access Control** | Protect entire site or specific pages | Based on your needs |
| **Protected Pages** | Which pages require subscription | Select premium pages |

### C. Common JSON Payloads

**Product Assigned:**
```json
{
  "event": "product_assigned",
  "customer_email": "user@example.com",
  "customer_id": "cus_abc123",
  "product_name": "Premium Monthly",
  "subscription_type": "monthly",
  "is_subscription": true,
  "renewal_date": "2025-12-03T10:00:00Z"
}
```

**Subscription Expired:**
```json
{
  "event": "subscription_expired",
  "customer_email": "user@example.com",
  "customer_id": "cus_abc123",
  "product_name": "Premium Monthly",
  "expiration_date": "2025-01-03T10:00:00Z"
}
```

**One-time Purchase:**
```json
{
  "event": "product_assigned",
  "customer_email": "user@example.com",
  "customer_id": "cus_abc123",
  "product_name": "Lifetime Access",
  "subscription_one_time": true,
  "is_subscription": false
}
```

### D. WordPress REST API Endpoints

**Plugin endpoints:**

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/wp-json/easytools/v1/webhook` | POST | Receive webhooks from Easytools |
| `/wp-json/easytools/v1/status` | GET | Check plugin status |

**Testing endpoint:**
```bash
curl https://yoursite.com/wp-json/easytools/v1/status
```

### E. Glossary

**Application Password:** Secure password for external apps, not your login password

**HMAC:** Hash-based Message Authentication Code - security signature

**JSON:** JavaScript Object Notation - data format for webhooks

**Payload:** Data sent with webhook (JSON format)

**REST API:** WordPress interface for external communication

**Signature:** Cryptographic proof that webhook is authentic

**Subscriber:** WordPress user role with minimal permissions

**Webhook:** Automated HTTP notification sent when event occurs

**Webhook Signing Key:** Secret key used to generate/verify signatures

---

**Thank you for using Easytools with WordPress!**

Your subscription-based membership site is now fully operational. If you have questions or need support, refer to the troubleshooting section or contact Easytools support.

**Happy building! 🚀**