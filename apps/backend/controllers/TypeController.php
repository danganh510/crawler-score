<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecArticle;
use Forexceccom\Repositories\TypeLang;
use Phalcon\Paginator\Adapter\NativeArray;
use Forexceccom\Utils\Validator;
use Forexceccom\Repositories\Type;
use Forexceccom\Repositories\Article;
use Forexceccom\Models\ForexcecType;
use Forexceccom\Models\ForexcecTypeLang;
use Forexceccom\Models\ForexcecLanguage;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\Country;
use Forexceccom\Repositories\Location;


class TypeController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $list_type = $this->modelsManager->executeQuery($data['sql'], $data['para']);
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
        $type = new Type();
        $type_parent_id = isset($data["para"]["type_parent_id"]) ? $data["para"]["type_parent_id"] : 0;
        $select_type = $type->getType("", 0, $type_parent_id);

        $location_country_code = isset($data["para"]["location_country_code"]) ? $data["para"]["location_country_code"] : $this->globalVariable->global['code'];
        $lang_code = isset($data["para"]["lang_code"]) ? $data["para"]["lang_code"] : $this->globalVariable->defaultLanguage;

        $result = array();
        if ($list_type && sizeof($list_type) > 0) {
            if ($lang_code != $this->globalVariable->defaultLanguage) {
                foreach ($list_type as $item) {
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ForexcecType(), array_merge($item->t->toArray(), $item->tl->toArray()));
                }
            } else {
                foreach ($list_type as $item) {
                    $result[] = \Phalcon\Mvc\Model::cloneResult(new ForexcecType(), $item->toArray());
                }
            }
        }
        $paginator = new NativeArray(
            [
                'data' => $result,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $select_location_country = Country::getCountryGlobalComboBox($location_country_code);

        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'select_type' => $select_type,
            'msg_result' => $msg_result,
            'msg_delete' => $msg_delete,
            'select_location_country' => $select_location_country,
        ));
        $replace = trim($this->request->get("txtReplace"));
        $this->dispatcher->setParam("txtReplace", $replace);
        $btn_replace = $this->request->getPost("btnReplace");
        $total_fail = 0;
        $total_success = 0;
        $res = false;
        $keyword = $data['keyword'];
        if (isset($btn_replace)) {
            $checkReplace = $this->dispatcher->getParam('checkReplace');
            if (empty($checkReplace)) {
                $occ_log = array();
                $occ_log['find'] = $keyword;
                $occ_log['replace'] = $replace;
                $occ_log['data'] = array();
                $message_log = '';
                if ($lang_code == $this->globalVariable->defaultLanguage) {
                    $message_log = 'forexcec_type';
                    foreach ($list_type as $item) {

                        $t = 0;
                        $change_field = array();

                        $temp = str_replace($keyword, $replace, $item->getTypeName());
                        if ($temp != $item->getTypeName()) {
                            $t++;
                            $item->setTypeName($temp);
                            array_push($change_field, 'type_name');
                        }

                        $temp = str_replace($keyword, $replace, $item->getTypeTitle());
                        if ($temp != $item->getTypeTitle()) {
                            $t++;
                            $item->setTypeTitle($temp);
                            array_push($change_field, 'type_title');
                        }

                        $temp = str_replace($keyword, $replace, $item->getTypeMetaKeyword());
                        if ($temp != $item->getTypeMetaKeyword()) {
                            $t++;
                            $item->setTypeMetaKeyword($temp);
                            array_push($change_field, 'type_meta_keyword');
                        }

                        $temp = str_replace($keyword, $replace, $item->getTypeMetaDescription());
                        if ($temp != $item->getTypeMetaDescription()) {
                            $t++;
                            $item->setTypeMetaDescription($temp);
                            array_push($change_field, 'type_meta_description');
                        }

                        $res = false;
                        if ($t > 0) {
                            $res = $item->update();
                        }
                        if ($res) {
                            $total_success++;
                            $key_log = 'id: ' . $item->getTypeId() . ', location_country_code: ' . $item->getTypeLocationCountryCode();
                            $data = array(
                                'key' => $key_log,
                                'change' => (count($change_field) > 0) ? implode($change_field, ', ') : '',
                            );
                            array_push($occ_log['data'], $data);
                        } else {
                            $total_fail++;
                        }
                    }
                } else {
                    $message_log = 'forexcec_type_lang';
                    foreach ($list_type as $item) {
                        $t = 0;
                        $change_field = array();

                        $temp = str_replace($keyword, $replace, $item->tl->getTypeName());
                        if ($temp != $item->tl->getTypeName()) {
                            $t++;
                            $item->tl->setTypeName($temp);
                            array_push($change_field, 'type_name');
                        }

                        $temp = str_replace($keyword, $replace, $item->tl->getTypeTitle());
                        if ($temp != $item->tl->getTypeTitle()) {
                            $t++;
                            $item->tl->setTypeTitle($temp);
                            array_push($change_field, 'type_title');
                        }

                        $temp = str_replace($keyword, $replace, $item->tl->getTypeMetaKeyword());
                        if ($temp != $item->tl->getTypeMetaKeyword()) {
                            $t++;
                            $item->tl->setTypeMetaKeyword($temp);
                            array_push($change_field, 'type_meta_keyword');
                        }

                        $temp = str_replace($keyword, $replace, $item->tl->getTypeMetaDescription());
                        if ($temp != $item->tl->getTypeMetaDescription()) {
                            $t++;
                            $item->tl->setTypeMetaDescription($temp);
                            array_push($change_field, 'type_meta_description');
                        }

                        $res = false;
                        if ($t > 0) {
                            $res = $item->tl->update();
                        }
                        if ($res) {
                            $total_success++;
                            $key_log = 'id: ' . $item->tl->getTypeId() . ', lang_code: ' . $item->tl->getTypeLangCode() . ', location_country_code: ' . $item->tl->getTypeLocationCountryCode();

                            $data = array(
                                'key' => $key_log,
                                'change' => (count($change_field) > 0) ? implode($change_field, ', ') : '',
                            );
                            array_push($occ_log['data'], $data);
                        } else {
                            $total_fail++;
                        }
                    }
                }

                $msg_result = array();
                $msg_result['status'] = 'success';
                $msg_result['msg'] = 'Replace success: ' . $total_success . '.';
                $this->session->set('msg_result', $msg_result);

                if (count($occ_log['data']) > 0) {
                    $data_log = json_encode(array('replace ' . $message_log => $occ_log));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, 'replace', $this->auth['id'], $message_log, $data_log);
                }

                return $this->dispatcher->forward(array(
                    'controller' => $this->dispatcher->getControllerName(),
                    'action' => $this->dispatcher->getActionName(),
                    'params' => array(
                        'checkReplace' => TRUE,
                    )
                ));
            }
        }
    }

    public function createAction()
    {
        $data = array(
            'id' => -1,
            'active' => 'Y',
            'parent_id' => 0,
            'order' => 1,
        );
        $messages = array();
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'location_country_code' => $this->globalVariable->global['code'],
                'parent_id' => $this->request->getPost("slcType"),
                'name' => $this->request->getPost("txtName", array('string', 'trim')),
                'title' => $this->request->getPost("txtTitle", array('string', 'trim')),
                'keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'meta_keyword' => $this->request->getPost("txtMetakey", array('string', 'trim')),
                'meta_description' => $this->request->getPost("txtMetades", array('string', 'trim')),
                'meta_image' => $this->request->getPost("txtMetaImage", array('string', 'trim')),
                'order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'active' => $this->request->getPost("radActive"),
                'icon' => $this->request->getPost("txtIcon"),
            );
            $type_repo = new Type();
            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
            }
            if (empty($data["title"])) {
                $messages["title"] = "Title field is required.";
            }
            if (empty($data["meta_keyword"])) {
                $messages["meta_keyword"] = "Meta Keyword field is required.";
            }
            if (empty($data["meta_description"])) {
                $messages["meta_description"] = "Meta description field is required.";
            }
            if (empty($data["keyword"])) {
                $messages["keyword"] = "Keyword field is required.";
            } elseif ($type_repo->checkKeyword($data["keyword"], $data['parent_id'], -1, $this->globalVariable->global['code'])) {
                $messages["keyword"] = "Keyword is exists!";
            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_type = new ForexcecType();
                $new_type->setTypeLocationCountryCode($data["location_country_code"]);
                $new_type->setTypeParentId($data["parent_id"]);
                $new_type->setTypeName($data["name"]);
                $new_type->setTypeTitle($data["title"]);
                $new_type->setTypeKeyword($data["keyword"]);
                $new_type->setTypeMetaKeyword($data["meta_keyword"]);
                $new_type->setTypeMetaDescription($data["meta_description"]);
                $new_type->setTypeMetaImage($data["meta_image"]);
                $new_type->setTypeOrder($data["order"]);
                $new_type->setTypeActive($data["active"]);
                $new_type->setTypeIcon($data["icon"]);
                $result = $new_type->save();
                $message = "We can't store your info now: \n";
                $data_log = json_encode(array());
                if ($result === true) {
                    $message = 'Create the type with ID: ' . $new_type->getTypeId() . ' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('forexcec_type' => array($new_type->getTypeId() => array($old_data, $new_data))));
                } else {
                    foreach ($new_type->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $new_type->getTypeId());
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-type");
            }
        }
        $type = new Type();
        $select_type = $type->getType("", 0, $data["parent_id"]);
        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
        ]);
    }

    public function editAction()
    {
        $location_country_code = $this->request->get('slcLocationCountry');
        $lang_current = $this->request->get('slcLang');
        if ($location_country_code != strtoupper($this->globalVariable->global['code'])) {
            $country_model = Country::findByCode($location_country_code);
            if (empty($country_model)) {
                return $this->response->redirect('notfound');
            }
        }
        $id_type = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($id_type)) {
            $this->response->redirect('notfound');
            return;
        }
        $type_model = ForexcecType::findFirstByIdAndLocationCountryCode($id_type, $location_country_code);
        if (empty($type_model)) {
            $this->response->redirect('notfound');
            return;
        }

        $messages = array();
        $data_post = array(
            'type_id' => -1,
            'type_parent_id' => '',
            'type_name' => '',
            'type_title' => '',
            'type_keyword' => '',
            'type_meta_keyword' => '',
            'type_meta_description' => '',
            'type_meta_image' => '',
            'type_active' => '',
            'type_order' => '',
            'type_icon' => '',
        );
        $save_mode = '';
        $lang_default = $this->globalVariable->defaultLanguage;
        $arr_language = Location::arrLanguages($location_country_code);
        if ($this->request->isPost()) {
            if (!isset($_POST['save'])) {
                $this->view->disable();
                $this->response->redirect("notfound");
                return;
            }
            $save_mode = $_POST['save'];
            $info = "";
            $result = false;
            $data_old = array();
            $data_new = array();
            if (isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if ($save_mode != ForexcecLanguage::GENERAL) {
                $data_post['type_name'] = $this->request->get("txtName", array('string', 'trim'));
                $data_post['type_title'] = $this->request->get("txtTitle", array('string', 'trim'));
                $data_post['type_meta_keyword'] = $this->request->get("txtMetaKey", array('string', 'trim'));
                $data_post['type_meta_description'] = $this->request->get("txtMetaDesc", array('string', 'trim'));
                if (empty($data_post['type_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }
                if (empty($data_post['type_title'])) {
                    $messages[$save_mode]['title'] = 'Title field is required.';
                }
                if (empty($data_post['type_meta_keyword'])) {
                    $messages[$save_mode]['meta_keyword'] = 'Meta keyword field is required.';
                }
                if (empty($data_post['type_meta_description'])) {
                    $messages[$save_mode]['meta_description'] = 'Meta description field is required.';
                }
            } else {

                $data_post['type_parent_id'] = $this->request->getPost('slcType');
                $data_post['type_keyword'] = $this->request->getPost('txtKeyword', array('string', 'trim'));
                $data_post['type_meta_image'] = $this->request->getPost("txtMetaImage", array('string', 'trim'));
                $data_post['type_active'] = $this->request->getPost('radActive');
                $data_post['type_order'] = $this->request->getPost('txtOrder', array('string', 'trim'));
                $data_post['type_icon'] = $this->request->getPost('txtIcon', array('string', 'trim'));
                if (empty($data_post['type_keyword'])) {
                    $messages['keyword'] = 'Keyword field is required.';
                } else {
                    if (Type::checkKeyword($data_post['type_keyword'], $data_post['type_parent_id'], $id_type, $location_country_code)) {
                        $messages['keyword'] = 'Keyword is exists.';
                    }
                }
                if (empty($data_post['type_order'])) {
                    $messages['order'] = 'Order field is required.';
                } else if (!is_numeric($data_post['type_order'])) {
                    $messages['order'] = 'Order is not valid.';
                }

            }

            if (empty($messages)) {

                switch ($save_mode) {

                    case ForexcecLanguage::GENERAL:
                        $data_old = array(
                            'type_parent_id' => $type_model->getTypeParentId(),
                            'type_keyword' => $type_model->getTypeKeyword(),
                            'type_meta_image' => $type_model->getTypeMetaImage(),
                            'type_order' => $type_model->getTypeOrder(),
                            'type_active' => $type_model->getTypeActive(),
                            'type_icon' => $type_model->getTypeOrder(),
                        );
                        $type_model->setTypeParentId($data_post['type_parent_id']);
                        $type_model->setTypeKeyword($data_post['type_keyword']);
                        $type_model->setTypeMetaImage($data_post['type_meta_image']);
                        $type_model->setTypeOrder($data_post['type_order']);
                        $type_model->setTypeActive($data_post['type_active']);
                        $type_model->setTypeIcon($data_post['type_icon']);
                        $result = $type_model->update();
                        $info = ForexcecLanguage::GENERAL;
                        $data_new = array(
                            'type_parent_id' => $type_model->getTypeParentId(),
                            'type_keyword' => $type_model->getTypeKeyword(),
                            'type_meta_image' => $type_model->getTypeMetaImage(),
                            'type_order' => $type_model->getTypeOrder(),
                            'type_active' => $type_model->getTypeActive(),
                            'type_icon' => $type_model->getTypeIcon(),
                        );
                        if ($result) {
                            $active = $type_model->getTypeActive();
                            if ($active == 'Y') {
                                $list_article = Article::getByTypeAndActive($id_type, 'N', $location_country_code);
                            } else {
                                $list_article = Article::getByTypeAndActive($id_type, 'Y', $location_country_code);
                            }
                            foreach ($list_article as $article) {
                                $article->setArticleActive($active);
                                $article->update();
                            }
                        }
                        break;
                    case $this->globalVariable->defaultLanguage:
                        $data_old = array(
                            'type_name' => $type_model->getTypeName(),
                            'type_title' => $type_model->getTypeTitle(),
                            'type_meta_keyword' => $type_model->getTypeMetaKeyword(),
                            'type_meta_description' => $type_model->getTypeMetaDescription(),
                        );
                        $type_model->setTypeName($data_post['type_name']);
                        $type_model->setTypeTitle($data_post['type_title']);
                        $type_model->setTypeMetaKeyword($data_post['type_meta_keyword']);
                        $type_model->setTypeMetaDescription($data_post['type_meta_description']);
                        $result = $type_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'type_name' => $type_model->getTypeName(),
                            'type_title' => $type_model->getTypeTitle(),
                            'type_meta_keyword' => $type_model->getTypeMetaKeyword(),
                            'type_meta_description' => $type_model->getTypeMetaDescription(),
                        );
                        break;
                    default:
                        $content_type_lang = TypeLang::findFirstByIdAndLang($id_type, $save_mode, $location_country_code);
                        if ($content_type_lang) {
                            $data_old = $content_type_lang->toArray();
                        } else {
                            $content_type_lang = new ForexcecTypeLang();
                            $content_type_lang->setTypeId($id_type);
                            $content_type_lang->setTypeLocationCountryCode(strtolower($location_country_code));
                            $content_type_lang->setTypeLangCode($save_mode);
                        }

                        $content_type_lang->setTypeName($data_post['type_name']);
                        $content_type_lang->setTypeTitle($data_post['type_title']);
                        $content_type_lang->setTypeMetaKeyword($data_post['type_meta_keyword']);
                        $content_type_lang->setTypeMetaDescription($data_post['type_meta_description']);
                        $result = $content_type_lang->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'type_lang_code' => $content_type_lang->getTypeLangCode(),
                            'type_name' => $content_type_lang->getTypeName(),
                            'type_title' => $content_type_lang->getTypeTitle(),
                            'type_meta_keyword' => $content_type_lang->getTypeMetaKeyword(),
                            'type_meta_description' => $content_type_lang->getTypeMetaDescription(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Content Type success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('forexcec_type' => array($id_type => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    $messages = array(
                        'message' => "Update Content Type fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'type_id' => $type_model->getTypeId(),
            'type_location_country_code' => $type_model->getTypeLocationCountryCode(),
            'type_name' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['type_name'] : $type_model->getTypeName(),
            'type_title' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['type_title'] : $type_model->getTypeTitle(),
            'type_meta_keyword' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['type_meta_keyword'] : $type_model->getTypeMetaKeyword(),
            'type_meta_description' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['type_meta_description'] : $type_model->getTypeMetaDescription(),
        );

        $arr_translate[$this->globalVariable->defaultLanguage] = $item;
        $arr_type_lang = ForexcecTypeLang::findByIdAndLocationCountryCode($id_type, $location_country_code);
        foreach ($arr_type_lang as $type_lang) {
            $item = array(
                'type_id' => $type_lang->getTypeId(),
                'type_name' => ($save_mode === $type_lang->getTypeLangCode()) ? $data_post['type_name'] : $type_lang->getTypeName(),
                'type_title' => ($save_mode === $type_lang->getTypeLangCode()) ? $data_post['type_title'] : $type_lang->getTypeTitle(),
                'type_meta_keyword' => ($save_mode === $type_lang->getTypeLangCode()) ? $data_post['type_meta_keyword'] : $type_lang->getTypeMetaKeyword(),
                'type_meta_description' => ($save_mode === $type_lang->getTypeLangCode()) ? $data_post['type_meta_description'] : $type_lang->getTypeMetaDescription(),
            );
            $arr_translate[$type_lang->getTypeLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'type_id' => -1,
                'type_name' => $data_post['type_name'],
                'type_title' => $data_post['type_title'],
                'type_meta_keyword' => $data_post['type_meta_keyword'],
                'type_meta_description' => $data_post['type_meta_description'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'type_id' => $type_model->getTypeId(),
            'type_location_country_code' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['type_keyword'] : $type_model->getTypeLocationCountryCode(),
            'type_meta_image' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['type_meta_image'] : $type_model->getTypeMetaImage(),
            'type_keyword' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['type_keyword'] : $type_model->getTypeKeyword(),
            'type_active' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['type_active'] : $type_model->getTypeActive(),
            'type_parent_id' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['type_parent_id'] : $type_model->getTypeParentId(),
            'type_order' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['type_order'] : $type_model->getTypeOrder(),
            'type_icon' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['type_icon'] : $type_model->getTypeIcon(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $type = new Type();
        $select_type = $type->getType("", 0, $formData['type_parent_id']);
        $messages['status'] = 'border-red';
        $select_location_country = Country::getCountryGlobalComboBox($location_country_code);
        $this->view->setVars(array(
            'formData' => $formData,
            'messages' => $messages,
            'select_type' => $select_type,
            'select_location_country' => $select_location_country,
            'location_country_code' => $location_country_code,
        ));
    }

    public function deleteAction()
    {
        $items_checked = $this->request->getPost("item");
        $location_country_code = $this->request->get('slcLocationCountry');
        $lang = $this->request->get("slcLang");

        if ($location_country_code == '') {
            return $this->response->redirect("notfound");
        }

        if ($location_country_code != strtoupper($this->globalVariable->global['code'])) {
            $country_model = Country::findByCode($location_country_code);
            if (empty($country_model)) {
                return $this->response->redirect("notfound");
            }
        }

        if (!empty($items_checked)) {
            $msg_result = array();
            $occ_log = array();
            foreach ($items_checked as $id) {
                if ($lang != $this->globalVariable->defaultLanguage) {
                    $item = TypeLang::findFirstByIdAndLang($id, $lang, $location_country_code);
                } else {
                    $item = ForexcecType::findFirstByIdAndLocationCountryCode($id, $location_country_code);
                }

                if ($item) {
                    if ($item->delete() === false) {
                        $message_delete = 'Can\'t delete the Type Name = ' . $item->getTypeName();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = $item->toArray();
                        $occ_log[$id] = $old_data;
                        if (strtolower($location_country_code) == $this->globalVariable->global['code']) {
                            TypeLang::deleteById($id);
                        }
                        if ($lang == $this->globalVariable->defaultLanguage) {
                            TypeLang::deleteByIdAndLocationCountryCode($id, $location_country_code);
                        }
                    }
                }
            }
            if (count($occ_log) > 0) {
                $message_delete = 'Delete ' . count($occ_log) . ' Type successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
                $message = '';
                $data_log = json_encode(array('occ_content_type' => $occ_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect('/list-type?slcLocationCountry=' . $location_country_code . '&slcLang=' . $lang);
        }
    }

    private function getParameter()
    {
        $arrParameter = array();

        $selectAll = '';
        $location_country_code = trim($this->request->get("slcLocationCountry"));
        if ($location_country_code == 'all') {
            $selectAll = "OR t.type_location_country_code != ''";
        }
        if (empty($location_country_code)) {
            $location_country_code = strtoupper($this->globalVariable->global['code']);
        }
        $this->dispatcher->setParam("slcLocationCountry", $location_country_code);
        $match = trim($this->request->get("radMatch"));
        if ($match == '') {
            $match = 'notmatch';
        }
        $this->dispatcher->setParam("radMatch", $match);

        $lang = $this->request->get("slcLang", array('string', 'trim'));
        $lang_code = !empty($lang) ? $lang : $this->globalVariable->defaultLanguage;
        $this->dispatcher->setParam("slcLang", $lang_code);

        $keyword = trim($this->request->get("txtSearch"));
        $validator = new Validator();
        if ($lang_code === $this->globalVariable->defaultLanguage) {
            $sql = "SELECT t.* FROM Forexceccom\Models\ForexcecType t WHERE 1";
            if (!empty($keyword)) {
                if ($validator->validInt($keyword)) {
                    $sql .= " AND (t.type_id = :number:)";
                    $arrParameter['number'] = $keyword;
                } else {
                    if ($match == 'match') {
                        $sql .= " AND (t.type_name =:keyword: OR t.type_title =:keyword: 
                                       OR t.type_meta_keyword =:keyword: 
                                       OR t.type_meta_description =:keyword:)";

                    } else {
                        $sql .= " AND (t.type_name like CONCAT('%',:keyword:,'%') OR t.type_title like CONCAT('%',:keyword:,'%') 
                                       OR t.type_meta_keyword like CONCAT('%',:keyword:,'%') 
                                       OR t.type_meta_description like CONCAT('%',:keyword:,'%'))";
                    }
                    $arrParameter['keyword'] = $keyword;
                }
                $this->dispatcher->setParam("txtSearch", $keyword);
            }
        } else {
            $sql = "SELECT t.*, tl.* FROM Forexceccom\Models\ForexcecType t
                    INNER JOIN Forexceccom\Models\ForexcecTypeLang tl
                        ON t.type_id = tl.type_id
                        AND t.type_location_country_code = tl.type_location_country_code
                        AND tl.type_lang_code = :lang_code:
                    WHERE 1";
            $arrParameter['lang_code'] = $lang_code;
            if (!empty($keyword)) {
                if ($validator->validInt($keyword)) {
                    $sql .= " AND (t.type_id = :number:)";
                    $arrParameter['number'] = $keyword;
                } else {
                    if ($match == 'match') {
                        $sql .= " AND (tl.type_name =:keyword: OR tl.type_title =:keyword: 
                                       OR tl.type_meta_keyword =:keyword: 
                                       OR tl.type_meta_description =:keyword:)";

                    } else {
                        $sql .= " AND (tl.type_name like CONCAT('%',:keyword:,'%') OR tl.type_title like CONCAT('%',:keyword:,'%') 
                                       OR tl.type_meta_keyword like CONCAT('%',:keyword:,'%') 
                                       OR tl.type_meta_description like CONCAT('%',:keyword:,'%'))";
                    }
                    $arrParameter['keyword'] = $keyword;
                }
                $this->dispatcher->setParam("txtSearch", $keyword);
            }
        }

        $type = $this->request->get("slType");
        $validator = new Validator();
        if (!empty($type)) {
            if ($validator->validInt($type) == false) {
                $this->response->redirect("/notfound");
                return;
            }
            $sql .= " AND t.type_parent_id = :type_parent_id:";
            $arrParameter["type_parent_id"] = $type;
            $this->dispatcher->setParam("slType", $type);
        }
        $sql .= " AND (t.type_location_country_code = :location_country_code:" . $selectAll . ") ORDER BY t.type_id DESC";
        $arrParameter['location_country_code'] = $location_country_code;

        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        $data['keyword'] = $keyword;
        return $data;
    }
}