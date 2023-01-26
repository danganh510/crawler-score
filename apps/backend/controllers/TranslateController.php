<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecCron;
use Forexceccom\Models\ForexcecTableTranslate;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\Language;
use Forexceccom\Repositories\Translate;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class TranslateController extends ControllerBase
{

    public function indexAction()
    {
        $current_page = $this->request->getQuery('page', 'int');
        $validator = new Validator();
        $keyword = $this->request->get('txtSearch','trim');
        $language = $this->request->get('slcLanguage');

        $sql = "SELECT * FROM Forexceccom\Models\ForexcecTableTranslate WHERE 1";
        $arrParameter = array();
        if(!empty($keyword)){
            if($validator->validInt($keyword)) {
                $sql.=" AND (translate_id = :keyword:) ";
            } else {
                $sql.=" AND (translate_language like CONCAT('%',:keyword:,'%') OR translate_cron like CONCAT('%',:keyword:,'%'))";
            }
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if (!empty($language)){
            $sql.=" AND (translate_language = :language: )";
            $arrParameter['language'] = $language;
            $this->dispatcher->setParam("slcLanguage", $language);
        }

        $sql.=" ORDER BY translate_active,translate_order ASC";
        $list_translate = $this->modelsManager->executeQuery($sql,$arrParameter);

        $paginator = new PaginatorModel(
            [
                'data'  => $list_translate,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $select_lang = Translate::getCombobox($language);
        $this->view->setVars(array(
            'list_translate' => $paginator->getPaginate(),
            'select_lang' => $select_lang,

        ));
    }

    public function createAction()
    {
        $array_models = array();
        $array_models[]= "ForexcecConfig";
        $directory_frontend =__DIR__."/../../models/*Lang.php";
        foreach (glob($directory_frontend) as $controller) {
            $className = basename($controller, '.php');
            array_push($array_models,$className);
        }
        $array_models = Translate::tableNotTranslate($array_models);

        $data = array('active' => 'Y','order' => 1);
        $messages = array();
        $msg_result = array();
        if ($this->request->isPost()){
            $data = array(
                'cron' => str_replace(' ','',$this->request->getPost('txtCron', array('string','trim'))),
                'order' => $this->request->getPost('txtOrder', array('string','trim')),
                'active' => $this->request->getPost("radActive"),
            );
            if (!empty($_POST['backendlangtranslate'])) {
                for ($i = 0; $i < count($_POST['backendlangtranslate']); $i++) {
                    $data['list_lang'][] = $_POST['backendlangtranslate'][$i];
                }
            } else {
                $messages["language"] = "Please choose language";
            }

            if (!empty($_POST['backendtranslate'])) {
                for ($i = 0; $i < count($_POST['backendtranslate']); $i++) {
                    $data['list_table'][] = $_POST['backendtranslate'][$i];
                }
            } else {
                $messages["table"] = "Please choose table";
            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["order"])) {
                $messages["order"] = "Order is not valid ";
            }
            $old_data = array();
           // var_dump($data);exit;
            if (empty($messages)){
                foreach ($data['list_lang'] as $language) {
                    $new_translate = new ForexcecTableTranslate();
                    $new_translate->setTranslateLanguage($language);
                    $new_translate->setTranslateTable(json_encode($data['list_table']));
                    $new_translate->setTranslateActive($data['active']);
                    $new_translate->setTranslateCron($data['cron']);
                    $new_translate->setTranslateOrder($data['order']);
                    $new_translate->setTranslateInsertTime($this->globalVariable->curTime);
                    $result = $new_translate->save();
                    if ($result === true) {
                        if ($data['active'] == 'Y') {
                            $cron_id = $this->checkCron();
                            $new_translate->setTranslateCronId($cron_id);
                            $new_translate->save();
                        }
                    }
                }
                $message = "We can't store your info now: " . "<br/>";
                if ($result === true){
                    $activity = new Activity();
                    $message = 'Create the translate language: ' . count($data['list_lang']) . ' success<br>';
                    $status = 'success';
                    $msg_result = array('status' => $status, 'msg' => $message);
                    $new_data = $data;
                    $data_log = json_encode(array('content_article' => array($new_translate->getTranslateId() => array($old_data, $new_data))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    foreach ($new_translate->getMessages() as $msg) {
                        $message .= $msg . "<br/>";
                    }
                    $msg_result = array('status' => 'error', 'msg' => $message);
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-translate");
            }
        }
        $listLang = Language::arrLanguages();
        $array_lang = [];
        foreach ($listLang as $key => $value) {
            if ($key != $this->globalVariable->defaultLanguage) {
                $array_lang[$key] = $value;
            }
        }
        $messages["status"] = "border-red";
        $this->view->setVars(array(
            'list_translate' => $array_models,
            'list_lang' => $array_lang,
            'formData' => $data,
            'messages' => $messages,
            'msg_result' => $msg_result
        ));
    }

    public function editAction()
    {
        $array_models = array();
        $directory_frontend =__DIR__."/../../models/*Lang.php";
        foreach (glob($directory_frontend) as $controller) {
            $className = basename($controller, '.php');
            array_push($array_models,$className);
        }
        $array_models = Translate::tableNotTranslate($array_models);

        $translate_id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($translate_id)) {
            $this->response->redirect('notfound');
            return;
        }

        $translate_model = ForexcecTableTranslate::findFirstById($translate_id);
        if(empty($translate_model)) {
            $this->response->redirect('notfound');
            return;
        }

        $data = $translate_model->toArray();
        $messages = array();
        $msg_result = array();

        if ($this->request->isPost()){
            $data = array(
                'translate_language' => $translate_model->getTranslateLanguage(),
                'translate_cron' => str_replace(' ','',$this->request->getPost('txtCron', array('string','trim'))),
                'translate_order' => $this->request->getPost('txtOrder', array('string','trim')),
                'translate_active' => $this->request->getPost("radActive"),
            );
            if(!empty($_POST['backendtranslate'])) {
                for ($i = 0; $i < count($_POST['backendtranslate']); $i++) {
                    $data['translate_table'][] = $_POST['backendtranslate'][$i];
                }
            } else {
                $messages["table"] = "Please choose table";
            }
            if (empty($data["translate_order"])) {
                $messages["order"] = "Order field is required.";
            }
            if (empty($messages)){
                $old_data = $translate_model->toArray();

                $translate_model->setTranslateTable(json_encode($data['translate_table']));
                $translate_model->setTranslateActive($data['translate_active']);
                $translate_model->setTranslateCron($data['translate_cron']);
                $translate_model->setTranslateOrder($data['translate_order']);

                $message = "We can't store your info now: " . "<br/>";
                if ($translate_model->save()){
                    if ($data['translate_active'] == 'Y') {
                        $cron_id = $this->checkCron();
                        $translate_model->setTranslateCronId($cron_id);
                        $translate_model->save();
                    }
                    $activity = new Activity();
                    $message = 'Update the translate language: ' . $translate_model->getTranslateLanguage() . ' success<br>';
                    $status = 'success';
                    $msg_result = array('status' => $status, 'msg' => $message);
                    $new_data = $translate_model->toArray();
                    $data_log = json_encode(array('content_article' => array($translate_model->getTranslateId() => array($old_data, $new_data))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    foreach ($translate_model->getMessages() as $msg) {
                        $message .= $msg . "<br/>";
                    }
                    $msg_result = array('status' => 'error', 'msg' => $message);
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-translate");
            }
        }
        $messages["status"] = "border-red";
        $this->view->setVars(array(
            'list_translate' => $array_models,
            'formData' => $data,
            'messages' => $messages,
            'msg_result' => $msg_result
        ));
    }

    public function deleteAction()
    {
        $translate_checked = $this->request->getPost("item");
        $msg_result = array();
        if(!empty($translate_checked))
        {
            $occ_log = array();
            foreach ($translate_checked as $id)
            {
                $translate_item = ForexcecTableTranslate::findFirstById($id);
                if($translate_item)
                {
                    if ($translate_item->delete() === false) {
                        $message_delete = 'Can\'t delete translate table lang = '.$translate_item->getTranslateLanguage();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = $translate_item->toArray();
                        $occ_log[$id] = $old_data;
                    }
                }
            }
            if(count($occ_log) > 0) {
                $message_delete = 'Delete '. count($occ_log) .' translate table successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
                $message = '';
                $data_log = json_encode(array('occ_table_translate' => $occ_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect("/list-translate");
        }
    }

    private function checkCron(){
        $forexcecCron = ForexcecCron::findFirstByType(ForexcecCron::TYPE_CRON_TRANSLATE,ForexcecCron::STATUS_CRON_RUNNING);
        if (!$forexcecCron){
            $forexcecCron = new ForexcecCron();
            $forexcecCron->setCronType(ForexcecCron::TYPE_CRON_TRANSLATE);
            $forexcecCron->setCronStatus(ForexcecCron::STATUS_CRON_RUNNING);
            $forexcecCron->setCronActive('Y');
            $forexcecCron->setCronUser($this->auth['email']);
            $forexcecCron->setCronInsertTime($this->globalVariable->curTime);
            $forexcecCron->save();
        }
        return $forexcecCron->getCronId();
    }
}
