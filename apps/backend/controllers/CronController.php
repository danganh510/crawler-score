<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecArticle;
use Forexceccom\Models\ForexcecListLwUser;
use Forexceccom\Models\ForexcecSentEmailLog;
use Forexceccom\Models\ForexcecTemplateAutoEmail;
use Forexceccom\Models\ForexcecTemplateEmail;
use Forexceccom\Repositories\Article;
use Forexceccom\Repositories\Cron;
use Forexceccom\Repositories\EmailTemplate;
use Forexceccom\Repositories\Leadform;
use Forexceccom\Repositories\ListLwUser;
use Forexceccom\Repositories\SentEmailLog;
use Forexceccom\Repositories\TemplateAutoEmail;
use Phalcon\Di;

class CronController extends ControllerBase
{
    const URL_LIST_USER = 'https://account.api.lwork.com/v1/api/user/list';
    const API_TENANTID = "T002689";
    const API_TOKEN = "0JVeadlscDGCHxCj";
    const lesson_leadform_start = 3;
    const lesson_user_start = 2;
    const FOLDER_LEADFORM = "report_lf.csv";
    const FOLDER_LW_USER = "report_tw.csv";

    public function updateuserlwapiAction()
    {
        $this->view->disable();
        $token = $this->request->get('token');
        if ($token != $this->globalVariable->cronPassword) {
            echo "Invalid token!";
            return;
        }
        $page = $this->request->get('page');
        $limit = $this->request->get('limit');
        $data = [
            'page' => $page ? $page : 1,
            'limit' => $limit ? $limit : 100,
            'total_update_success' => 0,
            'total_insert_success' => 0,
            'total_update_error' => 0,
            'total_insert_error' => 0,
            'total_run' => 0,
            'start_time' => microtime(true),
        ];
        echo "\r\n--------- CRON BEGIN AT ".$this->my->formatDateTime($data['start_time'])."------\r\n";

        $data = self::getListLWUser($data);
        $runTime = microtime(true) - $data['start_time'];

        echo "----------------------------------------------------------------------------";
        echo "\r\nUpdate list user from LW API success in " . $runTime . "\r\n";
        echo "---Total Page: ".$data['page']."\r\n";
        echo "---Total record: ".$data['total_run']."\r\n";
        if ($data['total_insert_success'] > 0) {
            echo "------Insert Success : ".$data['total_insert_success']."\r\n";
        }
        if ($data['total_insert_error'] > 0) {
            echo "------Insert Error : ".$data['total_insert_error']."\r\n";
        }
        if ($data['total_update_success'] > 0) {
            echo "------Update Success : ".$data['total_update_success']."\r\n";
        }
        if ($data['total_update_error'] > 0) {
            echo "------Update Error : ".$data['total_update_error']."\r\n";
        }

    }
    public function sendemailautoAction() {
        $this->view->disable();
        ini_set('max_execution_time', 60);
        $token = $this->request->get('token');
        $limit = $this->request->get('limit');
        $limit = $limit ? $limit : 10;
        if ($token != $this->globalVariable->cronPassword) {
            echo "Invalid token!";
            return;
        }
        $startTime = $this->globalVariable->curTime;
        $dataReturn = [
            'total' => 0,
            'total_success' => 0,
            'total_error' => 0
        ];
        echo "\r\n--------- CRON BEGIN AT ".$this->my->formatDateTime($startTime)."------\r\n\r\n";

        //update email and send email register in this day.
        $this->updateLwUserTimeSent($startTime,$limit,$dataReturn);

        //update email and send email lead form in this day.
        $this->updateLeadformTimeSent($startTime);

        $limit_ex = $limit - $dataReturn['total'];
        if ($limit_ex > 0 ) {
            $sql = "SELECT * FROM  Forexceccom\Models\ForexcecSentEmailLog 
                WHERE  sent_status = 'processing' 
                 AND ((FROM_UNIXTIME(:time: + :timezone:,'%Y-%m-%d') > FROM_UNIXTIME(sent_update_time + :timezone:,'%Y-%m-%d')))
                 AND sent_is_subscribe = 'Y'
                ORDER BY sent_update_time ASC 
                 LIMIT $limit_ex ";
            $arrPara['time'] = $startTime;
            $arrPara['timezone'] = $this->globalVariable->timeZone;
            $list_email =  $this->modelsManager->executeQuery($sql,$arrPara);

            if (count($list_email) > 0) {
                $this->sendListEmail($list_email,$startTime,$dataReturn);
            }
        }
        $runTime =  microtime(true) - $startTime;
        $str_time = date("i:s",$runTime);

        echo "----------------------------------------------------------------------------";
        echo "\r\nSend Email in Send Log Email success in " . $str_time . "\r\n";
        echo "---Total email: ".$dataReturn['total']."\r\n";
        if ($dataReturn['total_success'] > 0) {
            echo "------Sent Email Success : ".$dataReturn['total_success']."\r\n";
        }
        if ($dataReturn['total_error'] > 0) {
            echo "------Sent Email Error : ".$dataReturn['total_error']."\r\n";
        }
    }
    public function reportemailsentAction() {
        $this->view->disable();
        $token = $this->request->get('token');
        if ($token != $this->globalVariable->cronPassword) {
            echo "Invalid token!";
            return;
        }
        $startTime = $this->globalVariable->curTime;
        echo "\r\n--------- CRON BEGIN AT ".$this->my->formatDateTime($startTime)."------\r\n";
        $list_email_log = ForexcecSentEmailLog::find([
            "FROM_UNIXTIME(:time: + :timezone:,'%Y-%m-%d') = FROM_UNIXTIME(sent_update_time + :timezone:,'%Y-%m-%d')",
            'order' => 'sent_update_time ASC',
            'bind' => [
                'time' => $startTime,
                'timezone' => $this->globalVariable->timeZone,
            ]
        ]);

        //id template lesson
        $max_template_send = [
            ForexcecSentEmailLog::FORM_LW_USER => TemplateAutoEmail::getMaxIdByForm(ForexcecSentEmailLog::FORM_LW_USER),
            ForexcecSentEmailLog::FORM_LEADFORM => TemplateAutoEmail::getMaxIdByForm(ForexcecSentEmailLog::FORM_LEADFORM),
        ];
        $dataReturnLeadform = [];
        $dataReturnLwUser = [];
        $cron = new Cron();
        foreach ($list_email_log as $email_log) {
            $data_log = [];
            if ($email_log->getSentLogTime()) {
                $is_lesson = false;
                $lesson_name = "";
                $temp_subject = "";
                $de_log_time = json_decode($email_log->getSentLogTime(),true);
                $template_send_id = array_search(end($de_log_time['email']),$de_log_time['email']);
                $lesson_send = array_search(end($de_log_time['education']),$de_log_time['education']);
                $data = [];
                if ($template_send_id == $max_template_send[$email_log->getSentEmailType()]) {
                    if ($lesson_send) {
                        $data = SentEmailLog::findTempSendById($lesson_send,$email_log->getSentEmail(),true);
                        $lesson_name = Article::findFirstByLessionId($lesson_send) ? Article::findFirstByLessionId($lesson_send)->getArticleName() : "";
                        $is_lesson = true;
                    }
                } else {
                    $data = SentEmailLog::findTempSendById($template_send_id,$email_log->getSentEmail(),false);
                    $temp_subject = TemplateAutoEmail::getSubjectById($template_send_id);
                }
                if ($data) {
                    if ($data['success'] == "sent success") {
                        if ($cron->getDayByTimestamp($startTime,$data['time']) == 0) {
                            $data_log = [
                                'date' => $this->my->formatDateTime($data['time'],false),
                                'email' => $email_log->getSentEmail(),
                                'subject' => $is_lesson ? $lesson_name : $temp_subject,
                            ];
                        }
                    }
                }
                if ($data_log) {
                    if ($email_log->getSentEmailType() == ForexcecSentEmailLog::FORM_LEADFORM) {
                        array_push($dataReturnLeadform,$data_log);
                    } else {
                        array_push($dataReturnLwUser,$data_log);
                    }
                }
            }
        }
        $results_leadform[] = ["Lead Form - ".$this->my->formatDateTimeReport($startTime)];
        $results_leadform[] = array("Date (UTC -04:00)", "Email", "Subject");
        foreach ($dataReturnLeadform as $item) {
            $log = array(
                $item['date'],
                $item['email'],
                htmlspecialchars_decode($item['subject'],ENT_QUOTES),
            );
            $results_leadform[] = $log;
        }
        $results_lw_user[] = ["Lw User - ".$this->my->formatDateTimeReport($startTime)];
        $results_lw_user[] = array("Date (UTC -04:00)", "Email", "Subject");
        foreach ($dataReturnLwUser as $item) {
            $log = array(
                $item['date'],
                $item['email'],
                htmlspecialchars_decode($item['subject'],ENT_QUOTES),
            );
            $results_lw_user[] = $log;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Report-send-email-'.$this->my->formatDateTimeReport($startTime).'.csv');
        $out = fopen(self::FOLDER_LEADFORM, 'w+');
        fputs( $out, "\xEF\xBB\xBF" );
        foreach ($results_leadform as $fields) {
            fputcsv($out, $fields);
        }
        fclose($out);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Report-send-email-'.$this->my->formatDateTimeReport($startTime).'.csv');
        $out = fopen(self::FOLDER_LW_USER, 'w+');
        fputs( $out, "\xEF\xBB\xBF" );
        foreach ($results_lw_user as $fields) {
            fputcsv($out, $fields);
        }
        fclose($out);

        $content_lf = file_get_contents(self::FOLDER_LEADFORM);
        $content_lw = file_get_contents(self::FOLDER_LW_USER);

        $filename[] = array(
            "name" => 'Report-send-leadform-'.$this->my->formatDateTimeReport($startTime).'.csv',
            "content" => $content_lf,
            "type" => 'text/csv',
        );

        $filename[] = array(
            "name" => 'Report-send-user-'.$this->my->formatDateTimeReport($startTime).'.csv',
            "content" => $content_lw,
            "type" => 'text/csv',
        );

        $email_template['success'] = true;
        $email_template['subject'] = "[FOREXCEC] - Report Automatic Email for Customers - ".$this->my->formatDateTimeReport($startTime);
        $email_template['content'] = "Report Automatic Email for Customers on: ".$this->my->formatDateTimeReport($startTime);
        if ($email_template['success'] == true) {
            $subject = $email_template['subject'];
//            // Send  Chi vi
//            $from_email = "noreply@forexcec.com";
//            $to_email = 'linhvi@bin.com.vn';
//            $from_full_name = $reply_to_name = 'ForexCEC';
//            $reply_to_email = 'support@forexcec.com';
//            $to_full_name = "marketing";
//            $this->my->sendEmail($from_email, $to_email, $subject,$email_template['content'] , $from_full_name, $to_full_name, $reply_to_email, $reply_to_name,$filename);

//g

            // Send Marketing
            $from_email = "noreply@forexcec.com";
            $to_email = 'marketing@forexcec.com';
            $from_full_name = $reply_to_name = 'ForexCEC';
            $reply_to_email = 'support@forexcec.com';
            $to_full_name = "marketing";

            $result = $this->my->sendEmail($from_email, $to_email, $subject,$email_template['content'] , $from_full_name, $to_full_name, $reply_to_email, $reply_to_name,$filename);
            if ($result['success'] == true) {
                echo "\r\n Send Email report success";
            } else {
                echo "\r\n Send Email report false";
            }

        }
        unlink(self::FOLDER_LW_USER);
        unlink(self::FOLDER_LEADFORM);

        $runTime = microtime(true) - $startTime;
        $str_time = date("i:s",$runTime);
        echo "\r\n Date Time: " . $this->my->formatDateTime($startTime) .
            " in " . $str_time . "\r\n";
    }
    private static function getListLWUser($data) {
        $runTime = microtime(true) - $data['start_time'];
        if ($runTime > 60) {
            echo 'Request time out! ' . "\n";
            return $data;
        }
        $dataReturn = self::getApiListUser($data['page'],$data['limit']);
        if ($dataReturn['mcode'] == "m0000000" && $dataReturn['result'] === true) {
            $data['total_run'] = $dataReturn['data']['total'];
            $data = self::updateData($dataReturn['data']['list'],$data);
            if ($data['total_run'] > $data['limit']*$data['page']) {
                $data['page']++;
                $data = self::getListLWUser($data);
            }
        } else {
            echo "Call API false.\r\n";
            echo "ERROR: ".$dataReturn['mcode'];
            die();
        }
        return $data;
    }
    private static function updateData($arrData,$data) {
        foreach ($arrData as $item) {
            $new_record = ForexcecListLwUser::findFirst([
                'forexcec_pub_user_id = :user_id: AND forexcec_email = :email:',
                'bind' => [
                    'user_id' => $item['pubUserId'],
                    'email' => $item['email'],
                ]
            ]);
            $item['lastLoginTime'] = isset($item['lastLoginTime']) ? $item['lastLoginTime'] : "";
            if ($item['isEnable']) {
                $isEnable = "true";
            } else {
                $isEnable = "false";
            }
            $item['lastLoginTime'] = $item['lastLoginTime'] ? round($item['lastLoginTime']/1000) : 0;
            $item['registerTime'] = $item['registerTime'] ? round($item['registerTime']/1000) : 0;
            if (!$new_record) {
                $new_record = new ForexcecListLwUser();
                $new_record->setForexcecAccount(implode(',',$item['accounts']));
                $new_record->setForexcecEmail($item['email']);
                $new_record->setForexcecPubUserId($item['pubUserId']);
                $new_record->setForexcecRealName($item['realName']);
                $new_record->setForexcecRegisterTime($item['registerTime']);
                $new_record->setForexcecIsEnable($item['isEnable']);
                $new_record->setForexcecLastLoginTime($item['lastLoginTime']);
                $result = $new_record->save();
                if ($result) {
                    $data['total_insert_success']++;
                    echo " Insert New Success Email : ".$item['email'].", ID: ".$item['pubUserId'].", RealName: ".$item['realName'].", Account ID: [".implode(', ',$item['accounts'])."], IsEnable: ".$isEnable.", LastLoginTime: ".$item['lastLoginTime'].", RegisterTime: ".$item['registerTime']."\r\n";
                } else {
                    $data['total_insert_error']++;
                    echo " Insert false Email : ".$item['email'].", ID: ".$item['pubUserId']."\r\n";
                    foreach ($new_record->getMessages() as $msg) {
                        echo "--- Error: ". $msg . "<br/>";
                    }
                }
            } else {
                $update = false;
                if ($new_record->getForexcecIsEnable() != $item['isEnable']) {
                    $new_record->setForexcecIsEnable($item['isEnable']);
                    $update = true;
                }
                if ($new_record->getForexcecAccount() != implode(',',$item['accounts'])) {
                    $new_record->setForexcecAccount(implode(',',$item['accounts']));
                    $update = true;
                }
                if ($new_record->getForexcecLastLoginTime() != $item['lastLoginTime']) {
                    $new_record->setForexcecLastLoginTime(isset($item['lastLoginTime']) ? $item['lastLoginTime'] : 0);
                    $update = true;
                }
                if ($new_record->getForexcecRegisterTime() != $item['registerTime']) {
                    $new_record->setForexcecRegisterTime(isset($item['registerTime']) ? $item['registerTime'] : 0);
                    $update = true;
                }
                if ($update) {
                    $result = $new_record->save();
                    if ($result) {
                        $data['total_update_success']++;
                        echo " Update Success Email : ".$item['email'].", ID: ".$item['pubUserId'].", RealName: ".$item['realName'].", Account ID: [".implode(', ',$item['accounts'])."], IsEnable: ".$isEnable.", LastLoginTime: ".$item['lastLoginTime'].", RegisterTime: ".$item['registerTime']."\r\n";
                    } else {
                        $data['total_update_error']++;
                        echo " Update false Email : ".$item['email'].", ID: ".$item['pubUserId']."\r\n";
                        foreach ($new_record->getMessages() as $msg) {
                            echo "--- Error: ". $msg . "<br/>";
                        }
                    }
                }
            }

        }
        return $data;

    }
    private static function getApiListUser($page,$size)
    {

        $URL_API_LIST_USER = self::URL_LIST_USER;
        $URL_API_LIST_USER .= "?page=".$page;
        $URL_API_LIST_USER .= "&size=".$size;
        $result = self::curl_get_contents($URL_API_LIST_USER);
        return $result;
    }

    private static function curl_get_contents($url)
    {
        $request_headers=[];
        $request_headers[] = "x-api-tenantId: ".self::API_TENANTID;
        $request_headers[]= "x-api-token: ".self::API_TOKEN;
        $request_headers[]= "content-type: application/json";
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER=>1,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER =>$request_headers
        ));
        $data = curl_exec($ch);
        curl_close($ch);
        $data_de = json_decode($data,true);
        if($data_de === NULL){
            $data_de= [
                'status'=>'error',
                'message' =>$data,
            ];
        }
        return $data_de;
    }
    private static function saveCronSentEmailFinish($email,$starTime) {
        $email_log = SentEmailLog::findFirstByOnlyEmail($email);
        if ($email_log) {
            $email_log->setSentStatus('finish');
            $email_log->setSentUpdateTime($starTime);
            $email_log->save();
        }
    }
    private static function saveCronSentEMail($email,$form,$email_sent_id,$lesson_sent_id,$startTime,$insertime = 0) {
        $email_log = SentEmailLog::findFirstByEmail($email,$form);
        if ($email_log) {
            if ($form == ForexcecSentEmailLog::FORM_LEADFORM) {
                $log = json_decode($email_log->getSentLogLeadform());
            } else {
                $log = json_decode($email_log->getSentLogLwUser());
            }

            if ($log) {
                if (!$lesson_sent_id) {
                    if ($email_sent_id) {
                        if (count($log->email) > 0) {
                            if (!in_array($email_sent_id,$log->email)) {
                                array_push($log->email,$email_sent_id);
                            }
                        } else {
                            array_push($log->email,$email_sent_id);
                        }
                    }

                } else {
                    array_push($log->education,$lesson_sent_id);
                    if ($email_sent_id) {
                        if (!in_array($email_sent_id,$log->email)) {
                            array_push($log->email,$email_sent_id);
                        }
                    }
                }
                $new_log = [
                    'email' => $log->email,
                    'education' => $log->education,
                ];
            } else {
                $new_log = [
                    'email' => ($email_sent_id || $email_sent_id != 0)  ? [$email_sent_id] : [],
                    'education' => ($lesson_sent_id || $lesson_sent_id != 0) ? [$lesson_sent_id] : [],
                ];
            }
            if ($form == ForexcecSentEmailLog::FORM_LEADFORM) {
                $email_log->setSentLogLeadform(json_encode($new_log));

            } else {
                $email_log->setSentLogLwUser(json_encode($new_log));

            }
            $email_log->setSentUpdateTime($startTime);
            //change Leadform to LW user change insert_time = register_time(in LW user)
            if($insertime) {
                $email_log->setSentInsertTime($insertime);
            }
            return $email_log->save();
        } else {
            $log = [
                'email' => ($email_sent_id || $email_sent_id != 0)  ? [$email_sent_id] : [],
                'education' => ($lesson_sent_id || $lesson_sent_id != 0) ? [$lesson_sent_id] : [],
            ];
            $email_log = new ForexcecSentEmailLog();
            $email_log->setSentEmail($email);
            $email_log->setSentEmailType($form);
            $email_log->setSentStatus("processing");
            $email_log->setSentUpdateTime($startTime);
            $email_log->setSentInsertTime($insertime);
            $email_log->setSentIsSubcribe("Y");
            if ($form == ForexcecSentEmailLog::FORM_LEADFORM) {
                $email_log->setSentLogLeadform(json_encode($log));

            } else {
                $email_log->setSentLogLwUser(json_encode($log));

            }
        }

        return  $email_log->save();
    }
    private function updateLeadformTimeSent($startTime) {
        $sql = "SELECT * FROM  Forexceccom\Models\ForexcecLeadform  
                WHERE leadform_email NOT IN (SELECT sent_email FROM Forexceccom\Models\ForexcecSentEmailLog)
                AND leadform_email NOT IN (SELECT forexcec_email FROM Forexceccom\Models\ForexcecListLwUser)
                ORDER BY leadform_insert_time DESC";
        $arrPara['time'] = $startTime;
        $arrPara['timezone'] = $this->globalVariable->timeZone;
        $list_leadform =  $this->modelsManager->executeQuery($sql,$arrPara);
        $arrEmail = [];
        $cron = new Cron();
        foreach ($list_leadform as $leaform) {
            if (!in_array($leaform->getLeadformEmail(),$arrEmail)) {
                self::saveCronSentEMail($leaform->getLeadformEmail(),ForexcecSentEmailLog::FORM_LEADFORM,0,0,$startTime,$leaform->getLeadformInsertTime());
                $day_run =  $cron->getDayByTimestamp($startTime, $leaform->getLeadformInsertTime());
                if ($day_run > 0) {
                    self::saveCronSentEmailFinish($leaform->getLeadformEmail(),$startTime);
                } else {
                    array_push($arrEmail,$leaform->getLeadformEmail());
                }
            }

        }
    }
    private function updateLwUserTimeSent($startTime,$limit,&$dataReturn) {

        //create and edit send log for email not in email_log,type= api_lw
        $sql = "SELECT * FROM  Forexceccom\Models\ForexcecListLwUser
                WHERE forexcec_email NOT IN (SELECT sent_email FROM Forexceccom\Models\ForexcecSentEmailLog WHERE sent_email_type = 'api_lw')
                ORDER BY forexcec_register_time DESC";
        $arrPara['time'] = $startTime;
        $arrPara['timezone'] = $this->globalVariable->timeZone;
        $list_user = $this->modelsManager->executeQuery($sql,$arrPara);
        $list_user_send_email = [];

        $total = 0;
        $cron = new Cron();

        foreach ($list_user as $user) {
            if ($total >= $limit) {
                break;
            }
            $day_run =  $cron->getDayByTimestamp($startTime, $user->getForexcecRegisterTime());

            SentEmailLog::changeTypeEmailTypeLog($user->getForexcecEmail());

            self::saveCronSentEMail($user->getForexcecEmail(),ForexcecSentEmailLog::FORM_LW_USER,0,0,$startTime,$user->getForexcecRegisterTime());

            if ($day_run > 0) {
                self::saveCronSentEmailFinish($user->getForexcecEmail(),$startTime);

            } else {
                if ($user->getForexcecAccount() != "" || !empty($user->getForexcecAccount())) {
                    self::saveCronSentEmailFinish($user->getForexcecEmail(),$startTime);
                } else {
                    $email_log = SentEmailLog::findFirstByEmail($user->getForexcecEmail(),ForexcecSentEmailLog::FORM_LW_USER);

                    if ($email_log) {
                        array_push($list_user_send_email,$email_log);
                        $total++;
                    }
                }

            }
        }
        if (count($list_user_send_email) > 0) {
            $this->sendListEmail($list_user_send_email,$startTime,$dataReturn);
        }
    }
    public function sendListEmail($list_email_log,$startTime,&$dataReturn) {
        //time a day finish send email submit
        $max_time_send[ForexcecSentEmailLog::FORM_LW_USER] = TemplateAutoEmail::getMaxDaySendEmail(ForexcecSentEmailLog::FORM_LW_USER);
        $max_time_send[ForexcecSentEmailLog::FORM_LEADFORM] = TemplateAutoEmail::getMaxDaySendEmail(ForexcecSentEmailLog::FORM_LEADFORM);

        //id template lesson
        $max_template_send = [
            ForexcecSentEmailLog::FORM_LW_USER => TemplateAutoEmail::getMaxIdByForm(ForexcecSentEmailLog::FORM_LW_USER),
            ForexcecSentEmailLog::FORM_LEADFORM => TemplateAutoEmail::getMaxIdByForm(ForexcecSentEmailLog::FORM_LEADFORM),
        ];

        $max_lesson_id = Article::getMaxLessionId();
        $total = 0;
        $total_success = 0;
        $total_error = 0;
        $cron = new Cron();
        foreach ($list_email_log as $email_log) {
            /**
             * @var ForexcecSentEmailLog $email_log
             */

            //if email has account stop send email
            $lw_user = ListLwUser::checkEmailAndAcount($email_log->getSentEmail());
            if ($lw_user) {
                self::saveCronSentEmailFinish($email_log->getSentEmail(),$startTime);
                continue;
            }
            $time_now = $cron->getDayByTimestamp($startTime, $email_log->getSentInsertTime());

            $email_log->setSentUpdateTime($startTime);
            $email_log->save();

            $arrayTemplateEmail = TemplateAutoEmail::findBySendDay($time_now,$email_log->getSentEmailType());
            if (!$arrayTemplateEmail) {
                echo "\r\nNot found Template Email for Email: ".$email_log->getSentEmail()."\r\n";
                continue;
            }
            foreach ($arrayTemplateEmail as $template_send) {
                $lesson_sent_id = 0;
                if ($email_log->getSentEmailType() == ForexcecSentEmailLog::FORM_LEADFORM) {
                    $log = json_decode($email_log->getSentLogLeadform());
                    $name = Leadform::findNameByEmail($email_log->getSentEmail());

                    $lesson_start = self::lesson_leadform_start;
                } else {
                    $log = json_decode($email_log->getSentLogLwUser());
                    $name = ListLwUser::getNameByEmail($email_log->getSentEmail());

                    $lesson_start = self::lesson_user_start;
                }

                if (isset($log) && $log) {
                    if (count($log->education) > 0) {
                        $arrLessonSent = $log->education;
                        if (count($arrLessonSent) != 0) {
                            $lesson_sent_id =  end($arrLessonSent);
                        }
                        if ($lesson_sent_id < $max_lesson_id) {
                            $lesson_sent_id++;
                        } else {
                            echo "- Sent to: ".$email_log->getSentEmail()." all email.\r\n";
                            self::saveCronSentEmailFinish($email_log->getSentEmail(),$startTime);
                            continue;
                        }
                    } else {
                        if (count($log->email) > 0) {
                            $arrSubmit = $log->email;
                            if (end($arrSubmit) != $max_template_send[$email_log->getSentEmailType()]) {
                                if (in_array($template_send->getEmailId(),$arrSubmit)) {
                                    continue;
                                }
                                // change day -> timestamp
                                // start send email lesson
                                if ($template_send->getEmailId() == $max_template_send[$email_log->getSentEmailType()] ) {
                                    $lesson_sent_id = $lesson_start;
                                }
                            }
                        }
                    }
                }


                //for email submit leadform > last day sent email submit but unsent email (old leadform)
                if ($time_now >= $max_time_send[$email_log->getSentEmailType()] && $lesson_sent_id == 0) {
                    $lesson_sent_id = $lesson_start;
                }

                $result = $cron->sendEmail($email_log->getSentEmail(),$name, $email_log->getSentEmailType(), $template_send,$lesson_sent_id);

                echo "- Sent to: ".$email_log->getSentEmail();
                if($result['success'] == true) {
                    echo ' Successfully \r\n';
                    $total_success++;
                    $success_log_time = 1;
                } else {
                    echo ' Fail. \r\n';
                    echo " --- Email Template: ".$template_send->getEmailType(). ' ';
                    echo " --- Error: ".$result['message'];
                    $total_error++;
                    $success_log_time = 0;
                }
                //save log
                $save_log = self::saveCronSentEMail($email_log->getSentEmail(),$email_log->getSentEmailType(),$template_send->getEmailId(),$lesson_sent_id,$startTime);
                echo " --- Email Template: ".$template_send->getEmailType();
                if ($lesson_sent_id) {
                    echo ". Lesson: ".$lesson_sent_id;
                }
                if ($save_log) {
                    echo "\r\n --- Save LOG success\r\n";
                } else {
                    echo "\r\n  --- Save LOG false\r\n";
                }
                echo "\r\n";

                //finish send email
                if ($lesson_sent_id >= $max_lesson_id) {
                    self::saveCronSentEmailFinish($email_log->getSentEmail(),$startTime);
                }
                //save log time
                SentEmailLog::saveLogTime($email_log->getSentEmail(),$template_send->getEmailId(),$lesson_sent_id,$startTime,$success_log_time,$max_template_send);

                $timeExecute = $this->globalVariable->curTime - $startTime;
                if ($timeExecute >= 60) {
                    echo "time out!";
                    break;
                }
                $total++;
            }
        }

        $dataReturn =  [
            'total' => $total + $dataReturn['total'],
            'total_success' => $total_success + $dataReturn['total_success'],
            'total_error' => $total_error + $dataReturn['total_error']
        ];
    }
    public function deleteEmailExistAction() {
        $this->view->disable();
        $total = 0;
        $arrEmailLog = ForexcecSentEmailLog::find([
            'sent_email_type = "api_lw"',
        ]);

        foreach ($arrEmailLog as $email_log) {
            $arrEmailLeadform = ForexcecSentEmailLog::find([
                'sent_email_type = "leadform" AND sent_email = :email:',
                'bind' => ['email' => $email_log->getSentEmail()],
            ]);
            if (count($arrEmailLeadform) > 0) {
                foreach ($arrEmailLeadform as $emailLeadform) {
                    $emailLeadform->delete();
                    $total++;
                }
            }
        }

        $arrEmailSendLog = ForexcecSentEmailLog::query()
            ->where('sent_email_type = "leadform"')
            ->orderBy('sent_insert_time ASC')
            ->groupBy("sent_email")->execute();


        foreach ($arrEmailSendLog as $email_log) {
            $arrEmailLeadformExist = ForexcecSentEmailLog::find([
                'sent_email_type = "leadform" AND sent_email = :email: AND sent_id != :id:',
                'bind' => [
                    'email' => $email_log->getSentEmail(),
                    'id' => $email_log->getSentId()
                ],
            ]);
            if (count($arrEmailLeadformExist) > 0) {
                foreach ($arrEmailLeadformExist as $emailLeadform) {
                    $emailLeadform->delete();
                    $total++;
                }
            }
        }
        echo "Delete total :".$total." record.";
    }

    /*
        public function sendEmailTestAction() {
            $this->view->disable();
            $email_test = $this->request->get('email');
            $lesson = $this->request->get('lesson');
            $temp_test = $this->request->get('temp');
            $limit = $this->request->get('limit');
            $limit = $limit ? $limit : 5;
            $email_test = $email_test ? $email_test : "marketing@forexcec.com";
            $number_test = $this->request->get('test');
            $number_test = $number_test ? $number_test : 1;
            $name = "marketing";
            $arrTemp = TemplateAutoEmail::findForTest();
            $total_test_template = 0;
            $total_test_lesson = 0;
            $cron = new Cron();
            $time_start = $this->globalVariable->curTime;
            echo "Start test send email marketing at: ".$this->my->formatDateTime($time_start)."<br>";

            //send email
            if ($temp_test == "Y") {
                foreach ($arrTemp as $template) {
                    $result = $cron->sendEmail($email_test,$name,$template->getEmailForm(), $template,0,$number_test);
                    if ($result) {
                        $total_test_template++;
                    }
                };
            }

            if ($lesson == "Y") {
                $arrArticleLesson = ForexcecArticle::find([
                    'article_lession_id != "" OR article_lession_id != 0 OR (article_lession_id IS NOT NULL) ',
                    'order' => 'article_lession_id ASC'
                ]);
                $template_lesson = ForexcecTemplateAutoEmail::findFirst(' email_type = "FOREXCEC_LESSON" ');
                foreach ($arrArticleLesson as $article) {
                    if ($total_test_lesson <= $limit) {
                        $lesson_id = $article->getArticleLessionId();
                        if ($lesson_id < self::lesson_leadform_start) {
                            continue;
                        }
                        $result_lesson = $cron->sendEmail($email_test, $name, $template_lesson->getEmailForm(), $template_lesson, $lesson_id,$number_test);
                        if ($result_lesson) {
                            $total_test_lesson++;
                        }
                    }
                }
            }



            echo "-------------------<br>";
            echo "Sent success in ".(time() - $time_start);
            echo "<br>---Sent test email Markerting: ".$email_test." - total success: ".$total_test_template."<br>";
            echo "---Sent test email lesson: ".$email_test." - total success: ".$total_test_lesson."<br>";
        }
        */
}
