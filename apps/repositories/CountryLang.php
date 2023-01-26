<?php

namespace Forexceccom\Repositories;

use Phalcon\Mvc\User\Component;
use Forexceccom\Models\ForexcecCountryLang;

class CountryLang extends Component
{
    public static  function deleteByCode($code){
        $arr_lang = ForexcecCountryLang::findByCode($code);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
    public static function findFirstByCodeAndLang($code, $lang_code)
    {
        return ForexcecCountryLang::findFirst(array(
            "country_code = :country_code: AND country_lang_code = :CODE:",
            'bind' => array('country_code' => $code,'CODE' => $lang_code)));
    }
}



