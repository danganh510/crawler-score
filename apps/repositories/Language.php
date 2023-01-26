<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecLanguage;
use Phalcon\Mvc\User\Component;

class Language extends Component
{
    const FOLDER = "/../caches/languages/";
    const FILE_CACHED_LANGUAGE = "cached_language_header.txt";

    public static function checkCode($language_code, $language_id)
    {
        return ForexcecLanguage::findFirst(
            array(
                'language_code = :CODE: AND language_id != :languageid:',
                'bind' => array('CODE' => $language_code, 'languageid' => $language_id),
            ));
    }

    /**
     * @return ForexcecLanguage|ForexcecLanguage[]
     */
    public static function getLanguages()
    {
        return ForexcecLanguage::find(array("language_active = 'Y'",
            "order" => "language_code"));
    }

    public static function getCombo($lang_code)
    {
        $list_language = self::getLanguages();

        $string = "";
        foreach ($list_language as $language) {
            $selected = '';
            if ($language->getLanguageCode() == $lang_code) {
                $selected = 'selected';
            }
            $string .= "<option " . $selected . " value='" . $language->getLanguageCode() . "'>" . strtoupper($language->getLanguageCode()) . ' - ' . $language->getLanguageName() . "</option>";
        }
        return $string;
    }

    public static function getComboReg($lang_code)
    {
        $list_language = ForexcecLanguage::find(array("order" => "language_code"));

        $string = "";
        foreach ($list_language as $language) {
            $selected = '';
            if ($language->getLanguageCode() == $lang_code) {
                $selected = 'selected';
            }
            $string .= "<option " . $selected . " value='" . $language->getLanguageCode() . "'>" . strtoupper($language->getLanguageCode()) . ' - ' . $language->getLanguageName() . "</option>";
        }
        return $string;
    }

    public static function arrLanguages()
    {
        $arr_language = array();
        $arr_language['en'] = "English";
        $languages = self::getLanguages();
        foreach ($languages as $lang) {
            if ($lang->getLanguageCode() != 'en') {
                $arr_language[$lang->getLanguageCode()] = $lang->getLanguageName();
            }
        }
        return $arr_language;
    }

    public static function getNameByCode($language_code)
    {
        $occ_language = ForexcecLanguage::findFirst(array('language_code = :CODE: AND language_active="Y"', 'bind' => array('CODE' => $language_code),));
        return $occ_language ? $occ_language->getLanguageName() : '';
    }

    public static function getNameByCodeReg($language_code)
    {
        $occ_language = ForexcecLanguage::findFirst(array('language_code = :CODE:', 'bind' => array('CODE' => $language_code),));
        return $occ_language ? $occ_language->getLanguageName() : '';
    }

    public static function getCache($location_code)
    {
        $type_language = $location_code . "/";
        $folder = __DIR__ . self::FOLDER . $type_language;
        $cachedConfigFileName = $folder . self::FILE_CACHED_LANGUAGE;
        $message = '';
        if (file_exists($cachedConfigFileName)) {
            $message = file_get_contents($cachedConfigFileName);
        }
        return $message;
    }
    public static function  findAllLanguage(){
        $ar_lang = array();
        $list_language = ForexcecLanguage::find('language_active ="Y"');
        if (sizeof($list_language)>0){
            foreach ($list_language as $item){
                $ar_lang[] = $item->getLanguageCode();
            }
        }
        return $ar_lang;
    }

    public static function findAll()
    {
        return ForexcecLanguage::find(array(
            'columns' => 'language_code,language_name',
            'language_active ="Y" AND language_code != "en"'
        ));
    }
}
