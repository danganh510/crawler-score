<?php

namespace Forexceccom\Backend\Controllers;
require_once(__DIR__ . '/../../library/google-cloud-translate/vendor/autoload.php');

use Forexceccom\Google\GoogleTranslate;
use Forexceccom\Models\ForexcecConfig;
use Forexceccom\Models\ForexcecCountry;
use Forexceccom\Models\ForexcecCountryLang;
use Forexceccom\Repositories\Config;
use Forexceccom\Repositories\Language;
use Forexceccom\Repositories\Location;
use Phalcon\Mvc\Model;

class TooltranslatetableController extends ControllerBase
{
    protected $googleTranslate;

    public function indexAction()
    {
        ini_set('max_execution_time', 60);

        $messages = array();
        $data = array('table' => '');

        $total_success_insert = 0;
        $total_error_insert = 0;
        $limit = 1;
        if ($this->request->isAjax() && $this->request->isPost()) {
            $data = array(
                'id' => $this->request->getPost('id'),
                'table' => $this->request->getPost('tableName'),
                'langSuccess' => $this->request->getPost('langSuccess'),
                'status' => 'success',
                'percent' => 0,
                'message' => null,
                'totalLangRun' => $this->request->getPost('totalLangRun'),
                'columnTable' => $this->request->getPost('columnTable'),
                'columnTableLang' => $this->request->getPost('columnTableLang'),
                'action' => $this->request->getPost('action'),
                'seo' => $this->request->getPost('seo'),
                'langExcluded' => $this->request->getPost('langExcluded'),
            );
            if (empty($data['langSuccess'])) {
                $data['langSuccess'] = [];
            }
            if (empty($data['langExcluded'])) {
                $data['langExcluded'] = [];
            }
            if (empty($data['columnTable'])) {
                $data['columnTable'] = [];
            }
            if (empty($data['columnTableLang'])) {
                $data['columnTableLang'] = [];
            }
            if (empty($data["id"])) {
                $messages["id"] = "This field is required.";
            }
            if (empty($data["table"])) {
                $messages["table"] = "This field is required.";
            }

            if (count($data["action"]) == 0) {
                $messages["action"] = "This field is required.";
            }

            if (count($messages) == 0) {
                try {
                    // create library GoogleTranslate
                    $this->googleTranslate = new GoogleTranslate();

                    $listLang = Language::findAllLanguage();
                    $listLang = array_values(array_diff($listLang, ['en']));
                    $listLang = array_values(array_diff($listLang, $data['langExcluded']));
                    if (count($listLang) == 0) {
                        $data['status'] = 'error';
                        $data['message']['listLang'] = "The language list cannot be empty";
                        $this->returnResponse($data);
                    }

                    $strModelLang = 'Forexceccom\Models\\' . $data["table"];
                    $strModel = substr($strModelLang, 0, -4);

                    if (self::checkTableSEO($data['table'])) {
                        $data = self::handlingTableSEO($data, $listLang);
                        $this->returnResponse($data);
                    } else if (strpos($strModelLang, 'ForexcecConfig')) {
                        $data = self::handlingTableSpecial($data, $listLang);
                        $this->returnResponse($data);
                    } else if (strpos($strModelLang, 'ForexcecCountryLang')) {
                        $data = self::handlingTableSpecial($data, $listLang);
                        $this->returnResponse($data);
                    } else {
                        $columnModelLang = array_keys((new $strModelLang)->toArray());
                        $columnModel = array_keys((new $strModel)->toArray());
                        $tableName = (new $strModelLang)->getSource();
                        $columnId = reset($columnModel);
                        $columnLang = '';
                        $columnActive = '';

                        foreach ($columnModelLang as $column) {
                            if (strpos($column, 'lang_code')) {
                                $columnLang = $column;
                            }
                        }

                        foreach ($columnModel as $column) {
                            if (strpos($column, '_active')) {
                                $columnActive = $column;
                            }
                        }
                        $strSqlColumnActive = !empty($columnActive) ? " AND $columnActive = 'Y'" : '';

                        /**
                         * @var $strModel Model
                         * @var $strModelLang Model
                         */
                        $dataTable = $strModel::findFirst(array(
                            "$columnId = :ID: " . $strSqlColumnActive,
                            "bind" => ["ID" => $data['id']]
                        ));

                        if (!$dataTable) {
                            $data['status'] = "error";
                            $data['message']['id'] = "Invalid ID";
                            $this->returnResponse($data);
                        }

                        if (empty($data['langSuccess'])) {
                            $data['totalLangRun'] = $listLang;
                        }
                        foreach ($listLang as $itemLang) {
                            if ($total_success_insert < $limit && !in_array($itemLang, $data['langSuccess'])) {
                                $data['status'] = 'processing';

                                $this->googleTranslate->setGlossaryId('en', $itemLang);

                                $new_model = $strModelLang::findFirst(array(
                                    "$columnId = :ID: AND $columnLang = :LANG:",
                                    "bind" => ["ID" => $data['id'], "LANG" => $itemLang]
                                ));
                                if (!$new_model) {
                                    if (in_array('insert', $data['action'])) {
                                        $new_model = new $strModelLang();
                                        foreach ($columnModelLang as $item_column_lang) {
                                            if (strpos($item_column_lang, '_id')) {
                                                $new_model->$item_column_lang = $dataTable->$item_column_lang;
                                            } else if (strpos($item_column_lang, '_lang_code')) {
                                                $new_model->$item_column_lang = $itemLang;
                                            } else {
                                                $dataTranslate = $dataTable->$item_column_lang;
                                                if (!strpos($item_column_lang, '_id') && !empty($dataTranslate)) {
                                                    $response_translate = $this->translateAPI($dataTranslate, $itemLang, $tableName, $data['id']);
                                                    if ($response_translate['status']) {
                                                        $new_model->$item_column_lang = $response_translate['content'];
                                                    } else {
                                                        $data['status'] = "error";
                                                        $data['message']['api'] = $response_translate['content'];
                                                        $this->returnResponse($data);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        array_push($data['langSuccess'], $itemLang);
                                        continue;
                                    }
                                } else {
                                    if (in_array('update', $data['action'])) {
                                        foreach ($data['columnTableLang'] as $item_column_lang) {
                                            if (strpos($item_column_lang, '_id')) {
                                                $new_model->$item_column_lang = $dataTable->$item_column_lang;
                                            } else if (strpos($item_column_lang, '_lang_code')) {
                                                $new_model->$item_column_lang = $itemLang;
                                            } else {
                                                $dataTranslate = $dataTable->$item_column_lang;
                                                if (!strpos($item_column_lang, '_id') && !empty($dataTranslate) && $item_column_lang != 'country_iso_alpha2') {
                                                    $response_translate = $this->translateAPI($dataTranslate, $itemLang, $tableName, $data['id']);
                                                    if ($response_translate['status']) {
                                                        $new_model->$item_column_lang = $response_translate['content'];
                                                    } else {
                                                        $data['status'] = "error";
                                                        $data['message']['api'] = $response_translate['content'];
                                                        $this->returnResponse($data);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($new_model->save()) {
                                    array_push($data['langSuccess'], $itemLang);
                                    $total_success_insert++;
                                } else {
                                    continue;
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                $data['status'] = "error";
                $data['message'] = $messages;
                $this->returnResponse($data);
            }
            if ($data['totalLangRun'] == 0) {
                $data['percent'] = 100;
            } else {
                $totalLangRun = count($data['totalLangRun']);
                $data['percent'] = round((count($data['langSuccess']) * 100 / $totalLangRun), 2);
            }

            $this->returnResponse($data);
        }
        $slTable = $this->getTableComboBox($data['table']);
        $slLang = Language::findAll();
        $this->view->setVars(array(
            'fromData' => $data,
            'slTable' => $slTable,
            'slLang' => $slLang,

        ));
    }

    /**
     * @param array $data
     * @param array $listLang
     * @return array
     */
    function handlingTableSpecial($data, $listLang)
    {
        $totalSuccessInsert = 0;
        $limit = 1;
        switch ($data["table"]) {
            case 'ForexcecConfig':

                $dataConfigEnByKey = ForexcecConfig::findFirst(array(
                    "config_key = :key: AND config_language = :language:",
                    'bind' => array('key' => $data['id'], 'language' => 'en')
                ));
                if (!$dataConfigEnByKey) {
                    $data['status'] = "error";
                    $data['message']['id'] = "Invalid ID";
                    $this->returnResponse($data);
                }

                if (empty($data['langSuccess'])) {
                    $data['totalLangRun'] = $listLang;
                }
                foreach ($listLang as $itemLang) {
                    if ($totalSuccessInsert < $limit && !in_array($itemLang, $data['langSuccess'])) {
                        $data['status'] = 'processing';

                        $this->googleTranslate->setGlossaryId('en', $itemLang);

                        if (!empty($dataConfigEnByKey->getConfigContent())) {
                            $response_translate = $this->translateAPI($dataConfigEnByKey->getConfigContent(), $itemLang, 'forexcec_config', $data['id']);
                            if ($response_translate['status']) {

                                $new_model = Config::findByLanguage($data['id'], $itemLang);
                                if (!$new_model) {
                                    $new_model = new ForexcecConfig();
                                    $new_model->setConfigKey($dataConfigEnByKey->getConfigKey());
                                    $new_model->setConfigLanguage($itemLang);
                                }
                                $new_model->setConfigContent($response_translate['content']);
                                if ($new_model->save()) {
                                    array_push($data['langSuccess'], $itemLang);
                                    $totalSuccessInsert++;
                                }
                            } else {
                                $data['status'] = "error";
                                $data['message']['api'] = $response_translate['content'];
                                $this->returnResponse($data);
                            }
                        }
                    }
                }
                break;
            case 'ForexcecCountryLang':
                $dataCountryEnByID = ForexcecCountry::findFirst(array(
                    "country_id = :ID:",
                    'bind' => array('ID' => $data['id'])
                ));

                if (!$dataCountryEnByID) {
                    $data['status'] = "error";
                    $data['message']['id'] = "Invalid ID";
                    $this->returnResponse($data);
                }

                if (empty($data['langSuccess'])) {
                    $data['totalLangRun'] = $listLang;
                }

                foreach ($listLang as $itemLang) {
                    if ($totalSuccessInsert < $limit && !in_array($itemLang, $data['langSuccess'])) {
                        $data['status'] = 'processing';

                        $this->googleTranslate->setGlossaryId('en', $itemLang);

                        $newCountryLang = ForexcecCountryLang::findFirst(array(
                            "country_code = :CODE: AND country_lang_code = :LANG:",
                            "bind" => ["CODE" => $dataCountryEnByID->getCountryCode(), "LANG" => $itemLang]
                        ));
                        if (!$newCountryLang) {
                            $newCountryLang = new ForexcecCountryLang();
                            $newCountryLang->setCountryCode($dataCountryEnByID->getCountryCode());
                            $newCountryLang->setCountryLangCode($itemLang);
                        }
                        if (!empty($dataCountryEnByID->getCountryName())) {
                            $response_translate = $this->translateAPI($dataCountryEnByID->getCountryName(), $itemLang, 'forexcec_country', $data['id']);
                            if ($response_translate['status']) {
                                $newCountryLang->setCountryName($response_translate['content']);
                            } else {
                                $data['status'] = "error";
                                $data['message']['api'] = $response_translate['content'];
                                $this->returnResponse($data);
                            }
                            if (!empty($dataCountryEnByID->getCountryNationality())) {
                                $response_translate = $this->translateAPI($dataCountryEnByID->getCountryNationality(), $itemLang,'forexcec_country', $data['id']);
                                if ($response_translate['status']) {
                                    $newCountryLang->setCountryNationality($response_translate['content']);
                                }else {
                                    $data['status'] = "error";
                                    $data['message']['api'] = $response_translate['content'];
                                    $this->returnResponse($data);
                                }
                            }
                            if ($newCountryLang->save()) {
                                array_push($data['langSuccess'], $itemLang);
                                $totalSuccessInsert++;
                            }
                        }
                    }
                }
                break;
            default :
                break;
        }
        if (count($data['totalLangRun']) == 0) {
            $data['percent'] = 100;
        } else {
            $totalLangRun = count($data['totalLangRun']);
            $data['percent'] = round((count($data['langSuccess']) * 100 / $totalLangRun), 2);
        }
        return $data;
    }

    /**
     * @param array $data
     * @param array $listLang
     * @return array
     */
    function handlingTableSEO($data, $listLang)
    {

        $total_success_insert = 0;
        $limit = 1;

        $strModelLang = self::checkTableSEO($data['table'], true);
        $strModel = substr($strModelLang, 0, -4);

        $columnModelLang = array_keys((new $strModelLang)->toArray());
        $columnModel = array_keys((new $strModel)->toArray());
        $tableName = (new $strModelLang)->getSource();
        $columnId = reset($columnModel);
        $columnLang = '';
        $columnActive = '';
        $columnLocationCountryCode = '';
        $columnSeo = '';

        foreach ($columnModelLang as $column) {
            if (strpos($column, 'lang_code')) {
                $columnLang = $column;
            }
        }

        foreach ($columnModel as $column) {
            if (strpos($column, '_active')) {
                $columnActive = $column;
            }
            if (strpos($column, '_location_country_code')) {
                $columnLocationCountryCode = $column;
            }
            if (strpos($column, '_seo')) {
                $columnSeo = $column;
            }
        }

        $strSqlColumnActive = !empty($columnActive) ? " AND $columnActive = 'Y'" : '';
        /**
         * @var $strModel Model
         * @var $strModelLang Model
         */
        $dataTable = $strModel::findFirst(array(
            "$columnId = :ID: AND $columnLocationCountryCode = 'gx' " . $strSqlColumnActive,
            "bind" => ["ID" => $data['id']]
        ));

        if (!$dataTable) {
            $data['status'] = "error";
            $data['message']['id'] = "Invalid ID";
            $this->returnResponse($data);
        }

        if (empty($data['langSuccess'])) {
            $data['totalLangRun'] = $listLang;
        }
        foreach ($listLang as $itemLang) {
            if ($total_success_insert < $limit && !in_array($itemLang, $data['langSuccess'])) {
                $data['status'] = 'processing';

                $this->googleTranslate->setGlossaryId('en', $itemLang);

                $dataTranslateInsert = [];
                $dataTranslateInsert = [];

                if (in_array('insert', $data['action'])) {
                    foreach ($columnModelLang as $itemColumnLang) {
                        if (!in_array($itemColumnLang, [$columnId, $columnLocationCountryCode, $columnLang, $columnActive, $columnSeo])) {
                            $response_translate = $this->translateAPI($dataTable->$itemColumnLang, $itemLang,$tableName,$data['id']);
                            if ($response_translate['status']) {
                                $dataTranslateInsert[$itemColumnLang] = $response_translate['content'];
                            } else {
                                $data['status'] = "error";
                                $data['message']['api'] = $response_translate['content'];
                                $this->returnResponse($data);
                            }
                        }
                    }

                }

                if (in_array('update', $data['action']) ) {
                    foreach ($data['columnTableLang'] as $itemColumnLang) {
                        if (!in_array($itemColumnLang, [$columnId, $columnLocationCountryCode, $columnLang, $columnActive, $columnSeo])) {
                            $response_translate = $this->translateAPI($dataTable->$itemColumnLang, $itemLang,$tableName,$data['id']);
                            if ($response_translate['status']) {
                                $dataTranslateUpdate[$itemColumnLang] = $response_translate['content'];
                            } else {
                                $data['status'] = "error";
                                $data['message']['api'] = $response_translate['content'];
                                $this->returnResponse($data);
                            }
                        }
                    }
                }

                if (count($dataTranslateInsert) > 0 || $dataTranslateUpdate > 0) {
                    $locationByLang = Location::getCountryCodeByLang($itemLang);
                    foreach ($locationByLang as $itemLocation) {

                        /* ------- Table LANG ------- */
                        $newRecordLang = $strModelLang::findFirst(array(
                            "$columnId = :ID: AND $columnLocationCountryCode = :LOCATION: AND $columnLang =:LANG:",
                            "bind" => ["ID" => $data['id'], "LOCATION" => $itemLocation, "LANG" => $itemLang]
                        ));
                        if (!$newRecordLang) {
                            if (in_array('insert', $data['action'])) {
                                $newRecordLang = new $strModelLang();
                                $newRecordLang->$columnId = $data['id'];
                                $newRecordLang->$columnLocationCountryCode = $itemLocation;
                                $newRecordLang->$columnLang = $itemLang;

                                foreach ($dataTranslateInsert as $key => $value) {
                                    $newRecordLang->$key = $value;
                                }
                                $newRecordLang->save();

                            }
                        } else {
                            if (in_array('update', $data['action'])) {
                                if ($data['seo'] == 'Y') {
                                    if ($newRecordLang->$columnSeo != 'Y') {
                                        foreach ($dataTranslateUpdate as $key => $value) {
                                            $newRecordLang->$key = $value;
                                        }
                                        $newRecordLang->save();
                                    }
                                } else {
                                    foreach ($dataTranslateUpdate as $key => $value) {
                                        $newRecordLang->$key = $value;
                                    }
                                    $newRecordLang->save();
                                }
                            }
                        }
                    }
                    array_push($data['langSuccess'], $itemLang);
                    $total_success_insert++;
                }
            }
        }
        if (count($data['totalLangRun']) == 0) {
            $data['percent'] = 100;
        } else {
            $totalLangRun = count($data['totalLangRun']);
            $data['percent'] = round((count($data['langSuccess']) * 100 / $totalLangRun), 2);
        }
        if (empty($data['columnTableLang'])) {
            $data['status'] = 'success';
            $data['percent'] = 100;
        }
        if ($data['status'] == 'success') {
            $locationEn = Location::getCountryCodeByLang('en');
            foreach ($locationEn as $itemLocation) {
                /* ------- Table EN ------- */
                $newRecordEn = $strModel::findFirst(array(
                    "$columnId = :ID: AND $columnLocationCountryCode = :LOCATION: " . $strSqlColumnActive,
                    "bind" => ["ID" => $data['id'], "LOCATION" => $itemLocation]
                ));
                if (!$newRecordEn) {
                    /* ---- Insert record new ---- */
                    if (in_array('insert', $data['action'])) {
                        $newRecordEn = new $strModel();
                        $newRecordEn->$columnId = $data['id'];
                        $newRecordEn->$columnLocationCountryCode = $itemLocation;
                        if (!empty($columnActive)) $newRecordEn->$columnActive = $dataTable->$columnActive;
                        foreach ($columnModel as $itemColumn) {
                            if (!in_array($itemColumn, [$columnId, $columnLocationCountryCode, $columnActive, $columnSeo])) {
                                $newRecordEn->$itemColumn = $dataTable->$itemColumn;
                            }
                        }
                        $newRecordEn->save();
                    }
                } else {
                    /* ---- Update by column selected ---- */
                    if (in_array('update', $data['action'])) {
                        if ($data['seo'] == 'Y') {
                            if ($newRecordEn->$columnSeo != 'Y') {
                                foreach ($data['columnTable'] as $itemColumn) {
                                    if (!in_array($itemColumn, [$columnId, $columnLocationCountryCode, $columnActive, $columnSeo])) {
                                        $newRecordEn->$itemColumn = $dataTable->$itemColumn;
                                    }
                                }
                                $newRecordEn->save();
                            }
                        } else {
                            foreach ($data['columnTable'] as $itemColumn) {
                                if (!in_array($itemColumn, [$columnId, $columnLocationCountryCode, $columnActive, $columnSeo])) {
                                    $newRecordEn->$itemColumn = $dataTable->$itemColumn;
                                }
                            }
                            $newRecordEn->save();
                        }

                    }
                }
            }
        }
        if ($data['status'] == 'success' && $data['seo'] == 'Y') {
            $recordEnNotUpdate = $strModel::find(array(
                "$columnId = :ID: AND $columnSeo = 'Y'",
                "bind" => ["ID" => $data['id']]
            ));
            $recordLangNotUpdate = $strModelLang::find(array(
                "$columnId = :ID: AND $columnSeo = 'Y'",
                "bind" => ["ID" => $data['id']]
            ));
            // $urlByTable = self::getURLbyTable($data['table']);
            $listURLEn = '';
            $listURLLang = '';
//            foreach ($recordEnNotUpdate as $item) {
//                $link = URL_SITE . '/' . $urlByTable . '?id=' . $item->$columnId . '&slcLocationCountry=' . strtoupper($item->$columnLocationCountryCode) . '&slcLang=en';
//                $listURLEn .= "<p><a target='_blank' href='$link'> $link</a></p>";
//            }
//            foreach ($recordLangNotUpdate as $item) {
//                $link = URL_SITE . '/' . $urlByTable . '?id=' . $item->$columnId . '&slcLocationCountry=' . strtoupper($item->$columnLocationCountryCode) . '&slcLang=' . $item->$columnLang;
//                $listURLLang .= "<p><a target='_blank' href='$link'> $link</a></p>";
//            }
            $data['listURLEn'] = $listURLEn;
            $data['listURLLang'] = $listURLLang;
        }
        return $data;
    }

    private function returnResponse($data)
    {
        $data_response = json_encode($data);
        die($data_response);

    }

    public function getcolumntableAction()
    {
        $this->view->disable();
        if ($this->request->isAjax()) {
            $data = array(
                'table' => $this->request->getPost('tableName'),
            );
            $strModelLang = 'Forexceccom\Models\\' . $data["table"];
            $strModel = substr($strModelLang, 0, -4);

            $model = new $strModel();
            $modelLang = new $strModelLang();

            /**
             * @var Model $model
             * @var Model $modelLang
             */

            // Get Phalcon\Mvc\Model\Metadata instance
            /* ------------------------- */
            $metadataModel = $model->getModelsMetaData();
            $columnModel = $metadataModel->getAttributes($model);
            $columnModelNotNull = $metadataModel->getNotNullAttributes($model);
            $columnModelNull = array_values(array_diff($columnModel, $columnModelNotNull));
            /* ------------------------- */
            $metadataModelLang = $modelLang->getModelsMetaData();
            $columnModelLang = $metadataModelLang->getAttributes($modelLang);
            $columnModelLangNotNull = $metadataModelLang->getNotNullAttributes($modelLang);
            $columnModelLangNull = array_values(array_diff($columnModelLang, $columnModelLangNotNull));
            /* ------------------------- */

            $strColumnTable = "";
            $strColumnTableLang = '';
            if (self::checkTableSEO($data['table'])) {
                foreach ($columnModelNotNull as $item) {
                    $strColumnTable .= "<div class='role_block'>
                                <div class='well no-box'>
                                    <label class='container_checkbox'> $item
                                        <input type='checkbox' class='form-control check' name='columnTable[]' checked value='$item' />
                                        <span class='checkmark_checkbox'></span>
                                    </label><div class='clearfix'></div>
                                </div>
                            </div>";
                }
                foreach ($columnModelNull as $item) {
                    $strColumnTable .= "<div class='role_block'>
                                <div class='well no-box'>
                                    <label class='container_checkbox'> $item
                                        <input type='checkbox' class='form-control check' name='columnTable[]' checked value='$item' />
                                        <span class='checkmark_checkbox'></span>
                                    </label><div class='clearfix'></div>
                                </div>
                            </div>";
                }
            }
            foreach ($columnModelLangNotNull as $item) {
                $strColumnTableLang .= "<div class='role_block'>
                                <div class='well no-box'>
                                    <label class='container_checkbox'> $item
                                        <input type='checkbox' class='form-control check' name='columnTableLang[]' checked value='$item' />
                                        <span class='checkmark_checkbox'></span>
                                    </label><div class='clearfix'></div>
                                </div>
                            </div>";
            }

            foreach ($columnModelLangNull as $item) {
                $strColumnTableLang .= "<div class='role_block'>
                                <div class='well no-box'>
                                    <label class='container_checkbox'> $item
                                        <input type='checkbox' class='form-control check' name='columnTableLang[]' checked value='$item' />
                                        <span class='checkmark_checkbox'></span>
                                    </label><div class='clearfix'></div>
                                </div>
                            </div>";
            }

            $data = [
                'columnTable' => $strColumnTable,
                'columnTableLang' => $strColumnTableLang,
            ];

            $this->returnResponse($data);
        }
    }

    private function getTableComboBox($table)
    {
        $array_table = array();
        $array_table[] = "ForexcecConfig";
        $directory_frontend = __DIR__ . "/../../models/*Lang.php";
        foreach (glob($directory_frontend) as $controller) {
            $className = basename($controller, '.php');
            array_push($array_table, $className);
        }
        $data = $array_table;

        $output = "<option disabled value=''>-------------------------------------------------------TABLE-------------------------------------------------------</option>";
        $outputSEO = "<option disabled value=''>-------------------------------------------------------TABLE SEO-------------------------------------------------------</option>";
        foreach ($data as $value) {
            $selected = "";
            if ($value == $table) {
                $selected = "selected = 'selected'";
            }
            if (self::checkTableSEO($value)) {
                $outputSEO .= "<option " . $selected . " value='" . $value . "'>" . $value . "</option>";
            } else {
                $output .= "<option " . $selected . " value='" . $value . "'>" . $value . "</option>";
            }
        }
        return $output . $outputSEO;
    }

    private function checkTableSEO($table, $get = false)
    {
        $tableSEOs = $this->globalVariable->tableSEO;
        if ($get) {
            return isset($tableSEOs[$table]) ? $tableSEOs[$table] : '';
        }
        return isset($tableSEOs[$table]);
    }

    public function translateAPI($content, $tran_lang_code,$tableName,$id)
    {
        if (!filter_var($content, FILTER_VALIDATE_URL)) {
            $response_translate_api = $this->googleTranslate->translate($content, $tran_lang_code,'text/html', 'en', $tableName, $id);
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

    /* private function getURLbyTable($table)
     {
         $url = '';
         switch ($table) {
             case 'OccPageLang':
                 $url = 'edit-page';
                 break;
             case 'OccJurisdictionLang':
                 $url = 'edit-jurisdiction';
                 break;
             case 'OccContentArticleLang':
                 $url = 'edit-content-article';
                 break;
             case 'OccContentFaqLang':
                 $url = 'edit-content-faq';
                 break;
             case 'OccContentInsightLang':
                 $url = 'edit-content-insight';
                 break;
             case 'OccContentInsightTypeLang':
                 $url = 'edit-content-insight-type';
                 break;
             case 'OccContentPromotionLang':
                 $url = 'edit-content-promotion';
                 break;
             case 'OccContentServiceLang':
                 $url = 'edit-content-service';
                 break;
             case 'OccContentServiceTypeLang':
                 $url = 'edit-content-service-type';
                 break;
             case 'OccGlossaryLang':
                 $url = 'edit-glossary';
                 break;
             case 'OccCompanyKitLang':
                 $url = 'edit-company-kit';
                 break;
             case 'OccScopeOfServiceLang':
                 $url = 'edit-scope-of-service';
                 break;
             case 'OccContentTypeLang':
                 $url = 'edit-content-type';
                 break;
             default:
                 $url = '';
                 break;
         }
         return $url;
     }*/
}