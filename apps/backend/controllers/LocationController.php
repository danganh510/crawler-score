<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecCountry;
use Forexceccom\Models\ForexcecLocation;
use Forexceccom\Repositories\Country;
use Forexceccom\Repositories\Language;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\Location;

class LocationController extends ControllerBase
{
    public function indexAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $country = strtolower($this->request->get('slCountry'));
        $lang_code = strtolower($this->request->get('slLanguage'));
        $sql = "SELECT DISTINCT l.* 
                FROM Forexceccom\Models\ForexcecLocation as l
                LEFT JOIN Forexceccom\Models\ForexcecCountry as c
                ON l.location_country_code = c.country_code
                WHERE 1";
        $arrParameter = array();
        if (!empty($country)) {
            $sql .= " AND (location_country_code = :countryCODE:)";
            $arrParameter['countryCODE'] = $country;
            $this->dispatcher->setParam("country", $country);
        }
        if (!empty($lang_code)) {
            $sql .= " AND (location_lang_code = :langCODE:)";
            $arrParameter['langCODE'] = $lang_code;
            $this->dispatcher->setParam("lang_code", $lang_code);
        }
        $sql .= " ORDER BY c.country_name ASC,location_order ASC";
        $list_location = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(array(
            'data' => $list_location,
            'limit' => 1000,
            'page' => $current_page,
        ));
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        if ($this->session->has('msg_del')) {
            $msg_result = $this->session->get('msg_del');
            $this->session->remove('msg_del');
            $this->view->msg_del = $msg_result;
        }
        $country_combobox = Country::getComboboxByCode(strtoupper($country));
        $lang_combobox = Language::getCombo($lang_code);
        $this->view->slCountry = $country_combobox;
        $this->view->slLanguage = $lang_combobox;
        $this->view->list_location = $paginator->getPaginate();
    }

    public function createAction()
    {
        $data = array('location_id' => -1, 'location_active' => 'Y', 'location_is_public' => 'N', 'location_is_temp' => 'N', 'location_order' =>
            1,);
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'location_country_code' => strtolower($this->request->getPost("slcCountry", array('string', 'trim'))),
                'location_lang_code' => $this->request->getPost("slcLanguage", array('string', 'trim')),
                'location_hotline' => trim($this->request->getPost('txtHotline')),
                'location_mobile_footer_support' => trim($this->request->getPost('txtMobileFooterSupport')),
                'location_footer_social' => trim($this->request->getPost('txtFooterSocial')),
                'location_footer_content' => trim($this->request->getPost('txtFooterContent')),
                'location_schema_contactpoint' => trim($this->request->getPost('txtSchemaContactPoint')),
                'location_schema_social' => trim($this->request->getPost('txtSchemaSocial')),
                'location_alternate_hreflang' => trim($this->request->getPost('txtAlternateHrefLang')),
                'location_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'location_active' => $this->request->getPost("radActive"),
                'location_is_public' => $this->request->getPost("radIsPublic"),
                'location_is_temp' => $this->request->getPost("radIsTemp"),
            );
            if (empty($data["location_country_code"])) {
                $messages["country"] = "Country field is required.";
            }
            if (empty($data["location_lang_code"])) {
                $messages["language"] = "Language field is required.";
            }
            if (!empty($data['location_country_code']) && !empty($data['location_lang_code'])) {
                if (Location::checkCode($data["location_country_code"], $data["location_lang_code"], -1)) {
                    $messages["language"] = "Language is exists";
                }
            }
            if (empty($data['location_order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data["location_order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_location = new ForexcecLocation();
                $new_location->setLocationCountryCode($data["location_country_code"]);
                $new_location->setLocationLangCode($data["location_lang_code"]);
                $new_location->setLocationHotline($data["location_hotline"]);
                $new_location->setLocationMobileFooterSupport($data["location_mobile_footer_support"]);
                $new_location->setLocationFooterSocial($data["location_footer_social"]);
                $new_location->setLocationFooterContent($data["location_footer_content"]);
                $new_location->setLocationSchemaContactpoint($data["location_schema_contactpoint"]);
                $new_location->setLocationSchemaSocial($data["location_schema_social"]);
                $new_location->setLocationAlternateHrefLang($data["location_alternate_hreflang"]);
                $new_location->setLocationOrder($data["location_order"]);
                $new_location->setLocationActive($data["location_active"]);
                $new_location->setLocationIsPublic($data["location_is_public"]);
                $new_location->setLocationIsTemp($data["location_is_public"]);
                $result = $new_location->save();
                $data_log = json_encode(array());
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Location Success');
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('forexcec_location' => array($new_location->getLocationId() => array($old_data, $new_data))));

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_location->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $new_location->getLocationId());
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-location");
            }
        }
        $select_country = Country::getComboboxByCode(strtoupper($data['location_country_code']));
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_country' => $select_country,
        ]);
    }

    /**
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function editAction()
    {
        $id = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($id)) {
            return $this->response->redirect('notfound');
        }
        $location_model = ForexcecLocation::findFirstById($id);
        if (empty($location_model)) {
            return $this->response->redirect('notfound');
        }
        $model_data = array(
            'location_id' => $location_model->getLocationId(),
            'location_country_code' => $location_model->getLocationCountryCode(),
            'location_lang_code' => $location_model->getLocationLangCode(),
            'location_hotline' => $location_model->getLocationHotline(),
            'location_mobile_footer_support' => $location_model->getLocationMobileFooterSupport(),
            'location_footer_social' => $location_model->getLocationFooterSocial(),
            'location_footer_content' => $location_model->getLocationFooterContent(),
            'location_schema_contactpoint' => $location_model->getLocationSchemaContactpoint(),
            'location_schema_social' => $location_model->getLocationSchemaSocial(),
            'location_alternate_hreflang' => $location_model->getLocationAlternateHrefLang(),
            'location_order' => $location_model->getLocationOrder(),
            'location_active' => $location_model->getLocationActive(),
            'location_is_public' => $location_model->getLocationIsPublic(),
            'location_is_temp' => $location_model->getLocationIsTemp(),
        );
        $input_data = $model_data;
        $messages = array();
        if ($this->request->isPost()) {
            $data = array(
                'location_id' => $id,
                'location_country_code' => strtolower($this->request->getPost("slcCountry", array('string', 'trim'))),
                'location_lang_code' => $this->request->getPost("slcLanguage", array('string', 'trim')),
                'location_hotline' => trim($this->request->getPost('txtHotline')),
                'location_mobile_footer_support' => trim($this->request->getPost('txtMobileFooterSupport')),
                'location_footer_social' => trim($this->request->getPost('txtFooterSocial')),
                'location_footer_content' => trim($this->request->getPost('txtFooterContent')),
                'location_schema_contactpoint' => trim($this->request->getPost('txtSchemaContactPoint')),
                'location_schema_social' => trim($this->request->getPost('txtSchemaSocial')),
                'location_alternate_hreflang' => trim($this->request->getPost('txtAlternateHrefLang')),
                'location_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'location_active' => $this->request->getPost("radActive"),
                'location_is_public' => $this->request->getPost("radIsPublic"),
                'location_is_temp' => $this->request->getPost("radIsTemp"),
            );
            $input_data = $data;
            if (empty($data["location_country_code"])) {
                $messages["country"] = "Country field is required.";
            }
            if (empty($data["location_lang_code"])) {
                $messages["language"] = "Language field is required.";
            }
            if (!empty($data['location_country_code']) && !empty($data['location_lang_code'])) {
                if (Location::checkCode($data["location_country_code"], $data["location_lang_code"], $id)) {
                    $messages["language"] = "Language is exists";
                }
            }
            if (empty($data['location_order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data["location_order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $location_model->setLocationCountryCode($data["location_country_code"]);
                $location_model->setLocationLangCode($data["location_lang_code"]);
                $location_model->setLocationHotline($data["location_hotline"]);
                $location_model->setLocationMobileFooterSupport($data["location_mobile_footer_support"]);
                $location_model->setLocationFooterSocial($data["location_footer_social"]);
                $location_model->setLocationFooterContent($data["location_footer_content"]);
                $location_model->setLocationSchemaContactpoint($data["location_schema_contactpoint"]);
                $location_model->setLocationSchemaSocial($data["location_schema_social"]);
                $location_model->setLocationAlternateHrefLang($data["location_alternate_hreflang"]);
                $location_model->setLocationOrder($data["location_order"]);
                $location_model->setLocationActive($data["location_active"]);
                $location_model->setLocationIsPublic($data["location_is_public"]);
                $location_model->setLocationIsTemp($data["location_is_temp"]);
                $result = $location_model->update();
                $data_log = json_encode(array());
                if ($result === true) {
                    $old_data = $model_data;
                    $new_data = $input_data;
                    $data_log = json_encode(array('forexcec_location' => array($id => array($old_data, $new_data))));
                    $msg_result = array('status' => 'success', 'msg' => 'Edit location Success');
                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($location_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $id);
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-location");
            }
        }
        $select_country = Country::getComboboxByCode(strtoupper($input_data['location_country_code']));
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $input_data,
            'messages' => $messages,
            'select_country' => $select_country,
        ]);
    }

    public function deleteAction()
    {
        $location_checked = $this->request->getPost("item");
        if (!empty($location_checked)) {
            $occ_log = array();
            foreach ($location_checked as $id) {
                $location_item = ForexcecLocation::findFirstById($id);
                if ($location_item) {
                    $msg_result = array();
                    if ($location_item->delete() === false) {
                        $message_delete = 'Can\'t delete the Location ID = ' . $location_item->getLocationId();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = array(
                            'location_id' => $location_item->getLocationId(),
                            'location_country_code' => $location_item->getLocationCountryCode(),
                            'location_lang_code' => $location_item->getLocationLangCode(),
                            'location_hotline' => $location_item->getLocationHotline(),
                            'location_mobile_footer_support' => $location_item->getLocationMobileFooterSupport(),
                            'location_footer_social' => $location_item->getLocationFooterSocial(),
                            'location_footer_content' => $location_item->getLocationFooterContent(),
                            'location_schema_contactpoint' => $location_item->getLocationSchemaContactpoint(),
                            'location_schema_social' => $location_item->getLocationSchemaSocial(),
                            'location_alternate_hreflang' => $location_item->getLocationAlternateHrefLang(),
                            'location_order' => $location_item->getLocationOrder(),
                            'location_active' => $location_item->getLocationActive(),
                            'location_is_public' => $location_item->getLocationIsPublic(),
                            'location_is_temp' => $location_item->getLocationIsTemp(),
                        );
                        $occ_log[$id] = $old_data;
                    }
                }
            }
            if (count($occ_log) > 0) {
                $message_delete = 'Delete ' . count($occ_log) . ' Location successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
                $message = '';
                $data_log = json_encode(array('forexcec_location' => $occ_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect("/list-location");
        }
    }

    public function toolAction()
    {
        $listcountry = ForexcecCountry::find("country_active='Y' AND country_code != 'USD' AND country_code != 'MYL' ");
        $locaton_default = ForexcecLocation::findFirst(
            array('location_country_code = :COUNTRY: AND location_lang_code = :LANGUAGE: ',
                'bind' => array('COUNTRY' => 'fr',
                    'LANGUAGE' => 'en'),
            ));
        foreach ($listcountry as $item) {
            $country_code = strtolower($item->getCountryCode());
            $locaton = ForexcecLocation::findFirst(
                array('location_country_code = :COUNTRY: AND location_lang_code = :LANGUAGE: ',
                    'bind' => array('COUNTRY' => $country_code,
                        'LANGUAGE' => 'en'),
                ));
            if (!$locaton) {
                $new_location = new ForexcecLocation();
                $new_location->setLocationLangCode('en');
                $new_location->setLocationCountryCode($country_code);
                $new_location->setLocationOrder(1);
                $new_location->setLocationActive('Y');
                $new_location->setLocationIsPublic('N');
                $new_location->setLocationAlternateHrefLang($locaton_default->getLocationAlternateHrefLang());
                $new_location->setLocationFooterContent($locaton_default->getLocationFooterContent());
                $new_location->setLocationFooterSocial($locaton_default->getLocationFooterSocial());
                $new_location->setLocationHotline($locaton_default->getLocationHotline());
                $new_location->setLocationMobileFooterSupport($locaton_default->getLocationMobileFooterSupport());
                $new_location->setLocationSchemaContactpoint($locaton_default->getLocationSchemaContactpoint());
                $new_location->setLocationSchemaSocial($locaton_default->getLocationSchemaSocial());
                $new_location->save();
            }
        }
    }

    public function getlangbycodeAction()
    {
        $this->view->disable();
        $countryCode = $this->request->getPost('country_code', array('string', 'trim'));
        $langCode = $this->request->getPost('lang_code', array('string', 'trim'));
        if ($countryCode == 'all') {
            $select_lang = Language::getCombo($langCode);
            $string_json = array("string_json" => $select_lang);
            die(json_encode($string_json));
        }
        if (!empty($countryCode)) {
            $select_lang = Location::getComboLocationLangByCode(strtolower($countryCode), $langCode);
            $string_json = array("string_json" => $select_lang);
            die(json_encode($string_json));
        }
    }
}
