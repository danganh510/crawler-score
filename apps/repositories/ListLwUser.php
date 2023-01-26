<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecListLwUser;
use Phalcon\Mvc\User\Component;

class ListLwUser extends Component
{
    public static function findFirstByEmail($email) {
        return ForexcecListLwUser::findFirst([
           'forexcec_email = :email:',
            'bind' => ['email' => $email]
        ]);
    }
    public static function findFirstById($id) {
        return ForexcecListLwUser::findFirst([
            'forexcec_pub_user_id  = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function getNameByEmail($email) {
        $email = self::findFirstByEmail($email);
        return $email ? $email->getForexcecRealName() : "";
    }
    public static function checkEmailAndAcount($email) {
        $model = self::findFirstByEmail($email);
        if ($model) {
            return ($model->getForexcecAccount() == "" ) ? false : true;
        } else {
            return false;
        }
    }
}



