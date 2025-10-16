/**
 * Frontend JavaScript for Campus Ambassador Manager
 * Handles form validation, AJAX submission, and user interactions
 * 
 * @package Campus_Ambassador_Manager
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        /**
         * Form validation
         */
        function validateForm(formData) {
            var errors = [];
            
            // Name validation
            if (!formData.name || formData.name.trim().length < 2) {
                errors.push('Please enter your full name.');
            }
            
            // Email validation
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!formData.email || !emailRegex.test(formData.email)) {
                errors.push('Please enter a valid email address.');
            }
            
            // Phone validation (optional but if provided, must be valid)
            if (formData.phone && formData.phone.length > 0) {
                var phoneRegex = /^[\d\s()+-]+$/;
                if (!phoneRegex.test(formData.phone)) {
                    errors.push('Please enter a valid phone number.');
                }
            }
            
            // University validation
            if (!formData.university || formData.university.trim().length < 2) {
                errors.push('Please enter your university name.');
            }
            
            // Motivation validation
            if (formData.motivation && formData.motivation.length > 1000) {
                errors.push('Motivation text is too long (max 1000 characters).');
            }
            
            return errors;
        }
        
        /**
         * Display errors
         */
        function displayErrors(errors) {
            var errorHtml = '<div class="cam-error-message">';
            errorHtml += '<ul>';
            errors.forEach(function(error) {
                errorHtml += '<li>' + error + '</li>';
            });
            errorHtml += '</ul>';
            errorHtml += '</div>';
            
            $('.cam-form-messages').html(errorHtml);
            $('html, body').animate({
                scrollTop: $('.cam-form-messages').offset().top - 100
            }, 500);
        }
        
        /**
         * Display success message
         */
        function displaySuccess(message) {
            var successHtml = '<div class="cam-success-message">' + message + '</div>';
            $('.cam-form-messages').html(successHtml);
            $('html, body').animate({
                scrollTop: $('.cam-form-messages').offset().top - 100
            }, 500);
        }
        
        /**
         * Handle form submission
         */
        $('#campus-ambassador-form').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous messages
            $('.cam-form-messages').html('');
            
            // Get form data
            var formData = {
                name: $('#cam_name').val(),
                email: $('#cam_email').val(),
                phone: $('#cam_phone').val(),
                university: $('#cam_university').val(),
                major: $('#cam_major').val(),
                year: $('#cam_year').val(),
                motivation: $('#cam_motivation').val(),
                nonce: camAjax.nonce
            };
            
            // Validate form
            var errors = validateForm(formData);
            if (errors.length > 0) {
                displayErrors(errors);
                return;
            }
            
            // Disable submit button
            var $submitBtn = $('#cam_submit_btn');
            var originalText = $submitBtn.text();
            $submitBtn.prop('disabled', true).text('Submitting...');
            
            // Submit via AJAX
            $.ajax({
                url: camAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cam_submit_application',
                    ...formData
                },
                success: function(response) {
                    if (response.success) {
                        displaySuccess(response.data.message);
                        $('#campus-ambassador-form')[0].reset();
                    } else {
                        displayErrors([response.data.message]);
                        $submitBtn.prop('disabled', false).text(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    displayErrors(['An error occurred. Please try again later.']);
                    $submitBtn.prop('disabled', false).text(originalText);
                    console.error('AJAX Error:', error);
                }
            });
        });
        
        /**
         * Character counter for motivation field
         */
        $('#cam_motivation').on('input', function() {
            var length = $(this).val().length;
            var maxLength = 1000;
            var remaining = maxLength - length;
            
            if ($('#cam_char_counter').length === 0) {
                $(this).after('<div id="cam_char_counter" class="cam-char-counter"></div>');
            }
            
            $('#cam_char_counter').text(remaining + ' characters remaining');
            
            if (remaining < 0) {
                $('#cam_char_counter').addClass('cam-char-counter-error');
            } else {
                $('#cam_char_counter').removeClass('cam-char-counter-error');
            }
        });
        
        /**
         * Input field animations
         */
        $('.cam-form-input, .cam-form-textarea, .cam-form-select').on('focus', function() {
            $(this).parent('.cam-form-field').addClass('cam-field-focused');
        });
        
        $('.cam-form-input, .cam-form-textarea, .cam-form-select').on('blur', function() {
            $(this).parent('.cam-form-field').removeClass('cam-field-focused');
            if ($(this).val() !== '') {
                $(this).parent('.cam-form-field').addClass('cam-field-filled');
            } else {
                $(this).parent('.cam-form-field').removeClass('cam-field-filled');
            }
        });
        
        /**
         * Real-time email validation
         */
        $('#cam_email').on('blur', function() {
            var email = $(this).val();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                $(this).addClass('cam-input-error');
                if ($('#cam_email_error').length === 0) {
                    $(this).after('<span id="cam_email_error" class="cam-field-error">Please enter a valid email address.</span>');
                }
            } else {
                $(this).removeClass('cam-input-error');
                $('#cam_email_error').remove();
            }
        });
        
        /**
         * Handle verification success message
         */
        if (window.location.search.indexOf('cam_verified=1') !== -1) {
            displaySuccess('Your email has been successfully verified! Your application is now under review.');
        }
    });
    
})(jQuery);
