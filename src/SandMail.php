<?php
/**
 * Created by OxGroupMedia.
 * User: aliaxander
 * Date: 06.11.15
 * Time: 14:09
 */

namespace Ox;


use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Ox\AbstractModel;

class SandMail extends AbstractModel
{

    public static function sand($title, $messageText, $emailTo)
    {
        $smtpSettings = array();

        if (Settings::getSettings()->smtpPort) {
            $smtpSettings['port'] = Settings::getSettings()->smtpPort;
        }
        if (Settings::getSettings()->smtpHost) {
            $smtpSettings['host'] = Settings::getSettings()->smtpHost;
        }
        if (Settings::getSettings()->smtpAuth == 1) {
            $smtpSettings['username'] = Settings::getSettings()->smtpLogin;
            $smtpSettings['password'] = Settings::getSettings()->smtpPassword;
        }

        if (Settings::getSettings()->smtpSecure != 0) {
            $smtpSettings['secure'] = Settings::getSettings()->smtpSecure;
        }
        $mailer = new SmtpMailer($smtpSettings);

        $message = new Message();
        $fromName = Settings::getSettings()->smtpSandName;
        $fromMail = Settings::getSettings()->smtpEmail;
        $message->setFrom("{$fromName} <{$fromMail}>")
            ->addTo($emailTo)
            ->setSubject($title)
            ->setBody($messageText);

        $mailer->send($message);
    }
    
}