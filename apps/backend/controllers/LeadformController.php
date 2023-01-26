<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecLeadform;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Utils\Validator;

class LeadformController extends ControllerBase
{
    public function indexAction()
    {
        $list_leadForm = $this->getParameter();
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $paginator = new PaginatorModel(
            [
                'data' => $list_leadForm,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
        ));
        $btn_export = $this->request->getPost("btnExportcsv");
        if (isset($btn_export)) {
            $this->view->disable();
            $results[] = array("Id", "First Name", "Last Name", "Email", "Telephone", "Status","Nationality" ,"Account type" , "Insert Time");
            $list_lead_form_csv = $list_leadForm;
            foreach ($list_lead_form_csv as $item) {
                $status = ($item->getLeadformNumberVerify() == 'Y') ? 'Verified' : '';
                $leadform = array(
                    $item->getLeadformId(),
                    $item->getLeadformFirstName(),
                    $item->getLeadformLastName(),
                    $item->getLeadformEmail(),
                    $item->getLeadFormNumber(),
                    $status,
                    $item->getLeadformNationality(),
                    $item->getLeadformAccountType(),
                    $this->my->formatDateTime($item->getLeadFormInsertTime(),false),
                );
                $results[] = $leadform;
            }
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=leadform_'.  time() . '.csv');
            $out = fopen('php://output', 'w');
            fputs( $out, "\xEF\xBB\xBF" );
            foreach ($results as $fields) {
                fputcsv($out, $fields);
            }
            fclose($out);
            die();
        }
    }

    public function viewAction()
    {
        $leadFormID = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($leadFormID)) {
            $this->response->redirect('notfound');
            return;
        }
        $leadFormModel = ForexcecLeadform::findFirst($leadFormID);
        if (!$leadFormModel) {
            $this->response->redirect('notfound');
            return;
        }
        $this->view->leadFormModel = $leadFormModel;
    }

    private function getParameter()
    {
        $sql = ForexcecLeadform::query();
        $keyword = trim($this->request->get("txtSearch"));
        $from = trim($this->request->get("txtFrom")); //string
        $to = trim($this->request->get("txtTo"));  //string

        if (!empty($keyword)) {
            $sql->where("leadform_id = :keyword: OR 
                                  leadform_first_name like CONCAT('%',:keyword:,'%') OR 
                                  leadform_last_name like CONCAT('%',:keyword:,'%') OR
                                  leadform_email like CONCAT('%',:keyword:,'%')", ["keyword" => $keyword]);
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if ($from) {
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql->andWhere("leadform_insert_time >= :from:", ["from" => $intFrom]);
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if ($to) {
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql->andWhere("leadform_insert_time <= :to:", ["to" => $intTo]);
            $this->dispatcher->setParam("txtTo", $to);
        }
        $sql->orderBy("leadform_insert_time DESC");
        return $sql->execute();
    }
}
