<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecBannerLang;
use Phalcon\Mvc\User\Component;

class BannerLang extends Component
{
    public static  function findFirstByIdAndLang($id,$lang_code){
        return ForexcecBannerLang::findFirst(array (
            "banner_id = :ID: AND banner_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code )));
    }

    public static  function deleteById($id){
        $arr_lang = ForexcecBannerLang::findById($id);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
}