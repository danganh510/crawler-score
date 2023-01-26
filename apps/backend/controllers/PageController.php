<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecLanguage;
use Forexceccom\Models\ForexcecPage;
use Forexceccom\Models\ForexcecPageLang;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\Language;
use Forexceccom\Repositories\Page;
use Forexceccom\Repositories\PageLang;
use Forexceccom\Repositories\Location;
use Forexceccom\Repositories\Country;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\NativeArray;

class PageController extends ControllerBase
{
    protected $langCode;

    public function initialize()
    {
        $this->langCode = strtoupper($this->globalVariable->global['code']);
        parent::initialize();
    }

    public function indexAction()
    {
        $selectAll = '';
        $location_country_code = trim($this->request->get("slcLocationCountry"));
        if ($location_country_code == 'all') {
            $selectAll = "OR p.page_location_country_code != ''";
        }
        if (empty($location_country_code)) {
            $location_country_code = strtoupper($this->globalVariable->global['code']);
        }
        $btn_export = $this->request->getPost("btnExportcsv");
        $data = $this->getParameter($location_country_code, $selectAll);
        $list_page = $this->modelsManager->executeQuery($data['sql'], $data['para']);
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
        $lang_search = isset($data["para"]["lang_code"]) ? $data["para"]["lang_code"] : $this->globalVariable->defaultLanguage;
        $result = array();
        if ($list_page && sizeof($list_page) > 0) {
            if ($lang_search != $this->globalVariable->defaultLanguage) {
                foreach ($list_page as $item) {
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ForexcecPage(), array_merge($item->p->toArray(), $item->pl->toArray()));
                }
            } else {
                foreach ($list_page as $item) {
                    $result[] = \Phalcon\Mvc\Model::cloneResult(new ForexcecPage(), $item->toArray());
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
            'msg_result' => $msg_result,
            'msg_delete' => $msg_delete,
            'select_location_country' => $select_location_country,
            'location_country_code' => $location_country_code,
        ));
        $keyword = trim($this->request->get("txtSearch"));
        $replace = trim($this->request->get("txtReplace"));
        $this->dispatcher->setParam("txtReplace", $replace);
        $btn_replace = $this->request->getPost("btnReplace");
        $total_fail = 0;
        $total_success = 0;
        $res = false;
        if (isset($btn_replace)) {
            $checkReplace = $this->dispatcher->getParam('checkReplace');
            if (empty($checkReplace)) {
                $forexcec_log = array();
                $forexcec_log['find'] = $keyword;
                $forexcec_log['replace'] = $replace;
                $forexcec_log['data'] = array();
                $message_log = '';
                if ($lang_search == $this->globalVariable->defaultLanguage) {
                    $message_log = 'forexcec_page';
                    foreach ($list_page as $item) {

                        $t = 0;
                        $change_field = array();

                        $temp = str_replace($keyword, $replace, $item->getPageName());
                        if ($temp != $item->getPageName()) {
                            $t++;
                            $item->setPageName($temp);
                            array_push($change_field, 'page_name');
                        }

                        $temp = str_replace($keyword, $replace, $item->getPageTitle());
                        if ($temp != $item->getPageTitle()) {
                            $t++;
                            $item->setPageTitle($temp);
                            array_push($change_field, 'page_title');
                        }

                        $temp = str_replace($keyword, $replace, $item->getPageMetaKeyword());
                        if ($temp != $item->getPageMetaKeyword()) {
                            $t++;
                            $item->setPageMetaKeyword($temp);
                            array_push($change_field, 'page_meta_keyword');
                        }

                        $temp = str_replace($keyword, $replace, $item->getPageMetaDescription());
                        if ($temp != $item->getPageMetaDescription()) {
                            $t++;
                            $item->setPageMetaDescription($temp);
                            array_push($change_field, 'page_meta_description');
                        }

                        $temp = str_replace($keyword, $replace, $item->getPageContent());
                        if ($temp != $item->getPageContent()) {
                            $t++;
                            $item->setPageContent($temp);
                            array_push($change_field, 'page_content');
                        }

                        $res = false;
                        if ($t > 0) {
                            $res = $item->update();
                        }
                        if ($res) {
                            $total_success++;
                            $key_log = 'id: ' . $item->getPageId() . ', location_country_code: ' . $item->getPageLocationCountryCode();
                            $data = array(
                                'key' => $key_log,
                                'change' => (count($change_field) > 0) ? implode($change_field, ', ') : '',
                            );
                            array_push($forexcec_log['data'], $data);
                        } else {
                            $total_fail++;
                        }
                    }
                } else {
                    $message_log = 'forexcec_page_lang';
                    foreach ($list_page as $item) {

                        $t = 0;
                        $change_field = array();

                        $temp = str_replace($keyword, $replace, $item->pl->getPageName());
                        if ($temp != $item->pl->getPageName()) {
                            $t++;
                            $item->pl->setPageName($temp);
                            array_push($change_field, 'page_name');
                        }

                        $temp = str_replace($keyword, $replace, $item->pl->getPageTitle());
                        if ($temp != $item->pl->getPageTitle()) {
                            $t++;
                            $item->pl->setPageTitle($temp);
                            array_push($change_field, 'page_title');
                        }

                        $temp = str_replace($keyword, $replace, $item->pl->getPageMetaKeyword());
                        if ($temp != $item->pl->getPageMetaKeyword()) {
                            $t++;
                            $item->pl->setPageMetaKeyword($temp);
                            array_push($change_field, 'page_meta_keyword');
                        }

                        $temp = str_replace($keyword, $replace, $item->pl->getPageMetaDescription());
                        if ($temp != $item->pl->getPageMetaDescription()) {
                            $t++;
                            $item->pl->setPageMetaDescription($temp);
                            array_push($change_field, 'page_meta_description');
                        }

                        $temp = str_replace($keyword, $replace, $item->pl->getPageContent());
                        if ($temp != $item->pl->getPageContent()) {
                            $t++;
                            $item->pl->setPageContent($temp);
                            array_push($change_field, 'page_content');
                        }

                        $res = false;
                        if ($t > 0) {
                            $res = $item->pl->update();
                        }
                        if ($res) {
                            $total_success++;
                            $key_log = 'id: ' . $item->pl->getPageId() . ', lang_code: ' . $item->pl->getPageLangCode() . ', location_country_code: ' . $item->pl->getPageLocationCountryCode();
                            $data = array(
                                'key' => $key_log,
                                'change' => (count($change_field) > 0) ? implode($change_field, ', ') : '',
                            );
                            array_push($forexcec_log['data'], $data);
                        } else {
                            $total_fail++;
                        }
                    }
                }

                $msg_result = array();
                $msg_result['status'] = 'success';
                $msg_result['msg'] = 'Replace success: ' . $total_success . '.';
                $this->session->set('msg_result', $msg_result);

                if (count($forexcec_log['data']) > 0) {
                    $data_log = json_encode(array('replace ' . $message_log => $forexcec_log));
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
        if(isset($btn_export)){
            $this->view->disable();
            $results[] = array("Id","Name","Title","Meta Description","Meta Keyword","Content");
            foreach ($result as $item)
            {
                $test = array(
                    $item->getPageId(),
                    $item->getPageName(),
                    $item->getPageTitle(),
                    $item->getPageMetaDescription(),
                    $item->getPageMetaKeyword(),
                    $item->getPageContent(),
                );
                $results[] = $test;
            }
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=page_'.time().'.csv');
            $out = fopen('php://output', 'w');
            fputs( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM !!!!!
            foreach ($results as $fields) {
                fputcsv($out, $fields);
            }
            fclose($out);
            die();
        }
    }

    public function createAction()
    {
        $data = array('id' => -1);
        $messages = array();
        $location_country_code = $this->globalVariable->defaultLocation;
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'name' => trim($this->request->getPost("txtName")),
                'title' => $this->request->getPost("txtTitle", array('string', 'trim')),
                'keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'meta_description' => $this->request->getPost("txtMetades", array('string', 'trim')),
                'meta_image' => $this->request->getPost("txtMetaImage"),
                'meta_keyword' => $this->request->getPost("txtMetakey", array('string', 'trim')),
                'style' => $this->request->getPost("txtStyle"),
                'content' => $this->request->getPost("txtContent"),
            );
            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
            }
            if (empty($data["title"])) {
                $messages["title"] = "Title field is required.";
            }
            if (empty($data["keyword"])) {
                $messages["keyword"] = "Keyword field is required.";
            } elseif (!Page::checkKeyword($data["keyword"], -1, $location_country_code)) {
                $messages["keyword"] = "Keyword is exists!";
            }
            if (empty($data["meta_description"])) {
                $messages["meta_description"] = "Meta description field is required.";
            }
            if (empty($data["meta_keyword"])) {
                $messages["meta_keyword"] = "Meta keyword field is required.";
            }

            if (count($messages) == 0) {
                $msg_result = array();
                $new_page = new ForexcecPage();
                $new_page->setPageName($data["name"]);
                $new_page->setPageTitle($data["title"]);
                $new_page->setPageKeyword($data["keyword"]);
                $new_page->setPageMetaDescription($data["meta_description"]);
                $new_page->setPageMetaImage($data["meta_image"]);
                $new_page->setPageMetaKeyword($data["meta_keyword"]);
                $new_page->setPageStyle($data["style"]);
                $new_page->setPageContent($data["content"]);
                $new_page->setPageLocationCountryCode($location_country_code);
                $result = $new_page->save();

                if ($result === true) {
                    $message = 'Create the type ID: ' . $new_page->getPageId() . ' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('forexcec_page' => array($new_page->getPageId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $new_page->getPageId());
                } else {
                    $message = "We can't store your info now: <br/>";
                    foreach ($new_page->getMessages() as $msg) {
                        $message .= $msg . "<br/>";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-page");
            }
        }
        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
        ]);
    }

    public function editAction()
    {
        $page_id = $this->request->get('id');
        $location_country_code = $this->request->get('slcLocationCountry');
        $lang_current = $this->request->get('slcLang');
        if ($location_country_code != strtoupper($this->globalVariable->global['code'])) {
            $country_model = Country::findByCode($location_country_code);
            if (empty($country_model)) {
                return $this->response->redirect('notfound');
            }
        }
        $checkID = new Validator();
        if (!$checkID->validInt($page_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $page_model = ForexcecPage::findFirstByIdAndLocationCountryCode($page_id, $location_country_code);
        if (empty($page_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array(
            'page_id' => -1,
            'page_name' => '',
            'page_title' => '',
            'page_keyword' => '',
            'page_meta_keyword' => '',
            'page_meta_description' => '',
            'page_meta_image' => '',
            'page_style' => '',
            'page_content' => '',
            'page_location_country_code' => $location_country_code,
        );
        $save_mode = '';
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
            if ($save_mode != ForexcecPage::GENERAL) {
                $data_post['page_name'] = $this->request->getPost('txtName', array('string', 'trim'));
                $data_post['page_title'] = $this->request->getPost('txtTitle', array('string', 'trim'));
                $data_post['page_meta_keyword'] = $this->request->getPost('txtMetaKey', array('string', 'trim'));
                $data_post['page_meta_description'] = $this->request->getPost('txtMetaDesc', array('string', 'trim'));
                $data_post['page_content'] = $this->request->getPost('txtContent');
                if (empty($data_post['page_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }
                if (empty($data_post['page_title'])) {
                    $messages[$save_mode]['title'] = 'Title field is required.';
                }
                if (empty($data_post['page_meta_keyword'])) {
                    $messages[$save_mode]['meta_keyword'] = 'Meta keyword field is required.';
                }
                if (empty($data_post['page_meta_description'])) {
                    $messages[$save_mode]['meta_description'] = 'Meta description field is required.';
                }
            } else {
                $data_post['page_keyword'] = $this->request->getPost('txtKeyword', array('string', 'trim'));
                $data_post['page_style'] = $this->request->getPost('txtStyle');
                $data_post['page_meta_image'] = $this->request->getPost('txtMetaImage');
                $check_exist = new Page();
                if (empty($data_post['page_keyword'])) {
                    $messages['keyword'] = 'Keyword field is required.';
                } else if (!$check_exist->checkKeyword($data_post['page_keyword'], $page_id, $location_country_code)) {
                    $messages["keyword"] = "Keyword is exists.";
                }
            }
            if (empty($messages)) {
                switch ($save_mode) {
                    case ForexcecLanguage::GENERAL:
                        $data_old = array(
                            'page_keyword' => $page_model->getPageKeyword(),
                            'page_style' => $page_model->getPageStyle(),
                            'page_meta_image' => $page_model->getPageMetaImage(),
                        );
                        $page_model->setPageKeyword($data_post['page_keyword']);
                        $page_model->setPageStyle($data_post['page_style']);
                        $page_model->setPageMetaImage($data_post['page_meta_image']);
                        $result = $page_model->update();
                        $info = ForexcecLanguage::GENERAL;
                        $data_new = array(
                            'page_keyword' => $page_model->getPageKeyword(),
                            'page_style' => $page_model->getPageStyle(),
                            'page_meta_image' => $page_model->getPageMetaImage(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'page_name' => $page_model->getPageName(),
                            'page_title' => $page_model->getPageTitle(),
                            'page_meta_keyword' => $page_model->getPageMetaKeyword(),
                            'page_meta_description' => $page_model->getPageMetaDescription(),
                            'page_content' => $page_model->getPageContent(),
                        );
                        $page_model->setPageName($data_post['page_name']);
                        $page_model->setPageTitle($data_post['page_title']);
                        $page_model->setPageMetaKeyword($data_post['page_meta_keyword']);
                        $page_model->setPageMetaDescription($data_post['page_meta_description']);
                        $page_model->setPageContent($data_post['page_content']);
                        $result = $page_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'page_name' => $page_model->getPageName(),
                            'page_title' => $page_model->getPageTitle(),
                            'page_meta_keyword' => $page_model->getPageMetaKeyword(),
                            'page_meta_description' => $page_model->getPageMetaDescription(),
                            'page_content' => $page_model->getPageContent(),
                        );
                        break;
                    default:
                        $page_lang_model = PageLang::findFirstByIdAndLocationCountryCodeAndLang($page_id, $location_country_code, $save_mode);
                        if (!$page_lang_model) {
                            $page_lang_model = new ForexcecPageLang();
                            $page_lang_model->setPageId($page_id);
                            $page_lang_model->setPageLangCode($save_mode);
                            $page_lang_model->setPageLocationCountryCode(strtolower($location_country_code));
                        } else {
                            $data_old = $page_lang_model->toArray();
                        }
                        $page_lang_model->setPageName($data_post['page_name']);
                        $page_lang_model->setPageTitle($data_post['page_title']);
                        $page_lang_model->setPageMetaKeyword($data_post['page_meta_keyword']);
                        $page_lang_model->setPageMetaDescription($data_post['page_meta_description']);
                        $page_lang_model->setPageContent($data_post['page_content']);
                        $result = $page_lang_model->save();
                        $info = $arr_language[$save_mode];
                        $data_new = $page_lang_model->toArray();
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Page success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('forexcec_page_lang' => array($page_id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $page_id);
                } else {
                    $messages = array(
                        'message' => "Update Page fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $page_model = Page::getByIdAndLocationCountryCode($page_model->getPageId(), $page_model->getPageLocationCountryCode());
        $item = array(
            'page_id' => $page_model->getPageId(),
            'page_location_country_code' => $page_model->getPageLocationCountryCode(),
            'page_name' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['page_name'] : $page_model->getPageName(),
            'page_title' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['page_title'] : $page_model->getPageTitle(),
            'page_meta_keyword' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['page_meta_keyword'] : $page_model->getPageMetaKeyword(),
            'page_meta_description' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['page_meta_description'] : $page_model->getPageMetaDescription(),
            'page_content' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['page_content'] : $page_model->getPageContent(),
        );
        $arr_translate[$this->globalVariable->defaultLanguage] = $item;
        $arr_page_lang = ForexcecPageLang::findByIdAndLocationCountryCode($page_id, $location_country_code);
        foreach ($arr_page_lang as $page_lang) {
            $item = array(
                'page_id' => $page_lang->getPageId(),
                'page_location_country_code' => $page_lang->getPageLocationCountryCode(),
                'page_name' => ($save_mode === $page_lang->getPageLangCode()) ? $data_post['page_name'] : $page_lang->getPageName(),
                'page_title' => ($save_mode === $page_lang->getPageLangCode()) ? $data_post['page_title'] : $page_lang->getPageTitle(),
                'page_meta_keyword' => ($save_mode === $page_lang->getPageLangCode()) ? $data_post['page_meta_keyword'] : $page_lang->getPageMetaKeyword(),
                'page_meta_description' => ($save_mode === $page_lang->getPageLangCode()) ? $data_post['page_meta_description'] : $page_lang->getPageMetaDescription(),
                'page_content' => ($save_mode === $page_lang->getPageLangCode()) ? $data_post['page_content'] : $page_lang->getPageContent(),
            );
            $arr_translate[$page_lang->getPageLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'page_id' => -1,
                'page_location_country_code' => $data_post['page_location_country_code'],
                'page_name' => $data_post['page_name'],
                'page_title' => $data_post['page_title'],
                'page_meta_keyword' => $data_post['page_meta_keyword'],
                'page_meta_description' => $data_post['page_meta_description'],
                'page_content' => $data_post['page_content'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'page_id' => $page_model->getPageId(),
            'page_location_country_code' => $page_model->getPageLocationCountryCode(),
            'page_keyword' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['page_keyword'] : $page_model->getPageKeyword(),
            'page_style' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['page_style'] : $page_model->getPageStyle(),
            'page_meta_image' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['page_meta_image'] : $page_model->getPageMetaImage(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $messages["status"] = "border-red";
        $select_location_country = Country::getCountryGlobalComboBox($location_country_code);
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
            'location_country_code' => $location_country_code,
            'select_location_country' => $select_location_country,
        ]);
    }

    public function deleteAction()
    {
        $items_checked = $this->request->getPost("item");
        $location_country_code = $this->request->get('slcLocationCountry');
        $lang = $this->request->get("slcLang");

        if ($location_country_code == '') {
            return $this->response->redirect("notfound");
        }

        if ($location_country_code != $this->langCode) {
            $country_model = Country::findByCode($location_country_code);
            if (empty($country_model)) {
                return $this->response->redirect("notfound");
            }
        }

        if (!empty($items_checked)) {
            $msg_result = array();
            $forexcec_log = array();
            foreach ($items_checked as $id) {
                if ($lang != $this->globalVariable->defaultLanguage) {
                    $item = PageLang::findFirstByIdAndLocationCountryCodeAndLang($id, $location_country_code, $lang);
                } else {
                    $item = ForexcecPage::findFirstByIdAndLocationCountryCode($id, $location_country_code);
                }

                if ($item) {
                    if ($item->delete() === false) {
                        $message_delete = 'Can\'t delete the page Name = ' . $item->getPageName();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = $item->toArray();
                        $forexcec_log[$id] = $old_data;
                        if (strtolower($location_country_code) == $this->globalVariable->global['code']) {
                            PageLang::deleteById($id);
                        }
                        if ($lang == $this->globalVariable->defaultLanguage) {
                            PageLang::deleteByIdAndLocationCountryCode($id, $location_country_code);
                        }
                    }
                }
            }
            if (count($forexcec_log) > 0) {
                $message_delete = 'Delete ' . count($forexcec_log) . ' page successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
                $message = '';
                $data_log = json_encode(array('forexcec_page' => $forexcec_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect('/list-page?slcLocationCountry=' . $location_country_code . '&slcLang=' . $lang);
        }
    }

    private function getParameter($location_country_code, $selectAll = '')
    {
        $lang = $this->request->get("slcLang", array('string', 'trim'));

        $keyword = trim($this->request->get("txtSearch"));
        $langCode = !empty($lang) ? $lang : $this->globalVariable->defaultLanguage;
        $this->dispatcher->setParam("slcLang", $langCode);
        $arrParameter = array('location_country_code' => $location_country_code);

        $match = trim($this->request->get("radMatch"));
        if ($match == '') {
            $match = 'notmatch';
        }
        $this->dispatcher->setParam("radMatch", $match);

        $validator = new Validator();
        if ($langCode === $this->globalVariable->defaultLanguage) {
            $sql = "SELECT p.* FROM Forexceccom\Models\ForexcecPage p WHERE 1";
            if (!empty($keyword)) {
                if ($validator->validInt($keyword)) {
                    $sql .= " AND (p.page_id = :number:)";
                    $arrParameter['number'] = $keyword;
                } else {
                    if ($match == 'match') {
                        $sql .= " AND (p.page_name =:keyword: OR p.page_title =:keyword:
                                     OR p.page_meta_keyword =:keyword: OR p.page_meta_description =:keyword:
                                     OR p.page_content =:keyword:
                                    )";
                    } else {
                        $sql .= " AND (p.page_name like CONCAT('%',:keyword:,'%') OR p.page_title like CONCAT('%',:keyword:,'%')
                                     OR p.page_meta_keyword like CONCAT('%',:keyword:,'%') OR p.page_meta_description like CONCAT('%',:keyword:,'%')
                                     OR p.page_content like CONCAT('%',:keyword:,'%')
                                     )";
                    }
                    $arrParameter['keyword'] = $keyword;
                }
                $this->dispatcher->setParam("txtSearch", $keyword);
            }
        } else {
            $sql = "SELECT p.*, pl.* FROM Forexceccom\Models\ForexcecPage p 
                    INNER JOIN Forexceccom\Models\ForexcecPageLang pl
                                ON pl.page_id = p.page_id AND pl.page_location_country_code = p.page_location_country_code AND  pl.page_lang_code = :lang_code:                           
                    WHERE 1";
            $arrParameter['lang_code'] = $langCode;
            $this->dispatcher->setParam("slcLang", $langCode);
            if (!empty($keyword)) {
                if ($validator->validInt($keyword)) {
                    $sql .= " AND (p.page_id = :number:)";
                    $arrParameter['number'] = $keyword;
                } else {
                    if ($match == 'match') {
                        $sql .= " AND (pl.page_name =:keyword: OR pl.page_title =:keyword:
                                     OR pl.page_meta_keyword =:keyword: OR pl.page_meta_description =:keyword:
                                     OR pl.page_content =:keyword:
                                    )";
                    } else {
                        $sql .= " AND (pl.page_name like CONCAT('%',:keyword:,'%') OR pl.page_title like CONCAT('%',:keyword:,'%')
                                     OR pl.page_meta_keyword like CONCAT('%',:keyword:,'%') OR pl.page_meta_description like CONCAT('%',:keyword:,'%')
                                     OR pl.page_content like CONCAT('%',:keyword:,'%')
                                     )";
                    }
                    $arrParameter['keyword'] = $keyword;
                }
                $this->dispatcher->setParam("txtSearch", $keyword);
            }
        }
        $sql .= " AND (p.page_location_country_code = :location_country_code:" . $selectAll . ") ORDER BY p.page_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
    public function insertdataAction(){
        $this->view->disable();
        $id = $this->request->get('id');

        $page_model = ForexcecPage::findFirstByIdAndLocationCountryCode($id,"gx");
        $page_model_array = $page_model->toArray();
        unset($page_model_array['page_location_country_code']);
        if (!$page_model) die(false);
        $locationCountryEn = Location::getCountryCodeByLang("en");
        if ($locationCountryEn) {
            foreach ($locationCountryEn as $countryEn) {
                $model_country = ForexcecPage::findFirstByIdAndLocationCountryCode($id,$countryEn);
                if ($model_country) continue;
                $new_record = new ForexcecPage();
                $page_model_array['page_location_country_code'] = $countryEn;
                $new_record->save($page_model_array);
            }
        }
        $page_vi_model = PageLang::findFirstByIdAndLang($id,"vn","vi");
        if (!$page_vi_model) {
            $page_vi_model = new ForexcecPageLang();
            $page_model_array['page_location_country_code'] = "vn";
            $page_model_array['page_lang_code'] = "vi";
            $page_vi_model->save($page_model_array);
        }

        die("success");
    }

}