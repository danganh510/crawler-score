<?php

namespace Forexceccom\Backend\Controllers;


use Forexceccom\Models\ForexcecBanner;
use Forexceccom\Models\ForexcecBannerLang;
use Forexceccom\Models\ForexcecLanguage;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\Banner;
use Forexceccom\Repositories\BannerLang;
use Forexceccom\Repositories\Language;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Utils\Validator;

class BannerController extends ControllerBase
{
    public function indexAction()
    {
        $current_page = $this->request->getQuery('page', 'int');
        $validator = new Validator();
        $keyword = $this->request->get('txtSearch', 'trim');
        $controller = $this->request->get('slcController');
        $type = $this->request->get('slcType');
        $banner = new Banner();
        $select_controller = $banner->getControllerCombobox($controller);
        $select_type = $banner->getTypeCombobox($type);
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecBanner WHERE 1";
        $arrParameter = array();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (banner_id = :keyword:) ";
            } else {
                $sql .= " AND (banner_title like CONCAT('%',:keyword:,'%'))";
            }
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $banner_controller = Banner::getValue($controller, Banner::CONTROLLER);
        $banner_article_keyword = Banner::getValue($controller, Banner::ARTICLE);
        if (!empty($banner_controller)) {
            $sql .= " AND (banner_controller = :controller:)";
            $arrParameter['controller'] = $banner_controller;
            $this->dispatcher->setParam("slcController", $controller);
        }
        if (!empty($banner_article_keyword)) {
            $sql .= " AND (banner_article_keyword = :article_keyword:)";
            $arrParameter['article_keyword'] = $banner_article_keyword;
            $this->dispatcher->setParam("slcController", $controller);
        }
        if (!empty($type)) {
            $sql .= " AND (banner_type = :banner_type:)";
            $arrParameter['banner_type'] = $type;
            $this->dispatcher->setParam("slcType", $type);
        }
        $sql .= " ORDER BY banner_id DESC";
        $list_banner = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(
            [
                'data' => $list_banner,
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
            'list_banner' => $paginator->getPaginate(),
            'select_controller' => $select_controller,
            'select_type' => $select_type
        ));
    }

    public function createAction()
    {
        $data = array('banner_id' => -1, 'banner_active' => 'Y', 'banner_order' => 1, 'banner_controller' => '', 'banner_is_home' => 'N', 'banner_type' => ForexcecBanner::TYPEIMAGE);
        if ($this->request->isPost()) {
            $data = array(
                'banner_controller' => $this->request->getPost('slcController'),
                'banner_type' => $this->request->getPost('slcType'),
                'banner_title' => $this->request->getPost('txtTitle'),
                'banner_subtitle' => $this->request->getPost('txtSubtitle'),
                'banner_content' => $this->request->getPost('txtContent'),
                'banner_link' => $this->request->getPost('txtLink', array('string', 'trim')),
                'banner_image' => $this->request->getPost('txtImage', array('string', 'trim')),
                'banner_image_mobile' => $this->request->getPost('txtImageMobile', array('string', 'trim')),
                'banner_order' => $this->request->getPost('txtOrder', 'trim'),
                'banner_active' => $this->request->getPost('radActive'),
                'banner_is_home' => $this->request->getPost('radIsHome'),
            );
            $messages = array();
            if ($data['banner_controller'] == "") {
                $messages['controller'] = 'Controller is required.';
            }
            if (empty($data['banner_content'])) {
                $messages['content'] = 'Content field is required.';
            }
            if (empty($data['banner_order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data["banner_order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $banner_controller = Banner::getValue($data['banner_controller'], Banner::CONTROLLER);
                $banner_article_keyword = Banner::getValue($data['banner_controller'], Banner::ARTICLE);
                $msg_result = array();
                $new_banner = new ForexcecBanner();
                $new_banner->setBannerController($banner_controller);
                $new_banner->setBannerArticleKeyword($banner_article_keyword);
                $new_banner->setBannerType($data['banner_type']);
                $new_banner->setBannerTitle($data['banner_title']);
                $new_banner->setBannerSubtitle($data['banner_subtitle']);
                $new_banner->setBannerContent($data['banner_content']);
                $new_banner->setBannerLink($data['banner_link']);
                $new_banner->setBannerImage($data['banner_image']);
                $new_banner->setBannerImageMobile($data['banner_image_mobile']);
                $new_banner->setBannerOrder($data['banner_order']);
                $new_banner->setBannerActive($data['banner_active']);
                $new_banner->setBannerIsHome($data['banner_is_home']);
                $result = $new_banner->save();
                if ($result === false) {
                    $message = "Create Banner Fail !";
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                } else {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Banner Success');
                    $old_data = array();
                    $new_data = $data;
                    $message = '';
                    $data_log = json_encode(array('forexcec_banner' => array($new_banner->getBannerId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $new_banner->getBannerId());
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-banner");
            }
        }
        $banner = new Banner();
        $select_controller = $banner->getControllerCombobox($data['banner_controller']);
        $select_type = $banner->getTypeCombobox($data['banner_type']);
        $messages['status'] = 'border-red';
        $this->view->setVars(array(
            'formData' => $data,
            'messages' => $messages,
            'select_controller' => $select_controller,
            'select_type' => $select_type
        ));
    }

    public function editAction()
    {
        $id_banner = $this->request->getQuery('id');
        $checkID = new Validator();
        if (!$checkID->validInt($id_banner)) {
            return $this->response->redirect('notfound');
        }

        $banner_model = ForexcecBanner::findFirstById($id_banner);
        if (empty($banner_model)) {
            return $this->response->redirect('notfound');
        }
        $arr_language = array();
        $arr_translate = array();
        $messages = array();
        $data_post = array(
            'banner_controller' => '',
            'banner_type' => '',
            'banner_title' => '',
            'banner_subtitle' => '',
            'banner_content' => '',
            'banner_link' => '',
            'banner_image' => '',
            'banner_image_mobile' => '',
            'banner_order' => '',
            'banner_is_home' => '',
            'banner_active' => '',
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
            $data_old = array();
            $data_new = array();
            if (isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if ($save_mode != 'general') {
                $data_post['banner_title'] = $this->request->getPost('txtTitle');
                $data_post['banner_subtitle'] = $this->request->getPost('txtSubtitle');
                $data_post['banner_content'] = $this->request->getPost('txtContent');
                $data_post['banner_image'] = $this->request->getPost('txtImage', array('string', 'trim'));
                $data_post['banner_image_mobile'] = $this->request->getPost('txtImageMobile', array('string', 'trim'));
                if (empty($data_post['banner_content'])) {
                    $messages['content'] = 'Content field is required.';
                }
            } else {
                $data_post = array(
                    'banner_controller' => $this->request->getPost('slcController'),
                    'banner_type' => $this->request->getPost('slcType'),
                    'banner_link' => $this->request->getPost('txtLink', array('string', 'trim')),
                    'banner_order' => $this->request->getPost('txtOrder', 'trim'),
                    'banner_active' => $this->request->getPost('radActive'),
                    'banner_is_home' => $this->request->getPost('radIsHome'),
                );
                if ($data_post['banner_controller'] == "") {
                    $messages['controller'] = 'Controller is required.';
                }
                if (empty($data_post['banner_order'])) {
                    $messages["order"] = "Order field is required.";
                } else if (!is_numeric($data_post["banner_order"])) {
                    $messages["order"] = "Order is not valid ";
                }
            }
            if (empty($messages)) {
                switch ($save_mode) {
                    case 'general':
                        $data_old = array(
                            'banner_article_keyword' => $banner_model->getBannerArticleKeyword(),
                            'banner_controller' => $banner_model->getBannerController(),
                            'banner_type' => $banner_model->getBannerType(),
                            'banner_link' => $banner_model->getBannerLink(),
                            'banner_order' => $banner_model->getBannerOrder(),
                            'banner_active' => $banner_model->getBannerActive(),
                        );
                        $banner_controller = Banner::getValue($data_post['banner_controller'], Banner::CONTROLLER);
                        $banner_article_keyword = Banner::getValue($data_post['banner_controller'], Banner::ARTICLE);
                        $banner_model->setBannerController($banner_controller);
                        $banner_model->setBannerType($data_post['banner_type']);
                        $banner_model->setBannerArticleKeyword($banner_article_keyword);
                        $banner_model->setBannerLink($data_post['banner_link']);
                        $banner_model->setBannerOrder($data_post['banner_order']);
                        $banner_model->setBannerActive($data_post['banner_active']);
                        $banner_model->setBannerIsHome($data_post['banner_is_home']);
                        $result = $banner_model->update();
                        $info = "General";
                        $data_new = array(
                            'banner_article_keyword' => $banner_model->getBannerArticleKeyword(),
                            'banner_controller' => $banner_model->getBannerController(),
                            'banner_type' => $banner_model->getBannerType(),
                            'banner_link' => $banner_model->getBannerLink(),
                            'banner_order' => $banner_model->getBannerOrder(),
                            'banner_active' => $banner_model->getBannerActive(),
                            'banner_is_home' => $banner_model->getBannerIsHome(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old['banner_title'] = $banner_model->getBannerTitle();
                        $data_old['banner_subtitle'] = $banner_model->getBannerSubtitle();
                        $data_old['banner_content'] = $banner_model->getBannerContent();
                        $data_old['banner_image'] = $banner_model->getBannerImage();
                        $data_old['banner_image_mobile'] = $banner_model->getBannerImageMobile();
                        $banner_model->setBannerTitle($data_post['banner_title']);
                        $banner_model->setBannerSubtitle($data_post['banner_subtitle']);
                        $banner_model->setBannerContent($data_post['banner_content']);
                        $banner_model->setBannerImage($data_post['banner_image']);
                        $banner_model->setBannerImageMobile($data_post['banner_image_mobile']);
                        $result = $banner_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new['banner_title'] = $banner_model->getBannerTitle();
                        $data_new['banner_subtitle'] = $banner_model->getBannerSubtitle();
                        $data_new['banner_content'] = $banner_model->getBannerContent();
                        $data_new['banner_image'] = $banner_model->getBannerImage();
                        $data_new['banner_image_mobile'] = $banner_model->getBannerImageMobile();
                        break;
                    default:
                        $banner_lang_model = BannerLang::findFirstByIdAndLang($id_banner, $save_mode);
                        if (!$banner_lang_model) {
                            $banner_lang_model = new ForexcecBannerLang();
                            $banner_lang_model->setBannerId($id_banner);
                            $banner_lang_model->setBannerLangCode($save_mode);
                        }
                        $data_old = array(
                            'banner_lang_code' => $banner_lang_model->getBannerLangCode(),
                            'banner_title' => $banner_lang_model->getBannerTitle(),
                            'banner_subtitle' => $banner_lang_model->getBannerSubtitle(),
                            'banner_content' => $banner_lang_model->getBannerContent(),
                            'banner_image' => $banner_lang_model->getBannerImage(),
                            'banner_image_mobile' => $banner_lang_model->getBannerImageMobile(),
                        );
                        $banner_lang_model->setBannerTitle($data_post['banner_title']);
                        $banner_lang_model->setBannerSubtitle($data_post['banner_subtitle']);
                        $banner_lang_model->setBannerContent($data_post['banner_content']);
                        $banner_lang_model->setBannerImage($data_post['banner_image']);
                        $banner_lang_model->setBannerImageMobile($data_post['banner_image_mobile']);
                        $result = $banner_lang_model->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'banner_lang_code' => $banner_lang_model->getBannerLangCode(),
                            'banner_title' => $banner_lang_model->getBannerTitle(),
                            'banner_subtitle' => $banner_lang_model->getBannerSubtitle(),
                            'banner_content' => $banner_lang_model->getBannerContent(),
                            'banner_image' => $banner_lang_model->getBannerImage(),
                            'banner_image_mobile' => $banner_lang_model->getBannerImageMobile(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Banner success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('forexcec_banner' => array($id_banner => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $id_banner);
                } else {
                    $messages = array(
                        'message' => "Update Banner fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'banner_title' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['banner_title'] : $banner_model->getBannerTitle(),
            'banner_subtitle' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['banner_subtitle'] : $banner_model->getBannerSubtitle(),
            'banner_content' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['banner_content'] : $banner_model->getBannerContent(),
            'banner_image' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['banner_image'] : $banner_model->getBannerImage(),
            'banner_image_mobile' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['banner_image_mobile'] : $banner_model->getBannerImageMobile(),
        );
        $arr_translate[$lang_default] = $item;
        $arr_banner_lang = ForexcecBannerLang::findById($id_banner);
        foreach ($arr_banner_lang as $banner_language) {
            $item = array(
                'banner_title' => ($save_mode === $banner_language->getBannerLangCode()) ? $data_post['banner_title'] : $banner_language->getBannerTitle(),
                'banner_subtitle' => ($save_mode === $banner_language->getBannerLangCode()) ? $data_post['banner_subtitle'] : $banner_language->getBannerSubtitle(),
                'banner_content' => ($save_mode === $banner_language->getBannerLangCode()) ? $data_post['banner_content'] : $banner_language->getBannerContent(),
                'banner_image' => ($save_mode === $banner_language->getBannerLangCode()) ? $data_post['banner_image'] : $banner_language->getBannerImage(),
                'banner_image_mobile' => ($save_mode === $banner_language->getBannerLangCode()) ? $data_post['banner_image_mobile'] : $banner_language->getBannerImageMobile(),
            );
            $arr_translate[$banner_language->getBannerLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'banner_title' => $data_post['banner_title'],
                'banner_subtitle' => $data_post['banner_subtitle'],
                'banner_content' => $data_post['banner_content'],
                'banner_image' => $data_post['banner_image'],
                'banner_image_mobile' => $data_post['banner_image_mobile'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'banner_id' => $banner_model->getBannerId(),
            'banner_controller' => ($save_mode === 'general') ? $data_post['banner_controller'] : Banner::getItem($banner_model->getBannerController(), $banner_model->getBannerArticleKeyword()),
            'banner_type' => ($save_mode === 'general') ? $data_post['banner_type'] : $banner_model->getBannerType(),
            'banner_link' => ($save_mode === 'general') ? $data_post['banner_link'] : $banner_model->getBannerLink(),
            'banner_order' => ($save_mode === 'general') ? $data_post['banner_order'] : $banner_model->getBannerOrder(),
            'banner_active' => ($save_mode === 'general') ? $data_post['banner_active'] : $banner_model->getBannerActive(),
            'banner_is_home' => ($save_mode === 'general') ? $data_post['banner_is_home'] : $banner_model->getBannerIsHome(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_default' => $lang_default,
            'lang_current' => $lang_current
        );

        $banner = new Banner();
        $select_controller = $banner->getControllerCombobox($formData['banner_controller']);
        $select_type = $banner->getTypeCombobox($formData['banner_type']);

        $messages['status'] = 'border-red';
        $this->view->setVars(array(
            'formData' => $formData,
            'messages' => $messages,
            'select_controller' => $select_controller,
            'select_type' => $select_type
        ));
    }

    public function deleteAction()
    {
        $banner_checked = $this->request->getPost("item");
        $msg_result = array();
        if (!empty($banner_checked)) {
            $occ_log = array();
            foreach ($banner_checked as $id) {
                $banner_item = ForexcecBanner::findFirstById($id);
                if ($banner_item) {
                    if ($banner_item->delete() === false) {
                        $message_delete = 'Can\'t delete banner Title = ' . $banner_item->getBannerTitle();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = array(
                            'banner_id' => $banner_item->getBannerId(),
                            'banner_controller' => $banner_item->getBannerController(),
                            'banner_article_keyword' => $banner_item->getBannerArticleKeyword(),
                            'banner_title' => $banner_item->getBannerTitle(),
                            'banner_subtitle' => $banner_item->getBannerSubtitle(),
                            'banner_content' => $banner_item->getBannerContent(),
                            'banner_link' => $banner_item->getBannerLink(),
                            'banner_image' => $banner_item->getBannerImage(),
                            'banner_image_mobile' => $banner_item->getBannerImageMobile(),
                            'banner_order' => $banner_item->getBannerOrder(),
                            'banner_active' => $banner_item->getBannerActive(),
                        );
                        $occ_log[$id] = $old_data;
                        BannerLang::deleteById($id);
                    }
                }
            }
            if (count($occ_log) > 0) {
                $message_delete = 'Delete ' . count($occ_log) . ' banner successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
                $message = '';
                $data_log = json_encode(array('forexcec_banner' => $occ_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect("/list-banner");
        }
    }
}

