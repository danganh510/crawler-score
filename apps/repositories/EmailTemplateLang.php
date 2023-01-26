<?php

namespace Forexceccom\Repositories;

use Phalcon\Mvc\User\Component;
use Forexceccom\Models\ForexcecTemplateEmailLang;

class EmailTemplateLang extends Component
{
    public static function deleteById($id)
    {
        $arr_lang = ForexcecTemplateEmailLang::findById($id);
        foreach ($arr_lang as $lang) {
            $lang->delete();
        }
    }

    public static function findFirstByIdAndLang($id, $lang_code)
    {
        return ForexcecTemplateEmailLang::findFirst(array(
            "email_id = :ID: AND email_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code)));
    }
}