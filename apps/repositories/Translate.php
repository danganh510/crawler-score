<?php

namespace Forexceccom\Repositories;

use General\Models\LanguageSupportTranslate;
use Phalcon\Mvc\User\Component;

class Translate extends Component
{
    public static function getCombobox($code){
        $repoLang = new LanguageSupportTranslate();
        $data = $repoLang->getAll();
        $string = "";
        foreach($data as $item){
            $seleted = "";
            if($item->getLanguageCode() == $code) {
                $seleted = "selected='selected'";
            }
            $string.="<option ".$seleted." value='".$item->getLanguageCode()."'>".strtoupper($item->getLanguageCode())." - ".$item->getLanguageName()."</option>";
        }
        return $string;
    }

    public static function tableNotTranslate($array_models){
        $arr = [
            'OccNotificationLang',
            'OccShelfCompanyLang',
        ];
        foreach ($array_models as $key => $item) {
            if (in_array($item,$arr)){
                unset($array_models[$key]);
            }
        }
        return $array_models;

    }
}
