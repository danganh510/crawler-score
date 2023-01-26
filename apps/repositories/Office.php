<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecOffice;
use Phalcon\Mvc\User\Component;

class Office extends Component
{
    public static function findFirstById($id)
    {
        return ForexcecOffice::findFirst(array(
            'office_id = :id:',
            'bind' => array('id' => $id)
        ));
    }

    public static function getByID($id)
    {
        return ForexcecOffice::findFirst(array(
            'office_id = :office_id:',
            'bind' => array('office_id' => $id)
        ));
    }
    public  static  function  findFirstByCountryCode($country_code){
        return ForexcecOffice::findFirst(array(
            'office_country_code = :country_code:',
            'bind' => array('country_code' => $country_code)
        ));
    }
}