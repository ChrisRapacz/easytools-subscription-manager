# Easytools Subscription Manager - Video Script

**Duration: 12-15 minutes**

---

## Opening (30 seconds)

**[SCREEN: WordPress dashboard]**

"Hi! Welcome to Easytools Subscription Manager - the easiest way to turn your WordPress site into a membership platform powered by Easytools.

In this video, I'll show you how to:
- Set up the plugin in under 5 minutes
- Protect your premium content
- Automatically create user accounts from purchases
- Send beautiful branded emails to your subscribers
- And create a custom bouncer page

Let's get started!"

---

## Part 1: Installation & Basic Setup (2 minutes)

**[SCREEN: Plugins page]**

"First, let's install the plugin."

**ACTION:**
- Go to Plugins → Add New → Upload Plugin
- Select `easytools-subscription-manager-v1.5.3.zip`
- Click Install Now
- Click Activate Plugin

"Now let's configure the basic settings."

**[SCREEN: Easytools Subscription settings page]**

"Click on Easytools Subscription in your sidebar. You'll see this beautiful settings interface."

**ACTION:**
- Show the teal-colored premium interface

"First, add your Checkout URL. This is the link to your Easytools product page."

**[SCREEN: Easytools dashboard]**
- Show where to find/create the product URL
- Copy it

**[SCREEN: WordPress settings]**
- Paste into Checkout URL field

---

## Part 2: Webhook Configuration (2 minutes)

**[SCREEN: WordPress settings - Webhook Signing Key field]**

"Next, we need to connect WordPress to Easytools using webhooks."

**[SCREEN: Easytools dashboard - API & Webhooks]**

"In your Easytools account, go to API & Webhooks and generate a Webhook Signing Key."

**ACTION:**
- Show generating the key
- Copy it

**[SCREEN: WordPress settings]**

"Back in WordPress, click the eye icon to reveal the signing key field, and paste your key."

**ACTION:**
- Click eye icon
- Paste key
- Show the webhook URL that appears below

"Now copy this webhook URL - we'll add it to Easytools."

**[SCREEN: Easytools - Add Webhook]**

"In Easytools, click Add New Webhook and paste your WordPress URL."

**ACTION:**
- Paste URL
- Select events: subscription.active, subscription.expired, subscription.cancelled
- Save

**[SCREEN: WordPress settings]**

"Click Save Settings in WordPress."

**ACTION:**
- Click Save Settings button

---

## Part 3: Testing the Connection (1 minute)

**[SCREEN: Webhook Tester tab]**

"Let's test if everything is connected properly. Go to the Webhook Tester tab."

**ACTION:**
- Click Webhook Tester tab
- Enter test email: demo@example.com
- Click Send Test Webhook
- Show success message

"Perfect! Now let's check the logs."

**[SCREEN: Logs tab]**

"Go to the Logs tab and you'll see our test webhook was received successfully."

**ACTION:**
- Click Logs tab
- Show the test webhook entry with green checkmark
- Click to expand details

---

## Part 4: Access Control (2 minutes)

**[SCREEN: Settings page - Access Control section]**

"Now let's protect your premium content. You have two options:"

**Option 1: Protect Specific Pages**

"If you have a mix of free and premium content, use this mode."

**ACTION:**
- Leave "Protect Entire Site" unchecked
- Select a few pages from the Protected Pages dropdown
- Hold Ctrl/Cmd to select multiple

**Option 2: Protect Everything (Except...)**

"If most of your content is premium, check 'Protect Entire Site'."

**ACTION:**
- Check "Protect Entire Site"
- Select pages to exclude (Home, About, Contact, etc.)

"I'll use the second option for this demo."

**ACTION:**
- Save Settings

"Now let's test it. I'll open an incognito window and try to access a protected page."

**[SCREEN: Incognito browser window]**

**ACTION:**
- Try to access protected page
- Show redirect to checkout

"Perfect! Non-subscribers can't access protected content."

---

## Part 5: Creating a Bouncer Page (3 minutes)

**[SCREEN: Settings - Bouncer Page section]**

"But redirecting directly to checkout isn't the best user experience. Let's create a beautiful bouncer page instead."

"First, enable the bouncer page."

**ACTION:**
- Check "Enable custom bouncer page for non-subscribers"

"Now let's customize it to match our brand."

