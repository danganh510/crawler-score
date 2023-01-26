<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecArticle;
use Forexceccom\Models\ForexcecArticleLang;
use Phalcon\Mvc\User\Component;

class ArticleLang extends Component
{
    public static function findFirstByIdAndLocationCountryCodeAndLang($id, $location_country_code, $lang_code)
    {
        return ForexcecArticleLang::findFirst(array(
            " article_id = :ID: AND article_location_country_code = :location_country_code: AND article_lang_code = :CODE:",
            'bind' => array(
                'ID' => $id,
                'location_country_code' => $location_country_code,
                'CODE' => $lang_code,
            )));
    }

    public static function deleteById($id)
    {
        ForexcecArticle::findById($id)->delete();
        ForexcecArticleLang::findById($id)->delete();
    }

    public static function deleteByIdAndLocationCountryCode($id, $location_country_code)
    {
        ForexcecArticleLang::findByIdAndLocationCountryCode($id, $location_country_code)->delete();
    }
}

