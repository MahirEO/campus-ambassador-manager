<?php
/**
 * Ambassador Card Template
 * Template for displaying ambassador application form and cards
 * 
 * @package Campus_Ambassador_Manager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="cam-form-container">
    <h2 class="cam-form-title"><?php _e('Become a Campus Ambassador', 'campus-ambassador-manager'); ?></h2>
    <p class="cam-form-description">
        <?php _e('Join our community of passionate student leaders and represent us on your campus!', 'campus-ambassador-manager'); ?>
    </p>
    
    <div class="cam-form-messages"></div>
    
    <form id="campus-ambassador-form" class="cam-application-form">
        <!-- Name Field -->
        <div class="cam-form-field">
            <label for="cam_name" class="cam-form-label">
                <?php _e('Full Name', 'campus-ambassador-manager'); ?>
                <span class="required">*</span>
            </label>
            <input 
                type="text" 
                id="cam_name" 
                name="cam_name" 
                class="cam-form-input" 
                required
                placeholder="<?php _e('Enter your full name', 'campus-ambassador-manager'); ?>"
            />
        </div>
        
        <!-- Email Field -->
        <div class="cam-form-field">
            <label for="cam_email" class="cam-form-label">
                <?php _e('Email Address', 'campus-ambassador-manager'); ?>
                <span class="required">*</span>
            </label>
            <input 
                type="email" 
                id="cam_email" 
                name="cam_email" 
                class="cam-form-input" 
                required
                placeholder="<?php _e('your.email@university.edu', 'campus-ambassador-manager'); ?>"
            />
        </div>
        
        <!-- Phone Field -->
        <div class="cam-form-field">
            <label for="cam_phone" class="cam-form-label">
                <?php _e('Phone Number', 'campus-ambassador-manager'); ?>
            </label>
            <input 
                type="tel" 
                id="cam_phone" 
                name="cam_phone" 
                class="cam-form-input" 
                placeholder="<?php _e('+1 (555) 123-4567', 'campus-ambassador-manager'); ?>"
            />
        </div>
        
        <!-- University Field -->
        <div class="cam-form-field">
            <label for="cam_university" class="cam-form-label">
                <?php _e('University', 'campus-ambassador-manager'); ?>
                <span class="required">*</span>
            </label>
            <input 
                type="text" 
                id="cam_university" 
                name="cam_university" 
                class="cam-form-input" 
                required
                placeholder="<?php _e('Your University Name', 'campus-ambassador-manager'); ?>"
            />
        </div>
        
        <!-- Major Field -->
        <div class="cam-form-field">
            <label for="cam_major" class="cam-form-label">
                <?php _e('Major/Field of Study', 'campus-ambassador-manager'); ?>
            </label>
            <input 
                type="text" 
                id="cam_major" 
                name="cam_major" 
                class="cam-form-input" 
                placeholder="<?php _e('e.g., Computer Science', 'campus-ambassador-manager'); ?>"
            />
        </div>
        
        <!-- Year Field -->
        <div class="cam-form-field">
            <label for="cam_year" class="cam-form-label">
                <?php _e('Year of Study', 'campus-ambassador-manager'); ?>
            </label>
            <select id="cam_year" name="cam_year" class="cam-form-select">
                <option value=""><?php _e('Select Year', 'campus-ambassador-manager'); ?></option>
                <option value="freshman"><?php _e('Freshman', 'campus-ambassador-manager'); ?></option>
                <option value="sophomore"><?php _e('Sophomore', 'campus-ambassador-manager'); ?></option>
                <option value="junior"><?php _e('Junior', 'campus-ambassador-manager'); ?></option>
                <option value="senior"><?php _e('Senior', 'campus-ambassador-manager'); ?></option>
                <option value="graduate"><?php _e('Graduate', 'campus-ambassador-manager'); ?></option>
            </select>
        </div>
        
        <!-- Motivation Field -->
        <div class="cam-form-field">
            <label for="cam_motivation" class="cam-form-label">
                <?php _e('Why do you want to be a Campus Ambassador?', 'campus-ambassador-manager'); ?>
            </label>
            <textarea 
                id="cam_motivation" 
                name="cam_motivation" 
                class="cam-form-textarea"
                rows="5"
                maxlength="1000"
                placeholder="<?php _e('Tell us about your motivation, leadership experience, and what you can bring to our program...', 'campus-ambassador-manager'); ?>"
            ></textarea>
        </div>
        
        <!-- Submit Button -->
        <div class="cam-form-field">
            <button type="submit" id="cam_submit_btn" class="cam-submit-button">
                <?php _e('Submit Application', 'campus-ambassador-manager'); ?>
            </button>
        </div>
    </form>
</div>

<?php
// Display approved ambassadors (optional)
if (isset($show_ambassadors) && $show_ambassadors) :
    $approved_ambassadors = CAM_Form_Handler::get_applications('approved', 50);
    
    if (!empty($approved_ambassadors)) :
?>
    <div class="cam-ambassadors-section">
        <h2 class="cam-text-center"><?php _e('Meet Our Campus Ambassadors', 'campus-ambassador-manager'); ?></h2>
        
        <div class="cam-ambassador-cards">
            <?php foreach ($approved_ambassadors as $ambassador) : 
                $initials = '';
                $name_parts = explode(' ', $ambassador->name);
                if (count($name_parts) >= 2) {
                    $initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[1], 0, 1));
                } else {
                    $initials = strtoupper(substr($ambassador->name, 0, 2));
                }
            ?>
                <div class="cam-ambassador-card">
                    <div class="cam-ambassador-card-header">
                        <div class="cam-ambassador-avatar"><?php echo esc_html($initials); ?></div>
                        <div>
                            <h3 class="cam-ambassador-name"><?php echo esc_html($ambassador->name); ?></h3>
                            <p class="cam-ambassador-university"><?php echo esc_html($ambassador->university); ?></p>
                        </div>
                    </div>
                    
                    <div class="cam-ambassador-details">
                        <?php if ($ambassador->major) : ?>
                            <div class="cam-ambassador-detail-item">
                                <span class="cam-ambassador-detail-label"><?php _e('Major:', 'campus-ambassador-manager'); ?></span>
                                <span class="cam-ambassador-detail-value"><?php echo esc_html($ambassador->major); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($ambassador->year) : ?>
                            <div class="cam-ambassador-detail-item">
                                <span class="cam-ambassador-detail-label"><?php _e('Year:', 'campus-ambassador-manager'); ?></span>
                                <span class="cam-ambassador-detail-value"><?php echo esc_html(ucfirst($ambassador->year)); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php 
    endif;
endif;
?>
