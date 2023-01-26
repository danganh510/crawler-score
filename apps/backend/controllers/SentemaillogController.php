<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecLeadform;
use Forexceccom\Models\ForexcecSentEmailLog;
use Forexceccom\Repositories\SentEmailLog;
use Forexceccom\Repositories\TemplateAutoEmail;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Utils\Validator;

class SentemaillogController extends ControllerBase
{
    public function indexAction()
    {
        $list_log= $this->getParameter();
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $paginator = new PaginatorModel(
            [
                'data' => $list_log,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $status = trim($this->request->get("slStatus"));
        $type = trim($this->request->get("slType"));
        $slStatus = SentEmailLog::getComboboxStt($status);
        $slType = TemplateAutoEmail::getComboboxForm($type);
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'slStatus' => $slStatus,
            'slType' => $slType
        ));
//        $btn_export = $this->request->getPost("btnExportcsv");
//        if (isset($btn_export)) {
//            $this->view->disable();
//            $results[] = array("Id", "First Name", "Last Name", "Email", "Telephone", "Status","Nationality" ,"Account type" , "Insert Time");
//            $list_lead_form_csv = $list_leadForm;
//            foreach ($list_lead_form_csv as $item) {
//                $status = ($item->getLeadformNumberVerify() == 'Y') ? 'Verified' : '';
//                $leadform = array(
//                    $item->getLeadformId(),
//                    $item->getLeadformFirstName(),
//                    $item->getLeadformLastName(),
//                    $item->getLeadformEmail(),
//                    $item->getLeadFormNumber(),
//                    $status,
//                    $item->getLeadformNationality(),
//                    $item->getLeadformAccountType(),
//                    $this->my->formatDateTime($item->getLeadFormInsertTime(),false),
//                );
//                $results[] = $leadform;
//            }
//            header('Content-Type: text/csv; charset=utf-8');
//            header('Content-Disposition: attachment; filename=leadform_'.  time() . '.csv');
//            $out = fopen('php://output', 'w');
//            fputs( $out, "\xEF\xBB\xBF" );
//            foreach ($results as $fields) {
//                fputcsv($out, $fields);
//            }
//            fclose($out);
//            die();
//        }
    }

    public function viewAction()
    {
        $logID = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($logID)) {
            $this->response->redirect('notfound');
            return;
        }
        $logModel = SentEmailLog::findFirstById($logID);
        if (!$logModel) {
            $this->response->redirect('notfound');
            return;
        }
        $this->view->logModel = $logModel;
    }

    private function getParameter()
    {
        $sql = ForexcecSentEmailLog::query();
        $keyword = trim($this->request->get("txtSearch"));
        $status = trim($this->request->get("slStatus"));
        $type = trim($this->request->get("slType"));
        $update_from = trim($this->request->get("txtUpdateFrom")); //string
        $update_to = trim($this->request->get("txtUpdateTo"));  //string

        if (!empty($keyword)) {
            $sql->where("sent_id = :keyword: OR  sent_email like CONCAT('%',:keyword:,'%') ", ["keyword" => $keyword]);
            $this->dispatcher->setParam("txtSearch", $keyword);
        }

        if (!empty($type)) {
            $sql->where("sent_email_type = :type: ", ["type" => $type]);
            $this->dispatcher->setParam("slType", $type);
        }

        if (!empty($status)) {
            $sql->where("sent_status = :status: ", ["status" => $status]);
            $this->dispatcher->setParam("slStatus", $status);
        }
        if ($update_from) {
            $intFrom = $this->my->UTCTime(strtotime($update_from)); //UTC_mysql_time = date_picker - time zone
            $sql->andWhere("sent_update_time >= :from:", ["from" => $intFrom]);
            $this->dispatcher->setParam("txtUpdateFrom", $update_from);
        }
        if ($update_to) {
            $intTo = $this->my->UTCTime(strtotime($update_to)); //UTC_mysql_time = date_picker - time zone
            $sql->andWhere("sent_update_time <= :to:", ["to" => $intTo]);
            $this->dispatcher->setParam("txtUpdateTo", $update_to);
        }
        $sql->orderBy("sent_update_time DESC");
        return $sql->execute();
    }
    public function changeissubscribeAction()
    {
        $this->view->disable();
        $is_subscribe = $this->request->getPost('is_subscribe');
        $email = $this->request->getPost('email');
        $sent_log = SentEmailLog::findFirstByOnlyEmail($email);
        if ($sent_log) {
            $sent_log->setSentIsSubcribe($is_subscribe);
            $result = $sent_log->save();
            if ($result) {
                $dataReturn = [
                    'status' => 'success',
                ];
            } else {
                $dataReturn = [
                    'status' => 'fail',
                    'message' => $sent_log->getMessages(),
                ];
            }
        } else {
            $dataReturn = [
                'status' => 'fail',
                'message' => "email is not found in Sent Log",
            ];
        }
        die(json_encode($dataReturn));
    }
}
