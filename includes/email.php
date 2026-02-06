<?php 

/**
 * function to send an email to the site admin with the contact form data
 * @param mixed $addresses
 * @param mixed $subject
 * @param mixed $body
 * @return bool
 */
function email($addresses, $subject, $body)
{
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['EMAIL_USERNAME'];
        $mail->Password   = $_ENV['EMAIL_PASSWORD'];
        $mail->Port       = $_ENV['EMAIL_PORT'];

        $mail->setFrom($_ENV['EMAIL_SENDER'], $_ENV['EMAIL_NAME']);

        // No recipient
        if (!isset($addresses['to']) || !$addresses['to']) {
            return false;
        }
        if (is_array($addresses['to'])) {
            $mail->addAddress($addresses['to']['email'], $addresses['to']['name']);
        } else {
            $mail->addAddress($addresses['to']);
        }
        // Reply to
        if (isset($addresses['replyto']) && $addresses['replyto']) {
            if (is_array($addresses['replyto'])) {
                $mail->addReplyTo($addresses['replyto']['email'], $addresses['replyto']['name']);
            } else {
                $mail->addReplyTo($addresses['replyto']);
            }
        }
        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}