# Campus Ambassador Manager

A comprehensive WordPress plugin for managing campus ambassador programs with dynamic graphic generation, customizable campaigns, and user submissions.

## Overview

Campus Ambassador Manager is a powerful WordPress plugin designed to streamline the management of campus ambassador programs. It allows users to submit their information and photos to become campus ambassadors, while administrators can manage campaigns, generate custom graphics with dynamic frames, and track ambassador activities.

## Features

### Frontend Features
- **Ambassador Submission Form**: Easy-to-use form for users to apply as campus ambassadors
- **Photo Upload**: Users can upload their photos for ambassador profiles
- **Public Gallery**: Display approved ambassadors with their custom graphics
- **Responsive Design**: Mobile-friendly interface for all users
- **Shortcode Support**: Easy integration into any WordPress page or post

### Backend Features
- **Admin Dashboard**: Comprehensive overview of all ambassador submissions
- **Campaign Management**: Create and manage multiple ambassador campaigns
- **Approval Workflow**: Review and approve/reject ambassador applications
- **Custom Frame Designer**: Upload and manage custom graphic frames
- **Multi-Frame Support**: Support for multiple frame templates per campaign
- **Bulk Actions**: Process multiple submissions efficiently
- **Export Functionality**: Export ambassador data for reporting

### Graphics Generation
- **Dynamic Image Processing**: Automatically generate graphics with user photos
- **Custom Frame Overlays**: Apply branded frames to ambassador photos
- **Multiple Frame Templates**: Support for various frame designs
- **Automatic Resizing**: Smart image resizing and positioning
- **High-Quality Output**: Optimized graphics for web and social media

### API & Integration
- **REST API Endpoints**: Programmatic access to plugin features
- **AJAX Functionality**: Seamless user experience without page reloads
- **WordPress Integration**: Full compatibility with WordPress core features
- **Security**: Nonce verification and capability checks

## Installation

### Method 1: WordPress Admin Panel
1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to **Plugins > Add New**
4. Click **Upload Plugin** button
5. Choose the downloaded ZIP file
6. Click **Install Now**
7. After installation, click **Activate Plugin**

### Method 2: Manual Installation
1. Download and extract the plugin ZIP file
2. Upload the `campus-ambassador-manager` folder to `/wp-content/plugins/`
3. Log in to WordPress admin panel
4. Navigate to **Plugins**
5. Find "Campus Ambassador Manager" and click **Activate**

### Method 3: Git Clone (For Developers)
```bash
cd /path/to/wordpress/wp-content/plugins/
git clone https://github.com/MahirEO/campus-ambassador-manager.git
```
Then activate the plugin from WordPress admin panel.

## Usage

### Setting Up Your First Campaign

1. **Create a Campaign**
   - Go to **Campus Ambassadors > Campaigns**
   - Click **Add New Campaign**
   - Enter campaign name, description, and settings
   - Upload custom frame templates (optional)
   - Save the campaign

2. **Add Submission Form to a Page**
   - Create or edit a WordPress page
   - Add the shortcode: `[ambassador_form]`
   - Publish the page
   - Users can now submit applications through this page

3. **Display Ambassador Gallery**
   - Create or edit a WordPress page
   - Add the shortcode: `[ambassador_gallery]`
   - Publish the page
   - Approved ambassadors will appear in the gallery

### Managing Submissions

1. **View Submissions**
   - Navigate to **Campus Ambassadors > All Submissions**
   - View all pending and approved submissions
   - Use filters to sort by status, campaign, or date

2. **Approve/Reject Submissions**
   - Click on a submission to view details
   - Review the submitted information and photo
   - Click **Approve** to generate graphics and publish
   - Click **Reject** to decline the application

3. **Generate Graphics**
   - Graphics are automatically generated upon approval
   - Custom frames are applied to user photos
   - Generated graphics are saved in the media library
   - Users can download their custom graphics

