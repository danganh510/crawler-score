<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecLeadform;
use Forexceccom\Models\ForexcecTemplateAutoEmail;
use Phalcon\Mvc\User\Component;

class Leadform extends Component
{

    public static function saveUpdateTimeByEmail($email,$startTime) {
        $arrLeadformExistEmail = self::findByEmail($email);
        foreach ($arrLeadformExistEmail as $leadform) {
            $leadform->setLeadformUpdateSentEmailTime($startTime);
            $leadform->update();
        }
    }
    public static function findByEmail($email) {
        return ForexcecLeadform::find([
            'leadform_email = :email:',
            'bind' => ['email' => $email]
        ]);
    }
    public static function findNameByEmail($email) {
        $leadform =  ForexcecLeadform::findFirst([
            'leadform_email = :email:',
            'bind' => ['email' => $email],
            'order' => "leadform_insert_time DESC",
        ]);
        return $leadform ? $leadform->getLeadformFirstName()." ".$leadform->getLeadformLastName() : "";
    }
}



