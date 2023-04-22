<?php
// Importing the requisities of PHPMailer
require_once "{$_SERVER['DOCUMENT_ROOT']}/Modules/PHPMailer/src/PHPMailer.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Modules/PHPMailer/src/Exception.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Modules/PHPMailer/src/SMTP.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
/**
 * Simplifying the use of PHPMailer
 */
class Mail
{
    /**
     * The recipient of the mail
     */
    private string $recipient;
    /**
     * Subject of the mail
     */
    private string $subject;
    /**
     * body of the mail
     */
    private string $message;
    /**
     * PHPMailer
     */
    protected $PHPMailer;
    /**
     * Upon, instantiation, its dependecy is also instantiated
     */
    public function __construct()
    {
        $this->PHPMailer = new PHPMailer\PHPMailer\PHPMailer(true);
    }
    public function getRecipient(): string
    {
        return $this->recipient;
    }
    public function setRecipient(string $recipient): void
    {
        $this->recipient = $recipient;
    }
    public function getSubject(): string
    {
        return $this->subject;
    }
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
    /**
     * Sending the mail after having configured PHPMailer
     * @param   string  $recipient  Receiver of the mail
     * @param   string  $subject    Subject of the mail
     * @param   string  $message    Body of the mail
     * @return  void
     */
    public function send(string $recipient, string $subject, string $message): void
    {
        $this->setRecipient($recipient);
        $this->setSubject($subject);
        $this->setMessage($message);
        $this->PHPMailer->isSMTP();
        $this->PHPMailer->CharSet = "UTF-8";
        $this->PHPMailer->Host = "smtp-mail.outlook.com";
        $this->PHPMailer->SMTPDebug = 0;
        $this->PHPMailer->Port = 587;
        $this->PHPMailer->SMTPSecure = 'tls';
        $this->PHPMailer->SMTPAuth = true;
        $this->PHPMailer->isHTML(true);
        $this->PHPMailer->Username = Environment::MailUsername;
        $this->PHPMailer->Password = Environment::MailPassword;
        $this->PHPMailer->setFrom($this->PHPMailer->Username);
        $this->PHPMailer->addAddress($this->getRecipient());
        $this->PHPMailer->Subject = $this->getSubject();
        $this->PHPMailer->Body = $this->getMessage();
        $this->PHPMailer->send();
    }
}
