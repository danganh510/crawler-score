<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecLocation;
use Forexceccom\Google\GoogleTranslate;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\Country;
use Forexceccom\Models\ForexcecPageLang;
use Forexceccom\Models\ForexcecType;
use Forexceccom\Models\ForexcecArticle;
use Forexceccom\Repositories\Location;
use Forexceccom\Models\ForexcecActivity;

use Phalcon\Mvc\Model;

class CopydataController extends ControllerBase
{
    protected $googleTranslate;
    public function indexAction()
    {
        $table = $this->request->get('table');
        $from = $this->request->get('from');
        $to = $this->request->get('to');
        $sum_success = 0;
        $error_success = 0;
        $array_table = self::allTableLocation('full');
        $column_location_country_lang = '';
        $column_lang_code = '';
        $CountryCodeLocation = NULL;
        if (isset($array_table[$table])) {
            $column_models = array_keys((new $array_table[$table])->toArray());
            $column_id = reset($column_models);
            foreach ($column_models as $column) {
                if (strpos($column, '_location_country_code')) {
                    $column_location_country_lang = $column;
                    break;
                }
            }

            if (!strpos($table, '_lang')) {
                $CountryCodeLocation = $this->getCountryCodeLocationByLang();
            } else {
                foreach ($column_models as $column) {
                    if (strpos($column, '_lang_code')) {
                        $column_lang_code = $column;
                        break;
                    }
                }
            }

            if (!empty($column_location_country_lang)) {
                if (!empty($from) && !empty($to)) {
                    $datas = $array_table[$table]::find(array(
                        "$column_location_country_lang = 'gx' AND $column_id BETWEEN :FROM: AND :TO:",
                        "bind" => array("FROM" => $from, "TO" => $to)
                    ))->toArray();
                } else {
                    $datas = $array_table[$table]::find(array(
                        "$column_location_country_lang = 'gx'"
                    ))->toArray();
                }
                foreach ($datas as $data) {
                    if (!strpos($table, '_lang')) {
                        $CountryCodeTable = $array_table[$table]::find(array(
                            "columns" => "$column_location_country_lang",
                            "$column_location_country_lang != 'gx' AND $column_id = :ID:",
                            "bind" => array("ID" => $data[$column_id])
                        ))->toArray();
                    } else {
                        $CountryCodeTable = $array_table[$table]::find(array(
                            "columns" => "$column_location_country_lang",
                            "$column_location_country_lang != 'gx' AND $column_id = :ID: AND $column_lang_code = :LANG:",
                            "bind" => array("ID" => $data[$column_id], "LANG" => $data[$column_lang_code])
                        ))->toArray();
                        $CountryCodeLocation = $this->getCountryCodeLocationByLang($data[$column_lang_code]);
                    }
                    $CountryCodeTable = array_column($CountryCodeTable, $column_location_country_lang);
                    foreach ($CountryCodeLocation as $item) {
                        if (!in_array($item, $CountryCodeTable)) {
                            $record_new = new $array_table[$table];
                            foreach ($column_models as $column) {
                                $record_new->$column = $data[$column];
                            }
                            $record_new->$column_location_country_lang = $item;
                            if ($record_new->save()) {
                                $sum_success++;
                            } else {
                                $error_success++;
                            }
                        }

                    }
                }
            }
        }
        echo "Total Insert Success : " . $sum_success;
        echo $error_success > 0 ? "Total Insert Error : " . $error_success : '';
        die();
    }
    public function inserttranslateAction(){
        ini_set('max_execution_time', 120);
        $tableInput = $this->request->get('table');
        $IdInput = $this->request->get('id');
        $arrayLocationSuccess = $this->request->get('locationSuccess');
        $arrayLangTranslated = $this->request->get('langSuccess');
        $statusInsertEn = $this->request->get('statusInsertEn');
        $statusInsertLang = $this->request->get('statusInsertLang');

        if ($arrayLangTranslated == NULL){
            $arrayLangTranslated = [];
        }
        if ($arrayLocationSuccess == NULL){
            $arrayLocationSuccess = [];
        }
        $limit = 0;
        $limitEn = 0;
        $status = 'processing';
        // all table Multi Location
        $arrayTable = $array_table = self::allTableLocation();

        if (!isset($arrayTable[$tableInput])){
            return false;
        }
        if (empty($IdInput)){
            return false;
        }
        // create array column table
        if (isset($arrayTable[$tableInput])) {
            $arrayTableLang = self::allTableLocation('lang');
            // array column model lang
            $arrayColumnModelLang = array_keys((new $arrayTableLang[$tableInput.'_lang'])->toArray());

            $column_models = array_keys((new $arrayTable[$tableInput])->toArray());
            $column_id = reset($column_models);
            foreach ($column_models as $column) {
                if (strpos($column, '_location_country_code') && in_array($column,$arrayColumnModelLang)) {
                    $column_location_country_lang = $column;
                }
                if (strpos($column, '_active')) {
                    $column_active = $column;
                }

            }
        }
        // find record of table by ID input
        if (!empty($column_location_country_lang)) {
            $data = $arrayTable[$tableInput]::findFirst(array(
                "$column_location_country_lang = 'gx' AND $column_id = :ID:",
                "bind" => ['ID' => $IdInput]
            ));
            if ($data) {
                if (!empty($column_active)) {
                    $data->$column_active = 'Y';
                    $data->save();
                }
                $dataGX = $data;
                $data = $data->toArray();
            } else {
                return false;
            }
        }
        // get list language translate
        $modelLang = str_replace('Forexcec\Models\\', '', $arrayTable[$tableInput]) . 'Lang';
        // get all country of forexcec_location
        $CountryCodeLocation = $this->getCountryCodeLocationByLang();
        // insert data multi location
        if ($statusInsertEn == 'false') {
            foreach ($CountryCodeLocation as $item) {
                if ($limitEn > 50) {
                    break;
                }
                if (!in_array($item, $arrayLocationSuccess)) {
                    $record_new = new $arrayTable[$tableInput];
                    foreach ($column_models as $column) {
                        $record_new->$column = $data[$column];
                    }
                    $record_new->$column_location_country_lang = $item;
                    $record_new->save();
              /*      if ($record_new->save()) {
                        $data_new = $record_new->toArray();
                        $message = 'Create Success';
                        $data_log = json_encode(array($tableInput => array($data_new[$column_id] => array([], $data_new))));
                        $activity = new Activity();
                        $activity->logActivity($this->controllerName,$this->actionName,$this->auth['id'],$message,$data_log);
                    }
                    */
                    array_push($arrayLocationSuccess, $item);
                    $limitEn++;
                }

            }
            if (count($arrayLocationSuccess) == count($CountryCodeLocation)) {
                $statusInsertEn = true;
                $statusInsertLang = true;
            }
        }

        $column_lang_code = '';
        if ($statusInsertEn == 'false') {
            $tableInputLang = $arrayTable[$tableInput] . 'Lang';
            $arrayColumnModelLang = array_keys((new $tableInputLang)->toArray());

            foreach ($arrayColumnModelLang as $column) {
                if (strpos($column, '_lang_code')) {
                    $column_lang_code = $column;
                }
            }
            $langCodeNotTranslate = $tableInputLang::find(array(
                "columns" => "DISTINCT $column_lang_code",
                "$column_id = :ID:",
                "bind" => ['ID' => $IdInput]
            ))->toArray();
            $langCodeNotTranslate = array_column($langCodeNotTranslate, $column_lang_code);
            $arrayLangTranslated = array_merge($arrayLangTranslated,$langCodeNotTranslate);
        }
        $arrayLangTranslate = Location::findAllLocationLanguageCodes();
        if (empty($arrayLangTranslate)) {
            die(json_encode(['status' => 'insert success, no location language']));
        }
        if ($statusInsertLang != 'false') {
            foreach ($arrayLangTranslate as $lang) {
                if ($limit > 1) {
                    break;
                }
                if (!in_array($lang, $arrayLangTranslated)) {
                    $dataTranslated = $this->translateRecord($tableInput, $data, $lang,$IdInput);
                    if (!empty($dataTranslated)) {
                        $countryCodeLocationByLang = self::getCountryCodeLocationByLang($lang);
                        foreach ($countryCodeLocationByLang as $locationCountry) {
                            $new_model_lang = new $dataTranslated['tableLang']();
                            foreach ($dataTranslated['dataTranslated'] as $key => $value) {
                                $new_model_lang->$key = $value;
                            }
                            $new_model_lang->$column_location_country_lang = $locationCountry;
                            $new_model_lang->save();
                       /*     if ($new_model_lang->save()) {
                                $data_new = $new_model_lang->toArray();
                                $message = 'Create Success';
                                $data_log = json_encode(array($tableInput . '_lang' => array($data_new[$column_id] => array([], $data_new))));
                                $activity = new Activity();
                                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                            } */
                        }
                    }
                    array_push($arrayLangTranslated, $lang);
                    $limit++;
                }
            }
        }
        if (count($arrayLangTranslated) == count($arrayLangTranslate)){
            $status = 'success';
        }
        $percent = round((count($arrayLangTranslated) * 100 / count($arrayLangTranslate)), 2);
        $data_response = array(
            'status' => $status,
            'percent' => $percent,
            'id' => $IdInput,
            'table' => $tableInput,
            'langSuccess' => $arrayLangTranslated,
            'statusInsertEn' => $statusInsertEn,
            'statusInsertLang' => $statusInsertLang,
            'locationSuccess' => $arrayLocationSuccess,
        );
        die(json_encode($data_response));
    }
    private function translateRecord($tableInput,$data,$lang,$record_history_id){
        $arrayTableLang = self::allTableLocation('lang');

        $arrayColumnModelLang = array_keys((new $arrayTableLang[$tableInput.'_lang'])->toArray());

        try {
            // create library GoogleTranslate
            require_once(__DIR__ . '/../../library/google-cloud-translate/vendor/autoload.php');
            $this->googleTranslate = new GoogleTranslate();
            $this->googleTranslate->setGlossaryId('en', $lang);
            $dataTranslated = [];
            foreach ($arrayColumnModelLang as $column) {
                if (strpos($column, '_id')) {
                    $dataTranslated[$column] = $data[$column];
                }
                if (strpos($column, '_lang_code')) {
                    $dataTranslated[$column] = $lang;
                }
                if (!strpos($column, '_lang_code') && !strpos($column, '_id') && isset($data[$column]) && !empty($data[$column]) && !strpos($column,"_country_code")) {
                    $response_translate = $this->translateAPI($data[$column], $lang, $tableInput, $record_history_id);
                    if ($response_translate['status']) {
                        $dataTranslated[$column] = $response_translate['content'];
                    } else {
                        die(json_encode(
                            [
                                'status' => 'errorAPI',
                                'message' => $response_translate['content'],
                            ]
                        ));
                    }
                }
            }
            return array(
                'tableLang' => $arrayTableLang[$tableInput . '_lang'],
                'dataTranslated' => $dataTranslated
            );

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function translateAPI($content, $tran_lang_code,$table,$record_history_id)
    {
        if (!filter_var($content, FILTER_VALIDATE_URL)) {
            $response_translate_api = $this->googleTranslate->translate($content, $tran_lang_code,'text/html','en',$table,$record_history_id);
            if ($response_translate_api["status"] == "fail") {
                return array(
                    'status' => false,
                    'content' => $response_translate_api["errorcode"] . " - " . $response_translate_api["errormessage"],
                );
            }
            return array(
                'status' => true,
                'content' => $response_translate_api["data"],
            );
        } else {
            return array(
                'status' => true,
                'content' => $content,
            );
        }
    }

    function getCountryCodeLocationByLang($lang = 'en')
    {
        $data = ForexcecLocation::find(array(
            "columns" => "DISTINCT location_country_code",
            "location_lang_code = :CODE: AND location_active ='Y'",
            "bind" => array("CODE" => $lang)
        ))->toArray();
        return array_column($data, 'location_country_code');
    }


    private function allTableLocation($type = '')
    {
        $arrayTable = array(
            'forexcec_article' => 'Forexceccom\Models\ForexcecArticle',
            'forexcec_type' => 'Forexceccom\Models\ForexcecType',
            'forexcec_page' => 'Forexceccom\Models\ForexcecPage',

        );
        $arrayTableLang = array(
            'forexcec_article_lang' => 'Forexceccom\Models\ForexcecArticleLang',
            'forexcec_type_lang' => 'Forexceccom\Models\ForexcecTypeLang',
            'forexcec_page_lang' => 'Forexceccom\Models\ForexcecPageLang',
        );
        $response = $arrayTable;
        if ($type == 'full') {
            $response = array_merge($arrayTable, $arrayTableLang);
        } elseif ($type == 'lang') {
            $response = $arrayTableLang;
        }
        return $response;
    }
}
