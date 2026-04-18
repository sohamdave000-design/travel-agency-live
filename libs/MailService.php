<?php
require_once dirname(__DIR__) . '/config/mail.php';

class MailService {
    public static function send($to, $subject, $body, $debug = false) {
        $host = MAIL_HOST;
        $port = MAIL_PORT;
        $user = MAIL_USER;
        $pass = str_replace(' ', '', MAIL_PASS); // Remove spaces from app password
        $from = MAIL_FROM;
        $fromName = MAIL_FROM_NAME;
        
        $log = [];

        try {
            $socket = @fsockopen($host, $port, $errno, $errstr, 15);
            if (!$socket) throw new Exception("Connection failed: $errstr ($errno)");
            
            $getResponse = function($socket) use (&$log) {
                $res = "";
                while($str = fgets($socket, 515)) {
                    $res .= $str;
                    if(substr($str, 3, 1) == " ") break;
                }
                $log[] = "S: " . trim($res);
                return $res;
            };

            $sendCommand = function($socket, $cmd) use ($getResponse, &$log) {
                $log[] = "C: " . (strpos($cmd, 'AUTH') ? 'AUTH [hidden]' : $cmd);
                fputs($socket, $cmd . "\r\n");
                return $getResponse($socket);
            };

            $getResponse($socket);
            $sendCommand($socket, "EHLO localhost");
            $res = $sendCommand($socket, "STARTTLS");
            if (strpos($res, '220') === false) throw new Exception("STARTTLS failed: $res");
            
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new Exception("Unable to enable TLS");
            }

            $sendCommand($socket, "EHLO localhost");
            $sendCommand($socket, "AUTH LOGIN");
            $sendCommand($socket, base64_encode($user));
            $res = $sendCommand($socket, base64_encode($pass));
            if (strpos($res, '235') === false) throw new Exception("Login failed: $res");
            
            $sendCommand($socket, "MAIL FROM: <$from>");
            $sendCommand($socket, "RCPT TO: <$to>");
            $sendCommand($socket, "DATA");
            
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "From: $fromName <$from>\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "Date: " . date("r") . "\r\n";
            
            $res = $sendCommand($socket, $headers . "\r\n" . $body . "\r\n.");
            if (strpos($res, '250') === false) throw new Exception("Failed to send data: $res");
            
            $sendCommand($socket, "QUIT");
            fclose($socket);
            
            if ($debug) return $log;
            return true;
        } catch (Exception $e) {
            $log[] = "ERROR: " . $e->getMessage();
            error_log("SMTP Mail Error: " . $e->getMessage());
            if ($debug) return $log;
            return false;
        }
    }

    public static function notifyAdmin($name, $email, $message) {
        $subject = "New Inquiry from $name";
        $body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px; color: #1e293b;'>
                <h2 style='color: #2563eb;'>New Contact Inquiry</h2>
                <hr style='border: 0; border-top: 1px solid #f1f5f9; margin: 20px 0;'>
                <p><strong>Customer Name:</strong> $name</p>
                <p><strong>Customer Email:</strong> <a href='mailto:$email'>$email</a></p>
                <div style='background: #f8fafc; padding: 15px; border-radius: 8px; margin-top: 10px;'>
                    <p><strong>Message:</strong></p>
                    <p style='font-style: italic;'>\"" . nl2br(htmlspecialchars($message)) . "\"</p>
                </div>
                <p style='font-size: 0.85rem; color: #64748b; margin-top: 20px;'>Sent from Travel Agency Website contact form.</p>
            </div>
        ";
        return self::send(MAIL_USER, $subject, $body);
    }
}
?>
