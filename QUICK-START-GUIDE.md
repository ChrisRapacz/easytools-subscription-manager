# Easytools Subscription Manager - Quick Start Guide

**Version 1.5.3** | One-page setup checklist

---

## ⚡ 5-Minute Setup Checklist

### □ Step 1: Install Plugin (1 min)
1. Go to **Plugins → Add New → Upload Plugin**
2. Upload `easytools-subscription-manager-v1.5.3.zip`
3. Click **Activate Plugin**

### □ Step 2: Configure Basic Settings (2 min)
1. Go to **Easytools Subscription** menu
2. Enter **Checkout URL** (your Easytools product link)
3. In Easytools: Generate **Webhook Signing Key**
4. In WordPress: Click eye icon, paste signing key
5. Copy the **Webhook URL** shown below the field
6. Click **Save Settings**

### □ Step 3: Add Webhook to Easytools (1 min)
1. In Easytools: **API & Webhooks → Add New Webhook**
2. Paste your WordPress webhook URL
3. Select events: ✅ subscription.active ✅ subscription.expired ✅ subscription.cancelled
4. Save webhook

### □ Step 4: Test Connection (30 sec)
1. Go to **Webhook Tester** tab
2. Enter test email
3. Click **Send Test Webhook**
4. Check **Logs** tab for success ✅

### □ Step 5: Protect Content (30 sec)
Choose one:
- **Option A:** Select specific pages to protect
- **Option B:** Check "Protect Entire Site" and select pages to exclude

Click **Save Settings**

---

## 🎨 Optional: Create Bouncer Page (2 min)

### Why?
Better user experience than direct redirect to checkout

### How:
1. Scroll to **Bouncer Page** section
2. Check **Enable custom bouncer page**
3. Choose colors (icon, button, background)
4. Enter **Product URL**
5. Click **Create New Bouncer Page from Template**
6. Wait for success message ✅
7. Click **Save Settings**

---

## 📧 Optional: Customize Emails (2 min)

### Basic:
1. Set **Email From Address** and **From Name**
2. Choose **Brand Color**

### Advanced:
1. Edit **Subject**, **Heading**, **Message**
2. Use placeholders: `{username}`, `{site_name}`, `{login_url}`
3. Send **Test Email** to preview
4. Click **Save Settings**

---

## 🎯 Testing Your Setup

### Test 1: Webhook ✅
- **Webhook Tester** tab → Send test webhook → Check **Logs**

### Test 2: User Creation ✅
- Make test purchase in Easytools
- Check **Subscribers** tab for new user
- Check email inbox for welcome email

### Test 3: Access Control ✅
- Open incognito window
- Try to access protected page
- Should redirect to bouncer page (or checkout)

### Test 4: Subscriber Access ✅
- Log in with subscriber credentials
- Access protected page
- Should load without redirect ✅

---

## 📊 Managing Subscribers

### View All Subscribers
**Easytools Subscription → Subscribers**

### Status Colors:
- 🟢 **Green** = Active subscription
- 🔴 **Red** = Expired/cancelled

### Manual Actions:
- Click **Activate** to grant access
- Click **Deactivate** to remove access

### Use Cases:
- Free trials
- Staff access
- Partner access
- Fixing issues

---

## 📝 Monitoring Activity

### View Logs
**Easytools Subscription → Logs**

### Log Status:
- ✅ **Success** = User created/updated
- ❌ **Error** = Check details for issue

### Export Data:
- **Export to CSV** = Spreadsheet format
- **Export to MD** = Documentation format
- Use date filter to export specific date range

---

## 🔧 Common Issues & Quick Fixes

### ❌ Email Not Received
**Fix:** Install WP Mail SMTP plugin, check spam folder, verify "Send Welcome Email" is checked

### ❌ User Not Created
**Fix:** Verify webhook signing key matches, check webhook URL is correct (HTTPS), refresh permalinks

### ❌ Protected Page Still Accessible
**Fix:** Verify pages are in Protected Pages list, clear cache, test in incognito mode

### ❌ Bouncer Page Looks Wrong
**Fix:** Delete and recreate bouncer page with current version, use Text/HTML editor not Visual

### ❌ Password Reset Link Invalid
**Fix:** Update to version 1.5.3+

### ❌ Subscription Type Empty
**Fix:** Update to version 1.5.3+

---

## 📖 Key Features Summary

