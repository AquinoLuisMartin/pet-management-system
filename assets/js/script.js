<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

/**
 * Pet Management System - Animation and UI Enhancement Script
 * This script handles animations, transitions, and interactive elements across the application.
 */

$(document).ready(function() {
    
    // ===== FORM ANIMATIONS =====
    
    // Input field effects
    $('input, textarea, select').on('focus', function() {
        $(this).closest('.form-group').addClass('focused');
    }).on('blur', function() {
        if (!$(this).val()) {
            $(this).closest('.form-group').removeClass('focused');
        }
    });
    
    // Pre-fill effect for inputs with values
    $('input, textarea, select').each(function() {
        if ($(this).val()) {
            $(this).closest('.form-group').addClass('focused');
        }
    });
    
    // Form submission animation
    $('form').on('submit', function() {
        const form = $(this);
        if (!form.hasClass('no-animation') && !form.is('#searchForm')) {
            const submitBtn = form.find('[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
            
            // Re-enable after 3 seconds if form doesn't redirect
            setTimeout(function() {
                if (submitBtn.prop('disabled')) {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            }, 3000);
        }
    });
    
    // Login/Signup form toggle animation
    $('#toggleSignup, #toggleLogin').on('click', function(e) {
        e.preventDefault();
        
        const isSignup = $(this).attr('id') === 'toggleSignup';
        const currentForm = isSignup ? $('#loginForm') : $('#signupForm');
        const targetForm = isSignup ? $('#signupForm') : $('#loginForm');
        
        currentForm.fadeOut(300, function() {
            targetForm.fadeIn(300);
        });
        
        // Update page title
        const title = isSignup ? 'Sign Up' : 'Login';
        $('#authTitle').text(title);
    });
    
    // Password visibility toggle
    $('.password-toggle').on('click', function() {
        const input = $(this).siblings('input');
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Shake animation for login errors
    if ($('.login-error').length) {
        $('#loginForm').addClass('shake-animation');
        setTimeout(function() {
            $('#loginForm').removeClass('shake-animation');
        }, 500);
    }
    
    // ===== BUTTON ANIMATIONS =====
    
    // Standard button hover effect
    $('.btn').not('.btn-link, .no-animation').hover(
        function() { $(this).addClass('btn-pulse'); },
        function() { $(this).removeClass('btn-pulse'); }
    );
    
    // Action button click ripple effect
    $('.btn-primary, .btn-success, .btn-danger').on('mousedown', function(e) {
        const button = $(this);
        
        // Create ripple element
        const ripple = $('<span class="ripple-effect"></span>');
        const size = Math.max(button.outerWidth(), button.outerHeight());
        const pos = button.offset();
        
        // Set ripple position and size
        ripple.css({
            width: size + 'px',
            height: size + 'px',
            top: (e.pageY - pos.top - size/2) + 'px',
            left: (e.pageX - pos.left - size/2) + 'px'
        }).appendTo(button);
        
        // Remove after animation completes
        setTimeout(function() {
            ripple.remove();
        }, 500);
    });
    
    // CRUD operation buttons (Add/Edit/Delete)
    $('.add-btn').on('click', function() {
        $(this).addClass('scale-animation');
        setTimeout(() => $(this).removeClass('scale-animation'), 300);
    });
    
    // View button animation
    $('.view-btn').hover(
        function() { $(this).find('i').addClass('fa-beat-fade'); },
        function() { $(this).find('i').removeClass('fa-beat-fade'); }
    );
    
    // Edit button animation
    $('.edit-btn').hover(
        function() { $(this).find('i').addClass('fa-bounce'); },
        function() { $(this).find('i').removeClass('fa-bounce'); }
    );
    
    // Delete button animation
    $('.delete-btn').hover(
        function() { $(this).find('i').addClass('fa-shake'); },
        function() { $(this).find('i').removeClass('fa-shake'); }
    );
    
    // Action confirmation for delete buttons
    $('.delete-btn').on('click', function(e) {
        const button = $(this);
        
        if (!button.hasClass('confirm-active')) {
            e.preventDefault();
            e.stopPropagation();
            
            // First click: change to confirmation state
            button.addClass('confirm-active btn-danger').removeClass('btn-outline-danger');
            button.find('i').removeClass('fa-trash').addClass('fa-exclamation-triangle');
            
            // Reset after delay if not clicked
            setTimeout(function() {
                if (button.hasClass('confirm-active')) {
                    button.removeClass('confirm-active btn-danger').addClass('btn-outline-danger');
                    button.find('i').removeClass('fa-exclamation-triangle').addClass('fa-trash');
                }
            }, 3000);
        }
    });
    
    // ===== MODAL ANIMATIONS =====
    
    // Enhanced modal animations
    $('.modal').on('show.bs.modal', function() {
        $(this).find('.modal-dialog').removeClass('fade-out-down').addClass('fade-in-down');
    });
    
    $('.modal').on('hide.bs.modal', function() {
        $(this).find('.modal-dialog').removeClass('fade-in-down').addClass('fade-out-down');
    });
    
    // ===== NAVIGATION ANIMATIONS =====
    
    // Active link highlight effect
    $('.nav-link').each(function() {
        if (window.location.pathname.indexOf($(this).attr('href')) > -1) {
            $(this).addClass('active-link');
        }
    });
    
    // Navigation hover effect
    $('.nav-item').hover(
        function() {
            if (!$(this).find('.nav-link').hasClass('active-link')) {
                $(this).find('.nav-link').addClass('nav-hover');
            }
        },
        function() {
            $(this).find('.nav-link').removeClass('nav-hover');
        }
    );
    
    // ===== TABLE & CARD ANIMATIONS =====
    
    // Row hover effect
    $('tbody tr').hover(
        function() { $(this).addClass('row-highlight'); },
        function() { $(this).removeClass('row-highlight'); }
    );
    
    // Card hover effect
    $('.card').not('.no-animation').hover(
        function() { $(this).addClass('card-lift'); },
        function() { $(this).removeClass('card-lift'); }
    );
    
    // Data refresh spinner
    $('.refresh-btn').on('click', function() {
        const btn = $(this);
        btn.addClass('fa-spin');
        
        setTimeout(function() {
            btn.removeClass('fa-spin');
        }, 1000);
    });
    
    // ===== ALERT ANIMATIONS =====
    
    // Custom alert animations
    $('.alert').addClass('fade-in-down');
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').not('.alert-permanent').alert('close');
    }, 5000);
    
    // ===== DASHBOARD WIDGETS =====
    
    // Animate counters
    $('.counter-number').each(function() {
        const $this = $(this);
        const target = parseInt($this.text(), 10);
        
        $({ Counter: 0 }).animate({ Counter: target }, {
            duration: 1000,
            easing: 'swing',
            step: function() {
                $this.text(Math.ceil(this.Counter));
            },
            complete: function() {
                $this.text(target);
            }
        });
    });
    
    // ===== UTILITY FUNCTIONS =====
    
    // Flash message function
    window.flashMessage = function(message, type = 'success') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show fade-in-down" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Add to page and set to auto-dismiss
        const alert = $(alertHtml).prependTo('.container:first');
        setTimeout(function() {
            alert.alert('close');
        }, 5000);
    };
    
    // Loading overlay
    window.showLoading = function() {
        $('body').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        $('.loading-overlay').fadeIn(300);
    };
    
    window.hideLoading = function() {
        $('.loading-overlay').fadeOut(300, function() {
            $(this).remove();
        });
    };
    
    // Confirm dialog with animation
    window.animatedConfirm = function(message, callback) {
        const dialogHtml = `
            <div class="confirm-dialog-overlay">
                <div class="confirm-dialog fade-in-down">
                    <div class="confirm-dialog-content">
                        <p>${message}</p>
                        <div class="confirm-dialog-buttons">
                            <button class="btn btn-secondary confirm-cancel">Cancel</button>
                            <button class="btn btn-primary confirm-ok">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const dialog = $(dialogHtml).appendTo('body');
        
        dialog.find('.confirm-ok').on('click', function() {
            dialog.find('.confirm-dialog').removeClass('fade-in-down').addClass('fade-out-down');
            setTimeout(function() {
                dialog.remove();
                if (typeof callback === 'function') callback(true);
            }, 300);
        });
        
        dialog.find('.confirm-cancel').on('click', function() {
            dialog.find('.confirm-dialog').removeClass('fade-in-down').addClass('fade-out-down');
            setTimeout(function() {
                dialog.remove();
                if (typeof callback === 'function') callback(false);
            }, 300);
        });
    };
});

// Add required CSS for animations
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        /* Animation keyframes */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translate3d(0, -20px, 0); }
            to { opacity: 1; transform: translate3d(0, 0, 0); }
        }
        
        @keyframes fadeOutDown {
            from { opacity: 1; transform: translate3d(0, 0, 0); }
            to { opacity: 0; transform: translate3d(0, 20px, 0); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes ripple {
            to { transform: scale(4); opacity: 0; }
        }
        
        /* Animation classes */
        .fade-in-down { animation: fadeInDown 0.3s ease-out forwards; }
        .fade-out-down { animation: fadeOutDown 0.3s ease-out forwards; }
        .shake-animation { animation: shake 0.5s ease-in-out; }
        .btn-pulse { animation: pulse 0.3s ease-in-out; }
        .scale-animation { animation: pulse 0.3s ease-in-out; }
        
        /* Button effects */
        .btn { transition: all 0.2s ease; }
        .btn-primary:hover, .btn-success:hover, .btn-danger:hover { transform: translateY(-2px); box-shadow: 0 7px 14px rgba(50,50,93,.1), 0 3px 6px rgba(0,0,0,.08); }
        .btn-primary:active, .btn-success:active, .btn-danger:active { transform: translateY(1px); }
        
        /* Ripple effect */
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple 0.5s linear;
            pointer-events: none;
        }
        
        /* Card and table effects */
        .row-highlight { background-color: rgba(0, 123, 255, 0.04); transition: all 0.2s ease; }
        .card-lift { transform: translateY(-5px); box-shadow: 0 7px 14px rgba(50,50,93,.1), 0 3px 6px rgba(0,0,0,.08); transition: all 0.2s ease; }
        
        /* Form effects */
        .form-group.focused label { color: #007bff; font-weight: 500; transform: translateY(-5px); transition: all 0.2s ease; }
        
        /* Navigation effects */
        .nav-link { transition: all 0.2s ease; }
        .active-link { font-weight: 600; border-bottom: 2px solid #007bff; }
        .nav-hover { transform: translateY(-2px); }
        
        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        /* Confirm dialog */
        .confirm-dialog-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .confirm-dialog {
            background: white;
            border-radius: 8px;
            padding: 20px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 15px 35px rgba(50,50,93,.1), 0 5px 15px rgba(0,0,0,.07);
        }
        
        .confirm-dialog-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
    `;
    
    document.head.appendChild(style);
});