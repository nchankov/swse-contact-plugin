"use strict";

(function () {
    // Handle form submission for forms with data-contact-form attribute
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('[data-contact-form]');
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get the flush message container
                const flushContainer = form.querySelector('[data-flush]');
                if (!flushContainer) {
                    console.error('No element with data-flush attribute found in the form');
                    return;
                }
                
                // Clear previous messages
                flushContainer.innerHTML = '';
                flushContainer.className = '';
                
                // Get form data
                const formData = new FormData(form);
                const actionUrl = form.getAttribute('action') || window.location.pathname;
                const method = form.getAttribute('method') || 'POST';
                
                // Disable submit button to prevent double submission
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                }
                
                // Send AJAX request
                fetch(actionUrl, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    // Re-enable submit button
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                    
                    if (data.success) {
                        // Display success message
                        flushContainer.innerHTML = '<p>' + escapeHtml(data.message) + '</p>';
                        flushContainer.className = 'flash-success';
                        
                        // Optionally reset the form on success
                        form.reset();
                    } else {
                        // Display error message from server
                        let errorMessage = data.error || data.email || data.message || 'An error occurred. Please try again.';
                        flushContainer.innerHTML = '<p>' + escapeHtml(errorMessage) + '</p>';
                        flushContainer.className = 'flash-error';
                    }
                    
                    // Scroll to the message
                    flushContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                })
                .catch(function(error) {
                    // Re-enable submit button
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                    
                    // Display error message
                    flushContainer.innerHTML = '<p>An error occurred. Please try again.</p>';
                    flushContainer.className = 'flash-error';
                    console.error('Form submission error:', error);
                });
            });
        });
    });
    
    // Helper function to escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
})();