<?php
/**
 * This api provides all the functions to send a basic email to the customers of this site
 * IMPORTANT: the site owner email must be set in /admin/config/customer-product-predictor/cpp-ubercart
 * The development and maintenance of this file is made by ThinkBinario: thinkbinario.com
 * @package    Customer Product Predictor
 * @author     Jose Ortiz <jose@thinkbinario.com>
 */
class Email
{
    var $siteOwnersEmail;
    var $email;
    var $name;
    var $subject;
    var $contact_message;
    var $message;
    var $error;

    /**
     * Email constructor.
     * @param $user_email
     * @param $user_name
     * @param $email_subject
     * @param $email_body_message
     */
    public function __construct($user_email, $user_name, $email_subject, $email_body_message )
    {
        $this->siteOwnersEmail = trim(stripslashes(variable_get('owner_email'))); // extracted from cpp configuration page.
        $this->email = trim(stripslashes($user_email));
        $this->name = trim(stripslashes($user_name));
        $this->subject = trim(stripslashes($email_subject));
        $this->contact_message = trim(stripslashes($email_body_message));
        $this->message = "";
        $this->error = array();
    }

    /**
     * @return bool : true if the email was sent. Otherwise returns false.
     */
    public function send ()
    {
        if(variable_get('email_activation')) {

            // Check Name
            if (strlen($this->name) < 2) {
                $this->error['name'] = "Please enter your name.";
            }
            // Check Email
            if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $this->email)) {
                $this->error['email'] = "Please enter a valid email address.";
            }
            // Check Message
            if (strlen($this->contact_message) <= 0) {
                $this->error['message'] = "Please enter your message. It should have at least 15 characters.";
            }
            // Subject
            if ($this->subject == '') { $this->subject = "Contact Form Submission"; }

            // Set Message

            $this->message .= "Email from: " . $this->name . "<br />";
            $this->message .= "Email address: " . $this->email . "<br />";
            $this->message .= "Message: <br />";
            $this->message .= $this->contact_message;
            $this->message .= "<br /> ----- <br /> This email was sent from your site's contact form. <br />";

            // Set From: header
            $from =  $this->name . " <" . $this->email . ">";

            // Email Headers
            $headers = "From: " . $from . "\r\n";
            $headers .= "Reply-To: ". $this->email . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


            if (!$this->error) {

                ini_set("sendmail_from", $this->siteOwnersEmail); // for windows server
                return mail("joseortizcosta@icloud.com", $this->subject, $this->message, $headers);


            } # end if - no validation error
            else
                var_dump($this->error);
        }
    }

    /**
     * @return array : all the errors, if any, encountered during the sending email tasks
     */
    public function errors()
    {
        return $this->error;
    }

    // API Setters
    public function setRecipientName ($name)
    {
        $this->name = $name;
    }
    public function setRecipientEmail ($email)
    {
        $this->email = $email;
    }

    public function setSubject ($subject)
    {
        $this->subject = $subject;
    }

    public function setBodyMessage ($body)
    {
        $this->contact_message = $body;
    }

}

