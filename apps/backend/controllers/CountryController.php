<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecCountry;
use Forexceccom\Models\ForexcecCountryLang;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\AreaGeneral;
use Forexceccom\Repositories\Country;
use Forexceccom\Repositories\Office;
use Forexceccom\Utils\Validator;
use mysql_xdevapi\Exception;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Repositories\Language;
use Forexceccom\Models\ForexcecLanguage;
use Forexceccom\Repositories\CountryLang;

class CountryController extends ControllerBase
{
    public function indexAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $keyword = trim($this->request->get("txtSearch"));
        $corporate = $this->request->get('slcIsCorporate');
        $area_selected = $this->request->get('slcArea');
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecCountry WHERE 1";
        $arrParameter = array();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (country_id = :keyword:)";
            } else {
                $sql .= " AND ((country_name like CONCAT('%',:keyword:,'%')) OR (country_nationality like CONCAT('%',:keyword:,'%')))";
            }
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if (!empty($corporate)) {
            $sql .= " AND (country_is_corporate = :corporate: )";
            $arrParameter['corporate'] = $corporate;
            $this->dispatcher->setParam("slcIsCorporate", $corporate);
        }

        if (!empty($area_selected)) {
            $sql .= " AND (country_area_id = :area_id: )";
            $arrParameter['area_id'] = $area_selected;
            $this->dispatcher->setParam("slcArea", $area_selected);
        }
        $sql .= " ORDER BY country_name ASC";
        $list_country = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(array(
            'data' => $list_country,
            'limit' => 20,
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
        $slcArea = AreaGeneral::getCombobox($area_selected);
        $this->view->setVars(array(
            'list_country' => $paginator->getPaginate(),
            'slcArea' => $slcArea,
        ));
    }

    public function createAction()
    {
        $data = array('country_id' => -1, 'country_active' => 'Y', 'country_order' => 1, 'country_area_id' => '');
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'country_id' => -1,
                'country_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'country_nationality' => $this->request->getPost("txtNationality", array('string', 'trim')),
                'country_code' => $this->request->getPost("txtCode", array('string', 'trim')),
                'country_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'country_active' => $this->request->getPost("radActive"),
                'country_area_id' => $this->request->getPost("slcArea"),
            );
            if (empty($data["country_name"])) {
                $messages["name"] = "Name field is required.";
            } else if (Country::checkCountryName($data['country_name'], '')) {
                $messages["name"] = "Name is exists.";
            }
            if (empty($data["country_nationality"])) {
                $messages["nationality"] = "Nationality field is required.";
            } else if (Country::checkCountryNationality($data['country_nationality'], '')) {
                $messages["nationality"] = "Nationality is exists.";
            }
            if (empty($data["country_code"])) {
                $messages["code"] = "Code field is required.";
            }
            if (empty($data['country_order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data["country_order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $message = '';
                $msg_result = array();
                $new_country = new ForexcecCountry();
                $new_country->setCountryName($data["country_name"]);
                $new_country->setCountryNationality($data["country_nationality"]);
                $new_country->setCountryCode($data["country_code"]);
                $new_country->setCountryOrder($data["country_order"]);
                $new_country->setCountryActive($data["country_active"]);
                $new_country->setCountryAreaId($data["country_area_id"]);
                $result = $new_country->save();
                $data_log = json_encode(array());
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Country Success');
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('ForexcecCountry_country' => array($new_country->getCountryId() => array($old_data, $new_data))));
                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_country->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $new_country->getCountryId());
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-country");
            }
        }
        $select_area = AreaGeneral::getCombobox($data['country_area_id']);
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_area' => $select_area
        ]);
    }

    public function editAction()
    {
        $id = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($id)) {
            $this->response->redirect('notfound');
            return;
        }
        $country_model = ForexcecCountry::findFirstById($id);
        if (empty($country_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array(
            'country_id' => -1,
            'country_name' => '',
            'country_nationality' => '',
            'country_code' => '',
            'country_order' => '',
            'country_active' => '',
            'country_area_id' => ''
        );
        $save_mode = '';
        $lang_current = $this->globalVariable->defaultLanguage;
        $arr_language = Language::arrLanguages();
        if ($this->request->isPost()) {
            if (!isset($_POST['save'])) {
                $this->view->disable();
                $this->response->redirect("notfound");
                return;
            }
            $save_mode = $_POST['save'];
            if (isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if ($save_mode != ForexcecLanguage::GENERAL) {
                $data_post['country_name'] = $this->request->get("txtName", array('string', 'trim'));
                $data_post['country_nationality'] = $this->request->get("txtNationality", array('string', 'trim'));
                if (empty($data_post["country_name"])) {
                    $messages[$save_mode]["name"] = "Name field is required.";
                } else if (Country::checkCountryName($data_post['country_name'], $country_model->getCountryCode())) {
                    $messages[$save_mode]["name"] = "Name is exists.";
                }
                if (empty($data_post["country_nationality"])) {
                    $messages[$save_mode]["nationality"] = "Nationality field is required.";
                } else if (Country::checkCountryNationality($data_post['country_nationality'], $country_model->getCountryCode())) {
                    $messages[$save_mode]["nationality"] = "Nationality is exists.";
                }
            } else {
                $data_post['country_code'] = $this->request->getPost('txtCode', array('string', 'trim'));
                $data_post['country_area_id'] = $this->request->getPost('slcArea', array('string', 'trim'));
                $data_post['country_active'] = $this->request->getPost('radActive');
                $data_post['country_order'] = $this->request->getPost('txtOrder', array('string', 'trim'));
                if (empty($data_post['country_code'])) {
                    $messages["code"] = "Code field is required.";
                }
                if (empty($data_post['country_order'])) {
                    $messages["order"] = "Order field is required.";
                } else if (!is_numeric($data_post["country_order"])) {
                    $messages["order"] = "Order is not valid ";
                }
            }
            if (empty($messages)) {
                switch ($save_mode) {
                    case ForexcecLanguage::GENERAL:
                        $data_old = array(
                            'country_code' => $country_model->getCountryCode(),
                            'country_area_id' => $country_model->getCountryAreaId(),
                            'country_active' => $country_model->getCountryActive(),
                            'country_order' => $country_model->getCountryOrder(),
                        );
                        $country_model->setCountryCode($data_post['country_code']);
                        $country_model->setCountryAreaId($data_post['country_area_id']);
                        $country_model->setCountryActive($data_post['country_active']);
                        $country_model->setCountryOrder($data_post['country_order']);
                        $result = $country_model->update();
                        $info = ForexcecLanguage::GENERAL;
                        $data_new = array(
                            'country_code' => $country_model->getCountryCode(),
                            'country_area_id' => $country_model->getCountryAreaId(),
                            'country_active' => $country_model->getCountryActive(),
                            'country_order' => $country_model->getCountryOrder(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage:
                        $data_old = array(
                            'country_name' => $country_model->getCountryName(),
                            'country_nationality' => $country_model->getCountryNationality(),
                        );
                        $country_model->setCountryName($data_post['country_name']);
                        $country_model->setCountryNationality($data_post['country_nationality']);
                        $result = $country_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'country_name' => $country_model->getCountryName(),
                            'country_nationality' => $country_model->getCountryNationality(),
                        );
                        break;
                    default:
                        $country_lang_model = CountryLang::findFirstByCodeAndLang($country_model->getCountryCode(), $save_mode);
                        if (empty($country_lang_model)) {
                            $country_lang_model = new ForexcecCountryLang();
                            $country_lang_model->setCountryCode($country_model->getCountryCode());
                            $country_lang_model->setCountryLangCode($save_mode);
                        }
                        $data_old = $country_lang_model->toArray();
                        $country_lang_model->setCountryName($data_post['country_name']);
                        $country_lang_model->setCountryNationality($data_post['country_nationality']);
                        $result = $country_lang_model->save();
                        $info = $arr_language[$save_mode];
                        $data_new = $country_lang_model->toArray();
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Country success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('forexcec_country' => array($id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $id);
                } else {
                    $messages = array(
                        'message' => "Update Country fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'country_code' => $country_model->getCountryCode(),
            'country_name' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['country_name'] : $country_model->getCountryName(),
            'country_nationality' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['country_nationality'] : $country_model->getCountryNationality(),
        );

        $arr_translate[$this->globalVariable->defaultLanguage] = $item;
        $arr_country_lang = ForexcecCountryLang::findByCode($country_model->getCountryCode());
        foreach ($arr_country_lang as $country_lang) {
            $item = array(
                'country_code' => '',
                'country_name' => ($save_mode === $country_lang->getCountryLangCode()) ? $data_post['country_name'] : $country_lang->getCountryName(),
                'country_nationality' => ($save_mode === $country_lang->getCountryLangCode()) ? $data_post['country_nationality'] : $country_lang->getCountryNationality(),
            );
            $arr_translate[$country_lang->getCountryLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'country_code' => '',
                'country_name' => $data_post['country_name'],
                'country_nationality' => $data_post['country_nationality'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'country_id' => $country_model->getCountryId(),
            'country_code' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['country_code'] : $country_model->getCountryCode(),
            'country_active' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['country_active'] : $country_model->getCountryActive(),
            'country_order' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['country_order'] : $country_model->getCountryOrder(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $select_area = AreaGeneral::getCombobox($country_model->getCountryAreaId());
        $messages['status'] = 'border-red';
        $this->view->setVars(array(
            'formData' => $formData,
            'messages' => $messages,
            'select_area' => $select_area
        ));
    }


    public function deleteAction()
    {
        $country_checked = $this->request->getPost("item");
        if (!empty($country_checked)) {
            $messages = array('error' => '',
                'success' => '');
            $forexcec_log = array();
            $message = '';
            foreach ($country_checked as $id) {
                $country_item = ForexcecCountry::findFirstById($id);
                if ($country_item) {
                    $message_temp = "Can't delete the Country Name = " . $country_item->getCountryName() . ". Because It's exists in";
                    $table_names = array();
                    $member_model = Office::findFirstByCountryCode($country_item->getCountryCode());
                    if ($member_model) {
                        $table_names[] = " Office";
                    }
                    if (empty($table_names)) {
                        $old_data = $country_item->toArray();
                        $forexcec_log[$id] = $old_data;
                        $country_item->delete();
                        CountryLang::deleteByCode($country_item->getCountryCode());
                    }
                    else {
                        $message_temp .= implode(",", $table_names) . "</br>";
                        $message .= $message_temp;
                    }
                }
            }
            if (!empty($message)) {
                $messages['error'] = $message;
            }
            if (count($forexcec_log)>0) {
                $messages['success'] = 'Delete ' . count($forexcec_log) . ' country successfully.';
                $message_activity = '';
                $data_log = json_encode(array('forexcec_country' => $forexcec_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message_activity, $data_log);
            }
            $this->session->set('msg_del', $messages);
            return $this->response->redirect("/list-country");
        }
    }
}