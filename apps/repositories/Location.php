<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecLocation;
use Phalcon\Mvc\User\Component;
use GlobalVariable;
use Phalcon\Di;

/**
 * @property \GlobalVariable globalVariable
 * @property \My my
 */
class Location extends Component
{
    const FOLDER_LOCATION = "/../caches/locations/";
    const FILE_CACHED_LOCATION = "cached_location_header.txt";

    public static function checkCode($country_code, $language_code, $id)
    {
        return ForexcecLocation::findFirst(
            array('location_country_code = :COUNTRY: AND location_lang_code = :LANGUAGE: AND location_id != :ID:',
                'bind' => array('COUNTRY' => $country_code,
                    'LANGUAGE' => $language_code,
                    'ID' => $id),
            ));
    }


    public static function getCache($language)
    {
        $type_language = $language . "/";
        $folder = __DIR__ . self::FOLDER_LOCATION . $type_language;
        $cachedConfigFileName = $folder . self::FILE_CACHED_LOCATION;
        $message = '';
        if (file_exists($cachedConfigFileName)) {
            $message = file_get_contents($cachedConfigFileName);
        }
        return $message;
    }

    public static function findLocationLangByCode($code)
    {
        return ForexcecLocation::find(array(
            'column' => 'location_lang_code',
            'conditions' => 'location_active = "Y" AND location_country_code = :CODE:',
            'bind' => array("CODE" => $code),
            'order' => 'location_order'
        ));
    }

    public static function getComboLocationLangByCode($code, $lang)
    {
        $globalVariable = new GlobalVariable();
        if (strtolower($code) == $globalVariable->global['code']) {
            $string = "<option selected value='" . $globalVariable->defaultLanguage . "'>" . strtoupper($globalVariable->defaultLanguage) . ' - ' . Language::getNameByCode($globalVariable->defaultLanguage) . "</option>";
        } else {
            $list_language = Location::findLocationLangByCode($code);
            $string = "";
            foreach ($list_language as $language) {
                $selected = '';
                if ($language->getLocationLangCode() == $lang) {
                    $selected = 'selected';
                }
                $string .= "<option " . $selected . " value='" . $language->getLocationLangCode() . "'>" . strtoupper($language->getLocationLangCode()) . ' - ' . Language::getNameByCode($language->getLocationLangCode()) . "</option>";
            }
        }

        return $string;
    }

    public static function findLanguageByCountryCode($country_code)
    {
        $modelsManager = Di::getDefault()->get('modelsManager');
        $sql = 'SELECT * FROM \Forexceccom\Models\ForexcecLanguage WHERE language_code IN 
                    (SELECT location_lang_code FROM  \Forexceccom\Models\ForexcecLocation WHERE  location_active= "Y" AND location_country_code = :country_code:)';
        $para['country_code'] = $country_code;
        return $modelsManager->executeQuery($sql, $para);
    }

    public static function arrLanguages($country_code)
    {
        $arr_language = array();
        $arr_language['en'] = "English";
        $list_language = self::findLanguageByCountryCode($country_code);
        foreach ($list_language as $language) {
            if ($language->getLanguageCode() != 'en') {
                $arr_language[$language->getLanguageCode()] = $language->getLanguageName();
            }
        }
        return $arr_language;
    }

    public static function getCountryCodeByLang($itemLang){
        $data = ForexcecLocation::find(array(
            'column' => 'location_country_code',
            'conditions' => 'location_active = "Y" AND location_lang_code = :CODE:',
            'bind' => array("CODE" => $itemLang),
        ));
        return array_column($data->toArray(),'location_country_code');
    }
    public static function findAllLocationLanguageCodes()
    {
        $locations = ForexcecLocation::find(array(
            'columns' => 'location_lang_code',
            'location_active="Y" AND location_lang_code !="en"'
        ));
        return array_unique(array_column($locations->toArray(),'location_lang_code'));
    }
}
