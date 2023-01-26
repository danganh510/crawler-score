<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecGlossaryLang;
use Phalcon\Mvc\User\Component;

class GlossaryLang extends Component
{
    /**
     *
     * @param integer $id
     * @param integer $lang_code
     * @return ForexcecGlossaryLang
     */
    public static  function findFirstByIdAndLang($id,$lang_code){
        return ForexcecGlossaryLang::findFirst(array (
            "glossary_id = :ID: AND glossary_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code )));
    }
    public static function deleteById($id)
    {
        ForexcecGlossaryLang::findById($id)->delete();
    }
}

