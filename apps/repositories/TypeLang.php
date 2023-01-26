<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecType;
use Phalcon\Mvc\User\Component;
use Forexceccom\Models\ForexcecTypeLang;

class TypeLang extends Component
{
    public static function deleteById($id)
    {
        ForexcecType::findById($id)->delete();
        ForexcecTypeLang::findById($id)->delete();
    }

    public static function deleteByIdAndLocationCountryCode($id, $country_code)
    {
        ForexcecTypeLang::findByIdAndLocationCountryCode($id, $country_code)->delete();
    }

    public static function findFirstByIdAndLang($id, $lang_code, $location_code = 'gx')
    {

        return ForexcecTypeLang::findFirst(array(
            " type_id = :ID: AND type_lang_code = :CODE: AND type_location_country_code=:location_code:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code,
                'location_code' => $location_code)));
    }
}



