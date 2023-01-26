<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecGlossary;
use Forexceccom\Models\ForexcecGlossaryLang;
use Forexceccom\Models\ForexcecLanguage;
use Forexceccom\Repositories\Glossary;
use Forexceccom\Repositories\GlossaryLang;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\NativeArray;
use Forexceccom\Repositories\Language;
use Forexceccom\Repositories\Activity;

class GlossaryController extends ControllerBase
{
    public function indexAction()
    {
        $lang = $this->request->get("slcLang", array('string', 'trim'));
        $langCode = !empty($lang) ? $lang : $this->globalVariable->defaultLanguage;
        $this->dispatcher->setParam('slcLang', $langCode);
        $select_lang = Language::getCombo($langCode);
        $this->view->select_lang = $select_lang;
        $match = trim($this->request->get("radMatch"));
        if ($match == '') {
            $match = 'notmatch';
        }
        $this->dispatcher->setParam("radMatch", $match);
        $keyword = trim($this->request->get("txtSearch"));
        $keyword_specialchars = htmlspecialchars($keyword);
        $arrParameter = array();
        $validator = new Validator();
        if ($langCode === $this->globalVariable->defaultLanguage) {
            $sql = 'select g.* from Forexceccom\Models\ForexcecGlossary g where 1';
            if (!empty($keyword)) {
                if ($validator->validInt($keyword)) {
                    $sql .= ' and (g.glossary_id = :number:)';
                    $arrParameter['number'] = $keyword;
                } else {
                    if ($match == 'match') {
                        $sql .= ' and (g.glossary_name = :keyword: or g.glossary_title = :keyword: 
                                   or g.glossary_meta_keyword = :keyword: or g.glossary_meta_description = :keyword: 
                                   or g.glossary_summary = :keyword: or g.glossary_content = :keyword:
                                   )';
                    } else {
                        $sql .= " and (g.glossary_name like concat('%',:keyword:,'%') or g.glossary_title like concat('%',:keyword:,'%') 
                                   or g.glossary_meta_keyword like concat('%',:keyword:,'%') or g.glossary_meta_description like concat('%',:keyword:,'%') 
                                   or g.glossary_summary like concat('%',:keyword:,'%') or g.glossary_content like concat('%',:keyword:,'%')
                                   )";
                    }
                    $arrParameter['keyword'] = $keyword_specialchars;
                }
                $this->dispatcher->setParam("txtSearch", $keyword);
            }
        } else {
            $sql = "select g.*, gl.* from Forexceccom\Models\ForexcecGlossary g
                    inner join Forexceccom\Models\ForexcecGlossaryLang gl
                    on g.glossary_id = gl.glossary_id and gl.glossary_lang_code = :lang_code:
                    where 1";
            $arrParameter['lang_code'] = $langCode;
            if (!empty($keyword)) {
                if ($validator->validInt($keyword)) {
                    $sql .= ' and (g.glossary_id = :number:)';
                    $arrParameter['number'] = $keyword;
                } else {
                    if ($match == 'match') {
                        $sql .= ' and (gl.glossary_name = :keyword: or gl.glossary_title = :keyword: 
                                   or gl.glossary_meta_keyword = :keyword: or gl.glossary_meta_description = :keyword: 
                                   or gl.glossary_summary = :keyword: or gl.glossary_content = :keyword:
                                   )';
                    } else {
                        $sql .= " and (gl.glossary_name like concat('%',:keyword:,'%') or gl.glossary_title like concat('%',:keyword:,'%') 
                                   or gl.glossary_meta_keyword like concat('%',:keyword:,'%') or gl.glossary_meta_description like concat('%',:keyword:,'%') 
                                   or gl.glossary_summary like concat('%',:keyword:,'%') or gl.glossary_content like concat('%',:keyword:,'%')
                                   )";
                    }
                    $arrParameter['keyword'] = $keyword;
                }
                $this->dispatcher->setParam("txtSearch", $keyword);
            }
        }
        $sql .= " order by g.glossary_id desc";
        $list_glossary = $this->modelsManager->executeQuery($sql, $arrParameter);

        $result = array();
        if ($list_glossary && sizeof($list_glossary) > 0) {
            if ($langCode != $this->globalVariable->defaultLanguage) {
                foreach ($list_glossary as $item) {
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ForexcecGlossary(), array_merge($item->g->toArray(), $item->gl->toArray()));

                }
            } else {
                foreach ($list_glossary as $item) {
                    $result[] = \Phalcon\Mvc\Model::cloneResult(new ForexcecGlossary(), $item->toArray());
                }
            }
        }
        $current_page = $this->request->getQuery('page', 'int');
        $paginator = new NativeArray(
            [
                'data' => $result,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $this->view->setVars(array(
            'list_glossary' => $paginator->getPaginate(),
        ));

        $replace = trim($this->request->get("txtReplace"));
        $this->dispatcher->setParam("txtReplace", $replace);
        $btn_replace = $this->request->getPost("btnReplace");
        $total_fail = 0;
        $total_success = 0;
        $res = false;
        if (isset($btn_replace)) {
            $checkReplace = $this->dispatcher->getParam('checkReplace');
            if (empty($checkReplace)) {
                $occ_log = array();
                $occ_log['find'] = $keyword;
                $occ_log['replace'] = $replace;
                $occ_log['data'] = array();
                $message_log = '';
                if ($langCode == $this->globalVariable->defaultLanguage) {
                    $message_log = 'forexcec_glossary';
                    foreach ($list_glossary as $item) {

                        $t = 0;
                        $change_field = array();

                        $temp = str_replace($keyword, $replace, $item->getGlossaryName());
                        if ($temp != $item->getGlossaryName()) {
                            $t++;
                            $item->setGlossaryName($temp);
                            array_push($change_field, 'glossary_name');
                        }

                        $temp = str_replace($keyword, $replace, $item->getGlossaryTitle());
                        if ($temp != $item->getGlossaryTitle()) {
                            $t++;
                            $item->setGlossaryTitle($temp);
                            array_push($change_field, 'glossary_title');
                        }

                        $temp = str_replace($keyword, $replace, $item->getGlossaryMetaKeyword());
                        if ($temp != $item->getGlossaryMetaKeyword()) {
                            $t++;
                            $item->setGlossaryMetaKeyword($temp);
                            array_push($change_field, 'glossary_meta_keyword');
                        }

                        $temp = str_replace($keyword, $replace, $item->getGlossaryMetaDescription());
                        if ($temp != $item->getGlossaryMetaDescription()) {
                            $t++;
                            $item->setGlossaryMetaDescription($temp);
                            array_push($change_field, 'glossary_meta_description');
                        }

                        $temp = str_replace($keyword, $replace, $item->getGlossarySummary());
                        if ($temp != $item->getGlossarySummary()) {
                            $t++;
                            $item->setGlossarySummary($temp);
                            array_push($change_field, 'glossary_summary');
                        }

                        $temp = str_replace($keyword, $replace, $item->getGlossaryContent());
                        if ($temp != $item->getGlossaryContent()) {
                            $t++;
                            $item->setGlossaryContent($temp);
                            array_push($change_field, 'glossary_content');
                        }

                        $res = false;
                        if ($t > 0) {
                            $res = $item->update();
                        }
                        if ($res) {
                            $total_success++;
                            $key_log = 'id: ' . $item->getGlossaryId();
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
                    $message_log = 'forexcec_glossary_lang';
                    foreach ($list_glossary as $item) {

                        $t = 0;
                        $change_field = array();

                        $temp = str_replace($keyword, $replace, $item->gl->getGlossaryName());
                        if ($temp != $item->gl->getGlossaryName()) {
                            $t++;
                            $item->gl->setGlossaryName($temp);
                            array_push($change_field, 'glossary_name');
                        }

                        $temp = str_replace($keyword, $replace, $item->gl->getGlossaryTitle());
                        if ($temp != $item->gl->getGlossaryTitle()) {
                            $t++;
                            $item->gl->setGlossaryTitle($temp);
                            array_push($change_field, 'glossary_title');
                        }

                        $temp = str_replace($keyword, $replace, $item->gl->getGlossaryMetaKeyword());
                        if ($temp != $item->gl->getGlossaryMetaKeyword()) {
                            $t++;
                            $item->gl->setGlossaryMetaKeyword($temp);
                            array_push($change_field, 'glossary_meta_keyword');
                        }

                        $temp = str_replace($keyword, $replace, $item->gl->getGlossaryMetaDescription());
                        if ($temp != $item->gl->getGlossaryMetaDescription()) {
                            $t++;
                            $item->gl->setGlossaryMetaDescription($temp);
                            array_push($change_field, 'glossary_meta_description');
                        }

                        $temp = str_replace($keyword, $replace, $item->gl->getGlossarySummary());
                        if ($temp != $item->gl->getGlossarySummary()) {
                            $t++;
                            $item->gl->setGlossarySummary($temp);
                            array_push($change_field, 'glossary_meta_description');
                        }

                        $temp = str_replace($keyword, $replace, $item->gl->getGlossaryContent());
                        if ($temp != $item->gl->getGlossaryContent()) {
                            $t++;
                            $item->gl->setGlossaryContent($temp);
                            array_push($change_field, 'glossary_content');
                        }

                        $res = false;
                        if ($t > 0) {
                            $res = $item->gl->update();
                        }
                        if ($res) {
                            $total_success++;
                            $key_log = 'id: ' . $item->gl->getGlossaryId() . ', lang_code: ' . $item->gl->getGlossaryLangCode();

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
        $data = array('glossary_id' => -1, 'glossary_ishome' => 'N', 'glossary_active' => 'Y', 'glossary_order' => 1);
        $messages = array();
        if ($this->request->isPost()) {
            $data = array(
                'glossary_id' => -1,
                'glossary_name' => $this->request->getPost('txtName', array('string', 'trim')),
                'glossary_title' => $this->request->getPost('txtTitle', array('string', 'trim')),
                'glossary_keyword' => $this->request->getPost('txtKeyword', array('string', 'trim')),
                'glossary_meta_keyword' => $this->request->getPost('txtMetaKey', array('string', 'trim')),
                'glossary_meta_description' => $this->request->getPost('txtMetaDesc', array('string', 'trim')),
                'glossary_icon' => $this->request->getPost('txtIcon'),
                'glossary_meta_image' => $this->request->getPost('txtMetaImage'),
                'glossary_summary' => $this->request->getPost('txtSummary'),
                'glossary_content' => $this->request->getPost('txtContent'),
                'glossary_ishome' => $this->request->getPost('radIsHome'),
                'glossary_active' => $this->request->getPost('radActive'),
                'glossary_order' => $this->request->getPost('txtOrder', array('string', 'trim')),
            );
            $messages = array();
            if (empty($data['glossary_name'])) {
                $messages['name'] = 'Name field is required.';
            }
            if (empty($data['glossary_title'])) {
                $messages['title'] = 'Title field is required.';
            }
            if (empty($data['glossary_keyword'])) {
                $messages['keyword'] = 'Keyword field is required.';
            } else if (Glossary::checkKeyword($data['glossary_keyword'], $data['glossary_id'])) {
                $messages['keyword'] = 'Keyword is exists.';
            }
            if (empty($data['glossary_meta_keyword'])) {
                $messages['meta_keyword'] = 'Meta keyword field is required.';
            }
            if (empty($data['glossary_meta_description'])) {
                $messages['meta_description'] = 'Meta description field is required.';
            }
            if (empty($data['glossary_order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data['glossary_order'])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_glossary = new ForexcecGlossary();
                $new_glossary->setGlossaryName($data['glossary_name']);
                $new_glossary->setGlossaryTitle($data['glossary_title']);
                $new_glossary->setGlossaryKeyword($data['glossary_keyword']);
                $new_glossary->setGlossaryMetaKeyword($data['glossary_meta_keyword']);
                $new_glossary->setGlossaryMetaDescription($data['glossary_meta_description']);
                $new_glossary->setGlossaryIcon($data['glossary_icon']);
                $new_glossary->setGlossaryMetaImage($data['glossary_meta_image']);
                $new_glossary->setGlossarySummary($data['glossary_summary']);
                $new_glossary->setGlossaryContent($data['glossary_content']);
                $new_glossary->setGlossaryInsertTime($this->globalVariable->curTime);
                $new_glossary->setGlossaryUpdateTime($this->globalVariable->curTime);
                $new_glossary->setGlossaryIsHome($data['glossary_ishome']);
                $new_glossary->setGlossaryActive($data['glossary_active']);
                $new_glossary->setGlossaryOrder($data['glossary_order']);
                $result = $new_glossary->save();

                if ($result === false) {
                    $msg_result = array('status' => 'error', 'msg' => 'Create Glossary fail !');
                } else {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Glossary Success');
                    $old_data = array();
                    $new_data = $data;
                    $message = '';
                    $data_log = json_encode(array('occ_glossary' => array($new_glossary->getGlossaryId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $new_glossary->getGlossaryId());
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-glossary");
            }
        }
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
        ]);
    }

    public function editAction()
    {
        $glossary_id = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($glossary_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $glossary_model = ForexcecGlossary::findFirstById($glossary_id);
        if (empty($glossary_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array(
            'glossary_id' => -1,
            'glossary_name' => '',
            'glossary_title' => '',
            'glossary_keyword' => '',
            'glossary_meta_keyword' => '',
            'glossary_meta_description' => '',
            'glossary_icon' => '',
            'glossary_meta_image' => '',
            'glossary_summary' => '',
            'glossary_content' => '',
            'glossary_active' => '',
            'glossary_ishome' => '',
            'glossary_order' => '',
        );
        $save_mode = '';
        $lang_default = $this->globalVariable->defaultLanguage;
        $arr_language = Language::arrLanguages();
        $lang_current = $lang_default;
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
                $data_post['glossary_name'] = $this->request->getPost('txtName', array('string', 'trim'));
                $data_post['glossary_title'] = $this->request->getPost('txtTitle', array('string', 'trim'));
                $data_post['glossary_meta_keyword'] = $this->request->getPost('txtMetaKey', array('string', 'trim'));
                $data_post['glossary_meta_description'] = $this->request->getPost('txtMetaDesc', array('string', 'trim'));
                $data_post['glossary_summary'] = $this->request->getPost('txtSummary');
                $data_post['glossary_content'] = $this->request->getPost('txtContent');
                if (empty($data_post['glossary_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }
                if (empty($data_post['glossary_title'])) {
                    $messages[$save_mode]['title'] = 'Title field is required.';
                }
                if (empty($data_post['glossary_meta_keyword'])) {
                    $messages[$save_mode]['meta_keyword'] = 'Meta keyword field is required.';
                }
                if (empty($data_post['glossary_meta_description'])) {
                    $messages[$save_mode]['meta_description'] = 'Meta description field is required.';
                }
            } else {
                $data_post['glossary_keyword'] = $this->request->getPost('txtKeyword', array('string', 'trim'));
                $data_post['glossary_icon'] = $this->request->getPost('txtIcon');
                $data_post['glossary_meta_image'] = $this->request->getPost('txtMetaImage');
                $data_post['glossary_ishome'] = $this->request->getPost('radIsHome');
                $data_post['glossary_active'] = $this->request->getPost('radActive');
                $data_post['glossary_order'] = $this->request->getPost('txtOrder', array('string', 'trim'));
                if (empty($data_post['glossary_keyword'])) {
                    $messages['keyword'] = 'Keyword field is required.';
                } else if (Glossary::checkKeyword($data_post['glossary_keyword'])) {
                    $messages['keyword'] = 'Keyword is exists.';
                }
                if (empty($data_post['glossary_order'])) {
                    $messages["order"] = "Order field is required.";
                } else if (!is_numeric($data_post['glossary_order'])) {
                    $messages["order"] = "Order is not valid ";
                }
            }
            if (empty($messages)) {
                switch ($save_mode) {
                    case ForexcecLanguage::GENERAL:
                        $data_old = $glossary_model->toArray();
                        $glossary_model->setGlossaryKeyword($data_post['glossary_keyword']);
                        $glossary_model->setGlossaryIcon($data_post['glossary_icon']);
                        $glossary_model->setGlossaryMetaImage($data_post['glossary_meta_image']);
                        $glossary_model->setGlossaryIsHome($data_post['glossary_ishome']);
                        $glossary_model->setGlossaryActive($data_post['glossary_active']);
                        $glossary_model->setGlossaryOrder($data_post['glossary_order']);
                        $glossary_model->setGlossaryUpdateTime($this->globalVariable->curTime);
                        $result = $glossary_model->update();
                        $info = ForexcecLanguage::GENERAL;
                        $data_new = $glossary_model->toArray();
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = $glossary_model->toArray();
                        $glossary_model->setGlossaryName($data_post['glossary_name']);
                        $glossary_model->setGlossaryTitle($data_post['glossary_title']);
                        $glossary_model->setGlossaryMetaKeyword($data_post['glossary_meta_keyword']);
                        $glossary_model->setGlossaryMetaDescription($data_post['glossary_meta_description']);
                        $glossary_model->setGlossarySummary($data_post['glossary_summary']);
                        $glossary_model->setGlossaryContent($data_post['glossary_content']);
                        $result = $glossary_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = $glossary_model->toArray();
                        break;
                    default:
                        $glossary_lang = GlossaryLang::findFirstByIdAndLang($glossary_id, $save_mode);
                        if ($glossary_lang) {
                            $data_old = $glossary_lang->toArray();
                        } else {
                            $glossary_lang = new ForexcecGlossaryLang();
                            $glossary_lang->setGlossaryId($glossary_id);
                            $glossary_lang->setGlossaryLangCode($save_mode);
                        }
                        $glossary_lang->setGlossaryName($data_post['glossary_name']);
                        $glossary_lang->setGlossaryTitle($data_post['glossary_title']);
                        $glossary_lang->setGlossaryMetaKeyword($data_post['glossary_meta_keyword']);
                        $glossary_lang->setGlossaryMetaDescription($data_post['glossary_meta_description']);
                        $glossary_lang->setGlossarySummary($data_post['glossary_summary']);
                        $glossary_lang->setGlossaryContent($data_post['glossary_content']);
                        $result = $glossary_lang->save();
                        $info = $arr_language[$save_mode];
                        $data_new = $glossary_lang->toArray();
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Glossary success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('occ_glossary' => array($glossary_id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $glossary_id);
                } else {
                    $messages = array(
                        'message' => "Update Glossary fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'glossary_id' => $glossary_model->getGlossaryId(),
            'glossary_name' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['glossary_name'] : $glossary_model->getGlossaryName(),
            'glossary_title' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['glossary_title'] : $glossary_model->getGlossaryTitle(),
            'glossary_meta_keyword' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['glossary_meta_keyword'] : $glossary_model->getGlossaryMetaKeyword(),
            'glossary_meta_description' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['glossary_meta_description'] : $glossary_model->getGlossaryMetaDescription(),
            'glossary_summary' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['glossary_summary'] : $glossary_model->getGlossarySummary(),
            'glossary_content' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['glossary_content'] : $glossary_model->getGlossaryContent(),
        );
        $arr_translate[$this->globalVariable->defaultLanguage] = $item;
        $arr_glossary_lang = ForexcecGlossaryLang::findById($glossary_id);
        foreach ($arr_glossary_lang as $glossary_lang) {
            $item = array(
                'glossary_id' => $glossary_lang->getGlossaryId(),
                'glossary_name' => ($save_mode === $glossary_lang->getGlossaryLangCode()) ? $data_post['glossary_name'] : $glossary_lang->getGlossaryName(),
                'glossary_title' => ($save_mode === $glossary_lang->getGlossaryLangCode()) ? $data_post['glossary_title'] : $glossary_lang->getGlossaryTitle(),
                'glossary_meta_keyword' => ($save_mode === $glossary_lang->getGlossaryLangCode()) ? $data_post['glossary_meta_keyword'] : $glossary_lang->getGlossaryMetaKeyword(),
                'glossary_meta_description' => ($save_mode === $glossary_lang->getGlossaryLangCode()) ? $data_post['glossary_meta_description'] : $glossary_lang->getGlossaryMetaDescription(),
                'glossary_summary' => ($save_mode === $glossary_lang->getGlossaryLangCode()) ? $data_post['glossary_summary'] : $glossary_lang->getGlossarySummary(),
                'glossary_content' => ($save_mode === $glossary_lang->getGlossaryLangCode()) ? $data_post['glossary_content'] : $glossary_lang->getGlossaryContent(),
            );
            $arr_translate[$glossary_lang->getGlossaryLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'glossary_id' => -1,
                'glossary_name' => $data_post['glossary_name'],
                'glossary_title' => $data_post['glossary_title'],
                'glossary_meta_keyword' => $data_post['glossary_meta_keyword'],
                'glossary_meta_description' => $data_post['glossary_meta_description'],
                'glossary_summary' => $data_post['glossary_summary'],
                'glossary_content' => $data_post['glossary_content'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'glossary_id' => $glossary_model->getGlossaryId(),
            'glossary_keyword' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['glossary_keyword'] : $glossary_model->getGlossaryKeyword(),
            'glossary_icon' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['glossary_icon'] : $glossary_model->getGlossaryIcon(),
            'glossary_meta_image' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['glossary_meta_image'] : $glossary_model->getGlossaryMetaImage(),
            'glossary_ishome' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['glossary_ishome'] : $glossary_model->getGlossaryIsHome(),
            'glossary_active' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['glossary_active'] : $glossary_model->getGlossaryActive(),
            'glossary_order' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['glossary_order'] : $glossary_model->getGlossaryOrder(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
        ]);
    }

    public function deleteAction()
    {
        $items_checked = $this->request->getPost("item");
        $lang = $this->request->get("slcLang");
        if (!empty($items_checked)) {
            $msg_result = array();
            $occ_log = array();
            foreach ($items_checked as $id) {
                if ($lang != $this->globalVariable->defaultLanguage) {
                    $item = GlossaryLang::findFirstByIdAndLang($id, $lang);
                } else {
                    $item = ForexcecGlossary::findFirstById($id);
                }

                if ($item) {
                    if ($item->delete() === false) {
                        $message_delete = 'Can\'t delete the content service Name = ' . $item->getGlossaryName();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = $item->toArray();
                        $occ_log[$id] = $old_data;
                        if ($lang == $this->globalVariable->defaultLanguage) {
                            GlossaryLang::deleteById($id);
                        }
                    }
                }
            }
            if (count($occ_log) > 0) {
                $message_delete = 'Delete ' . count($occ_log) . ' Glossary successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
                $message = '';
                $data_log = json_encode(array('forexcec_glossary' => $occ_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect('/list-glossary?slcLang=' . $lang);
        }
    }
}