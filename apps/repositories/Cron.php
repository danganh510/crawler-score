<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecSentEmailLog;
use Phalcon\Mvc\User\Component;

class Cron extends Component
{
    public function sendEmail($email,$full_name,$type,$template,$lesson_sent_id)
    {
        $templateAutoEmail = new TemplateAutoEmail;
        $email_template = $templateAutoEmail->getEmailSendAuto($full_name,$template,$lesson_sent_id,$type,$email);
        return $this->sendEmailAuto($email_template,$email,$full_name);
    }
    public function sendEmailAuto($email_template,$email_sent,$email_fullname) {
        if($email_template['success'] == true) {
            $from_email = "noreply@forexcec.com";
            $reply_to_email = "noreply@forexcec.com";
            $to_email = $email_sent;
            $content = $email_template["content"];
            $subject = $email_template["subject"];
            $from_full_name = $reply_to_name = 'ForexCEC';
            $to_full_name = $email_fullname;
            return $this->my->sendEmail(
                $from_email,
                $to_email,
                $subject,
                $content ,
                $from_full_name,
                $to_full_name,
                $reply_to_email,
                $reply_to_name);
        } else {
            return [
                'success' => false,
                'message' => $email_template['message']
            ];
        }
    }
    public function getDayByTimestamp($time_now,$time_submit) {
        //format M/D/Y, not use formatDateTime()
        $date1 = new \DateTime($this->my->formatDateTimeSendEmail($time_now));
        $date2 = new \DateTime($this->my->formatDateTimeSendEmail($time_submit));
        $interval = $date1->diff($date2);
        return $interval->days;
    }
}