| Feature | Location | What It Does |
|---------|----------|--------------|
| **Webhook Integration** | Settings → Basic Settings | Connects WordPress to Easytools |
| **Access Control** | Settings → Access Control | Protects premium content |
| **Bouncer Page** | Settings → Bouncer Page | Custom page for non-subscribers |
| **Email Customization** | Settings → Email Content | Branded welcome emails |
| **Subscriber Management** | Subscribers Tab | View and manage all users |
| **Webhook Tester** | Webhook Tester Tab | Test webhook connection |
| **Activity Logs** | Logs Tab | Monitor all webhook events |

---

## 🔗 Important URLs

**Webhook Endpoint:**
```
https://yoursite.com/wp-json/easytools/v1/webhook
```
*Add this to Easytools → API & Webhooks*

**Webhook Events to Enable:**
- `subscription.active`
- `subscription.expired`
- `subscription.cancelled`

---

## 📋 Available Placeholders

### Email Templates:
- `{username}` - User's login name
- `{site_name}` - Your WordPress site name
- `{login_url}` - WordPress login page URL

---

## ⚙️ System Requirements

- ✅ WordPress 5.0 or higher
- ✅ PHP 7.4 or higher
- ✅ SSL Certificate (HTTPS required)
- ✅ Active Easytools account
- ✅ Write permissions for WordPress

---

## 📞 Support & Resources

**Documentation:**
- 🇬🇧 English: https://www.easy.tools/docs/explore
- 🇵🇱 Polish: https://www.easy.tools/pl/docs/odkrywaj

**Support Email:** kontakt.rapacz@gmail.com

**Plugin Author:** Chris Rapacz
**LinkedIn:** https://linkedin.com/in/krzysztofrapacz/

**Easytools Platform:** https://easy.tools

---

## 🎓 Best Practices

### For Best Results:

**Email Delivery:**
- ✅ Use professional SMTP service (SendGrid, Mailgun, etc.)
- ✅ Set up SPF/DKIM records
- ✅ Test regularly with different email providers

**Access Control:**
- ✅ Use "Protect Entire Site" for membership sites
- ✅ Use "Protect Specific Pages" for mixed content sites
- ✅ Always leave legal pages unprotected (Terms, Privacy)

**Bouncer Page:**
- ✅ Match colors to your brand
- ✅ Keep messaging clear and concise
- ✅ Test on mobile devices

**Monitoring:**
- ✅ Check webhook logs weekly
- ✅ Export logs monthly for records
- ✅ Monitor for errors or patterns

**Testing Before Launch:**
1. ✅ Test webhook with test purchase
2. ✅ Verify email delivery
3. ✅ Test access control (logged out)
4. ✅ Test access control (logged in subscriber)
5. ✅ Test bouncer page on mobile
6. ✅ Test subscription status changes

---

## 🚀 Advanced Tips

### Tip 1: Multiple Products
Use the same webhook URL for all your Easytools products. The plugin handles all products automatically.

### Tip 2: Custom User Roles
In **User Registration** settings, you can set the default WordPress role for new subscribers.

### Tip 3: Manual Access Management
Use the **Subscribers** tab to manually grant or revoke access without requiring payment.

### Tip 4: Webhook Monitoring
Set up email alerts by exporting logs regularly and checking for error patterns.

### Tip 5: Backup Strategy
Export subscriber list and webhook logs monthly for your records.

---

## ✅ Post-Launch Checklist

After launching your membership site:

- [ ] Monitor first 10 purchases closely
- [ ] Verify all emails are being delivered
- [ ] Check webhook logs for any errors
- [ ] Test on different devices and browsers
- [ ] Set up regular backup schedule
- [ ] Create support documentation for customers
- [ ] Monitor subscriber list for anomalies
- [ ] Test subscription expiration flow
- [ ] Verify bouncer page displays correctly
- [ ] Check page load times with protection enabled

---

## 📈 Metrics to Track

### Weekly:
- New subscriptions (Subscribers tab)
- Failed webhooks (Logs tab - errors)
- Email delivery rate

### Monthly:
- Active vs. expired subscribers
- Subscription retention rate
- Popular subscription types
- Most accessed protected pages

---

## 🎯 Success Criteria

Your setup is complete when:

✅ Test webhook shows success in Logs
✅ Test purchase creates user account
✅ Welcome email is received
✅ Password reset link works
✅ Non-subscribers are redirected
✅ Subscribers can access protected content
✅ Bouncer page displays correctly
✅ Subscription status changes update correctly

---

**Plugin Version:** 1.5.3
**Guide Last Updated:** November 2025
**Created by:** Chris Rapacz

---

*Print this guide or save as PDF for quick reference!*
