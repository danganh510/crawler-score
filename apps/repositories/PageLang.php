<?php

namespace Forexceccom\Repositories;

use Phalcon\Mvc\User\Component;
use Forexceccom\Models\ForexcecPage;
use Forexceccom\Models\ForexcecPageLang;

class PageLang extends Component
{
    public static function deleteById($id)
    {
        ForexcecPage::findById($id)->delete();
        ForexcecPageLang::findById($id)->delete();
    }

    public static function findFirstByIdAndLocationCountryCodeAndLang($id, $country_code, $lang_code)
    {
        return ForexcecPageLang::findFirst(array(
            "page_id = :ID: AND page_location_country_code = :country_code: AND page_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'country_code' => $country_code,
                'CODE' => $lang_code)));
    }

    public static function deleteByIdAndLocationCountryCode($id, $country_code)
    {
        ForexcecPageLang::findByIdAndLocationCountryCode($id, $country_code)->delete();
    }
    public static function findFirstByIdAndLang($id,$location_country_code, $lang_code)
    {
        return ForexcecPageLang::findFirst(array(
            " page_id = :ID: AND page_location_country_code  = :COUNTRY_CODE: AND page_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'COUNTRY_CODE' => $location_country_code,
                'CODE' => $lang_code)));
    }
}