**ACTION:**
- Enter Product URL
- Click Icon Color picker → Choose teal (#71efab)
- Click Button Color picker → Choose matching color
- Click Background Color picker → Choose dark blue (#172532)

"Watch how easy this is - just click 'Create New Bouncer Page from Template'."

**ACTION:**
- Click "Create New Bouncer Page from Template"
- Show spinning animation
- Show success message: "Bouncer page created successfully!"
- Page refreshes
- New page is automatically selected in dropdown

"That's it! Let's see how it looks."

**[SCREEN: Incognito window]**

**ACTION:**
- Refresh the protected page
- Show beautiful bouncer page with:
  - Lock icon in custom color
  - "This Content is for Subscribers Only" heading
  - Description text
  - "Start Your Free Trial" button in custom color
  - "Already a member? Log in here" link

"Beautiful! And it's fully responsive."

**ACTION:**
- Show on mobile view (browser dev tools)

---

## Part 6: Email Customization (2 minutes)

**[SCREEN: Settings - User Registration section]**

"When someone purchases a subscription, they automatically receive a welcome email with login credentials. Let's customize it."

**ACTION:**
- Scroll to User Registration section
- Point out "Send Welcome Email" is checked
- Enter custom From Address: support@yoursite.com
- Enter From Name: Your Company

**[SCREEN: Settings - Email Content Customization]**

"Now let's customize the email design."

**ACTION:**
- Click brand color picker → Choose color
- Show subject field with {placeholders}
- Edit heading: "🎉 Welcome to Our Community!"
- Customize message text
- Change button text if desired

"Let's see how it looks. I'll send a test email to myself."

**ACTION:**
- Scroll to Test Email section
- Enter your email
- Click Send Test Email
- Show success message

**[SCREEN: Email inbox]**

"Here's the email!"

**ACTION:**
- Open email
- Show beautiful HTML design
- Point out: branded colors, lock icon, "Set Your Password" button
- Show it's fully responsive

---

## Part 7: Managing Subscribers (1.5 minutes)

**[SCREEN: Subscribers tab]**

"Let's look at subscriber management. Click the Subscribers tab."

**ACTION:**
- Click Subscribers tab
- Show table with columns: Email, Username, Status, Subscription Type, Date, Actions

"You can see all your subscribers here. Green means active, red means expired."

**ACTION:**
- Point to status badges

"You can manually activate or deactivate access for any user."

**ACTION:**
- Click Activate button on expired user
- Show status changes to Active (green)
- Click Deactivate button
- Show status changes back to Expired (red)

"This is useful for:
- Giving free trial access
- Granting access to partners or staff
- Fixing subscription issues"

---

## Part 8: Real Purchase Demo (2 minutes)

**[SCREEN: Easytools product page]**

"Now let's do a real purchase to see everything in action."

**ACTION:**
- Make a test purchase in Easytools
- Use real email address you can check
- Complete checkout

**[SCREEN: WordPress - Logs tab]**

"Within seconds, WordPress receives the webhook."

**ACTION:**
- Refresh Logs tab
- Show new webhook entry: subscription.active
- Expand to show details
- Point out: Customer email, event type, success status

**[SCREEN: WordPress - Subscribers tab]**

"The user account is automatically created."

**ACTION:**
- Click Subscribers tab
- Show new subscriber with Active status
- Point out subscription type, date

**[SCREEN: Email inbox]**

"And the welcome email is sent!"

**ACTION:**
- Open welcome email
- Show beautiful branded email
- Click "Set Your Password" button

**[SCREEN: WordPress password reset page]**

"The customer can set their password."

**ACTION:**
- Enter new password
- Click Reset Password
- Show success message

**[SCREEN: Protected page - logged in]**

"And now they have full access to all premium content!"

**ACTION:**
- Navigate to previously protected page
- Show it loads without redirect
- "They're in!"

---

## Part 9: Monitoring & Logs (1 minute)

**[SCREEN: Logs tab]**

"You can monitor all webhook activity in the Logs tab."

**ACTION:**
- Show various log entries
- Click to expand one
- Show detailed JSON data

"You can export logs for analysis or record-keeping."

**ACTION:**
- Show date filter
- Click "Export to CSV"
- Show file downloads

---

## Closing (30 seconds)

**[SCREEN: Settings page with author footer visible]**

"And that's it! Your WordPress membership site is now fully integrated with Easytools."

**ACTION:**
- Scroll to show all sections quickly

"To recap:
- ✅ Automatic user account creation
- ✅ Beautiful branded emails
- ✅ Protected premium content
- ✅ Custom bouncer page
- ✅ Real-time webhook monitoring"

**[SCREEN: Author footer closeup]**

"This plugin was created by Chris Rapacz. You can find me on LinkedIn if you have questions."

**ACTION:**
- Show footer with LinkedIn link

"Link to the plugin and documentation in the description below."

"Thanks for watching, and happy selling!"

**[END SCREEN]**
- Plugin download link
- Documentation link
- Author LinkedIn
- Subscribe button

---

## B-Roll Suggestions

**During installation:**
- Fast-forward animation of progress bar

**During configuration:**
- Close-ups of color picker interactions
- Mouse cursor animations
- Success checkmarks appearing

**During testing:**
- Split screen: WordPress logs + email inbox updating in real-time
- Before/after comparisons of protected pages

**Background music:**
- Upbeat, professional
- Tech/corporate vibe
- Not too loud, let voice be clear

---

## On-Screen Text Overlays

**Key moments to add text:**
- "Step 1: Installation" (when uploading plugin)
- "Step 2: Basic Setup" (when entering checkout URL)
- "Step 3: Webhooks" (when copying webhook URL)
- "Step 4: Access Control" (when selecting pages)
- "Step 5: Bouncer Page" (when customizing colors)
- "Step 6: Emails" (when customizing email)
- "✅ User Created" (when showing new subscriber)
- "✅ Email Sent" (when showing inbox)
- "✅ Access Granted" (when viewing protected page)

---

## Common Questions to Address (Optional Extended Content)

If making a longer tutorial, add a Q&A section:

**Q: "What happens when subscription expires?"**
- Show webhook log for subscription.expired
- Show user status change to Expired
- Show redirect when trying to access content

**Q: "Can I use this with WooCommerce?"**
- "No, this is specifically for Easytools platform"

**Q: "How secure is this?"**
- Explain webhook signature verification
- Show signing key protection

**Q: "What if email doesn't send?"**
- Show test email feature
- Mention SMTP plugins
- Show debug logs

---

## Thumbnail Ideas

**Option 1:** Split screen
- Left: WordPress logo + Easytools logo
- Right: Checkmarks/success symbols
- Text: "Complete Integration Guide"

**Option 2:** Before/After
- Before: Plain WordPress site
- After: Premium membership site with bouncer page
- Text: "Turn WordPress into a Membership Site"

**Option 3:** Feature highlights
- Screenshots of: Email, Bouncer page, Subscriber list
- Text: "Easytools Subscription Manager"

**Text overlays on thumbnail:**
- "Step-by-Step Setup"
- "No Coding Required"
- "15 Minute Tutorial"

---

## Video Description Template

```
🚀 Turn your WordPress site into a membership platform with Easytools Subscription Manager!

In this tutorial, you'll learn how to:
✅ Install and configure the plugin
✅ Connect WordPress to Easytools via webhooks
✅ Automatically create user accounts from purchases
✅ Protect your premium content
✅ Create a beautiful custom bouncer page
✅ Send branded welcome emails
✅ Manage subscribers

⏱️ TIMESTAMPS:
0:00 - Introduction
0:30 - Installation
2:30 - Webhook Setup
4:30 - Testing Connection
5:30 - Access Control
7:30 - Creating Bouncer Page
10:30 - Email Customization
12:30 - Managing Subscribers
14:00 - Real Purchase Demo
16:00 - Monitoring Logs
17:00 - Conclusion

🔗 LINKS:
Plugin Download: [Your Link]
Documentation: https://www.easy.tools/docs/explore
Easytools Platform: https://easy.tools
Author LinkedIn: https://www.linkedin.com/in/krzysztofrapacz/

📧 SUPPORT:
Email: kontakt.rapacz@gmail.com

💡 REQUIREMENTS:
- WordPress 5.0+
- PHP 7.4+
- Easytools account
- SSL certificate (HTTPS)

#WordPress #Membership #Easytools #Tutorial #WebDevelopment
```

---

**Production Notes:**
- Record in 1080p minimum (1440p or 4K preferred)
- Use screen recording software with cursor highlight
- Record audio separately with good microphone
- Add subtle transitions between sections
- Use zoom-ins for important UI elements
- Add progress bar at bottom showing current step
- Include timestamps in video timeline

**Editing Checklist:**
- [ ] Remove long pauses and "umms"
- [ ] Speed up repetitive actions (2x)
- [ ] Add text overlays for key steps
- [ ] Add success sound effects for completions
- [ ] Background music at 20% volume
- [ ] Color grade for professional look
- [ ] Add end screen with links
- [ ] Export with subtitles/captions