### Managing Frame Templates

1. **Upload Frame Templates**
   - Go to **Campus Ambassadors > Frame Templates**
   - Click **Add New Frame**
   - Upload PNG image with transparent center area
   - Set frame name and dimensions
   - Assign to campaigns

2. **Frame Design Guidelines**
   - Use PNG format with transparency
   - Recommended size: 1080x1080 pixels
   - Center area should be transparent for photo overlay
   - Frame design should be in outer border area
   - Test with sample photos before deploying

## Architecture

### File Structure

```
campus-ambassador-manager/
├── admin/                          # Backend administration files
│   ├── class-admin.php            # Main admin class
│   ├── class-campaign-manager.php # Campaign management
│   ├── class-submission-list.php  # Submission list table
│   └── views/                     # Admin view templates
│       ├── campaigns.php
│       ├── submissions.php
│       └── settings.php
├── assets/                         # Static assets
│   ├── css/                       # Stylesheets
│   │   ├── admin.css
│   │   └── public.css
│   ├── js/                        # JavaScript files
│   │   ├── admin.js
│   │   └── public.js
│   └── images/                    # Plugin images
├── includes/                       # Core plugin files
│   ├── class-activator.php        # Plugin activation
│   ├── class-deactivator.php      # Plugin deactivation
│   ├── class-loader.php           # Hook loader
│   ├── class-database.php         # Database operations
│   └── class-graphics-generator.php # Image processing
├── public/                         # Frontend files
│   ├── class-public.php           # Public-facing functionality
│   ├── class-form-handler.php     # Form processing
│   └── class-gallery.php          # Gallery display
├── templates/                      # HTML templates
│   ├── form-template.php          # Submission form
│   ├── gallery-template.php       # Gallery display
│   └── single-ambassador.php      # Single ambassador view
├── campus-ambassador-manager.php   # Main plugin file
└── README.md                       # Documentation
```

### Database Schema

The plugin creates the following custom tables:

**wp_campus_ambassadors**
- `id` - Primary key
- `user_id` - WordPress user ID (if logged in)
- `name` - Ambassador name
- `email` - Email address
- `phone` - Phone number
- `university` - University/institution name
- `photo_url` - Original uploaded photo
- `generated_graphic_url` - Generated graphic with frame
- `campaign_id` - Associated campaign
- `status` - pending/approved/rejected
- `submitted_date` - Submission timestamp
- `approved_date` - Approval timestamp

**wp_ambassador_campaigns**
- `id` - Primary key
- `name` - Campaign name
- `description` - Campaign description
- `frame_template_id` - Associated frame template
- `start_date` - Campaign start date
- `end_date` - Campaign end date
- `status` - active/inactive
- `settings` - JSON encoded campaign settings

**wp_ambassador_frames**
- `id` - Primary key
- `name` - Frame template name
- `image_url` - Frame image URL
- `dimensions` - Frame dimensions (JSON)
- `created_date` - Creation timestamp

### Shortcodes

**[ambassador_form]**
- Displays the ambassador submission form
- Parameters:
  - `campaign="campaign-id"` - Specific campaign ID
  - `title="Custom Title"` - Form title
  - `button_text="Submit"` - Submit button text

Example:
```
[ambassador_form campaign="1" title="Join Our Team" button_text="Apply Now"]
```

**[ambassador_gallery]**
- Displays approved ambassadors gallery
- Parameters:
  - `campaign="campaign-id"` - Filter by campaign
  - `limit="12"` - Number of ambassadors to display
  - `columns="3"` - Number of columns
  - `orderby="date"` - Sort order (date, name, random)

Example:
```
[ambassador_gallery campaign="1" limit="20" columns="4" orderby="date"]
```

### Hooks & Filters

**Actions**
- `cam_before_submission` - Before submission is saved
- `cam_after_submission` - After submission is saved
- `cam_before_approval` - Before submission is approved
- `cam_after_approval` - After submission is approved
- `cam_graphic_generated` - After graphic is generated

