<?php

namespace Forexceccom\Repositories;


use Forexceccom\Models\ForexcecTemplateAutoEmail;
use Forexceccom\Utils\PasswordGenerator;
use Phalcon\Mvc\User\Component;


class TemplateAutoEmail extends Component {

    public static function getComboboxForm($form) {
        $list_data = ForexcecTemplateAutoEmail::ARRAY_FORM;
        $output = '';
        foreach ($list_data as $item => $value) {
            $selected = '';
            if ($item == $form) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $item . "'>" . $value . "</option>";

        }
        return $output;
    }

    public static function findFirstById($id) {
        return ForexcecTemplateAutoEmail::findFirst([
            'email_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function checkKeyword($emailtemplate_type,$emailtemplate_form, $emailtemplate_id)
    {
        return ForexcecTemplateAutoEmail::findFirst(
            array (
                'email_type = :type: AND email_id != :emailtemplateid: AND email_form = :form:',
                'bind' => array(
                    'type' => $emailtemplate_type,
                    'emailtemplateid' => $emailtemplate_id,
                    'form' => $emailtemplate_form
                ),
            ));
    }
    public static function findFirstByTime($day,$form) {
        return  ForexcecTemplateAutoEmail::findFirst([
            'email_day_send <= :day: AND email_form = :form: AND email_type != "WELCOME_TO_FOREXCEC_THANK_YOU_FOR_YOUR_APPLICATION" 
             AND email_type != "EMAIL_FOOTER" AND email_type != "EMAIL_HEADER" AND email_status = "Y"',
            'bind' => ['day' => $day,'form' => $form],
            'order' => ['email_day_send DESC']
        ]);
    }
    public static function findBySendDay($day,$form) {
        $email = self::findFirstByTime($day,$form);
        $result = [];
        if ($email) {
            $result =   ForexcecTemplateAutoEmail::find([
                'email_day_send = :day: AND email_form = :form: AND email_type != "WELCOME_TO_FOREXCEC_THANK_YOU_FOR_YOUR_APPLICATION" 
                AND email_type != "EMAIL_FOOTER" AND email_type != "EMAIL_HEADER" AND email_status = "Y"',
                'bind' => ['day' => $email->getEmailDaySend(),'form' => $form],
            ]);
        }
        return $result;
    }

    public function completeContentTemplate($content,$form,$email_send) {

        $headerMsg = '';
        $header = self::findFirstByType('EMAIL_HEADER',$form);
        if ($header) {
            $headerMsg = $header->getEmailContent();
        }
        $footerMsg = '';
        $footer = self::findFirstByType('EMAIL_FOOTER',$form);
        if ($footer) {
            $footerMsg = $footer->getEmailContent();
            $token = PasswordGenerator::encryptToken($email_send);
            $token = rawurlencode($token);
            $footerMsg = str_replace(["DATA_TOKEN"], $token, $footerMsg);
        }

        return  $headerMsg . $content . $footerMsg;
    }
    public function getEmailSendAuto($name,$email,$lesson_sent_id,$form,$email_send){
        //subject
        $subject = $email->getEmailSubject();
        $content = $email->getEmailContent();
        $content = str_replace("|||USER_NAME|||", $name, $content);
        $content = str_replace("USER_NAME", $name, $content);
        
        if ($lesson_sent_id) {
            $article_lession = Article::findFirstByLessionId($lesson_sent_id);

            if (!$article_lession) {
                return array('success' => false, 'message' => 'lesson id: '.$lesson_sent_id.' not found');
            }
            $lesson_name = $article_lession->getArticleName();
            $arrStrLesson = explode(': ',$lesson_name);
            unset($arrStrLesson[0]);
            $lesson_name = implode(" ",$arrStrLesson);

            $content = str_replace("|||LESSION_ID|||", $lesson_sent_id, $content);
            $content = str_replace("AR_CONTENT", $article_lession->getArticleContent(), $content);
            $content = str_replace("AR_NAME", $lesson_name, $content);
            $content = str_replace("<table ", "<table border='1' style='border-collapse:collapse;' cellpadding='10' ", $content);

            $subject = str_replace("|||AR_NAME|||", $lesson_name, $subject);
            $subject = str_replace("|||LESSION_ID|||", $lesson_sent_id, $subject);
        }
        $content = $this->completeContentTemplate($content,$form,$email_send);
        return array('success' => true, 'subject' => $subject, 'content' => $content);
    }

    public static function findFirstByType($type) {
        return ForexcecTemplateAutoEmail::findFirst([
            'email_type = :type:',
            'bind' => ['type' => $type]
        ]);
    }
    public static function getMaxDaySendEmail($form) {
        $email = self::getMaxEmailByForm($form);
        return $email ? $email->getEmailDaySend() : 0;
    }
    public static function getMaxIdByForm($form) {
        $email = self::getMaxEmailByForm($form);
        return $email ? $email->getEmailId() : 0;
    }
    public static function getMaxEmailByForm($form) {
        return ForexcecTemplateAutoEmail::findFirst([
            'email_status = "Y" AND email_form = :form:',
            'bind' => ['form' => $form],
            'order' => 'email_day_send DESC'
        ]);
    }
    public static function getSubjectById($id) {
        $model = self::findFirstById($id);
        return $model ? $model->getEmailSubject() : "";
    }
    public static function findForTest() {
        return ForexcecTemplateAutoEmail::find([
            'email_type != "EMAIL_HEADER" AND email_type != "EMAIL_FOOTER" AND email_type != "FOREXCEC_LESSON"',
            'order' => "email_form DESC"
        ]);
    }

}