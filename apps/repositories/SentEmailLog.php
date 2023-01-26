<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecSentEmailLog;
use Phalcon\Mvc\User\Component;

class SentEmailLog extends Component
{

    public static function findFirstById($id) {
        return ForexcecSentEmailLog::findFirst([
            'sent_id  = :id:',
            'bind' => [
                'id' => $id,
            ]
        ]);
    }

    public static function findFirstByEmail($email,$type) {
        return ForexcecSentEmailLog::findFirst([
            'sent_email = :email: AND sent_email_type = :type:',
            'bind' => [
                'email' => $email,
                'type' => $type
            ]
        ]);
    }
    public static function findFirstByOnlyEmail($email) {
        return ForexcecSentEmailLog::findFirst([
            'sent_email = :email:',
            'bind' => [
                'email' => $email,
            ]
        ]);
    }
    public static function getLogByEmailAndType($email,$type) {
        $email_model = self::findFirstByEmail($email,$type);
        if ($type == ForexcecSentEmailLog::FORM_LEADFORM) {
            return $email_model ? $email_model->getSentLogLeadform() : "";
        } else {
            return $email_model ? $email_model->getSentLogLeadform() : "";
        }
    }
    public static function getInsertTimeByEmailAndType($email,$type) {
        $email_model = self::findFirstByEmail($email,$type);
        return $email_model ? $email_model->getSentInsertTime() : 0;
    }
    public static function changeTypeEmailTypeLog($email) {
        $email_log = self::findFirstByEmail($email,ForexcecSentEmailLog::FORM_LEADFORM);
        if ($email_log) {
            $email_lw_user = ListLwUser::findFirstByEmail($email);
            if ($email_lw_user) {
                $email_log->setSentEmailType(ForexcecSentEmailLog::FORM_LW_USER);
                $email_log->setSentStatus('processing');
                $email_log->setSentInsertTime($email_lw_user->getForexcecRegisterTime());
                $email_log->save();
            }
        }
    }
    public static function getComboboxStt($status)
    {
        $list_status = [
            'finish' => 'Finish',
            'processing' => 'Processing'
        ];
        $output = '';
        foreach ($list_status as $item => $value) {
            $selected = '';
            if ($item == $status) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $item . "'>" . $value . "</option>";

        }
        return $output;
    }
    public static function getComboboxSubscribe($subscribe)
    {
        $list_subscribe = [
            'Y' => 'Yes',
            'N' => 'No'
        ];
        $output = '';
        foreach ($list_subscribe as $item => $value) {
            $selected = '';
            if ($item == $subscribe) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $item . "'>" . $value . "</option>";

        }
        return $output;
    }
    public static function saveLogTime($email,$template,$education,$time,$success,$max_template_send){
        $log = self::findFirstByOnlyEmail($email);
        $arr_log_template = [];
        $arr_log_education = [];
        if ($log) {
            if ($log->getSentLogTime()) {
                $log_time = json_decode($log->getSentLogTime(),true);

            } else {
                $log_time = [
                    'email' => [],
                    'education' => [],
                ];
            }
            if ($template) {
                $temp_log = $success.'-'.$time;
                $arr_log_template = $log_time['email'];
                $arr_log_template[$template] = $temp_log;

            }
            if ($education) {
                $arr_log_education = $log_time['education'];
                $education_log = $success.'-'.$time;
                $arr_log_education[$education] = $education_log;

            }
            $log_time = [
                'email' => $arr_log_template,
                'education' => $arr_log_education,
            ];
            $log->setSentLogTime(json_encode($log_time));
            $log->save();
        }
    }

    public static function findTempSendById($temp_id,$email,$is_lesson) {
        $log = self::findFirstByOnlyEmail($email);
        $data = [
            'success' => "",
            'time' => "",
        ];
        $result = [];
        if ($log) {
            if ($log->getSentLogTime()) {
                $log_time = json_decode($log->getSentLogTime(),true);
                if ($is_lesson) {
                    $result = isset($log_time['education'][$temp_id]) ? $log_time['education'][$temp_id] : "";
                } else {
                    $result = isset($log_time['email'][$temp_id]) ? $log_time['email'][$temp_id] : "";

                }
            }
        }

        if ($result) {
            $data_ex = explode('-',$result);
            $data = [
                'success' => $data_ex[0] == 1 ? "sent success" : "sent false",
                'time' => $data_ex[1],
            ];
        }
        return $data;
    }
    public function viewSentLog($log_leadform,$logModel,&$arrTemplateEmail,&$arrEducation) {
        if ($log_leadform) {
            $de_log_leadform = json_decode($log_leadform);
            if (count($de_log_leadform->email) > 0) {
                foreach ($de_log_leadform->email as $email_id){
                    $time = "";
                    $success = "";
                    $result = self::findTempSendById($email_id,$logModel->getSentEmail(),false);

                    if ($result['success'] && $result['time']) {
                        $time = " - ". $this->my->formatDateTime($result['time'],false);
                        $success = " - ".$result['success'];
                    }

                    $subject_email = TemplateAutoEmail::getSubjectById($email_id);
                    if ($subject_email) {
                        array_push($arrTemplateEmail,$subject_email.$time.$success);
                    }
                }
            }
            if (count($de_log_leadform->education) >0 ){
                foreach ($de_log_leadform->education as $lesson) {
                    $time = "";
                    $success = "";
                    $result = self::findTempSendById($lesson,$logModel->getSentEmail(),true);
                    if ($result['success'] && $result['time']) {
                        $time = " - ". $this->my->formatDateTime($result['time'],false);
                        $success = " - ".$result['success'];
                    }
                    array_push($arrEducation,'lesson: '.$lesson.$time.$success);
                }
            }
        }
    }
}



