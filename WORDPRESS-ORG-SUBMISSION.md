# WordPress.org Plugin Submission Guide

Complete step-by-step guide to submitting Easytools Subscription Manager to the WordPress.org plugin repository.

## Table of Contents
1. [Pre-Submission Checklist](#pre-submission-checklist)
2. [Required Files](#required-files)
3. [Preparation Steps](#preparation-steps)
4. [Submission Process](#submission-process)
5. [After Submission](#after-submission)
6. [SVN Management](#svn-management)
7. [Troubleshooting](#troubleshooting)

---

## Pre-Submission Checklist

### ✅ Required Items

- [x] Plugin is GPL v2 or later compatible
- [x] Plugin has unique functionality (subscription management for Easytools)
- [x] Code follows WordPress Coding Standards
- [x] All security issues fixed (SQL injection, nonce verification, etc.)
- [x] readme.txt file created and properly formatted
- [ ] Plugin tested on latest WordPress version
- [ ] Plugin tested with PHP 7.4 and 8.0+
- [ ] No external dependencies (all code is included)
- [ ] No obfuscated code or encoded files
- [ ] Plugin name doesn't conflict with existing plugins
- [ ] You have a WordPress.org account

### ✅ Assets Needed

- [ ] Plugin icon (128x128 and 256x256 PNG, or SVG)
- [ ] Plugin banner (772x250 PNG, optional: 1544x500 for retina)
- [ ] Screenshots (recommended: 8 screenshots showing features)
- [ ] All images optimized and under reasonable file size

### ✅ Documentation

- [x] readme.txt with proper format
- [x] Inline code documentation
- [x] Installation instructions
- [x] FAQ section
- [x] Changelog

---

## Required Files

### Core Files (Already Created)

```
easytools-subscription-manager/
├── readme.txt                              ✅ Created
├── easytools-subscription-manager.php      ✅ Main file
├── includes/                               ✅ All class files
├── assets/css/                             ✅ Stylesheets
├── languages/                              ✅ Translation files
└── .wordpress-org/                         ✅ Directory created
    └── (icons and banners go here)         ⚠️  Need to add
```

### Files to Create

1. **Plugin Icons** (place in `.wordpress-org/`)
   - `icon-128x128.png` or `icon.svg`
   - `icon-256x256.png` (if using PNG)

2. **Plugin Banners** (place in `.wordpress-org/`)
   - `banner-772x250.png` (required)
   - `banner-1544x500.png` (optional, recommended)

3. **Screenshots** (place in plugin root)
   - `screenshot-1.png` - Main settings page
   - `screenshot-2.png` - Access control
   - `screenshot-3.png` - Bouncer page
   - `screenshot-4.png` - Email customization
   - `screenshot-5.png` - Subscriber management
   - `screenshot-6.png` - Webhook logs
   - `screenshot-7.png` - Webhook tester
   - `screenshot-8.png` - Dashboard widget

---

## Preparation Steps

### Step 1: Create WordPress.org Account

1. Visit [wordpress.org/support/register.php](https://wordpress.org/support/register.php)
2. Create account with your email
3. Verify email address
4. Keep credentials handy

### Step 2: Create Plugin Assets

**Using Canva (Free & Easy):**
1. Go to [canva.com](https://www.canva.com)
2. Create custom size designs:
   - Icon: 256x256 pixels
   - Banner: 1544x500 pixels
3. Use plugin colors: #71efab (green), #172532 (dark blue)
4. Export as PNG
5. Use online tools to resize icon to 128x128 if needed

**Design Tips:**
- Keep it simple and recognizable
- Use consistent branding
- Make text readable at all sizes
- Test on both light and dark backgrounds

### Step 3: Take Screenshots

**Recommended Approach:**
1. Install plugin on a test WordPress site
2. Configure all features
3. Use browser at 1200px+ width
4. Take clean screenshots showing:
   - Settings page (full view)
   - Each major feature
   - User-facing elements (bouncer page, emails)
5. Crop to remove browser chrome
6. Save as PNG

**Screenshot Tools:**
- macOS: Cmd+Shift+4
- Windows: Windows+Shift+S
- Browser extensions: Awesome Screenshot, Nimbus

### Step 4: Validate readme.txt

1. Visit [wordpress.org/plugins/developers/readme-validator/](https://wordpress.org/plugins/developers/readme-validator/)
2. Copy contents of your `readme.txt`
3. Paste and click "Validate"
4. Fix any errors or warnings
5. Ensure "Tested up to" is latest WP version

### Step 5: Test Plugin Thoroughly

**Testing Checklist:**
- [ ] Fresh WordPress installation (latest version)
- [ ] Activate plugin - no errors
- [ ] Configure webhook settings
- [ ] Test webhook reception
- [ ] Create test subscription
- [ ] Verify access control works
- [ ] Check welcome email sending
- [ ] Test all admin pages load correctly
- [ ] Check for JavaScript console errors
- [ ] Test with PHP 7.4 and PHP 8.x
- [ ] Deactivate - no errors
- [ ] Reactivate - settings preserved

### Step 6: Create ZIP Package

```bash
cd /home/user/easytools-subscription-manager
zip -r easytools-subscription-manager.zip easytools-subscription-manager/ \
  -x "*.git*" -x "*node_modules*" -x "*.DS_Store*" \
  -x "*create-zip*" -x "*.wordpress-org/*"
```

**Package should include:**
- Main PHP file
- includes/ directory
- assets/ directory
- languages/ directory
- readme.txt

**Package should NOT include:**
- .git files
- Development files
- .wordpress-org assets (these go separately in SVN)
- Build tools or source files

---

## Submission Process

### Step 1: Submit Plugin for Review

1. Go to [wordpress.org/plugins/developers/add/](https://wordpress.org/plugins/developers/add/)
2. Log in to your WordPress.org account
3. Fill out the form:
   - **Plugin Name:** Easytools Subscription Manager
   - **Plugin URL:** https://easy.tools (or your plugin website)
   - **Description:** Brief description of what the plugin does
   - **Upload ZIP:** Your plugin ZIP file
4. Check the box confirming GPL compatibility
5. Click "Upload"

### Step 2: Wait for Review

**What Happens Next:**
- You'll receive email confirmation of submission
- WordPress.org team manually reviews your plugin
- Review time: 2-14 days (average 3-7 days)
- You may receive questions or requests for changes

**Common Review Comments:**
- Security issues (SQL injection, XSS, nonce verification)
- License conflicts
- Trademark issues
- Code quality concerns
- Functionality concerns

### Step 3: Respond to Review

If reviewers request changes:
1. Make requested modifications
2. Test thoroughly
3. Reply to the review ticket
4. Include clear explanation of changes made
5. Be professional and responsive

---

## After Approval

### Step 1: You'll Receive SVN Access

Email will contain:
- SVN repository URL: `https://plugins.svn.wordpress.org/easytools-subscription-manager/`
- Instructions for committing
- Your WordPress.org credentials work for SVN

### Step 2: Install SVN Client

**macOS/Linux:**
```bash
# Already included in most systems
svn --version
```

**Windows:**
- Download TortoiseSVN: [tortoisesvn.net](https://tortoisesvn.net/)
- Or use command line SVN

### Step 3: Initial SVN Checkout

```bash
# Checkout the repository
svn co https://plugins.svn.wordpress.org/easytools-subscription-manager/ svn-easytools

cd svn-easytools
```

**Repository Structure:**
```
svn-easytools/
├── assets/          # Plugin icons, banners, screenshots
├── branches/        # Development branches (optional)
├── tags/            # Released versions (1.5.5, 1.5.6, etc.)
└── trunk/           # Current development version
```

### Step 4: Add Plugin Files to Trunk

```bash
# Copy your plugin files to trunk
cp -r /path/to/easytools-subscription-manager/* trunk/

# Remove development files
rm -rf trunk/.git
rm trunk/create-zip*

# Add files to SVN
cd trunk
svn add --force *
svn commit -m "Initial commit of Easytools Subscription Manager v1.5.5"
```

### Step 5: Add Assets

```bash
# Add icons and banners to assets directory
cd ../assets
cp /path/to/.wordpress-org/icon-*.png .
cp /path/to/.wordpress-org/banner-*.png .

# Add screenshots
cp /path/to/screenshots/screenshot-*.png .

# Commit assets
svn add *.png
svn commit -m "Add plugin assets (icons, banners, screenshots)"
```

### Step 6: Create First Release Tag

```bash
# Tag the release
cd ..
svn cp trunk tags/1.5.5
svn commit -m "Tagging version 1.5.5"
```

**Important:** The tag number (1.5.5) must match the version in:
- readme.txt (Stable tag)
- Main plugin file header (Version)

### Step 7: Plugin Goes Live! 🎉

- Plugin appears on WordPress.org within a few hours
- URL: `https://wordpress.org/plugins/easytools-subscription-manager/`
- Users can now install from WordPress admin

---

## SVN Management

### Updating Plugin (Future Releases)

```bash
# Update your local copy
cd svn-easytools
svn up

# Make changes to trunk
cd trunk
# ... update files ...

# Commit changes
svn commit -m "Update feature X in version 1.5.6"

# Tag new version
cd ..
svn cp trunk tags/1.5.6
svn commit -m "Tagging version 1.5.6"
```

### Updating readme.txt Only

```bash
cd svn-easytools/trunk
# Edit readme.txt
svn commit readme.txt -m "Update readme.txt"
```

### Updating Assets Only

```bash
cd svn-easytools/assets
# Replace/add new images
svn commit -m "Update plugin banner"
```

### Common SVN Commands

```bash
# Check status
svn status

# Update from repository
svn up

# Add new files
svn add filename.php

# Delete files
svn delete filename.php

# View differences
svn diff filename.php

# Revert changes
svn revert filename.php

# View commit log
svn log
```

---

## WordPress.org Plugin Guidelines

### Must Follow:

1. **GPL Compatible License**
   - Your plugin ✅ (GPL v2 or later)

2. **No Obfuscated Code**
   - All code must be readable
   - No base64 encoding
   - No minified PHP

3. **No External Calls Without User Permission**
   - Webhooks from Easytools are fine (user configures)
   - Don't call home without consent

4. **No Affiliate Links in Plugin**
   - Can be in readme.txt
   - Not in plugin UI

5. **Unique Functionality**
   - Your plugin ✅ (Easytools integration)

6. **Security Best Practices**
   - Nonce verification ✅ (after fixes)
   - SQL injection prevention ✅ (after fixes)
   - XSS prevention ✅

7. **No Trademark Violations**
   - "Easytools" is your platform ✅
   - No use of "WordPress" in plugin name

### Best Practices:

- Prefix all functions and classes (`easytools_`)
- Use WordPress coding standards
- Sanitize input, escape output
- Use prepared statements
- Check user capabilities
- Provide uninstall.php (optional)
- Document code well

---

## Troubleshooting

### Common Submission Issues

**Issue: "Your plugin name is too similar"**
- Solution: Change plugin slug/name slightly
- Current: easytools-subscription-manager ✅ (likely unique)

**Issue: "Security concerns found"**
- Solution: Fix all security issues identified in review
- We've already identified and will fix: SQL injection, nonce verification

**Issue: "Plugin doesn't work"**
- Solution: Test on fresh WordPress install
- Provide clear setup instructions

**Issue: "readme.txt format incorrect"**
- Solution: Use WordPress readme validator
- Follow exact format requirements

### SVN Issues

**Issue: "Authentication failed"**
- Use WordPress.org username and password
- Not API key, not email

**Issue: "Conflict on commit"**
- Run `svn up` before committing
- Resolve conflicts manually
- Then commit again

**Issue: "File is out of date"**
- Someone else committed (rare for new plugin)
- Run `svn up`
- Merge changes
- Commit again

---

## Support After Launch

### Plugin Support Forum

- WordPress.org creates forum automatically
- URL: `https://wordpress.org/support/plugin/easytools-subscription-manager/`
- Monitor and respond to user questions
- Good support = better ratings

### Reviews

- Users can leave 1-5 star reviews
- Respond professionally to negative reviews
- Address legitimate concerns
- Don't argue with users

### Stats

- View stats: `https://wordpress.org/plugins/easytools-subscription-manager/advanced/`
- See download numbers
- Track version adoption
- View active installations (delayed)

---

## Timeline Summary

| Step | Time | What Happens |
|------|------|-------------|
| Preparation | 1-3 days | Create assets, test, prepare files |
| Submission | 5 minutes | Upload to WordPress.org |
| Review | 3-7 days | WordPress.org team reviews |
| Fixes (if needed) | 1-2 days | Address review comments |
| Approval | Instant | Receive SVN access |
| SVN Upload | 30 minutes | Commit files to SVN |
| Goes Live | 2-4 hours | Plugin appears on WordPress.org |

**Total: ~1-2 weeks from start to live**

---

## Quick Reference

### Key URLs

- Submit plugin: [wordpress.org/plugins/developers/add/](https://wordpress.org/plugins/developers/add/)
- Readme validator: [wordpress.org/plugins/developers/readme-validator/](https://wordpress.org/plugins/developers/readme-validator/)
- Developer handbook: [developer.wordpress.org/plugins/](https://developer.wordpress.org/plugins/)
- SVN primer: [developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)

### SVN Repository
```
https://plugins.svn.wordpress.org/easytools-subscription-manager/
```

### Your Plugin Page (After Approval)
```
https://wordpress.org/plugins/easytools-subscription-manager/
```

---

## Need Help?

**WordPress.org Resources:**
- Make WordPress Slack: [make.wordpress.org/chat/](https://make.wordpress.org/chat/)
- Plugin Review Team: [make.wordpress.org/plugins/](https://make.wordpress.org/plugins/)
- Developer Handbook: [developer.wordpress.org/plugins/](https://developer.wordpress.org/plugins/)

**Questions?**
- Contact: kontakt.rapacz@gmail.com

---

**Good luck with your submission! 🚀**

Remember: Be patient with the review process, respond professionally to feedback, and maintain your plugin well after launch.
