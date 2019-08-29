<?php namespace Helpers;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Helpers/Mailhelper.php
 * @package     Bootstrap Codecanyon Products
 * @company     Comestoarra Labs <labs@comestoarra.com>
 * @programmer  Rizki Wisnuaji, drg., M.Kom. <rizkiwisnuaji@comestoarra.com>
 * @copyright   2016 Comestoarra Labs. All Rights Reserved.
 * @license     http://codecanyon.net/licenses
 * @version     Release: @1.1@
 * @framework   http://slimframework.com
 *
 *
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 * This file may not be redistributed in whole or significant part.
**/

use Controllers\GlobalController;
use PHPMailer;
use Slim\Slim;

class MailHelper extends GlobalController
{

    private $error;

    public function sendMailWithNativeMailFunction( $user_email, $from_email, $from_name, $subject, $body )
    {

        $headers = "From: $from_name <$from_email>" . PHP_EOL;
        $headers .= "Reply-To: $from_email" . PHP_EOL;
        $headers .= "MIME-Version: 1.0" . PHP_EOL;
        $headers .= "Content-type: text/plain; charset=utf-8" . PHP_EOL;
        $headers .= "Content-Transfer-Encoding: quoted-printable" . PHP_EOL;

        if ( mail ( $user_email, $subject, $body, $headers ) ) :

            return true;

        else :

            Slim::getInstance()->flash( 'error',  'We have problems with Mail Function !' );
            return false;

        endif;

    }

    public function sendMailWithSwiftMailer() //SOON
    {
        return false;
    }
    
    public function sendMailWithSendGrid( $user_email, $from_email, $from_name, $subject, $body, $attachment = FALSE ) //SOON
    {
        return false;
    }

    public function sendMailWithPHPMailer( $user_email, $from_email, $from_name, $subject, $body, $attachment = FALSE, $attachments_name = NULL, $attachment_detail = NULL )
    {
        $mail = new PHPMailer;

        // if you want to send mail via PHPMailer using SMTP credentials
        if ( $this->mailUsedSmtp ) :

            // set PHPMailer to use SMTP
            $mail->IsSMTP();
            // 0 = off, 1 = commands, 2 = commands and data, perfect to see SMTP errors
            $mail->SMTPDebug = 0;
            // enable SMTP authentication
            $mail->SMTPAuth = $this->mailSmtpAuth;
            // encryption
            if ( $this->mailSmtpEncryption != "" ) :

                $mail->SMTPSecure = $this->mailSmtpEncryption;

            endif;

            // set SMTP provider's credentials
            $mail->Host         = $this->mailSmtpHost;
            $mail->Username     = $this->mailSmtpUsername;
            $mail->Password     = $this->mailSmtpPassword;
            $mail->Port         = $this->mailSmtpPort;

        else :

            $mail->IsMail();

        endif;

        // fill mail with data
        $mail->From = $from_email;
        $mail->FromName = $from_name;
        $mail->AddAddress($user_email);
        $mail->Subject = $subject;
        
        if ( $attachment ) :
        
            $mail->addAttachment( $attachment );
            
        endif;
        
        $mail->Body = $body;

        // try to send mail
        //$mail->Send();
        $wasSendingSuccessful = $mail->Send();

        if ( $wasSendingSuccessful ) :

            return true;

        else :
            // if not successful, copy errors into Mail's error property
            $this->error = $mail->ErrorInfo;
            return false;

        endif;
    }

    public function sendMail( $user_email, $from_email, $from_name, $subject, $body, $attachment = FALSE, $attachments_name = NULL, $attachment_detail = NULL )
    {
        if ( $this->mailEngine == "PHP Mailer" ) :

            // returns true if successful, false if not
            return $this->sendMailWithPHPMailer(
                $user_email, $from_email, $from_name, $subject, $body, $attachment = FALSE, $attachments_name = NULL, $attachment_detail = NULL
            );

        endif;

        if ( $this->mailEngine == "Swift Mailer" ) :

            return $this->sendMailWithSwiftMailer();

        endif;
        
        if ( $this->mailEngine == "Send Grid" ) :

            return $this->sendMailWithSendGrid();

        endif;

        if ( $this->mailEngine == "PHP Native Mail" ) :

            return $this->sendMailWithNativeMailFunction(
                $user_email, $from_email, $from_name, $subject, $body, $attachment = FALSE, $attachments_name = NULL, $attachment_detail = NULL
            );

        endif;

        // TODO : Need CHECK
        return false;
    }

    public function getError()
    {
        return $this->error;
    }

}