<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecCommunicationChannel;
use Forexceccom\Models\ForexcecCommunicationChannelCountry;
use Forexceccom\Models\ForexcecCommunicationChannelLang;
use Forexceccom\Models\ForexcecLanguage;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\CommunicationChannel;
use Forexceccom\Repositories\CommunicationChannelCountry;
use Forexceccom\Repositories\CommunicationChannelLang;
use Forexceccom\Repositories\Language;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class CommunicationchannelController extends ControllerBase
{

    public function indexAction()
    {
        $msg_delete = array();
        if ($this->session->has('msg_delete')) {
            $msg_delete = $this->session->get('msg_delete');
            $this->session->remove('msg_delete');
        }

        $current_page = $this->request->getQuery('page', 'int');
        $validator = new Validator();
        $keyword = $this->request->get('txtSearch', 'trim');
        $type = $this->request->get('slcType');
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecCommunicationChannel WHERE 1";
        $arrParameter = array();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (communication_channel_id = :keyword:) ";
            } else {
                $sql .= " AND (communication_channel_name like CONCAT('%',:keyword:,'%'))";
            }
            $arrParameter['keyword'] = str_replace("'", "&#39;", $keyword);
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if (!empty($type)) {
            $sql .= " AND (communication_channel_type = :type: )";
            $arrParameter['type'] = $type;
            $this->dispatcher->setParam("slcType", $type);
        }
        $sql .= " ORDER BY communication_channel_id DESC";
        $list_communication_channel = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(
            [
                'data' => $list_communication_channel,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $select_type = CommunicationChannel::getComboBox($type);
        $this->view->setVars(array(
            'list_communication_channel' => $paginator->getPaginate(),
            'select_type' => $select_type,
            'msg_delete' => $msg_delete
        ));
    }

    public function createAction()
    {
        $data = array(
            'communication_channel_id' => -1,
            'communication_channel_name' => "",
            'communication_channel_type' => "",
            'communication_channel_icon' => "",
            'communication_channel_active' => 'Y',
            'communication_channel_order' => 1
        );
        if ($this->request->isPost()) {
            $data = array(
                'communication_channel_id' => -1,
                'communication_channel_type' => $this->request->getPost('slcType'),
                'communication_channel_name' => $this->request->getPost('txtName', array('string', 'trim')),
                'communication_channel_icon' => $this->request->getPost('txtIcon', array('string', 'trim')),
                'communication_channel_active' => $this->request->getPost('radActive'),
                'communication_channel_order' => $this->request->getPost('txtOrder', array('trim')),
            );
            $messages = array();
            if (empty($data['communication_channel_name'])) {
                $messages['name'] = 'Name field is required.';
            } else {
                if (!empty($data['communication_channel_type'])) {
                    $checkName = CommunicationChannel::checkName($data['communication_channel_name'], $data['communication_channel_type'], -1);
                    if (!empty($checkName)) {
                        $messages['name'] = 'Name is exists.';
                    }
                }
            }
            if (empty($data['communication_channel_type'])) {
                $messages["type"] = 'Type field is required.';
            }
            if (empty($data['communication_channel_order'])) {
                $messages["order"] = "Order field is required.";
            } else {
                if (!is_numeric($data["communication_channel_order"])) {
                    $messages["order"] = "Order is not valid ";
                }
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_content_communication_channel = new ForexcecCommunicationChannel();
                $new_content_communication_channel->setCommunicationChannelType($data['communication_channel_type']);
                $new_content_communication_channel->setCommunicationChannelName($data['communication_channel_name']);
                $new_content_communication_channel->setCommunicationChannelIcon($data['communication_channel_icon']);
                $new_content_communication_channel->setCommunicationChannelActive($data['communication_channel_active']);
                $new_content_communication_channel->setCommunicationChannelOrder($data['communication_channel_order']);
                $result = $new_content_communication_channel->save();
                if ($result === false) {
                    $message = "Create Communication Channel Fail !";
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                } else {
                    $activity = new Activity();
                    $status = 'success';
                    $message = 'Create Communication Channel Success<br>';

                    $msg_result = array('status' => $status, 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $message = '';
                    $data_log = json_encode(array(
                        'sw_communication_channel' => array(
                            $new_content_communication_channel->getCommunicationChannelId() => array(
                                $old_data,
                                $new_data
                            )
                        )
                    ));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message,
                        $data_log, $new_content_communication_channel->getCommunicationChannelId());
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/list-communication-channel");
            }
        }
        $messages["status"] = "border-red";
        $select_type = CommunicationChannel::getComboBox($data['communication_channel_type']);
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
        ]);
    }

    public function editAction()
    {
        $communication_channel_id = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($communication_channel_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $communication_channel_model = ForexcecCommunicationChannel::findFirstById($communication_channel_id);
        if (!$communication_channel_model) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array(
            'communication_channel_id' => -1,
            'communication_channel_type' => $communication_channel_model->getCommunicationChannelType(),
            'communication_channel_name' => $communication_channel_model->getCommunicationChannelName(),
            'communication_channel_icon' => '',
            'communication_channel_order' => '',
            'communication_channel_active' => ''
        );
        $save_mode = '';
        $lang_default = $this->globalVariable->defaultLanguage;
        $lang_current = $lang_default;
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
                $data_post['communication_channel_name'] = $this->request->getPost('txtName', array('string', 'trim'));
                if (empty($data_post['communication_channel_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                } else {
                    $check_name = CommunicationChannel::checkName($data_post['communication_channel_name'], $data_post['communication_channel_type'], $communication_channel_id);
                    if (!empty($check_name)) {
                        $messages[$save_mode]['name'] = 'Name is exists.';
                    }
                }
            } else {
                $data_post['communication_channel_type'] = $this->request->getPost('slcType');
                $data_post['communication_channel_icon'] = $this->request->getPost('txtIcon', array('string', 'trim'));
                $data_post['communication_channel_active'] = $this->request->getPost('radActive');
                $data_post['communication_channel_order'] = $this->request->getPost('txtOrder', array('string', 'trim'));

                if (empty($data_post['communication_channel_type'])) {
                    $messages["type"] = 'Type field is required.';
                } else {
                    $check_type = CommunicationChannel::checkName($data_post['communication_channel_name'], $data_post['communication_channel_type'], $communication_channel_id);
                    if (!empty($check_type)) {
                        $messages["type"] = 'Type is exists.';
                    }
                }

                if (empty($data_post['communication_channel_order'])) {
                    $messages["order"] = "Order field is required.";
                } else if (!is_numeric($data_post['communication_channel_order'])) {
                    $messages["order"] = "Order is not valid ";
                }
            }
            if (empty($messages)) {
                switch ($save_mode) {
                    case ForexcecLanguage::GENERAL:
                        $data_old = array(
                            'communication_channel_type' => $communication_channel_model->getCommunicationChannelType(),
                            'communication_channel_icon' => $communication_channel_model->getCommunicationChannelIcon(),
                            'communication_channel_active' => $communication_channel_model->getCommunicationChannelActive(),
                            'communication_channel_order' => $communication_channel_model->getCommunicationChannelOrder(),
                        );
                        $communication_channel_model->setCommunicationChannelType($data_post['communication_channel_type']);
                        $communication_channel_model->setCommunicationChannelIcon($data_post['communication_channel_icon']);
                        $communication_channel_model->setCommunicationChannelActive($data_post['communication_channel_active']);
                        $communication_channel_model->setCommunicationChannelOrder($data_post['communication_channel_order']);
                        $result = $communication_channel_model->update();
                        $info = ForexcecLanguage::GENERAL;
                        $data_new = array(
                            'communication_channel_type' => $communication_channel_model->getCommunicationChannelType(),
                            'communication_channel_icon' => $communication_channel_model->getCommunicationChannelIcon(),
                            'communication_channel_active' => $communication_channel_model->getCommunicationChannelActive(),
                            'communication_channel_order' => $communication_channel_model->getCommunicationChannelOrder(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'communication_channel_name' => $communication_channel_model->getCommunicationChannelName()
                        );
                        $communication_channel_model->setCommunicationChannelName($data_post['communication_channel_name']);

                        $result = $communication_channel_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'communication_channel_name' => $communication_channel_model->getCommunicationChannelName()
                        );
                        break;
                    default:
                        $data_content_communication_channel_lang = CommunicationChannelLang::findFirstByIdAndLang($communication_channel_id, $save_mode);
                        if (empty($data_content_communication_channel_lang)) {
                            $data_content_communication_channel_lang = new ForexcecCommunicationChannelLang();
                            $data_content_communication_channel_lang->setCommunicationChannelId($communication_channel_id);
                            $data_content_communication_channel_lang->setCommunicationChannelLangCode($save_mode);
                        } else {
                            $data_old = array(
                                'communication_channel_name' => $data_content_communication_channel_lang->getCommunicationChannelName(),
                                'communication_channel_lang_code' => $data_content_communication_channel_lang->getCommunicationChannelLangCode(),
                            );
                        }
                        $data_content_communication_channel_lang->setCommunicationChannelName($data_post['communication_channel_name']);
                        $result = $data_content_communication_channel_lang->save();
                        $info = $arr_language[$save_mode];

                        $data_new = array(
                            'communication_channel_name' => $data_content_communication_channel_lang->getCommunicationChannelName(),
                            'communication_channel_lang_code' => $data_content_communication_channel_lang->getCommunicationChannelLangCode(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Communication Channel success<br>"),
                        'typeMessage' => 'success'
                    );
                    $message = '';
                    $data_log = json_encode(array('dsbcf_order_communication_channel_lang' => array($communication_channel_id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $communication_channel_id);
                } else {
                    $messages = array(
                        'message' => "Update Communication Channel fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'communication_channel_id' => $communication_channel_model->getCommunicationChannelId(),
            'communication_channel_name' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['communication_channel_name'] : $communication_channel_model->getCommunicationChannelName()
        );
        $arr_translate[$this->globalVariable->defaultLanguage] = $item;
        $arr_communication_channel_lang = ForexcecCommunicationChannelLang::findById($communication_channel_id);
        foreach ($arr_communication_channel_lang as $communication_channel_lang) {
            $item = array(
                'communication_channel_id' => $communication_channel_lang->getCommunicationChannelId(),
                'communication_channel_name' => ($save_mode === $communication_channel_lang->getCommunicationChannelLangCode()) ? $data_post['communication_channel_name'] : $communication_channel_lang->getCommunicationChannelName()
            );
            $arr_translate[$communication_channel_lang->getCommunicationChannelLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'communication_channel_id' => -1,
                'communication_channel_name' => $data_post['communication_channel_name']
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'communication_channel_id' => $communication_channel_model->getCommunicationChannelId(),
            'communication_channel_type' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['communication_channel_type'] : $communication_channel_model->getCommunicationChannelType(), 'communication_channel_icon' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['communication_channel_icon'] : $communication_channel_model->getCommunicationChannelIcon(),
            'communication_channel_active' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['communication_channel_active'] : $communication_channel_model->getCommunicationChannelActive(),
            'communication_channel_order' => ($save_mode === ForexcecLanguage::GENERAL) ? $data_post['communication_channel_order'] : $communication_channel_model->getCommunicationChannelOrder(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $messages["status"] = "border-red";
        $select_type = CommunicationChannel::getComboBox($data_post['communication_channel_type']);
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
            'select_type' => $select_type
        ]);
    }

    public function deleteAction()
    {
        $activity = new Activity();
        $communication_channel_checked = $this->request->getPost("item");
        $msg_delete = array('error' => '', 'success' => '');
        if (!empty($communication_channel_checked)) {
            $communication_channel_log = array();
            foreach ($communication_channel_checked as $id) {
                $communication_channel_item = ForexcecCommunicationChannel::findFirstById($id);
                if ($communication_channel_item) {
                    $table_names = array();
//                    $user = IbcUser::findFirstByCommunicationChannel($id);
                    $message_temp = "Can't delete the Communication Channel Name = " . $communication_channel_item->getCommunicationChannelName() . ". Because It's exist in";
//                    if ($user) {
//                        $table_names[] = " User";
//                    }
                    if (empty($table_names)) {
                        if ($communication_channel_item->delete() === false) {
                            $message_delete = 'Can\'t delete the Communication Channel Name = ' . $communication_channel_item->getCommunicationChannelName();
                            $msg_result['status'] = 'error';
                            $msg_result['msg'] = $message_delete;
                        } else {
                            $old_data = $communication_channel_item->toArray();
                            $communication_channel_log[$id] = $old_data;
                            CommunicationChannelLang::deleteById($id);
                        }
                    } else {
                        $msg_delete['error'] .= $message_temp . implode(", ", $table_names) . "<br>";
                    }

                }
            }
        }
        if (count($communication_channel_log) > 0) {
            $message_delete .= 'Delete ' . count($communication_channel_log) . ' Communication Channel successfully.';
            $msg_result['status'] = 'success';
            $msg_delete['success'] = $message_delete;
            $data_log = json_encode(array('dsbcf_order_communication_channel' => $communication_channel_log));
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message_delete, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        return $this->response->redirect("/list-communication-channel");
    }

    public function countryAction()
    {
        $id = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($id)) {
            return $this->response->redirect('notfound');
        }
        $channel_model = ForexcecCommunicationChannel::findFirstById($id);
        if (empty($channel_model)) {
            return $this->response->redirect('notfound');
        }
        if ($this->request->isPost()) {
            $countries = $this->request->getPost('slcCountry');
            $channel_country_old = ForexcecCommunicationChannelCountry::findByChannelId($id)->toArray();
            CommunicationChannelCountry::deleteAllByChannelId($id);
            if (!empty($countries)) {
                foreach ($countries as $item) {
                    $channelCountry = new ForexcecCommunicationChannelCountry();
                    $channelCountry->setCommunicationChannelId($id);
                    $channelCountry->setCommunicationChannelCountryCode($item);
                    $channelCountry->save();
                }
            }
            $channel_country_new = ForexcecCommunicationChannelCountry::findByChannelId($id)->toArray();
            $msg_result = array('status' => 'success', 'msg' => 'Create Channel ID = ' . $id . ' Success');
            $old_data = $channel_country_old;
            $new_data = $channel_country_new;
            $data_log = json_encode(array('occ_communication_channel_country' => array($id => array($old_data,
                $new_data))));
            $message = 'Create Channel ID = ' . $id . ' Success';
            $activity = new Activity();
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $id);
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect("/list-communication-channel");
        }
        $country = CommunicationChannelCountry::getCheckbox($id);
        $this->view->setVars([
            'channel_name' => $channel_model->getCommunicationChannelName(),
            'country' => $country,
        ]);
    }
}

