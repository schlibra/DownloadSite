<?php
use PHPMailer\PHPMailer\PHPMailer;
class Mail
{
    public static function send($address, $subject, $content, $altBody)
    {
        $config = parse_ini_file("mail.ini");
        $smtp = $config["smtp"];
        $username = $config["username"];
        $password = $config["password"];
        $port = $config["port"];
        $encrypt = $config["encrypt"];
        try {
            $mail               = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host         = $smtp;
            $mail->SMTPAuth     = true;
            $mail->Username     = $username;
            $mail->Password     = $password;
            $mail->SMTPSecure   = $encrypt;
            $mail->Port         = $port;

            $mail->setFrom($username, '下载站');
            $mail->addAddress($address);

            $mail->isHTML(true);
            $mail->CharSet      = "utf-8";
            $mail->Subject      = $subject;
            $mail->Body         = $content;
            $mail->AltBody      = $altBody;
            $mail->send();
            return true;
        }catch (Exception $e){
            result(500, $e->getMessage());
        }
        return false;
    }
}