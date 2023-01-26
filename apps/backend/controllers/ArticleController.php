<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecArticle;
use Forexceccom\Models\ForexcecArticleLang;
use Forexceccom\Models\ForexcecLanguage;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\Article;
use Forexceccom\Repositories\ArticleLang;
use Forexceccom\Repositories\Country;
use Forexceccom\Repositories\Location;
use Forexceccom\Repositories\Type;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\NativeArray;

class ArticleController extends ControllerBase
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
        if (empty($location_country_code)) {
            $location_country_code = strtoupper($this->globalVariable->global['code']);
        }
        if ($location_country_code == 'all') {
            $selectAll = "OR a.article_location_country_code != ''";
        }
//        if ($location_country_code != strtoupper($this->globalVariable->global['code'])) {
//            $location_country_model = Country::findByCode($location_country_code);
//            if (empty($location_country_model)) {
//                return $this->response->redirect('notfound');
//            }
//        }
        $result = array();
        $data = $this->getParameter($location_country_code, $selectAll);
        $list_article = $this->modelsManager->executeQuery($data['sql'], $data['para']);
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
        $type_search = $this->request->get("slType");
        $select_type = $type->getType("", 0, $type_search);
        $lang_search = isset($data["para"]["lang_code"]) ? $data["para"]["lang_code"] : $this->globalVariable->defaultLanguage;
        $country = isset($data["para"]["country"]) ? $data["para"]["country"] : '';
        if ($list_article && sizeof($list_article) > 0) {
            if ($lang_search != $this->globalVariable->defaultLanguage) {
                foreach ($list_article as $item) {
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ForexcecArticle(), array_merge($item->a->toArray(), $item->al->toArray()));
                }
            } else {
                foreach ($list_article as $item) {
                    $result[] = \Phalcon\Mvc\Model::cloneResult(new ForexcecArticle(), $item->toArray());
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
                    $message_log = 'forexcec_article';
                    foreach ($list_article as $item) {

                        $t = 0;
                        $change_field = array();

                        $temp = str_replace($keyword, $replace, $item->getArticleName());
                        if ($temp != $item->getArticleName()) {
                            $t++;
                            $item->setArticleName($temp);
                            array_push($change_field, 'article_name');
                        }

                        $temp = str_replace($keyword, $replace, $item->getArticleTitle());
                        if ($temp != $item->getArticleTitle()) {
                            $t++;
                            $item->setArticleTitle($temp);
                            array_push($change_field, 'article_title');
                        }

                        $temp = str_replace($keyword, $replace, $item->getArticleMetaKeyword());
                        if ($temp != $item->getArticleMetaKeyword()) {
                            $t++;
                            $item->setArticleMetaKeyword($temp);
                            array_push($change_field, 'article_meta_keyword');
                        }

                        $temp = str_replace($keyword, $replace, $item->getArticleMetaDescription());
                        if ($temp != $item->getArticleMetaDescription()) {
                            $t++;
                            $item->setArticleMetaDescription($temp);
                            array_push($change_field, 'article_meta_description');
                        }

                        $temp = str_replace($keyword, $replace, $item->getArticleSummary());
                        if ($temp != $item->getArticleSummary()) {
                            $t++;
                            $item->setArticleSummary($temp);
                            array_push($change_field, 'article_summary');
                        }

                        $temp = str_replace($keyword, $replace, $item->getArticleContent());
                        if ($temp != $item->getArticleContent()) {
                            $t++;
                            $item->setArticleContent($temp);
                            array_push($change_field, 'article_content');
                        }

                        $res = false;
                        if ($t > 0) {
                            $res = $item->update();
                        }
                        if ($res) {
                            $total_success++;
                            $key_log = 'id: ' . $item->getArticleId() . ', location_country_code: ' . $item->getArticleLocationCountryCode();
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
                    $message_log = 'forexcec_article_lang';
                    foreach ($list_article as $item) {

                        $t = 0;
                        $change_field = array();

                        $temp = str_replace($keyword, $replace, $item->al->getArticleName());
                        if ($temp != $item->al->getArticleName()) {
                            $t++;
                            $item->al->setArticleName($temp);
                            array_push($change_field, 'article_name');
                        }

                        $temp = str_replace($keyword, $replace, $item->al->getArticleTitle());
                        if ($temp != $item->al->getArticleTitle()) {
                            $t++;
                            $item->al->setArticleTitle($temp);
                            array_push($change_field, 'article_title');
                        }

                        $temp = str_replace($keyword, $replace, $item->al->getArticleMetaKeyword());
                        if ($temp != $item->al->getArticleMetaKeyword()) {
                            $t++;
                            $item->al->setArticleMetaKeyword($temp);
                            array_push($change_field, 'article_meta_keyword');
                        }

                        $temp = str_replace($keyword, $replace, $item->al->getArticleMetaDescription());
                        if ($temp != $item->al->getArticleMetaDescription()) {
                            $t++;
                            $item->al->setArticleMetaDescription($temp);
                            array_push($change_field, 'article_meta_description');
                        }

                        $temp = str_replace($keyword, $replace, $item->al->getArticleSummary());
                        if ($temp != $item->al->getArticleSummary()) {
                            $t++;
                            $item->al->setArticleSummary($temp);
                            array_push($change_field, 'article_summary');
                        }

                        $temp = str_replace($keyword, $replace, $item->al->getArticleContent());
                        if ($temp != $item->al->getArticleContent()) {
                            $t++;
                            $item->al->setArticleContent($temp);
                            array_push($change_field, 'article_content');
                        }

                        $res = false;
                        if ($t > 0) {
                            $res = $item->al->update();
                        }
                        if ($res) {
                            $total_success++;
                            $key_log = 'id: ' . $item->al->getArticleId() . ', lang_code: ' . $item->al->getArticleLangCode() . ', location_country_code: ' . $item->al->getArticleLocationCountryCode();
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
    }

    public function createAction()
    {
        $location_country_code = $this->globalVariable->global['code'];
        $data = array('id' => -1, 'active' => 'Y', 'is_home' => 'N', 'order' => 1, 'type_id' => 0);
        $messages = array();
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'type_id' => $this->request->getPost("slcType"),
                'name' => $this->request->getPost("txtName", array('string', 'trim')),
                'icon' => $this->request->getPost("txtIcon", array('string', 'trim')),
                'keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'title' => $this->request->getPost("txtTitle", array('string', 'trim')),
                'meta_keyword' => $this->request->getPost("txtMetakey", array('string', 'trim')),
                'meta_description' => $this->request->getPost("txtMetades", array('string', 'trim')),
                'meta_image' => $this->request->getPost("txtMetaImage"),
                'summary' => $this->request->getPost("txtSummary"),
                'content' => $this->request->getPost("txtContent"),
                'order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'active' => $this->request->getPost("radActive"),
                'is_home' => $this->request->getPost("radIsHomeActive"),
                'location_country_code' => $location_country_code,
            );
            if ($data["type_id"] == '-1') {
                $messages["type_id"] = "Type field is required.";
            }
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
            } elseif (Article::checkKeywordByCountryAndLocationCountry($data['keyword'], $data['type_id'], -1, $data['location_country_code'])) {
                $messages["keyword"] = "Keyword is exists.";
            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $new_type = new ForexcecArticle();
                $new_type->setArticleTypeId($data["type_id"]);
                $new_type->setArticleName($data["name"]);
                $new_type->setArticleIcon($data["icon"]);
                $new_type->setArticleKeyword($data["keyword"]);
                $new_type->setArticleTitle($data["title"]);
                $new_type->setArticleMetaKeyword($data["meta_keyword"]);
                $new_type->setArticleMetaDescription($data["meta_description"]);
                $new_type->setArticleMetaImage($data["meta_image"]);
                $new_type->setArticleSummary($data["summary"]);
                $new_type->setArticleContent($data["content"]);
                $new_type->setArticleOrder($data["order"]);
                $new_type->setArticleActive($data["active"]);
                $new_type->setArticleIsHome($data["is_home"]);
                $new_type->setArticleInsertTime($this->globalVariable->curTime);
                $new_type->setArticleUpdateTime($this->globalVariable->curTime);
                $new_type->setArticleLocationCountryCode(strtolower($data["location_country_code"]));
                $result = $new_type->save();

                $message = "We can't store your info now: " . "<br/>";
                if ($result === true) {
                    $message = 'Create the article ID: ' . $new_type->getArticleId() . ' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('forexcec_article' => array($new_type->getArticleId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $new_type->getArticleId());
                } else {
                    foreach ($new_type->getMessages() as $msg) {
                        $message .= $msg . "<br/>";
                    }
                    $msg_result = array('status' => 'error', 'msg' => $message);
                }
                $this->session->set('msg_result', $msg_result);
                $this->response->redirect("/list-article");
                return;

            }
        }
        $type = new Type();
        $select_type = $type->getType("", 0, $data["type_id"]);
        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
        ]);
    }

    public function editAction()
    {
        $article_id = $this->request->get('id');
        $location_country_code = $this->request->get('slcLocationCountry');
        $lang_current = $this->request->get('slcLang');
        if ($location_country_code != strtoupper($this->globalVariable->global['code'])) {
            $country_model = Country::findByCode($location_country_code);
            if (empty($country_model)) {
                return $this->response->redirect('notfound');
            }
        }

        $checkID = new Validator();
        if (!$checkID->validInt($article_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $article_model = Article::getByID($article_id, $location_country_code);
        if (empty($article_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array(
            'article_id' => -1,
            'article_type_id' => '',
            'article_name' => '',
            'article_icon' => '',
            'article_keyword' => '',
            'article_title' => '',
            'article_meta_keyword' => '',
            'article_meta_description' => '',
            'article_meta_image' => '',
            'article_summary' => '',
            'article_content' => '',
            'article_order' => '',
            'article_active' => '',
            'article_is_home' => '',
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
                $data_post['article_name'] = $this->request->get("txtName", array('string', 'trim'));
                $data_post['article_title'] = $this->request->get("txtTitle", array('string', 'trim'));
                $data_post['article_meta_keyword'] = $this->request->get("txtMetaKey", array('string', 'trim'));
                $data_post['article_meta_description'] = $this->request->get("txtMetaDesc", array('string', 'trim'));
                $data_post['article_icon'] = $this->request->getPost('txtIcon', array('string', 'trim'));
                $data_post['article_meta_image'] = $this->request->get("txtMetaImage", array('string', 'trim'));
                $data_post['article_summary'] = $this->request->get("txtSummary");
                $data_post['article_content'] = $this->request->get("txtContent");
                if (empty($data_post['article_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }
                if (empty($data_post['article_title'])) {
                    $messages[$save_mode]['title'] = 'Title field is required.';
                }
                if (empty($data_post['article_meta_keyword'])) {
                    $messages[$save_mode]['meta_keyword'] = 'Meta keyword field is required.';
                }
                if (empty($data_post['article_meta_description'])) {
                    $messages[$save_mode]['meta_description'] = 'Meta description field is required.';
                }
            } else {
                $data_post['article_type_id'] = $this->request->getPost('slcType');
                $data_post['article_keyword'] = $this->request->getPost('txtKeyword', array('string', 'trim'));
                $data_post['article_active'] = $this->request->getPost('radActive');
                $data_post['article_is_home'] = $this->request->getPost('radIsHomeActive');
                $data_post['article_order'] = $this->request->getPost('txtOrder', array('string', 'trim'));
                if ($data_post['article_type_id'] == 0) {
                    $messages['type'] = 'Type is required.';
                }
                if (empty($data_post['article_keyword'])) {
                    $messages['keyword'] = 'Keyword field is required.';
                } elseif (Article::checkKeywordByCountryAndLocationCountry($data_post['article_keyword'], $data_post['article_type_id'], $article_id, $location_country_code)) {
                    $messages["keyword"] = "Keyword is exists.";
                }
                if (empty($data_post['article_order'])) {
                    $messages['order'] = 'Order field is required.';
                } else if (!is_numeric($data_post['article_order'])) {
                    $messages['order'] = 'Order is not valid.';
                }
            }
            if (empty($messages)) {
                switch ($save_mode) {
                    case ForexcecLanguage::GENERAL:
                        $data_old = array(
                            'article_type_id' => $article_model->getArticleTypeId(),
                            'article_keyword' => $article_model->getArticleKeyword(),
                            'article_order' => $article_model->getArticleOrder(),
                            'article_active' => $article_model->getArticleActive(),
                            'article_is_home' => $article_model->getArticleIsHome(),
                        );
                        $article_model->setArticleTypeId($data_post['article_type_id']);
                        $article_model->setArticleKeyword($data_post['article_keyword']);
                        $article_model->setArticleOrder($data_post['article_order']);
                        $article_model->setArticleActive($data_post['article_active']);
                        $article_model->setArticleIsHome($data_post['article_is_home']);
                        $article_model->setArticleUpdateTime($this->globalVariable->curTime);
                        $result = $article_model->update();
                        $info = ForexcecLanguage::GENERAL;
                        $data_new = array(
                            'article_type_id' => $article_model->getArticleTypeId(),
                            'article_keyword' => $article_model->getArticleKeyword(),
                            'article_order' => $article_model->getArticleOrder(),
                            'article_active' => $article_model->getArticleActive(),
                            'article_is_home' => $article_model->getArticleIsHome(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'article_name' => $article_model->getArticleName(),
                            'article_title' => $article_model->getArticleTitle(),
                            'article_meta_keyword' => $article_model->getArticleMetaKeyword(),
                            'article_meta_description' => $article_model->getArticleMetaDescription(),
                            'article_icon' => $article_model->getArticleIcon(),
                            'article_meta_image' => $article_model->getArticleMetaImage(),
                            'article_summary' => $article_model->getArticleSummary(),
                            'article_content' => $article_model->getArticleContent(),
                        );
                        $article_model->setArticleName($data_post['article_name']);
                        $article_model->setArticleTitle($data_post['article_title']);
                        $article_model->setArticleMetaKeyword($data_post['article_meta_keyword']);
                        $article_model->setArticleMetaDescription($data_post['article_meta_description']);
                        $article_model->setArticleIcon($data_post['article_icon']);
                        $article_model->setArticleMetaImage($data_post['article_meta_image']);
                        $article_model->setArticleSummary($data_post['article_summary']);
                        $article_model->setArticleContent($data_post['article_content']);
                        $article_model->setArticleUpdateTime($this->globalVariable->curTime);
                        $result = $article_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'article_name' => $article_model->getArticleName(),
                            'article_title' => $article_model->getArticleTitle(),
                            'article_meta_keyword' => $article_model->getArticleMetaKeyword(),
                            'article_meta_description' => $article_model->getArticleMetaDescription(),
                            'article_icon' => $article_model->getArticleIcon(),
                            'article_meta_image' => $article_model->getArticleMetaImage(),
                            'article_summary' => $article_model->getArticleSummary(),
                            'article_content' => $article_model->getArticleContent(),
                        );
                        break;
                    default:
                        $content_article_lang = ArticleLang::findFirstByIdAndLocationCountryCodeAndLang($article_id, $location_country_code, $save_mode);
                        if ($content_article_lang) {
                            $data_old = $content_article_lang->toArray();
                        } else {
                            $content_article_lang = new ForexcecArticleLang();
                            $content_article_lang->setArticleId($article_id);
                            $content_article_lang->setArticleLocationCountryCode(strtolower($location_country_code));
                            $content_article_lang->setArticleLangCode($save_mode);
                        }

                        $content_article_lang->setArticleName($data_post['article_name']);
                        $content_article_lang->setArticleTitle($data_post['article_title']);
                        $content_article_lang->setArticleMetaKeyword($data_post['article_meta_keyword']);
                        $content_article_lang->setArticleMetaDescription($data_post['article_meta_description']);
                        $content_article_lang->setArticleIcon($data_post['article_icon']);
                        $content_article_lang->setArticleMetaImage($data_post['article_meta_image']);
                        $content_article_lang->setArticleSummary($data_post['article_summary']);
                        $content_article_lang->setArticleContent($data_post['article_content']);
                        $result = $content_article_lang->save();
                        $info = $arr_language[$save_mode];
                        $data_new = $content_article_lang->toArray();
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Article success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('forexcec_article_lang' => array($article_id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $article_id);
                } else {
                    $messages = array(
                        'message' => "Update Article fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'article_id' => $article_model->getArticleId(),
            'artical_location_country_code' => $article_model->getArticleLocationCountryCode(),
            'article_name' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['article_name'] : $article_model->getArticleName(),
            'article_title' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['article_title'] : $article_model->getArticleTitle(),
            'article_meta_keyword' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['article_meta_keyword'] : $article_model->getArticleMetaKeyword(),
            'article_meta_description' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['article_meta_description'] : $article_model->getArticleMetaDescription(),
            'article_meta_image' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['article_meta_image'] : $article_model->getArticleMetaImage(),
            'article_summary' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['article_summary'] : $article_model->getArticleSummary(),
            'article_content' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['article_content'] : $article_model->getArticleContent(),
            'article_icon' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['article_icon'] : $article_model->getArticleIcon(),
        );
        $arr_translate[$lang_default] = $item;
        $arr_article_lang = ForexcecArticleLang::findByIdAndLocationCountryCode($article_id, $location_country_code);
        foreach ($arr_article_lang as $article_lang) {
            $item = array(
                'article_id' => $article_lang->getArticleId(),
                'article_name' => ($save_mode === $article_lang->getArticleLangCode()) ? $data_post['article_name'] : $article_lang->getArticleName(),
                'article_title' => ($save_mode === $article_lang->getArticleLangCode()) ? $data_post['article_title'] : $article_lang->getArticleTitle(),
                'article_meta_keyword' => ($save_mode === $article_lang->getArticleLangCode()) ? $data_post['article_meta_keyword'] : $article_lang->getArticleMetaKeyword(),
                'article_meta_description' => ($save_mode === $article_lang->getArticleLangCode()) ? $data_post['article_meta_description'] : $article_lang->getArticleMetaDescription(),
                'article_icon' => ($save_mode === $article_lang->getArticleLangCode()) ? $data_post['article_icon'] : $article_lang->getArticleIcon(),
                'article_meta_image' => ($save_mode === $article_lang->getArticleLangCode()) ? $data_post['article_meta_image'] : $article_lang->getArticleMetaImage(),
                'article_summary' => ($save_mode === $article_lang->getArticleLangCode()) ? $data_post['article_summary'] : $article_lang->getArticleSummary(),
                'article_content' => ($save_mode === $article_lang->getArticleLangCode()) ? $data_post['article_content'] : $article_lang->getArticleContent(),
            );
            $arr_translate[$article_lang->getArticleLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'article_id' => -1,
                'article_name' => $data_post['article_name'],
                'article_title' => $data_post['article_title'],
                'article_meta_keyword' => $data_post['article_meta_keyword'],
                'article_meta_description' => $data_post['article_meta_description'],
                'article_icon' => $data_post['article_icon'],
                'article_meta_image' => $data_post['article_meta_image'],
                'article_summary' => $data_post['article_summary'],
                'article_content' => $data_post['article_content'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'article_id' => $article_model->getArticleId(),
            'article_type_id' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['article_type_id'] : $article_model->getArticleTypeId(),
            'article_keyword' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['article_keyword'] : $article_model->getArticleKeyword(),
            'article_order' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['article_order'] : $article_model->getArticleOrder(),
            'article_active' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['article_active'] : $article_model->getArticleActive(),
            'article_is_home' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['article_is_home'] : $article_model->getArticleIsHome(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_default' => $lang_default,
            'lang_current' => $lang_current,
        );

        $type = new Type();
        $select_type = $type->getType("", 0, $formData['article_type_id']);
        $messages['status'] = 'border-red';
        $select_country = Country::getCountryGlobalComboBox($location_country_code);
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
            'select_type' => $select_type,
            'select_country' => $select_country,
            'location_country_code' => $location_country_code,
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
                    $item = ArticleLang::findFirstByIdAndLocationCountryCodeAndLang($id, $location_country_code, $lang);
                } else {
                    $item = ForexcecArticle::findFirstByIdAndLocationCountryCode($id, $location_country_code);
                }
                if ($item) {

                    if ($item->delete() === false) {
                        $message_delete = 'Can\'t delete the article Name = ' . $item->getArticleName();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = $item->toArray();
                        $forexcec_log[$id] = $old_data;
                        if (strtolower($location_country_code) == $this->globalVariable->global['code']) {
                            ArticleLang::deleteById($id);
                        }
                        if ($lang == $this->globalVariable->defaultLanguage) {
                            ArticleLang::deleteByIdAndLocationCountryCode($id, $location_country_code);
                        }
                    }
                }
            }
            if (count($forexcec_log) > 0) {
                $message_delete = 'Delete ' . count($forexcec_log) . ' article successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
                $message = '';
                $data_log = json_encode(array('occ_content_article' => $forexcec_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect('/list-article?slcLocationCountry=' . $location_country_code . '&slcLang=' . $lang);
        }
    }

    private function getParameter($location_country_code, $selectAll)
    {
        $lang = $this->request->get("slcLang", array('string', 'trim'));
        $langCode = !empty($lang) ? $lang : $this->globalVariable->defaultLanguage;
        $match = trim($this->request->get("radMatch"));
        if ($match == '') {
            $match = 'notmatch';
        }
        $this->dispatcher->setParam("radMatch", $match);
        $keyword = trim($this->request->get("txtSearch"));
        $type = $this->request->get("slType");
        $this->dispatcher->setParam("slcLang", $langCode);
        $arrParameter = array('location_country_code' => $location_country_code);
        $validator = new Validator();
        if ($langCode === $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.* FROM Forexceccom\Models\ForexcecArticle a WHERE 1";
            if (!empty($keyword)) {
                if ($validator->validInt($keyword)) {
                    $sql .= " AND (a.article_id = :number: OR a.article_lession_id = :number:)";
                    $arrParameter['number'] = $keyword;
                } else {
                    if ($match == 'match') {
                        $sql .= " AND (a.article_name =:keyword: OR a.article_title =:keyword:
                                     OR a.article_meta_keyword =:keyword: OR a.article_meta_description =:keyword:
                                     OR a.article_summary =:keyword: OR a.article_content =:keyword:
                                    )";
                    } else {
                        $sql .= " AND (a.article_name like CONCAT('%',:keyword:,'%') OR a.article_title like CONCAT('%',:keyword:,'%')
                                     OR a.article_meta_keyword like CONCAT('%',:keyword:,'%') OR a.article_meta_description like CONCAT('%',:keyword:,'%')
                                     OR a.article_summary like CONCAT('%',:keyword:,'%') OR a.article_content like CONCAT('%',:keyword:,'%')
                                       )";
                    }
                    $arrParameter['keyword'] = $keyword;
                }
                $this->dispatcher->setParam("txtSearch", $keyword);
            }
        } else {
            $sql = "SELECT a.*,al.* FROM Forexceccom\Models\ForexcecArticle a
                    INNER JOIN \Forexceccom\Models\ForexcecArticleLang al
                                ON al.article_id = a.article_id AND al.article_lang_code = :lang_code: AND al.article_location_country_code = a.article_location_country_code                           
                    WHERE 1";
            $arrParameter['lang_code'] = $langCode;
            if (!empty($keyword)) {
                if ($validator->validInt($keyword)) {
                    $sql .= " AND (a.article_id = :number:)";
                    $arrParameter['number'] = $keyword;
                } else {
                    if ($match == 'match') {
                        $sql .= " AND (al.article_name =:keyword: OR al.article_title =:keyword:
                                    OR al.article_meta_keyword =:keyword: OR al.article_meta_description =:keyword:
                                    OR al.article_summary =:keyword: OR al.article_content =:keyword:
                                  )";
                    } else {
                        $sql .= " AND (al.article_name like CONCAT('%',:keyword:,'%') OR al.article_title like CONCAT('%',:keyword:,'%') 
                                    OR al.article_meta_keyword like CONCAT('%',:keyword:,'%') OR al.article_meta_description like CONCAT('%',:keyword:,'%')
                                    OR al.article_summary like CONCAT('%',:keyword:,'%') OR al.article_content like CONCAT('%',:keyword:,'%')
                                         )";
                    }
                    $arrParameter['keyword'] = $keyword;
                }
                $this->dispatcher->setParam("txtSearch", $keyword);
            }
        }
        $validator = new Validator();
        if (!empty($type)) {
            if ($validator->validInt($type) == false) {
                return $this->response->redirect("/notfound");
            }
            $list_type = implode(',',Type::getIdByParent($type));
            $sql .= " AND FIND_IN_SET(a.article_type_id,:list_type:)";
            $arrParameter["list_type"] = $list_type;
            $this->dispatcher->setParam("slType", $type);
        }
        $sql .= " AND (a.article_location_country_code = :location_country_code:" . $selectAll . ") ORDER BY a.article_update_time DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }

    private function getParameterExport()
    {
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecArticle WHERE article_active = 'Y'";
        $keyword = trim($this->request->get("txtSearch"));
        $type = $this->request->get("slType");
        $arrParameter = array();
        $validator = new Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (article_id = :number:)";
                $arrParameter['number'] = $keyword;
            } else {
                $sql .= " AND (article_name like CONCAT('%',:keyword:,'%') OR article_keyword like CONCAT('%',:keyword:,'%')
                OR article_title like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $validator = new Validator();
        if (!empty($type)) {
            if ($validator->validInt($type) == false) {
                $this->response->redirect("/notfound");
                return;
            }
            $sql .= " AND article_type_id = :type_id:";
            $arrParameter["type_id"] = $type;
            $this->dispatcher->setParam("slType", $type);
        }
        $sql .= " ORDER BY article_order ASC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }

    public function updatecontentAction(){
        $this->view->disable();
        $id = $this->request->get('id');
        $locationCountry = $this->request->get('country');
        $lang = $this->request->get('lang');
        if ($lang == $this->globalVariable->defaultLanguage) {
            $article_model = ForexcecArticle::findFirstByIdAndLocationCountryCode($id,$locationCountry);
            if (!$article_model) {
                die(false);
            }
            $article_mode_not_gx = ForexcecArticle::find(array(
                "article_id = :ID: AND article_location_country_code != 'gx'",
                "bind" => array('ID' => $id)
            ));
            foreach ($article_mode_not_gx as $item) {
                $item->setArticleContent($article_model->getArticleContent());
                $item->setArticleName($article_model->getArticleName());
                $item->setArticleTitle($article_model->getArticleTitle());
                $item->setArticleMetaKeyword($article_model->getArticleMetaKeyword());
                $item->setArticleSummary($article_model->getArticleSummary());
                $item->setArticleMetaDescription($article_model->getArticleMetaDescription());
                $item->save();
            }
            die('success');
        } else {
            $article_lang_model = ArticleLang::findFirstByIdAndLocationCountryCodeAndLang($id,$locationCountry, $lang);
            if (!$article_lang_model) {
                die(false);
            }
            $article_model_lang_not_gx = ForexcecArticleLang::find(array(
                "article_id = :ID: AND article_lang_code = :LANG: AND article_location_country_code != :country:",
                "bind" => array('ID' => $id, 'LANG' => $lang,'country' => $locationCountry)
            ));
            foreach ($article_model_lang_not_gx as $item) {
                $item->setArticleContent($article_lang_model->getArticleContent());
                $item->setArticleName($article_lang_model->getArticleName());
                $item->setArticleTitle($article_lang_model->getArticleTitle());
                $item->setArticleMetaKeyword($article_lang_model->getArticleMetaKeyword());
                $item->setArticleSummary($article_lang_model->getArticleSummary());
                $item->setArticleMetaDescription($article_lang_model->getArticleMetaDescription());
                $item->save();
            }
            die('success');
        }
    }
    public function lessionAction()
    {
           $this->view->disable();

           $i=1;
           $sql="SELECT * FROM \Forexceccom\Models\ForexcecType WHERE type_parent_id = 40 ORDER BY type_order";
           $list_type = $this->modelsManager->executeQuery($sql);

           foreach ($list_type as $item){

               $sql_1="SELECT * FROM \Forexceccom\Models\ForexcecType WHERE type_parent_id = :type_id: ORDER BY type_order";
               $list_type_1 = $this->modelsManager->executeQuery($sql_1, ['type_id' => $item->getTypeId()]);
               foreach ($list_type_1 as $item_2){
                       $sql ="SELECT * FROM \Forexceccom\Models\ForexcecArticle WHERE article_type_id =:type_id_3: ORDER BY article_order" ;
                       $list_article = $this->modelsManager->executeQuery($sql,['type_id_3' => $item_2->getTypeId()]);
                       foreach ($list_article as $item_4){
                           $item_4->setArticleLessionId($i);
                           $item_4->save();
                           $i++;
                       }

               }
           }

           echo $i;
    }
    public function replaceAction()
    {
        $this->view->disable();
        $i=1;
        $sql="SELECT * FROM \Forexceccom\Models\ForexcecType WHERE type_parent_id = 40 ORDER BY type_order";
        $list_type = $this->modelsManager->executeQuery($sql);

        foreach ($list_type as $item){

            $sql_1="SELECT * FROM \Forexceccom\Models\ForexcecType WHERE type_parent_id = :type_id: ORDER BY type_order";
            $list_type_1 = $this->modelsManager->executeQuery($sql_1, ['type_id' => $item->getTypeId()]);
            foreach ($list_type_1 as $item_2){
                $sql ="SELECT * FROM \Forexceccom\Models\ForexcecArticle WHERE article_type_id =:type_id_3: ORDER BY article_order" ;
                $list_article = $this->modelsManager->executeQuery($sql,['type_id_3' => $item_2->getTypeId()]);
                foreach ($list_article as $item_4){
                    $content = $item_4->getArticleContent();
                    $keyword ='<img src="https://dovyy1zxit6rl.cloudfront.net/uploads/sx-1602664268.svg"';
                    $replace ='<img';
                    $content = str_replace($keyword, $replace, $content);
                    $keyword ='<img data-src="';
                    $replace ='<img src="';
                    $content = str_replace($keyword, $replace, $content);
                    $item_4->setArticleContent($content);
                    $item_4->save();
                    $i++;
                }

            }
        }

        echo $i;
    }
}