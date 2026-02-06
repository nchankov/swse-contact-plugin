<?php 
include_once __DIR__ . '/../includes/email.php';
include_once __DIR__ . '/../includes/turnstile.php';

class Form
{
    public function post() {
        verifyCsrf();
        verifyTurnstile();

        try {
            $data = $this->sanitizeData();
            // Convert message new lines to paragraphs
            $message = explode("\n", $data['message']);
            $body = serveFile(
                __DIR__ . '/' . $_ENV['MESSAGE_TEMPLATE_PATH'],
                ['name' => $data['name'], 'paragraphs' => $message, 'email' => $data['email']]
            );
            // Send the email
            email(
                [
                    'to' => $_ENV['EMAIL_USERNAME'],
                    'replyto' => ['email' => $data['email'], 'name' => $data['name']]
                ],
                $_ENV['EMAIL_SUBJECT'],
                $body
            );
            return json(['success' => true, 'message' => 'Your message has been sent successfully!']);
        } catch (Exception $e) {
            return json(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function all()
    {
        return json(['status' => 'ok']);
    }

    /**
     * Check and sanitize the data passed on the post
     * @return array
     */
    protected function sanitizeData()
    {
        // Sanitize and validate input fields
        $name = getPost('name');
        $email = filter_var(getPost('email'), FILTER_VALIDATE_EMAIL);
        $message = getPost('message');

        // Additional validation
        if (empty($name) || strlen($name) < 2) {
            return json(['success' => false, 'message' => 'Please enter a valid name (at least 2 characters).']);
        }
        
        if (!$email) {
            return json(['success' => false, 'message' => 'Please enter a valid email address.']);
        }
        
        if (empty($message) || strlen($message) < 10) {
            return json(['success' => false, 'message' => 'Please enter a message (at least 10 characters).']);
        }

        // Check for spam patterns
        $spam_patterns = [
            '/\b(viagra|cialis|pharmacy|casino|poker|lottery|winner|congratulations)\b/i',
            '/http[s]?:\/\/[^\s]+/i', // URLs in message
            '/(.)\1{10,}/', // Repeated characters
        ];
        
        foreach ($spam_patterns as $pattern) {
            if (preg_match($pattern, $message) || preg_match($pattern, $name)) {
                return json(['success' => false, 'message' => 'Your message appears to be spam. Please try again.']);
            }
        }
        return [
            'name' => $name,
            'email' => $email,
            'message' => $message
        ];
    }
}