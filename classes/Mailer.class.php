<?php

class Mailer
{

    var $mailTo;
    var $smtp;
    var $template;
    
    function sendMail($mailTo, $data, $template)
    {
    
    }    
    
    function prepareTemplate($data, $template)
    {
        return $template;
    }
}
