<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecTemplateAutoEmail;
use Forexceccom\Repositories\TemplateAutoEmail;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class TemplateautoemailController extends ControllerBase
{
    public function indexAction()
    {

        $current_page = $this->request->getQuery('page');
        $validator = new Validator();
        $keyword = $this->request->get('txtSearch', array('string', 'trim'));
        $form = $this->request->get('slForm', array('string', 'trim'));
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecTemplateAutoEmail WHERE 1";
        $arrParameter = array();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (email_id = :keyword:)";
            } else {
                $sql .= " AND (email_type like CONCAT('%',:keyword:,'%') OR email_subject like CONCAT('%',:keyword:,'%'))";
            }
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if (!empty($form)) {
            $sql .= " AND (email_form = :form:)";
            $arrParameter['form'] = $form;
            $this->dispatcher->setParam("slForm", $form);
        }
        $sql .= " ORDER BY email_id DESC";
        $list_emailtemplate = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(
            array(
                'data' => $list_emailtemplate,
                'limit' => 20,
                'page' => $current_page,
            )
        );
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
        $select_form = TemplateAutoEmail::getComboboxForm($form);
        $this->view->setVars(array(
            'list_emailtemplate' => $paginator->getPaginate(),
            'select_form' => $select_form
        ));
    }

    public function createAction()
    {
        $this->view->pick($this->dispatcher->getControllerName().'/model');
        $data = array('email_id' => -1, 'email_status' => 'Y','email_form' => "");
        if ($this->request->isPost()) {
            $data = array(
                'email_type' => $this->request->getPost("txtType", array('string', 'trim')),
                'email_subject' => $this->request->getPost("txtSubject", array('string', 'trim')),
                'email_content' => $this->request->getPost("txtContent"),
                'email_status' => $this->request->getPost("radStatus"),
                'email_form' => $this->request->getPost("slcForm"),
                'email_day_send' => $this->request->getPost("txtDaySend", array('string', 'trim')),
            );

            $messages = array();
            if (empty($data['email_type'])) {
                $messages['type'] = 'Type field is required.';
            } else {
                if (TemplateAutoEmail::checkKeyword($data['email_type'],$data['email_form'] ,-1)) {
                    $messages['type'] = 'Type is exists.';
                }
            }
            if (!empty($data['email_day_send']) && !is_numeric($data["email_day_send"])) {
                $messages["day_send"] = "Send time is not valid ";
            }
            if (empty($data['email_form'])) {
                $messages['form'] = 'Form field is required.';
            }
            if (count($messages) == 0) {
                $new_emailtemplate = new ForexcecTemplateAutoEmail();
                if ($new_emailtemplate->save($data) === true) {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Email Auto Template Success');
                } else {
                    $msg_result = array('status' => 'error', 'msg' => 'Create Email Auto Template Fail !');
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect('/list-template-auto-email');
            }
        }
        $messages['status'] = 'border-red';
        $select_form = TemplateAutoEmail::getComboboxForm($data['email_form']);
        $this->view->setVars(array(
            'title' => 'Email Auto Create',
            'formData' => $data,
            'messages' => $messages,
            'select_form' => $select_form
        ));
    }

    public function editAction()
    {
        $this->view->pick($this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $email_model = TemplateAutoEmail::findFirstById($id);
        if (!$email_model) {
            return notfound;
        }

        $data = $email_model->toArray();
        if ($this->request->isPost()) {
            $data = array(
                'email_type' => $this->request->getPost("txtType", array('string', 'trim')),
                'email_subject' => $this->request->getPost("txtSubject", array('string', 'trim')),
                'email_content' => $this->request->getPost("txtContent"),
                'email_status' => $this->request->getPost("radStatus"),
                'email_form' => $this->request->getPost("slcForm"),
                'email_day_send' => $this->request->getPost("txtDaySend", array('string', 'trim')),
            );

            $messages = array();
            if (empty($data['email_type'])) {
                $messages['type'] = 'Type field is required.';
            } else {
                if (TemplateAutoEmail::checkKeyword($data['email_type'],$data['email_form'], $id)) {
                    $messages['type'] = 'Type is exists.';
                }
            }
            if (!empty($data['email_day_send']) && !is_numeric($data["email_day_send"])) {
                $messages["day_send"] = "Send time is not valid ";
            }
            if (empty($data['email_form'])) {
                $messages['form'] = 'Form field is required.';
            }
            if (count($messages) == 0) {
                if ($email_model->save($data) === true) {
                    $messages = array('typeMessage' => 'success', 'message' => 'Edit Email Auto Template Success');
                } else {
                    $messages = array('typeMessage' => 'error', 'message' => 'Edit Email Auto Template Fail !');
                }
            }
        }
        $messages['status'] = 'border-red';
        $select_form = TemplateAutoEmail::getComboboxForm($data['email_form']);
        $this->view->setVars(array(
            'title' => 'Email Auto Edit',
            'formData' => $data,
            'messages' => $messages,
            'select_form' => $select_form
        ));
    }

    public function deleteAction()
    {
        $emailtemplate_checked = $this->request->getPost("item");
        if (!empty($emailtemplate_checked)) {
            $total = 0;
            foreach ($emailtemplate_checked as $emailtemplate_id) {
                $emailtemplate_item = TemplateAutoEmail::findFirstById($emailtemplate_id);
                if ($emailtemplate_item) {
                    $msg_result = array();
                    if ($emailtemplate_item->delete() === false) {
                        $message_delete = 'Can\'t delete the Email Template Subject = ' . $emailtemplate_item->getEmailSubject();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $emailtemplate_item->delete();
                        $total++;

                    }
                }
            }
            if ($total > 0) {
                $message_delete = 'Delete ' . $total . ' Email Auto Template successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect("/list-template-auto-email");
        }
    }
}
