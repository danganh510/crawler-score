<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecLanguage;
use Forexceccom\Models\ForexcecOffice;
use Forexceccom\Models\ForexcecOfficeLang;
use Forexceccom\Repositories\Language;
use Forexceccom\Repositories\Office;
use Forexceccom\Repositories\OfficeLang;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\Country;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class OfficeController extends ControllerBase
{
    function indexAction()
    {
        $data = $this->getParameter();
        $list_office = $this->modelsManager->executeQuery($data['sql'], $data['para']);

        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $msg_result = array();
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
        }
        $msg_delete = array();
        if ($this->session->has('msg_delete')) {
            $msg_delete = $this->session->get('msg_delete');
            $this->session->remove('msg_delete');
        }
        $paginator = new PaginatorModel(
            [
                'data' => $list_office,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $this->view->setVars(array(
            'office' => $paginator->getPaginate(),
            'msg_result' => $msg_result,
            'msg_delete' => $msg_delete,
        ));
    }

    function createAction()
    {
        $data = array('id' => -1, 'active' => 'Y', 'order' => 1);
        $messages = array();
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'name' => $this->request->getPost("txtName"), array('string', 'trim'),
                'countryCode' => strtoupper($this->request->getPost("slcCountry", array('string', 'trim'))),
                'positionX' => $this->request->getPost("txtPositionX", array('string', 'trim')),
                'positionY' => $this->request->getPost("txtPositionY", array('string', 'trim')),
                'address' => $this->request->getPost("txtAddress", array('string', 'trim')),
                'phone' => $this->request->getPost("txtPhone", array('string', 'trim')),
                'order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'active' => $this->request->getPost("radActive"),
            );

            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
            }
            if (empty($data["countryCode"])) {
                $messages["countryCode"] = "Country Code field is required.";
            }
            if (empty($data["positionX"])) {
                $messages["positionX"] = "Latitude field is required.";
            } elseif (!is_numeric($data["positionX"])) {
                $messages["positionX"] = "Latitude is not valid ";
            }
            if (empty($data["positionY"])) {
                $messages["positionY"] = "Longitude field is required.";
            } elseif (!is_numeric($data["positionY"])) {
                $messages["positionY"] = "Longitude is not valid ";
            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["order"])) {
                $messages["order"] = "Order is not valid ";
            }

            if (count($messages) == 0) {
                $msg_result = array();
                $new_office = new ForexcecOffice();
                $new_office->setOfficeName($data["name"]);
                $new_office->setOfficeCountryCode($data["countryCode"]);
                $new_office->setOfficePositionX($data["positionX"]);
                $new_office->setOfficePositionY($data["positionY"]);
                $new_office->setOfficeOrder($data["order"]);
                $new_office->setOfficeActive($data["active"]);
                $new_office->setOfficeAddress($data["address"]);
                $new_office->setOfficePhone($data["phone"]);
                $result = $new_office->save();

                if ($result === true) {
                    $message = 'Create the office ID: ' . $new_office->getOfficeId() . ' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('forexcec_office' => array($new_office->getOfficeId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $new_office->getOfficeId());
                } else {
                    $message = "We can't store your info now: <br/>";
                    foreach ($new_office->getMessages() as $msg) {
                        $message .= $msg . "<br/>";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-office");
            }
        }
        $messages["status"] = "border-red";
        $select_country = Country::getCountryCombobox(strtoupper($data['countryCode']));
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
            'select_country' => $select_country,
        ]);
    }

    function editAction()
    {
        $office_id = $this->request->get('id');

        $checkID = new \Forexceccom\Utils\Validator();
        if (!$checkID->validInt($office_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $office_model = ForexcecOffice::findFirstById($office_id);
        if (empty($office_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array(
            'office_id' => -1,
            'office_name' => "",
            'office_country_code' => "",
            'office_positionX' => "",
            'office_positionY' => "",
            'office_address' => "",
            'office_phone' => "",
            'office_order' => "",
            'office_active' => "",
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
            $data_old = array();
            if (isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if ($save_mode != ForexcecLanguage::GENERAL) {
                $data_post['office_name'] = $this->request->getPost('txtName', array('string', 'trim'));
                $data_post['office_address'] = $this->request->getPost('txtAddress', array('string', 'trim'));
                if (empty($data_post['office_name'])) {
                    $messages[$save_mode]['office_name'] = 'Name field is required.';
                }
            } else {
                $data_post['office_country_code'] = strtoupper($this->request->getPost('slcCountry', array('string', 'trim')));
                $data_post['office_positionX'] = $this->request->getPost('txtPositionX', array('string', 'trim'));
                $data_post['office_positionY'] = $this->request->getPost('txtPositionY', array('string', 'trim'));
                $data_post['office_phone'] = $this->request->getPost('txtPhone', array('string', 'trim'));
                $data_post['office_order'] = $this->request->getPost('txtOrder');
                $data_post['office_active'] = $this->request->getPost('radActive');
                if (empty($data_post["office_country_code"])) {
                    $messages["office_country_code"] = "Country code field is required.";
                }
                if (empty($data_post["office_positionX"])) {
                    $messages["office_positionX"] = "Latitude field is required.";
                } elseif (!is_numeric($data_post["office_positionX"])) {
                    $messages["office_positionX"] = "Latitude is not valid ";
                }
                if (empty($data_post["office_positionY"])) {
                    $messages["office_positionY"] = "Longitude field is required.";
                } elseif (!is_numeric($data_post["office_positionY"])) {
                    $messages["office_positionY"] = "Longitude is not valid ";
                }
                if (empty($data_post["office_order"])) {
                    $messages["office_order"] = "Order field is required.";
                } elseif (!is_numeric($data_post["office_order"])) {
                    $messages["office_order"] = "Order is not valid ";
                }
            }
            if (empty($messages)) {
                switch ($save_mode) {
                    case ForexcecLanguage::GENERAL:
                        $data_old = array(
                            'office_country_code' => $office_model->getOfficeCountryCode(),
                            'office_positionX' => $office_model->getOfficePositionX(),
                            'office_positionY' => $office_model->getOfficePositionY(),
                            'office_phone' => $office_model->getOfficePhone(),
                            'office_order' => $office_model->getOfficeOrder(),
                            'office_active' => $office_model->getOfficeActive()
                        );
                        $office_model->setOfficeCountryCode($data_post['office_country_code']);
                        $office_model->setOfficePositionX($data_post['office_positionX']);
                        $office_model->setOfficePositionY($data_post['office_positionY']);
                        $office_model->setOfficePhone($data_post['office_phone']);
                        $office_model->setOfficeOrder($data_post['office_order']);
                        $office_model->setOfficeActive($data_post['office_active']);
                        $result = $office_model->update();
                        $info = ForexcecLanguage::GENERAL;
                        $data_new = array(
                            'office_country_code' => $office_model->getOfficeCountryCode(),
                            'office_positionX' => $office_model->getOfficePositionX(),
                            'office_positionY' => $office_model->getOfficePositionY(),
                            'office_phone' => $office_model->getOfficePhone(),
                            'office_order' => $office_model->getOfficeOrder(),
                            'office_active' => $office_model->getOfficeActive()
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'office_name' => $office_model->getOfficeName(),
                            'office_address' => $office_model->getOfficeAddress(),
                        );
                        $office_model->setOfficeName($data_post['office_name']);
                        $office_model->setOfficeAddress($data_post['office_address']);
                        $result = $office_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'office_name' => $office_model->getOfficeName(),
                            'office_address' => $office_model->getOfficeAddress(),
                        );
                        break;
                    default:
                        $office_lang_model = \Forexceccom\Repositories\OfficeLang::findFirstByIdAndLang($office_id, $save_mode);
                        if (!$office_lang_model) {
                            $office_lang_model = new ForexcecOfficeLang();
                            $office_lang_model->setOfficeId($office_id);
                            $office_lang_model->setOfficeLangCode($save_mode);
                        } else {
                            $data_old = $office_lang_model->toArray();
                        }
                        $office_lang_model->setOfficeName($data_post['office_name']);
                        $office_lang_model->setOfficeAddress($data_post['office_address']);
                        $result = $office_lang_model->save();
                        $info = $arr_language[$save_mode];
                        $data_new = $office_lang_model->toArray();
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Office success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('forexcec_office_lang' => array($office_id => array($data_old, $data_new))));
                    $activity = new \Forexceccom\Repositories\Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $office_id);
                } else {
                    $messages = array(
                        'message' => "Update Office fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }

        $office_model = \Forexceccom\Repositories\Office::getByID($office_model->getOfficeId());

        $item = array(
            'office_id' => $office_model->getOfficeId(),
            'office_name' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['office_name'] : $office_model->getOfficeName(),
            'office_address' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['office_address'] : $office_model->getOfficeAddress(),
        );
        $arr_translate[$this->globalVariable->defaultLanguage] = $item;

        $arr_office_lang = ForexcecOfficeLang::findById($office_model->getOfficeId());

        foreach ($arr_office_lang as $office_lang) {
            $item = array(
                'office_id' => $office_lang->getOfficeId(),
                'office_name' => ($save_mode === $office_lang->getOfficeLangCode()) ? $data_post['office_name'] : $office_lang->getOfficeName(),
                'office_address' => ($save_mode === $office_lang->getOfficeLangCode()) ? $data_post['office_address'] : $office_lang->getOfficeAddress(),
            );
            $arr_translate[$office_lang->getOfficeLangCode()] = $item;
        }

        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'office_id' => -1,
                'office_name' => $data_post['office_name'],
                'office_address' => $data_post['office_address'],
            );
            $arr_translate[$save_mode] = $item;
        }

        $formData = array(
            'office_id' => $office_model->getOfficeId(),
            'office_country_code' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['office_country_code'] : $office_model->getOfficeCountryCode(),
            'office_positionX' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['office_positionX'] : $office_model->getOfficePositionX(),
            'office_positionY' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['office_positionY'] : $office_model->getOfficePositionY(),
            'office_address' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['office_address'] : $office_model->getOfficeAddress(),
            'office_phone' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['office_phone'] : $office_model->getOfficePhone(),
            'office_order' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['office_order'] : $office_model->getOfficeOrder(),
            'office_active' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['office_active'] : $office_model->getOfficeActive(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );

        $messages["status"] = "border-red";
        $select_country = \Forexceccom\Repositories\Country::getCountryCombobox(strtoupper($formData['office_country_code']));
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
            'select_country' => $select_country,
        ]);
    }

    function deleteAction()
    {
        $list_office = $this->request->get('item');
        $career_office = array();
        $msg_delete = array('error' => '', 'success' => '');
        if ($list_office) {
            foreach ($list_office as $office_id) {
                $office_model = Office::getByID($office_id);
                if ($office_model) {
                    $old_type_data = array(
                        'office_country_code' => $office_model->getOfficeCountryCode(),
                        'office_positionX' => $office_model->getOfficePositionX(),
                        'office_positionY' => $office_model->getOfficePositionY(),
                        'office_phone' => $office_model->getOfficePhone(),
                        'office_order' => $office_model->getOfficeOrder(),
                        'office_active' => $office_model->getOfficeActive(),
                        'office_name' => $office_model->getOfficeName(),
                        'office_address' => $office_model->getOfficeAddress(),
                    );
                    $new_type_data = array();
                    $career_office[$office_id] = array($old_type_data, $new_type_data);
                    $office_model->delete();
                    OfficeLang::deleteById($office_id);
                }
            }
        }
        if (count($career_office) > 0) {
            // delete success
            $message = 'Delete ' . count($career_office) . ' Office success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('forexcec_office' => $career_office));
            $activity = new \Forexceccom\Repositories\Activity();
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        return $this->response->redirect('/list-office');
    }

    private function getParameter()
    {
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecOffice WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));

        $arrParameter = array();
        $validator = new Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (office_id = :number:)";
                $arrParameter['number'] = $keyword;
            } else {
                $sql .= " AND (office_name like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $sql .= " ORDER BY office_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}