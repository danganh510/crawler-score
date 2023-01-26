<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecTemplateEmail;
use Forexceccom\Models\ForexcecUser;
use Phalcon\Mvc\User\Component;

/**
 * Class EmailTemplate
 * @property \GlobalVariable globalVariable
 * @package Forexceccom\Repositories
 */
class EmailTemplate extends Component {
    const EMAIL = 'EMAIL';
    const PDF = 'PDF';
    const logoUrl = 'frontend/images/logo.svg';
    const logoReceiptUrl = 'frontend/images/logo.png';

    public static function checkKeyword($emailtemplate_type, $emailtemplate_id)
    {
        return ForexcecTemplateEmail::findFirst(
            array (
                'email_type = :type: AND email_id != :emailtemplateid:',
                'bind' => array('type' => $emailtemplate_type, 'emailtemplateid' => $emailtemplate_id),
            ));
    }

    public static function isTypePdf($type)
    {
        return $type == self::PDF;
    }

    public function findByTypeAndLanguage($emailType,$languageCode='en') {
        $email = false;
        if ($languageCode && $languageCode != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT te.*, tel.* FROM \Forexceccom\Models\ForexcecTemplateEmail te
                INNER JOIN \Forexceccom\Models\ForexcecTemplateEmailLang tel
                ON te.email_id = tel.email_id
                WHERE tel.email_lang_code = :lang:
                AND te.email_type = :type:";
            $item = $this->modelsManager->executeQuery($sql, array('lang' => $languageCode, 'type' => $emailType ))->getFirst();
            if ($item) {
                $email = \Phalcon\Mvc\Model::cloneResult(
                    new ForexcecTemplateEmail(),
                    array_merge($item->te->toArray(), $item->tel->toArray())
                );
            }
        }
        else {
            $email = ForexcecTemplateEmail::findFirst(array(
                'email_type = :type:',
                'bind' => array('type' => $emailType),
            ));
        }
        return $email;
    }

    public function completeContentTemplate($content, $type = 'EMAIL', $languageCode='en') {
        $type = (!isset($type) || !is_string($type) || !in_array($type, array(self::EMAIL, self::PDF))) ? self::EMAIL : strtoupper(trim($type));

        if (self::isTypePdf($type)) {
            $headerMsg = '';
            $footerMsg = '';
            /*$header = ForexcecTemplateEmail::findFirst(array(
                'email_type = :email_type: AND email_status = "Y"',
                'bind' => array('email_type' => 'PDF_HEADER')
            ));
            $footer = ForexcecTemplateEmail::findFirst(array(
                'email_type = :email_type: AND email_status = "Y"',
                'bind' => array('email_type' => 'PDF_FOOTER')
            ));*/

            $headerAddress = self::findByTypeAndLanguage('PDF_HEADER_ADDRESS', $languageCode);
            if ($headerAddress)
                $content = str_replace("|||PDF_HEADER_ADDRESS|||", $headerAddress->getEmailContent(), $content);
            else
                $content = str_replace("|||PDF_HEADER_ADDRESS|||", "", $content);

            $footerAddress = self::findByTypeAndLanguage('PDF_FOOTER_ADDRESS', $languageCode);
            if ($footerAddress)
                $content = str_replace("|||PDF_FOOTER_ADDRESS|||", $footerAddress->getEmailContent(), $content);
            else
                $content = str_replace("|||PDF_FOOTER_ADDRESS|||", "", $content);

            $content = str_replace("|||LANG|||", '/'.$languageCode, $content);

            return $headerMsg . $content . $footerMsg;
        }
        else {
            $headerMsg = '';
            $header = self::findByTypeAndLanguage('EMAIL_HEADER', $languageCode);
            if ($header) {
                $headerMsg = $header->getEmailContent();
            }

            $footerMsg = '';
            $footer = self::findByTypeAndLanguage('EMAIL_FOOTER', $languageCode);
            if ($footer) {
                $footerMsg = $footer->getEmailContent();
            }

            $headerMsg = str_replace("|||LOGO_URL|||", $this->url->getStatic(self::logoUrl), $headerMsg);
            $content = str_replace("|||LANG|||", '/'.$languageCode, $content);

            return  $headerMsg . $content . $footerMsg;
        }
    }

    /**
     * @param \Forexceccom\Models\ForexcecUser $user
     * @param $pass
     * @param $languageCode
     * @return array
     */
    public function getEmailNewUser($user, $pass,$languageCode)
    {
        $templateEmail = $this->findByTypeAndLanguage('EMAIL_CREATE_NEW_USER',$languageCode);

        $role = Role::getNameRole($user->getUserRoleId());
        if ($role == '') {
            $role = 'User';
        }
        if (!$templateEmail) return array('success' => false, 'content' => '');
        $user = ForexcecUser::findFirstById($user->getUserId());
        $subject = $templateEmail->getEmailSubject();
        $content = $templateEmail->getEmailContent();
        $content = str_replace(array("|||USER_NAME|||", "|||USER_EMAIL|||", "|||USER_PASSWORD|||", "|||USER_ROLE|||"), array($user->getUserName(), $user->getUserEmail(), $pass, $role), $content);

        $content = $this->completeContentTemplate($content, 'EMAIL',$languageCode);

        return array('success' => true, 'subject' => $subject, 'content' => $content);
    }

    public function getEmailResetPass($user, $pass,$languageCode)
    {
        $templateEmail = $this->findByTypeAndLanguage('EMAIL_RESET_PASSWORD',$languageCode);

        if (!$templateEmail) return array('success' => false, 'content' => '');
        $user = ForexcecUser::findFirstById($user->getUserId());
        $subject = $templateEmail->getEmailSubject();
        $content = $templateEmail->getEmailContent();
        $content = str_replace(array("|||USER_NAME|||", "|||USER_EMAIL|||", "|||USER_PASSWORD|||"), array($user->getUserName(), $user->getUserEmail(), $pass), $content);

        $content = $this->completeContentTemplate($content, 'EMAIL',$languageCode);

        return array('success' => true, 'subject' => $subject, 'content' => $content);
    }
    
}