**Filters**
- `cam_form_fields` - Customize form fields
- `cam_submission_data` - Modify submission data before saving
- `cam_graphic_settings` - Customize graphic generation settings
- `cam_gallery_query` - Modify gallery query

## Customization

### Custom CSS Styling

Add custom styles in your theme's CSS file:

```css
/* Customize form appearance */
.ambassador-form {
    max-width: 600px;
    margin: 0 auto;
}

.ambassador-form input,
.ambassador-form textarea {
    border: 2px solid #your-brand-color;
}

/* Customize gallery layout */
.ambassador-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.ambassador-card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
```

### Custom Form Fields

Add custom fields using the filter:

```php
add_filter('cam_form_fields', 'add_custom_fields');
function add_custom_fields($fields) {
    $fields['social_media'] = array(
        'type' => 'text',
        'label' => 'Instagram Handle',
        'required' => false
    );
    return $fields;
}
```

### Custom Graphic Processing

Modify graphic generation settings:

```php
add_filter('cam_graphic_settings', 'custom_graphic_settings');
function custom_graphic_settings($settings) {
    $settings['quality'] = 100;
    $settings['width'] = 1200;
    $settings['height'] = 1200;
    return $settings;
}
```

### Email Notifications

Customize email notifications:

```php
add_action('cam_after_approval', 'send_custom_approval_email', 10, 2);
function send_custom_approval_email($submission_id, $submission_data) {
    // Your custom email logic
    $to = $submission_data['email'];
    $subject = 'Welcome to our Ambassador Program!';
    $message = 'Congratulations! You have been approved...';
    wp_mail($to, $subject, $message);
}
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- GD Library or ImageMagick for graphics generation
- MySQL 5.6 or higher / MariaDB 10.0 or higher
- Recommended: SSL certificate for secure photo uploads

## Security Features

- Nonce verification on all forms
- Capability checks for admin functions
- Sanitization of all user inputs
- SQL injection prevention with prepared statements
- XSS protection with proper output escaping
- File upload validation and sanitization
- CSRF protection on AJAX requests

## Troubleshooting

### Graphics Not Generating
1. Check PHP GD Library is installed: `php -m | grep -i gd`
2. Verify upload directory permissions (775 or 755)
3. Check PHP memory_limit (recommended: 256M or higher)
4. Ensure frame templates are PNG format with transparency

### Form Submission Errors
1. Check WordPress debug log for errors
2. Verify nonce is being generated correctly
3. Check file upload size limits in php.ini
4. Ensure database tables were created during activation

### Gallery Not Displaying
1. Verify shortcode is correct: `[ambassador_gallery]`
2. Check if there are approved submissions
3. Clear WordPress cache and page cache
4. Check theme compatibility

## Support & Contribution

### Getting Help
- Check the [Issues](https://github.com/MahirEO/campus-ambassador-manager/issues) page
- Review [Troubleshooting](#troubleshooting) section
- Contact: [Open an issue](https://github.com/MahirEO/campus-ambassador-manager/issues/new)

### Contributing
Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### Version 1.0.0
- Initial release
- Ambassador submission form
- Admin dashboard
- Campaign management
- Graphics generation with custom frames
- Public gallery display
- Multi-frame template support
- REST API endpoints
- Export functionality

## Credits

Developed by MahirEO

## Screenshots

1. **Admin Dashboard** - Overview of all submissions and campaigns
2. **Submission Form** - User-facing ambassador application form
3. **Campaign Manager** - Create and manage ambassador campaigns
4. **Frame Template Manager** - Upload and manage custom graphic frames
5. **Ambassador Gallery** - Public display of approved ambassadors
6. **Graphics Generator** - Automated custom graphic creation

---

**Note**: For the best experience, ensure your hosting environment meets all requirements and keep WordPress, PHP, and the plugin up to date.
