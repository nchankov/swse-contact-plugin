<?php 
/**
 * Verify turnstile token
 * @return bool
 */
function verifyTurnstile()
{
    if (!isset($_ENV['CLOUDFLARE_SITE_KEY']) || !$_ENV['CLOUDFLARE_SECRET_KEY']) {
        return true; // Skip verification if keys are not set
    }

    // Verify Turnstile token with Cloudflare
    $turnstileSecret = $_ENV['CLOUDFLARE_SECRET_KEY'];
    $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    // Verify Cloudflare Turnstile token
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';

    $verifyData = [
        'secret' => $turnstileSecret,
        'response' => $turnstileToken,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($verifyData)
        ]
    ];

    $context = stream_context_create($options);
    $verifyResponse = file_get_contents($verifyUrl, false, $context);
    $verifyResult = json_decode($verifyResponse, true);

    if (!$verifyResult || !$verifyResult['success']) {
        return json([
            'success' => false,
            'message' => 'Security verification failed. Please try again.'
        ]);
    }
    return true;
}