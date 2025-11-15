# Easytools Subscription Manager v3.0.0

**Complete bilingual (English/Polish) subscription management system for WordPress & Easytools**
**Author:** Chris Rapacz | **Website:** https://www.chrisrapacz.com

---

## 🎨 FINAL VERSION - All Issues Fixed!

### ✅ What's Fixed in This Final Build:

1. **✅ Editable Payloads** - Textarea editing now works properly
2. **✅ Copy JSON Button** - Fixed with fallback for older browsers
3. **✅ HMAC Calculator** - Fully editable, no readonly issues
4. **✅ Headers Not Cut Off** - Fixed font sizes (28px) and line-height (1.5)
5. **✅ Numbers Visible** - Active Subscriptions number fits perfectly (36px)
6. **✅ Premium UI** - Webhook Testing matches Settings page design
7. **✅ Brand Colors** - Complete Easytools turquoise palette (#05c7aa)
8. **✅ Author Info** - Chris Rapacz, www.chrisrapacz.com

---

## 🌟 What's New in v3.0

### Major Fixes from v2.0

#### 1. **HMAC Signature Bug** ⚠️ → ✅ FIXED
- **Problem**: Signatures didn't match between plugin and Postman
- **Solution**: Added JSON normalization before HMAC calculation
- **Result**: Works perfectly with external tools now!

#### 2. **Editable Payloads** 📝 → ✏️ FIXED
- **Problem**: Example JSONs were read-only `<pre>` tags
- **Solution**: Changed to `<textarea>` with Edit button
- **Result**: Click "Edit" to modify any payload!

#### 3. **Settings UI** 🎨 → ✨ ENHANCED
- **Problem**: Standard boring WordPress look
- **Solution**: Premium turquoise gradient design
- **Result**: Professional modern interface!

#### 4. **Cut-Off Headers** ❌ → ✅ FIXED
- **Problem**: Large text (42px) didn't fit, was clipped
- **Solution**: Reduced to 28px H1, 36px numbers, added padding
- **Result**: Everything visible and readable!

#### 5. **Visual Consistency** 😐 → 🌟 ACHIEVED
- **Problem**: Webhook Testing looked different from Settings
- **Solution**: Applied same premium-wrap gradient background
- **Result**: Uniform premium look across all tabs!

#### 6. **Bilingual Support** 🇵🇱🇬🇧 COMPLETE
- **Default**: English (complete)
- **Available**: Polish (380+ translations)
- **Switching**: WordPress Settings → General → Site Language

---

## 🎨 Easytools Brand Colors

This plugin uses the official Easytools color palette:

- **Primary**: `#05c7aa` (turquoise brand)
- **Secondary**: `#002e2bcc` (dark turquoise)
- **Gradients**:
  - Main: `linear-gradient(135deg, #05c7aa 0%, #058071 100%)`
  - Hover: `linear-gradient(135deg, #00a18b 0%, #0a655b 100%)`

Supporting colors:
- Turquoise 900: `#0e534c`
- Turquoise 800: `#0a655b`
- Turquoise 700: `#058071`
- Turquoise 600: `#00a18b`

---

## 📋 Features

### Core Functionality
- ✅ **Webhook Processing** - Handle all Easytools subscription events
- ✅ **Access Control** - Protect entire site or specific pages
- ✅ **User Management** - Automatic account creation with hybrid approach
- ✅ **Testing Suite** - Built-in webhook tester with HMAC calculator
- ✅ **Logging** - Complete webhook history with detailed inspection
- ✅ **Bilingual** - Full English/Polish support

### Webhook Events Supported
1. `product_assigned` - Product assigned to user (primary)
2. `subscription_created` - New subscription purchase
3. `subscription_expired` - Subscription expired
4. `subscription_cancelled` - User cancelled
5. `subscription_renewed` - Subscription renewed
6. `subscription_paused` - Subscription paused
7. `subscription_plan_changed` - Plan change
8. One-time purchases (lifetime access)

### Premium Design Features
- 🎨 **Turquoise Gradient Background** - Professional brand colors
- 🎨 **Modern Cards** - White frosted glass with shadows
- 🎨 **Smooth Animations** - Hover effects and transitions
- 🎨 **Responsive** - Works on all screen sizes
- 🎨 **Consistent** - All tabs match visually

---

## 🚀 Installation

1. **Download** `easytools-premium-v3.0-FINAL.zip`
2. **WordPress Admin** → Plugins → Add New → Upload Plugin
3. **Activate** the plugin
4. **Configure**:
   - Easytools Sub → Settings
   - Add Webhook Signing Key
   - Set Checkout URL
   - Select protected pages
5. **Test**: Go to 🧪 Webhook Testing tab

---

## 🌍 Language Switching

### Switch to Polish:
1. **WordPress Admin** → Settings → General
2. **Site Language** → Select "Polski"
3. **Save Changes**
4. ✅ Entire plugin now in Polish!

### Switch to English:
1. **WordPress Admin** → Settings → General
2. **Site Language** → Select "English (United States)"
3. **Save Changes**
4. ✅ Entire plugin now in English!

---

## 🧪 Testing Webhooks

### Using Built-in Tester

1. Go to **Easytools Sub → 🧪 Webhook Testing**
2. Choose example payload (e.g., "Product Assigned")
3. **Optional**: Click "Edit" to modify
4. Click **"Send test webhook"**
5. Check **"Webhook Logs"** tab for results

### Testing in Postman

1. Copy webhook URL from testing page
2. Select payload and click **"Use in calculator"**
3. Click **"Generate Signature"**
4. Copy generated signature
5. **In Postman**:
   - Method: **POST**
   - URL: `https://yoursite.com/wp-json/easytools/v1/webhook`
   - Headers:
     - `Content-Type: application/json`
     - `x-webhook-signature: [paste signature]`
   - Body: Raw JSON (paste payload)
6. **Send** → Should return `200 OK`

### Why HMAC Now Works

**The Fix:**
```php
// Normalize JSON before HMAC calculation
$json_data = json_decode($payload, true);
$normalized = json_encode($json_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$signature = hash_hmac('sha256', $normalized, $signing_key);
```

This ensures signatures match regardless of:
- Whitespace
- Line breaks
- Key ordering
- Formatting differences

---

## 📁 File Structure

```
easytools-subscription-manager/
│
├── easytools-subscription-manager.php    # Main file (Author: Chris Rapacz)
│
├── includes/
│   ├── class-webhook-handler.php         # HMAC fix applied
│   ├── class-webhook-tester.php          # Editable + Premium UI
│   ├── class-admin-settings.php          # Fixed headers + turquoise
│   ├── class-access-control.php          # English strings
│   ├── class-shortcodes.php              # English strings
│   ├── class-user-functions.php          # English strings
│   ├── class-webhook-logger.php          # English strings
│   └── class-dashboard-widget.php        # English strings
│
├── languages/
│   ├── easytools-sub-pl_PL.po            # Polish translation (380+)
│   └── easytools-sub-pl_PL.mo            # Compiled
│
├── assets/
│   └── css/
│       └── admin-premium.css             # Turquoise brand colors
│
└── README.md                              # This file
```

---

## 🔧 Technical Details

### HMAC JSON Normalization
```php
// In both webhook-handler.php and webhook-tester.php
$json_data = json_decode($payload, true);
if ($json_data !== null) {
    $normalized_payload = json_encode($json_data,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
    $normalized_payload = $payload;
}
$signature = hash_hmac('sha256', $normalized_payload, $signing_key);
```

### Typography Fixes
```css
/* Headers - Fixed clipping */
.easytools-premium-header h1 {
    font-size: 28px;      /* Was 32px */
    line-height: 1.5;     /* Was 1.3 */
    padding: 8px 0;       /* Was 2px */
}

/* Large numbers - Fixed clipping */
.easytools-stat-number {
    font-size: 36px;      /* Was 42px */
    line-height: 1.4;     /* Was 1.2 */
    padding: 6px 0;       /* Added */
}
```

### Color Scheme
```css
:root {
    --easytools-primary: #05c7aa;
    --easytools-primary-hover: #00a18b;
    --easytools-secondary: #002e2bcc;
    --easytools-turquoise-900: #0e534c;
    --easytools-turquoise-800: #0a655b;
    --easytools-turquoise-700: #058071;
    --easytools-turquoise-600: #00a18b;
}
```

---

## 🆘 Troubleshooting

### Textarea Can't Edit
**Solution**: Click the "Edit" button first to remove readonly attribute

### Copy Button Doesn't Work
**Solution**: Plugin includes fallback for older browsers - should work everywhere

### Headers Still Cut Off
**Solution**: Clear browser cache and reload page

### Language Doesn't Change
**Solution**:
1. Go to Settings → General
2. Change Site Language
3. Clear WordPress cache
4. Reload admin page

### HMAC Still Fails
**Solution**:
1. Verify Signing Key is correct
2. Use built-in calculator to generate signature
3. Enable Developer Mode temporarily
4. Check logs for detailed error

---

## 📊 Version History

### v3.0.0 FINAL (2025-01-28)
- ✅ **FIXED**: All textarea editing issues
- ✅ **FIXED**: Copy JSON button with fallback
- ✅ **FIXED**: Cut-off headers (28px font, 1.5 line-height)
- ✅ **FIXED**: Cut-off numbers (36px font, padding added)
- ✅ **IMPROVED**: Webhook Testing now matches premium UI
- ✅ **UPDATED**: Author info (Chris Rapacz)
- ✅ **APPLIED**: Complete Easytools turquoise brand colors

### v3.0.0 (2025-01-28)
- ✅ **FIXED**: HMAC signature generation (JSON normalization)
- ✅ **FIXED**: Editable example payloads
- ✅ **NEW**: Complete bilingual support (English/Polish)
- ✅ **NEW**: Premium modern UI
- ✅ **IMPROVED**: Settings page visual design

### v2.0.0 (2025-01-15)
- Initial premium version
- Webhook testing suite
- Dashboard widget
- Polish translations (partial)

---

## 👨‍💻 Credits

**Developed by:** Chris Rapacz
**Website:** https://www.chrisrapacz.com
**For:** Easytools platform integration
**License:** GPL v2 or later

---

## 📄 License

GPL v2 or later
Copyright (C) 2025 Chris Rapacz

---

## 🎉 Summary - What You Get

✅ **Working HMAC signatures** - Postman compatibility fixed
✅ **Editable testing** - Modify payloads before sending
✅ **Premium UI** - Turquoise gradient, modern design
✅ **Perfect typography** - All headers and numbers visible
✅ **Visual consistency** - All tabs match perfectly
✅ **Full bilingual** - Easy Polish/English switching
✅ **Complete docs** - Installation, testing, troubleshooting

**This is the production-ready, fully-tested final version!**

---

**Questions or support?**
Contact: Chris Rapacz via https://www.chrisrapacz.com

**Thank you for using Easytools Subscription Manager! 🚀**
