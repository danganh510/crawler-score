<?php

namespace Forexceccom\Backend\Controllers;


use Forexceccom\Repositories\Language;
use Forexceccom\Repositories\RegisterPage;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Models\ForexcecRegisterPage;

class RegisterpageController extends ControllerBase
{
    public function indexAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $keyword = trim($this->request->get("txtSearch"));
        $domain = $this->request->get('slcDomain');
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecRegisterPage WHERE 1";
        $arrParameter = array();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (page_id = :keyword:) ";
            } else {
                $sql .= " AND ((page_name like CONCAT('%',:keyword:,'%')) OR page_title like CONCAT('%',:keyword:,'%')
                           OR page_keyword like CONCAT('%',:keyword:,'%') OR page_meta_description like CONCAT('%',:keyword:,'%'))";
            }
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if ($domain) {
            $sql .= " AND (page_domain = :domain:) ";
            $arrParameter['domain'] = $domain;
            $this->dispatcher->setParam("slcDomain", $domain);
        }
        $sql .= " ORDER BY page_id  DESC";
        $list_page = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(array(
            'data' => $list_page,
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
        $select_domain = RegisterPage::getRegisterDomainCombobox($domain);
        $this->view->setVars([
            'list_page' => $paginator->getPaginate(),
            'select_domain' => $select_domain
        ]);
    }


    public function createAction()
    {
        $data = array('page_id' => -1, 'page_default' => 'N','page_active' => 'Y','page_lang_code' => '', 'page_order' => 1);
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'page_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'page_title' => $this->request->getPost("txtTitle", array('string', 'trim')),
                'page_keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'page_meta_keyword' => $this->request->getPost("txtMetaKeyword", array('string', 'trim')),
                'page_meta_description' => $this->request->getPost("txtMetaDescription", array('string', 'trim')),
                'page_meta_image' => $this->request->getPost("txtMetaImage", array('string', 'trim')),
                'page_gtm' => $this->request->getPost("txtGtm", array('string', 'trim')),
                'page_domain' => $this->request->getPost("txtDomain", array('string', 'trim')),
                'page_lang_code' => $this->request->getPost("slcLang", array('string', 'trim')),
                'page_content' => trim($this->request->getPost("txtContent")),
                'page_style' => trim($this->request->getPost("txtStyle")),
                'page_default' => $this->request->getPost("radDefault"),
                'page_order' => $this->request->getPost("txtOrder"),
                'page_active' => $this->request->getPost("radActive"),
            );

            if (empty($data["page_name"])) {
                $messages["page_name"] = "Name field is required.";
            }
            if (empty($data["page_title"])) {
                $messages["page_title"] = "Title field is required.";
            }
            if (empty($data["page_keyword"])) {
                $messages["page_keyword"] = "Keyword field is required.";
            }
            if (empty($data["page_meta_keyword"])) {
                $messages["page_meta_keyword"] = "Meta Keyword field is required.";
            }
            if (empty($data["page_meta_description"])) {
                $messages["page_meta_description"] = "Meta Description field is required.";
            }
            if (empty($data["page_order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["page_order"])) {
                $messages["order"] = "Order is not valid ";
            }

            if (count($messages) == 0) {
                $msg_result = array();
                $new_language = new ForexcecRegisterPage();
                $result = $new_language->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Register Page Success');


                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_language->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-register-page");
            }
        }
        $messages["status"] = "border-red";
        $select_lang = Language::getComboReg($data['page_lang_code']);
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_lang' => $select_lang,
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
        $page_model = ForexcecRegisterPage::findFirstById($id);
        if (empty($page_model)) {
            return $this->response->redirect('notfound');
        }
        $data = $page_model->toArray();
        $messages = array();
        if ($this->request->isPost()) {
            $data = array(
                'page_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'page_title' => $this->request->getPost("txtTitle", array('string', 'trim')),
                'page_keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'page_meta_keyword' => $this->request->getPost("txtMetaKeyword", array('string', 'trim')),
                'page_meta_description' => $this->request->getPost("txtMetaDescription", array('string', 'trim')),
                'page_meta_image' => $this->request->getPost("txtMetaImage", array('string', 'trim')),
                'page_gtm' => $this->request->getPost("txtGtm", array('string', 'trim')),
                'page_domain' => $this->request->getPost("txtDomain", array('string', 'trim')),
                'page_lang_code' => $this->request->getPost("slcLang", array('string', 'trim')),
                'page_content' => trim($this->request->getPost("txtContent")),
                'page_style' => trim($this->request->getPost("txtStyle")),
                'page_default' => $this->request->getPost("radDefault"),
                'page_order' => $this->request->getPost("txtOrder"),
                'page_active' => $this->request->getPost("radActive"),
            );

            if (empty($data["page_name"])) {
                $messages["page_name"] = "Name field is required.";
            }
            if (empty($data["page_title"])) {
                $messages["page_title"] = "Title field is required.";
            }
            if (empty($data["page_keyword"])) {
                $messages["page_keyword"] = "Keyword field is required.";
            }
            if (empty($data["page_meta_keyword"])) {
                $messages["page_meta_keyword"] = "Meta Keyword field is required.";
            }
            if (empty($data["page_meta_description"])) {
                $messages["page_meta_description"] = "Meta Description field is required.";
            }
            if (empty($data["page_order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["page_order"])) {
                $messages["order"] = "Order is not valid ";
            }

            if (count($messages) == 0) {
                $msg_result = array();
                $result = $page_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'msg' => 'Edit Register Page Success');
                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($page_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-register-page");
            }
        }
        $messages["status"] = "border-red";
        $select_lang = Language::getComboReg($data['page_lang_code']);

        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_lang' => $select_lang,
        ]);
    }

    public function deleteAction()
    {
        $language_checked = $this->request->getPost("item");
        if (!empty($language_checked)) {
            $msg_result = array();
            $total = 0;
            foreach ($language_checked as $id) {
                $language_item = ForexcecRegisterPage::findFirstById($id);
                if ($language_item) {
                    if ($language_item->delete() === false) {
                        $message_delete = 'Can\'t delete the Language Name = ' . $language_item->getLanguageName();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $total++;
                    }
                }
            }
            if ($total > 0) {
                $message_delete = 'Delete ' . $total . ' Register page successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect("/list-register-page");
        }
    }
}
