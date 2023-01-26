<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecOfficeLang;
use Phalcon\Mvc\User\Component;

class OfficeLang extends Component
{   
    public static  function deleteById($id){
        $arr_lang = ForexcecOfficeLang::findById($id);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
    public static  function findFirstByIdAndLang($id,$lang_code){
        return ForexcecOfficeLang::findFirst(array (
            "office_id = :ID: AND office_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                            'CODE' => $lang_code )));
    }
}