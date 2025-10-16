<?php
/**
 * Verification Email Template
 * Email template for sending verification links to applicants
 * 
 * @package Campus_Ambassador_Manager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Variables available: $name, $verification_url
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php _e('Verify Your Email', 'campus-ambassador-manager'); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            color: #333;
            font-size: 22px;
            margin-top: 0;
        }
        .email-body p {
            color: #666;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .verification-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .alternative-link {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .alternative-link p {
            margin: 5px 0;
            font-size: 14px;
        }
        .alternative-link a {
            color: #667eea;
            word-break: break-all;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .divider {
            height: 1px;
            background: #e0e0e0;
            margin: 30px 0;
        }
        .highlight {
            color: #667eea;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1><?php _e('Campus Ambassador Program', 'campus-ambassador-manager'); ?></h1>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <h2><?php _e('Hi', 'campus-ambassador-manager'); ?> <?php echo esc_html($name); ?>! 👋</h2>
            
            <p>
                <?php _e('Thank you for applying to become a Campus Ambassador! We\'re excited to have you join our community.', 'campus-ambassador-manager'); ?>
            </p>
            
            <p>
                <?php _e('To complete your application, please verify your email address by clicking the button below:', 'campus-ambassador-manager'); ?>
            </p>
            
            <!-- Verification Button -->
            <div class="button-container">
                <a href="<?php echo esc_url($verification_url); ?>" class="verification-button">
                    <?php _e('Verify Email Address', 'campus-ambassador-manager'); ?>
                </a>
            </div>
            
            <!-- Alternative Link -->
            <div class="alternative-link">
                <p><strong><?php _e('Button not working?', 'campus-ambassador-manager'); ?></strong></p>
                <p><?php _e('Copy and paste this link into your browser:', 'campus-ambassador-manager'); ?></p>
                <p><a href="<?php echo esc_url($verification_url); ?>"><?php echo esc_url($verification_url); ?></a></p>
            </div>
            
            <div class="divider"></div>
            
            <p>
                <?php _e('Once verified, our team will review your application. You\'ll receive another email once a decision has been made.', 'campus-ambassador-manager'); ?>
            </p>
            
            <p>
                <strong><?php _e('What happens next?', 'campus-ambassador-manager'); ?></strong>
            </p>
            <ul>
                <li><?php _e('Our team reviews your application (typically 2-3 business days)', 'campus-ambassador-manager'); ?></li>
                <li><?php _e('You\'ll receive an email with the decision', 'campus-ambassador-manager'); ?></li>
                <li><?php _e('If approved, you\'ll get access to ambassador resources and training', 'campus-ambassador-manager'); ?></li>
            </ul>
            
            <p>
                <?php _e('If you didn\'t apply for this program, please ignore this email.', 'campus-ambassador-manager'); ?>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong><?php echo get_bloginfo('name'); ?></strong></p>
            <p><?php _e('Campus Ambassador Program', 'campus-ambassador-manager'); ?></p>
            <p style="margin-top: 20px; font-size: 12px; color: #999;">
                <?php printf(
                    __('This email was sent to %s because you applied to become a Campus Ambassador.', 'campus-ambassador-manager'),
                    esc_html($email)
                ); ?>
            </p>
        </div>
    </div>
</body>
</html>
