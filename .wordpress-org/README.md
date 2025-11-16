# WordPress.org Assets

This directory contains assets for the WordPress.org plugin repository.

## Required Assets

### Plugin Icons
- `icon-128x128.png` - Small icon (128x128 pixels)
- `icon-256x256.png` - Large icon (256x256 pixels)
- OR `icon.svg` - Vector icon (recommended for retina displays)

### Plugin Banners
- `banner-772x250.png` - Low resolution banner (required)
- `banner-1544x500.png` - High resolution banner (optional, for retina displays)

### Screenshots
Place screenshots in the main plugin directory as:
- `screenshot-1.png` - Main settings page
- `screenshot-2.png` - Access control settings
- `screenshot-3.png` - Bouncer page customization
- `screenshot-4.png` - Email editor
- `screenshot-5.png` - Subscriber management
- `screenshot-6.png` - Webhook logs
- `screenshot-7.png` - Webhook tester
- `screenshot-8.png` - Dashboard widget

## Image Specifications

### Icons
- Format: PNG or SVG
- Size: 128x128 and 256x256 pixels (or vector SVG)
- Use simple, recognizable design
- Should look good at small sizes
- Recommended: Use brand colors (#71efab, #172532)

### Banners
- Format: PNG or JPG
- Size: 772x250 (standard) and 1544x500 (retina)
- Should include plugin name and tagline
- Use eye-catching design
- Keep text readable at all sizes

### Screenshots
- Format: PNG or JPG
- Recommended width: 1200-2000 pixels
- Keep UI elements clear and readable
- Show actual plugin functionality
- Update when UI changes

## How to Create Assets

### Using Design Tools
1. **Canva** - Free, easy-to-use templates
2. **Figma** - Professional design tool
3. **Adobe Photoshop** - Advanced editing
4. **GIMP** - Free alternative to Photoshop

### Tips
- Use consistent branding (colors from plugin: #71efab, #172532)
- Keep design clean and professional
- Test visibility at different sizes
- Export at correct dimensions
- Use PNG for transparency, JPG for photos

## Updating Assets

After creating assets:
1. Place icons and banners in `.wordpress-org/` directory
2. Place screenshots in main plugin directory
3. Commit to SVN assets directory
4. Assets appear on WordPress.org within a few hours

## Current Status

⚠️ **Assets needed** - Please create and add:
- [ ] icon-128x128.png or icon.svg
- [ ] icon-256x256.png (if using PNG)
- [ ] banner-772x250.png
- [ ] banner-1544x500.png (optional)
- [ ] 8 screenshots showing plugin features

Once you have created these assets, add them to this directory and they will be included in the WordPress.org submission.
